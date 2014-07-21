<?php
    $currentPage = 1;
    if (isset($_GET['currentPage'])) {
        $currentPage = $_GET['currentPage'];
    }
    $firstPageLinkDisabled = "";
    $prevPage = "";
    if($currentPage == 1){
        $firstPageLinkDisabled = "disabled";
    }else{
        $prevPage = $currentPage - 1;
    }
    $lastPageLinkDisabled = "";
    $nextPage = "";
    if($currentPage >= ($NUM_ROWS / $PAGE_SIZE)){
        $lastPageLinkDisabled = "disabled";
    }else{
        $nextPage = $currentPage + 1;
    }
    
    $numPages = ceil($NUM_ROWS/$PAGE_SIZE);
    $numMovies = $NUM_ROWS;
    $new_data = array("numPages" => $numPages,
                      "numMovies" => $numMovies);
    $full_data = array_merge($_GET, $new_data);  // New data will overwrite old entry
    unset($full_data['currentPage']);
    $params = http_build_query($full_data);

    if($numPages == 1){
        echo "";
    }else{
?>
<input type="hidden" id="currentQueryParameters" value="<?php echo $params ?>">
<ul class="pagination">
    <li class="<?php echo $firstPageLinkDisabled ?>">
        <a href="#!" class="goToPage" data-page="<?php echo $prevPage ?>">&laquo;</a>
    </li>
    <?php for($i = 0; $i < $numPages; $i++){ 
        $active = "";
        if($i+1 == $currentPage){ 
            $active = "active";
        }
        ?>
    <li class="<?php echo $active ?>"><a href="#!" class="goToPage" data-page="<?php echo $i+1 ?>"><?php echo $i+1 ?></a></li>
    <?php } ?>
    <li class="<?php echo $lastPageLinkDisabled ?>">
        <a href="#!" class="goToPage" data-page="<?php echo $nextPage ?>">&raquo;</a>
    </li>
</ul>

<script>
    (function($){
        $(".goToPage").on("click", function(){
          var page = $(this).attr("data-page");
          var params = $("#currentQueryParameters").val();
          if(page){
            $.ajax({
              type: "GET",
              url: "movies.php?" + params 
                + "&currentPage=" + page
            })
            .done(function( data ) {
              $("#moviesContainer").html(data);
            })
            .fail(function(msg){
              console.error("failed " + msg); 
            });
          }
        })
    })(jQuery);
</script>
<?php
    }
?>