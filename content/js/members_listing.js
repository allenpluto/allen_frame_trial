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

function fillInAddress()
{
    if (!autocomplete_active)
    {
        return false;
    }
    autocomplete_active = false;
    var mapOptions = null;
    var map = null;
    var rev_geocoder = new google.maps.Geocoder();

    var place = autocomplete.getPlace();
    // document.getElementById('map-options').innerHTML = place['formatted_address']+'('+place['types'][0]+':'+place['geometry'].location+')';
    $('#member_listing_edit_form').data('google_place_autocomplete', [place])

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
        if (street_address)
        {
            document.getElementById('listing_edit_form_address').value = street_address;
            document.getElementById('listing_edit_form_suburb').value = google_place_row['locality'];
            document.getElementById('listing_edit_form_state').value = google_place_row['administrative_area_level_1_short'];
            document.getElementById('listing_edit_form_post').value = google_place_row['postal_code'];

            initialize_google_map(place['geometry'].location);
        }
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
