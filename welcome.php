<?php
	session_start();
	$key = $_REQUEST['code'];

	$client_id ="";
	$client_secret = "";
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
	foreach($obj->response->checkins->items as $item){
		$createdDataFormatted = DateTime::createFromFormat("U", $item->createdAt);
		//print_r($createdDataFormatted);
		$_SESSION['venues'][] = serialize($item);
		if($item->photos->count > 0){
			foreach ($item->photos->items as $photo) {
				$photoURL .= " <a href='image.php?venID=".$item->id."&photourl=".$photo->url."'>Use own photo</a>";
			}
			
		}else{
			$photoURL = "";
		}
		echo "<li>".$createdDataFormatted->format("Y-m-d H:i")." @ ".$item->venue->name." <a href='image.php?venID=".$item->id."'>Use this</a>".$photoURL."</li>";
		
	}
	echo "</ul>";
?>



