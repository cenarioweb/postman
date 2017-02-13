<?php

namespace CenarioWeb\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use CenarioWeb\Model\Email;
use CenarioWeb\Model\EmailLog;

class RequestController extends Controller
{
    public function request(Request $request)
    {
        $events = $request->all();

        foreach ($events as $event) {

            $mail = Email::where('message_id', $event['message_id'])->first();

            if ($mail) {

                EmailLog::create([
                    'email_id' => $mail->id,
                    'date'     => date('Y-m-d H:i:s'),
                    'event'    => $event['event'],
                    'url'      => isset($event['url']) ? $event['url'] : null
                ]);

            }
        }

        return response()->json(['success' => true], 200);
    }
}
