<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 20/11/2015
 * Time: 2:00 PM
 */
include('../system/config/config.php');

echo '<pre>';

/*$id_array = array(3, 4, 5, 'a', 'b', 'c', ' 231', '%333');

$format_function = format::get_obj();
$instance = $format_function->id_group($id_array);
print_r($instance);*/

/*$listing_id = array(1,4,8,11,22);
for($i=70220;$i<70237;$i++)
{
    $listing_id[] = $i;
}
shuffle($listing_id);
print_r($listing_id);
$category_id = 88;
$index_organization = new index_organization($listing_id);
print_r($index_organization->id_group);
$index_organization->filter_by_category($category_id);
print_r($index_organization->id_group);*/

/*$listing_id = array();
$index_organization = new index_organization($listing_id);
$index_postcode = new index_postcode();
$index_postcode->filter_by_location_text('castle hill, nsw');
print_r($index_postcode);

print_r($index_organization->filter_by_suburb($index_postcode->id_group));

$view_business_summary_obj = new view_business_summary($index_organization->id_group,array('page_size'=>4,'order'=>'RAND()'));
print_r($view_business_summary_obj);*/

$listing_id = array();
$index_organization = new index_organization($listing_id);
$keyword_score = $index_organization->filter_by_keyword($_GET['keyword']);
print_r($keyword_score);
$location_score = $index_organization->filter_by_location($_GET['location'],array('preset_score'=>$keyword_score));
print_r($location_score);
$final_score = array();
//$index_organization->reset();
//print_r($index_organization->filter_by_keywords2($_GET['keyword']));
print_r($index_organization->id_group);
$view_business_summary = new view_business_summary($index_organization->id_group);
print_r($view_business_summary->fetch_value());


/*$index_organization = new index_organization();
print_r($index_organization);*/

