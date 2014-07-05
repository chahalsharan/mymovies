<?php require_once 'mysqlidb.php';?>
<?php require_once 'databaseConfig.php' ?>
<?php	

	$db->join("links l", "l.movie_id = m.id", "LEFT");
	if(isset($_GET['years'])){
		# if years is set, show movies for those years
		$years = explode(",", $_GET['years']);
		$db->where('m.year', $years, "IN");
	}else if(! isset($_GET['filter'])){
		# else if not filtering ashow only latest year movies
		$db->where('m.year', date("Y"));
	}
	if(isset($_GET['categories'])){
		# if categories selected, show movies for those categories
		$categories = explode(",", $_GET['categories']);
		$db->join("categories c", "c.movieId = m.id");
		$db->where("c.category", $categories, "IN");
	}
	
	$movies = $db 	->groupBy('m.id, m.name, m.year, l.link, m.thumbnail')
					->orderBy("m.year", "desc")
					->orderBy("m.name", "asc")
					->get('movies m', null, 'm.id, m.name, m.year, l.link as link, m.thumbnail');
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
		$id = $movie['id'];
	?>
	<div class="item <?php echo $first ?>">
	  <div class="container">
		<div class="carousel-caption">
		 <img src="<?php echo $image ?>" alt="<?php echo $name  ?>">
			<h1><?php echo $name ?></h1>
			<p><?php echo $year ?></p>
			<p>
				<a class="btn btn-lg btn-success movieDetails" href="#!" data-movie-id="<?php echo $id  ?>" data-movie-name="<?php echo $name  ?>" role="button">Details</a>
				<a class="btn btn-lg btn-primary" target="blank" href="<?php echo $link ?>" role="button">Watch</a>
			</p>
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

<!-- Modal -->
<div id="myModal" class="modal fade <?php #bs-example-modal-lg ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <!--
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
  	  -->
    </div>
  </div>
</div>

<script>
	(function($){
		$(".movieDetails").on("click", function(){
			var movieName = $(this).attr("data-movie-name");
			var movieId = $(this).attr("data-movie-name");
			$("#myModalLabel").html(movieName);
			$('#myModal').modal();
		});
	})(jQuery);
</script>