<?php session_start() ?>
<div class="navbar-wrapper">
  <div class="container">
  	<div style="float:left;" data-link="<?php echo $SITE_URL . "letMeWatchThis.php" ?>">
    	<img class="index" src="myMovies.jpg">
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
		  <!-- <a class="navbar-brand active" href="letMeWatchThis.php"><img src="myMovies.jpg"></a> -->
		</div>
		<div class="navbar-collapse collapse">
		  <ul class="nav navbar-nav">
		  	<?php if(isset($_SESSION['isAdmin'])){ ?>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Load more <b class="caret"></b></a>
				<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
					<form accept-charset="UTF-8">
					  <input style="margin-bottom: 15px;" type="text" id="fromPage" size="30" placeholder="From page" />
					  <input style="margin-bottom: 15px;" type="text" id="toPage" size="30"  placeholder="To  page" />
					  <input style="margin-bottom: 15px;" type="text" id="year" size="30"  placeholder="For Year" />
					  <input id="loadMovies" class="btn btn-primary" style="clear: left; width: 100%; height: 32px; font-size: 13px;" type="button" value="Load" />
					</form>
				</div>
			</li>
			<?php } ?>
			<li class="dropdown" <?php if(isset($_SESSION['userId'])) echo 'style="display: none;"'; ?> id="loginNavOptions">
				<a href="#!" id="login" class="dropdown-toggle" data-toggle="dropdown">Login <b class="caret"></b></a>
				<div class="dropdown-menu" style="padding: 10px; padding-bottom: 0px;" >
					<?php include "loginForm.php" ?>
				</div>
			</li>
			<li class="dropdown">
				<a href="#" id="watchListDropdown" class="dropdown-toggle" data-toggle="dropdown">Watch-list<b class="caret"></b></a>
				<div class="dropdown-menu" style="padding: 10px; padding-bottom: 0px;" >
					<?php include "watchList.php" ?>
				</div>
			</li>
			<li class="dropdown" id="watchListNavOptions" <?php if(!isset($_SESSION['userId'])) echo 'style="display: none;"'; ?>>
				<a href="#" id="filterDropdown" class="dropdown-toggle" data-toggle="dropdown">Filter <b class="caret"></b></a>
				<div class="dropdown-menu" style="padding: 10px; padding-bottom: 0px;" >
					<?php include "filters.php" ?>
				</div>
			</li>
			<?php if(isset($_SESSION['isAdmin'])){ ?>
			<li id="logoutNavOptions"><a href="reloadMovies.php?startPage=1&endPage=1" id="reload">Reload</a></li>
			<?php } ?>
            <li id="logoutNavOptions" <?php if(!isset($_SESSION['userId'])) echo 'style="display: none;"'; ?>><a href="userController.php?action=logout" id="reload">Logout</a></li>
		  </ul>
          <div class="col-xs-1">&nbsp;</div>
		  <div class="col">
		  	<form class="navbar-form" id="searchMovie" role="search">
			  	<div class="input-group">
				  <input type="text" id="searchText" class="form-control input-x-large	">
				  <span class="input-group-addon" style="cursor:text;">
				  	<span class="glyphicon glyphicon-search"></span>
				  </span>
				</div>
	        </form>
          </div>
		</div>
	  </div>
	</div>

  </div>
</div>
