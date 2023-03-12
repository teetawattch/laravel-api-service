<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Illuminate\Http\Request;
use SendGrid;
use SendGrid\Mail\Mail;

class EmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->apiKeySendGrid = env('API_KEY_SENDGRID');
        $this->logs = new Logs();
    }

    public function sendEmail(Request $request)
    {
        $sender_email = 'test@tee-dev.online';
        $sender_name = auth()->user()->name;
        $receiver_email = $request->send_to;
        $email_subject = $request->subject;
        $email_body = $request->body;;

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($sender_email, $sender_name);
        $email->setSubject($email_subject);
        // $email->addTo($receiver_email, $receiver_name);
        $email->addTo($receiver_email);
        $email->addContent('text/plain', $email_body);
        $sendgrid = new \SendGrid($this->apiKeySendGrid);
        try {
            $response = $sendgrid->send($email);
            if (str_starts_with($response->statusCode(), '40')) {
                return response()->json(['error' => $response->body()], $response->statusCode());
            } else {
                $this->logs->uid = uniqid() . uniqid();
                $this->logs->user_uid = auth()->user()->uid;
                $this->logs->email = $sender_email;
                $this->logs->email_send_to = $receiver_email;
                $this->logs->service = 'SendGrid';
                $this->logs->subject = $email_subject;
                $this->logs->body = $email_body;

                $this->logs->save();
                return response()->json(['message' => 'send email with SendGrid success.'], $response->statusCode());
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function getAllOutbox()
    {
        try {
            $data = $this->logs->where('user_uid', auth()->user()->uid)->get();

            return response()->json(['data' => $data, 'message' => 'success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
}
