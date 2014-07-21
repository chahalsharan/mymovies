<?php require_once 'mysqlidb.php';?>
<?php require_once 'databaseConfig.php' ?>
<?php require_once 'User.php' ?>
<?php

    $userId = NULL;
    $confirmationCode = NULL;
    if(isset($_GET['userId'])){
        $userId = $_GET['userId'];
    }
    if(isset($_GET['confirmationCode'])){
        $confirmationCode = $_GET['confirmationCode'];
    }

    $err = "Invalid confirmation code.";
    if($userId && $confirmationCode){
        error_log("DEBUG: going to validate confirmation code");
        $db = getMysqlConnection();
        $user = User::loadFromDb($db, $userId);
        if($user){
            if($user->isActive()){
                error_log("DEBUG: going to validate confirmation code");
                $err = "Account already activated.";
            }else if($user->confirmCode($db, $confirmationCode)){
                $err = NULL;
            }
        }
        closeMysqlConnection($db);
    }else{
        error_log("DEBUG: going to validate confirmation code");
    }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> New Document </TITLE>
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
  <title>Let me watch these movies</title>
  <script type="text/javascript" src="json2.js"></script>
  <script type="text/javascript" src="jquery-1.11.1.js"></script>
  <!-- Latest compiled and minified CSS -->
  <!--<link rel="stylesheet" href="bootstrap-modal-bs3patch.css"> -->
  <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
  <!-- Optional theme -->
  <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
  <link rel="stylesheet" href="http://getbootstrap.com/examples/carousel/carousel.css">
  <!-- Latest compiled and minified JavaScript -->
  <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.js"></script>
  <script type="text/javascript" src="jquery.store.js"></script>
  <!-- Select2 -->
  <link href="http://ivaynberg.github.io/select2/select2-3.5.0/select2.css?ts=2014-06-26T15%3A33%3A45-07%3A00" rel="stylesheet">
  <script src="http://ivaynberg.github.io/select2/select2-3.5.0/select2.js?ts=2014-06-26T15%3A33%3A45-07%3A00"></script>
  <!--<script type="text/javascript" src="bootstrap-rating.js"></script>-->
  <link href="star-rating.css" media="all" rel="stylesheet" type="text/css"/>
  <script src="star-rating.js" type="text/javascript"></script>
  <link href="letMeWatchThis.css" rel="stylesheet">
 </HEAD>
<body>
    <div class="navbar-wrapper">
      <div class="container">
        <div class="index" style="float:left;">
            <img src="myMovies.jpg">
        </div>
        <div class="navbar navbar-inverse navbar-static-top" style="margin-left:210px; height: 25px;" role="navigation">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
            </div>
            <div class="navbar-collapse collapse">
            </div>
          </div>
        </div>

      </div>
    </div>
    <div class="container" style="margin-top:100px;">
        <?php if($err) { ?>
        <div class="alert errorMessage alert-danger col-sm-12"><?php echo $err ?></div>
        <?php }else{ ?>
        <h4>Account activated! <br/> <a href="letMeWatchThis.php">Click Here</a> to go to main site.</h4>
        <?php } ?>
    </div>
</body>
</html>