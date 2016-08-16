<?php 
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
	$stmt = $conn->prepare("SELECT DISTINCT `title` FROM `occupation_data` ORDER BY `title` ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
	$msg = "";
	foreach($result as $major){
		$msg .= "<option value='$major[0]'/>";
	}
	echo $msg;
?> 