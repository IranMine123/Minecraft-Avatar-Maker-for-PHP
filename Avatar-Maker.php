<?php
// Checking for Variables
if ((isset($_GET["width"]) && $_GET["width"] != "" && $_GET["width"] <= 2048) ||
    (isset($_GET["height"]) && $_GET["height"] != "" && $_GET["height"] <= 2048) ||
    (isset($_GET["size"]) && $_GET["size"] != "" && $_GET["size"] <= 2048)) {
    // Check for Width variable
    if (isset($_GET["width"]) && $_GET["width"] != "" && $_GET["width"] <= 2048) {
        $width = $_GET["width"];
    }
    // Check for Height variable
    if (isset($_GET["height"]) && $_GET["height"] != "" && $_GET["height"] <= 2048) {
        $height = $_GET["height"];
    }
    // Check for Size variable which affect on both width and height
    if (isset($_GET["size"]) && $_GET["size"] != "" && $_GET["size"] <= 2048) {
        $height = $_GET["size"];
        $width = $_GET["size"];
    }
}
// Default Height
if (!isset($height)) {
    $height = 256;
}
// Default Width
if (!isset($width)) {
    $width = 256;
}
// Check Username Variable
if (isset($_GET["username"])) {

    // Get UUID from Mojang API
    $url = "https://api.mojang.com/users/profiles/minecraft/" . $_GET["username"];
    if (file_get_contents($url)) {
        $json = file_get_contents($url);
        $obj = json_decode($json);
        $uuid = $obj->id;

        // Get the Skin from Mojang API with UUID
        $url = "https://sessionserver.mojang.com/session/minecraft/profile/" . $uuid;
        $json = file_get_contents($url);
        $obj = json_decode($json);
        $value = $obj->properties[0]->value;
        $value2 = json_decode(base64_decode($value));
        $skin = $value2->textures->SKIN->url;
        $file = imagecreatefrompng($skin);
    } else {
        // Return Steve Head if Failed/Not Allowed/Not Found.
        $file = imagecreatefrompng("./steve.png");
    }

    // Image Function Starts
    $format = $_GET["format"] ?? "png";
    $result = imagecreatetruecolor($width,$height);
    // Check for JPEG/JPG Format
    if ($format == "jpeg" || $format == "jpg") {
        $format = "jpeg";
        $bga = imagecolorallocate($result, 255, 255, 255);
    } else {
        // Functions for PNG Format
        $bga = imagecolorallocatealpha($result, 175, 54, 134, 127);
        imagecolortransparent($result, $bga);
    }
    imagefill($result, 0, 0, $bga);

    // Function for changing 'imagecopy' Positions.
    function relocatePosition($n, $size) {
        $number = $n * ($size/272);
        return $number;
    }

    // Copying the Head of Skin
    imagecopyresampled($result,$file,relocatePosition(8, $width),relocatePosition(8, $height),8,8,relocatePosition(256, $width),relocatePosition(256, $height),8,8);
    imagecopyresampled($result,$file,relocatePosition(0, $width),relocatePosition(0, $height),40,8,relocatePosition(272, $width),relocatePosition(272, $height),8,8);

    // Enable Preview for use with Browser
    header("Content-Type: image/".$format);

    // Printing Image
    if ($format == "jpeg") {imagejpeg($result);}
    else {imagepng($result);}
    imagedestroy($result);

} else return null;
