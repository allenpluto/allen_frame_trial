<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 25/10/2017
 * Time: 4:23 PM
 */
define('PATH_SITE_BASE','C:\\wamp\\www\\allen_frame_trial\\');
include('../../system/config/config.php');
$timestamp = time();
echo '<pre>';
$place_obj = new entity_place();
$place_obj->get_related_place(['id_group'=>['ChIJ_--CVj6kDGsRsCBEkLQJBgU']]);
print_r($place_obj);