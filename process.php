<?php
ini_set('memory_limit','1024M');
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
	$data = "";
	$config = json_decode(readFileInput('sql.cfg'),true);
	$username = $config[0];
	$password = $config[1];
	try {
		$conn = new PDO("mysql:host=localhost;dbname=MindSumo;charset=utf8mb4", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){}
	
	//var_dump($_GET);
	
	/*
	gender, age, 

	major, degree type, 

	and select interests. 

	Knowledge

	Skills

	Abilities

	Green
	*/
	
	// [Prob(Profession | Major) x Prob(Gender) x Prob(Degree) x Prob(Age)]/[Average Aggregate AverageDifference in Each Category]  ==> High prob divide by small number creates big classification curve linearly regressed, if denominator is 0, it is changed to .01 x categories
	
	$defGen = "Gen Y (1982-2000)";
	$defMajor = "Algebra and Number Theory\CIP CODE 27.0102"; //random default major
	
	$gen = "";
	if($_GET["date"] == null || $_GET["date"] == ""){
		$gen = $defGen;
	}else{
		$date = $_GET["date"];
		$date = explode("/",$date);
		$year = $date[2];
		if($year > 1981){
			$gen = $defGen;
		}else if($year > 1964){
			$gen = "Gen X (1965-1981)";
		}else{
			$gen = "Baby Boomer (1946-1964)";
		}
	}
	if($_GET["major"] == null || $_GET["major"] == ""){
		$major = $defMajor;
	}else{
		$major = $_GET["major"];
	}
	$degree = $_GET["degree"];
	$gender = $_GET["gender"] . "s";
	$oneId = array();
	//calculate interests
	$interests = array();
	$stmt = $conn->prepare("SELECT DISTINCT `element_id` FROM `interests` WHERE `scale_id` = 'OI' ORDER BY `element_id` ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
	foreach($result as $a){
		$stmt = $conn->prepare("SELECT * FROM `content_model_reference` WHERE `element_id` = :id");
		$stmt->bindParam(':id', $a["element_id"], PDO::PARAM_STR);
		$stmt->execute();
		$r = $stmt->fetchAll()[0];
		$val = $r[1];
		$interests[] = array($r[0],$val); //element_id, name
	}
	$intVals = array();
	foreach($interests as $interest){
		$intVals[] = array($interest[0],$_GET["$interest[1]"]); // element_id, value
	}
	$many = count($interests); //how many distinct interests
	$count = $many - 1;
	$num = 0;
	$diffInt = array();
	//calculate difference in each interest then after a cycle add it to the oneId array diffInt
	$stmt = $conn->prepare("SELECT * FROM `interests` WHERE `scale_id` = 'OI' ORDER BY `onetsoc_code` AND `element_id` ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
	$diff = 0;
	for ($x = 0; $x < count($result); $x++) {
		$a = $result[$x];
		$diff += abs($a["data_value"] - $intVals[$num][1]); //only distance
		$num++;
		if($num == $count){
			$num = 0;
			if($diff == 0){
				$diff = .01;
			}
			$diffInt[$a["onetsoc_code"]] = $diff / $many; //avg diff
			$oneId[] = $a["onetsoc_code"];
			$diff = 0;
		}
	} 
	$aggDiff = $diffInt;
	unset($diffInt);
	
	//calculate knowledge
	$interests = array();
	$stmt = $conn->prepare("SELECT DISTINCT `element_id` FROM `knowledge` WHERE `scale_id` = 'LV' ORDER BY `element_id` ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
	foreach($result as $a){
		$stmt = $conn->prepare("SELECT * FROM `content_model_reference` WHERE `element_id` = :id");
		$stmt->bindParam(':id', $a["element_id"], PDO::PARAM_STR);
		$stmt->execute();
		$r = $stmt->fetchAll()[0];
		$val = str_replace(" ","_",$r[1]);
		$interests[] = array($r[0],$val); //element_id, name
	}
	$intVals = array();
	foreach($interests as $interest){
		$intVals[] = array($interest[0],$_GET["$interest[1]"]); // element_id, value
	}
	$many = count($interests); //how many distinct interests
	$count = $many - 1;
	$num = 0;
	$diffKnow = array();
	//calculate difference in each knowledge then after a cycle add it to the oneId array diffInt
	$stmt = $conn->prepare("SELECT * FROM `knowledge` WHERE `scale_id` = 'LV' ORDER BY `onetsoc_code` AND `element_id` ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
	$diff = 0;
	for ($x = 0; $x < count($result); $x++) {
		$a = $result[$x];
		$diff += abs($a["data_value"] - $intVals[$num][1]); //only distance
		$num++;
		if($num == $count){
			$num = 0;
			if($diff == 0){
				$diff = .01;
			}
			$diffKnow[$a["onetsoc_code"]] = $diff / $many;
			$diff = 0;
		}
	} 
	//for careers without information we give them a 1.5 diff on average
	foreach($oneId as $one){
		$a = $diffKnow[$one];
		if($a == null || $a == ""){
			$a = 1.5;
		}
		
		$aggDiff["$one"] = ($a + $aggDiff["$one"])/2; //get average
	}
	unset($diffKnow);
	//calculate skills
	$interests = array();
	$stmt = $conn->prepare("SELECT DISTINCT `element_id` FROM `skills` WHERE `scale_id` = 'LV' ORDER BY `element_id` ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
	foreach($result as $a){
		$stmt = $conn->prepare("SELECT * FROM `content_model_reference` WHERE `element_id` = :id");
		$stmt->bindParam(':id', $a["element_id"], PDO::PARAM_STR);
		$stmt->execute();
		$r = $stmt->fetchAll()[0];
		$val = str_replace(" ","_",$r[1]);
		$interests[] = array($r[0],$val); //element_id, name
	}
	$intVals = array();
	foreach($interests as $interest){
		$intVals[] = array($interest[0],$_GET["$interest[1]"]); // element_id, value
	}
	$many = count($interests); //how many distinct interests
	$count = $many - 1;
	$num = 0;
	$diffSkill = array();
	//calculate difference in each skills then after a cycle add it to the oneId array diffInt
	$stmt = $conn->prepare("SELECT * FROM `skills` WHERE `scale_id` = 'LV' ORDER BY `onetsoc_code` AND `element_id` ASC");
	$stmt->bindParam(':id', $id, PDO::PARAM_STR);
	$stmt->execute();
	$result = $stmt->fetchAll();
	$diff = 0;
	for ($x = 0; $x < count($result); $x++) {
		$a = $result[$x];
		$diff += abs($a["data_value"] - $intVals[$num][1]); //only distance
		$num++;
		if($num == $count){
			$num = 0;
			if($diff == 0){
				$diff = .01;
			}
			$diffSkill[$a["onetsoc_code"]] = $diff / $many;
			$diff = 0;
		}
	} 
	foreach($oneId as $one){
		$a = $diffSkill[$one];
		if($a == null || $a == ""){
			$a = 1.5;
		}
		
		$aggDiff["$one"] = ($a + $aggDiff["$one"])/2; //get average
	}
	unset($diffSkill);
	//calculate abilities
	$interests = array();
	$stmt = $conn->prepare("SELECT DISTINCT `element_id` FROM `abilities` WHERE `scale_id` = 'LV' ORDER BY `element_id` ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
	foreach($result as $a){
		$stmt = $conn->prepare("SELECT * FROM `content_model_reference` WHERE `element_id` = :id");
		$stmt->bindParam(':id', $a["element_id"], PDO::PARAM_STR);
		$stmt->execute();
		$r = $stmt->fetchAll()[0];
		$val = str_replace(" ","_",$r[1]);
		$interests[] = array($r[0],$val); //element_id, name
	}
	$intVals = array();
	foreach($interests as $interest){
		$intVals[] = array($interest[0],$_GET["$interest[1]"]); // element_id, value
	}
	$many = count($interests); //how many distinct interests
	$count = $many - 1;
	$num = 0;
	$diffAbility = array();
	//calculate difference in each abilities then after a cycle add it to the oneId array diffInt
	$stmt = $conn->prepare("SELECT * FROM `abilities` WHERE `scale_id` = 'LV' ORDER BY `onetsoc_code` AND `element_id` ASC");
	$stmt->execute();
	$result = $stmt->fetchAll();
	$diff = 0;
	for ($x = 0; $x < count($result); $x++) {
		$a = $result[$x];
		$diff += abs($a["data_value"] - $intVals[$num][1]); //only distance
		$num++;
		if($num == $count){
			$num = 0;
			if($diff == 0){
				$diff = .01;
			}
			$diffAbility[$a["onetsoc_code"]] = $diff / $many;
			$diff = 0;
		}
	} 
	foreach($oneId as $one){
		$a = $diffAbility[$one];
		if($a == null || $a == ""){
			$a = 1.5;
		}
		
		$aggDiff["$one"] = ($a + $aggDiff["$one"])/2; //get average
	}
	unset($diffAbility);
	
	
	//Bayes
	//Calculate Major P(Profession | Evidence) = P(Evidence | Profession) x P(Profession) mostly handled in table.php set up
	//Utilize P(c|x) = P(Xo|c) x P(X1|c) ... x P(c)
	$oneTotal = array();
	$total = 0; //total of all titles
	$stmt = $conn->prepare("SELECT * FROM `TotalTitles`"); //already ine one order
	$stmt->execute();
	$result = $stmt->fetchAll();
	foreach($result as $a){
		$stmt = $conn->prepare("SELECT * FROM `occupation_data` WHERE `title` = :id");
		$stmt->bindParam(':id', $a["Title"], PDO::PARAM_STR);
		$stmt->execute();
		$r = $stmt->fetchAll();
		foreach($r as $z){
			$oneTotal[$z["onetsoc_code"]] = $a["Total"];
			$total += $a["Total"];
			break;
		}
	}

	$multProb = array();
	foreach($oneId as $one){
		$subtotal = $oneTotal[$one]; //get title's total
		if(isset($oneTotal[$one])){
			//major
			$majorProb = (1/821) * 1000;
			$stmt = $conn->prepare("SELECT * FROM `MajorValues` WHERE `SOC Code` = '$one' AND `Major` = :major AND `Sample Name` = 'All'"); 
			$stmt->bindParam(':major', $major, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll();
			foreach($result as $a){
				$majorProb = ($a["VALUE"]/$subtotal) * 1000;
			}
			//generation
			$generation = (1/4) * 1000;
			$stmt = $conn->prepare("SELECT * FROM `MajorValues` WHERE `SOC Code` = '$one' AND `Major` = :major AND `Sample Name` = '$gen'"); 
			$stmt->bindParam(':major', $major, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll();
			foreach($result as $a){
				$generation = ($a["VALUE"]/$subtotal) * 1000;
			}
			//degree
			$degree = (1/1536) * 1000;
			$stmt = $conn->prepare("SELECT * FROM `MajorValues` WHERE `SOC Code` = '$one' AND `Major` = :major AND `Sample Name` = '$degree'"); 
			$stmt->bindParam(':major', $major, PDO::PARAM_STR);
			$stmt->execute();
			$result = $stmt->fetchAll();
			foreach($result as $a){
				$degree = ($a["VALUE"]/$subtotal) * 1000;
			}
			//gender
			$gender = (1/2) * 1000;
			$stmt = $conn->prepare("SELECT * FROM `MajorValues` WHERE `SOC Code` = '$one' AND `Major` = :major AND `Sample Name` = '$gender'");
			$stmt->bindParam(':major', $major, PDO::PARAM_STR);		
			$stmt->execute();
			$result = $stmt->fetchAll();
			foreach($result as $a){
				$gender = ($a["VALUE"]/$subtotal) * 1000;
			}
			$profession = ($subtotal/$total);
			$multProb[$one] = ($gender * $degree * $generation * $majorProb * $profession);
		}else{
			$multProb[$one] = 0;
		}
	}
	
	
	//Final calculation Prob / Aggregate
	//default aggDiff is 1.5
	$final = array();
	foreach($oneId as $one){
		$a = $aggDiff[$one];
		if($a == null || $a == ""){
			$a = 1.5;
		}
		$final["$one"] = $multProb[$one]/$a; //id => value
	}
	
	
	//sort associative descending
	arsort($final);
	$keys = array();
	$x = 0;
	foreach ($final as $key => $val) {
		$keys[] = $key;
		$x++;
		if($x > 4){
			break;
		}
	}
	
	//find key names and pass that array
	$names = array();
	foreach($keys as $key){
		$stmt = $conn->prepare("SELECT * FROM `occupation_data` WHERE `onetsoc_code` = :id"); 
		$stmt->bindParam(':id', $key, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetchAll();
		foreach($result as $a){
			$names[] = $a["title"];
		}
	}
	$location = "http://projects.miscthings.xyz/CollegeCareer/result.html?";
	$numbers = array("one=","two=","three=","four=","five=");
	for($x = 0; $x < 5; $x++) {
		if($x < 4){
			$location .= $numbers[$x] . $names[$x] . "&";
		}else{
			$location .= $numbers[$x] . $names[$x];
		}
	}
	
	//header("Location: $location ");
	
	echo $location;
	
	
?>