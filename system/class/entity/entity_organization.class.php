<?php
// Class Object
// Name: entity_organization
// Description: organization, business, company table, which stores all company (or organziaton) reltated information

class entity_organization extends entity
{
    function __construct($value = Null, $parameter = array())
    {
        $default_parameter = [
            'relational_fields'=>[
                'category'=>[],
                'gallery'=>[]
            ]
        ];
        $parameter = array_merge($default_parameter, $parameter);
        return parent::__construct($value, $parameter);
    }

    function sync($parameter = array())
    {
//        $sync_parameter = array();
//
//        // set default sync parameters for index table
//        //$parameter['sync_table'] = str_replace('entity','index',$this->parameter['table']);
//        $sync_parameter['sync_table'] = 'tbl_index_organization_1';
//        $sync_parameter['update_fields'] = array(
//            'id' => 'tbl_entity_organization.id',
//            'name' => 'tbl_entity_organization.name',
//            'alternate_name' => 'tbl_entity_organization.alternate_name',
//            'description' => 'tbl_entity_organization.description',
//            'enter_time' => 'tbl_entity_organization.enter_time',
//            'update_time' => 'tbl_entity_organization.update_time',
//            'keywords' => 'tbl_entity_organization.keywords',
//            'latitude' => 'tbl_locality.geometry_location_lat',
//            'longitude' => 'tbl_locality.geometry_location_lng',
//            'abn' => 'tbl_entity_organization.abn',
//            'account_id' => 'tbl_entity_organization.account_id',
//            'place_id' => 'tbl_entity_google_place.id',
//            'category_id' => 'GROUP_CONCAT(tbl_rel_category_to_listing.category_id)',
//            'featured' => 'IF((CURDATE()<=ListingFeatured.date_end AND CURDATE()>=ListingFeatured.date_start), 1, 0)'
//        );
//
//        $sync_parameter['join'] = array(
//            'JOIN Listing_Category tbl_rel_category_to_listing ON tbl_entity_organization.id = tbl_rel_category_to_listing.listing_id',
//            'LEFT JOIN ListingFeatured ON tbl_entity_organization.id = ListingFeatured.id',
//            'LEFT JOIN tbl_entity_google_place tbl_locality ON tbl_entity_organization.id = tbl_entity_google_place.listing_id AND (tbl_entity_google_place.types = "route" OR tbl_entity_google_place.types = "street_address" OR tbl_entity_google_place.types = "subpremise")'
//        );
//
//        $sync_parameter['where'] = array(
//            'tbl_entity_organization.status = "A"'
//        );
//
//        $sync_parameter['group'] = array(
//            'tbl_entity_organization.id'
//        );
//
//        $sync_parameter['fulltext_key'] = array(
//            'fulltext_keywords' => ['name','alternate_name','description','keywords']
//        );
//
//        $sync_parameter = array_merge($sync_parameter, $parameter);
//
//        $result[] = parent::sync($sync_parameter);


        $sync_parameter = array();

        // set default sync parameters for view table
        $sync_parameter['sync_table'] = 'tbl_view_organization';
        $sync_parameter['update_fields'] = array(
            'id' => 'tbl_entity_organization.id',
            'friendly_uri' => 'tbl_entity_organization.friendly_uri',
            'name' => 'tbl_entity_organization.name',
            'alternate_name' => 'tbl_entity_organization.alternate_name',
            'description' => 'tbl_entity_organization.description',
            'enter_time' => 'tbl_entity_organization.enter_time',
            'update_time' => 'tbl_entity_organization.update_time',
            'view_time' => '"'.date('Y-m-d H:i:s').'"',
            'logo' => 'tbl_entity_organization.logo_id',
            'banner' => 'tbl_entity_organization.banner_id',
            'abn' => 'tbl_entity_organization.abn',
            'account' => 'tbl_entity_organization.account_id',
            'latitude' => 'tbl_locality.geometry_location_lat',
            'longitude' => 'tbl_locality.geometry_location_lng',
            'subpremise' => 'tbl_entity_organization.subpremise',
            'street_address' => 'tbl_entity_organization.street_address',
            'suburb' => 'tbl_locality.locality',
            'region' => 'tbl_locality.administrative_area_level_2',
            'state' => 'tbl_locality.administrative_area_level_1',
            'post' => 'tbl_locality.postal_code',
            'telephone' => 'tbl_entity_organization.telephone',
            'telephone_alt' => 'tbl_entity_organization.alternate_telephone',
            'mobile' => 'tbl_entity_organization.mobile',
            'fax' => 'tbl_entity_organization.fax_number',
            'website' => 'tbl_entity_organization.website_url',
            'hours_work' => 'tbl_entity_organization.hours_work',
            'facebook' => 'tbl_entity_organization.facebook_link',
            'twitter' => 'tbl_entity_organization.twitter_link',
            'linkedin' => 'tbl_entity_organization.linkedin_link',
            'youtube' => 'tbl_entity_organization.youtube_link',
            'blog' => 'tbl_entity_organization.blog_link',
            'pinterest' => 'tbl_entity_organization.pinterest_link',
            'googleplus' => 'tbl_entity_organization.googleplus_link',
            'keywords' => 'tbl_entity_organization.keywords',
            'content' => 'tbl_entity_organization.content',
            'place_id' => 'tbl_entity_organization.place_id',
            'category' => 'GROUP_CONCAT(DISTINCT tbl_rel_category_to_organization.category_id)',
            'gallery' => 'GROUP_CONCAT(DISTINCT tbl_rel_gallery_to_organization.gallery_id)',
            'image' => 'GROUP_CONCAT(DISTINCT tbl_rel_gallery_to_image.image_id)',
            'featured' => 'IF((CURDATE()<=ListingFeatured.date_end AND CURDATE()>=ListingFeatured.date_start), 1, 0)'
        );

        $sync_parameter['join'] = array(
            'LEFT JOIN tbl_entity_google_place tbl_locality ON tbl_entity_organization.place_id = tbl_locality.id',
            'LEFT JOIN ListingFeatured ON tbl_entity_organization.id = ListingFeatured.id',
            'LEFT JOIN tbl_rel_category_to_organization ON tbl_entity_organization.id = tbl_rel_category_to_organization.organization_id',
            'LEFT JOIN tbl_rel_gallery_to_organization ON tbl_entity_organization.id = tbl_rel_gallery_to_organization.organization_id',
            'LEFT JOIN tbl_rel_gallery_to_image ON tbl_rel_gallery_to_organization.gallery_id = tbl_rel_gallery_to_image.gallery_id'
        );

        $sync_parameter['where'] = array(
            'tbl_entity_organization.status = "A" AND tbl_entity_organization.import_error != 1'
        );

        $sync_parameter['group'] = array(
            'tbl_entity_organization.id'
        );

        $sync_parameter['fulltext_key'] = array();

        $sync_parameter = array_merge($sync_parameter, $parameter);

        $result[] = parent::sync($sync_parameter);

        return $result;
    }
}

?>