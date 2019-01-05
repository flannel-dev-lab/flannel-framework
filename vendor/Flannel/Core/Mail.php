<?php

namespace Flannel\Core;

\Flannel\Core\Config::required(['sparkpost.api.key','email.from']);

class Mail {

    /**
     *
     * @param string|array $to
     * @param string $subject
     * @param string $body
     * @param string|null $from
     * @param array|null $transmissionAttrs
     * @return boolean
     */
    public static function send($to, $subject, $body, $from=null) {
        $options = [
            'options' => [
                'click_tracking' => true,
                'open_tracking' => true,
                'transactional' => true,
                'inline_css' => true
            ],
            'recipients' => [
                ['address' => ['email'=>$to]]
            ],
            'content' => [
                'from' => [
                    'email' => $from ?: \Flannel\Core\Config::get('email.from'),
                    'name'  => \Flannel\Core\Config::get('email.name'),
                ],
                'reply_to' => $from ?: \Flannel\Core\Config::get('email.from'),
                'subject' => $subject,
                'html' => $body
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.sparkpost.com/api/v1/transmissions',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: ' . \Flannel\Core\Config::get('sparkpost.api.key')
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($options)
        ]);
        $result = json_decode(curl_exec($curl));
        curl_close($curl);

        if(!isset($result)) {
            \Flannel\Core\Monolog::get('mail')->error('cURL response was empty');
            return false;
        } elseif(isset($result->errors)) {
            foreach($result->errors as $error){
                \Flannel\Core\Monolog::get('mail')->error(($error->code ?? '0') . ': ' . ($error->message ?? '(no message)') . ' | ' . ($error->description ?? ''));
            }
            return false;
        }

        return true;
    }
}
