<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
function readFileInput($filename){
	$myfile = fopen($filename, "r");
	$read = fread($myfile,filesize($filename));
	fclose($myfile);
	return $read;
}
	$config = json_decode(readFileInput('sql.cfg'),true);
	$username = $config[0];
	$password = $config[1];
	try {
		$conn = new PDO("mysql:host=localhost;dbname=MindSumo;charset=utf8mb4", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){}
	
	$career = $_GET["career"];
	//$career = "Accountants and Auditors";
	$stmt = $conn->prepare("SELECT * FROM `occupation_data` WHERE `title` = :career"); //only relevant
	$stmt->bindParam(':career', $career, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$id = "";
	foreach($result as $title){
		$id = $title["onetsoc_code"];
	}
	$data = "";
	$overview = "<h3>Brief Overview Description</h3><table id='tfhover1' class='tftable' border='1'>
	<tr><th>Title</th><th>Overview</th></tr>";
	$stmt = $conn->prepare("SELECT * FROM `occupation_data`  WHERE `onetsoc_code` = :id"); //only relevant
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows != 0){
		foreach($result as $a){
			$val = $a['description'];
			$overview .= "<tr><td>$career</td><td>$val</td></tr>";
		}
	}else{
		$overview .= "<tr><td>None Available</td><td>None Available</td></tr>";
	}
	
	$data .= $overview;
	
	$knowledge = "<h3>Top Areas of Knowledge for this Career</h3><table id='tfhover2' class='tftable' border='1'>
	<tr><th>Element</th><th>Importance</th></tr>";
	
	$stmt = $conn->prepare("SELECT * FROM `knowledge` WHERE (`not_relevant` IS NULL OR `not_relevant` = 'N') AND `onetsoc_code` = :id AND `scale_id` = 'IM' ORDER BY `data_value` DESC LIMIT 25"); //only relevant
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
			$val = $a['data_value'];
			$knowledge .= "<tr><td>$r[1]</td><td>$val</td></tr>";
		}
	}else{
		$knowledge .= "<tr><td>None Available</td><td>None Available</td></tr>";
	}
	$knowledge .= "</table>";
	$data .= "|" . $knowledge;
	
	$skills = "<h3>Top Skills for this Career</h3><table id='tfhover3' class='tftable' border='1'>
	<tr><th>Element</th><th>Importance</th></tr>";
	
	$stmt = $conn->prepare("SELECT * FROM `skills` WHERE (`not_relevant` IS NULL OR `not_relevant` = 'N') AND `onetsoc_code` = :id AND `scale_id` = 'IM' AND `recommend_suppress` = 'N' ORDER BY `data_value` DESC LIMIT 25"); //only relevant
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
			$val = $a['data_value'];
			$skills .= "<tr><td>$r[1]</td><td>$val</td></tr>";
		}
	}else{
		$skills .= "<tr><td>None Available</td><td>None Available</td></tr>";
	}
	$skills .= "</table>";
	$data .= "|" . $skills;
	
	$abilities = "<h3>Top Abilities for this Career</h3><table id='tfhover4' class='tftable' border='1'>
	<tr><th>Element</th><th>Importance</th></tr>";
	
	$stmt = $conn->prepare("SELECT * FROM `abilities` WHERE (`not_relevant` IS NULL OR `not_relevant` = 'N') AND `onetsoc_code` = :id AND `scale_id` = 'IM' AND (`recommend_suppress` = 'N' OR `recommend_suppress` IS NULL) ORDER BY `data_value` DESC LIMIT 25"); //only relevant
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
			$val = $a['data_value'];
			$abilities .= "<tr><td>$r[1]</td><td>$val</td></tr>";
		}
	}else{
		$abilities .= "<tr><td>None Available</td><td>None Available</td></tr>";
	}
	$abilities .= "</table>";
	$data .= "|" . $abilities;
	
	$green = "<h3>Is this Occupation Green?</h3><table id='tfhover5' class='tftable' border='1'>
	<tr><th>Task</th><th>Type</th></tr>";
	
	$stmt = $conn->prepare("SELECT * FROM `green_occupations` WHERE `onetsoc_code` = :id "); 
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows != 0){
		foreach($result as $a){
			$stmt = $conn->prepare("SELECT * FROM `green_task_statements` WHERE `onetsoc_code` = :id");
			$stmt->bindParam(':id', $id, PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll();
			foreach($r as $b){
				$stmt = $conn->prepare("SELECT * FROM `task_statements` WHERE `task_id` = :id");
				$stmt->bindParam(':id', $b["task_id"], PDO::PARAM_STR);
				$stmt->execute();
				$re = $stmt->fetchAll()[0];
				$val = $re['task'];
				$val2 = $b["green_task_type"];
				$green .= "<tr><td>$val</td><td>$val2</td></tr>";
			}
		}
	}else{
		$green .= "<tr><td>Not a Green Occupation</td><td>Not a Green Occupation</td></tr>";
	}
	$green .= "</table>";
	$data .= "|" . $green;
	
	$tech = "<h3>Technologies Employed in this Field.</h3><table id='tfhover6' class='tftable' border='1'>
	<tr><th>Technology</th><th>Example</th></tr>";
	
	$stmt = $conn->prepare("SELECT * FROM `tools_and_technology` WHERE `hot_technology` = 'Y' AND `onetsoc_code` = :id AND `t2_type` = 'Technology'");
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows != 0){
		foreach($result as $a){
			$stmt = $conn->prepare("SELECT * FROM `unspsc_reference` WHERE `commodity_code` = :id");
			$stmt->bindParam(':id', $a["commodity_code"], PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll()[0];
			$val = $a['t2_example'];
			$tech .= "<tr><td>$r[1]</td><td>$val</td></tr>";
		}
	}else{
		$tech .= "<tr><td>None Available</td><td>None Available</td></tr>";
	}
	$tech .= "</table>";
	$data .= "|" . $tech;
	
	$similar = "<h3>Career Similar Interests</h3><br><table id='tfhover7' class='tftable' border='1'>
	<tr><th>Careers</th></tr>";
	
	$stmt = $conn->prepare("SELECT * FROM `career_starters_matrix` WHERE `onetsoc_code` = :id ORDER BY `related_index` ASC");
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows != 0){
		foreach($result as $a){
			$stmt = $conn->prepare("SELECT * FROM `occupation_data` WHERE `onetsoc_code` = :id");
			$stmt->bindParam(':id', $a["related_onetsoc_code"], PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll()[0];
			$val = $r['title'];
			$similar .= "<tr><td>$val</td></tr>";
		}
	}else{
		$similar .= "<tr><td>None Available</td></tr>";
	}
	$similar .= "</table>";
	$similar .= "<br><h3>Career Transition Capabilities</h3><br><table id='tfhover8' class='tftable' border='1'>
	<tr><th>Careers</th></tr>";
	
	$stmt = $conn->prepare("SELECT * FROM `career_changers_matrix` WHERE `onetsoc_code` = :id ORDER BY `related_index` ASC");
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$rows = $stmt->rowCount();
	if($rows != 0){
		foreach($result as $a){
			$stmt = $conn->prepare("SELECT * FROM `occupation_data` WHERE `onetsoc_code` = :id");
			$stmt->bindParam(':id', $a["related_onetsoc_code"], PDO::PARAM_STR);
			$stmt->execute();
			$r = $stmt->fetchAll()[0];
			$val = $r['title'];
			$similar .= "<tr><td>$val</td></tr>";
		}
	}else{
		$similar .= "<tr><td>None Available</td></tr>";
	}
	$similar .= "</table>";
	$data .= "|" . $similar;
	
	echo $data;
?>