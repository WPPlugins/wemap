function wemap_geolocate() {
    var input = document.getElementById('autocomplete');
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
      var place = autocomplete.getPlace();
      document.getElementById('pinpoint_latitude').value = place.geometry.location.lat();
      document.getElementById('pinpoint_longitude').value = place.geometry.location.lng();
    });
}
