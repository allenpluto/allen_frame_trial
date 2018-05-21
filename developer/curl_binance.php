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

    $handler_uri = 'https://api.binance.com/api/v1/ticker/24hr';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $handler_uri);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-MBX-APIKEY: 0S9YBqRjRkK0ZDnXoezHghFXX8Dy2TCyw4YQlfexpT4lrcnZXSKosD9Wb1q7ts51'
    ));
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
