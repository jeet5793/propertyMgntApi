<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plan extends CI_Controller {

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
        $this->load->model('plan_model');
    }
	public function index()
	{
			
			$data['data'] = $this->plan_model->getData();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('plan_list',$data);
			$this->load->view('common/footer');
			
			
	}
	
	public function add()
	{
		
		if(!empty($_POST))
		{
			
			$this->load->library('form_validation');

                $this->form_validation->set_rules('plan', 'Plan Name', 'required',//|is_unique[user.username]
				array(
					'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				));
				//$this->form_validation->set_rules('permonth', 'Per Month', 'required');
               // $this->form_validation->set_rules('perannum', 'Per Annum', 'required');
                $this->form_validation->set_rules('user_type', 'User Type', 'required',array(
					'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				));
                

                if ($this->form_validation->run() == True)
                {
						
						$plan = $this->input->post('plan');
						$per_month = $this->input->post('permonth');
						$per_annum = $this->input->post('perannum');
						$user_type = $this->input->post('user_type');
						$status = $this->input->post('status');
						$dataToInsert = array('plan'=>$plan,'per_month'=>$per_month,'per_annum'=>$per_annum,'user_type'=>$user_type,'status'=>$status);
						
						$InsertData = $this->plan_model->insertData($dataToInsert);
						redirect('/plan');
						
                }else{
					
							$this->load->view('common/home_header');
							$this->load->view('common/top_nav');
							$this->load->view('common/leftsidebar');
							$planusers['planusers'] = $this->plan_model->getPlanUser();
							$this->load->view('plan_add',$planusers);
							$this->load->view('common/footer');
						}
		}else{
			
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$planusers['planusers'] = $this->plan_model->getPlanUser();
			$this->load->view('plan_add',$planusers);
			$this->load->view('common/footer');
			
		}
			
	}
	
	public function edit()
	{
		$id = $this->uri->segment(3);
		if(!empty($_POST))
		{
			//$this->load->library('form_validation');

                //$this->form_validation->set_rules('plan', 'Plan Name', 'required',//|is_unique[user.username]
				//array(
					//'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				//));
				//$this->form_validation->set_rules('permonth', 'Per Month', 'required');
               // $this->form_validation->set_rules('perannum', 'Per Annum', 'required');
                //$this->form_validation->set_rules('user_type', 'User Type', 'required',array(
					//'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				//));
                

               // if ($this->form_validation->run() == True)
                //{
						
						$plan = $this->input->post('plan');
						$per_month = $this->input->post('permonth');
						$per_annum = $this->input->post('perannum');
						$user_type = $this->input->post('usertypeid');
						$status = $this->input->post('status');
						
						$dataToUpdate = array('plan'=>$plan,'per_month'=>$per_month,'per_annum'=>$per_annum,'user_type'=>$user_type,'status'=>$status);
					//print_r($dataToUpdate);
					//exit();
						$updateData = $this->plan_model->updateData($dataToUpdate,$id);
						redirect('/plan');
						
						
               // }else{
					
							
		}else{
			
			
			$data['data'] = $this->plan_model->getDataBy($id);
			$data['planusers'] = $this->plan_model->getPlanUser();
			$this->load->view('plan_edit',$data);
			$this->load->view('common/footer');
		}
			
	}
	
	public function tdelete()
	{
		$id = $this->uri->segment(3);
		$deleteData = $this->plan_model->deleteData($id);
		redirect('/plan');	
	}
	
	/*public function view()
	{
		$id = $this->uri->segment(3);
		$this->load->model('testimonial_model');
		$deleteData = $this->testimonial_model->getDataBy($id);
		redirect('testimonial', 'refresh');	
	}
	*/
	
	public function feature_list()
	{
		
			$data['data'] = $this->plan_model->getfeatureData();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('features_list',$data);
			$this->load->view('common/footer');
	}
	public function feature_edit()
	{
		$id = $this->uri->segment(3);
		
		if(!empty($_POST))
		{
			$feature_name = $this->input->post('feature_name');
			$feature_status = $this->input->post('feature_status');
			$feature_unit = $this->input->post('feature_unit');
			$status = $this->input->post('status');
			$dataToUpdate = array('feature_name'=>$feature_name,'feature_status'=>$feature_status,'feature_unit'=>$feature_unit,'status'=>$status);
			$updateData = $this->plan_model->updatefeatureData($dataToUpdate,$id);
			redirect('/features');
		}else{
				$this->load->view('common/home_header');
				$this->load->view('common/top_nav');
				$this->load->view('common/leftsidebar');
				$data['data'] = $this->plan_model->getfeatureDataby($id);
				$this->load->view('features_edit',$data);
				$this->load->view('common/footer');
		}
		
		
	}
	
	public function feature_delete()
	{
		$id = $this->uri->segment(3);
		$deleteData = $this->plan_model->deleteData($id);
		redirect('/plan');	
	}
	
	public function featuremapper()
	{

			$data['data'] = $this->plan_model->getmapperData();
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('featuremapper_list',$data);
			$this->load->view('common/footer');
	}
	public function featuremapper_add()
	{
		//$data['plan'] = $this->plan_model->getData();
		$data['usertype'] = $this->plan_model->getPlanUser();
		$data['feature'] = $this->plan_model->getfeatureData();
		if(!empty($_POST))
		{
			
			$this->load->library('form_validation');

				$this->form_validation->set_rules('user_type', 'User Type', 'required',//|is_unique[user.username]
				array(
					'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				));
                $this->form_validation->set_rules('plan', 'Plan Name', 'required',//|is_unique[user.username]
				array(
					'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				));
				//$this->form_validation->set_rules('permonth', 'Per Month', 'required');
               // $this->form_validation->set_rules('perannum', 'Per Annum', 'required');
                $this->form_validation->set_rules('feature', 'Feature', 'required',array(
					'required'      => 'You have not provided %s.',
					//'is_unique'     => 'This %s already exists.'
				));
                //$this->form_validation->set_rules('limitupto', 'Limit Upto', 'required');

                if ($this->form_validation->run() == True)
                {
						
						$plan = $this->input->post('plan');
						$feature_id = $this->input->post('feature');
						
						$confirmation = $this->input->post('feature_unit');
						$status = $this->input->post('status');
						
						
				
				
					if(isset($_POST['limitupto']))
					{
						$confirmation = '';
						$limitupto = $this->input->post('limitupto');
						
						
					}else 
					{
						$confirmation = $this->input->post('feature_unit');
						$limitupto = 0;
						
					}
						$dataToInsert = array('plan_id'=>$plan,'feature_id'=>$feature_id,'limit_upto'=>$limitupto,'status'=>$status,'confirmation'=>$confirmation);
						
						$InsertData = $this->plan_model->insertmapperData($dataToInsert);
						redirect('/featuremapper');
						
                }else{
					
							$this->load->view('common/home_header');
							$this->load->view('common/top_nav');
							$this->load->view('common/leftsidebar');
							$this->load->view('featuremapper_add',$data);
							$this->load->view('common/footer');
						}
		}else{
			
			$this->load->view('common/home_header');
			$this->load->view('common/top_nav');
			$this->load->view('common/leftsidebar');
			$this->load->view('featuremapper_add',$data);
			$this->load->view('common/footer');
			
		}
	}
	public function featuremapper_edit()
	{
		$id = $this->uri->segment(3);
		$data['usertype'] = $this->plan_model->getPlanUser();
		$data['feature'] = $this->plan_model->getfeatureData();
		if(!empty($_POST))
		{
			$plan = $this->input->post('plan');
			$feature_id = $this->input->post('feature');
			$limitupto = $this->input->post('limitupto');
			
			
			$query = $this->db->get_where('feature_tb',array('id'=>$feature_id));
			$featureData = $query->result_array();
			$status = $this->input->post('status');
			$unit = $featureData[0]['feature_unit'];
			
			
				if($unit=='Restrict')
				{
					$confirmation = $this->input->post('feature_unit');
					$limitupto = 0;
					
				}else if(unit=='Limit')
				{
					$confirmation = '';
					$limitupto = $this->input->post('limitupto');
					
				}
		 
			$dataToUpdate = array('plan_id'=>$plan,'feature_id'=>$feature_id,'limit_upto'=>$limitupto,'status'=>$status,'confirmation'=>$confirmation);
						
			$updateData = $this->plan_model->updateFeaturemapper($dataToUpdate,$id);
			redirect('/featuremapper');
		}else{
				$this->load->view('common/home_header');
				$this->load->view('common/top_nav');
				$this->load->view('common/leftsidebar');
				$data['data'] = $this->plan_model->getFeaturemapperby($id);
				$this->load->view('featuremapper_edit',$data);
				$this->load->view('common/footer');
		}
		
		
	}
	public function featuremapper_delete()
	{
		$id = $this->uri->segment(3);
		$deleteData = $this->plan_model->deleteFeaturemapper($id);
		redirect('/featuremapper');	
	}
	function getPlanlist()
	{
		
		 $usertype = $_POST['usertype'];
		 $query = $this->db->get_where('plan_tb',array('user_type'=>$usertype));
		 $planData = $query->result_array();
		 //print_r($planData);
		  $result='<option>Select a Plan</option>';
		 foreach($planData as $plan)
		 {
			   $result.='<option value="'.$plan['id'].'">'.$plan['plan'].'</option>';

		 }
		 echo $result;

		 
	}
	function getFeatureunit()
	{
		
		 $feature = $_POST['feature'];
		 $query = $this->db->get_where('feature_tb',array('id'=>$feature));
		 $featureData = $query->result_array();

		  
		 echo $featureData[0]['feature_unit'];

		 
	}
	
}
