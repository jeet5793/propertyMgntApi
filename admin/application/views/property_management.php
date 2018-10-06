
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
                        <h4 class="page-title">Property Management</h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <button class="right-side-toggle waves-effect waves-light btn-info btn-circle pull-right m-l-20"><i class="ti-settings text-white"></i></button>
                        
                        <ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
							<li class="active">Property Management</li>
                        </ol>
                    </div>
                </div>
                <!-- /.row -->
                <!-- row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="white-box">
                           
                            
                                <div class="">
                                    <div class="right-page-header">
                                        <div class="pull-right">
                                            <input type="text" id="demo-input-search2" placeholder="search owner" class="form-control">
                                        </div>
                                        <h3>Property List </h3> </div>
                                    <div class="clearfix"></div>
                                    <div class="scrollable">
                                        <div class="table-responsive">
                                           
                                            <table id="demo-foo-addrow" class="table m-t-30 table-hover contact-list" data-page-size="10">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Title</th>
                                                        <th>Address</th>
                                                        <th>City</th>
                                                        <th>State</th>
                                                        <th>Country</th>
                                                        <th>Type</th>
                                                        <th>Status</th>
                                                        <th>Zip Code</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                     $i = 1;
                                                     foreach($data as $key=>$val){
                                                         //if($val->owner_type == 1 OR $val->owner_type == 2)
                                                         {
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $i++;?></td>
                                                        <td>
                                                            <?php echo $val->title;?>
                                                        </td>
                                                        <td><?php echo $val->address;?></td>
                                                        <td><?php echo $val->city;?></td>
                                                       
                                                        <td><?php echo $val->state;?></td>
                                                        <td><?php echo $val->country;?></td>
                                                        <td><?php echo $val->property_type;?></td>
                                                        <td><?php echo $val->property_status;?></td>
                                                        <td><?php echo $val->zip_code;?></td>
                                                        
                                                        <td>
                                                            <!--<button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="Edit"><i class="ti-pencil" aria-hidden="true"></i></button>-->
                                                            <a href=""><button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="View" id="profile"  value="<?php echo $val->id; ?>"><i class="ti ti-user" aria-hidden="true" ></i></button></a>
                                                            <?php
                                                            if($val->status == 1){
                                                            ?>
                                                             <button type="button" id="stat" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="lock"   value="<?php echo $val->id; ?>" status=0><i class="ti ti-lock" aria-hidden="true"></i></button>
                                                             <?php }else{?>
                                                             <button type="button" id="stat" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="unlock"   value="<?php echo $val->id; ?>" status=1><i class="ti ti-unlock" aria-hidden="true"></i></button>
                                                            <?php }?>
                                                            <!--<a href="<?php echo base_url();?>property/deleteProperty/<?php echo $val->id;?>" ><button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="Delete"><i class="ti-close" aria-hidden="true"></i></button></a>
                                                            <a href="<?php echo base_url();?>property/editProperty"><button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="Edit"><i class="ti-pencil" aria-hidden="true" id="edit-data"></i></button></a>-->
                                                            

                                                        </td>
                                                        
                                                    </tr>
                                                <?php }} ?>
                                                    
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                       <?php /* <td colspan="2">
                                                            <button type="button" class="btn btn-info btn-rounded" data-toggle="modal" data-target="#add-contact">Add New Contact</button>
                                                        </td>
                                                        <div id="add-contact" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                                        <h4 class="modal-title" id="myModalLabel">Add New Contact</h4> </div>
                                                                    <div class="modal-body">
                                                                        <from class="form-horizontal form-material">
                                                                            <div class="form-group">
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" class="form-control" placeholder="Type name"> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" class="form-control" placeholder="Email"> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" class="form-control" placeholder="Phone"> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" class="form-control" placeholder="Designation"> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" class="form-control" placeholder="Age"> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" class="form-control" placeholder="Date of joining"> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <input type="text" class="form-control" placeholder="Salary"> </div>
                                                                                <div class="col-md-12 m-b-20">
                                                                                    <div class="fileupload btn btn-danger btn-rounded waves-effect waves-light"><span><i class="ion-upload m-r-5"></i>Upload Contact Image</span>
                                                                                        <input type="file" class="upload"> </div>
                                                                                </div>
                                                                            </div>
                                                                        </from>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Save</button>
                                                                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                                <!-- /.modal-content -->
                                                            </div>
                                                            <!-- /.modal-dialog -->
                                                        </div>*/?>
                                                        <td colspan="7">
                                                            <div class="text-right">
                                                                <ul class="pagination"> </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                    
                                </div>
                                <!-- .left-aside-column-->
                            </div>
                            <!-- /.left-right-aside-column-->
                            <div id="view_profile"></div>
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
                <!-- .row -->
              </div>  
            <!-- /.container-fluid -->
           
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript">
        // $('document').ready(function(){
        //     $('button #edit-data').on('click',function(){
        //         alert('hi');
        //     });
        // });
    </script>
    <script type="text/javascript">
             $('document').ready(function(){
                $('#demo-input-search2').on('keyup',function(){
                    var searchTerm = $(this).val().toLowerCase();
                    $('#demo-foo-addrow tbody tr').each(function(){
                        var lineStr = $(this).text().toLowerCase();
                        if(lineStr.indexOf(searchTerm) === -1)
                        {
                            $(this).hide();
                        }
                        else{
                            $(this).show();
                        }
                    });
                });
             });
         </script>
<script type="text/javascript">
        $('document').ready(function(){
            $('button#profile').click(function(){
                $('html, body').animate({
                scrollTop: $(this).offset().top
                }, 1000);
                
                var data = 'id='+ $(this).val();
                  
                 $.ajax(
     {
        url: "<?php echo base_url();?>property/propertyProfile",
        type: 'POST',
        data:data,
        success:function(data){
            $('#view_profile').html(data);
            //alert(data);
        }
        
     });
                 return false;
            });

           
        });
        

    </script>
    <script type="text/javascript">
        $('dicument').ready(function(){
            $('button#stat').on('click',function(){

                var status = $(this).attr('status');
                var id = $(this).attr('value');
                
                $.ajax({
                    url:'<?php echo base_url()?>property/propertyStatus',
                    type:'post',
                    data:{'status' : status, 'id' : id},
                    success:function(data){
                        //console.log(data);
                        //alert(data);
                        window.location.href="<?php echo base_url();?>property";
                    },
                });
            });
        });
    </script>
