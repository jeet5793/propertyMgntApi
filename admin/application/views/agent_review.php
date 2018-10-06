
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
                        <h4 class="page-title">Agent Review</h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <button class="right-side-toggle waves-effect waves-light btn-info btn-circle pull-right m-l-20"><i class="ti-settings text-white"></i></button>
                        
                        <ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
							<li class="active">Agent Review</li>
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
                                            <input type="text" id="demo-input-search2" placeholder="search blog" class="form-control">
                                        </div>
                                        <h3>Agent Review</h3> </div>
                                    <div class="clearfix"></div>
                                    <div class="scrollable">
                                        <div class="table-responsive">
                                            <table id="demo-foo-addrow" class="table m-t-30 table-hover contact-list" data-page-size="10">
											<thead>
                                                   <tr>
                                                      <th>SNo</th>
                                                      <th>Agent Name</th>
													  <th>City</th>
                                                      <th>Rating</th>
                                                      
														<!--<th>Action</th>-->
                                                  </tr>
                                                </thead>
                                                <tbody>
												<?php 
												$i = 1;
												foreach($review as $rev){?>
													<tr>
														<td><?php echo $i;?></td>
														<td><?php echo $rev->first_name." ".$rev->last_name;?></td>
														<td><?php echo $rev->city;?></td>
														<td><?php echo ROUND($rev->rating,1);?></td>
													</tr>
												<?php $i++; } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
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
    
