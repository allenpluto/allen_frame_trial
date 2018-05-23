<div id="search_wrapper" class="wrapper display_on_top_bar">
    <div id="search_container" class="container"><!--
        --><div id="search_wrapper_close" class="search_halt"></div><!--
        --><div id="search_keyword_container" class="search_form_row">
            <label for="search_keyword">What are you looking for?</label>
            <input name="keyword" type="text" placeholder="What are you looking for?" id="search_keyword" class="general_style_input_text" value="[[&search_what]]">
            <input name="category_id" type="hidden">
        </div><!--
        --><div id="search_location_container" class="search_form_row">
            <label for="search_location">In which Suburb?</label>
            <input name="location" type="text" placeholder="In which Suburb?" id="search_location" class="general_style_input_text" value="[[&search_where]]">
            <input name="location_id" type="hidden" id="search_location_place_id">
            <input name="geo_location" type="hidden" id="search_location_geometry_location">
        </div><!--
        --><div id="search_submit_container" class="search_form_row">
            <a id="search_submit" class="general_style_input_button general_style_input_button_orange"><span>Search</span></a>
        </div><!--
    --></div>
</div><!-- #search_wrapper -->