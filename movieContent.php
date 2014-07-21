<?php require_once 'mysqlidb.php';?>
<?php require_once 'movieUtils.php' ?>
<?php require_once 'Movie.php' ?>
<?php require_once 'User.php' ?>

<?php
    session_start();
    if(isset($_GET['movieId'])){
        $movieId = $_GET['movieId'];
        $db = getMysqlConnection();
        $movie = Movie::loadFromDb($db, $movieId);
        $user = NULL;
        $showAddToWatchList = TRUE;
        if(isset($_SESSION['userId'])){
            $user = User::loadFromDb($db, $_SESSION['userId']);
            $watching = $user->getWatchList($db);
            if(isset($watching[$movie->id])){
                $showAddToWatchList = FALSE;
            }
        }
        closeMysqlConnection($db);
?>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span></button>
  <h4 class="modal-title" id="movieModalName"><?php echo $movie->name . " (". $movie->year . ") " ?></h4>
</div>
<div class="modal-body" id="movieModalBody">
    <div class="container">        
        <div class="row row-margin-bottom">
            <div class="col-md-7 lib-item" data-category="view">
                <div class="lib-panel">
                    <div class="row box-shadow">
                        <div class="col-md-4" style="padding-left:0px; padding-right:0px">
                            <div class="lib-row lib-header">
                                <img class="lib-img-show" src="<?php echo $movie->thumbnail ?>">
                                <div class="lib-header-seperator"></div>
                            </div>
                            <div class="lib-row lib-desc">
                                <?php if($movie->rating->audienceScore > 0 || $movie->rating->criticsScore > 0){ ?>
                                    <?php if ($movie->rating->criticsScore > 0){ ?>
                                        <div class="progress" style="margin-bottom:5px;">
                                          <div class="progress-bar <?php echo getRatingColor($movie->rating->criticsScore) ?>" role="progressbar" 
                                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                                style="width: <?php echo intval($movie->rating->criticsScore) ?>%;">
                                                <font style="color:offgolden">
                                            <?php echo "C:" . $movie->rating->criticsScore ?>%
                                                </font>
                                          </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($movie->rating->audienceScore > 0){ ?>
                                        <div class="progress" style="margin-bottom:5px;">
                                          <div class="progress-bar <?php echo getRatingColor($movie->rating->audienceScore) ?>" role="progressbar" 
                                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                                                style="width: <?php echo intval($movie->rating->audienceScore) ?>%;">
                                            <?php echo "A: " . $movie->rating->audienceScore ?>%
                                          </div>
                                        </div>
                                    <?php } ?>
                                    <div class="pull-right">
                                        <img src="http://images.rottentomatoescdn.com/images/trademark/rottentomatoes_logo_40.png" style="height:15px; width:50px"/>
                                    </div>
                                <?php }else{ ?>
                                    <input value="<?php echo round($movie->rating->general, 1) ?>" type="number" 
                                        class="rating rating2" min="0" max="5" step="0.1" data-size="xs"
                                        data-caption="">

                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-8" style="padding-let:0px">
                            <div class="lib-row lib-header">
                                <?php if($movie->watch) { ?>
                                <button class="btn btn-primary watchMovieLink" data-link="<?php echo $movie->watch ?>">Watch</button>
                                <?php } else { ?>
                                <button class="btn btn-danger" disabled="disabled">Print not available</button>
                                <?php } ?>
                                <?php if($showAddToWatchList) { ?>
                                <button class="btn btn-success addToWatchList" data-movieId="<?php echo $movie->id ?>"><span class="glyphicon glyphicon-plus"></span>Watchlist</button>
                                <?php } ?>
                            </div>
                            <div class="lib-row lib-header">
                                Actors
                                <div class="lib-header-seperator"></div>
                            </div>
                            <div class="lib-row lib-desc">
                                <?php echo (implode(", ", array_unique($movie->actors))) ?>
                            </div>
                            <div class="lib-row lib-header">
                                <div class="lib-header-seperator"></div>
                            </div>
                            <div class="lib-row lib-desc">
                                <?php echo $movie->synopsis ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php
    }else{
?>
 <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span></button>
  <h4 class="modal-title" id="movieModalName">Movie Not Found</h4>
</div>
<div class="modal-body" id="movieModalBody">
</div>
</div>   
<?php
    }
?>
<script>
    (function($){
        $(".rating2").rating({
            showCaption : false,
            clearButton : "",
            disabled : true
        });
    })(jQuery);
</script>