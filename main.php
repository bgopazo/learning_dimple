<?php 	
error_reporting(E_ALL); 
require_once('proj4connectpath.php');
require_once('utils.php');
require_once('proj4-constants.php');
session_start();
		//prepared statements were very awkward using 'bind_result' because I could not 
		//fnd a way to fetch the columns as a row due to php version (5.2)
		//get_result() to be used with fetch_assoc() was not introduced until php 5.3
		$email = safeLookup($_SESSION,"email","");
		$tasksQuery = "SELECT title, start, due, hours, hrsperday FROM tasks WHERE email='" . $email . "'";	
		//make database connection
		$connect = $path;
		$tQuery = mysqli_query($connect, $tasksQuery);
		$taskArray = array();
		//put query results into an array
		while($row = mysqli_fetch_assoc($tQuery)) {
			$taskArray[] = $row;
		}
		//close database connection
		mysqli_close($connect);	
		?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="main.css" type="text/css"> 
  <script src="https://d3js.org/d3.v4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dimple/2.3.0/dimple.latest.min.js"> </script>
</head>
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
          <h1 class="display-1 text-center">Overview</h1>
        </div>
      </div>
    </div>
  </div>
  <div align="center" id="graph">
  	 <script  type="text/javascript"> 
		var tasks = <?php echo json_encode($taskArray); ?>;
		var coordinates = [];
		//check to see if input is not started yet or not done yet
		function outsideDateRange(date) {
			var today = new Date();
			var result = false;
			if (date <= today)
				result = true;
			
			return result;
		}
		//create point sub-arrays to add to coordinates array
		function pushPoint(task, startDate, dueDate, x, y) {
			var startYet = outsideDateRange(startDate);
			var doneYet = outsideDateRange(dueDate);
			var today = new Date();

			point = {};
			point["title"] = task;
			if(!doneYet) {
				if(startYet && x <= today) {
					point["date"] = today.getFullYear() + "-" + ("0" + (today.getMonth()+1)).slice(-2) + "-" + ("0" + today.getDate()).slice(-2);
					point["hours"] = y;
				}
				else {
					point["date"] = x.getFullYear() + "-" + ("0" + (x.getMonth()+1)).slice(-2) + "-" + ("0" + x.getDate()).slice(-2);
					point["hours"] = y;
				}
				point["due"] = dueDate.getFullYear() + "-" + ("0" + (dueDate.getMonth()+1)).slice(-2) + "-" + ("0" + dueDate.getDate()).slice(-2);
				coordinates.push(point);
			}
		}
		//create array with calculated values; create coordinate array
		for (var i=0; i<tasks.length; i++) {
			//perform date calculations and set variables
			var dueDate = new Date(tasks[i].due);
			var startDate = new Date(tasks[i].start);
			var timePerDay = tasks[i].hrsperday;
			var title = tasks[i].title;
			var today = new Date();
			var plus = startDate;
			//set plus to today plus 1 if startDate is in the past
			while (plus < today) {
				plus = d3.timeDay.offset(plus,1);
			}
			//send x,y combinations for range testing and addition to coordinates array
			//pushPoint(title, startDate, dueDate, startDate, timePerDay);  //x = startDate
			//add x,y values for intervals from current date or startDate until dueDate or 30 days is reached
			while ((plus >= today) && (plus <= dueDate) && (plus <= d3.timeDay.offset(today,30))) {
				pushPoint(title, startDate, dueDate, plus, timePerDay);
				plus = d3.timeDay.offset(plus,1);	
			}
		}

		//console.log(JSON.stringify(coordinates));

	if(coordinates.length != 0) {
		//STACKED BAR CHART	
		var svg = dimple.newSvg("#graph", 650, 450);	
		var myChart = new dimple.chart(svg, coordinates);
		myChart.setBounds(65, 50, 505, 310)
		var x = myChart.addCategoryAxis("x", "date");
		//x.tickFormat = "%m-%d-%y";
		x.addOrderRule("date");
		var y = myChart.addMeasureAxis("y", "hours");
		y.tickFormat = ",.2f";
		var s = myChart.addSeries(["due","title"], dimple.plot.bar);
		s.interpolation = "cardinal";
		myChart.addLegend(60, 10, 600, 100, "right");
		myChart.draw();
	}	  	
	</script>
  </div>
  <div class="py-4">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1 class="text-center display-4">Create Tasks</h1>
          <br/>
          <form action="main_handler.php" method ="post" class="text-center"> <b>Task Title: </b>
            <input type="text" name="taskTitle" id ="taskTitle" pattern='[a-z|A-z|0-9|\s]{1,25}' title="No special character allowed."class=""  minlength='<?php echo $minLength;?>' maxlength='<?php echo $maxLengths['taskTitle'];?>' required ='required'>
            <br/>
			<br/> <b>Start Date: </b>
            <input type="date" class="" name="startDate" id="startDate" required ='required'>
            <br/>
            <br/> <b>Due Date: </b>
            <input type="date" class="" name="dueDate" id="dueDate"required ='required'>
            <br/>
            <br> <b>Total Hours: </b>
            <input type="number" step="any" name="totalHours"  id="totalHours" max='<?php echo $maxTotal;?>'  min='<?php echo $minTotal;?>' required ='required'>
            <br/>
			<br/>
            <input type="submit" id ="submitBtn" value="Submit" class="text-center"> </form>
			<br/>
			<br/>
		</div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
</body>

</html>
