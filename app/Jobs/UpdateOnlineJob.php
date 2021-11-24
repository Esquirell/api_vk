<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VK\Client\VKApiClient;

class UpdateOnlineJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 8000;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $application = Application::where('worked', 0)->first();
        $access_token = $application->access_token;
        $application->worked = 1;
        $application->save();

        $users = User::all();
        $users_count = User::all()->count();
        $count = ceil($users_count/1000);

        $vk = new VKApiClient();

        $offset = 0;
        $counter = 0;

        for ($i = 0; $i < $count; ++$i) {
            $users_list = $users->slice($offset, 1000);
            $profilesId = [];
            foreach($users_list as $user) {
                $removeChar = ["https://", "http://", "/", 'vk.com', 'id'];
                $user_id = str_replace($removeChar, "", $user->url);
                $profilesId[] = $user_id;
            }

            $getInfoUser = $vk->users()->get($access_token, array(
                'user_ids' => $profilesId,
                'fields' => 'photo_200,last_seen'
            ));
            foreach ($getInfoUser as $user) {
                if (isset($user['last_seen']['time'])) {
                    $user_new = User::where('url', 'like', '%'.$user['id'])->first();
                    $user_new->last_seen = $user['last_seen']['time'];
                    $user_new->photo = $user['photo_200'];
                    $user_new->save();
                }
                ++$counter;
            }
            $offset += 1000;
        }

        $application->worked = 0;
        $application->save();
    }
}
