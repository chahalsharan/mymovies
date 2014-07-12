<?php require_once 'mysqlidb.php';?>
<?php require_once 'databaseConfig.php' ?>

<?php   
    $db = getMysqlConnection();
    $cols = Array("a.actor_id, a.name, count(*) as count");
    $actors = $db
                ->join("movie_actors ma", "ma.actor_id = a.actor_id")
                ->groupBy("a.name")
                ->orderBy("a.name", "asc")
                ->get("movies m", null, $cols);
    closeMysqlConnection($db);
?>
<!-- Three columns of text below the carousel -->
  <select id="movieActorsFilter" multiple="multiple" style="width:300px" class="populate placeholder select2-offscreen" tabindex="-1"><option></option>
  <?php
  // if we have a result loop over the result
  foreach($actors as $act){
  ?>
    <p>
        <option value="<?php echo $act['actor_id'] ?>">
          <h2><?php echo urldecode($act['name']) . " ( ". $act['count'] ." )"  ?></h2>
        </option>
      </p>
  <?php
  }
  ?>
  </select>
<script>
  (function($){
    $("#movieActorsFilter").select2({
      placeholder: "Filter by actors"
    });
  })(jQuery);
</script>