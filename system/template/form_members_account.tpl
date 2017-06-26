<div class="section_container container form_container">
    <div class="section_title"><h1>Edit My Business</h1></div>
    <div class="section_content ajax_form_container">
        <form id="form_members_account" class="ajax_form">
            <div class="form_row_container">
                <label for="form_members_account_name">Nickname</label>
                <input id="form_members_account_name" name="name" type="text" placeholder="Nickname" value="[[*name]]">
            </div>
            <div class="form_row_container form_row_account_image_container">
                <label>Image</label>
                [[image_id:object=`view_image`:template_name=`form_image_uploader`:empty_template_name=`form_image_uploader`:field=`{"file_uri":"./image/upload_image.jpg","field_name":"image","image_uploader_id":"form_members_account_image"}`]]
            </div>
            <div class="form_row_container form_row_account_banner_container">
                <label>Banner</label>
                [[banner_id:object=`view_image`:template_name=`form_image_uploader`:empty_template_name=`form_image_uploader`:field=`{"file_uri":"./image/upload_banner.jpg","field_name":"banner","image_uploader_id":"form_members_account_banner"}`]]
            </div>
            <div class="form_row_container">
                <label for="form_members_account_first_name">First Name</label>
                <input id="form_members_account_first_name" name="first_name" type="text" placeholder="First Name" value="[[*first_name]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_account_last_name">Last Name</label>
                <input id="form_members_account_last_name" name="last_name" type="text" placeholder="Last Name" value="[[*last_name]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_account_company">Company</label>
                <input id="form_members_account_company" name="company" type="text" placeholder="Company" value="[[*company]]">
            </div>
            <div class="form_row_container form_row_account_street_address_container">
                <input id="form_members_account_street_address_place_id" name="place_id" type="hidden" value="[[*place_id]]">
                <label for="form_members_account_street_address">Street Address</label>
                <input id="form_members_account_street_address" class="form_row_street_address_input" type="text">
                <div class="form_row_street_address_display_container"></div>
                <div id="form_members_account_street_address_map" class="form_row_street_address_map"></div>
            </div>
            <div class="form_row_container">
                <label for="form_members_account_telephone">Phone</label>
                <input id="form_members_account_telephone" name="telephone" type="text" placeholder="Phone" value="[[*telephone]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_account_fax_number">Fax</label>
                <input id="form_members_account_fax_number" name="fax_number" type="text" placeholder="Fax" value="[[*fax_number]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_account_email">Email Address</label>
                <input id="form_members_account_email" name="email" type="text" placeholder="Email Address" value="[[*email]]">
            </div>
            <div class="form_row_container">
                <label for="form_members_account_website_uri">Website</label>
                <input id="form_members_account_website_uri" name="website_uri" type="text" placeholder="Website" value="[[*website_uri]]">
            </div>
            <!-- div class="form_row_container general_style_input_button_container">
                <label for="form_members_account_password">Change Password</label>
                <input id="form_members_account_password" class="general_style_input_button general_style_input_button_gray" type="button" value="Change Password">
            </div -->
            <div class="form_bottom_row_container"></div>
            <div class="footer_action_wrapper"><!--
            --><a href="[[*base]]members/" class="footer_action_button footer_action_button_back">Back</a><!--
            --><a href="javascript:void(0)" class="footer_action_button footer_action_button_reset">Reset</a><!--
            --><a href="javascript:void(0)" class="footer_action_button footer_action_button_save">Save</a><!--
        --></div>
            <div class="ajax_form_mask"><div class="ajax_form_mask_loading_icon"></div><div class="ajax_form_info"></div></div>
        </form>
    </div>
</div>
[[-script:field_name=`google_map`:field=`{"source":"https://maps.googleapis.com/maps/api/js?key=AIzaSyAtw-geY0B0clS4SRzPsYfvT0ROsSl3JVA&libraries=places&callback=initialize_autocomplete"}`]]