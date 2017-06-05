<div class="section_container container form_container">
    <div class="section_title"><h1>Manage Gallery</h1></div>
    <div class="section_content ajax_form_container">
        <form id="form_members_gallery" class="ajax_form">
            <div class="form_row_container">
                <label for="form_members_gallery_name">Gallery Name</label>
                <input id="form_members_gallery_name" name="name" type="text" placeholder="Gallery Name" value="[[*name]]">
            </div>
            <input name="remove_image_id" type="hidden" value="">
            <input name="gallery_image" type="hidden" value="[[*gallery_image]]">
            <div class="form_row_container form_gallery_image_container">
                <div class="form_gallery_image_container">
                    <a href="javascript:void(0);" class="form_image_uploader_trigger"><img class="form_gallery_image_file" src="./image/upload_image.jpg"></a>
                </div>
            </div>
            [[image:object=`view_gallery_image`:template_name=`view_members_gallery_image`]]
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