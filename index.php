<!DOCTYPE html>
<html>
<head>
    <title>Spot 'Em!</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <script type="text/javascript">
        var map, infoWindow, marker;

        function display_message(message){
            $("#message").text(message);
        }

        function log_pos(){
            console.log("lat: " + marker.getPosition().lat() + ", long: " + marker.getPosition().lng());
        }

        function processImage() {
            let subscriptionKey = '5b27b18d831d486ca6905d7f428b2a32';
            let endpoint = 'https://spot-em.cognitiveservices.azure.com/';

            if (!subscriptionKey) { throw new Error('Set your environment variables for your subscription key and endpoint.'); }

            var uriBase = endpoint + "vision/v2.1/analyze";

            // Request parameters.
            var params = {
                "visualFeatures": "description",
                "details": "",
                "language": "en",
            };

            // Display the image.
            var sourceImageUrl = document.getElementById("inputImage").value;
            document.querySelector("#sourceImage").src = sourceImageUrl;

            // Make the REST API call.
            $.ajax({
                url: uriBase + "?" + $.param(params),

                // Request headers.
                beforeSend: function(xhrObj){
                    xhrObj.setRequestHeader("Content-Type","application/json");
                    xhrObj.setRequestHeader(
                        "Ocp-Apim-Subscription-Key", subscriptionKey);
                },

                type: "POST",

                // Request body.
                data: '{"url": ' + '"' + sourceImageUrl + '"}',
            })

            .done(function(data) {
                is_endangered(data["description"]["tags"]);
            })

            .fail(function(jqXHR, textStatus, errorThrown) {
                // Display error message.
                var errorString = (errorThrown === "") ? "Error. " :
                    errorThrown + " (" + jqXHR.status + "): ";
                errorString += (jqXHR.responseText === "") ? "" :
                    jQuery.parseJSON(jqXHR.responseText).message;
                alert(errorString);
            });
        };

        function is_endangered(tags){
            var xhttp = new XMLHttpRequest();
            endangered_species_list = [];
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    endangered_species_string = this.responseText;
                    endangered_species_list = endangered_species_string.split("---");

                    for (var i = 0; i < endangered_species_list.length; i++) {
                        if (tags.includes(endangered_species_list[i].toLowerCase())){
                            $("#result").text("endangered");
                            return;
                        }
                    }
                    $("#result").text("not endangered");
                }
            };
            xhttp.open("GET", "./php/functions.php?f=get_endangered_species", true);
            xhttp.send();
        }

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: -34.397, lng: 150.644},
                zoom: 6
            });
            infoWindow = new google.maps.InfoWindow;

            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                infoWindow.setPosition(pos);
                infoWindow.setContent('You\'re here');
                infoWindow.open(map);

                marker = new google.maps.Marker({
                    position: pos,
                    map: map,
                    draggable: true,
                });

                map.setCenter(pos);
                }, function() {
                handleLocationError(true, infoWindow, map.getCenter());
                });
            } else {
              // Browser doesn't support Geolocation
              handleLocationError(false, infoWindow, map.getCenter());
            }
        }

        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                                  'Error: The Geolocation service failed.' :
                                  'Error: Your browser doesn\'t support geolocation.');
            infoWindow.open(map);
        }

        function submitReport() {
            lat = marker.getPosition().lat();
            long = marker.getPosition().lng();
            url = document.getElementById("inputImage").value;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    display_message(this.responseText);
               }
            };

            xhttp.open("GET", "./php/functions.php?f=submit_report&lat=" + lat + "&long=" + long + "&url=" + url, true);
            xhttp.send();
        }

        function fill_animal_info(){
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("animal-info-container").innerHTML = this.responseText;
               }
            };

            xhttp.open("GET", "./php/functions.php?f=fill_animal_info");
            xhttp.send();
        }
    </script>
    <div class="logo-container">
        <!-- <span class="logo-text">SPOT 'EM</span> -->
        <img src="./images/logo.png" alt="" style="width:15%;">
    </div>
    <div class="description-container">
        Select an image to find out whether the animal in it is endangered!
    </div>

    <div class="mega-container">
        <div class="spotter-container">
            <div class="image-input-container">
                Image to analyze:
                <input type="text" class="link-input" name="inputImage" id="inputImage" value="https://tatia.me/images/PRI_98455879.jpg">
                <button class="submit-button" onclick="processImage()">Analyze</button>
            </div>

            <div class="map-img-container">
                <div class="src-img-container">
                    <div id="imageDiv" style="width:420px;">
                        <img id="sourceImage" width="400" style="margin:5px;">
                    </div>
                    <div class="result-container">
                        <div id="result">

                        </div>
                    </div>
                </div>
                <div class="map-container">
                    <div id="map"></div>
                </div>

            </div>
            <div class="detail-container display-center" style="font-size: 1.5em; margin-top: 10px;">
                Drag the marker to where you spotted the animal, and hit Submit!
            </div>

            <div class="submit-button-container">
                <button class="submit-button-main" onclick="submitReport()">Submit</button>
            </div>
            <div class="message-container">
                <div id="message">
                </div>
            </div>
        </div>

        <div class="info-container">
            <div class="info-box">
                <div class="title-container display-center">
                    <span>Endangered Animals in Australia</span>
                </div>
                <div id="animal-info-container" class="animal-info-container">
                    <div class="animal-info">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Made by:- Rishabh Tatia, Tanya Kejriwal, Kunal Bantwal
    </div>

    <script>
        fill_animal_info();
    </script>

    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBn63GZzWYL531aHdVP2JhPTAfthnAceTo&callback=initMap">
    </script>
</body>
</html>
