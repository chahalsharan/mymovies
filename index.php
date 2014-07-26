<?php require_once 'database.php';?>
<?php require_once 'globals.php' ?>
<?php session_start(); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
  <script type="text/javascript" src="js/lib/jquery-1.11.1.js"></script>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
  <link rel="stylesheet" href="http://getbootstrap.com/examples/carousel/carousel.css">
  <!-- Latest compiled and minified JavaScript -->
  <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.js"></script>
  <script src="http://rawgit.com/botmonster/jquery-bootpag/master/lib/jquery.bootpag.min.js"></script>
  <!-- Select2 -->
  <link href="http://ivaynberg.github.io/select2/select2-3.5.0/select2.css?ts=2014-06-26T15%3A33%3A45-07%3A00" rel="stylesheet">
  <script src="http://ivaynberg.github.io/select2/select2-3.5.0/select2.js?ts=2014-06-26T15%3A33%3A45-07%3A00"></script>
  <!--<script type="text/javascript" src="bootstrap-rating.js"></script>-->
  <link href="css/star-rating.css" media="all" rel="stylesheet" type="text/css"/>
  <link href="css/letMeWatchThis.css" rel="stylesheet" type="text/css"/>
  <link rel="icon" href="favicon.ico">
  <script src="js/lib/star-rating.js" type="text/javascript"></script>
  <script src="js/require.js" type="text/javascript"></script>
  
  <title>MyMovies</title>
 </HEAD>
<body>
    <?php
        #https://github.com/joshcam/PHP-MySQLi-Database-Class#ordering-method
        #http://getbootstrap.com/javascript/#buttons
    ?>
    <div id="topNavContainer">
	   <?php include "topNav.php" ?>
    </div>  
    <div class="container">
        <div id="moviesContainerMain">
	       <?php include "movies.php" ?>
        </div>
        <div class="row destacados" id="moviesPagination"></div>
        <?php include "movieModal.php" ?>
    </div>
</body>
</html>
<script type="text/javascript" src="js/app/letMeWatchThis.js"></script>