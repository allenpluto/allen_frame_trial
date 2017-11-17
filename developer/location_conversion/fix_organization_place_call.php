<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <style type="text/css">
        html { height: 100% }
        body { height: 100%; margin: 0; padding: 0 }
        #map-canvas { width:500px; height:500px; max-height: 100%; }
        .container {display:block;max-width:1200px;margin:0 auto;padding-top:20px;}
        .column_container {display:inline-block;width:45%;padding:0 2%;vertical-align: top;}
        .listing_container {display: block; padding: 15px; margin-bottom: 10px; background:#ffffff; border:2px solid #eeeeee; }
        .listing_container_active {border-color: #aaffaa;}
        .listing_container_similar {background: #ccffcc;}
        .listing_container_same {background: #66ff66;}
        .listing_original_address,
        .listing_google_address {display: inline-block; width: 50%;vertical-align: top;}
        .listing_button_container {display: block; padding: 5px 0; text-align: center;}
        .listing_button {margin:0 5px;}
        .listing_message_container {color:#00cc00}
        .error {color:#cc0000;}
    </style>
    <script type="text/javascript" src="/allen_frame_trial/content/js/jquery-1.11.3.js"></script>
    <script type="text/javascript">
        var autocomplete,map,geocoder;
        var colour_set = ['#000000','#FF0000','#00FF00','#0000FF','#999900','#009999','#FF00FF','#000000','#000000','#000000','#000000','#000000','#000000'];
        var rect_stack = [];

        function flatten_google_place(place)
        {
            var flatten_place = {'id':place['place_id']};
console.log(place);
            var place_type = place['types'][0];
            var address_component_field = ['subpremise','street_number','route','sublocality','locality','colloquial_area','postal_code','administrative_area_level_2','administrative_area_level_1','country'];
            if (address_component_field.indexOf(place_type) < 0)
            {
                // For place type like 'street_address' and 'intersection', use short route name as alias
                place_type = 'route';
            }
            if (place['address_components'])
            {
                var additional_address_components = [];
                place['address_components'].forEach(function(address_component,address_component_index) {
                    var component_type = address_component['types'][0];
                    if (component_type == place_type && (!flatten_place['alternate_name']))
                    {
                        flatten_place['alternate_name'] = address_component['short_name'];
                    }
                    if (address_component_field.indexOf(component_type) >= 0)
                    {
                        flatten_place[component_type] = address_component['long_name'];
                    }
                    else
                    {
                        additional_address_components[component_type] = address_component['long_name'];
                    }
                    if (additional_address_components) flatten_place['address_additional'] = additional_address_components;
                });
                if (flatten_place['street_number'])
                {
                    // If street_number is provided, add street_number to route
                    flatten_place['alternate_name'] = flatten_place['street_number']+' '+flatten_place['route'];
                }
                if (flatten_place['subpremise'])
                {
                    // If street_number is provided, add street_number to route
                    flatten_place['alternate_name'] = flatten_place['subpremise']+'/'+flatten_place['alternate_name'];
                }
            }
            if (place['geometry'])
            {
                if (place['geometry']['location'])
                {
                    flatten_place['location_latitude'] = place['geometry']['location'].lat();
                    flatten_place['location_longitude'] = place['geometry']['location'].lng();
                }
                if (place['geometry']['viewport'])
                {
                    flatten_place['viewport_northeast_latitude'] = place['geometry']['viewport'].getNorthEast().lat();
                    flatten_place['viewport_northeast_longitude'] = place['geometry']['viewport'].getNorthEast().lng();
                    flatten_place['viewport_southwest_latitude'] = place['geometry']['viewport'].getSouthWest().lat();
                    flatten_place['viewport_southwest_longitude'] = place['geometry']['viewport'].getSouthWest().lng();
                }
                if (place['geometry']['bounds'])
                {
                    flatten_place['bounds_northeast_latitude'] = place['geometry']['bounds'].getNorthEast().lat();
                    flatten_place['bounds_northeast_longitude'] = place['geometry']['bounds'].getNorthEast().lng();
                    flatten_place['bounds_southwest_latitude'] = place['geometry']['bounds'].getSouthWest().lat();
                    flatten_place['bounds_southwest_longitude'] = place['geometry']['bounds'].getSouthWest().lng();
                }
            }
            if (place['photos'])
            {
                flatten_place['photos'] = [];
                place['photos'].forEach(function(photo, photo_index){
                    flatten_place['photos'].push({'height':photo.height,'width':photo.width,'html_attributions':photo.html_attributions,'url':photo.getUrl({'maxWidth':2400})});
                });
            }
            var flatten_fields = ['icon','formatted_address','formatted_phone_number','international_phone_number','utc_offset','opening_hours','permanently_closed','rating','reviews','types','url','website','name'];

            flatten_fields.forEach(function(item, index){
                if (place[item])
                {
                    flatten_place[item] = place[item];
                }
            });

            if (!(flatten_place['name']) && flatten_place[place_type])
            {
                flatten_place['name'] = flatten_place[place_type];
                if (flatten_place['street_number'])
                {
                    // If street_number is provided, add street_number to route
                    flatten_place['name'] = flatten_place['street_number']+' '+flatten_place['name'];
                }
            }
            var friendly_uri = flatten_place['name'];
            if(place_type == 'administrative_area_level_1' || place_type == 'administrative_area_level_2')
            {
                friendly_uri = flatten_place['alternate_name'];
            }
            friendly_uri = friendly_uri.toLowerCase();
            friendly_uri = friendly_uri.trim();
            friendly_uri = friendly_uri.replace(/[^-a-z0-9]/g, '-');
            friendly_uri = friendly_uri.replace(/-+/g, '-');
            flatten_place['friendly_uri'] = friendly_uri;

            return flatten_place;
        }

        function initialGeoCoder(map_center, change_place_data)
        {
            if (!geocoder)
            {
                geocoder = new google.maps.Geocoder();
            }
            if (rect_stack.length > 0)
            {
                var rect_stack_length = rect_stack.length;
                for (var i=0; i<rect_stack_length; i++)
                {
                    rect_stack[i].setMap(null);
                }
                rect_stack = [];
            }
            geocoder.geocode({'location':map_center},function(results, status){
                if (status === google.maps.GeocoderStatus.OK) {
                    var results_length = results.length;
                    var region_set = [];
                    var region_types = ['sublocality','locality','postal_code','administrative_area_level_2','administrative_area_level_1','country'];
                    if (results_length > 0)
                    {
                        if (change_place_data)
                        {
                            $('#map-canvas').data('place',flatten_google_place(results[0]));
                            $('#map-canvas').data('region',[]);
                        }
                        for (var i=0; i<results_length; i++)
                        {
                            if (i!=10)
                            {
                                var viewport = null;
                                if (results[i]['geometry']['bounds']) viewport = results[i]['geometry']['bounds'];
                                else if (results[i]['geometry']['viewport']) viewport = results[i]['geometry']['viewport'];
                                var rect = new google.maps.Rectangle({
                                    strokeColor: colour_set[i],
                                    strokeOpacity: 0.3,
                                    strokeWeight: 2,
                                    fillColor: '#FFFFFF',
                                    fillOpacity: 0,
                                    map: map,
                                    bounds: {
                                        north: viewport.getNorthEast().lat(),
                                        south: viewport.getSouthWest().lat(),
                                        east: viewport.getNorthEast().lng(),
                                        west: viewport.getSouthWest().lng()
                                    }
                                });
                                rect_stack.push(rect);
                                if (region_types.indexOf(results[i]['types'][0]) >= 0)
                                {
                                    region_set.push(results[i]['place_id']);
                                }
                                document.getElementById('map-options').innerHTML += '<br>'+results[i]['formatted_address']+'('+results[i]['types'][0]+':'+results[i]['geometry'].location+':'+results[i]['place_id']+')<span style="display:inline-block;width:10px;height:10px;border:2px solid '+colour_set[i]+';"></span>';
                            }
                        }
                    } else {
                        document.getElementById('map-options').innerHTML += '<br>No results found for reverse geocoder';
                    }
                    $('#map-canvas').data('region',region_set);
                } else {
                    document.getElementById('map-options').innerHTML += '<br>'+status+': reverse geocoder';
                };
            });
        }

        function initialMap(map_center, map_zoom, map_mapTypeId)
        {
            if (typeof map_zoom == 'undefined')
            {
                map_zoom = 16;
            }
            if (typeof map_mapTypeId == 'undefined')
            {
                map_mapTypeId = google.maps.MapTypeId.ROADMAP;
            }

            // Initial Map
            mapOptions = {
                center: map_center,
                zoom: map_zoom,
                mapTypeId: map_mapTypeId
            };
            map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
            initialGeoCoder(map_center);

            // Initial Marker
            var marker = new google.maps.Marker({
                draggable: true,
                map: map,
                position: map.getCenter(),
                title: 'Drag to change Address'
            });
            var start_position_latLng = null;
            google.maps.event.addListener(marker, 'dragstart', function(event) {
                start_position_latLng = event.latLng;
            });
            google.maps.event.addListener(marker, 'dragend', function(event) {
                document.getElementById("map-options").innerHTML = '';
                initialGeoCoder(event.latLng, true);
            });
        }

        function fillInAddress()
        {
            var mapOptions = null;
            var map = null;

            var place = autocomplete.getPlace();
//console.log(place);
//console.log(flatten_google_place(place));
            $('#map-canvas').data('place',flatten_google_place(place));
            $('#map-canvas').data('region',[]);
            document.getElementById('map-options').innerHTML = '<div id="source_place">'+place['formatted_address']+'('+place['types'][0]+':'+place['geometry'].location+':'+place['place_id']+')</div>';
            initialMap(place['geometry'].location);

            var listing_container = $('.listing_container_active');
            var listing = listing_container.data('listing');
            if (parseInt($('#map-canvas').data('place').postal_code) != listing['zip_code'])
            {
                $('#source_place').css('background','#ffff99').append('<div>'+$('#map-canvas').data('place').postal_code+' '+listing['zip_code']+'</div>');
            }
            else
            {
                $('#source_place').css('background','#99ff99');
            }
            if (place['place_id'] == listing['place_id'] && !$('#address_aditional').val())
            {
                listing_container.find('.listing_button_keep').focus();
            }
            else
            {
                listing_container.find('.listing_button_update').focus();
            }
        }

        function initialize()
        {
            var autocomplete_type = document.getElementById("map_type");

            var filter = {};
            if (autocomplete_type.value)
            {
                filter['types'] = [autocomplete_type.value];
            }

            autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById('address_autocomplete')),
                filter);

            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);

            autocomplete_type.onchange = function()
            {
                autocomplete.setTypes([autocomplete_type.value]);
            }
        }

        function load_listings(listing_id)
        {
            var content_container = $('.listing_wrapper');

            var post_value = {
                'method': 'get_listing',
                'limit': 10
            };
            if (typeof listing_id !== 'undefined')
            {
                post_value['listing_id'] = listing_id;
            }
            $.ajax({
                'type': 'POST',
                'url': '/developer/location_conversion/fix_organization_place_handler.php',
                'data': post_value,
                'dataType': 'json',
                'timeout': 30000,
                'success': function(result) {
                    if (result.success)
                    {
                        var address_components = ['address','address2','city','state','zip_code'];
                        result.updated_data.forEach(function(listing,listing_index){
                            var listing_container = $('<div />',{
                                'class': 'listing_container'
                            });
                            listing_container.data('listing',listing);
                            if (listing['place_locality'] && listing['place_locality'].toLowerCase() == listing['city'].toLowerCase())
                            {
                                if (listing['address'].toLowerCase() == listing['place_name'].toLowerCase() || listing['address'].toLowerCase() == listing['place_alt_name'].toLowerCase())
                                {
                                    listing_container.addClass('listing_container_same');
                                }
                                else
                                {
                                    listing_container.addClass('listing_container_similar');
                                }
                            }

                            var listing_title = $('<div />',{
                                'class': 'listing_title'
                            }).html('<strong>'+listing['id']+': '+listing['title']+'</strong>').appendTo(listing_container);
                            $('<div />',{
                                'class': 'listing_button_container'
                            }).html('<input class="listing_button listing_button_search_by_address" type="button" value="Search by Address"><input class="listing_button listing_button_search_by_business" type="button" value="Search by Business">').appendTo(listing_container);

                            var listing_original_address = $('<div />',{
                                'class': 'listing_original_address'
                            });
                            address_components.forEach(function(address_component,address_component_index){
                                if (listing[address_component])
                                {
                                    $('<div />',{
                                        'class': 'listing_address_row'
                                    }).html(address_component+': '+listing[address_component]).appendTo(listing_original_address);
                                }
                            });
                            listing_original_address.appendTo(listing_container);

                            var listing_google_address = $('<div />',{
                                'class': 'listing_google_address'
                            });
                            if (listing['place_id'])
                            {
                                $('<div />',{
                                    'class': 'listing_address_row'
                                }).html('Google address: '+listing['formatted_address']).appendTo(listing_google_address);
                                $('<div />',{
                                    'class': 'listing_address_row'
                                }).html('Google place id: '+listing['place_id']).appendTo(listing_google_address);
                            }
                            listing_google_address.appendTo(listing_container);
                            $('<div />',{
                                'class': 'listing_message_container'
                            }).appendTo(listing_container);
                            $('<div />',{
                                'class': 'listing_button_container'
                            }).html('<input class="listing_button listing_button_update" type="button" value="Use Map Address"><input class="listing_button listing_button_keep" type="button" value="Keep Current"><input class="listing_button listing_button_delete" type="button" value="Delete Place">').appendTo(listing_container);
                            listing_container.appendTo(content_container);
                        });
                        if ($('.listing_container_active').length == 0)
                        {
                            $('.listing_container:first').addClass('listing_container_active').find('.listing_button_search_by_address').click();
                        }

                        var first_listing = content_container.find('.listing_container:first').data('listing');
                        var last_listing = content_container.find('.listing_container:last').data('listing');
                        if (first_listing['address'].toLowerCase().trim() == last_listing['address'].toLowerCase().trim() && first_listing['city'] == last_listing['city'] && first_listing['state'] == last_listing['state'] && first_listing['zip_code'] == last_listing['zip_code'])
                        {
                            var current_listing_id = [];
                            content_container.find('.listing_container').each(function(){
                                current_listing_id.push($(this).data('listing').id);
                            });
                            load_listings(current_listing_id.join());
                        }
                    }
                    else
                    {
                        content_container.append('<div class="error"><p>Result Error</p></div>');
console.log(result);
                        if (result.error_message)
                        {
                            result.error_message.forEach(function(item,index){
                                content_container.append('<div class="error"><p>'+item+'</p></div>');
                            });
                        }
                    }
                },
                'error': function(request, status, error) {
                    counter = max_count;
                    if (status == 'timeout')
                    {
                        content_container.append('<div class="error"><p>Timeout, requested server not responding</p></div>');
                    }
                    else
                    {
                        content_container.append('<div class="error"><p>Unknown Error, Submit Changes Failed</p></div>');
                    }
                },
                'complete': function()
                {
                }
            });
        }

        function set_listing(post_value,listing_container)
        {
            var content_container = listing_container.find('.listing_message_container');
console.log(post_value);
            $.ajax({
                'type': 'POST',
                'url': '/developer/location_conversion/fix_organization_place_handler.php',
                'data': post_value,
                'dataType': 'json',
                'timeout': 30000,
                'success': function(result) {
//                    console.log('result');
//                    console.log(result.success);
//                    console.log(result);
                    if (result.success)
                    {
                        content_container.append('<p>Place Updated</p>');
                        setTimeout(function(){
                            listing_container.fadeOut(100,function(){
                                var listing = $(this).data('listing');
                                $(this).remove();

                                var new_listing_container = $('.listing_wrapper .listing_container:eq(0)');
                                var compare_listing = new_listing_container.data('listing');

                                if (compare_listing['address_refined'] == listing['address_refined'] && compare_listing['city'] == listing['city'] && compare_listing['state'] == listing['state'] && compare_listing['zip_code'] == listing['zip_code'])
                                {
                                    $('#address_aditional').val(compare_listing['address_additional']);
                                    new_listing_container.find('.listing_button_update').focus();
                                }
                                else
                                {
                                    new_listing_container.find('.listing_button_search_by_address').click();
                                }


                                if ($('.listing_wrapper').find('.listing_container').length < 5)
                                {
                                    var current_listing_id = [];
                                    $('.listing_wrapper').find('.listing_container').each(function(){
                                        current_listing_id.push($(this).data('listing').id);
                                    });
                                    load_listings(current_listing_id.join());
                                }
                                else
                                {
                                    var first_listing = $('.listing_wrapper').find('.listing_container:first').data('listing');
                                    var last_listing = $('.listing_wrapper').find('.listing_container:last').data('listing');
                                    if (first_listing['address'].toLowerCase().trim() == last_listing['address'].toLowerCase().trim() && first_listing['city'] == last_listing['city'] && first_listing['state'] == last_listing['state'] && first_listing['zip_code'] == last_listing['zip_code'])
                                    {
                                        var current_listing_id = [];
                                        $('.listing_wrapper').find('.listing_container').each(function(){
                                            current_listing_id.push($(this).data('listing').id);
                                        });
                                        load_listings(current_listing_id.join());
                                    }
                                }
                            });
                        },100);
                    }
                    else
                    {
                        content_container.append('<div class="error"><p>Result Error</p></div>');
                        if (result.error_message)
                        {
                            result.error_message.forEach(function(item,index){
                                content_container.append('<div class="error"><p>'+item+'</p></div>');
                            });
                        }
                    }
                },
                'error': function(request, status, error) {
                    if (status == 'timeout')
                    {
                        content_container.append('<div class="error"><p>Timeout, requested server not responding</p></div>');
                    }
                    else
                    {
                        content_container.append('<div class="error"><p>Unknown Error, Submit Changes Failed</p></div>');
                    }
                },
                'complete': function(request)
                {
                    console.log(request);
                }
            });
        }

        $(document).ready(function(){
            load_listings();
            $('.listing_wrapper').on('click', '.listing_button_update', function(){
                var listing = $(this).closest('.listing_container').data('listing');
                var place = $('#map-canvas').data('place');
                var region = $('#map-canvas').data('region');
//console.log('update listing place');
//console.log(listing);
//console.log(place);
//console.log(region);
                var listing_container = $(this).closest('.listing_container');

                var post_value = {
                    'method': 'set_listing',
                    'id': [listing['id']],
                    'place': place,
                    'address_additional': $('#address_aditional').val(),
                    'region': region
                };
                $('.listing_container').each(function(){
                    if (!$(this).is(listing_container))
                    {
                        var compare_listing = $(this).data('listing');
                        if (compare_listing['address_refined'] == listing['address_refined'] && compare_listing['city'] == listing['city'] && compare_listing['state'] == listing['state'] && compare_listing['zip_code'] == listing['zip_code'])
                        {
                            if (compare_listing['address_additional'] == listing['address_additional'])
                            {
                                // Exact same location, submit multiple listing together
                                post_value['id'].push(compare_listing['id']);
                                $(this).remove();
                            }
                        }
                    }
                });
                set_listing(post_value,listing_container);
            });
            $('.listing_wrapper').on('click', '.listing_button_keep', function(){
                var listing = $(this).closest('.listing_container').data('listing');
                var listing_container = $(this).closest('.listing_container');

                var post_value = {
                    'method': 'set_listing',
                    'id': [listing['id']]
                };
                $('.listing_container').each(function(){
                    if (!$(this).is(listing_container))
                    {
                        var compare_listing = $(this).data('listing');
                        if (compare_listing['address'].toLowerCase().trim() == listing['address'].toLowerCase().trim() && compare_listing['city'] == listing['city'] && compare_listing['state'] == listing['state'] && compare_listing['zip_code'] == listing['zip_code'])
                        {
                            post_value['id'].push(compare_listing['id']);
                            $(this).remove();
                        }
                    }
                });
                set_listing(post_value,listing_container);
            });
            $('.listing_wrapper').on('click', '.listing_button_delete', function(){
                var listing = $(this).closest('.listing_container').data('listing');
                var listing_container = $(this).closest('.listing_container');

                var post_value = {
                    'method': 'set_listing',
                    'id': [listing['id']],
                    'place': ''
                };
                set_listing(post_value,listing_container);
            });
            $('.listing_wrapper').on('click', '.listing_button_search_by_address', function(){
                $(this).closest('.listing_container').addClass('listing_container_active');
                $('#address_aditional').val('');

                var listing = $(this).closest('.listing_container').data('listing');
                if (listing['address_additional'])
                {
                    $('#address_aditional').val(listing['address_additional']);
                }
                else
                {
                    if (listing['address2'])
                    {
                        $('#address_aditional').val(listing['address2']);
                    }
                }
                if (!listing['address_refined'] || listing['address'].toLowerCase().trim() == listing['city'].toLowerCase().trim())
                {
                    if ($('#map_type').val() != '(cities)')
                    {
                        $('#map_type').val('(cities)').trigger('change');
                    }
                    $('#address_autocomplete').val(listing['city']+', '+listing['state']).focus().trigger('click');
                }
                else
                {
                    if ($('#map_type').val() != 'address')
                    {
                        $('#map_type').val('address').trigger('change');
                    }
                    $('#address_autocomplete').val(listing['address_refined']+', '+listing['city']).focus().trigger('click');

                }
            });
            $('.listing_wrapper').on('click', '.listing_button_search_by_business', function(){
                $(this).closest('.listing_container').addClass('listing_container_active');
                $('#address_aditional').val('');

                var listing = $(this).closest('.listing_container').data('listing');
                if ($('#map_type').val() != 'establishment')
                {
                    $('#map_type').val('establishment').trigger('change');
                }
                $('#address_autocomplete').val(listing['title']).focus().trigger('click');
            });
        });
    </script>
</head>
<body>
<div class="container">
    <div class="left_column_container column_container">
        <div class="listing_wrapper">
        </div>
    </div>
    <div class="right_column_container column_container">
        <input type="text" id="address_aditional" name="address_aditional" style="width: 100%;margin-bottom: 10px;" placeholder="Additional Address Info, e.g. Company Name, Building Name, Unit X, Level X etc">
        <input type="text" id="address_autocomplete" name="address" style="width:400px;">
        <select id="map_type" name="map_type">
            <option value="">none</option>
            <option value="geocode">geocode</option>
            <option value="address" selected>address</option>
            <option value="establishment">establishment</option>
            <option value="(regions)">(regions)</option>
            <option value="(cities)">(cities)</option>
        </select>
        <div id="map-canvas"></div>
        <div id="map-options">
        </div>
        <script type="text/javascript"
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAtw-geY0B0clS4SRzPsYfvT0ROsSl3JVA&libraries=places&callback=initialize">
        </script>
    </div>
</div>
</body>
</html>