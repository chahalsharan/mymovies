<div class="container" style="width:350px;">    
    <div id="loginbox" style="margin-top:0px;" class="mainbox loginForms">
    <!--col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">-->
        <div class="panel panel-info" >
            <div class="panel-heading">
                <div class="panel-title">Sign In</div>
                <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
            </div>     

            <div style="padding-top:10px" class="panel-body" >
                    
                <form id="loginform" action="userController.php" class="form-horizontal" role="form">
                    <input type="hidden" class="userAction" value="login">
                    <div style="display:none" id="loginAlert" class="alert errorMessage alert-danger col-sm-12"></div>
                    <div style="display:none" id="loginSuccess" class="alert successMessage alert-success col-sm-12"></div>
                            
                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                        <input type="text" class="form-control userName" value="" placeholder="username or email">                                        
                    </div>
                        
                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                        <input type="password" class="form-control passwd" placeholder="password">
                    </div>
                    <div class="input-group">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" name="remember" value="1" class="rememberMe"> Remember me
                        </label>
                      </div>
                    </div>
                    <div style="margin-top:10px" class="form-group">
                        <div class="col-md-6 controls">
                            <button id="btn-login" type="submit" class="btn btn-info"><i class="icon-hand-right"></i> Login</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 control">
                            <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                Don't have an account! 
                            <a href="#" onClick="$('.loginForms').toggle();">
                                Sign Up Here
                            </a>
                            </div>
                        </div>
                    </div>    
                </form>
            </div>                     
        </div>  
    </div>
    <div id="signupbox" style="display:none; margin-top:0px" class="mainbox loginForms">
        <div class="panel panel-info">
            <div class="panel-heading">
                <div class="panel-title">Sign Up</div>
                <div style="float:right; font-size: 85%; position: relative; top:-10px"><a id="signinlink" href="#" onclick="$('.loginForms').toggle();">Sign In</a></div>
            </div>  
            <div class="panel-body" >
                <form id="signupform" action="userController.php" class="form-horizontal" role="form">
                    <input type="hidden" class="userAction" value="createUser">
                    <div style="display:none" class="alert errorMessage alert-danger col-sm-12"></div>
                    <div style="display:none" class="alert successMessage alert-success col-sm-12"></div>
                    <div class="form-group">
                        <div class="col-md-11">
                            <input type="text" class="form-control email" name="" placeholder="Email Address">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-11">
                            <input type="text" class="form-control userName" name="" placeholder="User Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-11">
                            <input type="password" class="form-control passwd" name="" placeholder="Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-11">
                            <input type="password" class="form-control cpasswd" name="" placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class="form-group">
                        <!-- Button -->                                        
                        <div class="col-md-9">
                            <button id="btn-signup" type="submit" class="btn btn-info"><i class="icon-hand-right"></i> Sign Up</button>
                        </div>
                    </div>
                </form>
             </div>
        </div>    
     </div> 
</div>
<script>
(function(){
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
                    $('#login').dropdown('toggle')
                    $(".alert").hide();
                    $("#loginNavOptions").hide();
                    $("#logoutNavOptions").show();
                }, 2000 );
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
})();
    
</script>
