
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
                        <h4 class="page-title">Advertisement</h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <button class="right-side-toggle waves-effect waves-light btn-info btn-circle pull-right m-l-20"><i class="ti-settings text-white"></i></button>
                        
                        <ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
							<li class="active">Advertisement</li>
                        </ol>
                    </div>
                </div>
                <!-- /.row -->
                <!-- .row -->
              </div>  
            <!-- /.container-fluid -->
            <div class="row">
                    <div class="col-md-12">
                        <div class="white-box">
                           
                            
                                <div class="">
                                    <div class="right-page-header">
                                        <div class="pull-right">
                                            <input type="text" id="demo-input-search2" placeholder="search advertisement" class="form-control">
                                        </div>
                                        <h3>Advertisement List </h3> </div>
                                    <div class="clearfix"></div>
                                    <div class="scrollable">
                                        <div class="table-responsive">
                                            <table id="demo-foo-addrow" class="table m-t-30 table-hover contact-list" data-page-size="10">
											<thead>
                                                    <tr>
                                                        <td colspan="2">
                                                            <a href="<?php echo base_url();?>advertisement/add"><button type="button" class="btn btn-info btn-rounded" data-toggle="modal">Add New Advertisement</button></a>
                                                        </td><?php /*data-target="#add-contact"*/?>
                                                      
                                                            <!-- /.modal-dialog -->
                                                        </div>
                                                       
                                                    </tr>
                                                
                                                
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Name</th>
                                                       
                                                        <th>Image</th>
														<th>Start Date</th>
														<th>End Date</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
												
												<?php 
												$i=1;
												foreach($data as $key=>$val){?>
                                                    <tr>
                                                        <td><?php echo $i;?></td>
                                                        <td>
                                                           <?php echo $val->name;?>
                                                        </td>
                                                       
                                                        <td><img src="<?php echo base_url().$val->img_path;?>"></td>
                                                       
                                                        <td><?php echo date('d-m-Y',strtotime($data[0]->start_date));?></td>
														 <td><?php echo date('d-m-Y',strtotime($data[0]->end_date));?></td>
                                                        <td><?php echo $val->status==0?'Inactive':'Active';?></td>
                                                        
                                                        <td>
														<?php /*<a href="<?php echo base_url();?>testimonial/view/<?php echo $val->id;?>"><button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="View"><i class="ti-eye" aria-hidden="true"></i></button></a>*/?>
														<a href="<?php echo base_url();?>advertisement/edit/<?php echo $val->id;?>"><button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="Edit"><i class="ti-pencil" aria-hidden="true"></i></button></a>
                                                        <a href="<?php echo base_url();?>advertisement/delete/<?php echo $val->id;?>"><button type="button" class="btn btn-sm btn-icon btn-pure btn-outline delete-row-btn" data-toggle="tooltip" data-original-title="Delete"><i class="ti-close" aria-hidden="true"></i></button></a>
                                                        </td>
                                                    </tr>
												<?php $i++; }?>  
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
                        </div>
                    </div>
                </div>
                <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
    
