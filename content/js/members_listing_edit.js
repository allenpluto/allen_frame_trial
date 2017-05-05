/**
 * Created by User on 15/03/2017.
 */
var autocomplete,map,geocoder,marker,service;
var autocomplete_active = true;

function displayPlace(place, status)
{
    if (status != google.maps.places.PlacesServiceStatus.OK) {
console.log(status);
        resetMap();
        return false;
    }
    var google_place_row = [];
    var address_components_length = place['address_components'].length;
    for (var j=0; j<address_components_length; j++)
    {
        var type = place['address_components'][j]['types'][0];
        google_place_row[type] = place['address_components'][j]['long_name'];
        google_place_row[type+'_short'] = place['address_components'][j]['short_name'];
    }

    $('.form_row_street_address_display_container').html('<span class="form_row_street_address_display_row form_row_street_address_display_row_name">'+place['name']+'</span><span class="form_row_street_address_display_row form_row_street_address_display_row_suburb">'+google_place_row['locality']+', '+google_place_row['administrative_area_level_1_short']+' '+google_place_row['postal_code']+'</span><span class="form_row_street_address_display_row form_row_street_address_display_row_country">'+google_place_row['country']+'</span><a href="javascript:void(0);" class="reset_map font_icon">&#xf040;</a> ');
    $('#form_members_organization_street_address_place_id').val(place['place_id']);
    console.log(google_place_row);
    initialMap(place['geometry'].location);
}

function initialMap(map_center, map_zoom, map_mapTypeId)
{
    if (typeof map_zoom == 'undefined')
    {
        map_zoom = 14;
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
    if (!map)
    {
        map = new google.maps.Map(document.getElementById("form_members_organization_street_address_map"), mapOptions);

        // Initial Marker
        marker = new google.maps.Marker({
            draggable: false,
            map: map,
            position: map_center
        });
    }
    else
    {
        map.setCenter(map_center);
        marker.setPosition(map_center);
    }
    $(".form_row_organization_street_address_container").addClass('form_row_organization_street_address_container_show_map');
}

function resetMap()
{
    $(".form_row_organization_street_address_container").removeClass('form_row_organization_street_address_container_show_map');
    $('#form_members_organization_street_address').val('');
    $('#form_members_organization_street_address_place_id').val('');
    $('.form_row_street_address_display_container').html('');
}

$('.form_row_street_address_display_container').on('click', '.reset_map', function(){
    resetMap();
});

function fillInAddress()
{
    if (!autocomplete_active)
    {
        return false;
    }
    autocomplete_active = false;
    var mapOptions = null;
    var map = null;

    var place = autocomplete.getPlace();
//console.log(place);
    if (place['address_components'])
    {
        displayPlace(place,google.maps.places.PlacesServiceStatus.OK);
    }
    autocomplete_active = true;
}

function initialize_autocomplete()
{
    autocomplete = new google.maps.places.Autocomplete(
        /** @type {!HTMLInputElement} */(document.getElementById('form_members_organization_street_address')),
        {types: ['address'],componentRestrictions: {country: 'au'}});

    // When the user selects an address from the dropdown, populate the address
    // fields in the form.
    autocomplete.addListener('place_changed', fillInAddress);

    $('#form_members_organization_street_address_place_id').trigger('change');
}

$('#form_members_organization_street_address_place_id').change(function(){
    var place_id = $(this).val();
//console.log(place_id);
    if (place_id)
    {
        var sydney_center = new google.maps.LatLng(-33.8736509,151.2068896);
        initialMap(sydney_center);

        var request = {'placeId':place_id};
        service = new google.maps.places.PlacesService(map);
        service.getDetails(request, displayPlace);
    }
    else
    {
        resetMap();
    }
});

$('.footer_action_button_reset').click(function(event){
    event.preventDefault();
console.log('reset clicked');
    $(this).closest('.ajax_form_container').trigger('retrieve_form_data');
});

$('.footer_action_button_save').click(function(event){
    event.preventDefault();
console.log('save clicked');
    $(this).closest('.ajax_form_container').trigger('post_form_data');
});