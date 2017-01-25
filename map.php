<?php
function curlmap($url){
	$motif = array("None", "Abandoned", "Aberrant", "Giant", "Undead", "Vermin", "Aquatic", "Desert", "Underdark", "Arcane", "Fire", "Cold", "Abyssal", "Infernal");
	$style = array("Standard", "Classic", "Crosshatch", "GraphPaper", "Parchment", "Marble", "Sandstone", "Slate", "Aquatic", "Infernal", "Glacial", "Asylum", "Steampunk", "Gamma");
	$grid = array("Square", "Hex", "VertHex");
	$layout = array("Square", "Rectangle", "Box", "Cross", "Dagger", "Saltire", "Keep", "Hexagon", "Round", "Cavernous");
	$size = array("Fine", "Diminiutive", "Tiny", "Small", "Medium", "Large", "Huge", "Gargantuan", "Colossal");
	$seed = rand(0, 9999999999);
	$motifn = rand(0,13);
	$stylen = rand(0, 13);
	$gridn = rand(0,2);
	$layoutn = rand(0, 9);
	$sizen = rand(0, 8);
	$param = [
		"name" => "The Forsaken Warrens of Death",
		"level" => "1",
		"infest" => "Basic",
		"motif" => $motif[$motifn],
		"seed" => $seed,
		"map_style" => "Parchment",
		"grid" => "Square",
		"dungeon_layout" => "Cavernous",
		"dungeon_size" => "Diminiutive",
		"map_cols" => "51",
		"map_rows" => "65", 
		"peripheral_egress" => false,
		"construct" => "Construct",
	];
	$params = http_build_query($param);	

	$fields_string = $params;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, count($fields));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	$data = curl_exec($ch);
	$data = str_replace(array("\t","\r","\n"), "", $data);
	return $data;
};

function savemap($img){
	$split_image = pathinfo($img);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL , $img);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$response= curl_exec ($ch);
	curl_close($ch);
	$file_name = "maps/".$split_image['filename'].".".$split_image['extension'];
	$file = fopen($file_name , 'w') or die("Big nope, no img for you.");
	fwrite($file, $response);
	fclose($file);
	return $split_image;
}
?>