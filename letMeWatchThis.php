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
  <!-- Select2 -->
  <link href="http://ivaynberg.github.io/select2/select2-3.5.0/select2.css?ts=2014-06-26T15%3A33%3A45-07%3A00" rel="stylesheet">
  <script src="http://ivaynberg.github.io/select2/select2-3.5.0/select2.js?ts=2014-06-26T15%3A33%3A45-07%3A00"></script>
  <script type="text/javascript" src="bootstrap-rating.js"></script>
  <style>
    .top-buffer { margin-top:20px; }
    .top-buffer-small { margin-top:5px; }
    .top-buffer-large { margin-top: 50px; }
    .destacados{
        padding: 20px 0;
        text-align: center;
    }
    .destacados > div > div{
      padding: 10px;
      border: 1px solid transparent;
      border-radius: 1px;
      transition: 0.2s;
    }
    .destacados > div:hover > div{
      margin-top: -10px;
      border: 1px solid rgb(200, 200, 200);
      box-shadow: rgba(0, 0, 0, 0.1) 0px 5px 5px 2px;
      background: rgba(200, 200, 200, 0.1);
      transition: 0.5s;
    }
  </style>
 </HEAD>
<body>
  <?php
    #https://github.com/joshcam/PHP-MySQLi-Database-Class#ordering-method
    #http://getbootstrap.com/javascript/#buttons
  ?>
	<?php include "topNav.php" ?>
  <div id="moviesContainer">
	 <?php include "movies.php" ?>
  </div>
</body>
</html>
