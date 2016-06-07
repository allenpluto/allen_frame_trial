DROP TABLE IF EXISTS `tbl_entity_google_place`;
CREATE TABLE IF NOT EXISTS `tbl_entity_google_place` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `place_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `administrative_area_level_1` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_1_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_1_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_2` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_2_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_2_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_3` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_3_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_3_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_4` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_4_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_4_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_5` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_5_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `administrative_area_level_5_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `airport` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `airport_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `airport_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `bus_station` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `bus_station_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `bus_station_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `colloquial_area` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `colloquial_area_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `colloquial_area_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `country_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `establishment` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `establishment_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `establishment_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `floor` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `floor_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `floor_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `intersection` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `intersection_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `intersection_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `locality` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `locality_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `locality_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `natural_feature` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `natural_feature_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `natural_feature_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `neighborhood` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `neighborhood_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `neighborhood_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `park` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `park_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `park_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `point_of_interest` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `point_of_interest_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `point_of_interest_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `political` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `political_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `political_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `post_box` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `post_box_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `post_box_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code_prefix` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code_prefix_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code_prefix_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code_suffix` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code_suffix_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_code_suffix_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_town` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_town_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `postal_town_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `premise` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `premise_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `premise_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `route` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `route_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `route_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `street_number` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `street_number_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `street_number_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_1` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_1_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_1_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_2` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_2_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_2_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_3` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_3_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_3_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_4` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_4_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_4_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_5` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_5_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sublocality_level_5_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `subpremise` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `subpremise_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `subpremise_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `train_station` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `train_station_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `train_station_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `transit_station` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `transit_station_short` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `transit_station_types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `formatted_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `formatted_phone_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `geometry_location_lat` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `geometry_location_lng` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `geometry_viewport_northeast_lat` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `geometry_viewport_northeast_lng` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `geometry_viewport_southwest_lat` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `geometry_viewport_southwest_lng` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `geometry_bounds_northeast_lat` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `geometry_bounds_northeast_lng` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `geometry_bounds_southwest_lat` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `geometry_bounds_southwest_lng` decimal(11,8) NOT NULL DEFAULT '0.00000000',
  `icon` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `international_phone_number` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `utc_offset` int(4) NOT NULL DEFAULT '0',
  `opening_hours` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `permanently_closed` tinyint(1) DEFAULT '0',
  `photos` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `rating` decimal(2,1) DEFAULT '0.0',
  `reviews` text COLLATE utf8_unicode_ci NOT NULL,
  `types` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `website` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `listing_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;