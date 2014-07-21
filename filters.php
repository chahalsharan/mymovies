<div class="container" style="width:400px;">    
    <div id="filterBox" style="margin-top:0px;" class="mainbox loginForms">
    <!--col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">-->
        <div class="panel panel-info" >
            <div class="panel-heading">
                <div class="panel-title">Filter results using following criteria</div>
            </div>     

            <div style="padding-top:10px" class="panel-body" >
                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>
                <form accept-charset="UTF-8" class="form-horizontal" role="form">
                    <div style="margin-bottom: 25px" class="input-group">
                        <?php include 'movieYears.php' ?>
                    </div>
                    <div style="margin-bottom: 25px" class="input-group">
                        <?php include 'movieCategories.php' ?>
                    </div>
                    <div style="margin-bottom: 25px" class="input-group">
                        <?php include 'movieActors.php' ?>
                    </div>
                    <div class="input-group">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" id="featuredOnly" value="featured" checked="true"> Movies with Quality links only
                        </label>
                      </div>
                    </div>
                    <div style="margin-top:10px" class="form-group">
                        <div class="col-md-6 controls">
                          <a id="filterMovies" href="#" class="btn btn-success">Filter  </a>
                        </div>
                    </div>
                </form>
            </div>                     
        </div>  
    </div>
</div>
