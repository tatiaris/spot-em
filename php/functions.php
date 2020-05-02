<?
$f = $_GET["f"];

if ($f == "get_endangered_species"){
    echo get_endangered_species();
} else if ($f == "submit_report"){
    echo submit_report();
} else if ($f == "fill_animal_info"){
    return fill_animal_info();
} else if ($f == "get_points") {
    echo get_points();
}

function get_endangered_species(){
    $conn = new mysqli("localhost", "tatiakqf_admin", "Gottobe$&@me", "tatiakqf_spotem");
    // $conn = new mysqli("localhost", "root", "", "spotem");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $names = $conn->query("SELECT * FROM `endangered_animals`");
    $names_string = "";

    foreach ($names as $key => $name) {
        if (strlen($names_string) < 1) {
            $names_string = $name['name'];
        } else {
            $names_string .= "---" . $name['name'];
        }
    }
    return $names_string;
}

function submit_report(){
    $conn = new mysqli("localhost", "tatiakqf_admin", "Gottobe$&@me", "tatiakqf_spotem");
    // $conn = new mysqli("localhost", "root", "", "spotem");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $url = $_GET["url"];
    $lat = $_GET["lat"];
    $lng = $_GET["long"];

    $sql = $conn->query("INSERT INTO `endangered_locations`(`url`, `lng`, `lat`) VALUES (\"$url\", \"$lng\", \"$lat\")");
    $result = $conn->query($sql);

    if (!$result) {
        return "Thank you for your submission. We are very grateful!";
    } else {
        return "Sorry, there was an error, please try again.";
    }
}

function fill_animal_info(){
    $conn = new mysqli("localhost", "tatiakqf_admin", "Gottobe$&@me", "tatiakqf_spotem");
    // $conn = new mysqli("localhost", "root", "", "spotem");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $animals = $conn->query("SELECT * FROM `endangered_animals`");
    foreach ($animals as $key => $animal) {
        ?>
        <div class="animal-info">
            <div class="animal-img display-center">
                <img src="./images/<?echo $animal["img"];?>" alt="bg.png" width="100%" height="100%">
            </div>
            <div class="animal-name display-center">
                <?echo $animal["name"];?>
            </div>
        </div>
        <?
    }
}

function get_points() {
    $conn = new mysqli("localhost", "tatiakqf_admin", "Gottobe$&@me", "tatiakqf_spotem");
    // $conn = new mysqli("localhost", "root", "", "spotem");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $coords = $conn->query("SELECT * FROM `endangered_locations`");
    $coords_string = "";
    foreach ($coords as $key => $coo) {
        if (strlen($coords_string) < 1){
            $coords_string = $coo["url"] . "," . $coo["lat"] . "," . $coo["lng"];
        } else {
            $coords_string .= "---" . $coo["url"] . "," . $coo["lat"] . "," . $coo["lng"];
        }
    }
    return $coords_string;
}

?>
