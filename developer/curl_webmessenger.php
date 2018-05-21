<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14/10/2016
 * Time: 3:30 PM
 */
if (!isset($_GET['handler']))
{
    $post_value = $_GET;
    if (isset($post_value['method']))
    {
        $method = $post_value['method'];
        unset($post_value['method']);
    }
    else
    {
        $method = 'list_available_method';
    }

    //$handler_uri = 'http://122.201.69.117:1337/webhook?hub.verify_token=top42016&hub.challenge=CHALLENGE_ACCEPTED&hub.mode=subscribe';
    //$handler_uri = 'https://kaput-twilight.glitch.me/webhook?hub.verify_token=top4&hub.challenge=CHALLENGE_ACCEPTED&hub.mode=subscribe';

    //$handler_uri = 'http://122.201.69.117:1337/webhook';
    $handler_uri = 'https://kaput-twilight.glitch.me/webhook';
    $post_value = [
        'object'=>'page',
        'entry'=>[
            ['messaging'=>[['message'=>'Test Message Remote 1','sender'=>['id'=>'1234567']]]]
        ]
    ];
    $post_value_json = json_encode($post_value);
    print_r('POST',$post_value_json);


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $handler_uri);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    //curl_setopt($ch,CURLOPT_POST, count($post_value));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $post_value_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $contents = curl_exec ($ch);
    //curl_close ($ch);
    //echo '<pre><h1>I\'m Caller</h1><br>';
    //print_r($_SERVER);
    print_r($contents);
}
else
{
    echo '<pre><h1>I\'m Handler</h1><br>';
    print_r($_SERVER);
    print_r(getallheaders());
}
