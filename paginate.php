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

?>
<input type="hidden" id="currentQueryParameters" value="<?php echo $params ?>">
<input type="hidden" id="moviesPaginationPages" value="<?php echo $numPages ?>">