<?php
//redirect to overview page 
header("Location: https://php.radford.edu/~proj4/main.php");
		require_once('proj4connectpath.php');
        require_once('utils.php');
        session_start();	
	// look up email session 
	$email = safeLookup($_SESSION,"email","");

	//prepared statement insert query 
	$insertQuery ="INSERT INTO tasks (email, title, start, due, hours, hrsperday) VALUES (?,?,?,?,?,?)" ;
	
	if (get_magic_quotes_gpc()) { $_POST = stripslashes_deep($_POST); }

	//calculate hours per day - forced to use weird workaround due to php version 5.2 not supporting date_diff()
	//workaround courtesy of https://stackoverflow.com/questions/4033224/what-can-use-for-datetimediff-for-php-5-2
	$start = $_POST["startDate"];
	$due = $_POST["dueDate"];
	$datetime1 = new DateTime($start);
	$datetime2 = new DateTime($due);
	$interval = round(($datetime2->format('U') - $datetime1->format('U')) / (60*60*24));
	$hrsperday = $_POST["totalHours"]/$interval;

	//make database connection
	 $connect = $path;
	 $stmt = mysqli_stmt_init($connect);
	 if(mysqli_stmt_prepare($stmt, $insertQuery)){	
		mysqli_stmt_bind_param($stmt, 'ssssss', $email, $_POST["taskTitle"], $_POST["startDate"], $_POST["dueDate"], $_POST["totalHours"], $hrsperday);
		mysqli_stmt_execute($stmt);		
	}
//close database
 mysqli_close ($connect); 	
?>