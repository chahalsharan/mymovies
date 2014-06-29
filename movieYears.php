<?php require_once 'mysqlidb.php';?>
<?php	
	// start database connection if one doesnt already exist
	if(!isset($db)){
		$db = new Mysqlidb('localhost', 'root', '', 'let_me_watch_this');
	}
	$cols = Array("year, count(*) as count");
	$years = $db
				->groupBy("year")
				->get("movies", null, $cols);
?>
<!-- Three columns of text below the carousel -->
<div class="row">
	<?php
	// if we have a result loop over the result
	$index = 1;
	foreach($years as $year){
	?>
	<div class="col-lg-4">   
	  <h2><?php echo $year['year'] . " ( ". $year['count'] ." )"  ?></h2>
	  <p>Movies for this year</p>
	  <p><a class="btn btn-default" href="#" role="button">Show</a></p>
	</div><!-- /.col-lg-4 -->
	<?php
	}
	?>
</div><!-- /.row -->