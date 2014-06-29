<?php require_once 'database.php';?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> New Document </TITLE>
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
  <title>Let me watch these movies</title>
  <script type="text/javascript" src="json2.js"></script>
  <script type="text/javascript" src="jquery-1.11.1.js"></script>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
  <link rel="stylesheet" href="http://getbootstrap.com/examples/carousel/carousel.css">
  <!-- Latest compiled and minified JavaScript -->
  <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.js"></script>
  <script type="text/javascript" src="jquery.store.js"></script>
 </HEAD>
<body>

	<?php include "topNav.php" ?>
	<?php include "movies.php" ?>
	
	<div class="container marketing">
		<?php include 'movieYears.php' ?>
    </div>
  </body>
</html>
