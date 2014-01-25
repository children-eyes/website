<?php

	$childcase = new Child_Case($post);

	$childrens_url = Util::url('children');

	$textcontent = "<a href='".$childrens_url."'>Vermisst seit: " . $childcase->missed_days . " Tagen<br>";
	$textcontent.= "Steuerzahlerkosten: " . $childcase->costs_taxpayer . "€ <br></a>";
	$textcontent.= "Anwaltskosten: " . $childcase->costs_euro . "€ <br><br></a>";
	$textcontent.= "<a class='button' href='".$childrens_url."'>Übersicht der Fälle</a>";


	// Slide output
	echo "<div class='da-slide'>"."\n";
	echo "<h2><a href='".$childrens_url."'>".$childcase->post_title."</a></h2>"."\n";
	echo "<p>".$textcontent."</p>"."\n";
	echo "<div class='da-img'><a href='".$childrens_url."'><img src='".$childcase->image."' alt='Kinderaugen' /><img class='mask' src='".site_url()."/wp-content/themes/childreneyes/images/mask.png' alt='maske'/></a></div>"."\n";
	echo "</div>";
