<div class="section_container container form_container">
    <div class="section_title"><h1>Edit My Business</h1></div>
    <div class="section_content ajax_form_container">
        <form id="form_members_organization" class="ajax_form">
            <div class="form_row_container">
                <label for="form_members_organization_name">Business Name</label>
                <input id="form_members_organization_name" name="name" type="text" placeholder="Business Name" value="[[*name]]">
            </div>
            <div class="form_row_container form_row_organization_logo_container">
                <label>Logo</label>
                [[logo_id:object=`view_image`:template_name=`form_image_uploader`:empty_template_name=`form_image_uploader`:field=`{"file_uri":"./image/upload_logo.jpg","field_name":"logo","image_uploader_id":"form_members_organization_logo"}`]]
            </div>
            <div class="form_row_container form_row_organization_banner_container">
                <label>Banner</label>
                [[banner_id:object=`view_image`:template_name=`form_image_uploader`:empty_template_name=`form_image_uploader`:field=`{"file_uri":"./image/upload_banner.jpg","field_name":"banner","image_uploader_id":"form_members_organization_banner"}`]]
            </div>
            <div class="form_row_container form_row_organization_street_address_container">
                <input id="form_members_organization_street_address_place_id" name="place_id" type="hidden" value="[[*place_id]]">
                <label for="form_members_organization_street_address">Street Address</label>
                <input id="form_members_organization_street_address" class="form_row_street_address_input" type="text">
                <div class="form_row_street_address_display_container"></div>
                <div id="form_members_organization_street_address_map" class="form_row_street_address_map"></div>
            </div>
            <div class="form_row_container">
                <label for="form_members_organization_category">Category</label>
                <div id="form_members_organization_category" class="form_select_container">
                    <input class="form_select_result" name="category" type="hidden" placeholder="Category" value="[[*category]]">
                    <select class="form_select_input"></select>
                    <div class="form_select_display_container"></div>
                </div>
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
            <div class="form_row_container">
                <label>Opening Hours</label>
                <div class="form_hours_work_container">
                    <input class="form_hours_work_result" type="hidden" name="hours_work" value="[[*hours_work]]">
                    <div class="form_hours_work_display"></div>
                    <div class="form_hours_work_input">
                        <div class="form_hours_work_input_label">Time Period</div>
                        <select class="form_hours_work_input_time">
                            <option value="[[0.375,0.708333333333]]">9:00 to 17:00</option>
                            <option value="[[0,1]]">24 Hours</option>
                            <option value="closed">Closed</option>
                            <option value="custom">Customise</option>
                        </select>
                        <div class="form_hours_work_input_time_custom_container">
                            <input class="form_hours_work_input_time_custom_result" type="hidden" value="[[0.375,0.708333333333]]">
                            <div class="form_hours_work_input_label">Open Time</div>
                            <select class="form_hours_work_input_time_custom_open"><option value="0">00:00</option><option value="0.0208333333">00:30</option><option value="0.0416666667">01:00</option><option value="0.0625">01:30</option><option value="0.0833333333">02:00</option><option value="0.1041666667">02:30</option><option value="0.125">03:00</option><option value="0.1458333333">03:30</option><option value="0.1666666667">04:00</option><option value="0.1875">04:30</option><option value="0.2083333333">05:00</option><option value="0.2291666667">05:30</option><option value="0.25">06:00</option><option value="0.2708333333">06:30</option><option value="0.2916666667">07:00</option><option value="0.3125">07:30</option><option value="0.3333333333">08:00</option><option value="0.3541666667">08:30</option><option value="0.375" selected>09:00</option><option value="0.3958333333">09:30</option><option value="0.4166666667">10:00</option><option value="0.4375">10:30</option><option value="0.4583333333">11:00</option><option value="0.4791666667">11:30</option><option value="0.5">12:00</option></select>
                            <div class="form_hours_work_input_label">Close Time</div>
                            <select class="form_hours_work_input_time_custom_close"><option value="0.5208333333">12:30</option><option value="0.5416666667">13:00</option><option value="0.5625">13:30</option><option value="0.5833333333">14:00</option><option value="0.6041666667">14:30</option><option value="0.625">15:00</option><option value="0.6458333333">15:30</option><option value="0.6666666667">16:00</option><option value="0.6875">16:30</option><option value="0.7083333333" selected>17:00</option><option value="0.7291666667">17:30</option><option value="0.75">18:00</option><option value="0.7708333333">18:30</option><option value="0.7916666667">19:00</option><option value="0.8125">19:30</option><option value="0.8333333333">20:00</option><option value="0.8541666667">20:30</option><option value="0.875">21:00</option><option value="0.8958333333">21:30</option><option value="0.9166666667">22:00</option><option value="0.9375">22:30</option><option value="0.9583333333">23:00</option><option value="0.9791666667">23:30</option><option value="1">23:59</option></select>
                        </div>
                        <div class="form_hours_work_input_label">Weekday</div>
                        <select class="form_hours_work_input_weekday">
                            <option value="1,2,3,4,5">Mon to Fri</option>
                            <option value="6,0">Sat and Sun</option>
                            <option value="1,2,3,4,5,6,0">All Week</option>
                            <option value="1">Monday</option>
                            <option value="2">Tuesday</option>
                            <option value="3">Wednesday</option>
                            <option value="4">Thursday</option>
                            <option value="5">Friday</option>
                            <option value="6">Saturday</option>
                            <option value="0">Sunday</option>
                        </select>
                        <input class="form_hours_work_input_submit general_style_input_button general_style_input_button_gray" type="button" value="Change Opening Hour">
                    </div>
                </div>
            </div>
            <div class="form_bottom_row_container"></div>
            <div class="footer_action_wrapper"><!--
            --><a href="[[*base]]members/listing/" class="footer_action_button footer_action_button_back">Back</a><!--
            --><a href="[[*base]]members/listing/reset" class="footer_action_button footer_action_button_reset">Reset</a><!--
            --><a href="[[*base]]members/listing/save" class="footer_action_button footer_action_button_save">Save</a><!--
        --></div>
            <div class="ajax_form_mask"><div class="ajax_form_mask_loading_icon"></div><div class="ajax_form_info"></div></div>
        </form>
    </div>
</div>
[[-script:field_name=`google_map`:field=`{"source":"https://maps.googleapis.com/maps/api/js?key=AIzaSyAtw-geY0B0clS4SRzPsYfvT0ROsSl3JVA&libraries=places&callback=initialize_autocomplete"}`]]