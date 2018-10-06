<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
//header("Access-Control-Allow-Headers: *");

require APPPATH .'third_party/SingularApi-php/autoload.php';
// require APPPATH .'third_party/dompdf/dompdf_config.inc.php';
require_once APPPATH .'third_party/dompdf/autoload.inc.php';
require_once APPPATH .'third_party/tcpdf/tcpdf.php';
// uncomment below to enable debugging
 //SingularApi\Configuration::getDefaultConfiguration()->setDebug(TRUE);

require APPPATH."libraries/lib/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
class Assetsapi extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
    public function __construct() {
        parent::__construct();
         $this->load->library(array('session','facebook','MyCustomPDFWithWatermark','google','email'));//'google',
        //$this->load->library(array('session','facebook'));
        $this->load->database();
        $this->load->model('assetsapi_model');
        $this->load->helper(array('url','file'));
		$this->load->helper('download');
        $this->load->library('form_validation');
		$this->load->config('linkedin');
		 $this->load->config('twitter');
		  $this->load->config('singular_payment');
    }
	 
	public function index()
	{
		//google login url
         $data['googleloginURL'] = $this->google->loginURL();
         //linkedin
		 $data['linkedinURL'] = base_url().$this->config->item('linkedin_redirect_url').'?oauth_init=1';
		 $data['fblogoutURL'] = $this->facebook->logout_url();
          $data['fbauthURL'] =  $this->facebook->login_url();
		   $data['twitterUrl'] = base_url().'assetsapi/twitter';
        //load google login view
        $this->load->view('welcome_message',$data);
	}

//===========================================================LOGIN===============================================================================================	
    public function login()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$email = $request['email'];
		$hashPassword = $this->assetsapi_model->encrypt_decrypt('encrypt',$request['password']);
		$password = $hashPassword;
		$assets_type = $request['assets_type'];
		$userData = array(
			'email' => $email,
			'password' => $password,
			'assets_type' =>$assets_type
		);
		
		
        $result = $this->assetsapi_model->login($userData);
        $retres=array();
        if($result)
        { 
			if($result['status']=='0'){
				$retres['Success']=0;
				$retres['msg']='Your account is not activated.';
				$retres['userdata']=$result;					
			}else{
				$retres['Success']=1;
				if($result['plan_id']==null && $result['agentType']!='Service Provider')
				{
					$result2 = $this->assetsapi_model->plan_by_assetstype($result['assetsTypeId']);
					if($result2)
					{
						$retres['userdata']=$result;
						$retres['plan']= $result2;
					}else
					{  $retres['userdata']=$result;
						$retres['msg']='Login Successfully. No plan available.!!!';
					}
				}else{
					
					$retres['msg']='Login Successfully';	
					$retres['userdata']=$result;	
				}
			}
            
			
        }
        else
        {
			$rslt = $this->assetsapi_model->another_type_login($userData);
			if($rslt)
			{	
				$retres['Success']=2;
				// $assetsType = $this->assetsapi_model->getAssetsType($assets_type);
				// $retres['msg']='Do you want to continue as '.$assetsType.'?';
				// $retres['userdata']=$rslt;	
				// $retres['assetsType']=strtolower($assetsType);
				
				$query1 = $this->db->get_where('registration_tb',array('email'=>$request['email'],'password'=>$password));
				$rslt1 = $query1->result_array();
				// print_r($rslt1);
				foreach($rslt1 as $val)
				{
					// $type[] = $val['assets_type'];
					$type[] = $this->assetsapi_model->getAssetsType($val['assets_type']);
					// $assets_id[] = $val['assets_id'];
					// $session_id[] = $val['session_id'];
					
				}
				 if(count($type)>1){
					 // foreach($type as $tval){
						 
						// $assetsT[] = $tval." and "; 
					 // }
					 $separated = implode(" and ", $type);
					$assetsType  = $separated;
				}else{
					$assetsType = $type[0];
				}
				$retres['msg']='You have already registered as a '.$assetsType;
				$retres['userdata']=$rslt;	
				$retres['assetsType']=$type;
				// $retres['assets_id']=$assets_id;
				// $retres['session_id']=$session_id;
				// $registerData = $this->assetsapi_model->register($userData);
				// if($registerData){
					// if($result['agentType']!='Service Provider')
					// {
						// $result2 = $this->assetsapi_model->plan_by_assetstype($request['assets_type']);
						// if($result2)
						// {
							// $retres['plan']= $result2;
						// }else
						// {
							// $retres['msg']='Registered Successfully. No plan available.!!!';
						// }
					// }else{
						// $retres['msg']='Registered Successfully';
					// }
				// }
			}else{
					$retres['Success']=0;
					$retres['msg']='Invalid Email or Password';
				}
            
				
        }
		
        $retres=json_encode($retres);
        echo $retres;
    }


//===============================================================Register Start====================================================================================		
    public function register()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		
		$email = $request['email'];
		$assets_type = $request['assets_type'];
		$sqlcont="SELECT * FROM registration_tb WHERE (email = '$email' AND assets_type = '$assets_type')";
		$query =$this->db->query($sqlcont);
		$result=$query->result_array();
		if(count($result)==0){
			if($request['owner_type']==2)
			{
				$company_name = $request['company_name'] ;
				$website_url =  $request['website_url'] ;
			}else
			{
				$company_name = '';
				$website_url =  '';
			}
				
			if($request['assets_type']==2)
			{
				$agent_type = isset($request['agent_type']) ? $request['agent_type'] : '';
				
			}else
			{
				$agent_type = '';
				
			}
			
			$randNumber = mt_rand(100000, 999999);
				$hashPassword = $this->assetsapi_model->encrypt_decrypt('encrypt',$request['password']);
			$userData = array(
				'owner_type' => $request['owner_type'],
				'first_name' => $request['first_name'],
				'last_name' => $request['last_name'],
				'email' => $request['email'],
				'password' => $hashPassword,
				'city' => $request['city'],
				'state' => $request['state'],
				'country' => $request['country'],
				'zip_code' => $request['zip_code'],
				'mobile_no' => $request['mobile_no'],
				'landline_no' => $request['landline_no'],
				'assets_type' => $request['assets_type'],
				'company_name' => $company_name,
				'website_url' => $website_url,
				'agent_type' =>$agent_type,
				'account_id' =>$randNumber,
				'status' =>0
			);
			$result = $this->assetsapi_model->register($userData);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['msg']='Registered Successfully';
				 $retres['user']= $result;
				
				$assets_id = $result['assets_id'];
				$result1 = $this->assetsapi_model->profile($assets_id);
				
				
				$assets_type = $result1['assets_type'];
				
				$fullName = $result1['first_name']." ".$result1['last_name'];
				$to_email = $result1['email'];
				
				
				if($result['agentType']!='Service Provider')
				{
					$result2 = $this->assetsapi_model->plan_by_assetstype($request['assets_type']);
					if($result2)
					{
						$retres['plan']= $result2;
					}else
					{
						$retres['msg']='Registered Successfully. No plan available.!!!';
						//========================Mail=================================================
						$to_email = $email;
						$from_email = "info@assetswatch.com";
						$RegFilename = 'registration_template.txt';
						$RegTemplate = read_file('assets/email_template/'.$RegFilename);
						$RegSubject = "Registration";
						
						$reacturl = $this->config->item('reacturl');
						$url = "<br/><a href='".$reacturl."' style='text-decoration: none;color: #FFFFFF;font-size: 17px;font-weight: 400;line-height: 120%;padding: 9px 26px;margin: 0;text-decoration: none;border-collapse: collapse;border-spacing: 0;border-radius: 4px;-webkit-border-radius: 4px;text-align: -webkit-center;vertical-align: middle;background-color: rgb(87, 187, 87);-moz-border-radius: 4px;-khtml-border-radius: 4px;'>Login</a>";

						$RegTokensArr = array(
						'USER_NAME' => $fullName,
						 'USER_EMAIL'=> $to_email,
						 'URL'=> trim($url)
						);
						$RegMail = $this->send_mail($from_email,$to_email,$RegSubject,$RegTokensArr,$RegTemplate);
						if($RegMail){
							$update = $this->db->update('registration_tb',array('status'=>1),array('assets_id'=>$assets_id));
						}
				// ========================Mail======================================================================
					}
				}else{
					$retres['msg']='Registered Successfully';
				}
				
			}
			else
			{
				$retres['success']=0;            
			}
		}else{
			$retres['success']=0;
			$retres['msg']='Email Already Exist, Please Try Again !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
//===============================================================Register End====================================================================================		
//=================================================================================================================================================================	


//=================================================================== Testimonial Start==============================================================================
    public function testimonial()
    {
		$result = $this->assetsapi_model->testimonial();
		if($result){
			$retres['success']=1;
			$retres['testimonial']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No testimonial found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
//=================================================================== Testimonial End==============================================================================
//=================================================================================================================================================================	


//==============================================================Blog Start===================================================================================	
    public function blog()
    {
		//$filename = 'blog.txt';
		//$result = read_file('textfiles/'.$filename);
		$result = $this->assetsapi_model->blog();
		if($result){
			$retres['success']=1;
			$retres['blog']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No blog found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	
    public function blog_details($id)
    {
		$result = $this->assetsapi_model->blog_details($id);
		if($result){
			$retres['success']=1;
			$retres['blog']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No blog found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	
    public function blog_views_update()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$blog_id = $request['blog_id'];
		$result = $this->assetsapi_model->blog_views_update($blog_id);
		$retres=array();
		if($result)
		{
			$retres['success']=1;
			$retres['msg']="Blog view count added successfully";
			$retres['blog']=$result;
		}
		else
		{
			$retres['success']=0;
			$retres['msg']='Something went wrong. Please try again !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	
    public function blog_comment_insert()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$blogCmtData = array(
			'blog_id' => $request['blog_id'],
			'name' => $request['name'],
			'email' => $request['email'],
			'status' => 1,
			'comment' => $request['comment']
		);
		$result = $this->assetsapi_model->blog_comment_insert($blogCmtData);
		$retres=array();
		if($result)
		{
			$retres['success']=1;
			$retres['msg']="Blog comment added successfully";
			$retres['blog_comment']=$result;
		}
		else
		{
			$retres['success']=0;
			$retres['msg']='Something went wrong. Please try again !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	
    public function blog_comment_tb($id)
    {
		$result = $this->assetsapi_model->blog_comment_tb($id);
		if($result){
			$retres['success']=1;
			$retres['blog']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No blog comments found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
//========================================================Blog End=====================================================================================	
//=================================================================================================================================================================	


//=================================================================Advertisement Start==========================================================================	
	public function advertisement()
    {
		$result = $this->assetsapi_model->advertisement();
		if($result){
			$retres['success']=1;
			$retres['advertisement']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No advertisement found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
//=================================================================Advertisement End==========================================================================	
//=============================================================================================================================================================		


//================================================================Property Start======================================================================	
	public function add_property()
    {
		$request = json_decode(file_get_contents('php://input'), true);
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
			'owner_details' => $request['owner_details'],
			'img_path' => $request['img_path'],
			'agent_perc'=>$request['agent_perc']
			
		);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->add_property($propertyData);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['msg']="Property added successfully !!!";
				$retres['property']=$result;
			}
			else
			{
				$retres['success']=0;
				$retres['msg']='Something went wrong. Please try again !!!';
			}
		}else
			{
				$retres['success']=0;
				$retres['msg']='Unauthorised access !!!';
			}
        $retres=json_encode($retres);
        echo $retres;
    }
	
	public function edit_property()
    {
		$request = json_decode(file_get_contents('php://input'), true);
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
			'owner_details' => $request['owner_details'],
			'img_path' => $request['img_path'],
			'property_id' => $request['property_id'],
			'agent_perc'=>$request['agent_perc'],
			'status' => 1,
			
		);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		
		if($validate)
		{
			$result = $this->assetsapi_model->edit_property($propertyData);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['msg']="Property edited successfully !!!";
				$retres['property']=$result;
			}
			else
			{
				$retres['success']=0;
				$retres['msg']='Something went wrong. Please try again !!!';
			}
		}else
		{
			$retres['success']=0;
			$retres['msg']='Unauthorised access !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	
	public function delete_property()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$propertyData = array(
			'property_id' => $request['property_id']
		);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->delete_property($propertyData);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['msg']='Property deleted successfully !!!';
			}
			else
			{
				$retres['success']=0;
				$retres['msg']='Something went wrong. Please try again !!!';
			}
	}else
			{
				$retres['success']=0;
				$retres['msg']='Unauthorised access !!!';
			}
        $retres=json_encode($retres);
        echo $retres;
    }
	
    public function property()
    {
		$result = $this->assetsapi_model->property();
		$retres=array();
		if($result){
			$retres['success']=1;
			$retres['property']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No property found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	public function property_by($id,$session_id)
    {
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->property_by($id);
			$retres=array();
			if($result){
				$retres['success']=1;
				$retres['property']=$result;
			}else{
				$retres['success']=0;
				$retres['msg']='No property found !!!';
			}
		}else{
			$retres['success']=0;
			$retres['msg']='Unauthorised access !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	
    public function property_details($id)
    {
		$result = $this->assetsapi_model->property_details($id);
		$retres=array();
		if($result){
			$retres['success']=1;
			$retres['property']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No property found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	/* Property  End*/
	
	/* Property Search start*/
	 
	 public function property_search()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$keyword = isset($request['keyword']) ? $request['keyword'] : '';
		$property_type = isset($request['property_type']) ? $request['property_type'] : '';
		$city = isset($request['city']) ? $request['city'] : '';
		$property_status = isset($request['property_status']) ? $request['property_status'] : '';
		$area = isset($request['area']) ? $request['area'] : '';
		$min_price = isset($request['min_price']) ? $request['min_price'] : '';
		$max_price = isset($request['max_price']) ? $request['max_price'] : '';
		
		$propertyData = array(
			'keyword' => $keyword,
			'city' => $city,
			'property_type' => $property_type,
			'property_status' => $property_status,
			'area' => $area,
			'min_price' => $min_price,
			'max_price' => $max_price,
			'status' => 1,
			
		);
		$result = $this->assetsapi_model->property_search($propertyData);
		$retres=array();
		if($result){
			$retres['success']=1;
			$retres['property_search']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No property found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	 /* Property Search End*/
	 
	  /*  Statics Count Start*/
	  
	public function statics_count()
    {
		$result = $this->assetsapi_model->statics_count();
		$retres=array();
		if($result){
			$retres['success']=1;
			$retres['statics_count']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No statics found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	   /*  Statics Count End*/
	 
	 /* property status Type */
	public function property_status()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$retres=array();
		$propertyStatus = array(
			'property_status' => $request['property_status'],
			);
		$result = $this->assetsapi_model->property_status($propertyStatus);
		if($result){
			$retres['success']=1;
			$retres['property_status']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No property found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	 /* property status Type End */
//==============================================================Property End========================================================================	
//=============================================================================================================================================================	
	 
	 /*  Our Agent */
	public function our_agent()
    {
		$result = $this->assetsapi_model->our_agent();
		$retres=array();
		if($result){
			$retres['success']=1;
			$retres['our_agent']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='No agent found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	
	public function contact()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$contactData = array(
			'name' => $request['name'],
			'email' => $request['email'],
			'phone' => $request['phone'],
			'subject' => $request['subject'],
			'message' => $request['message']
			
			
		);
		$result = $this->assetsapi_model->contact($contactData);
		$retres=array();
		if($result){
			$retres['success']=1;
			$retres['contact']=$result;
		}else{
			$retres['success']=0;
			$retres['msg']='Some error found !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	  /*  Our Agent end*/
	 

	/*  Profile Start*/
	public function profile($id,$session_id)
    { 
		//$SESSION_ID = $this->session->userdata('session_id');
		//$seesid = $SESSION_ID."::".$session_id;
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		$retres=array();
		if($validate)
		{
			$result = $this->assetsapi_model->profile($id);
			if($result){
				$retres['success']=1;
				$retres['profile']=$result;
			}else{
				$retres['success']=0;
				$retres['msg']='No profile found !!!';
			}
		}else{
			$retres['success']=0;
			$retres['msg']='Unauthorised User !!!';
		}
			$retres=json_encode($retres);
			echo $retres;
	}
	
	public function statics_count_by($id,$session_id)
    { 
		//$SESSION_ID = $this->session->userdata('session_id');
		//$seesid = $SESSION_ID."::".$session_id;
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		$retres=array();
		if($validate)
		{
			$result = $this->assetsapi_model->statics_count_by($id);
			if($result){
				$retres['success']=1;
				$retres['statics']=$result;
			}else{
				$retres['success']=0;
				$retres['msg']='No statics found !!!';
			}
		}else{
				$retres['success']=0;
				$retres['msg']='Unauthorised User !!!';
			}
        $retres=json_encode($retres);
        echo $retres;
	}
	public function profile_contact_list($id,$session_id)
    {
		//$SESSION_ID = $this->session->userdata('session_id');
		//$seesid = $SESSION_ID."::".$session_id;
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		$retres=array();
		if($validate)
		{
			$result = $this->assetsapi_model->profile_contact_list($id);
			if($result){
				$retres['success']=1;
				$retres['contactlist']=$result;
			}else{
				$retres['success']=0;
				$retres['msg']='No data found !!!';
			}
		}else{
				$retres['success']=0;
				$retres['msg']='Unauthorised User !!!';
			}
        $retres=json_encode($retres);
        echo $retres;
	}
	public function recent_added_property($id,$session_id)
    {
		//$SESSION_ID = $this->session->userdata('session_id');
		//$seesid = $SESSION_ID."::".$session_id;
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		$retres=array();
		if($validate)
		{
			$result = $this->assetsapi_model->recent_added_property($id);
			if($result){
				$retres['success']=1;
				$retres['property']=$result;
			}else{
				$retres['success']=0;
				$retres['msg']='No statics found !!!';
			}
		}else{
				$retres['success']=0;
				$retres['msg']='Unauthorised User !!!';
			}
        $retres=json_encode($retres);
        echo $retres;
	}
	/*  Profile End*/	
	
	 	// =====================================Settings Start=====================================================================
	 public function setting_profile()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		
		//$SESSION_ID = $this->session->userdata('session_id');
		// $seesid = $SESSION_ID."::".$request['session_id'];
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			
			$userData = array(
					'assets_id'=>$request['assets_id'],
					'first_name' => $request['first_name'],
					'last_name' => $request['last_name'],
					'email' => $request['email'],
					// 'password' => $request['password'],
					'city' => $request['city'],
					'state' => $request['state'],
					'country' => $request['country'],
					'zip_code' => $request['zip_code'],
					'mobile_no' => $request['mobile_no'],
					'landline_no' => $request['landline_no'],
					'assets_type' => $request['assets_type'],
					'owner_type'=>$request['owner_type'],
					'profile_photo' => $request['profile_photo'],
					'about_us' => $request['about_us'],
					'facebook_link' => $request['facebook_link'],
					'twitter_link' => $request['twitter_link'],
					'linkedin_link'=>$request['linkedin_link'],
					'SSN_EIN'=>$request['SSN_EIN'],
					'dob'=>$request['dob'],
					'gender'=>$request['gender']
				);
			$result = $this->assetsapi_model->setting_profile($userData);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['msg']="Profile edited successfully !!!";
				$retres['profile']=$result;
			}
			else
			{
				$retres['success']=0;
				$retres['msg']='Something went wrong. Please try again !!!';
				
			}
		}else{
			$retres['success']=0;
			$retres['msg']='Unauthorised User !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
    }
	
	  /* Settings Edit End */
	  
	  /* Settings  Password Edit */
	  public function setting_password()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$passwordData = array(
				'assets_id'=>$request['assets_id'],
				'old_password' => $request['old_password'],
				'new_password' => $request['new_password'],
				'confirm_password' => $request['confirm_password'],
				'email' => $request['email']
				
				
			);
		//$SESSION_ID = $this->session->userdata('session_id');
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
				$query = $this->db->get_where('registration_tb', array('assets_id' => $request['assets_id']));
				$data = $query->result_array();

				$old_password =  $this->assetsapi_model->encrypt_decrypt('decrypt',$data[0]['password']);
				if($old_password==$request['old_password'])
				{
					if($request['new_password']==$request['confirm_password'])
					{
						$result = $this->assetsapi_model->setting_password($passwordData);
						$retres=array();
						if($result)
						{
							$retres['success']=1;
							$retres['msg']="Password changed successfully !!!";
							$retres['profile']=$result;
						}
						else
						{
							$retres['success']=0;
							$retres['msg']='Something went wrong. Please try again !!!';
						}
					}else
					{
						$retres['success']=0;
						$retres['msg']='New password not matched !!!';
					}
					
				}else
					{
						$retres['success']=0;
						$retres['msg']='old password not matched !!!';
					}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
		$retres=json_encode($retres);
			echo $retres;
		
    }
	// =====================================Settings End=====================================================================
	 
	 
		// =====================================Notification Start=====================================================================
	public function notification($receiver,$session_id)
    {
		// $request = json_decode(file_get_contents('php://input'), true); 
		
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->notification($receiver);
			if($result){
				$retres['success']=1;
				$retres['notification']=$result;
			}else{
				$retres['success']=0;
				$retres['msg']='No notification found !!!';
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
        $retres=json_encode($retres);
        echo $retres;
	}
	public function notification_alert($receiver,$session_id)
    {
		// $request = json_decode(file_get_contents('php://input'), true); 
		
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->notification($receiver);
			if($result){
				$retres['success']=1;
				$retres['notification']=$result;
			}else{
				$retres['success']=0;
				$retres['msg']='No notification found !!!';
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
        $retres=json_encode($retres);
        echo $retres;
	}
	public function send_notification()
    {
	 $request = json_decode(file_get_contents('php://input'), true);
		$notificationData = array(
				'sender'=>$request['sender'],
				'receiver' => $request['receiver'],
				'assets_type' => $request['assets_type'],
				'message' => $request['message']
				
			);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->send_notification($notificationData);
				
				$retres=array();
				if($result)
				{
					$retres['success']=1;
					$retres['msg']="Notification send successfully !!!";
					$retres['notification']=$result;
				}
				else
				{
					$retres['success']=0;
					$retres['msg']='Something went wrong. Please try again !!!';
				}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
				$retres=json_encode($retres);
				echo $retres;
		
	}
	public function delete_notification($notify_id,$session_id)
    {
	 // $request = json_decode(file_get_contents('php://input'), true);
		// $NotifyData = array(
			// 'notify_id' =>  $request['notify_id']
		// );
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->delete_notification($notify_id);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['msg']='Notification deleted successfully !!!';
			}
			else
			{
				$retres['success']=0;
				$retres['msg']='Something went wrong. Please try again !!!';
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
        $retres=json_encode($retres);
        echo $retres;
		
	}
	// =====================================Delete Notification End=================================================================
	// =====================================Notification End=====================================================================
	
	
	// =====================================Agent/Tenant/Owner Start=====================================================================
	// =====================================dropdown for invite request start=====================================================================
	public function invite_request($userid,$assets_type,$session_id)
    {
		// $request = json_decode(file_get_contents('php://input'), true);
		// $requestData = array(
				
				// 'assets_type' => $request['assets_type']
				
			// );
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->invite_request($userid,$assets_type);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				//$retres['msg']="Invitation Accepted successfully.";
				$retres['invitation']=$result;
			}
			else{
				$retres['success']=0;
				$retres['msg']='No data found !!!';
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	// =====================================dropdown for invite request end=====================================================================
	
	
	// =====================================Send Invite Request Start=====================================================================
	public function invite()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$inviteData = array(
				'assets_id'=>$request['assets_id'],
				'invite_id' => $request['invite_id'],
				'message' => $request['message'],
				'property_id'=>$request['property_id']
				
			);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$sql = "SELECT * FROM invite_tb WHERE ((assets_id='".$request['assets_id']."' AND invite_id='".$request['invite_id']."' ) OR (assets_id='".$request['invite_id']."' AND invite_id='".$request['assets_id']."')) AND request_status=1";
			$query=$this->db->query($sql);
			$checkDuplicate = $query->result();
			
			$sql2 = "SELECT * FROM invite_tb WHERE ((assets_id='".$request['assets_id']."' AND invite_id='".$request['invite_id']."' ) OR (assets_id='".$request['invite_id']."' AND invite_id='".$request['assets_id']."')) AND request_status=0";
		
			$query2=$this->db->query($sql2);
			$checkDuplicate2 = $query2->result();
			
			if(count($checkDuplicate)==0)
			{
				if(count($checkDuplicate2)==1)
				{
					$updateData = array(
						'request_status'=>1
						
						
					);
					$this->db->update('invite_tb',$updateData);
					$query = $this->db->get('invite_tb');
					$result = $query->result_array();
					$retres=array();
					if($result)
					{
						$retres['success']=1;
						$retres['msg']="Now you both are connected. !!!";
						$retres['invitation']=$result;
					}
				}
				else
				{
					$result = $this->assetsapi_model->invite($inviteData);
					$retres=array();
					if($result)
					{
						$retres['success']=1;
						$retres['msg']="Invitation send successfully. !!!";
						$retres['invitation']=$result;
					}
				}
			}else
			{
				$retres['success']=0;
				$retres['msg']='User already connected. !!!';
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
			
				$retres=json_encode($retres);
				echo $retres;
		
	}
	// =====================================Send Invite Request End=====================================================================
		
	// =====================================Accept Invite Request Start=====================================================================
	public function invite_accept($assets_id,$invite_id,$session_id)
    {
		// $request = json_decode(file_get_contents('php://input'), true);
		// $inviteData = array(
				// 'assets_id'=>$request['assets_id'],
				// 'invite_id' => $request['invite_id']
				// );
	$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
				$result = $this->assetsapi_model->invite_accept($assets_id,$invite_id);
				
				$retres=array();
				if($result)
				{
					$retres['success']=1;
					$retres['msg']="Invitation Accepted successfully.";
					$retres['invitation']=$result;
				}
				else{
					$retres['success']=0;
					$retres['msg']='Something went wrong. Please try again !!!';
						
				}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
			
			$retres=json_encode($retres);
			echo $retres;
	}
		// =====================================Accept Invite Request End=====================================================================
		
	// =====================================Requested Owner/Agent/Tenant Start/===================================================================== 
	  
	public function requested()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$inviteData = array(
				'user_id'=>$request['user_id'],
				'assets_type' => $request['assets_type']
				
			);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->requested($inviteData);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['requested']=$result;
			}
			else{
				$retres['success']=0;
				$retres['msg']='No Data Found !!!';
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	// =====================================Requested Owner/Agent/Tenant Start/=====================================================================
	
		// =====================================Joind Owner/Agent/Tenant Start/=====================================================================
	public function joined()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$inviteData = array(
				'user_id'=>$request['user_id'],
				'assets_type' => $request['assets_type']
				
			);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->joined($inviteData);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['joined']=$result;
			}
			else{
				$retres['success']=0;
				$retres['msg']='No Data Found !!!';
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	// =====================================Joind Owner/Agent/Tenant End/=====================================================================	
	
	public function send_message()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$msgData = array(
				'sender'=>$request['sender'],
				'receiver' => $request['receiver'],
				'message' => $request['message']
				
			);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->send_message($msgData);
			$retres=array();
					if($result)
					{
						$retres['success']=1;
						$retres['msg']="Message send successfully !!!";
						$retres['notification']=$result;
					}
					else
					{
						$retres['success']=0;
						$retres['msg']='Something went wrong. Please try again !!!';
					}
			}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
				$retres=json_encode($retres);
				echo $retres;
	}
	// =====================================Agent/Tenant/Owner End=====================================================================
	
	// =====================================Service Start=====================================================================
	 public function service_request($userid,$session_id)
	 {
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->service_request($userid);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				//$retres['msg']="Service request send successfully.";
				$retres['service']=$result;
			}
			else{
				$retres['success']=0;
				$retres['msg']='Data not found !!!';
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}	
			$retres=json_encode($retres);
			echo $retres;
	}
	
	public function service_request_send()
    {
		$request = json_decode(file_get_contents('php://input'), true);
		$serviceData = array(
				'property_id'=>$request['property_id'],
				'send_by' => $request['send_by'],
				'service_provider' => $request['service_provider'],
				'service_msg' => $request['service_msg'],
				'service_photo' => $request['service_photo']
				
			);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->service_request_send($serviceData);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['msg']="Service request send successfully.";
				$retres['service']=$result;
			}
			else{
				$retres['success']=0;
				$retres['msg']='Something went wrong. Please try again !!!';
					$retres['service']=$result;
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}	
			$retres=json_encode($retres);
			echo $retres;
	}
	
	 public function service_send($userid,$session_id)
	 {
		 $validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->service_send($userid);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				//$retres['msg']="Service request send successfully.";
				$retres['service']=$result;
			}
			else{
				$retres['success']=0;
				$retres['msg']='Data not found !!!';
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	public function service_resolve($userid,$session_id)
	 {
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->service_resolve($userid);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				//$retres['msg']="Service request send successfully.";
				$retres['service']=$result;
			}
			else{
				$retres['success']=0;
				$retres['msg']='Data not found !!!';
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	public function service_detail($serviceid,$session_id)
	{
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->service_detail($serviceid);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				//$retres['msg']="Service request send successfully.";
				$retres['service']=$result;
			}
			else{
				$retres['success']=0;
				$retres['msg']='Data not found !!!';
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	public function service_requested($userid,$session_id)
	{
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->service_requested($userid);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				//$retres['msg']="Service request send successfully.";
				$retres['service']=$result;
			}
			else{
				$retres['success']=0;
				$retres['msg']='Data not found !!!';
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']='Unauthorised User !!!';
			}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	// =====================================Service End=====================================================================
	// =====================================Portal Content Start====================================================================
	public function portal_content($tag)
    {
		//$query = $this->db->get_where('portal_content_tb',array('tag'=>$tag));
		//$result = $query->result_array();
		$filename = $tag.'.txt';
		$result = file_get_contents('assetsadmin/textfiles/'.$filename);
		
		//$result = $query->result_array();

		if($result)
		{
			$retres['success']=1;
			//$retres['msg']="Service request send successfully.";
			$retres['portal_content']=json_decode($result);
		}
		else{
			$retres['success']=0;
			$retres['msg']='Data not found !!!';
				
		}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	// =====================================Portal Content End====================================================================
	
	//===========================================Logout============================================================================
	public function signout()
    {
		$this->session->unset_userdata('SESS_MEMBER_ID');
		$this->session->sess_destroy();
		 $this->facebook->destroy_session();
        // Remove user data from session
        $this->session->unset_userdata('userData');
		//redirect('login');
		$retres['success']=1;
		$retres['msg']="Signout successfully.";
			
	}
	//========================================Logout end=======================================================================
	
	//========================================Social Login start======================================================================
	
	public function google()
    {
		if(isset($_GET['code'])){
			
            //authenticate user
            $this->google->getAuthenticate();
            
            //get user info from google
            $gpInfo = $this->google->getUserInfo();
            
            //preparing data for database insertion
            $userData['oauth_provider'] = 'google';
            $userData['oauth_uid']      = $gpInfo['id'];
            $userData['first_name']     = $gpInfo['given_name'];
            $userData['last_name']      = $gpInfo['family_name'];
            $userData['email']          = $gpInfo['email'];
            //$userData['gender']         = !empty($gpInfo['gender'])?$gpInfo['gender']:'';
           // $userData['locale']         = !empty($gpInfo['locale'])?$gpInfo['locale']:'';
            //$userData['profile_url']    = !empty($gpInfo['link'])?$gpInfo['link']:'';
           // $userData['picture_url']    = !empty($gpInfo['picture'])?$gpInfo['picture']:'';
            
            //insert or update user data to the database
            // $userID = $this->assetsapi_model->checkUser($userData);
            
            //store status & user info in session
            // $this->session->set_userdata('loggedIn', true);
            // $this->session->set_userdata('userData', $userData);
            
            //redirect to profile page
			$reacturl = $this->config->item('reacturl').'social?oauth_provider="'.$userData["oauth_provider"].'"&oauth_uid="'.$userData["oauth_uid"].'"&first_name="'.$userData["first_name"].'"&last_name="'.$userData["last_name"].'"&email="'.$userData["email"].'"';
           redirect($reacturl);
        } 
		//google login url
		$loginURL = $this->google->loginURL();
		
		if(!empty($loginURL)){
			redirect($loginURL);
		}
        //redirect('assetsapi','refresh');
        //google login url
        //$data['loginURL'] = $this->google->loginURL();
        
        //load google login view
        //$this->load->view('welcome_message',$data);
		
		// $retres=array();
		// if($userID)
		// {
			// $retres['success']=1;
			// $retres['msg']="Login successfully.";
			// $retres['googlelogin']=$userID;
		// }
		// else{
			// $retres['success']=0;
			// $retres['msg']='Something went wrong. Please try again !!!';
				
		// }
			
			// $retres=json_encode($retres);
			// echo $retres;
	}
	public function social_login(){
		$request = json_decode(file_get_contents('php://input'), true);
		
		$email = $request['email'];
		$assets_type = $request['assets_type'];
		$oauth_provider = $request['oauth_provider'] ;
		$oauth_uid =  $request['oauth_uid'] ;
		
		$sqlcont="SELECT * FROM registration_tb WHERE ((oauth_provider = '$oauth_provider' AND oauth_uid = '$oauth_uid') AND assets_type = '$assets_type')";
		$query =$this->db->query($sqlcont);
		$result=$query->result_array();
		if(count($result)==0){
			if($request['owner_type']==2)
			{
				$company_name = $request['company_name'] ;
				$website_url =  $request['website_url'] ;
			}else
			{
				$company_name = '';
				$website_url =  '';
			}
				
			if($request['assets_type']==2)
			{
				$agent_type = isset($request['agent_type']) ? $request['agent_type'] : '';
				
			}else
			{
				$agent_type = '';
				
			}
			
			$randNumber = mt_rand(100000, 999999);
				$hashPassword = $this->assetsapi_model->encrypt_decrypt('encrypt',$request['password']);
			$userData = array(
				'owner_type' => $request['owner_type'],
				'first_name' => $request['first_name'],
				'last_name' => $request['last_name'],
				'email' => $request['email'],
				'password' => $hashPassword,
				'city' => $request['city'],
				'state' => $request['state'],
				'country' => $request['country'],
				'zip_code' => $request['zip_code'],
				'mobile_no' => $request['mobile_no'],
				'landline_no' => $request['landline_no'],
				'assets_type' => $request['assets_type'],
				'company_name' => $company_name,
				'website_url' => $website_url,
				'agent_type' =>$agent_type,
				'account_id' =>$randNumber,
				'oauth_provider' => $oauth_provider,
				'oauth_uid' =>  $oauth_uid,
				'status' => '0'
			);
			$result = $this->assetsapi_model->register($userData);
			$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['msg']='Registered Successfully';
				 $retres['user']= $result;
				
				$assets_id = $result['assets_id'];
				$result1 = $this->assetsapi_model->profile($assets_id);
				
				
				$assets_type = $result1['assets_type'];
				
				$fullName = $result1['first_name']." ".$result1['last_name'];
				/* $to_email = $result1['email'];
				
				$from_email = "info@assetswatch.com";
				//========================Mail======================================================================
				$filename = 'registration_template.txt';
				$template = read_file('assets/email_template/'.$filename);
				$subject = "Registration";
				
				$tokensArr = array(
				'USER_NAME' => $fullName,
				 'USER_EMAIL'=> $to_email
				);
				$mail = $this->send_mail($from_email,$to_email,$subject,$tokensArr,$template); */
				// ========================Mail======================================================================
				// if($mail)
				// {
					// echo "send";
					
				// }else{
					// echo "not send ::".$this->email->print_debugger(FALSE);
					
				// }
				
				if($result['agentType']!='Service Provider')
				{
					$result2 = $this->assetsapi_model->plan_by_assetstype($request['assets_type']);
					if($result2)
					{
						$retres['plan']= $result2;
					}else
					{
						$retres['msg']='Registered Successfully. No plan available.!!!';
					}
				}else{
					$retres['msg']='Registered Successfully';
				}
				
			}
			else
			{
				$retres['success']=0;            
			}
		}else{
			$retres['success']=0;
			$retres['msg']='Email Already Exist, Please go to login tab !!!';
		}
        $retres=json_encode($retres);
        echo $retres;
	}
	public function linkedin()
	{
	
		$userData = array();
        
        //Include the linkedin api php libraries
        include_once APPPATH."libraries/Linkedin/http.php";
		include_once APPPATH."libraries/Linkedin/oauth_client.php";

        
        
        //Get status and user info from session
        //$oauthStatus = $this->session->userdata('oauth_status');
        //$sessUserData = $this->session->userdata('userData');
         //print_r($sessUserData);
        //if(isset($oauthStatus) && $oauthStatus == 'verified'){
            //User info from session
			//print_r($sessUserData);
           // $userData = $sessUserData;
        //}else
			if((isset($_REQUEST["oauth_init"]) && $_REQUEST["oauth_init"] == 1) || (isset($_REQUEST['oauth_token']) && isset($_REQUEST['oauth_verifier']))){
            $client = new oauth_client_class;
            $client->client_id = $this->config->item('linkedin_api_key');
            $client->client_secret = $this->config->item('linkedin_api_secret');
            $client->redirect_uri = base_url().$this->config->item('linkedin_redirect_url');
            $client->scope = $this->config->item('linkedin_scope');
            $client->debug = false;
            $client->debug_http = true;
            $application_line = __LINE__;
            
            //If authentication returns success
            if($success = $client->Initialize()){
                if(($success = $client->Process())){
                    if(strlen($client->authorization_error)){
                        $client->error = $client->authorization_error;
                        $success = false;
                    }elseif(strlen($client->access_token)){
                        $success = $client->CallAPI('http://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name,location,picture-url,public-profile-url,formatted-name)', 
                        'GET',
                        array('format'=>'json'),
                        array('FailOnAccessError'=>true), $userInfo);
                    }
                }
                $success = $client->Finalize($success);
            }
            
            if($client->exit) exit;
    
            if($success){
                //Preparing data for database insertion
                $first_name = !empty($userInfo->firstName)?$userInfo->firstName:'';
                $last_name = !empty($userInfo->lastName)?$userInfo->lastName:'';
                $userData = array(
                    'oauth_provider'=> 'linkedin',
                    'oauth_uid'     => $userInfo->id,
                    'first_name'     => $first_name,
                    'last_name'     => $last_name,
                    'email'         => $userInfo->emailAddress,
                    /*'locale'         => $userInfo->location->name,
                    'profile_url'     => $userInfo->publicProfileUrl,
                    'picture_url'     => $userInfo->pictureUrl*/
               );
                
                //Insert or update user data
                //$userID = $this->assetsapi_model->checkUser($userData);
                
                //Store status and user profile info into session
                $this->session->set_userdata('oauth_status','verified');
                 $this->session->set_userdata('userData',$userData);
                
                //Redirect the user back to the same page
                //redirect('/user_authentication');
				$reacturl = $this->config->item('reacturl').'social?oauth_provider="'.$userData["oauth_provider"].'"&oauth_uid="'.$userData["oauth_uid"].'"&first_name="'.$userData["first_name"].'"&last_name="'.$userData["last_name"].'"&email="'.$userData["email"].'"';
				redirect($reacturl);

            }else{
                 $data['error_msg'] = 'Some problem occurred, please try again later!';
            }
        }elseif(isset($_REQUEST["oauth_problem"]) && $_REQUEST["oauth_problem"] <> ""){
            $data['error_msg'] = $_GET["oauth_problem"];
        }else{
            $data['linkedinURL'] = base_url().$this->config->item('linkedin_redirect_url').'?oauth_init=1';
        }
        
        $data['userData'] = $userData;
        
        // Load login & profile view
		 // $retres=array();
		// if($userID)
		// {
			// $retres['success']=1;
			// $retres['msg']="Linkedin login successfully.";
			// $retres['linkedin']=$userID;
		// }
		// else{
			// $retres['success']=0;
			// $retres['msg']='Something went wrong. Please try again !!!';
				
		// }
			
			// $retres=json_encode($retres);
			// echo $retres;
       //redirect('assetsapi','refresh');
	}
	
	
	public function facebook()
	{
		
		$userData = array();
		// Check if user is logged in
		if($this->facebook->is_authenticated()){
			// Get user facebook profile details
		 $fbUserProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,link,gender,locale,cover,picture');

            // Preparing data for database insertion
            $userData['oauth_provider'] = 'facebook';
            $userData['oauth_uid'] = $fbUserProfile['id'];
            $userData['first_name'] = $fbUserProfile['first_name'];
            $userData['last_name'] = $fbUserProfile['last_name'];
            $userData['email'] = $fbUserProfile['email'];
            // $userData['gender'] = $userProfile['gender'];
            // $userData['locale'] = $userProfile['locale'];
            // $userData['profile_url'] = 'https://www.facebook.com/'.$userProfile['id'];
            // $userData['picture_url'] = $userProfile['picture']['data']['url'];
			
            // Insert or update user data
              $userID = $this->user->checkUser($userData);
			
			/// Check user data insert or update status
            if(!empty($userID)){
                $data['userData'] = $userData;
                $this->session->set_userdata('userData',$userData);
            }else{
               $data['userData'] = array();
            }
			// Get logout URL
            $data['logoutURL'] = $this->facebook->logout_url();
        }else{
            // Get login URL
            $data['authURL'] =  $this->facebook->login_url();
        }
		
		// Load login & profile view

    
		 // print_r($userData);
	   // $reacturl = $this->config->item('reacturl').'social-login?oauth_provider="'.$userData["oauth_provider"].'"&oauth_uid="'.$userData["oauth_uid"].'"&first_name="'.$userData["first_name"].'"&last_name="'.$userData["last_name"].'"&email="'.$userData["email"].'"';
				// redirect($reacturl);
    }
	 public function twitter()
	 {
		 
	  
		
			/* $consumerKey = $this->config->item('twitter_consumer_token');
			$consumerSecret = $this->config->item('twitter_consumer_secret');
			$oauthCallback = base_url().'assetsapi/twitter/';
			$connection = new TwitterOAuth($consumerKey, $consumerSecret);
			$request_token = $connection->oauth("oauth/request_token", array("oauth_callback" =>"http://localhost/assetsapi/assetsapi/twitter"));

			$_SESSION['oauth_token'] = $request_token['oauth_token'];
			$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

			$url = $connection->url("oauth/authorize", array("oauth_token" => $request_token['oauth_token']));
			header('Location: ' . $url);
			//echo $url;
			if($_GET['oauth_token'] || $_GET['oauth_verifier'])
			{	
				$connection = new TwitterOAuth($consumerKey, $consumerSecret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
				$access_token = $connection->oauth('oauth/access_token', array('oauth_verifier' => $_REQUEST['oauth_verifier'], 'oauth_token'=> $_GET['oauth_token']));

				$connection = new TwitterOAuth($consumerKey, $consumerSecret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

				$user_info = $connection->get('account/verify_credentials');
			
				$oauth_token = $access_token['oauth_token'];
				$oauth_token_secret = $access_token['oauth_token_secret'];
					$name = explode(" ",$user_info->name);
					$first_name = isset($name[0])?$name[0]:'';
					$last_name = isset($name[1])?$name[1]:'';
					
					$userData['oauth_provider'] = 'twitter';
					$userData['oauth_uid'] = $user_info->id;
					$userData['first_name'] = $first_name;
					$userData['last_name'] = $last_name;
					//$userData['email'] = $fbUserProfile['email'];
					
					// $userID = $this->assetsapi_model->checkUser($userData);
					
					// $this->session->set_userdata('loggedIn', true);
					// $this->session->set_userdata('userData', $userData);
			}
			// $data['userData'] = $userData;
        
        // Load login & profile view
		 // $retres=array();
		// if($userID)
		// {
			// $retres['success']=1;
			// $retres['msg']="Twitter login successfully.";
			// $retres['twitter']=$userID;
		// }
		// else{
			// $retres['success']=0;
			// $retres['msg']='Something went wrong. Please try again !!!';
				
		// }
			
			// $retres=json_encode($retres);
			// echo $retres;
       //redirect('assetsapi','refresh');
	   $reacturl = $this->config->item('reacturl').'social?oauth_provider="'.$userData["oauth_provider"].'"&oauth_uid="'.$userData["oauth_uid"].'"&first_name="'.$userData["first_name"].'"&last_name="'.$userData["last_name"].'"';
				redirect($reacturl); */
			
    }
	

	
	//=======================================Plan Start======================================================================
	public function plan()
    {
		$result = $this->assetsapi_model->plan();
		$retres=array();
		if($result)
		{
			$retres['success']=1;
			$retres['plan']=$result;
		}
		else{
			$retres['success']=0;
			$retres['msg']='No record found !!!';
				
		}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	
	public function plan_by_assetstype($assets_type){
		$result = $this->assetsapi_model->plan_by_assetstype($assets_type);
		$retres=array();
		if($result)
		{
			$retres['success']=1;
			$retres['plan']=$result;
		}
		else{
			$retres['success']=0;
			$retres['msg']='No record found !!!';
				
		}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	//=======================================Plan End======================================================================
	/*function testAuthorizeV1() {
        // initialize the API client with default base URL: https://api.singularbillpay.com
        $api_client = new SingularApi\ApiClient();

        // create SbpRequest object
        $sbpRequest = new SingularApi\Model\SbpRequest();
        $sbpRequest->setAddress("sample address");
        $sbpRequest->setCity("sample city");
        $sbpRequest->setCountry("sample country");
        $sbpRequest->setCurrency("sample currency");
        $sbpRequest->setCvv("sample cvv");
        $sbpRequest->setEmail("sample email");
        $sbpRequest->setExpirymmyy("sample expirymmyy");
        $sbpRequest->setOrderid("sample orderid");
        $sbpRequest->setPartnerid("sample partnerid");
        $sbpRequest->setPartnerkey("sample partnerkey");
        $sbpRequest->setPayeefirstname("sample payeefirstname");
        $sbpRequest->setPayeeid("sample payeeid");
        $sbpRequest->setPayeelastname("sample payeelastname");
        $sbpRequest->setPaymentmode("sample paymentmode");
        $sbpRequest->setProfile("sample profile");
        $sbpRequest->setProfileid("sample profileid");
        $sbpRequest->setRoutingnumber("sample routingnumber");
        $sbpRequest->setState("sample state");
        $sbpRequest->setSurchargeamount("sample surchargeamount");
        $sbpRequest->setTokenizedaccountnumber("sample tokenizedaccountnumber");
        $sbpRequest->setTransactionamount("sample transactionamount");
        $sbpRequest->setTransactionreference("sample transactionreference");
        $sbpRequest->setTransactiontype("sample transactiontype");
        $sbpRequest->setUdfield1("sample udfield1");
        $sbpRequest->setUdfield2("sample udfield2");
        $sbpRequest->setUdfield3("sample udfield3");
        $sbpRequest->setZip("sample zip");

        try {
            $v1_api = new SingularApi\Api\V1API($api_client);
            // return <a href="#model_SbpResponse">SbpResponse (model)</a>
            $response = $v1_api->authorize($sbpRequest);
            print_r($response);
        } catch (SingularApi\ApiException $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
            echo 'Resepone Header: ', print_r($e->getResponseHeaders(), true), "\n";
            echo 'Resepone Body: ', $e->getResponseBody(), "\n";
        }
    }*/
	
	
	//==========================================Plan Restrictions Check============================================================
	public function checkPermissions($userid,$feature_alias)
	{
		$userData = $this->assetsapi_model->profile($userid);
				$assets_type= $userData['assets_type'];
				$plan_id= $userData['plan_id'];
		
				$featureTagQuery = $this->db->get_where('feature_tb',array('feature_tag'=>$feature_alias));
				$featureTagArr = $featureTagQuery->result_array();
				
				$featureAvailable = $this->assetsapi_model->getPlandetail($plan_id,$feature_alias);
				
				$retres=array();
				if(count($featureTagArr)>0 && count($featureAvailable)>0)
				{
					$featureTag = $featureTagArr[0]['feature_tag'];
					$feature_unit = $featureTagArr[0]['feature_unit'];
					
					if($featureTag=='manage_properties_upto'){
						$propertyCount = $this->assetsapi_model->getPropertydetail($userid);
						$used_feature = $propertyCount[0]['added_property'];
					}elseif($featureTag=='create_agreement'){
						
						$AgreementCount = $this->assetsapi_model->getAgreementCount($userid);
						$used_feature = $AgreementCount[0]['agreement_count'];
					}
					elseif($featureTag=='send_agreement_for_signature'){
						
						//$used_feature = 3;
					}
					
					
					
					if($feature_unit=='Limit'){
						if($featureTag=='upload_image_per_property'){
						
							$retres['success']=1;
							//$retres['msg']="You can proceed. !!!";
							$retres['available']=$featureAvailable;
						}
						$availableLimitUpto =$featureAvailable[0]['limit_upto'];
						if($featureTag!='upload_image_per_property' && isset($used_feature))
						{
							if($availableLimitUpto>$used_feature)
							{
								$retres['success']=1;
								$retres['msg']="You can proceed. !!!";
								$retres['used_feature']=$used_feature;
								
								
							}else{
								$retres['success']=0;
								$retres['msg']='Your limit is over.!!!';
								$retres['used_feature']=$used_feature;
							}
						}
					}elseif($feature_unit=='Restrict'){
					//
					}
				}else{
					$retres['success']=0;
					$retres['msg']='No data found.!!!';
							
				}
				$retres=json_encode($retres);
				echo $retres;
				
			
		}
		
		
		
//==========================================Payment Gateway===========================================================		
		/*public function purchase_plan($user_id,$plan_id)
		{
			$update = $this->db->update('registration_tb',array('plan_id'=>$plan_id),array('assets_id'=>$user_id));
			$retres=array();
			if($update)
			{
					$retres['success']=1;
					$retres['msg']="Plan successfully added to user. !!!";
					//$retres['used_feature']=$used_feature;
								
			}else{
					$retres['success']=0;
					$retres['msg']='Somthing went wrong please try later.!!!';
					
				}
				$retres=json_encode($retres);
				echo $retres;
		}
		*/
		public function payment($user_id,$plan_id,$plan_month_year)
		{ 
			// $update = $this->db->update('registration_tb',array('plan_id'=>$plan_id),array('assets_id'=>$user_id));
			
			$userData = $this->assetsapi_model->profile($user_id);
			$planData = $this->assetsapi_model->planDeatilBy($plan_id);
			
			$first_name = $userData['first_name'];
			$last_name = $userData['last_name'];
			//$address = $userData[0]['address'];
			$city = $userData['city'];
			$state = $userData['state'];
			$country = $userData['country'];
			$zip = $userData['zip_code'];
			$email = $userData['email'];
			$password = $userData['password'];
			$assets_type = $userData['assets_type'];
			if($plan_month_year=='per_month')
			{
				$planPrice = $planData[0]['per_month'];
			}elseif($plan_month_year=='per_annum'){
				$planPrice = $planData[0]['per_annum'];
			}
			
			$randNumber = mt_rand(100000, 999999);
			$orderid = 'TXN'.$randNumber;
			//$data = array('first_name'=>$first_name,'last_name'=>$last_name,'city'=>$city, 'state'=>$state, 'country'=>$country, 'zip'=>$zip,'email'=> $email, 'user_id'=>$user_id,'plan_id'=> $plan_id,'planPrice'=>$planPrice);
			$data = array('first_name'=>$first_name,'last_name'=>$last_name,'user_id'=>$user_id,'plan_id'=> $plan_id,'planPrice'=>$planPrice,'orderid'=>$orderid,'plan_month_year'=>$plan_month_year,'email'=> $email,'password'=> $password,'assets_type'=> $assets_type);
			
			//$this->load->view('payment_gatway',$data);
			
			
			// $datatoinsert = array('user_id'=>$user_id,'plan_id'=> $plan_id,'orderid'=> $orderid,'plan_amount'=>$planPrice);
			$retres=array();
			if($data)
			{
					$retres['success']=1;
					$retres['user_detail']=$data;
					$retres['orderid']=$orderid;
					// $insertData = $this->db->insert('transaction_tb',$datatoinsert);
					// $txn_id = $this->db->insert_id();
					
					$sessionData = array(
										// 'txn_id'=>$txn_id,
										'user_id'=>$user_id,
										'plan_id'=> $plan_id,
										'orderid'=> $orderid,
										'planPrice'=>$planPrice
										);
					$this->session->set_userdata('orderid',$orderid);

								
			}else{
					$retres['success']=0;
					$retres['msg']='Somthing went wrong please try later.!!!';
					
				}
				$retres=json_encode($retres);
				echo $retres;
		}
		
		public function paymentgateway()
		{
		//print_r($_POST);
		//exit();
			$request = json_decode(file_get_contents('php://input'), true);
			
			$partnerkey = $this->config->item('partnerkey');
			$partnerid = $this->config->item('partnerid');
			$transactiontype = $this->config->item('transactiontype');
			
			$userData = $this->assetsapi_model->profile($request["userid"]);
			//$planData = $this->assetsapi_model->planDeatilBy($plan_id);
			
			$first_name = $userData['first_name'];
			$last_name = $userData['last_name'];
			$address = $userData['city'].", ".$userData['state'].", ".$userData['country'].", ".$userData['zip_code'];
			$city = $userData['city'];
			$state = $userData['state'];
			$country = $userData['country'];
			$zip = $userData['zip_code'];
			$email = $userData['email'];
			//$orderid = $this->session->userdata('orderid');
			//$explodeName = explode(' ' ,$_POST['name'])
			//$firstName = (!empty($explodeName[0]))?$explodeName[0]:'';
			//$lastName = (!empty($explodeName[1]))?$explodeName[1]:'';
			//$expirymmyy = date('m',strtotime($_POST['month'])).date('y',strtotime($_POST['year']));
			$datatosend = array(
				"partnerkey"=>$partnerkey,
				"partnerid"=>$partnerid,
				"transactiontype"=>$transactiontype,
				"tokenizedaccountnumber"=>$request["tokenizedaccountnumber"],
				"expirymmyy"=>$request["expirymmyy"],
				"cvv"=>$request["cvv"],
				"paymentmode"=>$request["paymentmode"],
				"transactionamount"=>$request["transactionamount"],
				"routingnumber"=>$request["routingnumber"],
				"surchargeamount"=>$request["surchargeamount"],
				"currency"=>$request["currency"],
				"payeefirstname"=>$first_name,
				"payeelastname"=>$last_name,
				"address"=>$address,
				"city"=>$city,
				"state"=>$state,
				"country"=>$country,
				"zip"=>$zip,
				"email"=>$email,
				"transactionreference"=>$request["transactionreference"],
				"orderid"=>$request["orderid"],
				"payeeid"=>$request["payeeid"],
				"notifypayee"=>$request["notifypayee"],
				"profile"=>$request["profile"],
				"profileid"=>$request["profileid"]
			);
			//print_r($datatosend);
			//exit();
			$jsondata = json_encode($datatosend);
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.singularbillpay.com/v1/transaction",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				 CURLOPT_POSTFIELDS => $jsondata,
				 CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json"
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  //echo $response;
				  // $txn_id = $this->session->userdata('txn_no');
				  $responseArr = json_decode($response);
				  if($request["orderid"] == $responseArr->orderid)
				  {
						$this->db->order_by('transactiondate',"desc");
						$this->db->limit(1);
						$query = $this->db->get('transaction_tb');
						 $lastRecord = $query->result_array();
					  if($lastRecord[0]['invoice_number']!='' || $lastRecord[0]['invoice_number']!=null ){
						   $inv = $lastRecord[0]['invoice_number'];
						  
						    $inv_number = ++$inv;
					  }else{
						  $inv_number = 'AWLLC000001';
					  }
					  
						$invoice_number = $inv_number;
					   $datatoinsertTrans = array( 
									'invoice_number' => $invoice_number,
									'user_id'=>$request["userid"],
									'plan_id'=> $request["plan_id"],
									'orderid'=> $request["orderid"],
									'plan_amount'=>$request["transactionamount"],
									'transactionid'=>$responseArr->transactionid,
									'paymentmode'=>$responseArr->paymentmode,
									'transaction_type'=>$responseArr->transactiontype,
									'transactionamount'=>$responseArr->transactionamount,
									'transactionreference'=>$responseArr->transactionreference,
									'responsecode'=>$responseArr->responsecode,
									'responsestatus'=>$responseArr->responsestatus,
									'responsemessage'=>$responseArr->responsemessage,
									'transactiondate'=>date('Y-m-d H:i:s',strtotime($responseArr->transactiondate)),
									'trans_for'=>'Register',
									
					   );
					 // $update = $this->db->update('transaction_tb',$datatoupdate,array('orderid'=>$request["orderid"]));
					$insertTrans = $this->db->insert('transaction_tb',$datatoinsertTrans);
					 
					
					$currDate = date('Y-m-d');
					if($request["plan_type"]=='per_month')
					{
						
						$expireDate = date('Y-m-d', strtotime($currDate. ' + 1 month'));
						
						
					}else if($request["plan_type"]=='per_annum')
					{
						 $expireDate = date('Y-m-d', strtotime($currDate. ' + 1 year'));
						
					} 
					$datatoinsert = array( 
									'assets_id'=>$request["userid"],
									'plan_id'=>$request["plan_id"],
									'plan_type'=>$request["plan_type"],
									'upgrade_reason'=>'Register',
									'expire_date'=>$expireDate
									
					   );
					$insert = $this->db->insert('upgrade_plan_log_tb',$datatoinsert);
					$updateReg = $this->db->update('registration_tb',array('plan_id'=>$request["plan_id"],'status'=>1),array('assets_id'=>$request["userid"]));
				  }
				}
			 $retres=array();
			 if($request["orderid"] == $responseArr->orderid)
			 {
					 $retres['success']=1;
					 $retres['msg']='Registered Successfully.!!!';
					
				
				//========================Mail======================================================================
				$to_email = $email;
				$from_email = "info@assetswatch.com";
				$RegFilename = 'registration_template.txt';
				$RegTemplate = read_file('assets/email_template/'.$RegFilename);
				$RegSubject = "Registration";
				
				$reacturl = $this->config->item('reacturl');
				$url = "<br/><a href='".$reacturl."' style='text-decoration: none;color: #FFFFFF;font-size: 17px;font-weight: 400;line-height: 120%;padding: 9px 26px;margin: 0;text-decoration: none;border-collapse: collapse;border-spacing: 0;border-radius: 4px;-webkit-border-radius: 4px;text-align: -webkit-center;vertical-align: middle;background-color: rgb(87, 187, 87);-moz-border-radius: 4px;-khtml-border-radius: 4px;'>Login</a>";

				$RegTokensArr = array(
				'USER_NAME' => $first_name,
				 'USER_EMAIL'=> $to_email,
				 'URL'=> trim($url)
				);
				$RegMail = $this->send_mail($from_email,$to_email,$RegSubject,$RegTokensArr,$RegTemplate);
				// ========================Mail======================================================================
				//========================Mail Invoice====================================================
				
				$InvoiceFilename = 'invoice.txt';
				$InvoiceTemplate = read_file('assets/email_template/'.$InvoiceFilename);
				$InvoiceSubject = "Invoice For Registration";
				
				$transactionamount = $responseArr->transactionamount;
				$TaxAmount = 0;
				$TotAmount = $transactionamount + $TaxAmount;
				$InvoiceTokensArr = array(
				'ADDRESS' => $address,
				 'TITLE'=> 'Registration Fee',
				 'AMOUNT'=> $transactionamount,
				 'TAX_AMOUNT'=> $TaxAmount,
				 'TOTAL_AMOUNT'=> $TotAmount,
				 'INVOICE'=>$invoice_number,
				 'INVOICE_DATE'=>date('d M Y')
				);
				$InvoceMail = $this->send_mail($from_email,$to_email,$InvoiceSubject,$InvoiceTokensArr,$InvoiceTemplate);
				// 
				// if($mail)
				// {
					// echo "send";
					
				// }else{
					// echo "not send ::".$this->email->print_debugger(FALSE);
					
				// }
					 //$retres['payment']=$responseArr;
				
				
								
			 }else{
					 $retres['success']=0;
					 $retres['msg']='Somthing went wrong please try later.!!!';
					
				 }
				 $retres=json_encode($retres);
				 echo $retres;
		}
		
//========================================================================================================================================================================	

//========================================Plan Upgrade========================================================================
public function upgpaymentgateway()
		{
		//print_r($_POST);
		//exit();
			$request = json_decode(file_get_contents('php://input'), true);
			
			$partnerkey = $this->config->item('partnerkey');
			$partnerid = $this->config->item('partnerid');
			$transactiontype = $this->config->item('transactiontype');
			
			$userData = $this->assetsapi_model->profile($request["userid"]);
			//$planData = $this->assetsapi_model->planDeatilBy($plan_id);
			
			$first_name = $userData['first_name'];
			$last_name = $userData['last_name'];
			$address = $userData['city']." ".$userData['state']." ".$userData['country']." ".$userData['zip_code'];
			$city = $userData['city'];
			$state = $userData['state'];
			$country = $userData['country'];
			$zip = $userData['zip_code'];
			$email = $userData['email'];
			//$orderid = $this->session->userdata('orderid');
			//$explodeName = explode(' ' ,$_POST['name'])
			//$firstName = (!empty($explodeName[0]))?$explodeName[0]:'';
			//$lastName = (!empty($explodeName[1]))?$explodeName[1]:'';
			//$expirymmyy = date('m',strtotime($_POST['month'])).date('y',strtotime($_POST['year']));
			$datatosend = array(
				"partnerkey"=>$partnerkey,
				"partnerid"=>$partnerid,
				"transactiontype"=>$transactiontype,
				"tokenizedaccountnumber"=>$request["tokenizedaccountnumber"],
				"expirymmyy"=>$request["expirymmyy"],
				"cvv"=>$request["cvv"],
				"paymentmode"=>$request["paymentmode"],
				"transactionamount"=>$request["transactionamount"],
				"routingnumber"=>$request["routingnumber"],
				"surchargeamount"=>$request["surchargeamount"],
				"currency"=>$request["currency"],
				"payeefirstname"=>$first_name,
				"payeelastname"=>$last_name,
				"address"=>$address,
				"city"=>$city,
				"state"=>$state,
				"country"=>$country,
				"zip"=>$zip,
				"email"=>$email,
				"transactionreference"=>$request["transactionreference"],
				"orderid"=>$request["orderid"],
				"payeeid"=>$request["payeeid"],
				"notifypayee"=>$request["notifypayee"],
				"profile"=>$request["profile"],
				"profileid"=>$request["profileid"]
			);
			//print_r($datatosend);
			//exit();
			$jsondata = json_encode($datatosend);
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.singularbillpay.com/v1/transaction",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  // CURLOPT_POSTFIELDS => "{\r\n  \"partnerkey\": \"26041766-F331-44C0-8FE1-152E4B45A3D9\",\r\n  \"partnerid\": \"testcardconnect\",\r\n  \"transactiontype\": \"auth\",\r\n  \"tokenizedaccountnumber\": \"4111111111111111\",\r\n  \"paymentmode\": \"card\",\r\n  \"expirymmyy\": \"1221\",\r\n  \"cvv\": null,\r\n  \"routingnumber\": null,\r\n  \"transactionamount\": 12.34,\r\n  \"surchargeamount\": null,\r\n  \"currency\": null,\r\n  \"payeefirstname\": \"Binod\",\r\n  \"payeelastname\": \"Nair\",\r\n  \"address\": \"3316 Shady Valley Rd\",\r\n  \"city\": \"Plano\",\r\n  \"state\": \"TX\",\r\n  \"country\": \"US\",\r\n  \"zip\": \"75025\",\r\n  \"email\": \"nairbinod@gmail.com\",\r\n  \"transactionreference\": null,\r\n  \"orderid\": null,\r\n  \"payeeid\": null,\r\n  \"udfield1\": null,\r\n  \"udfield2\": null,\r\n  \"udfield3\": null,\r\n  \"notifypayee\": null,\r\n  \"profile\": null,\r\n  \"profileid\": null\r\n}\r\n",
				 CURLOPT_POSTFIELDS => $jsondata,
				 CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json"
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  //echo $response;
				  $txn_id = $this->session->userdata('txn_no');
				  $responseArr = json_decode($response);
				  if($request["orderid"] == $responseArr->orderid)
				  {
					  $this->db->order_by('transactiondate',"desc");
						$this->db->limit(1);
						$query = $this->db->get('transaction_tb');
						 $lastRecord = $query->result_array();
					  if($lastRecord[0]['invoice_number']!='' || $lastRecord[0]['invoice_number']!=null ){
						  $inv = $lastRecord[0]['invoice_number'];
						  
						    $inv_number = ++$inv;
					  }else{
						  $inv_number = 'AWLLC000001';
					  }
					  
					   $invoice_number = $inv_number;
					   $datatoinsertTrans = array( 
									'invoice_number' => $invoice_number,
									'user_id'=>$request["userid"],
									'plan_id'=> $request["plan_id"],
									'orderid'=> $request["orderid"],
									'plan_amount'=>$request["transactionamount"],
									'transactionid'=>$responseArr->transactionid,
									'paymentmode'=>$responseArr->paymentmode,
									'transaction_type'=>$responseArr->transactiontype,
									'transactionamount'=>$responseArr->transactionamount,
									'transactionreference'=>$responseArr->transactionreference,
									'responsecode'=>$responseArr->responsecode,
									'responsestatus'=>$responseArr->responsestatus,
									'responsemessage'=>$responseArr->responsemessage,
									'transactiondate'=>date('Y-m-d H:i:s',strtotime($responseArr->transactiondate)),
									'trans_for'=>'Upgrade',
									
					   );
					   // print_r($datatoinsertTrans);
					 // $update = $this->db->update('transaction_tb',$datatoupdate,array('orderid'=>$request["orderid"]));
					$insertTrans = $this->db->insert('transaction_tb',$datatoinsertTrans);
					$currDate = date('Y-m-d');
					if($request["plan_type"]=='per_month')
					{
						
						$expireDate = date('Y-m-d', strtotime($currDate. ' + 1 month'));
						
						
					}else if($request["plan_type"]=='per_annum')
					{
						 $expireDate = date('Y-m-d', strtotime($currDate. ' + 1 year'));
						
					} 
					$datatoinsert = array( 
									'assets_id'=>$request["userid"],
									'plan_id'=>$request["plan_id"],
									'plan_type'=>$request["plan_type"],
									'upgrade_reason'=>'Upgrade',
									'expire_date'=>$expireDate
									
					   );
					$insert = $this->db->insert('upgrade_plan_log_tb',$datatoinsert);
					$updateReg = $this->db->update('registration_tb',array('plan_id'=>$request["plan_id"],'status'=>1),array('assets_id'=>$request["userid"]));
					
				  }
				}
			 $retres=array();
			 if($request["orderid"] == $responseArr->orderid)
			 {
					 $retres['success']=1;
					 $retres['msg']='Plan Upgraded Successfully.!!!';
					 //========================Mail Invoice====================================================
				$to_email = $email;
				$from_email = "info@assetswatch.com";
				$InvoiceFilename = 'invoice.txt';
				$InvoiceTemplate = read_file('assets/email_template/'.$InvoiceFilename);
				$InvoiceSubject = "Invoice For Registration";
				
				$transactionamount = $responseArr->transactionamount;
				$TaxAmount = 0;
				$TotAmount = $transactionamount + $TaxAmount;
				$InvoiceTokensArr = array(
				'ADDRESS' => $address,
				 'TITLE'=> 'Registration Fee',
				 'AMOUNT'=> $transactionamount,
				 'TAX_AMOUNT'=> $TaxAmount,
				 'TOTAL_AMOUNT'=> $TotAmount,
				 'INVOICE'=>$invoice_number,
				 'INVOICE_DATE'=>date('d M Y')
				);
				$InvoceMail = $this->send_mail($from_email,$to_email,$InvoiceSubject,$InvoiceTokensArr,$InvoiceTemplate);
					 //$retres['payment']=$responseArr;
								
			 }else{
					 $retres['success']=0;
					 $retres['msg']='Somthing went wrong please try later.!!!';
					
				 }
				 $retres=json_encode($retres);
				 echo $retres;
		}
		
//========================================================================================================================================================================	
		
		
//================================================================Agreement Start=============================================================================================
		
	
	
	//==============================================================PDF Generation==============================================================================
		public function pdfGenerator($user_id,$agreement_id){
			// $propertyformQuery = $this->db->get_where('agreement_property_form_tb',array('status'=>'1'));
			// $propertyformData = $propertyformQuery->result_array();
			// $propDescription = $propertyformData[0]['description'];
			
			
			// $signatureQuery = $this->db->get('agreement_signature_form_tb',array('status'=>'1'));
			// $signatureformData = $signatureQuery->result_array();
			// $signDescription = $signatureformData[0]['description'];
			
			
			
			$userData = $this->assetsapi_model->profile($user_id);
			$assets_type= $userData['assets_type'];
			
			$agreementQuery = $this->db->get_where('agreement_tb',array('user_id'=>$user_id,'agreement_id'=>$agreement_id));
			$agreementData = $agreementQuery->result_array();
			
			$Title = $agreementData[0]['agreement_title'];
			$fileName = str_replace(" ", "_", $Title);
			
			$headerContent = $agreementData[0]['header_content'];
			$footerContent = $agreementData[0]['footer_content'];
			$headerImage = $agreementData[0]['header_image'];
			$watermarkImage = $agreementData[0]['watermark_image'];
			
			$html = $agreementData[0]['agreement_doc_content'];
			
			
			//$TermsDescription = $agreementData[0]['agreement_doc_content'];
			
			
			// $html = $propDescription.$TermsDescription.$signDescription;
			// $html = $signDescription;
			
			// Create a new PDF but using our own class that extends TCPDF
			$pdf = new MyCustomPDFWithWatermark(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			$template = array('headerContent'=>$headerContent,'footerContent'=>$footerContent,'headerImage'=>$headerImage,'watermarkImage'=>$watermarkImage);
			//echo $headerImage;
			
			$pdf->setData($template);
			
			// set document information
			//$pdf->SetAuthor('Our Code World');
			 
			// set default monospaced font
			//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
			// set default header data
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
			// set margins
			//$pdf->SetMargins('10', '40', '10');
			
			// set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
			
			 $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);//, 70,120, 57, 25, '', '', '', false, 300, '', false, false, 0);
			
			$pdf->SetFont('times', '', 8, '', true); 
				
			$pdf->AddPage();
			$pdf->writeHTML($html, true, false, true, false, '');
			
			if($assets_type == 1)
			{
				$path = "assets/agreement_DOC/Owner/$user_id/$agreement_id/";
				
				
			}elseif($assets_type == 2){
				$path = "assets/agreement_DOC/Agent/$user_id/$agreement_id/";
				
				
			}
			
			$folderPath = mkdir($path, 0777, true);
			$pdfFilePath = APPPATH.'../'.$path.$fileName.'.pdf';
			// echo $pdfFilePath;
			 ob_clean();
			  
			   $pdfgenerated = $pdf->Output($pdfFilePath, 'F');
			 //$pdfgenerated = $pdf->Output($_SERVER['DOCUMENT_ROOT']. $pdfFilePath, 'F');
				$this->db->set('agreement_file_name',$path.$fileName.'.pdf');
				$this->db->where('agreement_id', $agreement_id);
				$this->db->update('agreement_tb');
			
		}

		
//===========================================================Add Agreement================================================================================================
		public function add_agreement(){
			$request = json_decode(file_get_contents('php://input'), true);
			$agreement_doc_id = 'AWG'.mt_rand(100000, 999999);
			
			$header_content = isset($request['header_content'])?$request['header_content']:'';
			$header_image = isset($request['header_image'])?$request['header_image']:'';
			$watermark_image = isset($request['watermark_image'])?$request['watermark_image']:'';
			$footer_content = isset($request['footer_content'])?$request['footer_content']:'';
			
			$agreementData = array(
				'agreement_doc_id'=>$agreement_doc_id,
				'user_id'=>$request['user_id'],
				'agreement_title' => $request['agreement_title'],
				'agreement_doc_content' => $request['agreement_doc_content'],
				'header_content' => $header_content,
				'header_image' =>$header_image,
				'watermark_image' =>$watermark_image,
				'footer_content' =>$footer_content
				);
			
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
				$insertData = $this->db->insert('agreement_tb',$agreementData);
				$agreement_id = $this->db->insert_id();
				$retres=array();
				if($insertData)
				{
					$retres['success']=1;
					$retres['msg']="Agreement Created Successfully. !!!";
					$user_id = $request['user_id'];
					
			//===============================header image======================
			if(isset($request['header_image']) && $request['header_image']!='')
			{
					$headerImg = $header_image;
					$headImg = str_replace('data:image/jpeg;base64,', '', $headerImg);
					$img = str_replace('data:image/png;base64,', '', $headImg);
					$headerimage = base64_decode($img);
					// echo "<script>alert(".$image.")</script>";
					$image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
					$headerfilename = $image_name . '.' . 'png';
					//rename file name with random number
					// $path = './';
					$path = "assets/agreement_DOC/Owner/$user_id/$agreement_id/";
					$folderPath = mkdir($path, 0777, true);
					$headtargetPath = $path.$headerfilename;
					file_put_contents($headtargetPath , $headerimage);
			}
			//================================watermark image=========================================
			if(isset($request['watermark_image']) && $request['watermark_image']!='')
			{
					$watermark_Img = $watermark_image;
					$WImg = str_replace('data:image/jpeg;base64,', '', $watermark_Img);
					$waterimg = str_replace('data:image/png;base64,', '', $WImg);
					$wmimage = base64_decode($waterimg);
					// echo "<script>alert(".$image.")</script>";
					$wmimage_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
					$WMfilename = $wmimage_name . '.' . 'png';
					//rename file name with random number
					// $path = './';
					$path = "assets/agreement_DOC/Owner/$user_id/$agreement_id/";
					if (!file_exists($path)) {
						$folderPath = mkdir($path, 0777, true);
					}else{
						$folderPath = $path;
					}
					
					
					$WMtargetPath = $path.$WMfilename;
					file_put_contents($WMtargetPath, $wmimage);
			}
					$this->db->set(array('header_image'=>$headtargetPath,'watermark_image'=>$WMtargetPath));
					$this->db->where('agreement_id', $agreement_id);
					$this->db->update('agreement_tb');
					
					$pdfgeneration = $this->pdfGenerator($user_id,$agreement_id);
					$retres['success']=1;
					$retres['msg']="Agreement Created.!!!";
					
				}else{
					$retres['success']=0;
					$retres['msg']="Somthing went wrong please try again.!!!";
					
				}
			}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
					
				}
			
			$retres=json_encode($retres);
			echo $retres;
		
		}
		

//=============================================================Send Agreement================================================================================================
		public function send_agreement(){
			$request = json_decode(file_get_contents('php://input'), true);
			
			
			$agreementData = array(
				'sender_id'=>$request['sender_id'],
				'receiver_id'=>$request['receiver_id'],
				'agreement_id' => $request['agreement_id'],
				'property_id' => $request['property_id'],
				'initiated_date'=>date('Y-m-d'),
				'description'=>$request['description']
				);
				
				$retres=array();
			$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
			if($validate)
			{
				$result = $this->assetsapi_model->send_agreement($agreementData);
				if($result)
				{
					$retres['success']=1;
					$retres['msg']="Agreement send successfully. !!!";
					$retres['deal']=$result;
					
					
				}else{
					$retres['success']=0;
					$retres['msg']="Somthing went wrong please try again.!!!";
					
				}
			}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
					
				}
			$retres=json_encode($retres);
			echo $retres;
		}
//===========================================================================================================

//=============================send forwarded agreement======================================================
	public function send_forwarded_agreement(){
			$request = json_decode(file_get_contents('php://input'), true);
			
			
			$agreementData = array(
				'sender_id'=>$request['sender_id'],
				'receiver_id'=>$request['receiver_id'],
				'agreement_id' => $request['agreement_id'],
				'property_id' => $request['property_id'],
				'initiated_date'=>date('Y-m-d'),
				'description'=>$request['description'],
				'updatedTemplate'=>$request['updatedTemplate']
				);
				
				$retres=array();
			$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
			if($validate)
			{
				$result = $this->assetsapi_model->send_forwarded_agreement($agreementData);
				if($result)
				{
					$retres['success']=1;
					$retres['msg']="Agreement send successfully. !!!";
					$retres['deal']=$result;
					
					
				}else{
					$retres['success']=0;
					$retres['msg']="Somthing went wrong please try again.!!!";
					
				}
			}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
					
				}
			$retres=json_encode($retres);
			echo $retres;
		}
//==========================webview agreement============================================================
		public function webview_agreement($deal_id,$session_id){
			
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->webview_agreement($deal_id);
				$retres=array();
				if($result)
				{
					$retres['success']=1;
					$retres['webview']=$result;
					
					
				}else{
					$retres['success']=0;
					$retres['msg']="No data found.!!!";
					
				}
			}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
					
				}
			$retres=json_encode($retres);
			echo $retres;
			
		}
//===========================================================List Of Saved Agreement================================================================================================
		public function saved_agreement($user_id,$session_id)
		{
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->saved_agreement($user_id);
				$retres=array();
				if($result)
				{
					$retres['success']=1;
					$retres['saved_agreement']=$result;
					
					
				}else{
					$retres['success']=0;
					$retres['msg']="No data found.!!!";
					
				}
			}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
					
				}
			$retres=json_encode($retres);
			echo $retres;
		}


//===========================================================Agreement Details================================================================================================		
		public function agreement_detail($agreement_id,$session_id)
		{
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->agreement_detail($agreement_id);
				$retres=array();
				if($result)
				{
					$retres['success']=1;
					$retres['agreement_detail']=$result;
					
					
				}else{
					$retres['success']=0;
					$retres['msg']="No data found.!!!";
					
				}
			}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
					
				}
			$retres=json_encode($retres);
			echo $retres;
		}


//===========================================================List Of Requested Agreement=======================================================================================
		public function requested_agreement($user_id,$session_id)
		{
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->requested_agreement($user_id);
				$retres=array();
				if($result)
				{
					$retres['success']=1;
					$retres['requested_agreements']=$result;
					
					
				}else{
					$retres['success']=0;
					$retres['msg']="No data found.!!!";
					
				}
			}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
					
				}
			$retres=json_encode($retres);
			echo $retres;
		}


//===========================================================Requested Agreement Send=================================================================================
		/*public function requested_agreement_send()
		{
			$request = json_decode(file_get_contents('php://input'), true);
			$requestedData = array(
				
				'deal_id'=>$request['deal_id'],
				'version_name'=>$version_name,
				'file_name' => $file_name,
				'status' => $request['status'],
				'comment'=>$request['comment'],
				'acceptance_status'=>$request['acceptance_status']
				
				);
				
				$retres=array();
			
			$Insert = $this->db->insert('agreement_version_tb',$requestedData);
			if($Insert)
			{
				//$pdfgeneration = $this->pdfGenerator($user_id,$agreement_id);
			}
			/*$retres=array();
			if($result)
			{
				$retres['success']=1;
				$retres['agreement_detail']=$result;
				
				
			}else{
				$retres['success']=0;
				$retres['msg']="No data found.!!!";
				
			}
			$retres=json_encode($retres);
			echo $retres;
		}*/
		
//===========================================================List of Execute Agreement ====================================================================================
		public function execute_agreement($user_id,$session_id)
		{
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->execute_agreement($user_id);
				$retres=array();
				if($result)
				{
					$retres['success']=1;
					$retres['execute_agreements']=$result;
					
					
				}else{
					$retres['success']=0;
					$retres['msg']="No data found.!!!";
					
				}
			}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
					
				}
			$retres=json_encode($retres);
			echo $retres;
		}
		
		//------------------------------------------delete agreement==========================================================
		public function delete_agreement($agreement_id,$session_id)
		{
		 // $request = json_decode(file_get_contents('php://input'), true);
			// $NotifyData = array(
				// 'notify_id' =>  $request['notify_id']
			// );
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->delete_agreement($agreement_id);
				$retres=array();
				if($result)
				{
					$retres['success']=1;
					$retres['msg']='Agreement deleted successfully !!!';
				}
				else
				{
					$retres['success']=0;
					$retres['msg']='Something went wrong. Please try again !!!';
				}
			}else{
						$retres['success']=0;
						$retres['msg']='Unauthorised User !!!';
				}
			$retres=json_encode($retres);
			echo $retres;
			
		}
		
		//=======================================agreement detail by id===================================================
		public function agreement_detail_by($agreement_id,$session_id)
		{
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->getAgreement($agreement_id);
				$retres=array();
				if($result)
				{
					$retres['success']=1;
					$retres['agreement_detail']=$result;
				}
				else
				{
					$retres['success']=0;
					$retres['msg']='Something went wrong. Please try again !!!';
				}
			}else{
						$retres['success']=0;
						$retres['msg']='Unauthorised User !!!';
				}
			$retres=json_encode($retres);
			echo $retres;
		}
		//=====================================edit agreement=========================================================
		    public function edit_agreement()
			{
				$request = json_decode(file_get_contents('php://input'), true);
				$header_content = isset($request['header_content'])?$request['header_content']:'';
				$header_image = isset($request['header_image'])?$request['header_image']:'';
				$watermark_image = isset($request['watermark_image'])?$request['watermark_image']:'';
				$footer_content = isset($request['footer_content'])?$request['footer_content']:'';
				
				$agreementData = array(
				'agreement_title' => $request['agreement_title'],
				'agreement_doc_content' => $request['agreement_doc_content'],
				'header_content' =>$header_content,
				'header_image' =>$header_image,
				'watermark_image' =>$watermark_image,
				'footer_content' =>$footer_content,
				'agreement_id' => $request['agreement_id']
				);
				
				$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
				$agreement_id = $request['agreement_id'];
				if($validate)
				{
					$result = $this->assetsapi_model->edit_agreement($agreementData);
					$retres=array();
					if($result)
					{
						$retres['success']=1;
						$retres['msg']="Agreement edited successfully !!!";
						$retres['edit_agreement']=$result;
						$user_id = $result[0]['user_id'];
				//===============================header image======================
			if(isset($request['header_image']) && $request['header_image']!='')
			{
					$headerImg = $header_image;
					$headImg = str_replace('data:image/jpeg;base64,', '', $headerImg);
					$img = str_replace('data:image/png;base64,', '', $headImg);
					$headerimage = base64_decode($img);
					// echo "<script>alert(".$image.")</script>";
					$image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
					$headerfilename = $image_name . '.' . 'png';
					//rename file name with random number
					// $path = './';
					$path = "assets/agreement_DOC/Owner/$user_id/$agreement_id/";
					$folderPath = mkdir($path, 0777, true);
					$headtargetPath = $path.$headerfilename;
					file_put_contents($headtargetPath , $headerimage);
			}
			//================================watermark image=========================================
			if(isset($request['watermark_image']) && $request['watermark_image']!='')
			{
					$watermark_Img = $watermark_image;
					$WImg = str_replace('data:image/jpeg;base64,', '', $watermark_Img);
					$waterimg = str_replace('data:image/png;base64,', '', $WImg);
					$wmimage = base64_decode($waterimg);
					// echo "<script>alert(".$image.")</script>";
					$wmimage_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
					$WMfilename = $wmimage_name . '.' . 'png';
					//rename file name with random number
					// $path = './';
					$path = "assets/agreement_DOC/Owner/$user_id/$agreement_id/";
					if (!file_exists($path)) {
						$folderPath = mkdir($path, 0777, true);
					}else{
						$folderPath = $path;
					}
					
					
					$WMtargetPath = $path.$WMfilename;
					file_put_contents($WMtargetPath, $wmimage);
			}
							$this->db->set(array('header_image'=>$headtargetPath,'watermark_image'=>$WMtargetPath));
							$this->db->where('agreement_id', $agreement_id);
							$this->db->update('agreement_tb');
							
							$pdfgeneration = $this->pdfGenerator($user_id,$request['agreement_id']);
					}
					else
					{
						$retres['success']=0;
						$retres['msg']='Something went wrong. Please try again !!!';
					}
				}else
				{
					$retres['success']=0;
					$retres['msg']='Unauthorised access !!!';
				}
				$retres=json_encode($retres);
				echo $retres;
			}
//====================================================AgreementEnd======================================================================

				
//===================================================================================================================================
	
	//=========================================================Add Services by service provider====================================================================
		public function getServices()
		{
			$query = $this->db->get('property_services_tb');
			$result = $query->result_array();
			
			$retres=array();
			
			if($result)
			{
				$retres['success']=1;
				$retres['services_list']=$result;
				
				
			}else{
				$retres['success']=0;
				$retres['msg']="No data found.!!!";
				
			}
			$retres=json_encode($retres);
			echo $retres;
		}
		
		public function add_service_by_provider()
		{
			$request = json_decode(file_get_contents('php://input'), true);
			// if(!empty($request['services_list']))
			// foreach($request['services_list'] as $key=>$val)
			// {
				// $serviceArr[$request['user_id']][$val['service_id']] = $val['service_id'];
			// }
			$dataToAdd = array(
				
				'user_id'=>$request['user_id'],
				'services_list' => $request['services_list']
				
				);
			
			$validate = $this->assetsapi_model->getSessionValidate($request['session_id)']);
			if($validate)
			{
				$result = $this->assetsapi_model->add_service_by_provider($dataToAdd);			
				if($result)
				{
					$retres['success']=1;
					$retres['msg']="Data inserted successfully. !!!";
					$retres['added_service']=$result;
					
					
				}else{
					$retres['success']=0;
					$retres['msg']="Somthing went wrong please try again.!!!";
					
				}
			}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
					
				}
			$retres=json_encode($retres);
			echo $retres;
		}
	
//======================================Add Services by service provider End ====================================
//================================================================================================================


//==========================================Send Email===========================================================
		public function send_mail($from_email='',$to_email='',$subject='',$tokensArr='',$template='')
		{ 
			$pattern = '[%s]';
			
			foreach($tokensArr as $key=>$val){
				$varMap[sprintf($pattern,$key)] = $val;
			}

			$emailContent = strtr($template,$varMap);
		  
		$config = Array(
                    'protocol' => 'smtp',
                    // 'smtp_host' => 'smtp.googlemail.com',
                    // 'smtp_port' => 587,
					//'smtp_host' => 'smtp.gmail.com', 
					'smtp_host' => 'smtpout.secureserver.net', 
					'smtp_crypto' => 'ssl',
					'smtp_port' => 465,
                    'smtp_user' => 'info@assetswatch.com',//'jirehhelpdesk@gmail.com', 
                    'smtp_pass' => 'Assets123',//my valid email password
                    'mailtype' => 'html',
                    'charset' => 'iso-8859-1',
                    'wordwrap' => TRUE
					
                  );
    $this->email->initialize($config);
    $this->load->library('email', $config);
    $this->email->set_newline("\r\n");  
    $this->email->from($from_email); 
    $this->email->to($to_email);
    $this->email->subject($subject);
    $this->email->message($emailContent);
		
		
   
         //Send mail 
          if($this->email->send()) 
			 return true; //$this->session->set_flashdata("email_sent","Email sent successfully."); 
          else 
			 return false;//$this->session->set_flashdata("email_sent","Error in sending Email."); 
      } 
//======================================================================Send Email End=============================================================================================
//========================================================================================================================================================================

//====================================================================User Search Start==========================================================================================	
	public function userSearch()
	{
		$request = json_decode(file_get_contents('php://input'), true);
		$datatopass = array(
			"assets_type"=>$request['assets_type'],
			"string"=>$request['string'],
			"userid"=>$request['userid'],
		);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->userSearch($datatopass);
			$retres = array();
			if($result)
			{
				$retres['success']=1;
				$retres['users']=$result;
				
			}else{
					$retres['success']=0;
					$retres['msg']="No records found.!!!";
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
		}
		$retres=json_encode($retres);
		echo $retres;
	}
//====================================================================User Search End==========================================================================================	
//===============================================================================================================================================================================	


//======================================================================get countries=========================================================================================
	public function country()
	{
		$result = $this->assetsapi_model->getCountries();
		$retres = array();
		if($result)
		{
			$retres['success']=1;
			$retres['countries']=$result;
			
		}else{
				$retres['success']=0;
				$retres['msg']="No records found.!!!";
				
		}
		$retres=json_encode($retres);
		echo $retres;
	}
//==============================================================================================================================================================================

//==========================================================================get states=============================================================================================
	public function state($name)
	{
		$Name = utf8_decode(urldecode($name));
		$result = $this->assetsapi_model->getStates($Name);
		$retres = array();
		if($result)
		{
			$retres['success']=1;
			$retres['states']=$result;
			
		}else{
				$retres['success']=0;
				$retres['msg']="No records found.!!!";
				
		}
		$retres=json_encode($retres);
		echo $retres;
	}
//==============================================================================================================================================================================

//=====================================================================================get cities==========================================================================
	public function city($name)
	{
		$Name = utf8_decode(urldecode($name));
		$result = $this->assetsapi_model->getCities($Name);
		$retres = array();
		if($result)
		{
			$retres['success']=1;
			$retres['cities']=$result;
			
		}else{
				$retres['success']=0;
				$retres['msg']="No records found.!!!";
				
		}
		$retres=json_encode($retres);
		echo $retres;
	}
//=================================================================================================================================================================
//=================================================================================================================================================================

//========================================Static count service provider=======================================
	public function statics_service_provider($user_id,$session_id)
	{
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->statics_service_provider($user_id);
			$retres = array();
			if($result)
			{
				$retres['success']=1;
				$retres['statics']=$result;
				
			}else{
					$retres['success']=0;
					$retres['msg']="No statics found.!!!";
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
		}
		$retres=json_encode($retres);
		echo $retres;
	}
	
	public function recent_resolved_request($user_id,$session_id)
	{
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->recent_resolved_request($user_id);
			$retres = array();
			if($result)
			{
				$retres['success']=1;
				$retres['recent_resolve_request']=$result;
				
			}else{
					$retres['success']=0;
					$retres['msg']="No Data found.!!!";
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
		}
		$retres=json_encode($retres);
		echo $retres;
		
	}
	//===============================================================================================================
	
	//==========================================Singular Bill Pay Enroll=====================================
	public function singularbill_enroll()
	{
			$request = json_decode(file_get_contents('php://input'), true);
			
			$partnerkey = $this->config->item('partnerkey');
			$partnerid = $this->config->item('partnerid');
			$datatosend = array(
				"partnerkey"=>$partnerkey,
				"partnerid"=>$partnerid,
				"email"=>$request["email"],
				"dba_name"=>$request["dba_name"],
				"legal_name"=>$request["legal_name"],
				"business_address_line_1"=>$request["business_address_line_1"],
				"business_city"=>$request["city"],
				"business_state_province"=>$request["state"],
				"business_postal_code"=>$request["zip_code"],
				"business_phone_number"=>$request["mobile_no"],
				"principal_first_name"=>$request["first_name"],
				"principal_last_name"=>$request["last_name"],
				"fed_tax_id"=>$request["fed_tax_id"]
				
			);
			
			$jsondata = json_encode($datatosend);
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.singularbillpay.com/v1/enroll",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => $jsondata,
				  CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json"
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  
				  $responseArr = json_decode($response);
				  
				}
			 $retres=array();
			 if($responseArr)
			 {
					 $retres['success']=1;
					 $retres['msg']='Sub-Mechant Account Initiated Successfully, Key will update soon.!!!';
					 $retres['enroll']=$responseArr;
					 
					 $clientpartnerid = $responseArr->clientpartnerid;
					 $enrollmentlink = $responseArr->enrollmentlink;
					 
					 $responsedata= array('clientpartnerid' => $clientpartnerid,
					 'enrollmentlink' => $enrollmentlink);
					 
					 $DataTostore = $responsedata + $request;
					 
					  $enrollInfo = $this->assetsapi_model->storeEnrollinfo($DataTostore);
					 
					 $retres['enrollInfo']=$enrollInfo;
					  
					 
								
			 }else{
					 $retres['success']=0;
					 $retres['msg']='Somthing went wrong please try later.!!!';
					
				 }
				 $retres=json_encode($retres);
				 echo $retres;
	}
	public function enroll_info($user_id,$session_id){
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
		if($validate)
		{
			$result = $this->assetsapi_model->enroll_info($user_id);
			$retres = array();
			if($result)
			{
				$retres['success']=1;
				$retres['enroll_info']=$result;
				
			}else{
					$retres['success']=0;
					$retres['msg']="No records found.!!!";
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
		}
		$retres=json_encode($retres);
		echo $retres;
	}
	
	public function MerchantStatus()
	{
			
			
			 $partnerkey = $this->config->item('partnerkey');
			 $partnerid = $this->config->item('partnerid');
			
			$MerchantStatus = $this->assetsapi_model->getMerchantStatus();
			 // print_r($MerchantStatus);
			
			
			foreach($MerchantStatus as $merch)
			{
				$fed_tax_id = $merch['fed_tax_id'];
				$datatosend = array(
				"partnerkey"=>$partnerkey,
				"partnerid"=>$partnerid,
				"fed_tax_id"=>$fed_tax_id
				
				);
			  // print_r($datatosend);
			 
			$jsondata = json_encode($datatosend);
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://devapi.singularbillpay.com/v1/MerchantStatus",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => $jsondata,
				  CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json"
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				  echo "cURL Error #:" . $err;
				   log_message("cURL Error #:", $err);
				} else {
				  
				  $responseArr = json_decode($response);
				  // print_r($responseArr);
				  if(count($responseArr->subaccounts)>0){
					  
				  $ACC = $responseArr->subaccounts;
				     
				  

					  $clientpartnerkey = $ACC[0]->clientpartnerkey;
					  $dbaname = $ACC[0]->dbaname;
					  $status = $ACC[0]->status;
					  $statusmessage = $ACC[0]->statusmessage;
					  
					  $dataToUpdate = array(
					  'clientpartnerkey'=>$clientpartnerkey,
					  'sub_dbaname'=>$dbaname,
					  'sub_status'=>$status,
					  'sub_statusmessage'=>$statusmessage
					  );
					  // print_r($dataToUpdate);
					$update=$this->db->update('enroll_submerchant_tb',$dataToUpdate,array('fed_tax_id'=>$fed_tax_id)); 
				  }else{
					  log_message('debug', 'Due to some error subaccount not created... ');
				  }
				  }
				  
				 
			}
			// print_r($client_partner_id);
			 
			 
	}
	public function change_merchant_status($id,$status,$session_id){
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				if($status=="Active"){
					$updateStatus = "Inactive";
				}else if($status=="Inactive"){
					$updateStatus = "Active";
				}
				
				$changeStatus = $this->db->update('enroll_submerchant_tb',array('status'=>$updateStatus),array('id'=>$id));
				
					
				
					
					if($changeStatus)
					{
						$retres['success']=1;
						$retres['msg']= "Account status changed.";
						
					}else{
							$retres['success']=0;
							$retres['msg']="Something went wrong.Please try again!!!";
							
					}
				
			}else{
						$retres['success']=0;
						$retres['msg']="Unauthorised User.!!!";
			}
			$retres=json_encode($retres);
			echo $retres;
	}
	//============================================================================================================
	
	//===============================property search================================================================
	public function property_search_by(){
		$request = json_decode(file_get_contents('php://input'), true);
		$datatopass = array(
			"keyword"=>$request['keyword']
			
		);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->property_search_by($datatopass);
			$retres = array();
			if($result)
			{
				$retres['success']=1;
				$retres['property_list']=$result;
				
			}else{
					$retres['success']=0;
					$retres['msg']="No records found.!!!";
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
		}
		$retres=json_encode($retres);
		echo $retres;
	}
	
	//Start Elakkiya//
	//==============================================Forgot Password======================================================================
	public function forgot_password(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'email'  => $request['email'],
		);
		$ses_id = $this->assetsapi_model->get_session_id($data);
		$session_id = isset($ses_id[0]->session_id)?$ses_id[0]->session_id:'';
		// $url = "www.assetswatch.com/reset_password?id=".$session_id;
		$reacturl = $this->config->item('reacturl');
		 $url = "<br/><a href='".$reacturl."reset-password?id=".$session_id."' style='text-decoration: none;color: #FFFFFF;font-size: 17px;font-weight: 400;line-height: 120%;padding: 9px 26px;margin: 0;text-decoration: none;border-collapse: collapse;border-spacing: 0;border-radius: 4px;-webkit-border-radius: 4px;text-align: -webkit-center;vertical-align: middle;background-color: rgb(87, 187, 87);-moz-border-radius: 4px;-khtml-border-radius: 4px;'>Reset Password</a>";
		
		if($session_id!='' || !empty($session_id)){
		$record = array(
			'assets_type'		=> $request['assets_type'],
			'email'				=> $request['email'],
			'current_session_id'=> $session_id,
			'status'			=> 'pending',
			'created_date' 		=> date('Y-m-d H:i:s')
		);
		$ins_rec = $this->assetsapi_model->insert_forgot_password_status($record);
		
			$result['success'] = 1;
			$result['url'] = $url;
				
				$to_email = $request['email'];
				$from_email = "info@assetswatch.com";
				//========================Mail======================================================================
				$filename = 'forget_password.txt';
				$template = read_file('assets/email_template/'.$filename);
				$subject = "Forget Password";
				
				$tokensArr = array(
				 'URL'=> trim($url)
				);
				$mail = $this->send_mail($from_email,$to_email,$subject,$tokensArr,$template);
				if($mail){
					$result['success'] = 1;
					$result['msg'] = "Password reset link sended to your email.!!!";
				}else{
					$result['success'] = 0;
					$result['msg'] = "Somthing went wrong. Please try later!!!";
				}
		}
		else{
			$result['success'] = 0;
			$result['msg'] = "Unauthorised User.!!!";
		}
		echo json_encode($result);
	}
	public function change_password(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'password' 	=> $request['password'],
			'session_id' 	=> $request['session_id'],
		);
		$email = $this->assetsapi_model->get_email($data['session_id']);
		
		if(!empty($email)){
			$rec = array(
					'email' => $email[0]->email,
					'password' 	=> $request['password'],
					'session_id' 	=> $request['session_id']
				);
			
				$upd_pwd = $this->assetsapi_model->update_password($rec);
				if($upd_pwd==1){
					$upd_status = $this->db->delete('forgot_password_status_tb',array('current_session_id'=>$request['session_id']));
					$result['success'] = 1;
					$result['msg'] = "Password Updated Successfully";
				}else{
					$result['success'] = 0;
					$result['msg'] = "This link is expired. Please request for new link.!!!";
				}
			
		}
		else{
			$result['success'] = 0;
			$result['msg'] = "Unauthorised User.!!!";
		}
		echo json_encode($result);
	}
	
	public function forget_pass_data($session_id)
	{
		$fPassQuery = $this->db->get_where('forgot_password_status_tb',array('current_session_id'=>$session_id));
			if($fPassQuery->num_rows()>0)
			{
				$result['success'] = 1;
				
			}
			else{
					$result['success'] = 0;
					$result['msg'] = "This link is expired. Please request for new link.!!!";
				}
			echo json_encode($result);
	}
	//==============================================Owner-agent rating======================================================================
	public function rating(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'owner_id'	=> $request['user_id'],
			'agent_id'	=> $request['agent_id'],
			'rating' 	=> $request['rating'],
			'feedback' 	=> $request['feedback'],
			'created_date'=>date("Y-m-d H:i:s")
			);
			$rating = $this->assetsapi_model->insert_rating($data);
			
			if($rating){
				$result['success'] = 1;
				$result['msg'] = "Rated Successfully";
			}else{
				$result['success'] = 0;
				$result['msg'] = "Something went wrong. Please try again !!! ";
			}
			
			echo json_encode($result);
	}
	//==============================================Get Owner's relevant service provider ==========================================================
	public function get_serviceprovider(){
		$request = json_decode(file_get_contents('php://input'), true);
		$owner_id = $request['owner_id'];
		$sp_id = $this->assetsapi_model->get_serviceprovider_by_owner($owner_id);
		$id = explode(',',$sp_id);
		
		foreach($id as $assets_id){
			$agent = $this->assetsapi_model->get_serviceprovider($assets_id);
			if($agent!='')
				$result[] = $agent;
		}
		if($sp_id){
			if($result){
				$output['success'] = 1;
				$output['service_provider'] = $result;
			}else{
				$output['success'] = 0;
				$output['msg'] = "No records found ";
			}
		}else{
			$output['success'] = 0;
			$output['msg'] = "Something went wrong. Please try again !!! ";
		}
		echo json_encode($output);
	}
	//==============================================Insert service request ==========================================================
	public function add_service_request(){
		$request = json_decode(file_get_contents('php://input'), true);
		
		$deal = $this->assetsapi_model->get_deal_id($request['property_id']);
		if(!empty($deal)){
		$data = array(
			'deal_id'			=> $deal->deal_id,
			'send_by'			=> $request['user_id'],
			'service_provider'	=> $request['service_provider'],
			'service_msg' 		=> $request['description'],  
			'service_photo' 	=> $request['service_photo'],
			'service_status' 	=> 0,
			'entry_date'		=>date("Y-m-d H:i:s")
			); 
		$service_request = $this->assetsapi_model->insert_service_request($data);
		
			if($service_request){
				$result['success'] = 1;
				$result['msg'] = "Service request added Successfully";
			}else{
				$result['success'] = 0;
				$result['msg'] = "Something went wrong. Please try again !!! ";
			}
		}
		else{
			$result['success'] = 0;
			$result['msg'] = "Something went wrong. Please try again !!! ";
		}
		
		echo json_encode($result);
	}
	//============================================== Property Report ==========================================================
	public function property_report(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'property_id' 	=> $request['property_id'],
			'user_id' 		=> $request['user_id'],
			'from_date' 	=> date("Y-m-d H:i:s",strtotime($request['from_date'])),
			'to_date' 		=> date("Y-m-d H:i:s",strtotime($request['to_date']))
		);
			$report = $this->assetsapi_model->get_property_report($data);
			if($report){
				$result['success'] = 1;
				$result['report'] = $report;
			}else{
				$result['success'] = 0;
				$result['msg'] = "No records found";
			}
			echo json_encode($result);
	}
	//============================================== Transaction Report ==========================================================
	public function transaction_report(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'user_id' 		=> $request['user_id'],
			'from_date' 	=> date("Y-m-d H:i:s",strtotime($request['from_date'])),
			'to_date' 		=> date("Y-m-d H:i:s",strtotime($request['to_date']))
		);
		$report = $this->assetsapi_model->get_transaction_report($data);
		if($report){
			$result['success'] = 1;
			$result['report'] = $report;
		}else{
			$result['success'] = 0;
			$result['msg'] = "No records found";
		}
		echo json_encode($result);
	}
	
		public function download_trans_invoice_report($invoiceId){


					$InvoiceFilename = 'invoice.txt';
					$InvoiceTemplate = read_file('assets/email_template/'.$InvoiceFilename);
					
					$transData = $this->assetsapi_model->getTransactionBy($invoiceId);
					
					 $transactionamount = $transData[0]['transactionamount'];
					 $address  = $transData[0]['city'].", ".$transData[0]['state'].", ".$transData[0]['country'].", ".$transData[0]['zip_code'];
					 $TaxAmount = 0;
					 $TotAmount = $transactionamount + $TaxAmount;
					 $title = $transData[0]['trans_for'];
					 $transactiondate = $transData[0]['transactiondate'];
					 
					$InvoiceTokensArr = array(
					'ADDRESS' => $address,
					 'TITLE'=> $title,
					 'AMOUNT'=> $transactionamount,
					 'TAX_AMOUNT'=> $TaxAmount,
					 'TOTAL_AMOUNT'=> $TotAmount,
					 'INVOICE'=>$invoiceId,
					 'INVOICE_DATE'=>$transactiondate
					);
					$pattern = '[%s]';
				
					foreach($InvoiceTokensArr as $key=>$val){
						$varMap[sprintf($pattern,$key)] = $val;
					}
					$Template = strtr($InvoiceTemplate,$varMap);
			
					$dompdf = new Dompdf\Dompdf();
				 
						// (Optional) Setup the paper size and orientation
						// print_r($Template);
						 $dompdf->set_option('isHtml5ParserEnabled', true);
						$dompdf->loadHtml($Template);
						$dompdf->setPaper('A4', 'landscape');
						// Render the HTML as PDF
						$dompdf->render();
				 
						// Get the generated PDF file contents
						$pdf = $dompdf->output();
				 
						// Output the generated PDF to Browser
						$dompdf->stream();
						
 }
	//============================================== Contact Report ==========================================================
	public function contact_report(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'user_id' 		=> $request['user_id'],
			'from_date' 	=> date("Y-m-d H:i:s",strtotime($request['from_date'])),
			'to_date' 		=> date("Y-m-d H:i:s",strtotime($request['to_date']))
		);
		$report = $this->assetsapi_model->get_contact_report($data);
		if($report){
			$result['success'] = 1;
			$result['report'] = $report;
		}else{
			$result['success'] = 0;
			$result['msg'] = "No records found";
		}
		echo json_encode($result);
	}
	//======================================================== Search API ============================================================
	public function search_api(){
		$request = json_decode(file_get_contents('php://input'), true);
		$keyword 		= isset($request['keyword']) ? $request['keyword'] : '';
		$property_type 	= isset($request['property_type']) ? $request['property_type'] : '';
		$city 			= isset($request['city']) ? $request['city'] : '';
		$property_status= isset($request['property_status']) ? $request['property_status'] : '';
		$area 			= isset($request['area']) ? $request['area'] : '';
		$zip_code 		= isset($request['zip_code']) ? $request['zip_code'] : '';
		
		$propertyData = array(
			'keyword' 		 => $keyword,
			'city' 			 => $city,
			'property_type'  => $property_type,
			'property_status'=> $property_status,
			'area' 			 => $area,
			'zip_code' 		 => $zip_code,
			'owner_id'		 => $request['owner_id']
		);
		$report = $this->assetsapi_model->property_search_by_user($propertyData);
		if($report){
			$result['success'] = 1;
			$result['report'] = $report;
		}else{
			$result['success'] = 0;
			$result['msg'] = "No records found";
		}
		echo json_encode($result);
	}
	//======================================================== BGV  ============================================================
	public function User_background_verification(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'user_id' 			=> $request['user_id'],
			'selected_package' 	=> $request['package'],
			'created_date' 		=> date("Y-m-d H:i:s")
		);
		if(!empty($data['user_id'])){
		$record = $this->assetsapi_model->add_bgv($data);
		
			$result['success'] = 1;
			$result['msg'] = "Details Added Successfully";
		}
		else{
			$result['success'] = 0;
			$result['msg'] = "Something went wrong. Please try again !!! ";
		}
		echo json_encode($result);
	}
	//================================================= BGV document============================================================
	public function bgv_document(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'user_id' 			=> $request['user_id'],
			'document' 			=> $request['document']
			);
		$record = $this->assetsapi_model->update_bgv_document($data);
		if($record){
			$result['success'] = 1;
			$result['msg'] = "Document Updated Successfully";
		}else{
			$result['success'] = 0;
			$result['msg'] = "Invalid user_id";
		}
		echo json_encode($result);
	}
	//=================================================Download BGV document============================================================
	public function download_bgv_document(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'user_id' 			=> $request['user_id'],
			'document' 			=> $request['document']
			);
		$bgv = $this->assetsapi_model->find_bgv_document($data);
		if($bgv){
			$result['success'] = 1;
			$result['download_url'] = $bgv;
		}else{
			$result['success'] = 0;
			$result['msg'] = "Something went wrong. Please try again !!! ";
		}
		echo json_encode($result);
	}
	//=================================================Payment============================================================
	public function payment_details(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'user_id' 		=> $request['user_id'],
			'name' 			=> $request['name'],
			'legal_name' 	=> $request['legal_name'],
			'address' 		=> $request['address'],
			'city' 			=> $request['city'],
			'zip_code' 		=> $request['zip_code'],
			'mobile_no' 	=> $request['mobile_no'],
			'email' 		=> $request['email'],
			'first_name' 	=> $request['first_name'],
			'last_name' 	=> $request['last_name'],
			'payment_by'	=> $request['payment_by'], //singlular_billpay, amazon, paypal
			'created_date' 	=> date("Y-m-d H:i:s")
			);
		if(!empty($data['user_id'])){
		$record = $this->assetsapi_model->add_payment_details($data);
		
			$result['success'] = 1;
			$result['msg'] = "Details Added Successfully";
		}
		else{
			$result['success'] = 0;
			$result['msg'] = "Something went wrong. Please try again !!! ";
		}
		echo json_encode($result);
	}
	//=================================================card_profile============================================================
	public function card_profile(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'user_id' 		=> $request['user_id'],
			'name' 			=> $this->assetsapi_model->encrypt_decrypt('encrypt',$request['name']),
			'card_no' 		=> $this->assetsapi_model->encrypt_decrypt('encrypt',$request['card_no']),
			'exp_date' 		=> $this->assetsapi_model->encrypt_decrypt('encrypt',$request['exp_date']),
			'created_date' 	=> date("Y-m-d H:i:s")
			);
		if(!empty($data['user_id'])){
		$record = $this->assetsapi_model->add_card_profile($data);
			$result['success'] = 1;
			$result['msg'] = "Details Added Successfully";
		}
		else{
			$result['success'] = 0;
			$result['msg'] = "Something went wrong. Please try again !!! ";
		}
		echo json_encode($result);	
	}
	public function get_card_details(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'user_id' 		=> $request['user_id']
			);
		$record = $this->assetsapi_model->get_card_details($data);
		if($record){
			$result['success'] = 1;
			$result['data'] = $record;
		}
		else{
			$result['success'] = 0;
			$result['msg'] = "Unauthorised user ";
		}
		echo json_encode($result);	
	}
	//---------Start Elakkiya on 11.08.2018---------------------
	
	//================================================ To get Agreement content========================================================
	public function agreement_content(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'deal_id' 		=> $request['deal_id']
			);
		$record = $this->assetsapi_model->get_agreement_content($data);
		if($record){
			$result['success'] = 1;
			$result['data'] = $record;
		}
		else{
			$result['success'] = 0;
			$result['msg'] = "Invalid deal_id";
		}
		echo json_encode($result);	
	}
	//=================================================== Agreement Acceptance =========================================
	public function agreement_acceptance(){
		$request = json_decode(file_get_contents('php://input'), true);
		$verData = $this->assetsapi_model->get_version_agreement($request['deal_id']);
		$version = $verData[0]['version_name'];
		$strReplace = trim(str_replace('AWG_V','',$version));
		$actualversion = 'AWG_V'.($strReplace + 1);
		
		$data = array(
			'deal_id' 			=> $request['deal_id'],
			'comment' 			=> $request['comment'],
			'user_id' 			=> $request['user_id'],
			'agreement_content' => $request['agreement_content'],
			'div_id' 			=> $request['div_id'],
			'signature_content' => $request['signature_content'],
			'signature_type' 	=> $request['signature_type'],
			'acceptance_status' => '1', //Submitted
			'created_date'		=> date("Y-m-d H:i:s"),
			'version_name'		=> $actualversion
			);
			
		$record = $this->assetsapi_model->agreement_acceptance($data);
		if($record){
			$result['success'] = 1;
			$result['msg'] = 'Agreement Accepted Successfully';
			
			$datatoupdate = array(
			'replaced_template' 	=> $request['agreement_content'],
			'status' => 'Inprocess'
			);
			$this->db->update('property_deal_tb',$datatoupdate,array('deal_id'=>$request['deal_id']));
		}
		else{
			$result['success'] = 0;
			$result['msg'] = "No input";
		}
		echo json_encode($result);	
	}
	//================================================ Owner submitted deal  ==================================================
	public function get_submitted_deal(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'user_id' 		=> $request['user_id']
			);
		$record = $this->assetsapi_model->get_submitted_deal($data);
		if(!empty($record)){
			$deal_id = explode(',',$record->deal_id);
			foreach($deal_id as $id){
				$res[] = $this->assetsapi_model->fetch_submitted_deal($id);
			}
			
			if($res){
				$result['success'] = 1;
				$result['data'] = $res;
			}
			else{
				$result['success'] = 0;
				$result['msg'] = "Invalid Userid";
			}
		}else{
			$result['success'] = 0;
			$result['msg'] = "Invalid Userid";
		}
		
			echo json_encode($result);	
	}
	//======================================= View Submitted deal ============================================
	public function view_submitted_deal(){
		$request = json_decode(file_get_contents('php://input'), true);
		$data = array(
			'id' 		=> $request['id']
			);
		$agreement_version = $this->assetsapi_model->view_submitted_data($data);
		if($agreement_version){
		 	$result['success'] = 1;
		 	$result['data'] = $agreement_version;
		 }
		 else{
		 	$result['success'] = 0;
		 	$result['msg'] = "Invalid input";
		 }
		 echo json_encode($result);
	}
	//======================================== Download Agreement PDF ===========================================
	public function  download_agreement($deal_id){
		// $request = json_decode(file_get_contents('php://input'), true);	
		// $data = array(
			// 'deal_id' 		=> $request['deal_id']
		// );
		$record = $this->assetsapi_model->get_agreement($deal_id);
		// print_r($record);
		// exit();
		// $agreement ='';
		// $agreement .= '<div class="col-md-12" align="center" style="text-align:center;text-transform: uppercase; text-decoration: underline;"><h1>'.$record->agreement_title.'</h1></div>';
		// $agreement .= '<div class="col-md-12" align="center" style="text-align:justify; line-spacing: 2px; font-size: 20px;">'.$record->agreement_content.'</div>'; 
		// $agreement .= '<div class="col-md-12" align="right">'.$record->signature_content.'</div>';
		
			// Create a new PDF but using our own class that extends TCPDF
			$pdf = new MyCustomPDFWithWatermark(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			
			$headerContent = $record->header_content;
			$footerContent = $record->footer_content;
			$headerImage = $record->header_image;
			$watermarkImage = $record->watermark_image;
			
			$html = $record->replaced_template;
			// $html .= "<style>.sigDiv{width: 297px; height: 97px;}</style>";
			
			$template = array('headerContent'=>$headerContent,'footerContent'=>$footerContent,'headerImage'=>$headerImage,'watermarkImage'=>$watermarkImage);
			//echo $headerImage;
			// print_r($template);
			// exit();
			$pdf->setData($template);
			
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
			
			// set header and footer fonts
				$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
				$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

				// set default monospaced font
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
			
			// set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
			
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);//, 70,120, 57, 25, '', '', '', false, 300, '', false, false, 0);
			
			$pdf->SetFont('times', '', 8, '', false); 
				
			$pdf->AddPage();
			$pdf->writeHTML($html, true, false, true, false, '');
			ob_clean();
			
			$fileName = $record->agreement_unique_id.'.pdf';
			 $pdf->Output($fileName,'D');
			

			/* $pdf->writeHTML($agreement, true, false, true, false, '');
			$path = "/";
			$filename = "Agreement_".$request['deal_id']=1;
			$folderPath = mkdir($path, 0777, true);
			$pdfFilePath = APPPATH.'../'.$path.$filename.'.pdf';
			 ob_clean();
			  $pdfgenerated = $pdf->Output($pdfFilePath, 'I'); */
			 
		}
		
		//===============================Unsubscribe Plan============================================
		public function unsubscribe_plan($user_id,$assetsTypeId,$session_id)
		{
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$profile = $this->assetsapi_model->profile($user_id);
				$planName = $profile['planName'];
				$retres = array();
				if($planName=='Basic')
				{
					$retres['success']=0;
					$retres['msg']="Already In Basic Plan!!!";
				}else{
					
					$result = $this->assetsapi_model->unsubscribe_plan($user_id,$assetsTypeId);
					
					if($result)
					{
						$retres['success']=1;
						$retres['msg']= "Unsubscribe successfully.";
						
					}else{
							$retres['success']=0;
							$retres['msg']="Something went wrong.Please try again!!!";
							
					}
				}
				
			}else{
						$retres['success']=0;
						$retres['msg']="Unauthorised User.!!!";
			}
			$retres=json_encode($retres);
			echo $retres;
		}
	
//==============================Notification UserList ================================================	
		public function userlist_notification($user_id,$assets_type,$session_id)
		{
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->userlist_notification($user_id,$assets_type);
				$retres = array();
				if($result)
				{
					$retres['success']=1;
					$retres['userlist']= $result;
					
				}else{
						$retres['success']=0;
						$retres['msg']="Something went wrong.!!!";
						
				}
			}else{
						$retres['success']=0;
						$retres['msg']="Unauthorised User.!!!";
			}
			$retres=json_encode($retres);
			echo $retres;
		}
		
		//=================================get Agreement template==================================================
		
		public function agreement_template_name($session_id)
		{
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->agreement_template_name();
				$retres = array();
				if($result)
				{
					$retres['success']=1;
					$retres['template_list']= $result;
					
				}else{
						$retres['success']=0;
						$retres['msg']="Something went wrong.!!!";
						
				}
			}else{
						$retres['success']=0;
						$retres['msg']="Unauthorised User.!!!";
			}
			$retres=json_encode($retres);
			echo $retres;
		}
		public function agreement_template_detail($template_id,$session_id)
		{
			$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->agreement_template_detail($template_id);
				$retres = array();
				if($result)
				{
					$retres['success']=1;
					$retres['template_detail']= $result;
					
				}else{
						$retres['success']=0;
						$retres['msg']="Something went wrong.!!!";
						
				}
			}else{
						$retres['success']=0;
						$retres['msg']="Unauthorised User.!!!";
			}
			$retres=json_encode($retres);
			echo $retres;
		}
	//=========================================contact info for contact us page =============================
		public function contactinfo()
		{
			
				$result = $this->assetsapi_model->contactinfo();
				$retres = array();
				if($result)
				{
					$retres['success']=1;
					$retres['contactinfo']= $result;
					
				}else{
						$retres['success']=0;
						$retres['msg']="Something went wrong.!!!";
						
				}
			
			$retres=json_encode($retres);
			echo $retres;
		}
		
		public function social_links(){
			$data['googleloginURL'] = $this->google->loginURL();
         //linkedin
		 $data['linkedinURL'] = base_url().$this->config->item('linkedin_redirect_url').'?oauth_init=1';
		 $data['fblogoutURL'] = $this->facebook->logout_url();
          $data['fbauthURL'] =  $this->facebook->login_url();
		   $data['twitterUrl'] = base_url().'assetsapi/twitter';
		   $retres['data']=$data;
		   $retres=json_encode($retres);
			echo $retres;
		}
		 
		public function change_status_execute()
		{
			$request = json_decode(file_get_contents('php://input'), true);
			
			
			$agreementData = array(
				'property_id'=>$request['property_id']
				);
				
				$retres=array();
			
				$result = $this->assetsapi_model->change_status_execute($agreementData);
				if($result)
				{
					$retres['success']=1;
					$retres['msg']="Agreement Completed successfully. !!!";
					
					
					
				}else{
					$retres['success']=0;
					$retres['msg']="Somthing went wrong please try again.!!!";
					
				}
		
			$retres=json_encode($retres);
			echo $retres;
		}
	
	//===========================user search =====================================
	public function user_search($request=array())
	{
		
		$request = json_decode(file_get_contents('php://input'), true);
		$datatopass = array(
			"assets_type"=>$request['assets_type'],
			"keyword"=>$request['keyword']
		);
		$validate = $this->assetsapi_model->getSessionValidate($request['session_id']);
		if($validate)
		{
			$result = $this->assetsapi_model->user_search($datatopass);
			$retres = array();
			if($result)
			{
				$retres['success']=1;
				$retres['search_userlist']=$result;
				
			}else{
					$retres['success']=0;
					$retres['msg']="No records found.!!!";
					
			}
		}else{
					$retres['success']=0;
					$retres['msg']="Unauthorised User.!!!";
		}
		$retres=json_encode($retres);
		echo $retres;
	}
	
	//========================rating DEtail ===================================
	public function rating_detail($id,$session_id)
	{
		$validate = $this->assetsapi_model->getSessionValidate($session_id);
			if($validate)
			{
				$result = $this->assetsapi_model->rating_detail($id);
				$retres = array();
				if($result)
				{
					$retres['success']=1;
					$retres['rating_detail']= $result;
					
				}else{
						$retres['success']=0;
						$retres['msg']="Something went wrong.!!!";
						
				}
			}else{
						$retres['success']=0;
						$retres['msg']="Unauthorised User.!!!";
			}
			$retres=json_encode($retres);
			echo $retres;
	}
	
	//=======================swith user type=================================
	public function switch_usertype($request=array())
	{
		
		$request = json_decode(file_get_contents('php://input'), true);
		$datatopass = array(
			"email"=>$request['email'],
			"assets_type"=>$request['assets_type']
		);
		
			$result = $this->assetsapi_model->switch_usertype($datatopass);
			$retres = array();
			if($result)
			{
				$retres['success']=1;
				$retres['userType']=$result;
				
			}else{
					$retres['success']=0;
					$retres['msg']="No records found.!!!";
					
			}
		
		$retres=json_encode($retres);
		echo $retres;
	}
	
	//=======================Background Verification=================================
	public function background_verification($request=array())
	{
		
		 $request = json_decode(file_get_contents('php://input'), true);  
		
			
			$partnerkey = $this->config->item('partnerkey');
			$partnerid = $this->config->item('partnerid');
			$transactiontype = $this->config->item('transactiontype');
			
			$randNumber = mt_rand(100000, 999999);
			$orderid = 'TXN'.$randNumber;
			$this->session->set_userdata('orderid', $orderid);
			 $userData = $this->assetsapi_model->profile($request["login_user_id"]);
			//$planData = $this->assetsapi_model->planDeatilBy($plan_id);
			$plan_id = $userData['plan_id'];
			/*$first_name = $userData['first_name'];
			$last_name = $userData['last_name'];
			$address = $userData['city']." ".$userData['state']." ".$userData['country']." ".$userData['zip_code'];
			$city = $userData['city'];
			$state = $userData['state'];
			$country = $userData['country'];
			$zip = $userData['zip_code'];
			$email = $userData['email']; */
			//$orderid = $this->session->userdata('orderid');
			//$explodeName = explode(' ' ,$_POST['name'])
			//$firstName = (!empty($explodeName[0]))?$explodeName[0]:'';
			//$lastName = (!empty($explodeName[1]))?$explodeName[1]:'';
			//$expirymmyy = date('m',strtotime($_POST['month'])).date('y',strtotime($_POST['year']));
			$datatosend = array(
				"partnerkey"=>$partnerkey,
				"partnerid"=>$partnerid,
				"transactiontype"=>$transactiontype,
				"tokenizedaccountnumber"=>$request["tokenizedaccountnumber"],
				"expirymmyy"=>$request["expirymmyy"],
				"cvv"=>$request["cvv"],
				"paymentmode"=>$request["paymentmode"],

				"transactionamount"=>0,//$request["amount"],
				"routingnumber"=>$request["routingnumber"],
				"surchargeamount"=>$request["surchargeamount"],
				"currency"=>$request["currency"],
				"payeefirstname"=>$request["first_name"],
				"payeelastname"=>$request["last_name"],
				"address"=>$request["address"],
				"city"=>$request["city"],
				"state"=>$request["state"],
				"country"=>$request["country"],
				"zip"=>$request["zip_code"],
				"email"=>$request["email"],
				"transactionreference"=>$request["transactionreference"],
				 "orderid"=>$orderid,
				"payeeid"=>$request["payeeid"],
				"notifypayee"=>$request["notifypayee"],
				"profile"=>$request["profile"],
				"profileid"=>$request["profileid"]
			);
			//print_r($datatosend);
			//exit();
			$jsondata = json_encode($datatosend);
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.singularbillpay.com/v1/transaction",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				 CURLOPT_POSTFIELDS => $jsondata,
				 CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json"
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  //echo $response;
				  // $txn_id = $this->session->userdata('txn_no');
				  $responseArr = json_decode($response);
				  if($orderid == $responseArr->orderid)
				  {
					  $this->db->order_by('transactiondate',"desc");
						$this->db->limit(1);
						$query = $this->db->get('transaction_tb');
						 $lastRecord = $query->result_array();
					  if($lastRecord[0]['invoice_number']!='' || $lastRecord[0]['invoice_number']!=null ){
						  $inv = $lastRecord[0]['invoice_number'];
						  
						    $inv_number = ++$inv;
					  }else{
						  $inv_number = 'AWLLC000001';
					  }
					  
					   $invoice_number = $inv_number;
					   $datatoinsertTrans = array( 
									'invoice_number' => $invoice_number,
									'user_id'=>$request["login_user_id"],
									'plan_id'=> $plan_id,
									'orderid'=> $orderid,
									'plan_amount'=>$request["amount"],
									'transactionid'=>$responseArr->transactionid,
									'paymentmode'=>$responseArr->paymentmode,
									'transaction_type'=>$responseArr->transactiontype,
									'transactionamount'=>$responseArr->transactionamount,
									'transactionreference'=>$responseArr->transactionreference,
									'responsecode'=>$responseArr->responsecode,
									'responsestatus'=>$responseArr->responsestatus,
									'responsemessage'=>$responseArr->responsemessage,
									'transactiondate'=>date('Y-m-d H:i:s',strtotime($responseArr->transactiondate)),
									'trans_for'=>'BGV',
									
					   );
					   // print_r($datatoinsertTrans);
					 // $update = $this->db->update('transaction_tb',$datatoupdate,array('orderid'=>$request["orderid"]));
					$insertTrans = $this->db->insert('transaction_tb',$datatoinsertTrans);
						if($insertTrans){
							
							//========================Mail Invoice====================================================
				$to_email = $request["email"];
				$from_email = "info@assetswatch.com";
				$InvoiceFilename = 'invoice.txt';
				$InvoiceTemplate = read_file('assets/email_template/'.$InvoiceFilename);
				$InvoiceSubject = "Invoice For Background Verification";
				
				$transactionamount = $responseArr->transactionamount;
				$TaxAmount = 0;
				$TotAmount = $transactionamount + $TaxAmount;
				$InvoiceTokensArr = array(
				'ADDRESS' => $request['address'],
				 'TITLE'=> 'Background Verification Fee',
				 'AMOUNT'=> $transactionamount,
				 'TAX_AMOUNT'=> $TaxAmount,
				 'TOTAL_AMOUNT'=> $TotAmount,
				 'INVOICE'=>$invoice_number,
				 'INVOICE_DATE'=>date('d M Y')
				);
				$InvoceMail = $this->send_mail($from_email,$to_email,$InvoiceSubject,$InvoiceTokensArr,$InvoiceTemplate);
				
							$SSN_EIN = $request['SSN_EIN'];
							$resultSSN_EIN = substr_replace($SSN_EIN, '-', 3, 0);
							$ssn	=substr_replace($resultSSN_EIN, '-', 6, 0);
				
				$retres=array();
				$Entolldatatosend =	array (
							  'webhookURL' => 'https://devstg.assetswatch.com',
							  'packageId' => $request['packageid'],
							  'locationId' => 'ASSEMON',
							  'refId' => '',
							  'billingRefId' => '',
							  'applicationRefId' => '',
							  'user' => 
							  array (
								'refId' => '',
								'firstName' => $request['first_name'],
								'lastName' => $request['last_name'],
								'email' => $request['email'],
							  ),
							  'applicant' => 
							  array (
								'refId' => '',
								'sendReportCopy' => 'true',
								'basicInformation' => 
								array (
								  'firstName' => $request['first_name'],
								  'middleName' => '',
								  'lastName' => $request['last_name'],
								  'ssn' => $ssn,
								  'phoneNumber' => $request['mobile_no'],
								  'email' => $request['email'],
								  'dob' => date('m/d/Y',strtotime($request['DOB'])),
								),
								'address' => 
								array (
								  'addressLine1' => $request['address'],
								  'city' => $request['city'],
								  'state' => $request['state'],
								  'zipCode' => $request['zip_code'],
								),
							  ),
							);
		
							$jsondataEnroll = json_encode($Entolldatatosend);
							 // print_r($jsondata);
							// exit();
							
							$header = array(
										//'Accept: application/json',
										'Content-Type: application/json',
										//'Authorization: Basic NjUyQTY3MzItOTE3MC00Q0Q0LUI5QzctREZBNzlFNTYxRTU5Og=='
										'Authorization: Basic '. base64_encode("652A6732-9170-4CD4-B9C7-DFA79E561E59:")
									);
								$curl = curl_init();

								curl_setopt_array($curl, array(
								  CURLOPT_URL => "https://api.screening.services/v1/orders",
								  CURLOPT_RETURNTRANSFER => true,
								  CURLOPT_ENCODING => "",
								  CURLOPT_MAXREDIRS => 10,
								  CURLOPT_TIMEOUT => 30,
								  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								  CURLOPT_CUSTOMREQUEST => "POST",
								 CURLOPT_POSTFIELDS => $jsondataEnroll,
								 CURLOPT_HTTPHEADER => $header
								));

								$responseEnroll = curl_exec($curl);
								$err = curl_error($curl);

								curl_close($curl);

								if ($err) {
								  echo "cURL Error #:" . $err;
								} else {
									// print_r($response);
									// echo "<br/>";
									// echo "<br/>";
									// echo "<br/>";
									$responseArrEnroll=json_decode($responseEnroll, true);
									 
									   // print_r($responseArrEnroll);
								} 
							if(count($responseArrEnroll)>0)
							{
								
								
										$reportId =$responseArrEnroll['reportId'];
										$mvpId = $responseArrEnroll['mvpId'];
										$status = $responseArrEnroll['status'];
										$orderDate =date('Y-m-d H:i:s');
										$login_user_id = $request['login_user_id'];
										$user_id = $request['user_id'];
										$packageId = $request['packageid'];
								if($reportId>0){
									
										$dataToInsert = array(
											'reportId'=>$reportId,
											'mvpId'=>$mvpId,
											'status'=>$status,
											'orderDate'=>$orderDate,
											'login_user'=>$login_user_id,
											'user_id'=>$user_id,
											'selected_package'=>$packageId,
											'created_date'=>date('Y-m-d H:i:s')
											// 'document'=>$responseReport
										);
										$this->db->insert('background_verification_tb',$dataToInsert);
										// print_r($responseReport);
										$retres['success']=1;
										$retres['msg']="Background Verification Completed.!!!";
										$retres['reportId']=$reportId;
									 
								}else{
									$retres['success']=0;
									$retres['msg']="Something went wrong.!!!";
								}
								
							}else{
									$retres['success']=0;
									$retres['msg']="Something went wrong.!!!";
									
							}
							
						}
					
					
				  }
				}
			
			 
				 $retres=json_encode($retres);
				 echo $retres;
		   
				
		
		
	}
	public function bgv_report($reportId)
	{

			
					
					$curl = curl_init();
					$header = array(
						'Content-Type: application/json',
						'Authorization: Basic '. base64_encode("652A6732-9170-4CD4-B9C7-DFA79E561E59:")
					);
					curl_setopt_array($curl, array(
					  CURLOPT_URL => "https://api.screening.services/v1/widgets/report-viewer/".$reportId,
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => "",
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 30,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => "GET",
					 CURLOPT_HTTPHEADER => $header
					));

					$responseReport = curl_exec($curl);
					$err = curl_error($curl);

					curl_close($curl);
					if ($err) {
					  echo "cURL Error #:" . $err;
					} else {
						// $html = $responseReport;
						$dompdf = new Dompdf\Dompdf();
				 
						// (Optional) Setup the paper size and orientation
						
						$dompdf->set_option('isHtml5ParserEnabled', true);
						$dompdf->loadHtml($responseReport);
						$dompdf->setPaper('A4', 'landscape');
						// Render the HTML as PDF
						$dompdf->render();
				 
						// Get the generated PDF file contents
						$pdf = $dompdf->output();
				 
						// Output the generated PDF to Browser
						$dompdf->stream();
					} 
					
					
					
 
						
	}
	public function bgv_information($login_userId,$report_userId){
		$result =$this->assetsapi_model->get_bgvinfo_by($login_userId,$report_userId);
		$retres = array();
				if($result)
				{
					$retres['success']=1;
					$retres['bgvInfo']= $result;
					
				}else{
						$retres['success']=0;
						$retres['msg']="Something went wrong.!!!";
						
				}
			
			$retres=json_encode($retres);
			echo $retres;
	}
	//=========================ratiing list with top agents==================
	public function top_rating_agents()
	{
		$result = $this->assetsapi_model->top_rating_agents();
				$retres = array();
				if($result)
				{
					$retres['success']=1;
					$retres['agent_list']= $result;
					
				}else{
						$retres['success']=0;
						$retres['msg']="Something went wrong.!!!";
						
				}
			
			$retres=json_encode($retres);
			echo $retres;
				
	}
	
	//========================send_emailto_non_register=======================================
	public function send_emailto_non_register($request=array())
	{
		$request = json_decode(file_get_contents('php://input'), true);  
		
			if(strpos($request['email'], ',') !== false) {
				$exploded = explode(',',$request['email']);
				$emailArr = $exploded;
			}else{
				$emailArr = $request['email'];
			}
			 
		  $from_email = "info@assetswatch.com";
		  $filename = 'non_register_user.txt';
		  $template = read_file('assets/email_template/'.$filename);
		  $subject = "Registration";
		  $to_email = $emailArr;
			
		$reacturl = $this->config->item('reacturl');
		 $url = "<br/><a href='".$reacturl."register' style='text-decoration: none;color: #FFFFFF;font-size: 17px;font-weight: 400;line-height: 120%;padding: 9px 26px;margin: 0;text-decoration: none;border-collapse: collapse;border-spacing: 0;border-radius: 4px;-webkit-border-radius: 4px;text-align: -webkit-center;vertical-align: middle;background-color: rgb(87, 187, 87);-moz-border-radius: 4px;-khtml-border-radius: 4px;'>Register</a>";
		
		$tokensArr = array(
				 'URL'=> trim($url)
				);
				$mail = $this->send_mail($from_email,$to_email,$subject,$tokensArr,$template);
				// ========================Mail======================================================================
				$retres = array();
			if($mail)
				{
					$retres['success']=1;
					$retres['msg']="Mail sended successfully.!!!";
					
				}else{
					// echo "not send ::".$this->email->print_debugger(FALSE);
					$retres['success']=0;
					$retres['msg']="Something went wrong.!!!";
					
				}
			$retres=json_encode($retres);
			echo $retres;
				
	}
	
	
	//=======================property payment=================================
	public function property_payment($request=array())
	{
		
		 $request = json_decode(file_get_contents('php://input'), true);  
		
			
			$partnerkey = $this->config->item('partnerkey');
			//$partnerid = $this->config->item('partnerid');
			$transactiontype = $this->config->item('transactiontype');
			
			$randNumber = mt_rand(100000, 999999);
			$orderid = 'TXN'.$randNumber;
			$this->session->set_userdata('orderid', $orderid);

			$userData = $this->assetsapi_model->profile($request["payeeid"]);
			
			$enrollInfo = $this->assetsapi_model->enroll_info($request["payeeid"]);
			$partnerid = $enrollInfo[0]['clientpartnerid'];
			//$planData = $this->assetsapi_model->planDeatilBy($plan_id);
			$first_name = $userData['first_name'];
			$last_name = $userData['last_name'];
			$address = $userData['city']." ".$userData['state']." ".$userData['country']." ".$userData['zip_code'];
			$city = $userData['city'];
			$state = $userData['state'];
			$country = $userData['country'];
			$zip = $userData['zip_code'];
			$email = $userData['email']; 
			
			$datatosend = array(
				"partnerkey"=>$partnerkey,
				"partnerid"=>$partnerid,
				"transactiontype"=>$transactiontype,
				"tokenizedaccountnumber"=>$request["tokenizedaccountnumber"],
				"expirymmyy"=>$request["expirymmyy"],
				"cvv"=>$request["cvv"],
				"paymentmode"=>$request["paymentmode"],
				"transactionamount"=>$request["transactionamount"],
				"routingnumber"=>$request["routingnumber"],
				"surchargeamount"=>$request["surchargeamount"],
				"currency"=>$request["currency"],
				"payeefirstname"=>$first_name,
				"payeelastname"=>$last_name,
				"address"=>$address,
				"city"=>$city,
				"state"=>$state,
				"country"=>$country,
				"zip"=>$zip,
				"email"=>$email,
				"transactionreference"=>$request["transactionreference"],
				 "orderid"=>$orderid,
				"payeeid"=>$request["payeeid"],
				"notifypayee"=>$request["notifypayee"],
				"profile"=>$request["profile"],
				"profileid"=>$request["profileid"]
			);
			//print_r($datatosend);
			//exit();
			 $jsondata = json_encode($datatosend);
				$curl = curl_init();

				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://api.singularbillpay.com/v1/transaction",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => "",
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 30,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => "POST",
				 CURLOPT_POSTFIELDS => $jsondata,
				 CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json"
				  ),
				));

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
				  echo "cURL Error #:" . $err;
				} else {
				  //echo $response;
				  // $txn_id = $this->session->userdata('txn_no');
				  $responseArr = json_decode($response);
				  if($orderid == $responseArr->orderid)
				  {
					  $this->db->order_by('transactiondate',"desc");
						$this->db->limit(1);
						$query = $this->db->get('property_transaction_tb');
						 $lastRecord = $query->result_array();
					  if($lastRecord[0]['invoice_number']!='' || $lastRecord[0]['invoice_number']!=null ){
						  $inv = $lastRecord[0]['invoice_number'];
						  
						    $inv_number = ++$inv;
					  }else{
						  $inv_number = 'AWLLC000001';
					  }
					  
					   $invoice_number = $inv_number;
					   $datatoinsertTrans = array( 
									'invoice_number' => $invoice_number,
									'payee_id'=>$request["payee_id"],
									'orderid'=> $orderid,
									'transactionid'=>$responseArr->transactionid,
									'paymentmode'=>$responseArr->paymentmode,
									'transaction_type'=>$responseArr->transactiontype,
									'transactionamount'=>$responseArr->transactionamount,
									'transactionreference'=>$responseArr->transactionreference,
									'responsecode'=>$responseArr->responsecode,
									'responsestatus'=>$responseArr->responsestatus,
									'responsemessage'=>$responseArr->responsemessage,
									'transactiondate'=>date('Y-m-d H:i:s',strtotime($responseArr->transactiondate)),
									'paid_for'=>$request["paid_for"],
									'paid_amt_owner'=>$paid_amt_owner,
									'paid_amt_agent'=>$paid_amt_agent
									
					   );
					   // print_r($datatoinsertTrans);
					 // $update = $this->db->update('transaction_tb',$datatoupdate,array('orderid'=>$request["orderid"]));
					$insertTrans = $this->db->insert('property_transaction_tb',$datatoinsertTrans);
						
				}
			$retres = array();
				 if($orderid == $responseArr->orderid)
				 {
						 $retres['success']=1;
						 $retres['msg']='Payment Completed.!!!';
						 //========================Mail Invoice====================================================
					$to_email = $email;
					$from_email = "info@assetswatch.com";
					$InvoiceFilename = 'invoice.txt';
					$InvoiceTemplate = read_file('assets/email_template/'.$InvoiceFilename);
					$InvoiceSubject = "Invoice For Property";
					
					$transactionamount = $responseArr->transactionamount;
					$TaxAmount = 0;
					$TotAmount = $transactionamount + $TaxAmount;
					$InvoiceTokensArr = array(
					'ADDRESS' => $address,
					 'TITLE'=> 'Property',
					 'AMOUNT'=> $transactionamount,
					 'TAX_AMOUNT'=> $TaxAmount,
					 'TOTAL_AMOUNT'=> $TotAmount,
					 'INVOICE'=>$invoice_number,
					 'INVOICE_DATE'=>date('d M Y')
					);
					$InvoceMail = $this->send_mail($from_email,$to_email,$InvoiceSubject,$InvoiceTokensArr,$InvoiceTemplate);
						 //$retres['payment']=$responseArr;
									
				 }else{
						 $retres['success']=0;
						 $retres['msg']='Somthing went wrong please try later.!!!';
						
					 } 
				 
		   
		}
		$retres=json_encode($retres);
				 echo $retres;
	}
	
	public function basic_plan_update($userid,$planid)
	{
		$update = $this->db->update('registration_tb',array('plan_id'=>$planid),array('assets_id'=>$userid));
		$retres = array();
		if($update){
				$retres['success']=1;
				$retres['msg']='Successfully Register.!!!';
		}else{
				$retres['success']=0;
				$retres['msg']='Somthing went wrong please try later.!!!';
						
			} 
		$retres=json_encode($retres);
		 echo $retres;
	}
	
	
	
}