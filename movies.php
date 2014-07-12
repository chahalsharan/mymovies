<?php require_once 'mysqlidb.php';?>
<?php require_once 'databaseConfig.php' ?>
<?php	

	$featured = FALSE;
	$db = getMysqlConnection();
	$db->join("links l", "l.movie_id = m.id", "LEFT");
	$db->join("rotten_data r", "r.movie_id = m.id", "LEFT");
	 
	if (isset($_GET['years'])) {
		$years = explode(",", $_GET['years']);
		$db->where('m.year', $years, "IN");
	} else if(! isset($_GET['filter'])) {
		$db->where('m.year', date("Y"));
		$featured = TRUE;
		error_log("DEBUG: featured listings");
	} else {
		$featured = FALSE;
	}

	if (isset($_GET['categories'])){
		$categories = explode(",", $_GET['categories']);
		$db->join("movie_categories mc", "mc.movie_id = m.id");
		$db->where("mc.category_id", $categories, "IN");
	}

	if (isset($_GET['actors'])) {
		$actors = explode(",", $_GET['actors']);
		$db->join("movie_actors ma", "ma.movie_id = m.id");
		$db->where("ma.actor_id", $actors, "IN");
	}

	if (isset($_GET['notFeatured'])) {
		$featured = FALSE;
	} else {
		$featured = TRUE;
	}
	
	$movies = $db 	->groupBy('m.id, m.name, m.year, l.link, m.rating, m.thumbnail')
					->orderBy("m.year", "desc")
					->orderBy("m.name", "asc")
					->get('movies m', null, 
						'm.id, m.name, m.year, l.link as link, m.rating, m.synopsis,  m.thumbnail, r.synopsis as rsynopsis, r.critics_score, r.audience_score');
	$numRows = $db->count;

 	error_log("DEBUG: Query: " . $db->getLastQuery());
	closeMysqlConnection($db);
	
	function getRatingColor($rating){
		if($rating > 0 && $rating < 25){
			return "progress-bar-danger";
		}
		if($rating >=30 && $rating < 50){
			return "progress-bar-warning";
		}
		if($rating >=50 && $rating < 75){
			return "progress-bar-info";
		}
		if($rating >=75){
			return "progress-bar-success";
		}
	};
?>

<!--
<div id="movieCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
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
	$first = "active";
	foreach($movies as $movie){
		$image = urldecode($movie['thumbnail']);
		$name = urldecode($movie['name']);
		$year = urldecode($movie['year']);
		$link = urldecode($movie['link']);
		$rating = urldecode($movie['rating']);
		$id = $movie['id'];
		if($link == NULL && $featured){
			error_log("DEBUG: link not present, featured list: ");
			continue;
		}else{
			
	?>
	<div class="item <?php echo $first ?>">
	  <div class="container">
		<div class="carousel-caption">
		 <img src="<?php echo $image ?>" alt="<?php echo $name  ?>">
			<h1><?php echo $name ?></h1>
			<p><?php echo $year ?></p>
			<p>
				<a class="btn btn-lg btn-success movieDetails" href="#!" data-movie-id="<?php echo $id  ?>" data-movie-name="<?php echo $name  ?>" role="button">Details</a>
				<?php if (isset($link) && $link != NULL) { ?>
				<a class="btn btn-lg btn-primary" target="blank" href="<?php echo $link ?>" role="button">Watch</a>
				<?php } ?>
				<?php if (isset($rating) && $rating != NULL) { ?>
				<input class="rating" value="<?php echo floor($rating) ?>" data-max="5" data-min="1" id="some_id" name="your_awesome_parameter" type="number" />
				<?php } ?>
			</p>
		</div>
	  </div>
	</div>
<?php
		$first = "";
		}
	}
?>
	</div>
	<a class="left carousel-control" href="#movieCarousel" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
	<a class="right carousel-control" href="#movieCarousel" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
</div>
-->
<style>
.rtContainer {
	height: 50px;
    position: relative;
}
.rtLogo{
	padding-top: 20px;
	opacity: 0.5;
}
.rtLogo, 
.rtRating {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    vertical-align: middle;
}

.rtLogo {
    z-index: 10;
}
</style>
<div class="container top-buffer-large">
	<div class="row destacados">
<?php
	// if we have a result loop over the result
	$first = "active";
	$index = 0;
	foreach($movies as $movie){
		$image = urldecode($movie['thumbnail']);
		$name = urldecode($movie['name']);
		$year = urldecode($movie['year']);
		$link = urldecode($movie['link']);
		$rating = urldecode($movie['rating']);
		$criticsRating = $movie['critics_score'];
		$audienceRating = $movie['audience_score'];
		$synopsis = NULL;
		if(isset($movie['synopsis']) && $movie['synopsis'] != NULL){
			$synopsis = urldecode($movie['synopsis']);
		}
		if (isset($movie['rsynopsis']) && $movie['rsynopsis'] != null) {
			$synopsis = urldecode($movie['rsynopsis']);
		}
		
		$id = $movie['id'];
		if($link == NULL && $featured){
			error_log("DEBUG: link not present, featured list: ");
			continue;
		}else{
			$index ++;
	?>
			<div class="col-md-4">
	    		<div>
					<img src="<?php echo $image ?>" alt="<?php echo $name  ?>" class="img-circle img-thumbnail">
					<h5>
						<?php echo $name ?> 
						<?php if ($synopsis != NULL) { ?>
						<button type="button" class="btn btn-lg btn-danger popover" data-toggle="popover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?">Click to toggle popover</button>

						<a href="#!" class="tooltip"
							data-toggle="tooltip" data-placement="left" title="<?php echo $synopsis ?>">
							<span class="glyphicon glyphicon-comment"></span>
						</a>
						<?php } ?>
					</h5>
					<p>
						<a class="movieDetails" href="#!" data-movie-id="<?php echo $id  ?>" data-movie-name="<?php echo $name  ?>" role="button">Details</a>
						<?php if (isset($link) && $link != NULL) { ?>
						<a class="" target="blank" 
							href="<?php echo $link ?>" role="button">Watch</a>
						<?php } ?>
					</p>
					<p>
						<?php if (isset($rating) && $rating != NULL) { ?>
						<input class="rating" value="<?php echo floor($rating) ?>" data-max="5" data-min="1" id="some_id" name="your_awesome_parameter" type="number" />
						<?php } ?>
					</p>
					<p>
						<div class="rtContainer">
							<div class="rtLogo" >
						<?php if($audienceRating > 0 || $criticsRating > 0){ ?>
							<img src="http://images.rottentomatoescdn.com/images/trademark/rottentomatoes_logo_40.png" style="height:20px; width:70px"/>
						<?php } ?>
							</div>
							<div class="rtRating" >
						<?php if ($criticsRating > 0){ ?>
							<div class="progress">
							  <div class="progress-bar" role="progressbar" 
							  		aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
							  		style="width: <?php echo intval($criticsRating) ?>%;">
							  		<font style="color:offgolden">
							    <?php echo "C:" . $criticsRating ?>%
							    	</font>
							  </div>
							</div>
						<?php } ?>
						<?php if ($audienceRating > 0){ ?>
							<div class="progress">
							  <div class="progress-bar <?php echo getRatingColor($audienceRating) ?>" role="progressbar" 
							  		aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
							  		style="width: <?php echo intval($audienceRating) ?>%;">
							    <?php echo "A: " . $audienceRating ?>%
							  </div>
							</div>
						<?php } ?>
							</div>
						</div>
					</p>
					
				</div>
			</div>
	<?php if($index%3 == 0){ ?>
		</div>
		<div class="row destacados">
	<?php } ?>
		
<?php
		$first = "";
		}
	}
?>
	</div>
</div>

<script>
	(function($){
		//$(".tooltip").tooltip();
		$('#popover').popover();
		$(".movieDetails").on("click", function(){
			var movieName = $(this).attr("data-movie-name");
			var movieId = $(this).attr("data-movie-name");
			$("#myModalLabel").html(movieName);
			$('#myModal').modal();
		});
	})(jQuery);
</script>