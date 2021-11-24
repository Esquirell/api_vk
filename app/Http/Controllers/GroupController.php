<?php

namespace App\Http\Controllers;

use App\Events\ProgressAdded;
use App\Events\ProgressAddedEvent;
use App\Http\Requests\GroupRequest;
use App\Jobs\GroupJob;
use App\Models\Application;
use App\Models\Group;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use Ratchet\App;
use VK\Client\VKApiClient;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $groups = Group::all();
        return response()->json($groups);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {
        $data = $request->validated();

        $remove_char = ["https://", "http://", "/", 'vk.com', 'public', 'club'];
        $groupName = str_replace($remove_char, "", $data['url']);

        $job = (new GroupJob($data['url'], $data['count_posts'], $data['access_token']));
        $this->dispatch($job);
        $response = ['message' => 'Группа успешно добавлена'];
        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = Group::find($id);
        return response()->json($group);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function checkGroup(Request $request)
    {
        $data = $request->input();
        $application = Application::where('worked', 0)->first();
        $remove_char = ["https://", "http://", "/", 'vk.com', 'public', 'club'];
        $group_id = str_replace($remove_char, "", $data['url']);

        $vk = new VKApiClient();
        $response = $vk->groups()->getById($application->access_token, array(
            'group_ids' => $group_id,
        ));
        $group = Group::where('title', $response[0]['name'])->first();
        if ($group) {
            return response()->json('Группа уже существует', 404);
        }
        return response()->json('Можно парсить!');
    }

}
