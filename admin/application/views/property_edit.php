owner_management<!DOCTYPE html>
<html lang="en">

<?php
$this->load->view('common/home_header');
$this->load->view('common/top_nav');
$this->load->view('common/leftsidebar');
?>
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
                        <h4 class="page-title">Property</h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <button class="right-side-toggle waves-effect waves-light btn-info btn-circle pull-right m-l-20"><i class="ti-settings text-white"></i></button>
                        
                        <ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
                            <li class="">Testimonial</li>
							<li class="active">Edit</li>
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
                                        
                                        <h3>Edit Property </h3> </div>
                                    <div class="clearfix"></div>
                                    <div class="scrollable">
									
                                       <form class="form-horizontal form-material" action="" method="post" id="myForm" name="myForm" onsubmit="return validateForm()">
                                                                      <?php foreach($data as $key => $val){?>
                                                                            <div class="form-group">
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input id="title" type="text" name="title" class="form-control" placeholder="Property Title" value="<?php echo $val->title;?>"><?php //echo form_error('name');?> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" name="address" class="form-control" placeholder="Address" value="<?php echo $val->address;?>"><?php //echo form_error('designation');?> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" name="state" class="form-control" placeholder="State" value="<?php echo $val->state;?>"><?php //echo form_error('designation');?> </div>   
                                                                                    <input type="hidden" name="hidden" value="<?php echo $val->id; ?>"> 
                                                                               <div class="col-md-12 m-b-20">
                                                                                    <input type="text" name="country" class="form-control" placeholder="Country" value="<?php echo $val->country;?>"><?php //echo form_error('designation');?> </div>    
                                                                               <div class="col-md-12 m-b-20">
                                                                                    <input type="text" name="type" class="form-control" placeholder="Type" value="<?php echo $val->property_type;?>"><?php //echo form_error('designation');?> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" name="status" class="form-control" placeholder="Status" value="<?php echo $val->property_status;?>"><?php //echo form_error('designation');?> </div>     
                                                                                <?php }?>
                                                                                
																			<div class="modal-footer">
                                                                        <button type="submit" class="btn btn-info waves-effect" id="save-button">Save</button>
                                                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
																		</div>
                                                                         </form>
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
<?php
$this->load->view('common/footer');
?>


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
    function validateForm() {
    var title = document.forms["myForm"]["title"].value;
    if (title == "") {
        alert("Title must be filled out");
        return false;
    }
    var address = document.forms["myForm"]["address"].value;
    if (address == "") {
        alert("Address must be filled out");
        return false;
    }
    var state = document.forms["myForm"]["state"].value;
    if (state == "") {
        alert("State must be filled out");
        return false;
    }
    var country = document.forms["myForm"]["country"].value;
    if (country == "") {
        alert("Country must be filled out");
        return false;
    }
    var type = document.forms["myForm"]["type"].value;
    if (type == "") {
        alert("Type must be filled out");
        return false;
    }
    var status = document.forms["myForm"]["status"].value;
    if (status == "") {
        alert("Status must be filled out");
        return false;
    }
    else{
        $('document').ready(function(){
            var data = $('#myForm').serialize();
            //alert(data);
       $.ajax({
        url:'<?php echo base_url();?>property/editProperty',
        type:'post',
        data:data,
        success:function(data){
            console.log(data);
            window.location.href = "<?php echo base_url();?>/property";
        },
       });
       return false;
   });
    }
}
    
</script>