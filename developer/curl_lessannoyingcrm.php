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

    $handler_uri = 'https://api.lessannoyingcrm.com';

    $request_data  = [
        'UserCode'=>'254C5',
        'APIToken'=>'6W72XVBPSMMYX5JV4M3NV29J2B7GZ75BDWC67FPYZNH2316X03',
        'Function'=>'SearchContacts',
        'Parameters'=>urlencode(json_encode([
            'SearchTerms'=>'Akins Plumbing',
            'NumRows'=>25,
            'Pages'=>1,
            'Sort'=>'Relevance'
        ]))
    ];


//    $request_data  = [
//        'UserCode'=>'254C5',
//        'APIToken'=>'6W72XVBPSMMYX5JV4M3NV29J2B7GZ75BDWC67FPYZNH2316X03',
//        'Function'=>'CreateContact',
//        'Parameters'=>json_encode([
//            'FullName'=>'Gary Hannaford Test 2',
//            'CompanyName'=>'Gary Hannaford Plumbing Test 2',
//            'Email'=>[['Text'=>'admin2@garyhannafordsplumbing.com.au','Type'=>'Work']],
//            'Website'=>[['Text'=>'www.garyhannafordsplumbing.com.au']]
//        ])
//    ];
//print_r($request_data);
//
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, $handler_uri.'?'.http_build_query($request_data));
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//    $contents = curl_exec ($ch);
//    $contents = json_decode($contents,true);
//
//    print_r($contents);
//
//    if (!$contents['Success'])
//    {
//        print_r($contents['Error']);
//        exit;
//    }
//
//    $company_id = $contents['CompanyId'];
//
//    $request_data  = [
//        'UserCode'=>'254C5',
//        'APIToken'=>'6W72XVBPSMMYX5JV4M3NV29J2B7GZ75BDWC67FPYZNH2316X03',
//        'Function'=>'AddContactToGroup',
//        'Parameters'=>json_encode([
//            'ContactId'=>$company_id,
//            'GroupName'=>'Top4_Sign_Up'
//        ])
//    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $handler_uri.'?'.http_build_query($request_data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $contents = curl_exec ($ch);
    //curl_close ($ch);
    //echo '<pre><h1>I\'m Caller</h1><br>';
    //print_r($_SERVER);
    $contents = json_decode($contents,true);
    print_r($contents);
}
else
{
    echo '<pre><h1>I\'m Handler</h1><br>';
    print_r($_SERVER);
    print_r(getallheaders());
}
