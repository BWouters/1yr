<?php
	session_start();
	include_once("config.php");
	$key = $_REQUEST['code'];

	
	$redirect_uri = "http://1yr.visionsandviews.net/welcome.php"; //In this example the redirect_uri is just pointing back to this file

	$uri = @file_get_contents("https://foursquare.com/oauth2/access_token?client_id=".$client_id."&client_secret=".$client_secret."&grant_type=authorization_code&redirect_uri=".$redirect_uri."&code=".$key, 
	    true);
	if($uri === FALSE){
		echo "Access denied, <a href='index.php'>login again</a>";
		exit();
	}
	$obj = json_decode($uri);

	$usertoken = $obj->access_token; 
    //If you want to show "Connected App" check-in replies for this user you will need to save this access token  
    //in a database with the user's foursquare id so you get access it later 
	$datenow = new DateTime();
	$datenowFormattedRead = $datenow->format("Ymd");
	
	//echo $datenowFormattedRead;
	//echo "<br />";
	$dateoneyrago = $datenow->sub(new DateInterval("P1Y4W"));
	$dateoneyragoFormatted = $dateoneyrago->format("U");
	$dateoneyragoFormattedRead = $dateoneyrago->format("Y-m-d");
	//echo $dateoneyragoFormattedRead;
	
	$uri = file_get_contents("https://api.foursquare.com/v2/users/self/checkins?oauth_token=".$obj->access_token."&sort=oldestfirst&afterTimestamp=".$dateoneyragoFormatted."&v=".$datenowFormattedRead,
	  true); 
	$uri = file_get_contents("https://api.foursquare.com/v2/users/self/checkins?oauth_token=".$obj->access_token,
	  true); 

	$obj = json_decode($uri);
	// Pull the info you want to save about the user https://developer.foursquare.com/docs/responses/user
	// Examples
	$foursquareid = $obj->response->user->id;
	$firstname = $obj->response->user->firstName;
	$lastname = $obj->response->user->lastName;
		
		// Not all fields available are actually present in the user object..	
	    if(isset($obj->response->user->contact->phone))	
			$phone = $obj->response->user->contact->phone;
		else 	
	    	$phone="";

		
	    if(isset($obj->response->user->contact->email))	
			$email = $obj->response->user->contact->email;
		else 	
	    	$email="";
	echo "<ul>";
	/*foreach($obj->response->checkins->items as $item){
		$createdDataFormatted = DateTime::createFromFormat("U", $item->createdAt);
		//print_r($createdDataFormatted);
		//$_SESSION['venues'][] = serialize($item);
		$imageURL = "user/".session_id()."/".$item->id.".jpg";
		if(file_exists($imageURL)){
			//echo "Image already created";
		}
		if($item->photos->count > 0){
			foreach ($item->photos->items as $photo) {
				$photoURL .= " <a href='image.php?venID=".$item->id."&photourl=".$photo->url."'>Use own photo</a>";
			}

		}else{
			$photoURL = "";
		}
		//echo "<li>".$createdDataFormatted->format("Y-m-d H:i")." @ ".$item->venue->name." <a href='image.php?venID=".$item->id."'>Use this</a>".$photoURL."</li>";
		
	}*/
	//echo "</ul>";

?>
<html>
	<head>
		<script src="https://maps.googleapis.com/maps/api/js?v=3&amp;sensor=false"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<style type="text/css">
		#map{
			width: 100%;
			height: 100%;
		}
		</style>
		<script type="text/javascript">
		$(document).ready(function(){
			var map;
			var marker = null;
			var infowindow = new google.maps.InfoWindow();
			var bounds = new google.maps.LatLngBounds();
			var loaded = false;
			var counter = 0;
			function initialize() {
			  var mapOptions = {
			    zoom: 8,
			    center: new google.maps.LatLng(50.845175,4.357131),
			    mapTypeId: google.maps.MapTypeId.ROADMAP
			  };
			  map = new google.maps.Map(document.getElementById('map'),
			      mapOptions);

			<?php
			foreach ($obj->response->checkins->items as $item) {
				$createdDataFormatted = DateTime::createFromFormat("U", $item->createdAt);
				?>
				var latLng = new google.maps.LatLng(<?php echo $item->venue->location->lat.",".$item->venue->location->lng; ?>);
				marker = new google.maps.Marker({
					position: latLng,
					map:map,
					<?php
					$imageURL = "user/".session_id()."/".$item->id.".jpg";
					if(file_exists($imageURL)){
						?>
						info: "<div id='checkinContent'><?php echo $item->venue->name ?><img src='<?php echo $imageURL; ?>' alt='<?php echo $item->venue->name; ?>' />",
						<?php
					}else{
						$output = "";
						if($item->photos->count > 0){
							foreach ($item->photos->items as $photo) {
								$output .= "<p><a href='image.php?venID=".$item->id."&photourl=".$photo->url."'>Use own photo</a></p>";
							}
						}else{
							$output .= "<p><a href='image.php?venID=".$item->id."'>Create image</a></p>";
						}
						
						?>
						info: "<div id='checkinContent'><h1><?php echo $item->venue->name ?></h1><h2><?php echo $createdDataFormatted->format('Y-m-d H:i') ?> @ <?php echo $item->venue->name ?></h2><?php echo $output; ?></div>",
						<?php
					}
					?>
					
					title: "<?php echo $item->venue->name; ?>",
				});
				google.maps.event.addListener(marker, 'click', function(){
					infowindow.setContent(this.info);
					infowindow.open(map, this);
				});
				//checkin.push(marker[counter]);
				bounds.extend(latLng);
			<?php
			}
			?>
			map.fitBounds(bounds);
			}
			initialize();

		});
		</script>
		
	</head>
	<body>
		<div id="map">
		</div>
	</body>
</html>



