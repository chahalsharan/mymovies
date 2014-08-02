<?php require_once 'mysqliDb.php';?>
<?php require_once 'databaseConfig.php' ?>

<?php	

	$db = getMysqlConnection();
	$cols = Array("year, count(*) as count");
	$years = $db
				->groupBy("year")
				->orderBy("year", "desc")
				->get("movies", null, $cols);
	closeMysqlConnection($db);
?>
<!-- Three columns of text below the carousel -->
	<select id="movieYearsFilter" multiple="multiple" style="width:300px" class="populate placeholder select2-offscreen" tabindex="-1"><option></option>
	<?php
	// if we have a result loop over the result
	foreach($years as $year){
	?>
		<p>
    		<option value="<?php echo $year['year'] ?>">
    			<h2><?php echo $year['year'] . " ( ". $year['count'] ." )"  ?></h2>
    		</option>
    	</p>
	<!--
	<div class="col-md-3">   
	  <h2><?php echo $year['year'] . " ( ". $year['count'] ." )"  ?></h2>
	  <p>Movies for this year</p>
	  <p><a class="btn btn-default" href="#" role="button">Show</a></p>
	</div>-->
	<?php
	}
	?>
	</select>
<script>
  (function($){
    $("#movieYearsFilter").select2({
    	placeholder: "Filter by year(s)"
    });
  })(jQuery);
</script>