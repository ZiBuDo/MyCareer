<?php 
function readFileInput($filename){
	$myfile = fopen($filename, "r");
	$read = fread($myfile,filesize($filename));
	fclose($myfile);
	return $read;
}
	$data = "";
	$config = json_decode(readFileInput('sql.cfg'),true);
	$username = $config[0];
	$password = $config[1];
	try {
		$conn = new PDO("mysql:host=localhost;dbname=MindSumo;charset=utf8mb4", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){}
	$stmt = $conn->prepare("SELECT DISTINCT `Major` FROM `Majors` ORDER BY `Major` ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
	$msg = "";
	foreach($result as $major){
		$msg .= "<option value='$major[0]'/>";
	}
	
	$data .= $msg;
	
	$interests = "<header class='major'><h3>Interests (1-7)</h3>";
	
	$stmt = $conn->prepare("SELECT DISTINCT `element_id` FROM `interests` WHERE `scale_id` = 'OI' ORDER BY `element_id` ASC");
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows != 0){
		foreach($result as $a){
			$stmt = $conn->prepare("SELECT * FROM `content_model_reference` WHERE `element_id` = :id");
			$stmt->bindParam(':id', $a["element_id"], PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll()[0];
			$val = $r[1] . "T";
			$desc = $r["description"];
			$interests .= "<h4 class='interests' inter='$r[1]'>$r[1]</h4><h6>$desc</h6><p><div id='$r[1]' style='width: 500px;margin-left:30%'></div><br><input value='1' id='$val' name='$r[1]' type='text' style='width:75px;'/></p>";
		}
	}else{
		$interests .= "<h5>None Available</h5>";
	}
	$interests .= "</header>";
	$data .= "|" . $interests;
	
	$knowledge = "<header class='major'><h3>Knowledge Level (0-7)</h3>";
	
	$stmt = $conn->prepare("SELECT DISTINCT `element_id` FROM `knowledge` ORDER BY `element_id` ASC");
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows != 0){
		foreach($result as $a){
			$stmt = $conn->prepare("SELECT * FROM `content_model_reference` WHERE `element_id` = :id");
			$stmt->bindParam(':id', $a["element_id"], PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll()[0];
			$id = str_replace(" ","_",$r[1]);
			$val = $id . "T";
			$desc = $r["description"];
			$knowledge .= "<h4 class='knowledge' know='$id'>$r[1]</h4><h6>$desc</h6><p><div id='$id' style='width: 500px;margin-left:30%'></div><br><input value='0' id='$val' name='$id' type='text' style='width:75px;'/></p>";
		}
	}else{
		$knowledge .= "<h5>None Available</h5>";
	}
	$knowledge .= "</header>";
	$data .= "|" . $knowledge;
	
	$skills = "<header class='major'><h3>Skill Level (0-7)</h3>";
	
	$stmt = $conn->prepare("SELECT DISTINCT `element_id` FROM `skills` ORDER BY `element_id` ASC");
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows != 0){
		foreach($result as $a){
			$stmt = $conn->prepare("SELECT * FROM `content_model_reference` WHERE `element_id` = :id");
			$stmt->bindParam(':id', $a["element_id"], PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll()[0];
			$id = str_replace(" ","_",$r[1]);
			$val = $id . "T";
			$desc = $r["description"];
			$skills .= "<h4 class='skills' skill='$id'>$r[1]</h4><h6>$desc</h6><p><div id='$id' style='width: 500px;margin-left:30%'></div><br><input value='0' id='$val' name='$id' type='text' style='width:75px;'/></p>";
		}
	}else{
		$skills .= "<h5>None Available</h5>";
	}
	$skills .= "</header>";
	$data .= "|" . $skills;
	
	$abilities = "<header class='major'><h3>Ability Level (0-7)</h3>";
	
	$stmt = $conn->prepare("SELECT DISTINCT `element_id` FROM `abilities` ORDER BY `element_id` ASC");
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows != 0){
		foreach($result as $a){
			$stmt = $conn->prepare("SELECT * FROM `content_model_reference` WHERE `element_id` = :id");
			$stmt->bindParam(':id', $a["element_id"], PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll()[0];
			$id = str_replace(" ","_",$r[1]);
			$val = $id . "T";
			$desc = $r["description"];
			$abilities .= "<h4 class='abilities' ability='$id'>$r[1]</h4><h6>$desc</h6><p><div id='$id' style='width: 500px;margin-left:30%'></div><br><input value='0' id='$val' name='$id' type='text' style='width:75px;'/></p>";
		}
	}else{
		$abilities .= "<h5>None Available</h5>";
	}
	$abilities .= "</header>";
	$data .= "|" . $abilities;
	echo $data;
?> 