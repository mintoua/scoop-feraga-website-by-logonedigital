<?php
namespace App\Classes;
use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Mail extends AbstractController
{
    
    // private $api_key = $this->getParameter('api_key');
    // private $api_key_secret= $this->getParameter('api_key_secret');
    public function __construct(private ParameterBagInterface $params)
    {
        
    }

    public function send($to_email, $to_name, $content, $subject){
        $mj = new Client($this->params->get('api_key'), $this->params->get('api_key_secret'),true,['version' => 'v3.1']);
        //dd($content);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "emmanuelbenjamin@logonedigital.com",
                        'Name' => "logonedigital"
                        ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4134788,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    
                    'Variables' => [
                        'content'=>$content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
       
    }
}