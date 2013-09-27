<?php
session_start();
class Image{
	
	private $photo, $map, $mayorIcon, $categoryIcon, $infoCanvas;
	private $text, $checkinText, $isMayor, $shout;
	private $draw, $pixel;
	private $outputImage;
	public function __construct(){
		$this->infoCanvas = new Imagick();
		$this->infoCanvas->newImage(960,200, "white", "jpg");
		$this->mayorIcon = new Imagick("./img/mayor.png");
		$this->mayorIcon->setImageFormat("jpg");
		$this->draw = new ImagickDraw();
		$this->pixel = new ImagickPixel( 'grey' );
		$this->draw->setFillColor("black");
		$this->draw->setFont('Bookman');
		$this->draw->setFontSize(15 );
		$this->draw->setGravity(Imagick::GRAVITY_NORTHWEST);
	}
	
	public function setPhoto($imageURL){
		$this->photo = new Imagick($imageURL);
		$this->photo->scaleImage(960, 0);
	}

	public function getPhoto(){
		return $this->photo;
	}
	
	public function setMapImage($mapURL){
		$this->map = new Imagick($mapURL);
		$this->map->borderImage("#f45", 3,3);
	}

	public function getMapImage(){
		return $this->map;
	}

	public function setCategoryIcon($categoryIconURL){
		$this->categoryIcon = new Imagick($categoryIconURL);
	}

	public function getCategoryIcon(){
		return $this->categoryIcon;
	}

	public function getMayorIcon(){
		
		return $this->mayorIcon;
	}

	public function getInfoCanvas(){		
		return $this->infoCanvas;
	}

	public function setText($text){
		$this->text = $text;
	}

	public function setCheckinText($checkinText){
		$this->checkinText = $checkinText;
	}

	public function addTextToImage(){		
		$this->getInfoCanvas()->annotateImage($this->draw, 230, 20, 0, $this->text);
		$this->getInfoCanvas()->annotateImage($this->draw, 230, 50, 0, $this->checkinText);
	}

	public function generateImage($isMayor, $shout){
		$this->isMayor = $isMayor;
		if($this->isMayor){
			$this->getInfoCanvas()->compositeImage($this->getMayorIcon(), Imagick::COMPOSITE_DEFAULT, 230, 110);
		}
		$this->shout = $shout;
		if(!is_null($this->shout)){
			$this->draw->SetFontSize(30);
			$this->draw->setFontStyle(Imagick::STYLE_ITALIC);
			$this->getInfoCanvas()->annotateImage($this->draw, 260, 140, 0, "\"".$this->shout."\"");
		}
		$this->getInfoCanvas()->compositeImage($this->getCategoryIcon(), Imagick::COMPOSITE_DEFAULT, 230, 80);
		$this->getInfoCanvas()->compositeImage($this->getMapImage(), Imagick::COMPOSITE_DEFAULT, 20, 20);
		$combined = new Imagick();
		$combined->addImage($this->getPhoto());

		$combined->addImage($this->getInfoCanvas());
		$combined->resetIterator();

		$this->outputImage = $combined->appendImages(TRUE);
	}

	public function showImage(){
		return $this->outputImage;
	}

	public function saveImage($venueID){
		$filename = $venueID;
		$session_id = session_id();
		if(!is_dir("./user/".$session_id)){
			mkdir("./user/".$session_id);
			
		}
		$this->outputImage->writeImage("./user/".$session_id."/".$venueID.".jpg");

	}

}
