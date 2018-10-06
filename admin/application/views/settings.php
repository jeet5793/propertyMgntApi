
    <!-- ============================================================== -->
    <div id="wrapper">
        <!-- ============================================================== -->
       
        
        <!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Settings</h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <button class="right-side-toggle waves-effect waves-light btn-info btn-circle pull-right m-l-20"><i class="ti-settings text-white"></i></button>
                        
                        <ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
							<li class="active">Settings</li>
                        </ol>
                    </div>
                </div>
                <!-- /.row -->
				
                <div class="row">
				
				
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="white-box">
						<?php
				if(isset($_REQUEST['msg'])){
					if($_REQUEST['msg'] == "success")
						echo '<span style="color:green;">Password changed successfully</span>';
					else if($_REQUEST['msg'] == "invalid")
						echo '<span style="color:red;">Old password you entered is wrong</span>';
					else if($_REQUEST['msg'] == "mismatch")
						echo '<span style="color:red;">New password and Confirm password must be same</span>';
				}
				?>
							<div class="">
                               <div class="right-page-header"><h3>Change Password</h3> </div>
                                    <div class="clearfix"></div>
                                    <div class="scrollable">
									<?php echo form_open(base_url().'settings/change_password', 'class="form-horizontal form-material" id="settings_form" name="update_pwd" ');?>
                                       <div class="form-group">
                                            <div class="col-md-12 m-b-20">
                                                <input type="password" class="form-control" name="old_pwd" id="old_pwd" placeholder="Old Password" required>
											 </div>
											<div class="col-md-12 m-b-20">
                                              <input type="password" class="form-control" name="new_pwd" id="new_pwd" placeholder="New Password" required>
											 </div>
											<div class="col-md-12 m-b-20">
                                                <input type="password" class="form-control" name="confirm_pwd" id="confirm_pwd" placeholder="Confirm Password" required>
											 </div>
											<div class="modal-footer">
                                                <button type="submit" class="btn btn-info waves-effect">Update</button>
                                                 <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
											</div>
                                     <?php echo form_close();?>
                                    </div>
                                </div>
                               
                            </div>
                            
                        </div>
                    </div>
			 <div class="col-md-3"></div>	
			 </div> 
		
    </div>
  
    
