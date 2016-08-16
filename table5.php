<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function readFileInput($filename){
	$myfile = fopen($filename, "r");
	$read = fread($myfile,filesize($filename));
	fclose($myfile);
	return $read;
}
sleep(25);
$config = json_decode(readFileInput('sql.cfg'),true);
$username = $config[0];
$password = $config[1];
/*
Parse the challenge.csv to create tables for the appropriate majors
Also include a list of all possible majors for users to select
*/
$file = new SplFileObject('/home/mindsumo/CollegeCareer/sql/challenge.csv'); //get the file
$file->seek(0); //go to first line
$cols = explode("|",$file->current()); //read first line
try {
	$conn = new PDO("mysql:host=localhost;dbname=MindSumo;charset=utf8mb4", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(PDOException $e){}

$supermax = 1750;
for ($x = 1401; $x < count($cols); $x++) {
	$col = $cols[$x];
	//skip first three cols
	if($x > 2){
		$col = str_replace("\"","",$col);
		$col = str_replace("'","",$col);
		$col = str_replace("`","",$col);
		echo "$col \n";
		//insert into Majors if haven't already done so
		$stmt = $conn->prepare("SELECT * FROM `Majors` WHERE `Major` = :major");
		$stmt->bindParam(':major', $col, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->rowCount();
		if($rows == 0){
			$stmt = $conn->prepare("INSERT INTO `Majors` VALUES (:major)");
			$stmt->bindParam(':major', $col, PDO::PARAM_STR);
			$stmt->execute();
		}

		$file->seek(0); //go to first data line
		$skip = false;
		while(!$file->eof()){
			if($skip == true){ //skip first line
				$data = explode("|",$file->fgets());
				$code = str_replace("\"","",$data[0]);
				if($code != null && trim($code) != ""){ //skip last line
					$occupation = str_replace("\"","",$data[1]);
					$name = str_replace("\"","",$data[2]);
					$value = str_replace("\"","",$data[$x]);
					//add that row
					$stmt = $conn->prepare("INSERT INTO `MajorValues` VALUES (:code,:occupation,:name,:value, :col)");
					$stmt->bindParam(':code', $code, PDO::PARAM_STR);
					$stmt->bindParam(':occupation', $occupation, PDO::PARAM_STR);
					$stmt->bindParam(':name', $name, PDO::PARAM_STR);
					$stmt->bindParam(':value', $value, PDO::PARAM_STR);
					$stmt->bindParam(':col', $col, PDO::PARAM_STR);
					$stmt->execute();
				}
			}
			if($skip == false){
				$file->fgets();
			}
			$skip = true;
		}
		
		
		
		//Add  Title totals to Title tables, create if needed
		$stmt = $conn->prepare("SELECT DISTINCT `Detailed SOC Occupation` FROM `MajorValues` WHERE `Sample Name` = 'All' AND `Major` = :col");
		$stmt->bindParam(':col', $col, PDO::PARAM_STR);
		$stmt->execute();
		$dists = $stmt->rowCount();
		$dists = $dists * 10; //10 categories [All,Males,Females,etc...]
		$titles = $stmt->fetchAll();
		
		//total that major in a row named TOTAL for Naive Bayes Algorithm
		$stmt = $conn->prepare("SELECT `VALUE` FROM `MajorValues` WHERE `Major` = :col");
		$stmt->bindParam(':col', $col, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
		$values = $result;
		$total = 0;
		foreach($values as $val){
			$total = $total + $val["VALUE"];
		}
		$code = "TOTAL";
		$occupation =  "TOTAL";
		$name =  "TOTAL";
		$value = $total;
		$stmt = $conn->prepare("INSERT INTO `MajorValues` VALUES (:code,:occupation,:name,:value,:col)");
		$stmt->bindParam(':code', $code, PDO::PARAM_STR);
		$stmt->bindParam(':occupation', $occupation, PDO::PARAM_STR);
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->bindParam(':value', $value, PDO::PARAM_STR);
		$stmt->bindParam(':col', $col, PDO::PARAM_STR);
		$stmt->execute();
		
		//Smoothing with pseudocount one
		$stmt = $conn->prepare("SELECT * FROM `MajorValues` WHERE `Major` = :col");
		$stmt->bindParam(':col', $col, PDO::PARAM_STR);
		$stmt->execute();
		$smoothing = $stmt->fetchAll();
		foreach($smoothing as $smooth){
			//get esitmator based on Laplace add one smoothing and update row
			$v = (double)$smooth["VALUE"];
			$v = ($v + 1)/($dists); //calculator esitmator assuming 1 trial
			$occ = $smooth["Detailed SOC Occupation"];
			$n = $smooth["Sample Name"];
			$stmt = $conn->prepare("UPDATE `MajorValues` SET `VALUE` = :v WHERE `Detailed SOC Occupation` = :occ AND `Sample Name` = :n AND `Major` = :col");
			$stmt->bindParam(':col', $col, PDO::PARAM_STR);
			$stmt->bindParam(':occ', $occ, PDO::PARAM_STR);
			$stmt->bindParam(':n', $n, PDO::PARAM_STR);
			$stmt->bindParam(':v', $v, PDO::PARAM_STR);
			$stmt->execute();
		}

		
		//add to titles after smoothed
		foreach($titles as $title){
			$title = $title[0];
			if($title != ""  && $title != null && $title != " "){
				//create if it doesn't exist
				$stmt = $conn->prepare("CREATE TABLE IF NOT EXISTS `TotalTitles` (`Title` VARCHAR(250), `Total` DOUBLE)");
				$stmt->execute();
				//get the total from ALL from that particular major for that title
				$stmt = $conn->prepare("SELECT `VALUE` FROM `MajorValues` WHERE `Sample Name` = 'All' AND `Detailed SOC Occupation` = '$title' AND `Major` = :col");
				$stmt->bindParam(':col', $col, PDO::PARAM_STR);
				$stmt->execute();
				$result = $stmt->fetchAll();
				$titleVal = $result[0];
				$titleVal = $titleVal[0];
				//update if exists, or insert it
				$stmt = $conn->prepare("SELECT * FROM `TotalTitles` WHERE `Title` = :title");
				$stmt->bindParam(':title', $title, PDO::PARAM_STR);
				$stmt->execute();
				$rows = $stmt->rowCount();
				if($rows == 0){
					$stmt = $conn->prepare("INSERT INTO `TotalTitles` VALUES (:title,:titleVal)");
					$stmt->bindParam(':title', $title, PDO::PARAM_STR);
					$stmt->bindParam(':titleVal', $titleVal, PDO::PARAM_STR);
					$stmt->execute();
				}else{
					$stmt = $conn->prepare("UPDATE `TotalTitles` SET `Total` = `Total` + :titleVal WHERE `Title` = :title");
					$stmt->bindParam(':titleVal', $titleVal, PDO::PARAM_STR);
					$stmt->bindParam(':title', $title, PDO::PARAM_STR);
					$stmt->execute();
				}
			}
		}
	}
	if($x == $supermax){
		break; //finished this process each does 160 majors or to the end of 1536
	}
}






?>