<?php
namespace App\Classes;
use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = "8e0693129d3ba6b3ad19989b59feb58e";
    private $api_key_secret= "4c5b0b3ec7bb83a3e4f305ee16c8c30a";

    public function send($to_email, $to_name, $subjet, $content){
        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);
        
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "ngueemmanuel.prof@gmail.com",
                        'Name' => "LOGNEDIGITAL"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4086985,
                    'TemplateLanguage' => true,
                    'Subject' => $subjet,
                    'Variables' => [
                        'content'=>$content
                    ]
                ]
            ]
        ];
        
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() ;
    }
}