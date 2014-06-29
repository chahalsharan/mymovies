<?php require_once 'mysqlidb.php';?>
<?php	

	$year = date("Y");
	$categories = [];

	if(isset($_GET['year'])){
		$year = $_GET['year'];
	}
	if(isset($_GET['categories'])){
		$categories = $_GET['categories'];
	}
	// start database connection if one doesnt already exist
	if(!isset($db)){
		$db = new Mysqlidb('localhost', 'root', '', 'let_me_watch_this');
	}
	
	//$q = "SELECT m.*, l.* FROM movies m LEFT OUTER JOIN links l ON(m.id = l.movie_id) WHERE m.year = " . $year . " ORDER BY name ASC";
	$db->where('year', 9);
	$movies = $db->get('movies');
	$numRows = $db->count;
?>
<!-- Carousel
================================================== -->
<div id="movieCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
  <!-- Indicators -->
  <ol class="carousel-indicators">
<?php
	$indicators = 0;
	$first = "active";
	while ($indicators < $numRows){
?>
		<li data-target="#movieCarousel" data-slide-to="<?php echo $indicators ?>" class="<?php echo $first ?>"></li>
<?php
		$first = "";
		$indicators++;
	}
?>
  </ol>
  <div class="carousel-inner">
<?php
	// if we have a result loop over the result
	$first = "active";
	foreach($movies as $movie){
		$image = urldecode($movie['thumbnail']);
		$name = urldecode($movie['name']);
		$year = urldecode($movie['year']);
		$link = urldecode($movie['link']);
	?>
	<div class="item <?php echo $first ?>">
	  <div class="container">
		<div class="carousel-caption">
		 <img src="<?php echo $image ?>" alt="<?php echo $name  ?>">
			<h1><?php echo $name ?></h1>
			<p><?php echo $year ?></p>
			<p><a class="btn btn-lg btn-primary" target="blank" href="<?php echo $link ?>" role="button">Watch</a></p>
		</div>
	  </div>
	</div>
<?php
		$first = "";
	}
?>
	</div>
	<a class="left carousel-control" href="#movieCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
	<a class="right carousel-control" href="#movieCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
</div><!-- /.carousel -->