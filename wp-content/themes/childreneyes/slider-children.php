<?php

	$childcase = new Child_Case($post);

	$textcontent = "<a href='/children'>Vermisst seit: <strong>" . $childcase->missed_days . " Tagen</strong><br>";
	$textcontent.= "Anwaltskosten: <strong>" . $childcase->costs_euro . "€ </strong><br><br></a>";
	$textcontent.= "<a class='button' href='/children'>Übersicht der Fälle</a>";

	// Slide output
	echo "<div class='da-slide'>"."\n";
	echo "<h2><a href='/children'>".$childcase->post_title."</a></h2>"."\n";
	echo "<p>".$textcontent."</p>"."\n";
	echo "<div class='da-img'><a href='/children'><img src='".$childcase->image."' alt='Kinderaugen' /><img class='mask' src='".site_url()."/wp-content/themes/childreneyes/images/mask.png' alt='maske'/></a></div>"."\n";
	echo "</div>";
