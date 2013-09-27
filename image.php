<?php
session_start();
require_once("classes/Image.class.php");


$image = new Image();



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
$fileURL = "user/".session_id()."/".$venID.".jpg";
if(file_exists($fileURL)){
	echo "Image already created";
	echo "<img src='".$fileURL."' alt='".$venID."' />";
	echo "http://".$_SERVER['SERVER_NAME']."/".$fileURL;
}else{
	if(!isset($_GET['debug'])){
		header('Content-type: image/jpg');	
	}
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


	if(isset($_GET['photourl'])){
		$photoURL = $_GET['photourl'];
		$image->setPhoto($photoURL);
	}else{
		$image->setPhoto("./uploads/temp/panorama_ballstad.jpg");
	}
	

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
	$image->generateImage($getVenue->isMayor, $getVenue->shout);
	$image->saveImage($venID);
	echo $image->showImage();
}

?>