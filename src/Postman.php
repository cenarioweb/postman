<?php

namespace CenarioWeb;

use CenarioWeb\Model\Email;

use Config;
use Sendgrid;

class Postman
{
    protected $token;
    protected $messageKey;
    protected $result;

    public function __construct()
    {
        do {
            $this->messageKey = str_random(32);
        } while (Email::where('key', $this->messageKey)->count() > 0);
    }

    public function send($attributes)
    {
        $message = Email::create([
            'key'               => $this->messageKey,
            'from'              => $attributes['from']['email'],
            'to'                => $attributes['to']['email'],
            'subject'           => $attributes['subject'],
            'date'              => date('Y-m-d H:i:s'),
            'vendor'            => env('EMAIL_GATEWAY')
        ]);

        if (env('EMAIL_GATEWAY')) {
            $this->token = env('SENDGRID_API_KEY');

            $from    = new SendGrid\Email($attributes['from']['name'], $attributes['from']['email']);
            $to      = new SendGrid\Email($attributes['to']['name'], $attributes['to']['email']);
            $subject = $attributes['subject'];
            $content = new SendGrid\Content("text/html", $attributes['content']);

            $mail = new SendGrid\Mail($from, $subject, $to, $content);

            foreach ($attributes['category'] as $cat) {
                $mail->addCategory($cat);
            }

            if (isset($attributes['files'])) {
                foreach ($attributes['files'] as $file) {
                    $attachment = new SendGrid\Attachment();
                    $attachment->setContent(base64_encode(file_get_contents($file['path'])));
                    $attachment->setType($file['mimetype']);
                    $attachment->setFilename($file['name']);
                    $attachment->setDisposition('attachment');
                    $mail->addAttachment($attachment);
                }
            }

            $mail->addCustomArg('message_id', $message->id);
            $mail->addCustomArg('message_key', $message->key);

            $sg       = new SendGrid($this->token);
            $response = $sg->client->mail()->send()->post($mail);

            $this->result['result']      = str_replace("\r", "", $response->headers()[0]);
            $this->result['status_code'] = $response->statusCode();

            if ($response->statusCode() == '202') {
                $this->result['message_id'] = str_replace("\r", "", $response->headers()[6]);
                $this->result['message_id'] = str_replace("X-Message-Id: ", "", $this->result['message_id']);
            } else {
                try {
                    $this->result['error'] = json_decode($response->body())->errors[0]->message;
                } catch(\Exception $e) {
                    $this->result['error'] = 'Erro ao tentar enviar um email. Code error: ' . $response->statusCode() ;
                }
            }

            $message->vendor_response = $this->result['result'];
            $message->vendor_status = $this->result['status_code'];
            $message->vendor_message_id = isset($this->result['message_id'])
                ? $this->result['message_id']
                : null;
            $message->vendor_error = isset($this->result['error'])
                ? $this->result['error'] = $this->result['error']
                : null;

            $message->save();

            return $message;
        }
    }
}
