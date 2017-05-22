<div id="[[*friendly_uri]]" itemscope="" itemtype="[[*schema_itemtype]]">
    <div id="listing_detail_view_wrapper_[[*id]]" class="wrapper listing_detail_view_wrapper">
        <div class="wrapper listing_detail_view_top_wrapper">
            <div class="container column_container listing_detail_view_top_container">
                [[banner:object=`view_business_detail_banner`:field=`{"organization_id":"[[*id]]"}`]]
                [[logo:object=`view_business_detail_logo`]]
                <div class="listing_detail_view_top_text_container column [[*logo:template=`column_8`:empty_template=`column_12`]]">
                    <div class="listing_detail_view_title_container"><h1 itemprop="name">[[*name]]</h1></div>
                    <div class="listing_detail_view_rating_container">
                        <div class="listing_detail_view_count_visit"><span>[[*count_visit]]</span><span class="listing_detail_view_count_visit_label"> Visits</span></div>
                        <div class="rating_star_wrapper">
                            <div class="rating_star_container rating_star_bg_container"><!--
                                --><span class="rating_star"></span><!--
                                --><span class="rating_star"></span><!--
                                --><span class="rating_star"></span><!--
                                --><span class="rating_star"></span><!--
                                --><span class="rating_star"></span><!--
                            --></div>
                            <div class="rating_star_container rating_star_front_container"><!--
                                --><span class="rating_star"></span><!--
                                --><span class="rating_star"></span><!--
                                --><span class="rating_star"></span><!--
                                --><span class="rating_star"></span><!--
                                --><span class="rating_star"></span><!--
                            --></div>
                        </div>
                        [[$aggregaterating]]
                    </div>
                    <div id="listing_detail_view_address_container_large_screen" class="listing_detail_view_address_container">
                        <p itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
                            <span itemprop="streetAddress">[[*street_address]]</span>,
                            <span itemprop="addressLocality">[[*locality]]</span> <span itemprop="addressRegion">[[*administrative_area_level_1]]</span>
                            <span itemprop="postalCode">[[*postal_code]]</span>
                        </p>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div id="listing_detail_view_summary_wrapper" class="wrapper listing_detail_view_section_wrapper">
        <div class="wrapper listing_detail_view_section_title_wrapper">
            <div class="container listing_detail_view_section_title_container"><h2>[[*category]] in [[*locality]], [[*administrative_area_level_1]] [[*postal_code]]</h2></div>
        </div>
        <div class="wrapper listing_detail_view_section_content_wrapper">
            <div class="container listing_detail_view_section_content_container">
                <p class="listing_detail_view_summary" itemprop="description">
                    [[*description]]
                </p>
            </div>
        </div>
    </div>
    <div id="listing_detail_view_overview_wrapper" class="wrapper listing_detail_view_section_wrapper expand_parent_expanded">
        <div class="wrapper listing_detail_view_section_title_wrapper expand_trigger">
            <div class="container listing_detail_view_section_title_container"><h3>Overview</h3></div>
        </div>
        <div class="wrapper listing_detail_view_section_content_wrapper expand_wrapper">
            <div class="container listing_detail_view_section_content_container expand_container">
                [[*long_description]]
            </div>
        </div>
    </div>
    [[*keywords:container_name=`container_business_section_keywords`]]
    [[*description:container_name=`container_business_section`:field=`{"section_name":"summary","section_title":"[[*category_name]] in [[*suburb]], [[*state]] [[*post]]"}`]]

</div>