<?php 

	error_reporting(E_ALL);
	
	require_once('proj4connectpath.php');

		//NEEDS TO be sanitized!!!
		$email=$_POST['email'];
		$password=$_POST['password'];
		$message="";
		
		if (isset($_POST['login'])) {
			//NEED TO make prepared statement!!!
			$userQuery = "SELECT * FROM users WHERE email='" . $email . "' and password = '" . $password . "'";
			
			//make database connection, if valid user/email combination, pull row, close connection
			$connect = $path;
			echo "Connection ", ($connect ? "" : "NOT "), "established.<br />\n";
			$uQuery = mysqli_query($connect, $userQuery);
			$user = mysqli_fetch_assoc($uQuery);
			mysqli_close($connect);
			
			if(is_array($user)) {
				session_start();
				$_SESSION['email'] = $email;
				var_dump($_SESSION);
				header('Location: main.php');
				exit();
			}
			else {
				$message = "Please try again.";
				echo $message;
			   header('Location: index.php');
			   exit();
			}
		}


	
?>