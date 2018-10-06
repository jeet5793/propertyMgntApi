<div class="col-lg-12 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <h3 class="box-title">Property Manage</h3>
                            <!--<p class="text-muted m-b-30">Use default tab with class <code>customtab</code></p>-->
                            <!-- Nav tabs -->
                            <ul class="nav customtab nav-tabs" role="tablist">
                                <li role="presentation" class="active"><a href="#description" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> Property Description</span></a></li>
                                <li role="presentation" class=""><a href="#geo" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Geo Location</span></a></li>
                                <li role="presentation" class=""><a href="#detail" aria-controls="messages" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-email"></i></span> <span class="hidden-xs">Property Details</span></a></li>
                                <!--<li role="presentation" class=""><a href="#settings1" aria-controls="settings" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-settings"></i></span> <span class="hidden-xs">Settings</span></a></li>-->
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade active in" id="description">
                                    <!--<div class="col-md-6">
                                        <h3>Best Clean Tab ever</h3>
                                        <h4>you can use it with the small code</h4> </div>
                                    <div class="col-md-5 pull-right">
                                        <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a.</p>
                                    </div>-->
                                    <div class="clearfix"></div>
                                    <div class="tab-content br-n pn">
                                    <div id="navpills-1" class="tab-pane active">
                                        <div class="row">
                                            <?php 
											foreach($data as $datas){
												
												if($image!='')
													$img1 = base_url().$image->img_path;
												else $img1 = '';
											?>
                                            <div class="col-md-4"><img src="<?php echo $img1;?>" class="img-responsive thumbnail m-r-15"> </div>
                                            <div class="col-md-8"> <?php echo $datas->description;?>
                                                <p>
                                                    <br/> Reprehenderit butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terry richardson ex squid.</p>
                                            </div>
                                        <?php }?>
                                        </div>
                                    </div>
                                    <!--<div id="navpills-2" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-8"> Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.
                                                <p>
                                                    <br/> Reprehenderit butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terry richardson ex squid.</p>
                                            </div>
                                            <div class="col-md-4"> <img src="../plugins/images/large/img2.jpg" class="img-responsive thumbnail mr25"> </div>
                                        </div>
                                    </div>-->
                                    <div id="navpills-2" class="tab-pane">
                                        <div class="row">
                                            <div class="col-md-4"> <img src="../plugins/images/large/img3.jpg" class="img-responsive thumbnail mr25"> </div>
                                            <div class="col-md-8"> Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua, retro synth master cleanse. Mustache cliche tempor, williamsburg carles vegan helvetica.
                                                <p>
                                                    <br/> Reprehenderit butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terry richardson ex squid.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="geo">
                                    <div class="col-md-6">
                                        <h3>Geo Location</h3>
                                        <?php foreach($data as $datas){?>
                                            <!--<div class="col-md-12" contenteditable="true" onBlur="saveToGeolocation(this,'geo_location','<?php echo $datas->id;?>')" >
<?php echo $datas->geo_location; ?>
                                            
                                            </div>-->
                                            <div><?php echo $datas->geo_location; ?></div>
                                            <!--<p rows="4" cols="50" contenteditable="true" onBlur="saveToGeolocation(this,'geo_location','<?php echo $datas->id;?>')"><?php echo $datas->geo_location; ?></p>-->
                                        <?php }?>
                                        <div id="result-geo"></div>
                                        </div>

                                    <div class="col-md-5 pull-right">
                                        
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="detail">
                                    <div class="col-md-6">
                                        <table id="demo-foo-addrow" class="table m-t-30 table-hover contact-list" data-page-size="10">
                                            <tr>
                                                <th>Square Feet</th>
                                                <th>Bedroom</th>
                                                <th>Bathroom</th>
                                                <th>Total Amount</th>
                                                <th>Advance</th>
                                            </tr>
                                            <tr>
                                                <?php foreach($data as $datas){?>
                                                
                                                 <!--<td contenteditable="true" onBlur="saveToDatabase(this,'square_feet','<?php echo $datas->id;?>')" ><?php echo $datas->square_feet;?></td>-->
                                                 <td><?php echo $datas->square_feet;?></td>
                                                 <!--<td contenteditable="true" onBlur="saveToDatabase(this,'bedroom','<?php echo $datas->id;?>')" ><?php echo $datas->bedroom;?></td>-->
                                                 <td><?php echo $datas->bedroom;?></td>
                                                 <!--<td contenteditable="true" onBlur="saveToDatabase(this,'bathroom','<?php echo $datas->id;?>')" ><?php echo $datas->bathroom;?></td>-->
                                                 <td><?php echo $datas->bathroom;?></td>
                                                 <!--<td contenteditable="true" onBlur="saveToDatabase(this,'total_amount','<?php echo $datas->id;?>')" ><?php echo $datas->total_amount;?></td>-->
                                                 <td><?php echo $datas->total_amount;?></td>
                                                 <!--<td contenteditable="true" onBlur="saveToDatabase(this,'advance','<?php echo $datas->id;?>')" ><?php echo $datas->advance;?></td>-->
                                                 <td><?php echo $datas->advance;?></td>
                                                 
                                             <?php }?>
                                            </tr>

                                        </table>
                                        <div id="result"></div>
                                        </div>
                                    <div class="col-md-5 pull-right">
                                        <!--<p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a.</p>-->
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="settings1">
                                    <div class="col-md-6">
                                        <h3>Just do Settings</h3>
                                        <h4>you can use it with the small code</h4> </div>
                                    <div class="col-md-5 pull-right">
                                        <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a.</p>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script type="text/javascript">
   
function saveToDatabase(editableObj,column,id) {
    //alert(id);
    //$(editableObj).css("background","#FFF url(loaderIcon.gif) no-repeat right");
     $.ajax({
        url: "<?php echo base_url();?>/property/editPropertyProfile",
         type: "POST",
         data:'column='+column+'&editval='+editableObj.innerHTML+'&id='+id,
         dataType:'json',
         success: function(data){
             $(editableObj).css("background","#FDFDFD");
             if(data.error == 1){
                //alert(data.msg);
                //$('#result').html(data.msg);
                $('#result').fadeIn().html(data.msg);
                setTimeout(function(){
                    $('#result').fadeOut().html(data.msg);}, 1000);
             }
             else{
                $('#result').fadeIn().html(data.msg);
                setTimeout(function(){
                    $('#result').fadeOut().html(data.msg);}, 1000);
             }
             //alert(data);
         }        
    });
}

function saveToGeolocation(editableObj,column,id) {
    //alert(id);
    //$(editableObj).css("background","#FFF url(loaderIcon.gif) no-repeat right");
     $.ajax({
        url: "<?php echo base_url();?>/property/editGeoLocation",
         type: "POST",
         data:'column='+column+'&editval='+editableObj.innerHTML+'&id='+id,
         dataType:'json',
         success: function(data){
             $(editableObj).css("background","#FDFDFD");
             //alert(data);
             if(data.error == 1){
                //alert(data.msg);
                //$('#result').html(data.msg);
                $('#result-geo').fadeIn().html(data.msg);
                setTimeout(function(){
                    $('#result-geo').fadeOut().html(data.msg);}, 1000);
             }
             else{
                $('#result').fadeIn().html(data.msg);
                setTimeout(function(){
                    $('#result-geo').fadeOut().html(data.msg);}, 1000);
             }
             //alert(data);
         }        
    });
}

</script>