<div class="section_container container form_container">
    <div class="section_title"><h1>Edit My Business</h1></div>
    <div class="section_content ajax_editor_container">
        <form id="form_members_organization">
            <div class="form_row_container">
                <label for="form_members_organization_name">Business Name</label>
                <input id="form_members_organization_name" name="name" type="text" placeholder="Business Name" value="[[*name]]">
            </div>
            <div class="form_row_container form_row_organization_logo_container">
                <label>Logo</label>
                [[logo_id:object=`view_image`:template_name=`form_image_uploader`:field=`{"file_uri":"./image/upload_logo.jpg","field_name":"logo","dom_id":"form_members_organization_logo"}`]]
            </div>
            <div class="form_row_container form_row_organization_banner_container">
                <label>Banner</label>
                [[banner_id:object=`view_image`:template_name=`form_image_uploader`:field=`{"file_uri":"./image/upload_banner.jpg","field_name":"banner","dom_id":"form_members_organization_banner"}`]]
            </div>
            <div class="form_row_container form_row_organization_street_address_container">
                <input id="form_members_organization_street_address_place_id" name="place_id" type="hidden" value="[[*place_id]]">
                <label for="form_members_organization_street_address">Street Address</label>
                <input id="form_members_organization_street_address" class="form_row_street_address_input" type="text">
                <div class="form_row_street_address_display_container"></div>
                <div id="form_members_organization_street_address_map" class="form_row_street_address_map"></div>
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
                <input id="form_members_organization_facebook" name="facebook_link" type="text" placeholder="Facebook" value="[[*facebook_link]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_organization_googleplus">Google+</label>
                <input id="form_members_organization_googleplus" name="googleplus_link" type="text" placeholder="Google+" value="[[*googleplus_link]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_organization_linkedin">LinkedIn</label>
                <input id="form_members_organization_linkedin" name="linkedin_link" type="text" placeholder="LinkedIn" value="[[*linkedin_link]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_organization_pinterest">Pinterest</label>
                <input id="form_members_organization_pinterest" name="pinterest_link" type="text" placeholder="Pinterest" value="[[*pinterest_link]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_organization_blog">Blog</label>
                <input id="form_members_organization_blog" name="blog_link" type="text" placeholder="Blog" value="[[*blog_link]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_organization_twitter">Twitter</label>
                <input id="form_members_organization_twitter" name="twitter_link" type="text" placeholder="Twitter" value="[[*twitter_link]]">
            </div>
            <div class="ajax_editor_info"></div>
            <div class="form_bottom_row_container"></div>
            <div class="footer_action_wrapper"><!--
            --><a href="[[*base]]members/listing/" class="footer_action_button footer_action_button_back">Back</a><!--
            --><a href="[[*base]]members/listing/reset" class="footer_action_button footer_action_button_reset">Reset</a><!--
            --><a href="[[*base]]members/listing/save" class="footer_action_button footer_action_button_save">Save</a><!--
        --></div>
        </form>
        <div class="ajax_editor_mask"></div>
    </div>
</div>
[[-script:field_name=`google_map`:field=`{"source":"https://maps.googleapis.com/maps/api/js?key=AIzaSyAtw-geY0B0clS4SRzPsYfvT0ROsSl3JVA&libraries=places&callback=initialize_autocomplete"}`]]