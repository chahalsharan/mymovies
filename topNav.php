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
					  <!--<input id="user_remember_me" style="float: left; margin-right: 10px;" type="checkbox" name="user[remember_me]" value="1" />
					  <label class="string optional" for="user_remember_me"> Remember me</label>-->
					 
					  <input id="loadMovies" class="btn btn-primary" style="clear: left; width: 100%; height: 32px; font-size: 13px;" type="button" value="Load" />
					</form>
				</div>
				<!--
				<ul class="dropdown-menu">
				  <li></li>
				  <li><input type="text" id="fromPage" /></li>
				  <li><input type="text" id="toPage" /></li>
				  <li class="divider"></li>
				  <li class="nav-header">Nav header</li>
				  <li><a href="#">Separated link</a></li>
				  <li><a href="#">One more separated link</a></li>
				</ul>
				-->
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
			if(fromPage && toPage){
				$.ajax({
				  type: "POST",
				  url: "reloadMovies.php",
				  data: { fromPage: fromPage, toPage: toPage, cacheBuster: uid }
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
	})(jQuery);

</script>