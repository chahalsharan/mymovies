<?php require_once 'mysqliDb.php';?>
<?php require_once 'databaseConfig.php' ?>
<?php require_once 'movieUtils.php' ?>
<?php	

	global $PAGE_SIZE;
	global $NUM_ROWS;
	$PAGE_SIZE = 12;

	$db = getMysqlConnection();

	$featured = FALSE;
	if (isset($_GET['notFeatured'])) {
		$featured = FALSE;
	} else {
		$featured = TRUE;
	}
	// if no filters, i.e. home page only show featured movies
	if(! isset($_GET['filter'])) {
        $featured = TRUE;
    }
    $currentPage = getFilterCriteriaFromRequest("currentPage", FALSE, 1);
    $numMovies = getFilterCriteriaFromRequest("numMovies", FALSE);

    if(!$numMovies){
    	$numMovies = getFilteredMovies($db,
			$featured,
			getFilterCriteriaFromRequest("years", TRUE),
			getFilterCriteriaFromRequest("categories", TRUE),
			getFilterCriteriaFromRequest("actors", TRUE),
			getFilterCriteriaFromRequest("searchText", FALSE)
		);
		$numMovies = count($numMovies);
        error_log("DEBUG: numMovies found :" . $numMovies);
    }
    $NUM_ROWS = $numMovies;

    $paginationLimits = getPaginationLimits($currentPage, $PAGE_SIZE);

	
	$movies = getFilteredMovies($db,
			$featured,
			getFilterCriteriaFromRequest("years", TRUE),
			getFilterCriteriaFromRequest("categories", TRUE),
			getFilterCriteriaFromRequest("actors", TRUE),
			getFilterCriteriaFromRequest("searchText", FALSE),
			$paginationLimits
		);

	closeMysqlConnection($db);

?>

<style>

</style>
<div class="container top-buffer-large">
	<div class="row destacados" style="padding-bottom:0px;">
<?php
	// if we have a result loop over the result
	$first = "active";
	$index = 0;
	foreach($movies as $movie){
		$movieId = $movie['id'];
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
			<div class="col-md-4 detailsLink" data-movieId="<?php echo $movieId ?>">
	    		<div>
                    <div class="pull-right ignoreLinks" style="display:none"><span class="glyphicon glyphicon-remove"></span></div>
					<img src="<?php echo $image ?>" alt="<?php echo $name  ?>" class="img-circle img-thumbnail">
					<h5>
						<?php echo $name . " (" . $year . ")" ?> 
						<?php if ($synopsis != NULL) { ?>
						<button type="button" class="btn btn-lg btn-danger popover" data-toggle="popover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?">Click to toggle popover</button>

						<a href="#!" class="tooltip"
							data-toggle="tooltip" data-placement="left" title="<?php echo $synopsis ?>">
							<span class="glyphicon glyphicon-comment"></span>
						</a>
						<?php } ?>
					</h5>
					<p>
						<?php if ($criticsRating > 0){ ?>
							<input value="<?php echo round(($criticsRating/100)*5, 1) ?>" type="number" 
								class="rating" min="0" max="5" step="0.1" data-size="xs"
								data-caption="">
						<?php }else if($audienceRating > 0){ ?>
							<input value="<?php echo round(($audienceRating/100)*5, 1) ?>" type="number" 
								class="rating" min="0" max="5" step="0.1" data-size="xs"
								data-caption="">
						<?php }else { ?>
							<input value="<?php echo round($rating, 1) ?>" type="number" 
								class="rating" min="0" max="5" step="0.1" data-size="xs"
								data-caption="">
						<?php } ?>
					</p>
				</div>
			</div>
	<?php if($index%3 == 0 && $index < count($movies)){ ?>
		</div>
		<div class="row destacados" style="padding-bottom:0px;">
	<?php } ?>
		
<?php
		$first = "";
		}
	}
?>
	</div>
    <?php include "paginate.php" ?>
</div>

<script>
	(function($){
		$(".rating").rating({
			showCaption : false,
			clearButton : "",
			disabled : true
		});
	})(jQuery);
</script>
