<?php 	
error_reporting(E_ALL);
require_once('proj4connectpath.php');
require_once('proj4-constants.php');
session_start();


$email = $_SESSION['email'];

//make database connection
$connect = $path;
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

$tQuery = mysqli_stmt_init($connect);
mysqli_stmt_prepare($tQuery, "SELECT title, start, due, hours, hrsperday FROM tasks WHERE email=?"); 
mysqli_stmt_bind_param($tQuery, 's', $email);
mysqli_stmt_bind_result($tQuery, $title, $start, $due, $hours, $hrsperday);
mysqli_stmt_execute($tQuery);


?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="manage.css" type="text/css"> </head>
<body>
  <nav class="navbar navbar-expand-md bg-secondary navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="main.php">
        <img src="logo.PNG" width="30" height="30" class="d-inline-block align-top p-0" alt=""> </a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="main.php">Overview</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="manage.php">Manage Tasks</a>
          </li>
        </ul>
        <a class="btn navbar-btn ml-2 text-white btn-secondary" href="index.php"><i class="fa d-inline fa-lg fa-sign-out"></i> Log Out</a>
      </div>
    </div>
  </nav>
  <div class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1 class="display-1 text-center">Manage Tasks</h1>
        </div>
      </div>
    </div>
  </div>
  <div class = "py-4">
  	<div class="container">
  		<?php 
  		while(mysqli_stmt_fetch($tQuery)) {
			echo "<div class='row'>
					<div class='col-md-6 text-center'>
						<div align='left'>
						    <u><p class='lead'>$title:</p></u>
							</div>
							<div align='right'>
							<form action='manage_handler.php' method ='post' class='text-center'> <b>Task Title:  </b>
							<input type='text' align ='center'  pattern='[a-z|A-z|0-9|\s]{1,25}' title='No special character allowed.' width: 100%; name='oldTitle[$title]' class='' value='$title'>
							<br/><br/> <b>Start Date:  </b>
							<input type='date' align ='center'  size='20' name='oldStart[$start]' id='startDate' value='$start' required ='required'>
							<br/><br/> <b>Due Date:  </b>
							<input type='date' size='20' align ='center'  name='oldDue[$due]' id='dueDate' value='$due' required ='required'>
							<br/><br/> <b>Total Hours:  </b>
							<input type='number' step='any' align ='right' name='oldHours[$hours]' max='10000' min='1' value='$hours' required ='required'>
							<br/> 
							<br/>
						</div>
					  </div>
					<div class='col-md-6' align='left'>
						<div class='col-md-12'  align='left'>
						<div align='left'>
						<br />
						<br />
						<br />
						<br />
						<input type='submit' id ='submitBtn' value='Save Changes' class='btn btn-primary w-50 px-4 mx-5'> 
						<br/>
						<br/>
						<a class='btn btn-primary w-50 px-4 mx-5'align ='left'  href='manage_handler.php?delTitle=$title&delDue=$due'>Delete Task </a>
						<br />
						<br />
						  </form>
						</div>		
					  </div>
					</div>
				  </div>";
		}
		echo mysqli_error($connect);
		mysqli_close($connect);
  		?>
  	</div>
  </div>
  <div class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <a class="btn btn-primary btn-lg btn-block" href="main.php">Return to Overview</a>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>

</html>
