<div id="organization_block_container_[[*id]]" class="block_container organization_block_container[[*extra_classes]]" itemscope="" itemtype="[[*schema_itemtype]]">
    <div class="block_thumb_container">
        [[image:object=`view_business_summary_image`:field=`{"organization_id":"[[*id]]"}`:page_size=`1`:page_number=`0`]]
        [[logo:object=`view_business_summary_logo`]]
        <div class="clear"></div>
    </div>
    <div class="block_content_container">
        <div class="block_content_rating_container">
            <div class="rating_star_wrapper">
                <div class="rating_star_container rating_star_bg_container"><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
             --></div>
                <div class="rating_star_container rating_star_front_container" style="width: [[*avg_review_percentage]]80%;"><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
                 --><span class="rating_star"></span><!--
             --></div>
            </div>
            <div class="rating_text">
                <p itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating"><span itemprop="ratingValue">[[*avg_review]]4.00</span> stars by <span itemprop="reviewCount">10</span> ratings</p>
            </div>
        </div>
        <div class="block_content_text_container">
            <h3 class="block_content_title" itemprop="name" title="[[*name]]">[[*name]]</h3>
            <div class="block_content_description_container">
                <p class="block_content_description" itemprop="description">
                    [[*description]]
                </p>
            </div>
            <p class="block_content_address" itemprop="address" itemscope="" itemtype="http://schema.org/PostalAddress">
                <span class="block_content_address_street_address" itemprop="streetAddress">[[*street_address]]</span>
                <span class="block_content_address_locality" itemprop="addressLocality">[[*suburb]]</span> <span class="block_content_address_administrative_area_level_2" itemprop="addressRegion">[[*state]]</span>
                <span class="block_content_address_administrative_area_level_1" itemprop="postalCode">[[*post]]</span>
            </p>
        </div>
    </div>
    <div class="clear"></div>
    <div class="block_cover_over_link_container">
        <a href="[[*base]]business/[[*friendly_uri]]" itemprop="url" title="[[*name]]" class="block_thumb_cover_over_link"></a>
    </div>
</div>