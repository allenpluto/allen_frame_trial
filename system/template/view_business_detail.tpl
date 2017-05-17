<div id="[[*friendly_uri]]" itemscope="" itemtype="[[*schema_itemtype]]">
    <div class="wrapper listing_detail_view_wrapper">
        <div class="wrapper listing_detail_view_top_wrapper">
            <div class="container column_container listing_detail_view_top_container">
                [[logo:object=`view_business_summary_logo`]]
                <div class="listing_detail_view_top_text_container[[*top_text_container_column]]">
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
                            <span itemprop="addressLocality">[[*suburb]]</span> <span itemprop="addressRegion">[[*state]]</span>
                            <span itemprop="postalCode">[[*post]]</span>
                        </p>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
<div id="organization_block_container_[[*id]]" class="block_container organization_block_container[[*extra_classes]]" itemscope="" itemtype="[[*schema_itemtype]]">
    <div class="block_thumb_container">
        [[image:object=`view_business_summary_image`:field=`{"organization_id":"[[*id]]"}`:page_size=`1`:page_number=`0`]]
        [[logo:object=`view_business_summary_logo`]]
        <div class="clear"></div>
    </div>
    <div class="block_content_container">
        <div class="block_content_text_container">
            <h3 class="block_content_title" itemprop="name" title="[[*name]]">[[*name]]</h3>
            <p class="block_content_description" itemprop="description">
                [[*description]]
            </p>
            <p class="block_content_address" itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
                <span itemprop="streetAddress">[[*street_address]]</span>,
                <span itemprop="addressLocality">[[*suburb]]</span> <span itemprop="addressRegion">[[*state]]</span>
                <span itemprop="postalCode">[[*post]]</span>
            </p>
        </div>
        <div class="block_content_rating_container">
            <div class="rating_star_wrapper">
                <div class="rating_star_container rating_star_bg_container"><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
             --></div>
                <div class="rating_star_container rating_star_front_container" style="width: [[*avg_review_percentage]]%;"><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
             --></div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <div class="block_cover_over_link_container">
        <a href="[[*base]][[*module]]/[[*friendly_uri]]" itemprop="url" title="[[*name]]" class="block_thumb_cover_over_link"></a>
    </div>
</div>