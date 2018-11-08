


<?php
//redirect to index
header("Location: https://php.radford.edu/~proj4/index_create.php");
		require_once('proj4connectpath.php');
        require_once('utils.php');
  
	//prepared statement insert query 
	$insertQuery ="INSERT INTO users (email,password) VALUES (?,?)" ;
	
	if (get_magic_quotes_gpc()) { $_POST = stripslashes_deep($_POST); }
	//make database connection
	 $connect = $path;
	 $stmt = mysqli_stmt_init($connect);
	 if(mysqli_stmt_prepare($stmt, $insertQuery)){	
	mysqli_stmt_bind_param($stmt, 'ss', $_POST["emailAdd"], $_POST["passwd"]);
	mysqli_stmt_execute($stmt);		
}
//close database
 mysqli_close ($connect); 	
?>
	




