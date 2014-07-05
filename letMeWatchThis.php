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
  <style>
    .top-buffer { margin-top:20px; }
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
	<div class="container marketing">
    <div class="row">
      <div class="col-md-5">
        <h2>Search movies</h2>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5">
        <?php include 'movieYears.php' ?>
      </div>
      <div class="col-md-5">
        <?php include 'movieCategories.php' ?>
      </div>
    </div>
    <div class="row top-buffer">
      <div class="col-md-5">
        <a href="#!" id="filterMovies" class="btn btn-success">Filter</a>
      </div>
    </div>
  </div>
</body>
</html>
<script>
  (function($){
    $("#filterMovies").on("click", function(){
      var years = $("#movieYearsFilter").val();
      var categories = $("#movieCategoriesFilter").val();
      if(years || categories){
        $.ajax({
          type: "GET",
          url: "movies.php?filter=yes" + getIfNotNull("&years", years) + getIfNotNull("&categories", categories)
        })
        .done(function( data ) {
          $("#moviesContainer").html(data);
        })
        .fail(function(msg){
          console.error("failed " + msg); 
        });
      }
    });
    var getIfNotNull = function (label, toGet){
      if(toGet){
        return label + "=" + toGet;
      }else{
        return "";
      }
    }
  })(jQuery);
</script>