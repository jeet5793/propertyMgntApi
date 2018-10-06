<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registration_model extends CI_Model {

	// Owner Database 

	// Get Owner Datas 
	public function getData(){
		//echo "sdsd";
		$this->db->select("*");
	    $this->db->from('registration_tb');
	    $query = $this->db->get();
	    return $query->result();
	    
	}
	//Elakkiya starts 29_08_2018
	public function getLastAgentData(){
		$this->db->select("*");
		$this->db->where('assets_type',2);
	    $this->db->from('registration_tb');
	    $this->db->order_by('assets_id',"DESC");
	    $this->db->limit(10);
	    $query = $this->db->get();
		return $query->result();
	}	
	public function getLastOwnerData(){
		$this->db->select("*");
		$this->db->where('assets_type',1);
	    $this->db->from('registration_tb');
	    $this->db->order_by('assets_id',"DESC");
	    $this->db->limit(10);
	    $query = $this->db->get();
		return $query->result();
	}	
	public function getLastTenantData(){
		$this->db->select("*");
		$this->db->where('assets_type',3);
	    $this->db->from('registration_tb');
	    $this->db->order_by('assets_id',"DESC");
	    $this->db->limit(10);
	    $query = $this->db->get();
		return $query->result();
	}
	//Elakkiya ends 29_08_2018
	public function getLastData(){
		//echo "sdsd";
		$this->db->select("*");
	    $this->db->from('registration_tb');
	    //$this->db->limit(6);
	    $this->db->order_by('assets_id',"DESC");
	    $query = $this->db->get();
	    return $query->result();
	    
	}

	public function countOwner(){

		
    $this->db->select('*');
    $this->db->where('assets_type',1);
    //$this->db->or_where('owner_type',2);
    $this->db->from('registration_tb');
    $owner = $this->db->get();
    return $owner->num_rows();

	}
	public function countAgent(){

	$this->db->select('*');
    //$this->db->where('agent_type',1);
    $this->db->or_where('assets_type',2);
    $this->db->from('registration_tb');
    $agent = $this->db->get();
    return $agent->num_rows();
//     $this->db->select('*')->from('registration_tb')->where(array('agent_type'=>1));
// $q = $this->db->get();
// return $q->num_rows();

	}
	public function countTenant(){

	$this->db->select('*');
    $this->db->where('assets_type',3);
    // $this->db->or_where('assets_type',2);
    // $this->db->or_where('assets_type',3);
    // $this->db->or_where('assets_type',4);
    // $this->db->or_where('assets_type',5);
    $this->db->from('registration_tb');
    $tenant = $this->db->get();
    return $tenant->num_rows();	
    }

    public function countProperty(){
      $this->db->select("id");
      $this->db->from('property_tb');
      $property = $this->db->get();
      
      return $property->num_rows();


//$this->db->count_all_results('registration_tb');
    }
	
	// Delete Owner Datas
	public function deleteOwnerData($id){
		$this->db->delete('registration_tb', array('assets_id' => $id)); 
	 }
	 public function getOwnerData(){
	 	$id = $this->input->post('id');
		$this->db->select("*");
		$this->db->where('assets_id',$id);
	    $this->db->from('registration_tb');
	    $query = $this->db->get();
		// echo $this->db->last_query();
	    return $query->result(); 
	 }

	 // Update Owner Profile 
	 public function updateOwnerProfile(){
	 	$name = $this->input->post('name');
	 	$explode_name = explode(" ",$name);
	 	$fname = $explode_name[0];
	 	$lname = $explode_name[1];
	 	$email = $this->input->post('email');
	 	$phone = $this->input->post('phone');
	 	$country = $this->input->post('country');
	 	$id = $this->input->post('hidden_value');
	 	
	 	$value=array('first_name'=>$fname,'last_name'=>$lname,'email'=>$email,'mobile_no'=>$phone,'country'=>$country);
 		$this->db->where('assets_id',$id);
        if( $this->db->update('registration_tb',$value))
      {
        return true;
      }
      else
      {
        return false;
      }
      
	 }

 	 // Update Owner Status
	 public function updateOwnerStatus(){
	 	$id = $this->input->post('id');
		$status = $this->input->post('status');
	 	$value=array('status'=>$status);
 		$this->db->where('assets_id',$id);
        if( $this->db->update('registration_tb',$value))
      {
        return true;
      }
      else
      {
        return false;
      }
      
	 }


	 // Agent Database 

	// Get Agent Datas 
	public function AllAgentData(){
		//echo "sdsd";
		$this->db->select("*");
	    $this->db->from('registration_tb');
	    $query = $this->db->get();
	    return $query->result();
	    
	}

	// Tenent Database

	// Get Tenent Datas
	 public function AllTenentData(){
		//echo "sdsd";
		$this->db->select("*");
	    $this->db->from('registration_tb');
	    $query = $this->db->get();
	    return $query->result();
	    
	}
	
	// // Delete Agent Datas
	// public function deleteAgentData($id){
	// 	$this->db->delete('registration_tb', array('assets_id' => $id)); 
	//  }
	//  public function getAgentData(){
	//  	$id = $this->input->post('id');
	// 	$this->db->select("*");
	// 	$this->db->where('assets_id',$id);
	//     $this->db->from('registration_tb');
	//     $query = $this->db->get();
	//     return $query->result(); 
	//  }

	//  // Update Agent Profile 
	//  public function updateAgentProfile(){
	//  	$name = $this->input->post('name');
	//  	$explode_name = explode(" ",$name);
	//  	$fname = $explode_name[0];
	//  	$lname = $explode_name[1];
	//  	$email = $this->input->post('email');
	//  	$phone = $this->input->post('phone');
	//  	$country = $this->input->post('country');
	//  	$id = $this->input->post('hidden_value');
	 	
	//  	$value=array('first_name'=>$fname,'last_name'=>$lname,'email'=>$email,'mobile_no'=>$phone,'country'=>$country);
 // 		$this->db->where('assets_id',$id);
 //        if( $this->db->update('registration_tb',$value))
 //      {
 //        return true;
 //      }
 //      else
 //      {
 //        return false;
 //      }
      
	//  }

 // 	 // Update Agent Status
	//  public function updateAgentStatus(){
	//  	$id = $this->input->post('id');
	// 	$status = $this->input->post('status');
	//  	$value=array('status'=>$status);
 // 		$this->db->where('assets_id',$id);
 //        if( $this->db->update('registration_tb',$value))
 //      {
 //        return true;
 //      }
 //      else
 //      {
 //        return false;
 //      }
      
	//  }
	
		public function getOwner(){
			$this->db->select('*');
			$this->db->where('assets_type',1);
			//$this->db->or_where('owner_type',2);
			$this->db->from('registration_tb');
			$query = $this->db->get();
			return $query->result();

		}
		public function getAgent(){
			$this->db->select('*');
			$this->db->where('assets_type',2);
			//$this->db->or_where('owner_type',2);
			$this->db->from('registration_tb');
			$query = $this->db->get();
			return $query->result();
		}
		public function getTenant(){
			$this->db->select('*');
			$this->db->where('assets_type',3);
			//$this->db->or_where('owner_type',2);
			$this->db->from('registration_tb');
			$query = $this->db->get();
			return $query->result();

		}
		public function agent_review(){
			$query = $this->db->query("SELECT A.agent_id,B.first_name,B.last_name,B.city, SUM(A.rating)/COUNT(*) as rating FROM owner_agent_rating_tb as A LEFT JOIN registration_tb as B ON A.agent_id = B.assets_id GROUP BY agent_id ORDER BY rating DESC");
			return $query->result();
		}
}
