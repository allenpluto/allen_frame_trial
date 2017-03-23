<div class="section_container container form_container">
    <div class="section_title"><h1>Edit My Business</h1></div>
    <div class="section_content">
        <div class="form_row_container">
            <label for="form_members_organization_name">Business Name</label>
            <input id="form_members_organization_name" name="name" type="text" placeholder="Business Name" value="[[*name]]">
        </div>
        <div class="form_row_container form_row_organization_logo_container">
            <label for="form_members_organization_logo">Logo</label>
            <input id="form_members_organization_logo" class="form_image_uploader_result" type="hidden" name="logo">
            <a href="javascript:void(0);" class="form_image_uploader_trigger"><img src="./image/upload_logo.jpg"></a>
            <a href="javascript:void(0);" class="form_image_uploader_delete_trigger"></a>
        </div>
        <div class="form_row_container form_row_organization_banner_container">
            <label for="form_members_organization_banner">Banner</label>
            <input id="form_members_organization_banner" class="form_image_uploader_result" type="hidden" name="banner">
            <a href="javascript:void(0);" class="form_image_uploader_trigger"><img src="./image/upload_banner.jpg"></a>
            <a href="javascript:void(0);" class="form_image_uploader_delete_trigger"></a>
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_street_address">Street Address</label>
            <input id="form_members_organization_street_address" type="text">
            <input name="place_id" type="hidden" value="[[*place_id]]">
            <div class="form_row_street_address_display_container"></div>
            <div id="map-canvas" class="form_row_street_address_map"></div>
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_telephone">Main Phone</label>
            <input id="form_members_organization_telephone" name="telephone" type="text" placeholder="Main Phone" value="[[*telephone]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_alternate_telephone">Alternate Phone</label>
            <input id="form_members_organization_alternate_telephone" name="alternate_telephone" type="text" placeholder="Alternate Phone" value="[[*alternate_telephone]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_mobile">Mobile Phone</label>
            <input id="form_members_organization_mobile" name="mobile" type="text" placeholder="Mobile Phone" value="[[*mobile]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_fax_number">Fax</label>
            <input id="form_members_organization_fax_number" name="fax_number" type="text" placeholder="Fax" value="[[*fax_number]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_email">Email Address</label>
            <input id="form_members_organization_email" name="email" type="text" placeholder="Email Address" value="[[*email]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_website_uri">Website</label>
            <input id="form_members_organization_website_uri" name="website_uri" type="text" placeholder="Website" value="[[*website_uri]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_facebook">Facebook</label>
            <input id="form_members_organization_facebook" name="facebook" type="text" placeholder="Facebook" value="[[*facebook]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_googleplus">Google+</label>
            <input id="form_members_organization_googleplus" name="googleplus" type="text" placeholder="Google+" value="[[*googleplus]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_linkedin">LinkedIn</label>
            <input id="form_members_organization_linkedin" name="linkedin" type="text" placeholder="LinkedIn" value="[[*linkedin]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_pinterest">Pinterest</label>
            <input id="form_members_organization_pinterest" name="pinterest" type="text" placeholder="Pinterest" value="[[*pinterest]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_blog">Blog</label>
            <input id="form_members_organization_blog" name="blog" type="text" placeholder="Blog" value="[[*blog]]">
        </div>
        <div class="form_row_container">
            <label for="form_members_organization_twitter">Twitter</label>
            <input id="form_members_organization_twitter" name="twitter" type="text" placeholder="Twitter" value="[[*twitter]]">
        </div>
        <div class="form_bottom_row_container"></div>
        <div class="footer_action_wrapper"><!--
            --><a href="javascript:void(0)" class="footer_action_button">Reset</a><!--
            --><a href="javascript:void(0)" class="footer_action_button">Save</a><!--
            --><a href="javascript:void(0)" class="footer_action_button">Publish</a><!--
        --></div>
    </div>
</div>
[[-script:field_name=`google_map`:field=`{"source":"https://maps.googleapis.com/maps/api/js?key=AIzaSyAtw-geY0B0clS4SRzPsYfvT0ROsSl3JVA&libraries=places&callback=initialize_autocomplete"}`]]