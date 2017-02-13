<?php

namespace CenarioWeb;

use CenarioWeb\Model\Email;

use Config;
use Sendgrid;

class Postman
{
    protected $token;
    protected $messageId;

    public function __construct()
    {
    }

    public function send($attributes)
    {
        if (env('EMAIL_GATEWAY') == 'sendgrid') {

            $this->token = env('SENDGRID_API_KEY');

            do {
                $this->messageId = str_random(32);
            } while (Email::where('message_id', $this->messageId)->count() > 0);

            $from    = new SendGrid\Email($attributes['from']['name'], $attributes['from']['email']);
            $to      = new SendGrid\Email($attributes['to']['name'], $attributes['to']['email']);
            $subject = $attributes['subject'];
            $content = new SendGrid\Content("text/html", $attributes['content']);

            $mail = new SendGrid\Mail($from, $subject, $to, $content);

            foreach ($attributes['category'] as $cat) {
                $mail->addCategory($cat);
            }

            $mail->addCustomArg('message_id', $this->messageId);

            $mail->addCategory('app:'.Config::get('myapp.appName'));

            $sg       = new SendGrid($this->token);
            $response = $sg->client->mail()->send()->post($mail);

            $this->result['result']      = str_replace("\r", "", $response->headers()[0]);
            $this->result['status_code'] = $response->statusCode();

            if ($response->statusCode() == 202) {
                $this->result['message_id'] = str_replace("\r", "", $response->headers()[6]);
                $this->result['message_id'] = str_replace("X-Message-Id: ", "", $this->result['message_id']);
            } else {
                $this->result['errors'] = json_decode($response->body())->errors;
            }

            return Email::create([
                'message_id'        => $this->messageId,
                'from'              => $attributes['from']['email'],
                'to'                => $attributes['to']['email'],
                'subject'           => $attributes['subject'],
                'date'              => date('Y-m-d H:i:s'),
                'vendor'            => env('EMAIL_GATEWAY'),
                'vendor_response'   => str_replace("\r", "", $response->headers()[0]),
                'vendor_status'     => $response->statusCode(),
                'vendor_message_id' => isset($this->result['message_id']) ? $this->result['message_id'] : null,
                'vendor_error'      => isset($this->result['errors'][0]->message) ? $this->result['errors'][0]->message : null
            ]);

        }
    }
}
