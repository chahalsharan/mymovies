(function ($) {
  $("body").on("click", ".index", function(){
    window.location($(this).attr("data-link"));
  });

  $("body").on("click", ".detailsLink", function(){
    var movieId = $(this).attr("data-movieId");
    if(movieId){
      $.ajax({
        type: "GET",
        url: "movieContent.php?movieId=" + movieId
      })
      .done(function( data ) {
        $("#movieModalContent").html(data);
        $("#movieModal").modal('show')
      })
      .fail(function(msg){
        console.error("failed " + msg); 
      });
    }
    return false;
  });

  $("body").on("click", ".watchMovieLink", function(){
    var movieLink = $(this).attr("data-link");

    if(movieLink){
      window.open(movieLink,'_blank');
    }
    return false;
  });

  $("body").on("click", ".removeFromWatchList", function(event){
    event.preventDefault();
    var movieId = $(this).attr("data-movieId");
    if(movieId){
      $(this).attr("disabled", "disabled");
      var btnHtml = $(this).siblings().html();
      $(this).siblings().html("Removing...");
      var that = this;
      $.post( "userController.php", { 
        "movieId" : movieId,
        "action" : "removeFromWatchList"
      }).done(function(){
        $(that).siblings().html("Removed!");
        $(that).siblings().attr("data-movieId", "");
        $(that).attr("data-movieId", "");
      }).fail(function(data){
        $(that).removeAttr("disabled");
        $(that).siblings().html(btnHtml);
        console.log(data);
      });
      return false;
    }
  })

  $("body").on("click", ".addToWatchList", function(event){
    event.preventDefault();
    var movieId = $(this).attr("data-movieId");
    if(movieId){
      $(this).attr("disabled", "disabled");
      var btnHtml = $(this).html();
      $(this).html("Adding...");
      var that = this;
      $.post( "userController.php", { 
        "movieId" : movieId,
        "action" : "addToWatchList"
      }).done(function(){
        $(that).html("Added!");
      }).fail(function(data){
        $(that).removeAttr("disabled");
        $(that).html(btnHtml);
        console.log(data);
      });
    }
  });
}(jQuery));