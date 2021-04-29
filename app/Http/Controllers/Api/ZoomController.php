<?php

namespace App\Http\Controllers\Api;

use App\Events\NewMeetingStarted;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class ZoomController extends Controller
{
    private $jwt_token = '';

    public function generateJWT(){

        $payload= [
            "iss" => env('ZOOM_API_KEY'),
            "exp" => time() + 3600
        ];

        $this->jwt_token = JWT::encode($payload, env('ZOOM_API_SECRET'), "HS256");
    }

    public function createMeeting(Request $request){

        $this->generateJWT();

		$requestBody = [
			'topic'			=> $request->topic 		?? 'PHP General Talk',
			'type'			=> 1,
			// 'type'			=> $request->type 		?? 2,
			// 'start_time'	=> $request->startTime	?? date('Y-m-dTh:i:00').'Z',
			'duration'		=> $request->duration 	?? 30,
			'password'		=> $request->password 	?? mt_rand(),
			'timezone'		=> config('app.timezone'),
			'agenda'		=> 'PHP Session',
			'settings'		=> [
			  	'host_video'			=> false,
			  	'participant_video'		=> true,
			  	'cn_meeting'			=> false,
			  	'in_meeting'			=> false,
			  	'join_before_host'		=> true,
			  	'mute_upon_entry'		=> true,
			  	'watermark'				=> false,
			  	'use_pmi'				=> false,
			  	'approval_type'			=> 1,
			  	'registration_type'		=> 1,
			  	'audio'					=> 'voip',
				'auto_recording'		=> 'none',
				'waiting_room'			=> false,
			]
		];

        // User "host" must have a zoom account in order to create a meeting
		// $zoomUserId = auth()->user()->email;
		$zoomUserId = "me";

        $response = Http::withToken($this->jwt_token)->post("https://api.zoom.us/v2/users/" . $zoomUserId . "/meetings", $requestBody);

        $data = $response->json();

        $data['invited'] = $request->invited;

        if($response->successful()){
            broadcast(new NewMeetingStarted($data))->toOthers();
        }

        return response($data, $response->status());

	}
}
