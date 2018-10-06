
    <!-- Preloader -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
        </svg>
    </div>
    <section id="wrapper" class="new-login-register">
        <div class="lg-info-panel">
            <div class="inner-panel">
                <a href="javascript:void(0)" class="p-20 di"><img src="../plugins/images/admin-logo.png"></a>
                <div class="lg-content">
                    <h2>THE ULTIMATE & MULTIPURPOSE ADMIN TEMPLATE OF 2017</h2>
                    <p class="text-muted">with this admin you can get 2000+ pages, 500+ ui component, 2000+ icons, different demos and many more... </p> <a href="#" class="btn btn-rounded btn-danger p-l-20 p-r-20"> Buy now</a> </div>
            </div>
        </div>
        <div class="new-login-box">
            <div class="white-box">
                <h3 class="box-title m-b-0">Sign UP to Admin</h3> <small>Enter your details below</small>
                <form class="form-horizontal new-lg-form" method="post" id="loginform" action="<?php echo base_url();?>register">
                    <div class="form-group ">
					
                        <div class="col-xs-12">
                            <input class="form-control" type="text" required="" placeholder="Name" name="username"><small><?php echo form_error('username');?></small> </div>
                    </div>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input class="form-control" type="text" required="" placeholder="Email" name="email"> <small><?php echo form_error('email');?></small></div>
                    </div>
                    <div class="form-group ">
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required="" placeholder="Password" name="password"></small> </div>
                    </div>
                    <div class="form-group">
					
                        <div class="col-xs-12">
                            <input class="form-control" type="password" required="" placeholder="Confirm Password" name="passconf"> <small><?php echo form_error('passconf');?></small></div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary p-t-0">
                                <input id="checkbox-signup" type="checkbox">
                                <label for="checkbox-signup"> I agree to all <a href="#">Terms</a></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center m-t-20">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Sign Up</button>
                        </div>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>Already have an account? <a href="<?php echo base_url();?>login" class="text-danger m-l-5"><b>Sign In</b></a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
   