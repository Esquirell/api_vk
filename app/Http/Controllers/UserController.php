<?php

namespace App\Http\Controllers;

use App\Events\GroupAddedEvent;
use App\Events\ProgressAddedEvent;
use App\Jobs\UpdateOnlineJob;

use App\Models\User;
use App\Models\Group;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use VK\Client\VKApiClient;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getUserFromGroup($id)
    {
        $group = Group::with('users')->find($id);
        $users = $group
            ->users()
            ->with('groups')
            ->orderBy('last_seen', 'DESC')
            ->paginate(30);
        $this->setDateFromTimestamp($users);
        return response()->json($users);
    }

    public function getAllUser()
    {
        $users = User::with('groups')
            ->withCount('groups')
            ->orderBy('last_seen', 'DESC')
            ->orderBy(DB::raw('groups_count'), 'DESC')
            ->paginate(30);
        $this->setDateFromTimestamp($users);
        return response()->json($users);
    }


    private function setDateFromTimestamp($users)
    {
        foreach ($users as &$user) {
            $user->last_seen = Carbon::createFromTimestamp($user->last_seen)
                ->format('H:i d/m/Y');
        }
        return $users;
    }

    public function searchUsers(Request $request)
    {
        $search_string = $request->input('search_string');
        $array = explode(' ', $search_string);
        $users = User::where('url', $search_string)
            ->orWhere('first_name', 'LIKE', '%'.$search_string.'%')
            ->orWhere('last_name', 'LIKE', '%'.$search_string.'%');
        if (count($array) === 2) {
            $users = $users
                ->orWhere(function ($query) use ($array) {
                    $query->orWhere('first_name', $array[0])
                        ->where('last_name',$array[1]);
            })
                ->orWhere(function ($query) use ($array) {
                    $query->orWhere('first_name', $array[1])
                        ->where('last_name', $array[0]);
                });
        }
        $users = $users->with('posts', 'groups')
            ->withCount('groups')
            ->orderBy('groups_count', 'DESC')
            ->get();
        return response()->json($users);
    }

    public function updateOnline()
    {
        $job = (new UpdateOnlineJob());
        $this->dispatch($job);
        return response()->json('Обновление запущено');

    }
}
