<div class="navbar-wrapper">
  <div class="container">
	<div class="navbar navbar-inverse navbar-static-top" role="navigation">
	  <div class="container">
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </button>
		  <a class="navbar-brand active" href="#">Watch these movies</a>
		</div>
		<div class="navbar-collapse collapse">
		  <ul class="nav navbar-nav">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Load more <b class="caret"></b></a>
				<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
					<form accept-charset="UTF-8">
					  <input style="margin-bottom: 15px;" type="text" id="fromPage" size="30" placeholder="From page" />
					  <input style="margin-bottom: 15px;" type="text" id="toPage" size="30"  placeholder="To  page" />
					  <input style="margin-bottom: 15px;" type="text" id="year" size="30"  placeholder="For Year" />
					  <!--<input id="user_remember_me" style="float: left; margin-right: 10px;" type="checkbox" name="user[remember_me]" value="1" />
					  <label class="string optional" for="user_remember_me"> Remember me</label>-->
					 
					  <input id="loadMovies" class="btn btn-primary" style="clear: left; width: 100%; height: 32px; font-size: 13px;" type="button" value="Load" />
					</form>
				</div>
			</li>
			<li class="dropdown">
				<a href="#" id="filterDropdown" class="dropdown-toggle" data-toggle="dropdown">Filter <b class="caret"></b></a>
				<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;" >
					<form accept-charset="UTF-8">
					  	<div class="row">
					      <div class="col-md-5">
					        <?php include 'movieYears.php' ?>
					      </div>
				    	</div>
				    	<div class="row top-buffer-small ">
				    		<div class="col-md-5">
					        	<?php include 'movieCategories.php' ?>
					      	</div>
					  	</div>
					  	<div class="row top-buffer-small ">
				    		<div class="col-md-5">
					        	<?php include 'movieActors.php' ?>
					      	</div>
					  	</div>
					    <div class="row top-buffer-small ">
					      <div class="col-md-10">
					        Show movies with DVD links only <input type="checkbox" id="featuredOnly" value="featured" checked="true" />
					      </div>
					    </div>
					    <div class="row top-buffer-small ">
					      <div class="col-md-5">
					        <input id="filterMovies" class="btn btn-primary" style="clear: left; width: 100%; height: 32px; font-size: 13px;" type="button" value="Filter" />
					      </div>
					    </div>
					</form>
				</div>
			</li>
			<li><a href="reloadMovies.php?startPage=1&endPage=1" id="reload">Reload</a></li>
		  </ul>
		</div>
	  </div>
	</div>

  </div>
</div>
<script>
	(function($,undefined){
		var uid = (new Date()).getTime();
		$('.dropdown input, .dropdown label').click(function(e) {
			e.stopPropagation();
		  });
		$("#loadMovies").on("click", function(){
			var fromPage = $("#fromPage").val();
			var toPage = $("#toPage").val();
			var year = $("#year").val();
			
			if(fromPage && toPage && year){
				$.ajax({
				  type: "POST",
				  url: "reloadMovies.php",
				  data: { fromPage: fromPage, toPage: toPage, year: year, cacheBuster: uid }
				})
				.done(function( msg ) {
					console.log(msg);
					//location.reload();
				})
				.fail(function(msg){
					console.error("failed " + msg);	
				});
			}else{
				alert("Please select valid from and to page");
			}
		});
	    $("#filterMovies").on("click", function(){
	      var years = $("#movieYearsFilter").val();
	      var categories = $("#movieCategoriesFilter").val();
	      var actors = $("#movieActorsFilter").val();
	      var notFeatured = !$("#featuredOnly").is(':checked');
	      if(years || categories || actors){
	        $.ajax({
	          type: "GET",
	          url: "movies.php?filter=yes" + getIfNotNull("&years", years) + 
	          		getIfNotNull("&categories", categories) + 
	          		getIfNotNull("&actors", actors) + 
	          		getIfNotNull("&notFeatured", notFeatured)
	        })
	        .done(function( data ) {
	          $("#moviesContainer").html(data);
	          $("#filterDropdown").dropdown('toggle')
	        })
	        .fail(function(msg){
	          console.error("failed " + msg); 
	        });
	      }
	    });
	    var getIfNotNull = function (label, toGet){
	      if(toGet){
	        return label + "=" + toGet;
	      }else{
	        return "";
	      }
	    }

	})(jQuery);

</script>