/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function initialize_map(latitude, longitude, event_location) {
    var latlngPos = new google.maps.LatLng(latitude, longitude);
    // Set up options for the Google map
    var myOptions = {
        zoom: 16,
        center: latlngPos,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoomControlOptions: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.LARGE
        }
    };
    // Define the map
    map = new google.maps.Map(document.getElementById(event_location), myOptions);
    // Add the marker
    var marker = new google.maps.Marker({
        position: latlngPos,
        map: map,
        title: "test"
    });
}
jQuery(document).ready(function ($) {
    jQuery("#show_map").click(function () {
        var address = $('#loc_address_1').val() + '+' + $('#loc_address_2').val() + '+' + $('#loc_city').val() + '+' + $('#loc_state').val() + '+' + $('#loc_zip').val() + '+' + $('#loc_country').val();
        jQuery.ajax({
            type: "post",
            url: ajaxurl,
            data: {action: "load_event_location", address: address},
            success: function (response) {
                jQuery("#event_location_map").html(response)

            }
        });
        return false;
    });
    $('#loc_address_1, #loc_address_2, #loc_city, #loc_state, #loc_zip, #loc_country').on('change', function () {
        var address = $('#loc_address_1').val() + "\n" + $('#loc_address_2').val() + "\n" + $('#loc_city').val() + "\n" + $('#loc_state').val() + "\n" + $('#loc_zip').val() + "\n" + $('#loc_country').val();
        $('#full_address').val(address)
    });
        $('#show_map').on('click', function () {
        var address = $('#loc_address_1').val() + "\n" + $('#loc_address_2').val() + "\n" + $('#loc_city').val() + "\n" + $('#loc_state').val() + "\n" + $('#loc_zip').val() + "\n" + $('#loc_country').val();

        $('#full_address').val(address)
    });
});


