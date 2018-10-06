<!DOCTYPE html>
<html lang="en">

<style>
 .fixed-action-btn {
    position: fixed;
    right: 6%;
    top: 40%;
    padding-top: 15px;
    margin-bottom: 0;
    z-index: 998;
}
.btn-floating.btn-large {
    width: 40px;
    height: 40px;
    line-height: 43px;
    text-align: center;
    color: #fff !important;
    font-size: 20px;
    font-weight: 100;
}
.btn-floating {
    display: inline-block;
    color: #fff;
    position: relative;
    overflow: hidden;
    z-index: 1;
    width: 40px;
    height: 40px;
    line-height: 40px;
    padding: 0;
    background-color: #26a69a;
    border-radius: 50%;
    transition: .3s;
    cursor: pointer;
    vertical-align: middle;
}
.custome-temp {
    position: fixed;
    z-index: 999;
    background: rgb(255, 255, 255);
    right: 9%;
    display: block;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid rgb(227, 236, 238);
    border-radius: 3px;
    top: 40%;
    width: 25%;
}
.m-b-10 {
    margin-bottom: 10px !important;
}
.m-b-5 {
    margin-bottom: 5px !important;
}

.card {
    position: relative;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -webkit-flex-direction: column;
    -ms-flex-direction: column;
    flex-direction: column;
    background-color: #fff;
    border: 1px solid rgba(0,0,0,.125);
    border-radius: .25rem;
}
.custome-temp .card .card-header {
    padding: 7px;
    border: 0 !important;
    text-align: left;
}

.card-header:first-child {
    border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
}
.btn-success {
    background-color: #32c861 !important;
    border: 1px solid #32c861 !important;
}
.mb-0 {
    margin-bottom: 0!important;
	margin: 0;
}
.font-blk {
    color: #fff !important;
}
.custome-temp .card .card-block {
    padding: 10px;
}

.card-block {
    -webkit-box-flex: 1;
    -webkit-flex: 1 1 auto;
    -ms-flex: 1 1 auto;
    flex: 1 1 auto;
    padding: 1.25rem;
}
.add-name [type=button] {
	margin-bottom: 2px;
	    padding: 1px 2px;
    background: none;
    border-radius: 2px;
    color: #3fc45e;
    font-size: 11px;
	font-weight: 500;
	letter-spacing: 0.5px;
	text-transform: uppercase;
	cursor: pointer;
	display: flex;
	align-items: center;
	/* -webkit-box-shadow: 0 0 0 0.5px rgba(49,49,93,0.03), 0 2px 5px 0 rgba(49,49,93,0.1), 0 1px 2px 0 rgba(0,0,0,0.08); */
    /* box-shadow: 0 0 0 0.5px rgba(49,49,93,0.03), 0 2px 5px 0 rgba(49,49,93,0.1), 0 1px 2px 0 rgba(0,0,0,0.08); */
	-webkit-transition: background 0.2s ease;
	transition: background 0.2s ease;
	border: 1px solid #ddd;
	font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
</style>

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
                        <h4 class="page-title">Agreement Form</h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <button class="right-side-toggle waves-effect waves-light btn-info btn-circle pull-right m-l-20"><i class="ti-settings text-white"></i></button>
                        
                        <ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
                            <li class="">Agreement Form</li>
							<li class="active">Add</li>
                        </ol>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box">
                           
                            
                                <div class="row">
                                    <div class="right-page-header">
                                        
                                        <h3>Add Form </h3> </div>
                                    <div class="clearfix"></div>
									 <div class="col-md-11">
                                    <div class="scrollable">
									<?php echo form_open(base_url().'agreementform/add', 'class="form-horizontal form-material" id="planform" name="plan" enctype="multipart/form-data"');?>
                                     
                                        <div class="form-group">
                                            
											<div class="col-md-12 m-b-20">
                                                <?php 
													$data = array(
															'type'  => 'text',
															'name'  => 'form_name',
															'value' => '',
															'class' => 'form-control',
															'placeholder' => 'Form Name'
													);

													echo form_input($data);
													 echo "<small>".form_error('form_name')."</small>";
												?>
											 </div>
											 <div class="row">
                            <div class="col-md-12">
                               
							      <!-- sample modal content -->

                            
							  <div class="fixed-action-btn hide-on-large-only">

							  <a class="btn-floating btn-large teal" onclick="showSideOption()"> <i class="large ti-menu"></i> </a> 
							  
							  </div>
                              
							  
							  
							  <div class="custome-temp" id="sideTogle" style="display: none;">
                                
								<div class="autohide1-scroll">
                                  <div id="accordion"  class="m-b-10">
									<div class="card m-b-5">
                                      <div class="card-header btn btn-success waves-effect w-md waves-light" role="tab" id="headingFour">
                                        <h5 class="mb-0 mt-0"> <a class="font-blk" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour"> Insert Dynamic Value </a> </h5>
                                      </div>
                                      <div id="collapseFour" class="collapse" role="tabpanel" aria-labelledby="headingFour">
                                        <div class="card-block">
                                          <div class="add-name">
                                            <input type="button" value="Rent Amount" onclick="insertComponent(this.value)"/>
                                            <input type="button" value="Selling Amount" onclick="insertComponent(this.value)"/>
                                            <input type="button" value="Deposit Amount" onclick="insertComponent(this.value)"/>
                                            <input type="button" value="Owner Full Name" onclick="insertComponent(this.value)"/>
                                            <input type="button" value="Agent Full Name" onclick="insertComponent(this.value)"/>
                                            <input type="button" value="Tenant Full Name" onclick="insertComponent(this.value)"/>
                                            <input type="button" value="Agent Address" onclick="insertComponent(this.value)"/>
                                            <input type="button" value="Owner Address" onclick="insertComponent(this.value)"/>
                                            <input type="button" value="Tenant Address" onclick="insertComponent(this.value)"/>
                                            <input type="button" value="Property Address" onclick="insertComponent(this.value)"/>
										</div>
                                        </div>
                                      </div>
                                    </div>
									
									
									<div class="card">
                                      <div class="card-header btn btn-success waves-effect w-md waves-light" role="tab" id="headingFive">
                                        <h5 class="mb-0 mt-0"> <a class="font-blk" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive"> Insert Components </a> </h5>
                                      </div>
                                      <div id="collapseFive" class="collapse" role="tabpanel" aria-labelledby="headingFive">
                                        <div class="card-block">
                                          <div class="add-name">
                                            <input type="button" value="Insert Signature Block" onclick="insertComponent(this.value)"/>
											<input type="button" value="Insert Text Box" onclick="insertComponent(this.value)"/>
											<input type="button" value="Insert Date Box" onclick="insertComponent(this.value)"/>
											<input type="button" value="Insert Check Box" onclick="insertComponent(this.value)"/>
										 </div>
                                        </div>
                                      </div>
                                    </div>
									
									
									
                                  </div>
								  
                                </div>
                              </div>
                            </div>
                          </div>
											<div class="col-md-12 m-b-20">
                                              <textarea name="description" id="mymce" class="form-control" placeholder="Description"></textarea><?php echo form_error('description');?></div>
											
											<div class="col-md-12 m-b-20">
                                                <?php 
													$data = array(
															'type'  => 'text',
															'name'  => 'header_content',
															'value' => '',
															'class' => 'form-control',
															'placeholder' => 'Header Content'
													);

													echo form_input($data);
													 echo "<small>".form_error('header_content')."</small>";
												?>
											 </div>
											 <div class="col-md-12 m-b-20">
                                                <?php 
													$data = array(
															'type'  => 'file',
															'name'  => 'header_image',
															'value' => '',
															'class' => 'form-control',
															'placeholder' => 'Header Image'
													);

													echo 'Header Image : '.form_input($data);
													 echo "<small>".form_error('header_image')."</small>";
												?>
											 </div>
											 <div class="col-md-12 m-b-20">
                                                <?php 
													$data = array(
															'type'  => 'file',
															'name'  => 'watermark_image',
															'value' => '',
															'class' => 'form-control',
															'placeholder' => 'Watermark Image'
													);

													echo 'Watermark Image : '.form_input($data);
													 echo "<small>".form_error('watermark_image')."</small>";
													 
												?>
												
											 </div>
											 <div class="col-md-12 m-b-20">
                                                <?php 
													$data = array(
															'type'  => 'text',
															'name'  => 'footer_content',
															'value' => '',
															'class' => 'form-control',
															'placeholder' => 'Footer Content'
													);

													echo form_input($data);
													 echo "<small>".form_error('footer_content')."</small>";
												?>
											 </div>
											 <div class="col-md-12 m-b-20">
											
                                                <input type="radio" name="paytype"  class="paytype"  value="Free" <?php echo set_radio('paytype', 'Free'); ?>>Free
												<input type="radio" name="paytype"   class="paytype" value="Paid" <?php echo set_radio('paytype', 'Paid'); ?>>Paid </div>
                                             
										<div id="agreement_paid" style="display:none">
                                           <div class="col-md-3 m-b-20">
                                                <?php 
													$data = array(
															'type'  => 'text',
															'name'  => 'amount',
															'value' => '',
															
															'class' => 'form-control',
															'placeholder' => 'Agreement Amount'
													);

													echo form_input($data);
													?>
												</div>
											<div class="col-md-3 m-b-20">												
												<?php	echo "<small>".form_error('form_name')."</small>";
													 
													
													 $options = array(
																''=>'Select Currency',
															'USD'         => 'USD',
															'INR'           => 'INR'
													);

													
													echo form_dropdown('currency', $options, '','class="form-control"');
												?>
												
											 </div>
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
	<script src="<?php echo base_url();?>assets/plugins/bower_components/tinymce/tinymce.min.js"></script>
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
	
	if ($("#mymce").length > 0) {
            tinymce.init({
                selector: "textarea#mymce",
                theme: "modern",
                height: 300,
                plugins: [
                    "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker", "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking", "save table contextmenu directionality emoticons template paste textcolor"
                ],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor emoticons",
            });
        }
});
$('.paytype').click(function(){
	//alert("hello");
	var paytype = $("input[name='paytype']:checked"). val();
	//alert(paytype);
	if(paytype=='Paid')
	{
		$('#agreement_paid').show();
	}
	if(paytype=='Free')
	{
		$('#agreement_paid').hide();
	}
});


</script> 

<script>
	
	var i =1;
	
	function insertComponent(compName)
	{
		i = i+1;
		
		var ed = tinymce.get('mymce');     
		
		var content = tinymce.get("mymce").getContent();
			
			if(compName=='Insert Signature Block')
		    {
				tinymce.activeEditor.execCommand('mceInsertContent', false, "<p><div contenteditable='false' class='sigDiv' id='sigId"+i+"' style='width:220px;height:40px;border:1px solid #eee; border-top:0' data-toggle='modal' data-target='#custom-width-modal' onclick='addplaceId(this.id)'>"+compName+"</div></p>");				
			}
			else if(compName=='Insert Text Box')
		    {
				tinymce.activeEditor.execCommand('mceInsertContent', false, "<p><input class='inner' type='text' id='textId"+i+"'  style='width: 300px;height: auto;border: 1px solid #eee;padding: 7px 10px;border-top: 0;' placeholder='Enter text value' /></p>");
			}
			else if(compName=='Insert Date Box')
		    {
				tinymce.activeEditor.execCommand('mceInsertContent', false, "<p><input class='datepickerWithoutTime' type='text' id='dateId"+i+"'  style='width:120px;height:20px;border:1px solid #eee;' placeholder='dd/mm/yyyy' /></p>");
			}
			else if(compName=='Insert Check Box')
		    {
				tinymce.activeEditor.execCommand('mceInsertContent', false, "<p><input class='inner' type='checkbox' id='dateId"+i+"' /></p>");
			}
			else
			{
				tinymce.activeEditor.execCommand('mceInsertContent', false, "<p><span class='inner' style='background:#57bb57;padding:2px 10px;border-radius:2px;font-size: 14px;color: #fff;float: left;margin-right: 5px;'>"+compName+"</span></p>");
				
				<!-- tinymce.get("editor").setContent(content+" "+"<span class='inner' style='background:#57bb57;padding:2px 10px;border-radius:2px;font-size: 14px;color: #fff;float: left;margin-right: 5px;'>"+compName+"</span>"); -->
			}			
	}
	

    function showSideOption()
	{
		if($('#sideTogle').css('display') == 'none')
		{
			$('#sideTogle').show();
		}
		else
		{
			$('#sideTogle').hide();
		}		
	}
	
	function demoTemplate()
	{
	    var agreeTemplate = $("#agreeTemplate").html();
		                                                
		tinymce.get("editor").setContent(agreeTemplate);
	}
		
	
	function showComp()
	{
		if($('input[name="customComp"]').is(':checked'))
		{
		  $("#compDiv").slideDown();
		}
		else
		{
		  $("#compDiv").slideUp();
		}		
	}
	
	
</script>

 