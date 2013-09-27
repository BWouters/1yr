<?php
session_start();
require_once("classes/Image.class.php");


$image = new Image();

if(!isset($_GET['debug'])){
header('Content-type: image/jpg');	
}

$venID = $_GET['venID'];
$venues = $_SESSION['venues'];

$getVenue = "";
foreach($venues as $venue){
	$unserializedVenue = unserialize($venue);
	if($unserializedVenue->id == $venID){
		$getVenue = $unserializedVenue;
		break;
	}
}
//print_r($getVenue);

$checkindate = DateTime::createFromFormat("U", $getVenue->createdAt);
$datenowFormattedRead = $checkindate->format("Y-m-d H:i");

$location = $getVenue->venue->location;
$text = "Location: ".$getVenue->venue->name;
if(!$getVenue->venue->isFuzzed){
	$text.= " - ".$getVenue->venue->location->address.", ".$getVenue->venue->location->city." in ".$getVenue->venue->location->country;
}




$text .= "\nTime: ".$datenowFormattedRead;
$image->setText($text);
$moretext = "Checkins: ".$getVenue->venue->beenHere->count;
$image->setCheckinText($moretext);


$image->setPhoto("./uploads/temp/panorama_ballstad.jpg");
/*$photo = new Imagick("./uploads/temp/panorama_ballstad.jpg");
$photo->scaleImage(960, 0);*/


$image->setMapImage("http://maps.googleapis.com/maps/api/staticmap?center=".$location->lat.",".$location->lng."&zoom=15&size=200x150&sensor=true&format=jpg&visual_refresh=true&markers=color:blue|".$location->lat.",".$location->lng);
/*$mapImage = new Imagick("http://maps.googleapis.com/maps/api/staticmap?center=".$location->lat.",".$location->lng."&zoom=15&size=200x150&sensor=true&format=jpg&visual_refresh=true&markers=color:blue|".$location->lat.",".$location->lng);
$mapImage->borderImage("#f45", 3, 3);*/



$url = $getVenue->venue->categories[0]->icon;
$image->setCategoryIcon($url);
//$icon = new Imagick($url);



//$mayor = new Imagick("./img/mayor.png");
//$mayor->setImageFormat("jpg");

// Context

$image->addTextToImage();
echo $image->generateImage($getVenue->isMayor, $getVenue->shout);
/*$info = new Imagick();
$info->newImage(960, 200,"white", "jpg");

$draw = new ImagickDraw();
$pixel = new ImagickPixel( 'grey' );
$draw->setFillColor("black");
$draw->setFont('Bookman');
$draw->setFontSize(15 );
$draw->setGravity(Imagick::GRAVITY_NORTHWEST);*/

/* Positioning and adding text */
/*$info->annotateImage($draw, 230, 20, 0, $text);
$info->annotateImage($draw, 230, 50, 0, $moretext);
if($getVenue->isMayor){
	$info->compositeImage($mayor, Imagick::COMPOSITE_DEFAULT, 230, 110);
}
if(!is_null($getVenue->shout)){
	$shout = $getVenue->shout;
	$draw->SetFontSize(30);
	$draw->setFontStyle(Imagick::STYLE_ITALIC);
	$info->annotateImage($draw, 260, 140, 0, "\"".$shout."\"");
}
$info->compositeImage($icon, Imagick::COMPOSITE_DEFAULT, 230, 80);
$info->compositeImage($mapImage, Imagick::COMPOSITE_DEFAULT, 20, 20);
$combined = new Imagick();
$combined->addImage($photo);

$combined->addImage($info);
$combined->resetIterator();

$combo = $combined->appendImages(TRUE);

echo $combo;*/


//echo $combined;*/
?>