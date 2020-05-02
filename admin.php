<!DOCTYPE html>
<html>
    <head>
        <style>
            /* Set the size of the div element that contains the map */
            #map {
                height: 90vh;  /* The height is 400 pixels */
                width: 100%;  /* The width is the width of the web page */
            }
            .page-title {
                width: 100%;
                height: 10vh;
                font-size: 2.5em;
                text-align: center;
            }
        </style>
    </head>
    <body style="margin: 0px; background-color: #abdaff;">
        <div class="page-title">Admin Map</div>
        <div id="map"></div>
        <script>
        // Initialize and add the map
            var map, marker;
            function initMap() {
                var uluru = {lat: -25.344, lng: 131.036};

                map = new google.maps.Map(document.getElementById('map'), {zoom: 4, center: uluru});

                // marker = new google.maps.Marker({position: uluru, map: map});

                get_points();
            }

            function get_points() {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        add_points(this.responseText);
                   }
                };

                xhttp.open("GET", "./php/functions.php?f=get_points");
                xhttp.send();
            }

            function add_points(all_points){
                var all_locations = all_points.split("---");
                for (var i = 0; i < all_locations.length; i++){
                    all_locations[i] = all_locations[i].split(",");
                    all_locations[i][1] = parseFloat(all_locations[i][1]);
                    all_locations[i][2] = parseFloat(all_locations[i][2]);

                    var icon = {
                        url: all_locations[i][0], // url
                        scaledSize: new google.maps.Size(90, 90), // scaled size
                        origin: new google.maps.Point(0,0), // origin
                        anchor: new google.maps.Point(0, 0) // anchor
                    };

                    marker = new google.maps.Marker({position: {lat: all_locations[i][1], lng: all_locations[i][2]}, map: map, icon: icon});
                }
            }

        </script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBn63GZzWYL531aHdVP2JhPTAfthnAceTo&callback=initMap">
        </script>
    </body>
</html>
