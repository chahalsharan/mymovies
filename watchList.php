<?php require_once 'mysqlidb.php';?>
<?php require_once 'User.php' ?>

<?php
    if(isset($_SESSION['userId'])){
        $db = getMysqlConnection();
        $user = User::loadFromDb($db, $_SESSION['userId']);
        $watching = $user->getWatchList($db);
        closeMysqlConnection($db);
?>
<div class="container" style="width:400px;height: 300px;overflow: auto;">    
    <div id="watchListBox" style="margin-top:0px;" class="mainbox">
        <div class="panel panel-info" >
            <div style="padding:0px" class="panel-body" >
                <table class="table">
                    <tbody>
                        <?php 
                            $star = '<span class="glyphicon glyphicon-play-circle"></span>';
                            foreach ($watching as $key => $value) { ?>
                            <tr>
                                <td class="detailsLink" data-movieId="<?php echo $key ?>">
                                    <a href="#!"><?php echo $value; ?></a>
                                </td>
                                <td class="detailsLink" data-movieId="<?php echo $key ?>">
                                    <a href="#!"><?php if(isset($watching[$key . "_featured"])) echo $star; ?></a>
                                </td>
                                <td class="removeFromWatchList" data-movieId="<?php echo $key ?>"><a href="#!"><span class="glyphicon glyphicon-remove"></span></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>                     
        </div>  
    </div>
</div>
<?php
    }
?>