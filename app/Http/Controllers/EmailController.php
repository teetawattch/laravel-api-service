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
        $this->middleware('auth:api', ['except' => ['sendEmail']]);
        $this->apiKeySendGrid = env('API_KEY_SENDGRID');
        $this->logs = new Logs();
    }

    public function sendEmail()
    {
        $sender_email = 'teetawat.tch@gmail.com';
        $sender_name = 'tee';
        $receiver_email = 'teetawat.tch@gmail.com';
        $receiver_name = 'tee';
        $email_subject = 'Subject Email';
        $email_body = 'Hi, tee.';

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($sender_email, $sender_name);
        $email->setSubject($email_subject);
        $email->addTo($receiver_email, $receiver_name);
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
}
