/**
 * Created by User on 15/03/2017.
 */
$('#body_wrapper').click(function(event){
    //console.log(event.target);
    if ($(event.target).parents('.members_organization_block_container').hasClass('members_organization_block_container_active'))
    {
        $(event.target).parents('.members_organization_block_container').removeClass('members_organization_block_container_active')
    }
    else
    {
        $('.members_organization_block_container_active').removeClass('members_organization_block_container_active');
        $(event.target).parents('.members_organization_block_container').addClass('members_organization_block_container_active');
    }
});

var autocomplete,map,geocoder,marker;
var autocomplete_active = true;

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
        map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

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
    $("#map-canvas").show();
    //initialGeoCoder(map_center);
    //var start_position_latLng = null;
    //google.maps.event.addListener(marker, 'dragstart', function(event) {
    //    start_position_latLng = event.latLng;
    //});
    //google.maps.event.addListener(marker, 'dragend', function(event) {
    //    document.getElementById("map-options").innerHTML = '';
    //    initialGeoCoder(event.latLng);
    //});
}

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
    $('.form_row_street_address_display_container').html(place.adr_address+' '+place.place_id)
    // document.getElementById('map-options').innerHTML = place['formatted_address']+'('+place['types'][0]+':'+place['geometry'].location+')';
    //$('#member_listing_edit_form').data('google_place_autocomplete', [place])
console.log(place);
    if (place['address_components'])
    {
        var google_place_row = [];
        var address_components_length = place['address_components'].length;
        for (var j=0; j<address_components_length; j++)
        {
            var type = place['address_components'][j]['types'][0];
            google_place_row[type] = place['address_components'][j]['long_name'];
            google_place_row[type+'_short'] = place['address_components'][j]['short_name'];
        }

        var street_address = '';
        if (google_place_row['route'])
        {
            street_address = google_place_row['route'];
            if (google_place_row['street_number'])
            {
                street_address = google_place_row['street_number']+' '+street_address;
            }
            if (google_place_row['subpremise'])
            {
                street_address = google_place_row['subpremise']+' / '+street_address;
            }
        }
        initialMap(place['geometry'].location);
        //if (street_address)
        //{
        //    document.getElementById('listing_edit_form_address').value = street_address;
        //    document.getElementById('listing_edit_form_suburb').value = google_place_row['locality'];
        //    document.getElementById('listing_edit_form_state').value = google_place_row['administrative_area_level_1_short'];
        //    document.getElementById('listing_edit_form_post').value = google_place_row['postal_code'];
        //
        //    initialize_google_map(place['geometry'].location);
        //}
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
}
