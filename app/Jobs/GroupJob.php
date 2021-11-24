<?php

namespace App\Jobs;

use App\Events\GroupAddedEvent;
use App\Events\ProgressAddedEvent;
use App\Models\Application;
use App\Models\User;
use App\Models\Group;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VK\Client\VKApiClient;

class GroupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 36000;

    public $url;
    public $count_post;
    public $access_token;
    public $progress;
    public $status;
    public $group;
    public $group_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $count_post, $access_token)
    {
        $this->url = $url;
        $this->count_post = $count_post;
        $this->access_token = $access_token;
        $this->progress = 0;
        $this->status = 'Start';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->createGroup($this->url, $this->access_token);
        $this->setWorkedAndCountApplication($this->access_token, $this->count_post);
        $posts_id = $this->getPostsAndPostsId($this->access_token, $this->count_post, $this->url, $this->group);
        $users = $this->getListOfLiked($this->access_token, $this->group, $posts_id);
        $this->saveUsers($users, $this->group);
    }

    private function createGroup($url, $access_token)
    {
        $remove_char = ["https://", "http://", "/", 'vk.com', 'public', 'club'];
        $group_id = str_replace($remove_char, "", $url);

        $vk = new VKApiClient();
        $response = $vk->groups()->getById($access_token, array(
            'group_ids' => $group_id,
        ));

        $this->group_id = $response[0]['id'];

        $group = Group::firstOrCreate(
            ['url_group' => 'https://vk.com/public'.$this->group_id],
            [
                'title' => $response[0]['name'],
                'image' => $response[0]['photo_200'],
                'progress' => $this->progress,
                'status' => $this->status,
            ]
        );
        $group->progress = $this->progress;
        $group->status = $this->status;
        $group->save();
        $this->group = $group;
    }

    private function setWorkedAndCountApplication($access_token, $count_post)
    {
        $application = Application::where('access_token', $access_token)->first();
        $application->worked = 1;
        $application->count += $count_post;
        $application->save();
    }

    private function getPostsAndPostsId($access_token, $count_post, $url, $group)
    {
        $posts_id = [];

        $cicles = ceil($count_post/100);
        $counter = $count_post;
        $vk = new VKApiClient();

        for ($offset = 0; $offset < $counter; $offset += 100) {
            $response = $vk->wall()->get($access_token, [
                'owner_id' => '-'. $this->group_id,
                'offset' => $offset,
                'count' => $count_post
            ]);
            $first_action = microtime(true);

            $posts = $response['items'];
            foreach ($posts as $post) {
                $posts_id [] = $post['id'];
            }
            $count_post = $count_post - 100;
            $calculated_progress = 33.33/$cicles;

            $this->progress += $calculated_progress;
            $this->status = 'Получение постов';
            $group->progress = $this->progress;
            $group->status = $this->status;
            $group->save();
            event(new ProgressAddedEvent($group->progress, $group->id, $group->status));

            $last_action = microtime(true);
            $time_difference = $last_action - $first_action;
            if ($time_difference >= 0.34) {
                continue;
            }
            usleep(340000 - $time_difference);
        }
        return $posts_id;
    }

    private function getListOfLiked($access_token, $group, $posts_id)
    {
        $vk = new VKApiClient();
        $users_id = '';
        $cicles = 33.33/count($posts_id);
        $count = 1;
        $return_users = [];

        foreach ($posts_id as $post_id) {
            for ($offset = 0; $offset < 30000; $offset += 1000) {
                $first_action = microtime(true);
                $response = $vk->likes()->getList($access_token, [
                    'type' => 'post',
                    'owner_id' => '-' . $this->group_id,
                    'item_id' => $post_id,
                    'filter' => 'likes',
                    'extended' => '1',
                    'count' => '1000',
                    'offset' => $offset
                ]);

                $list_of_liked = $response['items'];

                foreach ($list_of_liked as $user) {
                    if ($user == end($list_of_liked) and $post_id == end($posts_id)) {
                        $users_id .= $user['id'];
                        break;
                    }
                    $users_id .= $user['id'] . ',';
                }

                $last_action = microtime(true);
                $difference_time = $last_action - $first_action;

                if ($difference_time < 0.34) {
                    usleep(340000 - $difference_time);
                }
                if (count($response['items']) == 0) {
                    break;
                }

                $first_action = microtime(true);

                $response = $vk->users()->get($access_token, [
                    'user_ids' => $users_id,
                    'fields' => 'photo_200,city,sex,bdate,last_seen'
                ]);

                $users = $this->findUsersFromList($response);
                foreach ($users as &$user) {
                    $user['post'] = 'http://vk.com/wall-'.$this->group_id.'_'.$post_id;
                    $return_users[] = $user;
                }

                $list_of_liked = [];
                $users_id = '';

                $last_action = microtime(true);
                $difference_time = $last_action - $first_action;
                if ($difference_time < 0.34) {
                    usleep(340000 - $difference_time);
                }
            }

            $post = Post::firstOrCreate([
                'url' => 'http://vk.com/wall-'.$this->group_id.'_'.$post_id
            ]);

            $list_of_liked = [];

            $this->progress += $cicles;
            $this->status = 'Обработка постов '.$count.'/'.$this->count_post;
            $group->progress = $this->progress;
            $group->status = $this->status;
            $group->save();
            event(new ProgressAddedEvent($group->progress, $group->id, $group->status));
            ++$count;
        }
        return $return_users;
    }

    private function findUsersFromList($users)
    {
        $finded_users = [];
        foreach ($users as $user) {
            if (!isset($user['city'])) {
                continue;
            }
            if($user['city']['id'] == 650) {
                $finded_users [] = [
                    'id' => $user['id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'photo' => $user['photo_200'],
                    'bdate' => (isset($user['bdate'])) ? $user['bdate'] : '---',
                    'last_seen' => (isset($user['last_seen'])) ? $user['last_seen'] : '0'
                ];
            }
        }
        return $finded_users;
    }

    private function saveUsers($users, $group)
    {

        $cicles = 33.34/count($users);
        $count = 1;
        foreach ($users as $user) {
            $this->progress += $cicles;
            $group->progress = $this->progress;
            $group->status = 'Сохранение юзеров '.$count.'/'.count($users);
            $group->save();
            event(new ProgressAddedEvent($group->progress, $group->id, $group->status));

            $new_user = User::firstOrCreate(
                ['url' => 'https://vk.com/id'.$user['id']],
                [
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'bdate' => $user['bdate'],
                    'last_seen' => $user['last_seen']['time'],
                    'photo' => $user['photo'],
                ]
            );

            $post = Post::firstOrCreate(
                ['url' => $user['post']]
            );

            $new_user->groups()->syncWithoutDetaching($group);
            $new_user->posts()->syncWithoutDetaching($post);

            $count++;
        }

        $this->progress = 100;
        $this->status = 'Найдено '.$group->loadCount('users')->users_count.' пользователей';
        $group->progress = $this->progress;
        $group->status = $this->status;
        $group->save();
        $application = Application::where('access_token', $this->access_token)->first();
        $application->worked = 0;
        $application->save();
        event(new ProgressAddedEvent($group->progress, $group->id, $group->status));
    }
}
