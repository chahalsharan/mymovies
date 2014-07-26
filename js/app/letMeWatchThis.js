var letMeWatchThis = {};
(function ($, _$) {
  $("body").on("click", ".index", function(){
    location.reload();
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

  $("#loginform").on("submit", function(event){
        // Stop form from submitting normally
        event.preventDefault();
        var userName = $(this).find(".userName").val();
        var password = $(this).find(".passwd").val();
        var alertBox = $($(this).find(".errorMessage"));
        var successBox = $($(this).find(".successMessage"));
        var userAction = $($(this).find(".userAction")).val();
        $(".alert").hide();

        if(userName && password && userAction){
            var url = $(this).attr( "action" );
            $.post( url, { 
                "user" : userName,
                "password" : password,
                "action" : userAction
            }).done(function(){
                successBox.html("Login successful!");
                successBox.show();
                setTimeout( function(){
                    _$.reloadTopNav();
                }, 1000 );
            }).fail(function(data){
                console.log(data);
                if(data.responseText){
                    alertBox.html(data.responseText);
                }else{
                    alertBox.html(data.statusText);
                }
                alertBox.show();
            });
        }else{
            alertBox.html("Please enter all details")
            alertBox.show();
            return false;
        }
    });
    $("#signupform").on("submit", function(event){
        // Stop form from submitting normally
        event.preventDefault();
        var userName = $(this).find(".userName").val();
        var password = $(this).find(".passwd").val();
        var cpassword = $(this).find(".cpasswd").val();
        var email = $(this).find(".email").val();
        var alertBox = $($(this).find(".errorMessage"));
        var successBox = $($(this).find(".successMessage"));
        var userAction = $($(this).find(".userAction")).val();

        $(".alert").hide();

        if(userName && password && email && cpassword && userAction){
            if(!IsEmail(email)){
                alertBox.html("Email is invalid")
                alertBox.show();
                return false;
            }
            if(password != cpassword){
                alertBox.html("Passwords don't match")
                alertBox.show();
                return false;
            }
            successBox.hide();
            alertBox.hide();
            var url = $(this).attr( "action" );
            // Send the data using post
            $.post( url, { 
                "user" : userName,
                "email" : email,
                "password" : password,
                "action" : userAction
            }).done(function(){
                successBox.html("User create successfully! Confirmation email sent to your email.");
                successBox.show();
            }).fail(function(data){
                console.log(data);
                if(data.responseText){
                    alertBox.html(data.responseText);
                }else{
                    alertBox.html(data.statusText);
                }
                alertBox.show();
            });
        }else{
            $("#signupalert").html("Please enter all details")
            $("#signupalert").show();
        }
        return false;

    })

    function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(email);
    }

    _$.reloadTopNav = function reloadTopNav(){
        $.ajax({
            type: "GET",
            url: "topNav.php"
        })
        .done(function( data ) {
            $("#topNavContainer").html(data);
        })
        .fail(function(msg){
            console.error("failed " + msg); 
        });
    };

    $('#moviesPagination').bootpag({
        total: $("#moviesPaginationPages").val(),
        page: 1,
        maxVisible: 10 
    }).on('page', function(event, num){
        var params = $("#currentQueryParameters").val();
        if(num){
            $.ajax({
                type: "GET",
                url: "movies.php?" + params 
                    + "&currentPage=" + num
                })
            .done(function( data ) {
                $("#moviesContainerMain").html(data);
                //$(this).bootpag({total: $("#moviesPaginationPages").val()});
            })
            .fail(function(msg){
                console.error("failed " + msg); 
            });
        }
    });

    _$.reInitPaginator = function reInitPaginator(id, totalPages, page){
        var pages = $("#moviesPaginationPages").val();
        if(pages <= 0){
            pages = 1;
        }
        $('#moviesPagination').bootpag({total: pages, page: 1});
    }

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
        var uid = (new Date()).getTime();
        if(years || categories || actors){
            $.ajax({
                type: "GET",
                url: "movies.php?filter=yes" + getIfNotNull("&years", years) + 
                    getIfNotNull("&categories", categories) + 
                    getIfNotNull("&actors", actors) + 
                    getIfNotNull("&notFeatured", notFeatured) +
                    '&uid=' + uid
            })
            .done(function( data ) {
                $("#moviesContainerMain").html(data);
                $("#filterDropdown").dropdown('toggle');
                _$.reInitPaginator();
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

    $("#searchMovie").on("submit", function(){
        var uid = (new Date()).getTime();
        var searchText = $("#searchText").val();
        event.preventDefault();
        if(searchText){
            $.ajax({
                type: "GET",
                url: "movies.php?filter=yes" + getIfNotNull("&searchText", searchText)
                    + getIfNotNull("&notFeatured", "1") +
                    '&uid=' + uid
            })
            .done(function( data ) {
                $("#moviesContainerMain").html(data);
                _$.reInitPaginator();
            })
            .fail(function(msg){
                console.error("failed " + msg); 
            });
        }
        return false;
    });


}(jQuery, letMeWatchThis));