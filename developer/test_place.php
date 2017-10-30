<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 25/10/2017
 * Time: 4:23 PM
 */
define('PATH_SITE_BASE','C:\\wamp\\www\\allen_frame_trial\\');
include('../system/config/config.php');
$timestamp = time();
echo '<pre>';
$place_obj = new entity_place();
$place_obj->get_related_place(['id_group'=>['ChIJdd4hrwug2EcRmSrV3Vo6llI'],'type'=>'locality']);
print_r($place_obj);