<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 9/03/2017
 * Time: 5:00 PM
 */
define('PATH_SITE_BASE','C:\\wamp\\www\\allen_frame_trial\\');
include('../system/config/config.php');
$start_stamp = microtime(1);
echo '<pre>';

$entity_account_obj = new entity_account([1]);
$entity_account_obj->update(['last_name'=>'Woo','password'=>'twmg2011']);