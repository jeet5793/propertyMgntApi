<!DOCTYPE html>
<html lang="en">



<body class="fix-header">
    <!-- ============================================================== -->
    <!-- Preloader -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
    <!-- ============================================================== -->
    <!-- Wrapper -->
    <!-- ============================================================== -->
    <div id="wrapper">
        <!-- ============================================================== -->
     
        
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Feature Mapper</h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <button class="right-side-toggle waves-effect waves-light btn-info btn-circle pull-right m-l-20"><i class="ti-settings text-white"></i></button>
                        
                        <ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
                            <li class="">Feature Mapper</li>
							<li class="active">Add</li>
                        </ol>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box">
                           
                            
                                <div class="">
                                    <div class="right-page-header">
                                        
                                        <h3>Add  </h3> </div>
                                    <div class="clearfix"></div>
                                    <div class="scrollable">
									<?php echo form_open(base_url().'featuremapper/add', 'class="form-horizontal form-material" id="planform" name="featuremapper" ');?>
                                     
                                        <div class="form-group">
                                            <div class="col-md-12 m-b-20">
											<?php 
											foreach($usertype as $type)
											{
												$typeArr[0]="Select a user type";
												$typeArr[$type['id']]=$type['user_type'];
											}
											$js = array(
													'class' => 'form-control',
													'id'       => 'usertype',
													'onChange' => 'getPlan();'
											);
												echo form_dropdown('user_type', $typeArr,'', $js);
												 echo "<small>".form_error('user_type')."</small>";
											?>
												
											</div>
											  <div class="col-md-12 m-b-20">
											<?php 
											
											$js = array(
													'class' => 'form-control',
													'id'       => 'plan',
													
											);
												echo form_dropdown('plan','','', $js);
												 echo "<small>".form_error('plan')."</small>";
											?>
												
											</div>
											 <div class="col-md-12 m-b-20">
											<?php 
											foreach($feature as $featureval)
											{
												$featureArr[0]="Select a feature";
												$featureArr[$featureval['id']]=$featureval['feature_name'];
											}
												echo form_dropdown('feature', $featureArr,'', ['class' => 'form-control','onChange' => 'getFeatureunit();','id'=>'feature']);
												 echo "<small>".form_error('feature')."</small>";
											?>
												
											</div>
											<div class="col-md-12 m-b-20" id="limitupto" style="display:none">
                                                <?php 
													$data = array(
															
															'type'  => 'text',
															'name'  => 'limitupto',
															'value' => '',
															'class' => 'form-control',
															'placeholder' => 'Limit Upto'
													);

													echo form_input($data);
													 echo "<small>".form_error('limitupto')."</small>";
												?>
											 </div>
											  
											  <div class="col-md-12 m-b-20" id="feature_unit" style="display:none">
											
                                                <input type="radio" name="feature_unit" class=""  value="Yes" <?php echo set_radio('confirm', 'Yes', TRUE); ?>>Yes
												<input type="radio" name="feature_unit" class="" value="No" <?php echo set_radio('confirm', 'No'); ?>>No </div>
                                                                                
                                            </div>
											
                                            <div class="col-md-12 m-b-20">
											
                                                <input type="radio" name="status" class=""  value="1" <?php echo set_radio('status', '1', TRUE); ?>>Active
												<input type="radio" name="status" class="" value="0" <?php echo set_radio('status', '0'); ?>>Inactive </div>
                                                                                
                                            </div>
											<div class="modal-footer">
                                                <button type="submit" class="btn btn-info waves-effect">Save</button>
                                                 <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
											</div>
                                     <?php echo form_close();?>
                                    </div>
                                </div>
                                <!-- .left-aside-column-->
                            </div>
                            <!-- /.left-right-aside-column-->
                        </div>
                    </div>
                </div>
                <!-- /.row -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <div class="right-sidebar">
                    <div class="slimscrollright">
                        <div class="rpanel-title"> Service Panel <span><i class="ti-close right-side-toggle"></i></span> </div>
                        <div class="r-panel-body">
                            <ul id="themecolors" class="m-t-20">
                                <li><b>With Light sidebar</b></li>
                                <li><a href="javascript:void(0)" data-theme="default" class="default-theme">1</a></li>
                                <li><a href="javascript:void(0)" data-theme="green" class="green-theme">2</a></li>
                                <li><a href="javascript:void(0)" data-theme="gray" class="yellow-theme">3</a></li>
                                <li><a href="javascript:void(0)" data-theme="blue" class="blue-theme">4</a></li>
                                <li><a href="javascript:void(0)" data-theme="purple" class="purple-theme">5</a></li>
                                <li><a href="javascript:void(0)" data-theme="megna" class="megna-theme">6</a></li>
                                <li><b>With Dark sidebar</b></li>
                                <br/>
                                <li><a href="javascript:void(0)" data-theme="default-dark" class="default-dark-theme">7</a></li>
                                <li><a href="javascript:void(0)" data-theme="green-dark" class="green-dark-theme">8</a></li>
                                <li><a href="javascript:void(0)" data-theme="gray-dark" class="yellow-dark-theme">9</a></li>
                                <li><a href="javascript:void(0)" data-theme="blue-dark" class="blue-dark-theme working">10</a></li>
                                <li><a href="javascript:void(0)" data-theme="purple-dark" class="purple-dark-theme">11</a></li>
                                <li><a href="javascript:void(0)" data-theme="megna-dark" class="megna-dark-theme">12</a></li>
                            </ul>
                            <ul class="m-t-20 all-demos">
                                <li><b>Choose other demos</b></li>
                            </ul>
                            <ul class="m-t-20 chatonline">
                                <li><b>Chat option</b></li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../plugins/images/users/varun.jpg" alt="user-img" class="img-circle"> <span>Varun Dhavan <small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../plugins/images/users/genu.jpg" alt="user-img" class="img-circle"> <span>Genelia Deshmukh <small class="text-warning">Away</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../plugins/images/users/ritesh.jpg" alt="user-img" class="img-circle"> <span>Ritesh Deshmukh <small class="text-danger">Busy</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../plugins/images/users/arijit.jpg" alt="user-img" class="img-circle"> <span>Arijit Sinh <small class="text-muted">Offline</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../plugins/images/users/govinda.jpg" alt="user-img" class="img-circle"> <span>Govinda Star <small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../plugins/images/users/hritik.jpg" alt="user-img" class="img-circle"> <span>John Abraham<small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../plugins/images/users/john.jpg" alt="user-img" class="img-circle"> <span>Hritik Roshan<small class="text-success">online</small></span></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)"><img src="../plugins/images/users/pawandeep.jpg" alt="user-img" class="img-circle"> <span>Pwandeep rajan <small class="text-success">online</small></span></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- /.container-fluid -->
           
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
 <script tyle="javascript">
$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div><input type="text" name="feature[]" class="" placeholder="Feature"><a href="#" class="remove_field">Remove</a></div>'); //add input box
        }
    });
    
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});
function getPlan()
{
	var usertype = $("#usertype").val();
	//alert(usertype);
	$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>" + "plan/getPlanlist",
			//dataType: 'json',
			data: {usertype: usertype},
			success: function(res) 
			{
				//console.log(res);
				$("#plan").html(res);
			}
	});
}
function getFeatureunit()
{
	var feature = $("#feature").val();
	//alert(usertype);
	$.ajax({
			type: "POST",
			url: "<?php echo base_url(); ?>" + "plan/getFeatureunit",
			//dataType: 'json',
			data: {feature: feature},
			success: function(res) 
			{
				console.log(res);
				if(res=='Restrict')
				{
					$("#feature_unit").show();
					$("#limitupto").hide();
				}else if(res=='Limit')
				{
					$("#limitupto").show();
					$("#feature_unit").hide();
				}
			}
	});
}

</script> 