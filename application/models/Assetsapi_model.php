<?php
class Assetsapi_model extends CI_Model {
	
	/* Login Model Start */
	public function login($data = array())
    {
		$email = $data['email'];
		
		$password = $data['password'];
		$assets_type = $data['assets_type'];
		if($email){
			$sqlcont="SELECT * FROM registration_tb WHERE (email = '$email' AND assets_type='$assets_type') AND password='$password'";
			$query =$this->db->query($sqlcont);
			$result=$query->result_array();
			if($result){
				$assets_id = $result[0]['assets_id'];
				$assetsType = $result[0]['assets_type'];
				$profile_photo = $result[0]['profile_photo'];
				$email = $result[0]['email'];
				$status = $result[0]['status'];
				if($assetsType==2)
				{	$agentTypeID = $result[0]['agent_type'];
					$agentType = $this->getAgentType($agentTypeID);
				}else{
					$agentType = '';
				}
				if($result[0]['plan_id']>0)
				{
					$plan_id = $result[0]['plan_id'];
					$planName = $this->getPlanName($plan_id);
					$this->db->select('expire_date');
					$this->db->from('upgrade_plan_log_tb');
					$this->db->where('assets_id',$assets_id);
					$this->db->where('plan_id',$plan_id);
					$this->db->order_by('upgrade_date','DESC');
					$this->db->limit(1);
					$queryPlanInfn = $this->db->get();
					$planInfoArr = $queryPlanInfn->result_array();
					if(count($planInfoArr)>0){
						$expireDate=date('d-m-Y',strtotime($planInfoArr[0]['expire_date']));
					}else{
						$expireDate='';
					}
					
					
				}else{
					$plan_id = '';
					$planName = '';
					$expireDate='';
				}
				$assets_type = $this->getAssetsType($assetsType);
				$session_id = session_id();
				$this->db->update('registration_tb',array('session_id'=>$session_id),array('assets_id'=>$assets_id));
				$this->session->set_userdata('session_id', $session_id);
				$sess_data = array('assets_id'=>$assets_id,'assets_type'=>$assets_type,'plan_id'=>$plan_id,'assetsTypeId'=>$assetsType,'session_id'=>$session_id,'agentType'=>$agentType,'planName'=>$planName,'profile_photo'=>$profile_photo,'email'=>$email,'status'=>$status,'expireDate'=>$expireDate);
				//$this->session->set_userdata($sess_data);
				return $sess_data;
			}else{
				return false;
			}
		}else{
			return false;
		}
    }
	public function another_type_login($data = array()){
		$email = $data['email'];
		
		$password = $data['password'];
		$assets_type = $data['assets_type'];
		if($email){
			$sqlcont="SELECT * FROM registration_tb WHERE email = '$email'  AND password='$password'";
			$query =$this->db->query($sqlcont);
			$result=$query->result_array();
			if($result){
				$Queryresult = $result[0]; 
						$owner_type	=	$Queryresult['owner_type']!=''?$Queryresult['owner_type']:'';
						$first_name	=	$Queryresult['first_name']!=''?$Queryresult['first_name']:'';				
						$last_name	=	$Queryresult['last_name']!=''?$Queryresult['last_name']:'';				
						$email		=	$Queryresult['email']!=''?$Queryresult['email']:'';				
						$password	=	$Queryresult['password']!=''?$this->encrypt_decrypt('decrypt',$Queryresult['password']):'';				
						$city		=	$Queryresult['city']!=''?$Queryresult['city']:'';				
						$state		=	$Queryresult['state']!=''?$Queryresult['state']:'';				
						$country	=	$Queryresult['country']!=''?$Queryresult['country']:'';				
						$zip_code	=	$Queryresult['zip_code']!=''?$Queryresult['zip_code']:'';				
						$mobile_no	=	$Queryresult['mobile_no']!=''?$Queryresult['mobile_no']:'';				
						$landline_no	=	$Queryresult['landline_no']!=''?$Queryresult['landline_no']:'';				
						$company_name	=	$Queryresult['company_name']!=''?$Queryresult['company_name']:'';				
						$website_url	=	$Queryresult['website_url']!=''?$Queryresult['website_url']:'';				
						$agent_type	=	$Queryresult['agent_type']!=''?$Queryresult['agent_type']:'';				
								
						$randNumber = mt_rand(100000, 999999);
						$NewData = array(
						'owner_type' =>$owner_type ,
						'first_name' => $first_name,
						'last_name' => $last_name,
						'email' => $email,
						'password' => $password,
						'city' => $city,
						'state' => $state,
						'country' => $country,
						'zip_code' => $zip_code,
						'mobile_no' => $mobile_no,
						'landline_no' => $landline_no,
						'assets_type' => $assets_type,
						'company_name' => $company_name,
						'website_url' => $website_url,
						'agent_type' =>$agent_type,
						'account_id' =>$randNumber
					);
				return $NewData;
			}else{
				return false;
			}
		}else{
			return false;
		}
			
	}
	/* Login Model End */
	public function getPlanName($plan_id){
		$query = $this->db->get_where('plan_tb',array('id'=>$plan_id));
		$data = $query->result_array();
		return $data[0]['plan'];
	}
	public function getAssetsType($assetsType)
	{
		if($assetsType==1)
		{
			$assets_type = 'Owner';
		}elseif($assetsType==2){
			$assets_type = 'Agent';
		}elseif($assetsType==3){
			$assets_type = 'Tenant';
		}elseif($assetsType==4){
			$assets_type = 'Employee';
		}
		elseif($assetsType==5){
			$assets_type ='Admin' ;
		}
		return $assets_type;
	}
	public function getAgentType($agentTypeID)
	{
		if($agentTypeID==1)
		{
			return $agent_type = 'Service Provider';
		}elseif($agentTypeID==2){
			return $agent_type = 'Broker';
		}
		 
	}
	/* Register Model Start */
	public function register($data = array())
    {
		$reg_insert = $this->db->insert('registration_tb',$data);
		if($reg_insert){
			$assets_id = $this->db->insert_id();
			 $userData = $this->profile($assets_id);
			 $assetsType = $userData['assets_type'];
			
			 $assets_type = $this->getAssetsType($assetsType);
				if($assetsType==2)
				{	 $agent_typeid = $userData['agent_type'];
					$agentTyped= $this->getAgentType($agent_typeid);
				}else{
					$agentTyped = '';
				}
			 $sess_data = array('assets_id'=>$assets_id,'assets_type'=>$assets_type,'agentType'=>$agentTyped);

            $this->session->set_userdata($sess_data);
			return $sess_data;
		}else{
			return false;
		}
    }
	/* Register Model End */
	
	/* Testimonial Model Start */
	public function testimonial()
    {
		$array = array('status' => '1');
		$this->db->select("id,name,img_path,message,designation");
		$this->db->from('testimonial_tb');
		$this->db->where($array);
		$query = $this->db->get();
		return $query->result_array();
    }
	/* Testimonial Model End */
	
	/* Blog Model Start */
	public function blog()
    {
		$array = array('b.status' => '1');
		$this->db->select("b.id,b.name as blog_name,c.name as commenter,img_path,description,views_count,b.entry_date,COUNT(c.blog_id) as comments_count");
		$this->db->from('blog_tb b');
		$this->db->join("blog_comment_tb c","b.id=c.blog_id",'left');
		$this->db->where($array);
		$this->db->group_by('c.blog_id');
		$query = $this->db->get();
		return $query->result_array();
    }
	
	public function blog_details($id)
    {
		$array = array('b.id' => $id);
		$this->db->select("b.id,b.name as blog_name,c.name as commenter,img_path,description,views_count,b.entry_date,COUNT(c.blog_id) as comments_count");
		$this->db->from('blog_tb b');
		$this->db->join("blog_comment_tb c","b.id=c.blog_id",'left');
		$this->db->where($array);
		$query = $this->db->get();
		return $query->result_array();
    }
	
	public function blog_views_update($blog_id)
    {
		$this->db->set('views_count', 'views_count+1', FALSE);
		$this->db->where('id', $blog_id);
		$view_update = $this->db->update('blog_tb');
		if($view_update){
			$blog = $this->blog_details($blog_id);
			return $blog;
		} else{
			return false;
		}
    }
	
	public function blog_comment_tb($blog_id)
    {
		$array = array('blog_id' => $blog_id);
		$this->db->select("*");
		$this->db->from('blog_comment_tb');
		$this->db->where($array);
		$query = $this->db->get();
		return $query->result_array();
    }
	
	public function blog_comment_insert($data = array())
    {
		$cmt_insert = $this->db->insert('blog_comment_tb',$data);
		$blog_id = $data['blog_id'];
		if($cmt_insert){
			$blog_cmt = $this->blog_comment_tb($blog_id);
			return $blog_cmt;
		} else{
			return false;
		}
    }
	/* Blog Model End */
	
	/* Advertisement Model Start */
	public function advertisement()
    {
		$array = array('status' => '1');
		$this->db->select("id,name,img_path,start_date,end_date");
		$this->db->from('advertisement_tb');
		$this->db->where($array);
		$query = $this->db->get();
		return $query->result_array();
    }
	/* Advertisement Model End */
	
	/* Property Model Start */
	public function add_property($request = array())
    {
		// return $request;
		$propertyData = array(
			'owner_id' => $request['owner_id'],
			'title' => $request['title'],
			'address' => $request['address'],
			'address2' => $request['address2'],
			'city' => $request['city'],
			'state' => $request['state'],
			'country' => $request['country'],
			'zip_code' => $request['zip_code'],
			'property_type' => $request['property_type'],
			'property_status' => $request['property_status'],
			'description' => $request['description'],
			'geo_location' => $request['geo_location'],
			'square_feet' => $request['square_feet'],
			'bedroom' => $request['bedroom'],
			'bathroom' => $request['bathroom'],
			'total_amount' => $request['total_amount'],
			'advance' => $request['advance'],
			'agent_perc'=>$request['agent_perc']
			
		);
		$property_insert = $this->db->insert('property_tb',$propertyData);
		$id = $this->db->insert_id();
		$userData = $this->profile($request['owner_id']);
		$assetstype = $this->getAssetsType($userData['assets_type']);

		if($property_insert){
			
			if(isset($request['owner_details']) && ($request['owner_details'])!='')
			{
				for($i=0;$i<count($request['owner_details']);$i++){
					$propertyOwnerData = array(
						'property_id' => $id,
						'owner_name' => $request['owner_details'][$i]['owner_name'],
						'address' => $request['owner_details'][$i]['address'],
						'address2' => $request['owner_details'][$i]['address2'],
						'city' => $request['owner_details'][$i]['city'],
						'state' => $request['owner_details'][$i]['state'],
						'country' => $request['owner_details'][$i]['country'],
						'pin_code' => $request['owner_details'][$i]['zip_code'],
						
					);
					$owner_insert = $this->db->insert('property_owner_tb',$propertyOwnerData);
				}
			}
		if(isset($request['img_path']) && ($request['img_path'])!='')
		{
			for($j=0;$j<count($request['img_path']);$j++){
				$imgData = $request['img_path'][$j];
				$img1 = str_replace('data:image/jpeg;base64,', '', $imgData);
				$img = str_replace('data:image/png;base64,', '', $img1);
				$image = base64_decode($img);
				$image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
				$filename = $image_name . '.' . 'png';

				$path = 'assets/'.$assetstype.'/'.$request['owner_id'].'/Property/';
				if (!file_exists($path)) {
					$folderPath = mkdir($path, 0777, true);
				}
				else{
						$folderPath = $path;
					}
		
			$targetPath = $path.$filename;
			file_put_contents($targetPath, $image);
				$propertyPhotoData = array(
					'property_id' => $id,
					'img_path' => $targetPath
				);
				$photo_insert = $this->db->insert('property_photo_tb',$propertyPhotoData);
			}
		}
			$result = $this->assetsapi_model->property();
			return $result;
		} else{
			return false;
		}
    }
	
	public function edit_property($request = array())
    {
		$propertyData = array(
			'owner_id' => $request['owner_id'],
			'title' => $request['title'],
			'address' => $request['address'],
			'city' => $request['city'],
			'state' => $request['state'],
			'country' => $request['country'],
			'zip_code' => $request['zip_code'],
			'property_type' => $request['property_type'],
			'property_status' => $request['property_status'],
			'description' => $request['description'],
			'geo_location' => $request['geo_location'],
			'square_feet' => $request['square_feet'],
			'bedroom' => $request['bedroom'],
			'bathroom' => $request['bathroom'],
			'total_amount' => $request['total_amount'],
			'advance' => $request['advance'],
			'agent_perc'=>$request['agent_perc'],
			'status' => 1,
		);
		$this->db->where('id', $request['property_id']);
		$property_update = $this->db->update('property_tb',$propertyData);
		$userData = $this->profile($request['owner_id']);
		$assetstype = $this->getAssetsType($userData['assets_type']);
		if($property_update){
			
			$this->db->delete('property_owner_tb', array('property_id' => $request['property_id'])); 
			if(count($request['owner_details'])>0){
				for($i=0;$i<count($request['owner_details']);$i++){
					$propertyOwnerData = array(
						'property_id' => $request['property_id'],
						'owner_name' => $request['owner_details'][$i]['owner_name'],
						'address' => $request['owner_details'][$i]['address'],
						'city' => $request['owner_details'][$i]['city'],
						'state' => $request['owner_details'][$i]['state'],
						'country' => $request['owner_details'][$i]['country'],
						'pin_code' => $request['owner_details'][$i]['zip_code']
					);
					$owner_insert = $this->db->insert('property_owner_tb',$propertyOwnerData);
				}
			}

			
			
		if(isset($request['img_path']) && ($request['img_path'])!='')
		{
			$this->db->delete('property_photo_tb', array('property_id' => $request['property_id'])); 
			for($j=0;$j<count($request['img_path']);$j++){
				$imgData = $request['img_path'][$j];
				$img1 = str_replace('data:image/jpeg;base64,', '', $imgData);
				$img = str_replace('data:image/png;base64,', '', $img1);
				$image = base64_decode($img);
				$image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
				$filename = $image_name . '.' . 'png';

				$path = 'assets/'.$assetstype.'/'.$request['owner_id'].'/Property/';
				if (!file_exists($path)) {
					$folderPath = mkdir($path, 0777, true);
				}
				else{
						$folderPath = $path;
					}
		
			$targetPath = $path.$filename;
			file_put_contents($targetPath, $image);
				$propertyPhotoData = array(
					'property_id' => $request['property_id'],
					'img_path' => $targetPath
				);
				$photo_insert = $this->db->insert('property_photo_tb',$propertyPhotoData);
			}
		}
			$result = $this->assetsapi_model->property_details($request['property_id']);
			return $result;
		} else{
			return false;
		}
    }
	
	public function delete_property($request = array())
    {
		$property_delete = $this->db->delete('property_tb', array('id' => $request['property_id'])); 
		if($property_delete){
			$property_delete1 = $this->db->delete('property_owner_tb', array('property_id' => $request['property_id'])); 
			$property_delete2 = $this->db->delete('property_photo_tb', array('property_id' => $request['property_id'])); 
			return true;
		} else{
			return false;
		}
	}
	
	public function property()
    {		
		$array = array('status' => '1');
		$property_status = array('Rent' ,'Sale');
		$this->db->select("*");
		$this->db->from('property_tb');
		$this->db->where($array);
		$this->db->where_in('property_status',$property_status);
		$query = $this->db->get();
		$property_det = $query->result_array();
		$property = array();
		foreach($property_det as $prop){
			$property_id = $prop['id'];
			$array1 = array('property_id' => $property_id);
			$this->db->select("*");
			$this->db->from('property_owner_tb');
			$this->db->where($array1);
			$query1 = $this->db->get();
			$property_owner = $query1->result_array();
			
			$array2 = array('property_id' => $property_id);
			$this->db->select("*");
			$this->db->from('property_photo_tb');
			$this->db->where($array2);
			$query2 = $this->db->get();
			$property_img = $query2->result_array();
			
			$property[] = array(
				'id'=>$prop['id'],
				'owner_id'=>$prop['owner_id'],
				'title'=>strip_tags($prop['title']),
				'description'=>strip_tags($prop['description']),
				'address'=>$prop['address'],
				'city'=>$prop['city'],
				'state'=>$prop['state'],
				'country'=>$prop['country'],
				'zip_code'=>$prop['zip_code'],
				'property_type'=>$prop['property_type'],
				'property_status'=>$prop['property_status'],
				'square_feet'=>$prop['square_feet'],
				'total_amount'=>$prop['total_amount'],
				'advance'=>$prop['advance'],
				'geo_location'=>$prop['geo_location'],
				'owner_details'=>$property_owner,
				'img_path'=>$property_img
			);
		}
		return $property;
    }
	
	public function property_details($id)
    {
		$array = array('id' => $id);
		$this->db->select("*");
		$this->db->from('property_tb');
		$this->db->where($array);
		$query = $this->db->get();
		$property_det = $query->result_array();
		$property = array();
		foreach($property_det as $prop){
			$property_id = $prop['id'];
			$array1 = array('property_id' => $property_id);
			$this->db->select("*");
			$this->db->from('property_owner_tb');
			$this->db->where($array1);
			$query1 = $this->db->get();
			$property_owner = $query1->result_array();
			
			$array2 = array('property_id' => $property_id);
			$this->db->select("*");
			$this->db->from('property_photo_tb');
			$this->db->where($array2);
			$query2 = $this->db->get();
			$property_img = $query2->result_array();
			
			$property[] = array(
				'id'=>$prop['id'],
				'owner_id'=>$prop['owner_id'],
				'title'=>strip_tags($prop['title']),
				'description'=>strip_tags($prop['description']),
				'address'=>$prop['address'],
				'city'=>$prop['city'],
				'state'=>$prop['state'],
				'country'=>$prop['country'],
				'zip_code'=>$prop['zip_code'],
				'property_type'=>$prop['property_type'],
				'property_status'=>$prop['property_status'],
				'geo_location'=>$prop['geo_location'],
				'owner_details'=>$property_owner,
				'img_path'=>$property_img
			);
		}
		return $property;
    }
	/* Property Model End */
	
	/* Property Search start*/
	 
	public function property_search($request = array())
    {
		/*$propertyData = array(
			//'owner_id' => $request['keyword'],
			'title' => $request['address'],
			'address' => $request['property_category'],
			'city' => $request['property_type'],
			'state' => $request['area'],
			'country' => $request['min_price'],
			'zip_code' => $request['max_price'],
			'status' => 1
		);*/
		 if($request['keyword'])
		 {
			 $this->db->like('title', $request['keyword']);
		 }
		if($request['city'])
		{
			$this->db->where('city', $request['city']);
		}
		if($request['property_type'])
		{
			$this->db->where('property_type', $request['property_type']);
		}	
		if($request['property_status'])
		{
			$this->db->where('property_status', $request['property_status']);
		}
		if($request['area'])
		{
			$this->db->where('square_feet', $request['area']);
		}
		if($request['min_price'])
		{
			$this->db->where('total_amount >=', $request['min_price']);
		}	
		if($request['max_price'])
		{
			$this->db->where('total_amount <=', $request['max_price']);
		}
		
			$this->db->select("*");
			$this->db->from('property_tb');
			$this->db->where('status', 1);
			$query = $this->db->get();
			$property_data = $query->result_array();
		if($property_data)
		{
			return $property_data;
		} else{
			return false;
		}
    }
	 
	 /* Property Search End*/
	 
	 
	 //=====================================property by user==================================================
	 public function property_by($id)
	 {
		 $array = array('status' => '1','owner_id'=>$id);
		$this->db->select("*");
		$this->db->from('property_tb');
		$this->db->where($array);
		$query = $this->db->get();
		$property_det = $query->result_array();
		$property = array();
		foreach($property_det as $prop){
			$property_id = $prop['id'];
			$array1 = array('property_id' => $property_id);
			$this->db->select("*");
			$this->db->from('property_owner_tb');
			$this->db->where($array1);
			$query1 = $this->db->get();
			$property_owner = $query1->result_array();
			
			$array2 = array('property_id' => $property_id);
			$this->db->select("*");
			$this->db->from('property_photo_tb');
			$this->db->where($array2);
			$query2 = $this->db->get();
			$property_img = $query2->result_array();
			
			$property[] = array(
				'id'=>$prop['id'],
				'owner_id'=>$prop['owner_id'],
				'title'=>strip_tags($prop['title']),
				'description'=>strip_tags($prop['description']),
				'address'=>$prop['address'],
				'address2'=>$prop['address2'],
				'city'=>$prop['city'],
				'state'=>$prop['state'],
				'country'=>$prop['country'],
				'zip_code'=>$prop['zip_code'],
				'property_type'=>$prop['property_type'],
				'property_status'=>$prop['property_status'],
				'square_feet'=>$prop['square_feet'],
				'total_amount'=>$prop['total_amount'],
				'advance'=>$prop['advance'],
				'geo_location'=>$prop['geo_location'],
				'owner_details'=>$property_owner,
				'img_path'=>$property_img,
				'bedroom'=>$prop['bedroom'],
				'bathroom'=>$prop['bathroom']
			);
		}
		return $property;
	 }
	 
	 
	 /* Statics Count Start */
	public function statics_count()
    {//select (select count(*) from registration_tb where assets_type=2) as Agent,(select count(*) from registration_tb where assets_type=3) as Tenant,(select count(*) from property_tb) as property from registration_tb
			//$this->db->select("SELECT count(*) from registration_tb where assets_type=2) as Agent",FALSE);
			//$this->db->select("SELECT count(*) from registration_tb where assets_type=3) as Tenant",FALSE);
			//$this->db->select("SELECT count(*) from property_tb) as Property",FALSE);

			//$this->db->select("*");
			//$this->db->from('registration_tb');
			//$this->db->where('status', 1);
			//$query = $this->db->get();
			//$sql = "select (select count(*) from registration_tb where assets_type=1) as Owner,(select count(*) from registration_tb where assets_type=2) as Agent,(select count(*) from registration_tb where assets_type=3) as Tenant,(select count(*) from property_tb) as property,(select count(*) from registration_tb where assets_type=5) as Admin from registration_tb group by assets_type";
			$sql ="SELECT (SELECT COUNT(*)  FROM   registration_tb group by assets_type having assets_type=1) as Owner,(SELECT COUNT(*)  FROM   registration_tb group by assets_type having assets_type=2) as Agent,(SELECT COUNT(*)  FROM   registration_tb group by assets_type having assets_type=3) as Tenant,(SELECT COUNT(*)  FROM   property_tb) as Property,(SELECT COUNT(*)  FROM  signing_deal_tb where status=1) as Deal";
			
			
			$query=$this->db->query($sql);
			$statics = $query->result_array();
		if($statics)
		{
			return $statics;
		} else{
			return false;
		}
	}
	
	public function property_status($request = array())
    {		
		$array = array('status' => '1');
		$this->db->select("*");
		$this->db->from('property_tb');
		$this->db->where($array);
		$this->db->where('property_status', $request['property_status']);
		$query = $this->db->get();
		$property_det = $query->result_array();
		$property = array();
		foreach($property_det as $prop){
			$property_id = $prop['id'];
			$array1 = array('property_id' => $property_id);
			$this->db->select("*");
			$this->db->from('property_owner_tb');
			$this->db->where($array1);
			$query1 = $this->db->get();
			$property_owner = $query1->result_array();
			
			$array2 = array('property_id' => $property_id);
			$this->db->select("*");
			$this->db->from('property_photo_tb');
			$this->db->where($array2);
			$query2 = $this->db->get();
			$property_img = $query2->result_array();
			
			$property[] = array(
				'id'=>$prop['id'],
				'owner_id'=>$prop['owner_id'],
				'title'=>strip_tags($prop['title']),
				'description'=>strip_tags($prop['description']),
				'address'=>$prop['address'],
				'city'=>$prop['city'],
				'state'=>$prop['state'],
				'country'=>$prop['country'],
				'zip_code'=>$prop['zip_code'],
				'property_type'=>$prop['property_type'],
				'property_status'=>$prop['property_status'],
				'geo_location'=>$prop['geo_location'],
				'owner_details'=>$property_owner,
				'img_path'=>$property_img
			);
		}
		return $property;
    }
	
	public function our_agent()
    {
		$sql = "SELECT registration_tb.* FROM registration_tb WHERE assets_type=2 LIMIT 3";
		
			$query=$this->db->query($sql);
			$agent = $query->result_array();
		if($agent)
		{
			return $agent;
		} else{
			return false;
		}
	}
	
	public function contact($request = array())
    {
		$contactData = array(
			'name' => $request['name'],
			'email' => $request['email'],
			'phone' => $request['phone'],
			'subject' => $request['subject'],
			'message' => $request['message']
				
		);
		$from_email = $request['email'];
        $to_email = "jeet.mishra57@gmail.com,kesavan.p@jirehsol.com,kesavankesavan747@gmail.com";
        //Load email library
        $this->load->library('email');
        $this->email->from($from_email, 'Assets Watch');
        $this->email->to($to_email);
        $this->email->subject($request['subject']);
        $this->email->message($request['message']);
        //Send mail
        if($this->email->send())
		{
            return "Your contact information submitted successfully.";
		}
        else
		{
            return "You have encountered an error";
        }
	}
	
	/* Profile  Start*/
	public function profile($id)
    {
			$query = $this->db->get_where('registration_tb', array('assets_id' => $id));
			$result = $query->result_array();
			
			if(count($result)>0)
			 {
					
				$SSN_EIN = $result[0]['SSN_EIN'];
				$SSN = $this->encrypt_decrypt('decrypt',$SSN_EIN);
				$pass = $result[0]['password'];
				$password = $this->encrypt_decrypt('decrypt',$pass);
				 $dob = date('d-m-Y',strtotime($result[0]['DOB']));
				if($result[0]['plan_id']>0)
				{
					$plan = $result[0]['plan_id'];
					$planName = $this->getPlanName($plan);
				}else{
					$plan = '';
					$planName = '';
				}
				 $userData = array(
						'assets_id'=>$result[0]['assets_id'],
						'first_name' => $result[0]['first_name'],
						'last_name' => $result[0]['last_name'],
						'email' => $result[0]['email'],
						'city' =>$result[0]['city'],
						'state' => $result[0]['state'],
						'country' => $result[0]['country'],
						'zip_code' => $result[0]['zip_code'],
						'mobile_no' => $result[0]['mobile_no'],
						'landline_no' => $result[0]['landline_no'],
						'assets_type' => $result[0]['assets_type'],
						'agent_type' => $result[0]['agent_type'],
						'owner_type'=>$result[0]['owner_type'],
						'profile_photo' => $result[0]['profile_photo'],
						'about_us' => $result[0]['about_us'],
						'facebook_link' => $result[0]['facebook_link'],
						'twitter_link' =>$result[0]['twitter_link'],
						'linkedin_link'=>$result[0]['linkedin_link'],
						'SSN_EIN'=>$SSN,
						'dob'=>$dob,
						'gender'=>$result[0]['gender'],
						'plan_id'=>$plan,
						'planName'=>$planName,
						'password'=>$password
				 );
				 return $userData;
			 }else{
				return False;
			}
			
	}
	public function statics_count_by($id)
    {
		$this->db->select('assets_type'); 
		$this->db->from('registration_tb');   
		$this->db->where('assets_id', $id);
		$rslt = $this->db->get()->result_array();
		$user_assets_type = $rslt[0]['assets_type'];
		// ============================== for Owner ===================================================//
		if($user_assets_type==1)
		{
			$subsql = "SELECT (SELECT count(*) from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."')) as p where assetsType=2 and request_status='1') as Agent,(SELECT count(*) from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."')) as p where assetsType=3 and request_status='1') as Tenant,(select count(*) from property_tb group by owner_id having owner_id=$id)  as Property,(select count(*) from agreement_tb group by user_id having user_id=$id)  as Agreement ";
		}else if($user_assets_type==3){
			$subsql = "SELECT (SELECT count(*) from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."')) as p where assetsType=2 and request_status='1') as Agent, (SELECT count(*) from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."')) as p where assetsType=1 and request_status='1') as Owner,(select COUNT(*) from (Select * from property_deal_tb where (sender_id='".$id."' or receiver_id='".$id."') and status='Completed' group by property_id) as prop)  as Property";
		}else if($user_assets_type==2){
			$subsql="SELECT (SELECT count(*) from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."')) as p where assetsType=3 and request_status='1') as Tenant, (SELECT count(*) from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."')) as p where assetsType=1 and request_status='1') as Owner,(select count(*) from agreement_tb group by user_id having user_id=$id)  as Agreement,(select COUNT(*) from (Select * from property_deal_tb where (sender_id='".$id."' or receiver_id='".$id."') and status='Completed' group by property_id) as prop)  as Property";
		}
			
			// $sql = "select ".$subsql; 
			
			$query=$this->db->query($subsql);
			$statics = $query->result_array();
		if($statics)
		{
			return $statics;
		} else{
			return false;
		}
	}
	
	public function profile_contact_list($id)
    {
		$this->db->select('assets_type'); 
		$this->db->from('registration_tb');   
		$this->db->where('assets_id', $id);
		$rslt = $this->db->get()->result_array();
		 $user_assets_type = $rslt[0]['assets_type'];
		// ============================== for Owner ===================================================//
		if($user_assets_type == 1)
		{
			// ============================== Agent ===================================================//
			$agentsql = "SELECT profile_id,name,country,profile_photo,assets_id,invite_id,assetsType,connectedDate from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ',r1.last_name), CONCAT(r.first_name,' ',r.last_name)) as name,if(i.assets_id='".$id."',r1.country, r.country) as country,if(i.assets_id='".$id."',r1.profile_photo, r.profile_photo) as profile_photo,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id,i.entry_date as connectedDate  FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."') and i.request_status=1) as p where assetsType=2 LIMIT 5"; 
			$query=$this->db->query($agentsql);
			$agentArr = $query->result_array();
			
			// ============================== Tenant ===================================================//
			$tenantsql = "SELECT profile_id,name,country,profile_photo,assets_id,invite_id,assetsType,connectedDate from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ',r1.last_name), CONCAT(r.first_name,' ',r.last_name)) as name,if(i.assets_id='".$id."',r1.country, r.country) as country,if(i.assets_id='".$id."',r1.profile_photo, r.profile_photo) as profile_photo,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id,i.entry_date as connectedDate   FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."') and i.request_status=1) as p where assetsType=3 LIMIT 5"; 
			$query1=$this->db->query($tenantsql);
			$tenantArr = $query1->result_array();
			
			$data=array('Agent'=>$agentArr,'Tenant'=>$tenantArr);
		}// ============================== for Agent ===================================================//
		else if($user_assets_type == 2)
		{
			// ============================== Owner ===================================================//
			$ownersql = "SELECT profile_id,name,country,profile_photo,assets_id,invite_id,assetsType,connectedDate  from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ',r1.last_name), CONCAT(r.first_name,' ',r.last_name)) as name,if(i.assets_id='".$id."',r1.country, r.country) as country,if(i.assets_id='".$id."',r1.profile_photo, r.profile_photo) as profile_photo,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id,i.entry_date as connectedDate FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."') and i.request_status=1) as p where assetsType=1 LIMIT 5"; 
			$query=$this->db->query($ownersql);
			$ownerArr = $query->result_array();
			
			// ============================== Tenant ===================================================//
			$tenantsql = "SELECT profile_id,name,country,profile_photo,assets_id,invite_id,assetsType,connectedDate from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ',r1.last_name), CONCAT(r.first_name,' ',r.last_name)) as name,if(i.assets_id='".$id."',r1.country, r.country) as country,if(i.assets_id='".$id."',r1.profile_photo, r.profile_photo) as profile_photo,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id,i.entry_date as connectedDate   FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."') and i.request_status=1) as p where assetsType=3 LIMIT 5"; 
			$query=$this->db->query($tenantsql);
			$tenantArr = $query->result_array();
			
			$data=array('Owner'=>$ownerArr,'Tenant'=>$tenantArr);
		}// ============================== for Tenant ===================================================//
		else if($user_assets_type ==3)
		{
			// ============================== Owner ===================================================//
			$ownersql = "SELECT profile_id,name,country,profile_photo,assets_id,invite_id,assetsType,connectedDate from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ',r1.last_name), CONCAT(r.first_name,' ',r.last_name)) as name,if(i.assets_id='".$id."',r1.country, r.country) as country,if(i.assets_id='".$id."',r1.profile_photo, r.profile_photo) as profile_photo,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id,i.entry_date as connectedDate   FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."') and i.request_status=1) as p where assetsType=1 LIMIT 5"; 
			$query=$this->db->query($ownersql);
			$ownerArr = $query->result_array();
			
			// ============================== Agent ===================================================//
			$agentsql = "SELECT profile_id,name,country,profile_photo,assets_id,invite_id,assetsType,connectedDate from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ',r1.last_name), CONCAT(r.first_name,' ',r.last_name)) as name,if(i.assets_id='".$id."',r1.country, r.country) as country,if(i.assets_id='".$id."',r1.profile_photo, r.profile_photo) as profile_photo,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id,i.entry_date as connectedDate   FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."') and i.request_status=1) as p where assetsType=2 LIMIT 5"; 
			$query=$this->db->query($agentsql);
			$agentArr = $query->result_array();
			
			$data=array('Owner'=>$ownerArr,'Agent'=>$agentArr);
		}
			//$query=$this->db->query($sql);
			//$statics = $query->result_array();
		if($data)
		{
			return $data;
		} else{
			return false;
		}
	}
	
	/* Profile  Start*/
	
	/* Settings Edit */
	
	public function setting_profile($request = array())
    {
		$SSN_EIN = (isset($request['SSN_EIN']))?$request['SSN_EIN']:'';
		$SSN = $this->encrypt_decrypt('encrypt',$SSN_EIN);
		//return $SSN;
		$dob = (isset($request['dob']))?date('Y-m-d',strtotime($request['dob'])):'';
		
		$assetstype = $this->assetsapi_model->getAssetsType($request['assets_type']);
		if(isset($request['profile_photo']) && ($request['profile_photo'])!='')
		{
			$imgData = $request['profile_photo'];
			$img1 = str_replace('data:image/jpeg;base64,', '', $imgData);
			$img = str_replace('data:image/png;base64,', '', $img1);
			$image = base64_decode($img);
			// echo "<script>alert(".$image.")</script>";
			$image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
			$filename = $image_name . '.' . 'png';
			//rename file name with random number
			// $path = './';
			$path = 'assets/'.$assetstype.'/'.$request['assets_id'].'/Profile/';
			if (!file_exists($path)) {
				$folderPath = mkdir($path, 0777, true);
			}
			else{
					$folderPath = $path;
				}
			
			$targetPath = $path.$filename;
			file_put_contents($targetPath, $image);
		}else{
			$targetPath = '';
		}
		
		$userData = array(
				// 'assets_id'=>$request['assets_id'],
				'first_name' => $request['first_name'],
				'last_name' => $request['last_name'],
				'email' => $request['email'],
				'city' => $request['city'],
				'state' => $request['state'],
				'country' => $request['country'],
				'zip_code' => $request['zip_code'],
				'mobile_no' => $request['mobile_no'],
				'landline_no' => $request['landline_no'],
				//'assets_type' => $request['assets_type'],
				'owner_type'=>$request['owner_type'],
				'profile_photo' => $targetPath,
				'about_us' => $request['about_us'],
				'facebook_link' => $request['facebook_link'],
				'twitter_link' => $request['twitter_link'],
				'linkedin_link'=>$request['linkedin_link'],
				'SSN_EIN'=>$SSN,
				'DOB'=>$dob,
				'gender'=>$request['gender']
		);
		$this->db->where('email', $request['email']);
		$profile_update = $this->db->update('registration_tb',$userData);

		if($profile_update){
			$query = $this->db->get_where('registration_tb', array('assets_id' => $request['assets_id']));
			$result = $query->result_array();
			return $result;
		} else{
			return false;
		}
    }
	/* Settings Edit End */
	
	//==================================================Encrypt-Descypt function====================================================
	function encrypt_decrypt($action, $string) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = 'assetswatch';
		$secret_iv = '1234';
		// hash
		$key = hash('sha256', $secret_key);
		
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ( $action == 'encrypt' ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if( $action == 'decrypt' ) {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}

	
	//============================================================================================================================
	
	
	/* Settings  Password Edit */
	
	public function setting_password($request = array())
    {
		
		$hashPassword = $this->encrypt_decrypt('encrypt',$request['new_password']);
		$userData = array(
				// 'assets_id'=>$request['assets_id'],
				'password' => $hashPassword
				
		);
		$this->db->where('email', $request['email']);
		$password_update = $this->db->update('registration_tb',$userData);

		if($password_update){
			$query = $this->db->get_where('registration_tb', array('assets_id' => $request['assets_id']));
			$result = $query->result_array();
			return $result;
		} else{
			return false;
		}
    }
	/* Settings  Password Edit */
	
	/* Send Notification */
	
	public function send_notification($request = array())
    {
		$notificationData = array(
				'sender'=>$request['sender'],
				'receiver' => $request['receiver'],
				'assets_type' => $request['assets_type'],
				'message' => $request['message']
				
			);
		$notification_insert = $this->db->insert('notification_tb',$notificationData);
		if($notification_insert){
			$query = $this->db->get('notification_tb');
			$result = $query->result_array();
			return $result;
		} else{
			return false;
		}
    }
	public function notification($receiver)
    {
		$array = array('receiver'=>$receiver);
		$this->db->select("notify_id,CONCAT(r2.first_name,' ', r2.last_name) as receiver,CONCAT(r1.first_name,' ', r1.last_name) as sender,message,n.entry_date as date");
		$this->db->from("notification_tb n");
		$this->db->where($array);
		$this->db->join("registration_tb r1","n.sender=r1.assets_id",'left');
		$this->db->join("registration_tb r2","n.receiver=r2.assets_id",'left');
		$this->db->order_by('date','DESC');
		$query = $this->db->get();
		$result = $query->result_array();
		
		return $result;
	}
	public function notification_alert($receiver)
    {
		$array = array('receiver'=>$receiver);
		$this->db->select("notify_id,CONCAT(r2.first_name,' ', r2.last_name) as receiver,CONCAT(r1.first_name,' ', r1.last_name) as sender,message,n.entry_date as date");
		$this->db->from("notification_tb n");
		$this->db->where($array);
		$this->db->join("registration_tb r1","n.sender=r1.assets_id",'left');
		$this->db->join("registration_tb r2","n.receiver=r2.assets_id",'left');
		$this->db->order_by('date','DESC');
		$this->db->limit(5);
		$query = $this->db->get();
		$result = $query->result_array();
		
		return $result;
	}
	public function delete_notification($notify_id)
    {
		$notification_delete = $this->db->delete('notification_tb', array('notify_id' => $notify_id)); 
		if($notification_delete){
			return true;
		} else{
			return false;
		}
	}
	
	/* Send Notification */
	
	//================================================================Invite Start============================================================================
	public function invite_request($userid,$assets_type)
    {
		// $inviteData = array(
			
				// 'assets_type' => $request['assets_type']
				
			// );

		
		$this->db->select('assets_id,first_name,last_name,email,assets_type'); 
		$this->db->from('registration_tb');   
		$this->db->where('assets_type', $assets_type);
		if($assets_type==2)
		{
			$this->db->where('agent_type', 2);
		}
		$query = $this->db->get();
		$invite = $query->result_array();
 
		if(count($invite)>0){
			
			$this->db->select('id,title'); 
			$this->db->from('property_tb');
			$this->db->where('owner_id', $userid);			
			$query1 = $this->db->get();
			$property = $query1->result_array();
			$data=array('users'=>$invite,'property'=>$property);
			return $data;
		} else{
			return false;
		}
    }
	
	
	//================================================================Send Invite============================================================================
	public function invite($request = array())
    {
		$inviteData = array(
				'assets_id'=>$request['assets_id'],
				'property_id'=>$request['property_id'],
				'invite_id' => $request['invite_id'],
				'message' => $request['message']
				
			);
		$invite_insert = $this->db->insert('invite_tb',$inviteData);
		if($invite_insert){
			$query = $this->db->get('invite_tb');
			$result = $query->result_array();
			return $result;
		} else{
			return false;
		}
    }
	
	
	//================================================================Accept Invite Start============================================================================
	public function invite_accept($assets_id,$invite_id)
    {
		// $inviteData = array(
				// 'assets_id'=>$request['assets_id'],
				// 'invite_id' => $request['invite_id']
				
			// );
			$sql = "UPDATE invite_tb SET request_status=1 WHERE ((assets_id='".$assets_id."' AND invite_id='".$invite_id."' ) OR (assets_id='".$invite_id."' AND invite_id='".$assets_id."')) AND request_status=0";
			$requestupdate=$this->db->query($sql);
			
		if(count($requestupdate)>0){
			$query1 = $this->db->get('invite_tb');
			$result = $query1->result_array();
			return $result;
		} else{
			return false;
		}
		

    }
	
	//================================================================List of requested Invite============================================================================
	public function requested($request = array())
    {
		$inviteData = array(
				'assets_id'=>$request['user_id'],
				'assets_type' => $request['assets_type']
				
			);
			$id = $request['user_id'];
			$sql = "SELECT * from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ',r1.last_name), CONCAT(r.first_name,' ',r.last_name)) as name,if(i.assets_id='".$id."',r1.country, r.country) as country,if(i.assets_id='".$id."',r1.profile_photo, r.profile_photo) as profile_photo,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id,if(i.assets_id='".$id."',r1.email, r.email) as email,if(i.assets_id='".$id."',r1.mobile_no, r.mobile_no) as mobile_no FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.invite_id='".$id."')) as p where assetsType='".$request['assets_type']."' and request_status=0";
			$query=$this->db->query($sql);
			$result = $query->result_array();
			
		if(count($result)>0){
			return $result;
		} else{
			return false;
		}
		

    }
	
	//================================================================List of Joined invite============================================================================
	public function joined($request = array())
    {
		$inviteData = array(
				'assets_id'=>$request['user_id'],
				'assets_type' => $request['assets_type']
				
			);
			$id = $request['user_id'];
			  $sql = "SELECT * from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ',r1.last_name), CONCAT(r.first_name,' ',r.last_name)) as name,if(i.assets_id='".$id."',r1.country, r.country) as country,if(i.assets_id='".$id."',r1.profile_photo, r.profile_photo) as profile_photo,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id,if(i.assets_id='".$id."',r1.email, r.email) as email,if(i.assets_id='".$id."',r1.mobile_no, r.mobile_no) as mobile_no FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where ( i.assets_id='".$id."' or i.invite_id='".$id."')) as p  where assetsType='".$request['assets_type']."' and request_status=1";
			$query=$this->db->query($sql);
			$result = $query->result_array();
			
		if(count($result)>0){
			return $result;
		} else{
			return false;
		}
		
	
    }
	public function send_message($request = array())
    {
		$msgData = array(
				'sender'=>$request['sender'],
				'receiver' => $request['receiver'],
				'message' => $request['message']
				
			);
		$notification_insert = $this->db->insert('notification_tb',$msgData);
		if($notification_insert){
			$array = array('sender'=>$request['sender']);
			$this->db->select("notify_id,CONCAT(r2.first_name,' ', r2.last_name) as receiver,CONCAT(r1.first_name,' ', r1.last_name) as sender,message,n.entry_date as date");
			$this->db->from("notification_tb n");
			$this->db->where($array);
			$this->db->join("registration_tb r1","n.sender=r1.assets_id",'left');
			$this->db->join("registration_tb r2","n.receiver=r2.assets_id",'left');
			$this->db->order_by('date','DESC');
			$query = $this->db->get();
			$result = $query->result_array();
			return $result;
		} else{
			return false;
		}
	}
	
	// =====================================Service Start=====================================================================
	
	public function service_request($userid)
    {
		$this->db->select('assets_type'); 
		$this->db->from('registration_tb');   
		$this->db->where('assets_id', $userid);
		$rslt = $this->db->get()->result_array();
		$user_assets_type = $rslt[0]['assets_type'];
		
		 $id= $userid;
		 if($user_assets_type == 1)
		 {
			 $this->db->select('id as property_id,title as property_name');
			$query = $this->db->get_where('property_tb',array('owner_id'=>$userid));
			 $property_list = $query->result_array();
		 }else{
			$where = "p.sender_id=$userid OR p.receiver_id=$userid  and p.status = 'Completed'";
			 $this->db->select("deal_id,p.property_id,title as property_name,sender_id,receiver_id,p.status,total_amount as rent,advance,address,img_path,initiated_date,pro.property_type,pro.property_status");
			 $this->db->from("property_deal_tb p");
			 $this->db->join("property_tb pro","pro.id=p.property_id",'left');
			  $this->db->join("property_photo_tb pp","pro.id=pp.property_id",'left');
			 $this->db->where($where);
			 $this->db->group_by('p.property_id');
			 $query = $this->db->get();
			
			 $property_list = $query->result_array();
		 }
		 if($user_assets_type ==1)
		 {
			$sql = "SELECT * from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',r1.agent_type, r.agent_type) as agent_type,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ', r1.last_name), CONCAT(r.first_name,' ', r.last_name)) as name,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."')) as p where (assetsType=2 and agent_type=1) and request_status=1";
			   $query1=$this->db->query($sql);
			   $agentlist = $query1->result_array();
			
			 $result = array('property_list'=>$property_list,'users'=>$agentlist);
			
		 }
		else if($user_assets_type ==3)
		{
			 $sql = "SELECT * from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',r1.agent_type, r.agent_type) as agent_type,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ', r1.last_name), CONCAT(r.first_name,' ', r.last_name)) as name,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."')) as p where request_status=1";
			  $query1=$this->db->query($sql);
			  $agentlist = $query1->result_array();
			
			$result = array('property_list'=>$property_list,'users'=>$agentlist);
			
		}
		else if($user_assets_type ==2)
		{
			 $sql = "SELECT * from (select i.*,if(i.assets_id='".$id."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$id."',r1.agent_type, r.agent_type) as agent_type,if(i.assets_id='".$id."',CONCAT(r1.first_name,' ', r1.last_name), CONCAT(r.first_name,' ', r.last_name)) as name,if(i.assets_id='".$id."',r1.assets_id, r.assets_id) as profile_id FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$id."' or i.invite_id='".$id."')) as p where request_status=1 and (assetsType='1' or assetsType='2')" ;
			  $query1=$this->db->query($sql);
			  $agentlist = $query1->result_array();
			// echo $this->db->last_query();
			$result = array('property_list'=>$property_list,'users'=>$agentlist);
			
		}
		if(count($result)>0){
			return $result;
		} else{
			return false;
		}
	}
	public function service_request_send($request = array())
    {
		$imgData = $request['service_photo'];
		$img1 = str_replace('data:image/jpeg;base64,', '', $imgData);
		$img = str_replace('data:image/png;base64,', '', $img1);
		$image = base64_decode($img);
		// echo "<script>alert(".$image.")</script>";
		$image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
		$filename = $image_name . '.' . 'png';
		//rename file name with random number
		// $path = './';
		$data = $this->profile($request['send_by']);
		$assets_type = $data['assets_type'];
		$data1 = $this->getAssetsType($assets_type);
		
		$path = 'assets/'.$data1.'/'.$request['send_by'].'/property_service_provider/';
		if (!file_exists($path)) {
			$folderPath = mkdir($path, 0777, true);
		}
		else{
				$folderPath = $path;
			}
		
		$targetPath = $path.$filename;
		file_put_contents($targetPath, $image);
		
		
		$serviceData = array(
				'property_id'=>$request['property_id'],
				'send_by' => $request['send_by'],
				'service_provider' => $request['service_provider'],
				'service_msg' => $request['service_msg'],
				'service_photo' => $targetPath
				
			);
		$service_insert = $this->db->insert('service_tb',$serviceData);
		if($service_insert){
			return true;
		} else{
			return false;
		}
	}
	
	public function service_send($userid)
    {
		$this->db->select("s.*,pro.title as property_name,first_name,last_name");
			$this->db->from("service_tb s");
			// $this->db->join("property_deal_tb pd","pd.deal_id=s.deal_id",'left');
			$this->db->join("property_tb pro","pro.id=s.property_id",'left');
			$this->db->join("registration_tb r","r.assets_id=s.service_provider",'left');
			$this->db->where("send_by", $userid);
			$this->db->where("service_status",'0');
			$query = $this->db->get();
			$result = $query->result_array();
			
		if(count($result)>0){
			return $result;
		} else{
			return false;
		}
	}
	public function service_resolve($userid)
    {
		$this->db->select("s.*,pro.title as property_name,first_name,last_name,profile_photo");
			$this->db->from("service_tb s");
			// $this->db->join("property_deal_tb pd","pd.deal_id=s.deal_id",'left');
			$this->db->join("property_tb pro","pro.id=s.property_id",'left');
			$this->db->join("registration_tb r","r.assets_id=s.service_provider",'left');
			$this->db->where("send_by", $userid);
			$this->db->where("service_status", '1');
			$query = $this->db->get();
			$result = $query->result_array();
			
		if(count($result)>0){
			return $result;
		} else{
			return false;
		}
	}
	public function service_detail($serviceid)
    {
		$this->db->select("s.*,pro.title as property_name,first_name,last_name,profile_photo,pro.description");
			$this->db->from("service_tb s");
			// $this->db->join("property_deal_tb pd","pd.deal_id=s.deal_id",'left');
			$this->db->join("property_tb pro","pro.id=s.property_id",'left');
			$this->db->join("registration_tb r","r.assets_id=s.service_provider",'left');
			$this->db->where("service_id", $serviceid);
			//$this->db->where("service_status", 1);
			$query = $this->db->get();
			$result = $query->result_array();
		if(count($result)>0){
			return $result;
		} else{
			return false;
		}
	}
	public function service_requested($userid)
    {
		
		$this->db->select("s.*,pro.title as property_name,first_name,last_name,profile_photo");
			$this->db->from("service_tb s");
			// $this->db->join("property_deal_tb pd","pd.deal_id=s.deal_id",'left');
			$this->db->join("property_tb pro","pro.id=s.property_id",'left');
			$this->db->join("registration_tb r","r.assets_id=s.service_provider",'left');
			$this->db->where("service_provider", $userid);
			$this->db->where("service_status", 0);
			$query = $this->db->get();
			$result = $query->result_array();
		if(count($result)>0){
			return $result;
		} else{
			return false;
		}
	}
		
	
	
	// =====================================Service End=====================================================================
	
	// =====================================Google Login=================================================================
	 public function checkUser($data = array()){
        $this->db->select('assets_id');
        $this->db->from('registration_tb');
		/*if($data['oauth_provider']=='facebook')
		{
			$where = ' (facebook_id="'.$data['facebook_id'].'")';
		}
		if($data['oauth_provider']=='twitter')
		{
			$where = ' (twitter_id = "'.$data['twitter_id'].'")';
		}
		if($data['oauth_provider']=='google')
		{
			$where = ' (google_id = "'.$data['google_id'].'")';
		}
		if($data['oauth_provider']=='linkedin')
		{
			$where = '(linkedin_id = "'.$data['linkedin_id'].'")';
		}*/
        $this->db->where(array('oauth_provider'=>$data['oauth_provider'],'oauth_uid'=>$data['oauth_uid']));
		
		//$this->db->where(array('email'=>$data['email']));
		
		//$this->db->where($where);
        $query = $this->db->get();
        $check = $query->num_rows();
		//echo $this->db->last_query();
        //echo $check;
        if($check > 0){
            $result = $query->row_array();
            $data['modified_date'] = date("Y-m-d H:i:s");
            $update = $this->db->update('registration_tb',$data,array('assets_id'=>$result['assets_id']));
            $userID = $data['oauth_uid'];
        }else{
            $data['created_date'] = date("Y-m-d H:i:s");
            $data['modified_date']= date("Y-m-d H:i:s");
            $insert = $this->db->insert('registration_tb',$data);
            $userID = $data['oauth_uid'];
        }

        return $userID?$userID:false;
    }
	// =====================================Google Login end=================================================================
	
	// =====================================Plan Start=================================================================
	 public function plan()
	 {
		 $this->db->select("pf.*,p.plan,p.user_type as usertype_id,u.user_type,f.feature_name,p.per_month,p.per_annum,f.feature_tag,f.feature_status,f.feature_unit");
		 $this->db->from('plan_feature_relation_tb pf');
		 $this->db->join("plan_tb p","pf.plan_id=p.id",'left');
		 $this->db->join("plan_user_type_tb u","p.user_type=u.id",'left');
		 $this->db->join("feature_tb f","pf.feature_id=f.id",'left');
		 $query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			foreach($result as $val)
			{
				$planArr[$val['user_type']][$val['plan']][$val['feature_tag']] = array(
																		// "id"=>$val['id'],
																		// "plan_id"=>$val['plan_id'],
																		//"feature_id"=>$val['feature_id'],
																		"limit_upto"=>$val['limit_upto'],
																		"confirmation"=>$val['confirmation'],
																		// "status"=>$val['status'],
																		// "entry_date"=>$val['entry_date'],
																		// "plan"=>$val['plan'],
																		// "usertype_id"=>$val['usertype_id'],
																		// "user_type"=>$val['user_type'],
																		"feature_name"=>$val['feature_name'],
																		//"feature_tag"=>$val['feature_tag'],
																		// "per_month"=>$val['per_month'],
																		// "per_annum"=>$val['per_annum'],
																		"feature_unit"=>$val['feature_unit'],
																		"feature_status"=>$val['feature_status']
															);

				$plandetail[$val['user_type']][$val['plan']]= array("plan_id"=>$val['plan_id'],
																	"per_month"=>$val['per_month'],
																	"per_annum"=>$val['per_annum'],
																	"planName"=>$val['plan']
																	);


			}
			foreach($planArr as $key=>$valArr)
			{
				foreach($valArr as $plankey=>$planval)
				{
					foreach($valArr as $fetkey=>$fval)
					{
						 $plan[$key][$fetkey]=Array("plan_details"=>$plandetail[$key][$fetkey],"features"=>$fval);


					}
				}
			}
				return $plan;
		}else{
				return false;
			}

	 }
	 
	 public function plan_by_assetstype($assets_type)
	 {
		 $this->db->select("pf.*,p.plan,p.user_type as usertype_id,u.user_type,f.feature_name,p.per_month,p.per_annum,f.feature_tag,f.feature_status,f.feature_unit");
		 $this->db->from('plan_feature_relation_tb pf');
		 $this->db->join("plan_tb p","pf.plan_id=p.id",'left');
		 $this->db->join("plan_user_type_tb u","p.user_type=u.id",'left');
		 $this->db->join("feature_tb f","pf.feature_id=f.id",'left');
		  $this->db->where('p.user_type', $assets_type);
		  
		 $query = $this->db->get();
		$result = $query->result_array();
		
		if(count($result)>0)
		{
			foreach($result as $val)
			{
				$planArr[$val['user_type']][$val['plan']][$val['feature_tag']] = array(
																		// "id"=>$val['id'],
																		// "plan_id"=>$val['plan_id'],
																		//"feature_id"=>$val['feature_id'],
																		"limit_upto"=>$val['limit_upto'],
																		"confirmation"=>$val['confirmation'],
																		// "status"=>$val['status'],
																		// "entry_date"=>$val['entry_date'],
																		// "plan"=>$val['plan'],
																		// "usertype_id"=>$val['usertype_id'],
																		// "user_type"=>$val['user_type'],
																		"feature_name"=>$val['feature_name'],
																		//"feature_tag"=>$val['feature_tag'],
																		// "per_month"=>$val['per_month'],
																		// "per_annum"=>$val['per_annum'],
																		"feature_unit"=>$val['feature_unit'],
																		"feature_status"=>$val['feature_status']
															);

				$plandetail[$val['user_type']][$val['plan']]= array("plan_id"=>$val['plan_id'],
																	"per_month"=>$val['per_month'],
																	"per_annum"=>$val['per_annum'],
																	"planName"=>$val['plan']
																	);


			}
			foreach($planArr as $key=>$valArr)
			{
				foreach($valArr as $plankey=>$planval)
				{
					foreach($valArr as $fetkey=>$fval)
					{
						 $plan[$key][$fetkey]=Array("plan_details"=>$plandetail[$key][$fetkey],"features"=>$fval);


					}
				}
			}
				return $plan;
				
		}else{
				return false;	
			}
		
	 }
	// =====================================Plan end=================================================================
	
	public function getPlandetail($plan_id,$feature_alias){
		
		 $this->db->select("pf.*,p.plan,p.user_type as usertype_id,u.user_type,f.feature_name,p.per_month,p.per_annum,f.feature_tag,f.feature_status,f.feature_unit");
		 $this->db->from('plan_feature_relation_tb pf');
		 $this->db->join("plan_tb p","pf.plan_id=p.id",'left');
		 $this->db->join("plan_user_type_tb u","p.user_type=u.id",'left');
		 $this->db->join("feature_tb f","pf.feature_id=f.id",'left');
		  $this->db->where('pf.plan_id', $plan_id);
		   $this->db->where('f.feature_tag', $feature_alias);
		 $query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
	public function getPropertydetail($userid){
		$this->db->select("COUNT(owner_id) as added_property");
		$this->db->from('property_tb');
		$this->db->where('owner_id', $userid);
		$this->db->group_by('owner_id');
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	public function getPropertyphotos($userid){
		$this->db->select("COUNT(property_id) as added_photos");
		$this->db->from('property_tb');
		$this->db->where('owner_id', $userid);
		$this->db->join("property_photo_tb ph","property_tb.id=ph.property_id",'left');
		$this->db->group_by('property_id');
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	public function send_agreement($agreementData){
		if($agreementData['property_id']!='')
		{
			$propQuery = $this->db->get_where('property_tb',array('id'=>$agreementData['property_id']));
			$propRslt = $propQuery->result_array();
			$PropertyAddress = $propRslt[0]['address'];
			$RentAmount = $propRslt[0]['total_amount'];
			$DepositAmount = $propRslt[0]['advance'];
			$property_status = $propRslt[0]['property_status'];
			
			$tokensArr['Rent Amount'] = $RentAmount;
			$tokensArr['Deposit Amount'] = $DepositAmount;
			$tokensArr['Property Address'] = $PropertyAddress;
		}
		if($agreementData['sender_id']!='')
		{
			$SenderQuery = $this->profile($agreementData['sender_id']);
			if($SenderQuery['assets_type']=='1')
			{	
				$OwnerFullName = $SenderQuery['first_name'].' '.$SenderQuery['last_name'];
				$OwnerAddress = $SenderQuery['city'].','.$SenderQuery['state'].','.$SenderQuery['country'];
				
				$tokensArr['Owner Full Name'] = $OwnerFullName;
				$tokensArr['Owner Address'] = $OwnerAddress;
				
			}else if($SenderQuery['assets_type']=='2')
			{
				$AgentFullName = $SenderQuery['first_name'].' '.$SenderQuery['last_name'];
				$AgentAddress = $SenderQuery['city'].','.$SenderQuery['state'].','.$SenderQuery['country'];
				
				$tokensArr['Agent Full Name'] = $AgentFullName;
				$tokensArr['Agent Address'] = $AgentAddress;
			}
			else if($SenderQuery['assets_type']=='3'){
				$TenantFullName = $SenderQuery['first_name'].' '.$SenderQuery['last_name'];
				$TenantAddress = $SenderQuery['city'].','.$SenderQuery['state'].','.$SenderQuery['country'];
				
				$tokensArr['Tenant Full Name'] = $TenantFullName;
				$tokensArr['Tenant Address'] = $TenantAddress;
			}
			
		}
		if($agreementData['receiver_id']!='')
		{
			$RecQuery = $this->profile($agreementData['receiver_id']);
			if($RecQuery['assets_type']=='1')
			{	
				$OwnerFullName = $RecQuery['first_name'].' '.$RecQuery['last_name'];
				$OwnerAddress = $RecQuery['city'].','.$RecQuery['state'].','.$RecQuery['country'];
				
				$tokensArr['Owner Full Name'] = $OwnerFullName;
				$tokensArr['Owner Address'] = $OwnerAddress;
				
			}else if($RecQuery['assets_type']=='2')
			{
				$AgentFullName = $RecQuery['first_name'].' '.$RecQuery['last_name'];
				$AgentAddress = $RecQuery['city'].','.$RecQuery['state'].','.$RecQuery['country'];
				
				$tokensArr['Agent Full Name'] = $AgentFullName;
				$tokensArr['Agent Address'] = $AgentAddress;
				
			}
			else if($RecQuery['assets_type']=='3'){
				$TenantFullName = $RecQuery['first_name'].' '.$RecQuery['last_name'];
				$TenantAddress = $RecQuery['city'].','.$RecQuery['state'].','.$RecQuery['country'];
				
				$tokensArr['Tenant Full Name'] = $TenantFullName;
				$tokensArr['Tenant Address'] = $TenantAddress;
			}
			
		}
		if($agreementData['agreement_id']!='')
		{
			$Agreement = $this->getAgreement($agreementData['agreement_id']);
			$template = $Agreement[0]['agreement_doc_content'];
		}
			
			
			$pattern = '%s';
			foreach($tokensArr as $key=>$val){
				$varMap[sprintf($pattern,$key)] = $val;
				
			}
			 // print_r($varMap);
			//echo $template;
			$Content = strtr($template,$varMap);
			$agreementUniqueCode = 'AW_agreement_'.mt_rand(100000, 999999);
			$dataToInsert = array(
				'sender_id'=>$agreementData['sender_id'],
				'receiver_id'=>$agreementData['receiver_id'],
				'agreement_id' => $agreementData['agreement_id'],
				'property_id' => $agreementData['property_id'],
				'initiated_date'=>date('Y-m-d'),
				'replaced_template'=>$Content,
				'comment'=>$agreementData['description'],
				'property_status'=>$property_status,
				'agreement_unique_id'=>$agreementUniqueCode
			);
		  $insert = $this->db->insert('property_deal_tb',$dataToInsert);
		
		if($insert)
		{
			  $deal_id = $this->db->insert_id();
			  
			  $dataVersion = array(
					'deal_id' 			=>$deal_id,
					'version_name' 		=>'AWG_V1',
					// 'file_name' 		=>'',
					'user_id' 			=>$agreementData['sender_id'],
					'agreement_content' =>$Content,
					// 'div_id' 			=> $,
					// 'signature_content' => $,
					// 'signature_type' 	=> $,
					'acceptance_status' => '0', //Submitted
					'created_date'		=> date("Y-m-d H:i:s")
			  );
				$this->db->insert('agreement_version_tb',$dataVersion);
			return $deal_id;
		}else{
				return false;	
			}
		}
		
		public function send_forwarded_agreement($agreementData){
		if($agreementData['property_id']!='')
		{
			$propQuery = $this->db->get_where('property_tb',array('id'=>$agreementData['property_id']));
			$propRslt = $propQuery->result_array();
			$PropertyAddress = $propRslt[0]['address'];
			$RentAmount = $propRslt[0]['total_amount'];
			$DepositAmount = $propRslt[0]['advance'];
			$property_status = $propRslt[0]['property_status'];
			
			$tokensArr['Rent Amount'] = $RentAmount;
			$tokensArr['Deposit Amount'] = $DepositAmount;
			$tokensArr['Property Address'] = $PropertyAddress;
		}
		if($agreementData['sender_id']!='')
		{
			$SenderQuery = $this->profile($agreementData['sender_id']);
			if($SenderQuery['assets_type']=='1')
			{	
				$OwnerFullName = $SenderQuery['first_name'].' '.$SenderQuery['last_name'];
				$OwnerAddress = $SenderQuery['city'].','.$SenderQuery['state'].','.$SenderQuery['country'];
				
				$tokensArr['Owner Full Name'] = $OwnerFullName;
				$tokensArr['Owner Address'] = $OwnerAddress;
				
			}else if($SenderQuery['assets_type']=='2')
			{
				$AgentFullName = $SenderQuery['first_name'].' '.$SenderQuery['last_name'];
				$AgentAddress = $SenderQuery['city'].','.$SenderQuery['state'].','.$SenderQuery['country'];
				
				$tokensArr['Agent Full Name'] = $AgentFullName;
				$tokensArr['Agent Address'] = $AgentAddress;
			}
			else if($SenderQuery['assets_type']=='3'){
				$TenantFullName = $SenderQuery['first_name'].' '.$SenderQuery['last_name'];
				$TenantAddress = $SenderQuery['city'].','.$SenderQuery['state'].','.$SenderQuery['country'];
				
				$tokensArr['Tenant Full Name'] = $TenantFullName;
				$tokensArr['Tenant Address'] = $TenantAddress;
			}
			
		}
		if($agreementData['receiver_id']!='')
		{
			$RecQuery = $this->profile($agreementData['receiver_id']);
			if($RecQuery['assets_type']=='1')
			{	
				$OwnerFullName = $RecQuery['first_name'].' '.$RecQuery['last_name'];
				$OwnerAddress = $RecQuery['city'].','.$RecQuery['state'].','.$RecQuery['country'];
				
				$tokensArr['Owner Full Name'] = $OwnerFullName;
				$tokensArr['Owner Address'] = $OwnerAddress;
				
			}else if($RecQuery['assets_type']=='2')
			{
				$AgentFullName = $RecQuery['first_name'].' '.$RecQuery['last_name'];
				$AgentAddress = $RecQuery['city'].','.$RecQuery['state'].','.$RecQuery['country'];
				
				$tokensArr['Agent Full Name'] = $AgentFullName;
				$tokensArr['Agent Address'] = $AgentAddress;
				
			}
			else if($RecQuery['assets_type']=='3'){
				$TenantFullName = $RecQuery['first_name'].' '.$RecQuery['last_name'];
				$TenantAddress = $RecQuery['city'].','.$RecQuery['state'].','.$RecQuery['country'];
				
				$tokensArr['Tenant Full Name'] = $TenantFullName;
				$tokensArr['Tenant Address'] = $TenantAddress;
			}
			
		}
		if($agreementData['agreement_id']!='')
		{
			$Agreement = $this->getAgreement($agreementData['agreement_id']);
			// $template = $Agreement[0]['agreement_doc_content'];
		}
			
			
			// $pattern = '%s';
			// foreach($tokensArr as $key=>$val){
				// $varMap[sprintf($pattern,$key)] = $val;
				
			// }
			 // print_r($varMap);
			//echo $template;
			// $Content = strtr($template,$varMap);
			$agreementUniqueCode = 'AW_agreement_'.mt_rand(100000, 999999);
		
			$dataToInsert = array(
				'sender_id'=>$agreementData['sender_id'],
				'receiver_id'=>$agreementData['receiver_id'],
				'agreement_id' => $agreementData['agreement_id'],
				'property_id' => $agreementData['property_id'],
				'initiated_date'=>date('Y-m-d'),
				'replaced_template'=>$agreementData['updatedTemplate'],
				'comment'=>$agreementData['description'],
				'property_status'=>$property_status,
				'agreement_unique_id'=>$agreementUniqueCode
			);
		  $insert = $this->db->insert('property_deal_tb',$dataToInsert);
		
		if($insert)
		{
			  $deal_id = $this->db->insert_id();
			  
			  $dataVersion = array(
					'deal_id' 			=>$deal_id,
					'version_name' 		=>'AWG_V1',
					// 'file_name' 		=>'',
					'user_id' 			=>$agreementData['sender_id'],
					'agreement_content' =>$agreementData['updatedTemplate'],
					// 'div_id' 			=> $,
					// 'signature_content' => $,
					// 'signature_type' 	=> $,
					'acceptance_status' => '0', //Submitted
					'created_date'		=> date("Y-m-d H:i:s")
			  );
				$this->db->insert('agreement_version_tb',$dataVersion);
			return $deal_id;
		}else{
				return false;	
			}
		}
		
	public function saved_agreement($userid){
		$this->db->select("*");
		$this->db->from('agreement_tb ag');
		$this->db->where('user_id', $userid);
		$query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
	public function agreement_detail($agreement_id){
		$this->db->select("pd.*,agreement_title,agreement_file_name,agreement_doc_content,branding_logo,header_content,header_image,watermark_image,footer_content");
		$this->db->from('property_deal_tb pd');
		$this->db->join('agreement_tb a','pd.agreement_id=a.agreement_id','LEFT');
		$this->db->where('pd.agreement_id', $agreement_id);
		$this->db->where('pd.status', '0');
		$query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
	public function requested_agreement($userid){
		$this->db->select("pd.*,agreement_title,agreement_file_name,agreement_doc_content");
		$this->db->from('property_deal_tb pd');
		$this->db->join('agreement_tb a','pd.agreement_id=a.agreement_id','LEFT');
		$this->db->where('receiver_id', $userid);
		$this->db->where('pd.status', 'Pending');
		$query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}

	public function execute_agreement($userid){
		// $where = "(sender_id=$userid OR receiver_id=$userid) and (pd.status='Inprocess' or pd.status='Completed')";
		// $this->db->select("pd.*,agreement_title,agreement_file_name,agreement_doc_content");
		// $this->db->from('property_deal_tb pd');
		// $this->db->join('agreement_tb a','pd.agreement_id=a.agreement_id','LEFT');
		// $this->db->where($where);
		// $this->db->order_by('pd.initiated_date','DESC');
		
		// $query = $this->db->get();
		$query = $this->db->query('SELECT * FROM property_deal_tb INNER JOIN agreement_tb on agreement_tb.agreement_id = property_deal_tb.agreement_id INNER JOIN (SELECT property_id, MAX(initiated_date) as TopDate FROM property_deal_tb GROUP BY property_id) AS EachItem ON EachItem.TopDate = property_deal_tb.initiated_date AND EachItem.property_id = property_deal_tb.property_id');
		$result = $query->result_array();
		 //echo $this->db->last_query();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
	public function add_service_by_provider($request=array())
	{
	
		$delete = $this->db->delete('service_provider_facilities_tb',array('service_provider_id'=>$request['user_id']));
			 foreach($request['services_list'] as $key=>$val)
			 {
				$service = $val['service_id'];
				$dataToInsert = array(
								'service_provider_id'=>$request['user_id'],
								'service_id'=>$service,
							);
			$inserted = $this->db->insert('service_provider_facilities_tb',$dataToInsert);
			 }
			
		if($inserted)
		{
			$query = $this->db->get_where('service_provider_facilities_tb',array('service_provider_id'=>$request['user_id']));
			$result = $query->result_array();
			return $result;
		}else{
				return false;	
			}
	
	}
	public function getAgreementCount($userid){
		$this->db->select("COUNT(agreement_id) as agreement_count");
		$this->db->from('agreement_tb ag');
		$this->db->where('user_id', $userid);
		$this->db->group_by('user_id');
		$query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
	public function planDeatilBy($plan_id)
	{
		$query = $this->db->get_where('plan_tb',array('id'=>$plan_id));
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
//====================================================================User Search Start==========================================================================================	
	public function userSearch($request=array())
	{

		$query = $this->db->query("SELECT value,label from (select i.*,if(i.assets_id='".$request['userid']."',r1.assets_type, r.assets_type) as assetsType,if(i.assets_id='".$request['userid']."',r1.agent_type, r.agent_type) as agent_type,if(i.assets_id='".$request['userid']."',CONCAT(r1.first_name,' ', r1.last_name), CONCAT(r.first_name,' ', r.last_name)) as label,if(i.assets_id='".$request['userid']."',r1.assets_id, r.assets_id) as value,if(i.assets_id='".$request['userid']."',r1.email, r.email) as email,if(i.assets_id='".$request['userid']."',r1.city, r.city) as city,if(i.assets_id='".$request['userid']."',r1.zip_code, r.zip_code) as zip_code FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$request['userid']."' or i.invite_id='".$request['userid']."')) as p where (assetsType='".$request['assets_type']."' and (label like '%".$request['string']."%' OR zip_code like '%".$request['string']."%' OR email like '%".$request['string']."%' OR city like '%".$request['string']."%')) and request_status=1");
		$result = $query->result_array();
		// echo $this->db->last_query();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	public function user_search($request=array())
	{

		$where = "assets_type='".$request['assets_type']."' AND (first_name like '%".$request['keyword']."%' OR last_name like '%".$request['keyword']."%' OR zip_code like '%".$request['keyword']."%' OR email like '%".$request['keyword']."%' OR city like '%".$request['keyword']."%')";
		$this->db->select("assets_id as value,CONCAT(first_name,' ',last_name) as label");
		$query = $this->db->get_where('registration_tb',$where);
		$result = $query->result_array();
		// echo  $this->db->last_query();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
//====================================================================User Search End==========================================================================================	
//===============================================================================================================================================================================	
	
	//=============================================================Recent Property Added===========================================================================================
	public function recent_added_property($id)
	{
		$this->db->select('*');
		$this->db->from('property_tb p');
		$this->db->join('property_photo_tb ph','p.id=ph.property_id','LEFT');
		$this->db->where('owner_id', $id);
		$this->db->order_by('p.id','desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
	//==========================================Session validate================================================================================================
	public function getSessionValidate($session_id){
		$validate = $this->db->get_where('registration_tb',array('session_id'=>$session_id));
		if(($validate->num_rows())>0)
		{
			return true;
		}else
		{
			return false;
		}
	}
	
	//==========================================Get Countries================================================================================================
	public function getCountries(){
		$query = $this->db->get('countries');
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else
		{
			return false;
		}
	}
	
	//==========================================Get Countries================================================================================================
	public function getStates($name){
		$query1 = $this->db->get_where('countries',array('name'=>$name));
		$data = $query1->result_array();
		$countryid = $data[0]['id'];
		$query = $this->db->get_where('states',array('country_id'=>$countryid));
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else
		{
			return false;
		}
	}
	
	//==========================================Get Countries================================================================================================
	public function getCities($name){
		$query1 = $this->db->get_where('states',array('name'=>$name));
		$data = $query1->result_array();
		$stateid = $data[0]['id'];
		$query = $this->db->get_where('cities',array('state_id'=>$stateid));
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else
		{
			return false;
		}
	}
	
	//====================================Statics for service provider=========================
	public function statics_service_provider($user_id)
	{
		$sql = "select (select count(*) from service_tb group by service_provider having service_provider='".$user_id."') as TotalRequest,(select count(*) from service_tb where service_status='1' group by service_provider having service_provider='".$user_id."') as ResolvedRequest,(select count(*) from service_tb where service_status='0' and DATE(entry_date)='".date('Y-m-d')."' group by service_provider having service_provider='".$user_id."') as TodaysRequest";
		$query=$this->db->query($sql);
		$result = $query->result_array();
		//echo $this->db->last_query();
		if(count($result)>0)
		{
			return $result;
		}else
		{
			return false;
		}
	}
	
	public function recent_resolved_request($user_id)
	{
		$this->db->select("CONCAT(first_name,'',last_name)as requestedUserName,country as requestedUserCountry,s.entry_date,send_by as requestedUserID,profile_photo as requestedUserPhoto,service_provider as ServiceProviderID");
		$this->db->from('service_tb s');
		$this->db->join('registration_tb r','s.send_by=r.assets_id','LEFT');
		$this->db->where('service_provider', $user_id);
		$this->db->where('service_status','1');
		$this->db->order_by('entry_date','desc');
		$this->db->limit(5);
		$query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
	//================================================ property search ============================================
	public function property_search_by($request=array())
	{

		$where = " zip_code like '%".$request['keyword']."%' OR title like '%".$request['keyword']."%' OR city like '%".$request['keyword']."%'";
		$this->db->select('id,title');
		$this->db->from('property_tb');
		$this->db->where($where);
		$query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
	//================================================ delete agreement =============================================================
	public function delete_agreement($agreement_id)
    {
		$agreement_delete = $this->db->delete('agreement_tb', array('agreement_id' => $agreement_id)); 
		if($agreement_delete){
			return true;
		} else{
			return false;
		}
	}
	
	//============================================== edit property ==============================================================
	public function edit_agreement($request = array())
    {
		$agreementData = array(
				'agreement_title' => $request['agreement_title'],
				'agreement_doc_content' => $request['agreement_doc_content'],
				'header_content' =>$request['header_content'],
				'header_image' =>$request['header_image'],
				'watermark_image' =>$request['watermark_image'],
				'footer_content' =>$request['footer_content']
				
				);
		$this->db->where('agreement_id', $request['agreement_id']);
		$AgreementUpdate = $this->db->update('agreement_tb',$agreementData);

		if($AgreementUpdate){

			$result = $this->getAgreement($request['agreement_id']);
			return $result;
		} else{
			return false;
		}
    }
	public function getAgreement($agreement_id)
    {
			$query = $this->db->get_where('agreement_tb',array('agreement_id'=>$agreement_id));
			$result = $query->result_array();
			
		if($result){
			return $result;
		} else{
			return false;
		}
    }
	
	public function webview_agreement($deal_id)
	{
		
		$this->db->select('deal_id,	replaced_template');
		$this->db->from('property_deal_tb');
		$this->db->where('deal_id', $deal_id);
		$query = $this->db->get();
		$result = $query->result_array();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	
	//Start Elakkiya
	//==========================Rating ==================================
	public function insert_rating($data){
		// $this->db->get_where('owner_agent_rating_tb',array('owner_id'=>$data[''],'agent_id'=>$data['']))
		if(!empty($data))
			return $this->db->insert('owner_agent_rating_tb',$data);
		else
			return false;
	}
	public function get_session_id($data){
		$this->db->select('session_id');
		$this->db->where('email',$data['email']);
		$query = $this->db->get('registration_tb');
		$result = $query->result();
		if(count($result)>0)
		{
			return $result;
		}else{
				return false;	
			}
	}
	public function insert_forgot_password_status($record){
		return $this->db->insert('forgot_password_status_tb',$record);
	}
	public function get_email($session_id){
		$this->db->select('email');
		$this->db->where('session_id',$session_id);
		$query = $this->db->get('registration_tb');
		return $result = $query->result();
	}
	public function update_password($data){
		$email = $data['email'];
		$session_id = $data['session_id'];
		//check mail id is present or not
		$this->db->select('count(*) as count');
		$this->db->where('email',$email);
		$query = $this->db->get('registration_tb');
		$result = $query->result();
		$cnt = $result[0]->count;
		if($cnt > 0){
			//update password
			$hashPassword = $this->encrypt_decrypt('encrypt',$data['password']);
			$res['password'] = $hashPassword;
			$this->db->where('email',$email);
			$this->db->where('session_id',$session_id);
			return $this->db->update('registration_tb',$res);
		}
		else 	
			return false;
	}
	public function update_forgot_pwd_status($data){
		$this->db->select('count(*) as count');
		$this->db->where('current_session_id',$data['session_id']);
		$query = $this->db->get('forgot_password_status_tb');
		$result = $query->result();
		$cnt = $result[0]->count;
		if($cnt > 0){
			$res['status'] = 'Done';
			$this->db->where('current_session_id',$data['session_id']);
			$result = $this->db->update('forgot_password_status_tb',$res);
			return $result;
		}
		else 	
			return false;
	}
	public function get_serviceprovider_by_owner($id){
		$this->db->select('GROUP_CONCAT(invite_id) as invite_id');
		$this->db->where('assets_id',$id);
		$query = $this->db->get('invite_tb');
		$result = $query->result_array();	
		$invite_id = $result[0]['invite_id'];
		
		$this->db->select('GROUP_CONCAT(assets_id) as assets_id');
		$this->db->where('invite_id',$id);
		$query1 = $this->db->get('invite_tb');
		$result1 = $query1->result_array();
		$assets_id = $result1[0]['assets_id'];	
		$sp_id =  $invite_id.','.$assets_id;
		
		if($invite_id!=''|| $assets_id!='')
			return $sp_id;
		else 	
			return false;
	}
	public function get_serviceprovider($id){
		$this->db->select('assets_id,first_name,last_name');
		$this->db->where('agent_type',2);//agent_type = 2 is service provider
		$this->db->where('assets_id',$id);
		$query = $this->db->get('registration_tb');
		return $result = $query->row();	
	}
	public function get_deal_id($property_id){
		$this->db->select('deal_id');
		$this->db->where('property_id',$property_id);
		$query = $this->db->get('property_deal_tb');
		return $result = $query->row();
	}
	public function insert_service_request($data){
		$this->db->insert('service_tb',$data);
		return $id = $this->db->insert_id();
	}
	public function get_property_report($data){
		$this->db->select('C.title, C.description, A.transactiondate, A.transactionamount');
		$this->db->where('A.user_id',$data['user_id']);
		$this->db->where('A.transactiondate >=',$data['from_date']);
		$this->db->where('A.transactiondate <=',$data['to_date']);
		$this->db->where('B.property_id',$data['property_id']);
		$this->db->from('transaction_tb as A');
		$this->db->join('property_transaction_tb as B','A.txn_id = B.transaction_id', 'left');
		$this->db->join('property_tb as C','C.id = B.property_id', 'left');
		$query = $this->db->get();
		echo $this->db->last_query();
		return $result = $query->result_array();	
	}
	public function get_transaction_report($data){
		/* $this->db->select('C.title,C.description, D.plan, A.transactiondate, A.transactionamount');
		$this->db->where('A.user_id',$data['user_id']);
		$this->db->where('A.transactiondate >=',$data['from_date']);
		$this->db->where('A.transactiondate <=',$data['to_date']);
		$this->db->from('transaction_tb as A');
		$this->db->join('property_transaction_tb as B','A.txn_id = B.transaction_id', 'left');
		$this->db->join('property_tb as C','C.id = B.property_id', 'left');
		$this->db->join('plan_tb as D','D.id = A.plan_id', 'left'); */
		$this->db->select('*');
		$this->db->from('transaction_tb');
		$this->db->where('user_id',$data['user_id']);
		$this->db->where('transactiondate >=',$data['from_date']);
		$this->db->where('transactiondate <=',$data['to_date']);
		$query = $this->db->get();
		// echo $this->db->last_query();
		return $result = $query->result_array();
	}
	public function get_contact_report($data){
		$query = $this->db->query("SELECT A.assets_id as sender_id, A.invite_id as receiver_id,(select first_name from registration_tb where assets_id = sender_id) as sender,(select first_name from registration_tb where assets_id = receiver_id) as receiver, B.title, B.description FROM invite_tb as A LEFT JOIN property_tb as B ON A.property_id = B.id WHERE A.entry_date >= '".$data['from_date']."' AND A.entry_date <= '".$data['to_date']."' AND A.assets_id = '".$data['user_id']."' OR A.invite_id = '".$data['user_id']."'");
		// echo $this->db->last_query();
		return $result = $query->result_array();
	}
	public function property_search_by_user($request){
		if($request['keyword'])
		 {
			 $this->db->like('title', $request['keyword']);
		 }
		if($request['city'])
		{
			$this->db->where('city', $request['city']);
		}
		if($request['property_type'])
		{
			$this->db->where('property_type', $request['property_type']);
		}	
		if($request['property_status'])
		{
			$this->db->where('property_status', $request['property_status']);
		}
		if($request['area'])
		{
			$this->db->where('address', $request['area']);
		}
		if($request['zip_code'])
		{
			$this->db->where('zip_code', $request['zip_code']);
		}	
		
			$this->db->select("*");
			$this->db->from('property_tb');
			$this->db->where('status', 1);
			$this->db->where('owner_id', $request['owner_id']);
			$query = $this->db->get();
			$property_data = $query->result_array();
		if($property_data)
		{
			return $property_data;
		} else{
			return false;
		}
    }
	public function add_bgv($data){
		return $result = $this->db->insert('background_verification_tb',$data);
	}
	public function update_bgv_document($data){
		$this->db->select('count(*) as count');
		$this->db->where('user_id',$data['user_id']);
		$query = $this->db->get('background_verification_tb');
		$res = $query->result();
		
		if($res[0]->count > 0){
			$bv['document'] = $data['document'];
			$this->db->where('user_id',$data['user_id']);
			return $this->db->update('background_verification_tb',$bv);
		}
		else
			return false;
		
	}
	public function find_bgv_document($data){
		$this->db->select('count(*) as count');
		$this->db->where('user_id',$data['user_id']);
		$this->db->where('document',$data['document']);
		$query = $this->db->get('background_verification_tb');
		$res = $query->result();
		if($res[0]->count > 0){
			$result = "www.assetswatch.com/uploads/".$data['document'];
			return $result;
		}else{
			return false;
		}
	}
	public function add_payment_details($data){
		return $result = $this->db->insert('payment_tb',$data);
	}
	public function add_card_profile($data){
		return $result = $this->db->insert('card_profile_tb',$data);
	}
	public function get_card_details($data){
		$this->db->select('*,count(id) as cnt');
		$this->db->where('user_id',$data['user_id']);
		$query = $this->db->get('card_profile_tb');
		$res = $query->result();
		if($res[0]->cnt > 0){
		$data = array(
			'name' 			=> $this->assetsapi_model->encrypt_decrypt('decrypt',$res[0]->name),
			'card_no' 		=> $this->assetsapi_model->encrypt_decrypt('decrypt',$res[0]->card_no),
			'exp_date' 		=> $this->assetsapi_model->encrypt_decrypt('decrypt',$res[0]->exp_date)
		);
		return $data;
		}
		else
			return false;
	}
	//---------Start Elakkiya on 11.08.2018---------------------
	public function get_agreement_content($data){
		$this->db->select('*');
		$this->db->where('deal_id',$data['deal_id']);
		$query = $this->db->get('property_deal_tb');
		$result = $query->row();
		if($result){
			return $result;
		} else{
			return false;
		}
	}
	public function agreement_acceptance($data){
		$result = $this->db->insert('agreement_version_tb',$data);
		return $result;

	}
	public function get_submitted_deal($data){
		$this->db->select('GROUP_CONCAT(DISTINCT deal_id) as deal_id');
		$this->db->where('sender_id',$data['user_id']);
		$query = $this->db->get('property_deal_tb');
		$result = $query->row();
		if(!empty($result->deal_id)){
			return $result;
		} else{
			return false;
		} 
	}
	public function fetch_submitted_deal($id){
		// $this->db->select('*');
		// $this->db->where('acceptance_status','1');
		// $this->db->where('deal_id',$id);
		// $query = $this->db->get('agreement_version_tb');
		// return $result = $query->row();
		$result = $this->get_agreement($id);
		return $result;
		
	}
	public function view_submitted_data($data){
		// $this->db->where('agreement_version_id',$data['id']);
		// $query = $this->db->get('agreement_version_tb');
		// $result = $query->row();
		// if(!empty($result->agreement_version_id)){
			// return $result;
		// } else{
			// return false;
		// } 
		$this->db->where('deal_id',$data['id']);
		$query = $this->db->get('property_deal_tb');
		$result = $query->row();
		if(!empty($result->deal_id)){
			return $result;
		} else{
			return false;
		} 
	}
	public function get_agreement($deal_id){
		// $this->db->select('A.*,C.agreement_title,C.header_content,C.header_image,C.watermark_image,C.footer_content,B.replaced_template');
		// $this->db->where('A.deal_id',$deal_id);
		// $this->db->from('agreement_version_tb as A');
		// $this->db->join('property_deal_tb as B','A.deal_id = B.deal_id','left');
		// $this->db->join('agreement_tb as C','C.agreement_id = B.agreement_id','left');
		$this->db->select('pd.*,C.agreement_title,C.header_content,C.header_image,C.watermark_image,C.footer_content');
		$this->db->where('pd.deal_id',$deal_id);
		$this->db->from('property_deal_tb as pd');
		$this->db->join('agreement_version_tb as av','av.deal_id = pd.deal_id','left');
		$this->db->join('agreement_tb as C','C.agreement_id = pd.agreement_id','left');
		$query = $this->db->get();
		//echo $this->db->last_query();
		return $result = $query->row();
	}	
	public function get_version_agreement($deal_id)
	{
		$this->db->select('version_name');
		$this->db->where('deal_id',$deal_id);
		$this->db->order_by('created_date','DESC');
		$query = $this->db->get('agreement_version_tb');
		return $result = $query->result_array();
	}
	
	//============================singular enroll payment user===============================
	public function storeEnrollinfo($request)
	{
		$dataToInsert = array(
					
					"login_user"=>$request["login_user"],
					"email"=>$request["email"],
					"dba_name"=>$request["dba_name"],
					"legal_name"=>$request["legal_name"],
					"bz_address"=>$request["business_address_line_1"],
					"bz_city"=>$request["city"],
					"bz_state"=>$request["state"],
					"bz_postal_code"=>$request["zip_code"],
					"bz_phone"=>$request["mobile_no"],
					"principal_fname"=>$request["first_name"],
					"principal_lname"=>$request["last_name"],
					"fed_tax_id"=>$request["fed_tax_id"],
					"created_on" => date('Y-m-d'),
					"enrollmentlink" => $request["enrollmentlink"],
					"clientpartnerid" => $request["clientpartnerid"]
					);
			$insert = $this->db->insert('enroll_submerchant_tb',$dataToInsert);
			if($insert)
			{
				return $this->enroll_info($request["login_user"]);
			}else{
				return false;
			}
		
	}
	public function enroll_info($user_id)
	{
				$query = $this->db->get_where('enroll_submerchant_tb',array('login_user'=>$user_id));
				return $query->result_array();
	}
	
	//==============================Unsubscribe plan================================================
	public function unsubscribe_plan($user_id,$assetsTypeId)
	{
		if($assetsTypeId==1){
			$plan = '1';
		}else if($assetsTypeId==2){
			$plan = '5';
		}else if($assetsTypeId==3){
			$plan = '9';
		}
		return $this->db->update('registration_tb',array('plan_id'=>$plan),array('assets_id'=>$user_id));
	}
	
	//==============================Notification UserList ================================================
	public function userlist_notification($user_id,$assets_type)
	{
			$query = $this->db->query("SELECT * from (select request_status,if(i.assets_id='".$user_id."',r1.assets_type,r.assets_type) as assets_type,if(i.assets_id='".$user_id."',CONCAT(r1.first_name,' ',r1.last_name), CONCAT(r.first_name,' ',r.last_name)) as name,if(i.assets_id='".$user_id."',r1.assets_id, r.assets_id) as profile_id FROM `invite_tb` i JOIN registration_tb r on i.assets_id=r.assets_id JOIN registration_tb r1 on i.invite_id=r1.assets_id where (i.assets_id='".$user_id."' or i.invite_id='".$user_id."')) as p where assets_type='".$assets_type."' and request_status=1");
			return $query->result_array();
			
			$where = "assets_type='".$request['assets_type']."' AND (first_name like '%".$request['keyword']."%' OR last_name like '%".$request['keyword']."%' OR zip_code like '%".$request['keyword']."%' OR email like '%".$request['keyword']."%' OR city like '%".$request['keyword']."%')";
		$this->db->select("assets_id as value,CONCAT(first_name,' ',last_name) as label");
		$query = $this->db->get_where('registration_tb',$where);
		$result = $query->result_array();
		
		
		
	}
	
	//============================get agreement template========================================================
	public function agreement_template_name(){
		$this->db->select('id as templateId, form_name as templateTitle,description as templateDescription,	header_content,footer_content,header_image,watermark_image');
		$this->db->where('status' ,'1');
		$query = $this->db->get('agreement_signature_form_tb');
		return $query->result_array();
	}
	//=========================agreemnet template by id==========================================================
	/* public function agreement_template_detail($template_id)
		{
			$this->db->select('id as templateId, description as templateDescription');
			$this->db->where('status' ,'1');
			$this->db->where('id' ,$template_id);
			$query = $this->db->get('agreement_signature_form_tb');
			return $query->result_array();
		} */
		
	//===============================contactinfo for homepage===================================================
	public function contactinfo()
		{
			$query = $this->db->get_where('contactinfo_tb',array('status'=>'1'));
			return $query->result_array();
		}
		
		//=============================contactinfo for homepage===================================================
	public function change_status_execute($agreementData)
		{
			$data_to_update = array('status'=>'Completed');
			return  $this->db->update('property_deal_tb',$data_to_update,$agreementData);
			 
		}
		
	//==============================rating details===============================
	public function rating_detail($id)
	{
		$this->db->select('id,owner_id,agent_id,rating,feedback,owner_agent_rating_tb.created_date as created_date,concat(first_name,"",last_name) as name,profile_photo');
		$this->db->join('registration_tb r','r.assets_id=owner_agent_rating_tb.owner_id','LEFT');
		$query = $this->db->get_where('owner_agent_rating_tb',array('agent_id'=>$id));
		
		return $query->result_array();
	}
	
//=======================swith user type=================================
		public function switch_usertype($request)
		{
			$this->db->select('assets_type,email,password');
			$this->db->from('registration_tb');
			$this->db->where('email',$request['email']);
			$this->db->where_not_in('assets_type', $request['assets_type']);
			$query = $this->db->get();
			$data =  $query->result_array();
			if(count($data)>0){
			foreach($data as $val)
			{
				$assets_type = $val['assets_type'];
				$password= $this->encrypt_decrypt('decrypt',$val['password']);
				$email = $val['email'];
				$dataToReturn[] = array(
					'assets_type'=>$assets_type,
					'password'=>$password,
					'email'=>$email,
				);
			}
			// return $dataToReturn;
			if(count($dataToReturn)>0)
			{
				return $dataToReturn;
			}else{
				return false;
			}
			}else{
				return false;
			}
		}
		
		//======================================
		public function top_rating_agents(){
			$this->db->select('A.agent_id,CONCAT(B.first_name," ",B.last_name) as name,B.profile_photo,B.mobile_no,B.email,B.city, SUM(A.rating)/COUNT(*) as rating');
			$this->db->from('owner_agent_rating_tb A');
			$this->db->join('registration_tb B',' A.agent_id = B.assets_id','LEFT');
			$this->db->group_by('agent_id');
			$this->db->order_by('rating','DESC');
			$this->db->limit(5);
			$query = $this->db->get();
			return $query->result();
		}
		
		public function getMerchantStatus(){
			$this->db->select('*');
			$this->db->from('enroll_submerchant_tb');
			$this->db->where('clientpartnerkey',null);
			$query = $this->db->get();
			return $query->result_array();
		}
		
		public function get_bgvinfo_by($login_userId,$report_userId){
			$this->db->select('*');
			$this->db->from('background_verification_tb');
			$this->db->where('login_user',$login_userId);
			$this->db->where('user_id',$report_userId);
			$query = $this->db->get();
			return $query->result_array();
		}
		
		public function getTransactionBy($invoiceId){
			$this->db->select("t.*,CONCAT(first_name,'',last_name) as name,city,state,country,zip_code");
			$this->db->from('transaction_tb T');
			$this->db->join('registration_tb R','T.user_id=R.assets_id','LEFT');
			$this->db->where('invoice_number',$invoiceId);
			$query = $this->db->get();
			return $query->result_array();
		}
}