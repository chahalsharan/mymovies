<?php require_once 'mysqlidb.php';?>
<?php require_once 'databaseConfig.php' ?>

<?php   
    $db = getMysqlConnection();
    $cols = Array("category, count(*) as count");
    $categories = $db
                ->join("movie_categories mc", "mc.category_id = c.category_id")
                ->groupBy("c.category")
                ->orderBy("category", "asc")
                ->get("categories c", null, $cols);
    closeMysqlConnection($db);
?>
<!-- Three columns of text below the carousel -->
  <select id="movieCategoriesFilter" multiple="multiple" style="width:300px" class="populate placeholder select2-offscreen" tabindex="-1"><option></option>
  <?php
  // if we have a result loop over the result
  foreach($categories as $cat){
  ?>
    <p>
        <option value="<?php echo $cat['category'] ?>">
          <h2><?php echo $cat['category'] . " ( ". $cat['count'] ." )"  ?></h2>
        </option>
      </p>
  <?php
  }
  ?>
  </select>
<script>
  (function($){
    $("#movieCategoriesFilter").select2({
      placeholder: "Filter by categories"
    });
  })(jQuery);
</script>