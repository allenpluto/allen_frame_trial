<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/03/2017
 * Time: 12:16 PM
 */
define('PATH_SITE_BASE','C:\\wamp\\www\\allen_frame_trial\\');
include('../system/config/config.php');
$start_stamp = microtime(1);
echo '<pre>';

$entity_organization = new entity_organization(121);
$get_result = $entity_organization->get(['fields'=>['id','friendly_uri','name','category','gallery','place']]);
print_r($get_result);

print_r($global_message->display());

print_r("\nExcution Time: ".(microtime(1)-$start_stamp)."\n");