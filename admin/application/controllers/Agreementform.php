<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agreementform extends CI_Controller {

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
        $this->load->model('Agreementform_model');
    }
	public function index()
	{
			
			/*$data['data'] = $this->plan_model->getData();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('plan_list',$data);
			$this->load->view('common/footer');*/
			
			
	}
	
	
	
	public function propertyform()
	{
		
			$data['data'] = $this->Agreementform_model->getpropData();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('property_form_list',$data);
			$this->load->view('common/footer');
	}
	
	public function propertyform_add()
	{
		if(!empty($_POST))
		{
			$form_name = $this->input->post('form_name');
			$description = $this->input->post('description');
			$status = $this->input->post('status');
			$dataToinsert = array('form_name'=>$form_name,'description'=>$description,'status'=>$status);
			$InsertData = $this->Agreementform_model->insertpropData($dataToinsert);

				redirect('/propertyform');
			
			
		}else{
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('property_form_add');
			$this->load->view('common/footer');
		}
	}
	public function propertyform_edit()
	{
		$id = $this->uri->segment(3);
		
		if(!empty($_POST))
		{
			$form_name = $this->input->post('form_name');
			$description = $this->input->post('description');
			$status = $this->input->post('status');
			$dataToUpdate = array('form_name'=>$form_name,'description'=>$description,'status'=>$status);
			$updateData = $this->Agreementform_model->updatepropData($dataToUpdate,$id);
			
				redirect('/propertyform');
			
		}else{
				$this->load->view('common/home_header');
				$this->load->view('common/top_nav');
				$this->load->view('common/leftsidebar');
				$data['data'] = $this->Agreementform_model->getPropDataBy($id);
				$this->load->view('property_form_edit',$data);
				$this->load->view('common/footer');
		}
		
		
	}
	
	/*public function feature_delete()
	{
		$id = $this->uri->segment(3);
		$deleteData = $this->plan_model->deleteData($id);
		redirect('/plan');	
	}
	*/
	public function agreementform()
	{
		
			$data['data'] = $this->Agreementform_model->getSignatureData();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('signature_form_list',$data);
			$this->load->view('common/footer');
	}
	
	public function signatureform_add()
	{
		if(!empty($_POST))
		{
			$form_name = $this->input->post('form_name');
			$description = $this->input->post('description');
			$status = $this->input->post('status');
			$header_content = $this->input->post('header_content');
			$header_image = $this->input->post('header_image');
			$watermark_image = $this->input->post('watermark_image');
			$footer_content = $this->input->post('footer_content');

			if(isset($_POST['paytype']) && $_POST['paytype']=='Paid')
			{
				$paytype = $this->input->post('paytype');
				$amount = $this->input->post('amount');
				$currency = $this->input->post('currency');
			}elseif(isset($_POST['paytype']) && $_POST['paytype']=='Free'){
				$paytype = $this->input->post('paytype');
				$amount = '';
				$currency = '';
			}
			
				/* $header_image1 = str_replace('data:image/jpeg;base64,', '', $header_image);
				$header_img = str_replace('data:image/png;base64,', '', $header_image1);
				$Headerimage = base64_decode($header_img);
				$Header_image_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
				$filename = $Header_image_name . '.' . 'png';

				$Headerimagepath = 'assets/agreement/images/';
				if (!file_exists($Headerimagepath)) {
					$HeaderimagefolderPath = mkdir($Headerimagepath, 0777, true);
				}
				else{
						$HeaderimagefolderPath = $Headerimagepath;
					}
		
			$targetPath = $Headerimagepath.$filename;
			file_put_contents($targetPath, $Headerimage);
			
			//==========================watermark image==========================
				$watermark_image1 = str_replace('data:image/jpeg;base64,', '', $watermark_image);
				$watermark_img = str_replace('data:image/png;base64,', '', $watermark_image1);
				$wpImage = base64_decode($watermark_img);
				$WPimage_name = md5(uniqid(rand(), true));// image name generating with random number with 32 characters
				$WPfilename = $WPimage_name . '.' . 'png';

				$WMpath = 'assets/agreement/images/';
				if (!file_exists($WMpath)) {
					$WpfolderPath = mkdir($WMpath, 0777, true);
				}
				else{
						$WpfolderPath = $WMpath;
					}
		
			$WMtargetPath = $WMpath.$WPfilename;
			file_put_contents($WMtargetPath, $wpImage); */
			if(!empty($_FILES)) {
								if(is_uploaded_file($_FILES['header_image']['tmp_name'])) {
									$sourcePath = $_FILES['header_image']['tmp_name'];
									$targetPath = 'assets/agreement/images/'.$_FILES['header_image']['name'];
									move_uploaded_file($sourcePath,$targetPath);
								}
								if(is_uploaded_file($_FILES['watermark_image']['tmp_name'])) {
									$WMsourcePath = $_FILES['watermark_image']['tmp_name'];
									$WMtargetPath = 'assets/agreement/images/'.$_FILES['watermark_image']['name'];
									move_uploaded_file($WMsourcePath,$WMtargetPath);
								}
							} 
			$dataToinsert = array(
							'form_name'=>$form_name,
							'description'=>$description,
							'status'=>$status,
							'header_content'=>$header_content,
							'footer_content'=>$footer_content,
							'header_image'=>$targetPath,
							'watermark_image'=>$WMtargetPath,
							'paytype'=>$paytype,
							'amount'=>$amount,
							'currency'=>$currency
							);
							
			$InsertData = $this->Agreementform_model->insertSignatureData($dataToinsert);

				redirect('/agreementform');
			
			
		}else{
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('signature_form_add');
			$this->load->view('common/footer');
		}
	}
	public function signatureform_edit()
	{
		$id = $this->uri->segment(3);
		
		if(!empty($_POST))
		{
			$form_name = $this->input->post('form_name');
			$description = $this->input->post('description');
			$status = $this->input->post('status');
			$header_content = $this->input->post('header_content');
			// $header_image = $this->input->post('header_image');
			// $watermark_image = $this->input->post('watermark_image');
			$header_image_old = $this->input->post('header_image_old');
			$wm_image_old = $this->input->post('wm_image_old');
			$footer_content = $this->input->post('footer_content');
			
			if(isset($_POST['paytype']) && $_POST['paytype']=='Paid')
			{
				$paytype = $this->input->post('paytype');
				$amount = $this->input->post('amount');
				$currency = $this->input->post('currency');
			}elseif(isset($_POST['paytype']) && $_POST['paytype']=='Free'){
				$paytype = $this->input->post('paytype');
				$amount = '';
				$currency = '';
			}

			
			
			 if(!empty($_FILES)) {
								if(is_uploaded_file($_FILES['header_image']['tmp_name'])) {
									$sourcePath = $_FILES['header_image']['tmp_name'];
									$targetPath = 'assets/agreement/images/'.$_FILES['header_image']['name'];
									move_uploaded_file($sourcePath,$targetPath);
								}else{
										$targetPath = $header_image_old;
								}
								if(is_uploaded_file($_FILES['watermark_image']['tmp_name'])) {
									$WMsourcePath = $_FILES['watermark_image']['tmp_name'];
									$WMtargetPath = 'assets/agreement/images/'.$_FILES['watermark_image']['name'];
									move_uploaded_file($WMsourcePath,$WMtargetPath);
								}else{
									$targetPath = $wm_image_old;
								}
							} 
			
			$dataToUpdate = array(
							'form_name'=>$form_name,
							'description'=>$description,
							'status'=>$status,
							'header_content'=>$header_content,
							'footer_content'=>$footer_content,
							'header_image'=>$targetPath,
							'watermark_image'=>$WMtargetPath,
							'paytype'=>$paytype,
							'amount'=>$amount,
							'currency'=>$currency
							);
			$updateData = $this->Agreementform_model->updateSignatureData($dataToUpdate,$id);
			
				redirect('/agreementform'); 
			
		}else{
				$this->load->view('common/home_header');
				$this->load->view('common/top_nav');
				$this->load->view('common/leftsidebar');
				$data['data'] = $this->Agreementform_model->getSignatureDataBy($id);
				$this->load->view('signature_form_edit',$data);
				$this->load->view('common/footer');
		}
		
		
	}
	
}
