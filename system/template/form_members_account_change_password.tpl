<div class="section_container container form_container">
    <div class="section_title"><h1>Change Password</h1></div>
    <div class="section_content ajax_form_container">
        <form id="form_members_account_change_password" class="ajax_form">
            <div class="form_row_container">
                <label for="form_members_account_password">New Password</label>
                <input id="form_members_account_password" name="password" type="password" placeholder="New Password" value="">
            </div>
            <div class="form_row_container">
                <label for="form_members_account_password_repeat">Retype Password</label>
                <input id="form_members_account_password_repeat" name="password_repeat" type="password" placeholder="Type the Password Again" value="">
            </div>
            <div class="form_bottom_row_container"></div>
            <div class="footer_action_wrapper"><!--
            --><a href="[[*base]]members/" class="footer_action_button footer_action_button_back">Back</a><!--
            --><a href="javascript:void(0)" class="footer_action_button footer_action_button_save">Save</a><!--
        --></div>
            <div class="ajax_form_mask"><div class="ajax_form_mask_loading_icon"></div><div class="ajax_form_info"></div></div>
        </form>
    </div>
</div>