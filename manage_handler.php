<?php
//redirect to overview page 
header("Location: https://php.radford.edu/~proj4/manage.php");
		require_once('proj4connectpath.php');
        require_once('utils.php');
        session_start();	
		
	
	// look up email session 
	$email = $_SESSION['email'];
	//prepared statement insert query
	$connect = $path;
	$updateQuery ="UPDATE tasks SET title=?, start=?, due=?, hours=?, hrsperday=? WHERE email=? AND title=? AND due=?" ;
	
	if (get_magic_quotes_gpc()) { $_POST = stripslashes_deep($_POST); }
	
	if(array_key_exists("oldTitle", $_POST)){
		
		$newTitle = end($_POST['oldTitle']);
		$oldTitle = array_search($newTitle, $_POST['oldTitle']);
		$newStart = end($_POST['oldStart']);
		$oldStart = array_search($newStart, $_POST['oldStart']);
		$newDue = end($_POST['oldDue']);
		$oldDue = array_search($newDue, $_POST['oldDue']);
		$newHours = end($_POST['oldHours']);
		$oldHours = array_search($newHours, $_POST['oldHours']);
		
		//calculate new hours per day
		$datetime1 = new DateTime($newStart);
		$datetime2 = new DateTime($newDue);
		$interval = round(($datetime2->format('U') - $datetime1->format('U')) / (60*60*24));
		$newHPD = $newHours/$interval;
		$stmt = mysqli_stmt_init($connect);
	 
		if(mysqli_stmt_prepare($stmt, $updateQuery)){	
		mysqli_stmt_bind_param($stmt, 'ssssssss', $newTitle, $newStart, $newDue, $newHours, $newHPD, $email, $oldTitle, $oldDue);
		mysqli_stmt_execute($stmt);		
		}
	}



//If someone has clicked the delete task button, this array will be populated
//and the delete statement will be executed before the page loads all the tasks back up 
if (array_key_exists('delTitle', $_GET)){
	$deleteTitle = $_GET['delTitle'];
	$deleteDue = $_GET['delDue'];
	$dQuery = mysqli_stmt_init($connect);
	mysqli_stmt_prepare($dQuery, "DELETE FROM tasks WHERE email=? AND title=? AND due=?"); 
	mysqli_stmt_bind_param($dQuery, 'sss', $email, $deleteTitle, $deleteDue);
	mysqli_stmt_execute($dQuery);
	
}
//close database
 mysqli_close ($connect); 	
?>