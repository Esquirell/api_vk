<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Http\Request;
use VK\Client\VKApiClient;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $application = Application::all();
        return response()->json($application);
    }

    public function indexFreeApplication()
    {
        $applications = Application::where('worked', 0)->get();
        $number = 1;
        foreach ($applications as &$application) {
            $application->id = $number;
            $number++;
        }
        return response()->json($applications);
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
    public function store(Request $request)
    {
        $data = $request->input();

        $oauth = new VKOAuth();
        $display = VKOAuthDisplay::PAGE;
        $scope = array(VKOAuthUserScope::WALL, VKOAuthUserScope::GROUPS);
        $state = 'secret_state_code';
        $browser_url = $oauth->getAuthorizeUrl(VKOAuthResponseType::CODE, $data['client_id'], $data['redirect_uri'], $display, $scope, $state);
        $data['browser_url'] = $browser_url;
        Application::create($data);
        $response = [
            'data' => $data,
            'message' => 'Приложение успешно добавлено'
        ];
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
        //
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

    public function getCode($id)
    {
        $application = Application::find($id);
        $application->need_token = 1;
        $application->save();
        return redirect()->to($application->browser_url);
    }

    public function getToken(Request $request)
    {
        $application = Application::where('need_token', 1)->first();
        $code = $request->input('code');

        $oauth = new VKOAuth();
        $response = $oauth->getAccessToken($application->client_id, $application->client_secret, $application->redirect_uri, $code);

        $application->access_token = $response['access_token'];
        $application->vk_token_expires = Carbon::now()->addDay(1);
        $application->need_token = 0;
        $application->count = 0;
        $application->save();

        return redirect()->to('/applications');
    }
}
