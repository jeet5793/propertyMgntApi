<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plan_model extends CI_Model {

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
	public function insertData($dataToInsert)
	{
		$this->db->insert('plan_tb',$dataToInsert);
	}
	public function insertfeature($featuredataToInsert)
	{
		$this->db->insert('plan_feature_tb',$featuredataToInsert);
	}
	
	public function getData(){
	  $this->db->select("p.id,plan,per_month,per_annum,u.user_type as usertype,p.user_type usertypeid,status");
	  $this->db->from('plan_tb p');
	  $this->db->join("plan_user_type_tb u","p.user_type=u.id");
	  $this->db->where('status', '1');
	  $query = $this->db->get();
	  return $query->result_array();
	 }
	 
	 public function updateData($dataToUpdate,$id){
		$this->db->where('id', $id);
		$this->db->update('plan_tb', $dataToUpdate);
	 }
	 public function deleteData($id){
		$this->db->delete('plan_tb', array('id' => $id)); 
	 }
	 
	 public function getDataBy($id){
		$this->db->select("p.id,plan,per_month,per_annum,u.user_type as usertype,p.user_type usertypeid,status");
	  $this->db->from('plan_tb p');
	  $this->db->join("plan_user_type_tb u","p.user_type=u.id");
	  $this->db->where('p.id', $id);
	  $query = $this->db->get();
	  return $query->result_array();
	 }
	 
	 public function getPlanUser(){
	  $query = $this->db->get('plan_user_type_tb');
	  return $query->result_array();
	 }
	 
	 public function getfeatureData(){
	  $query = $this->db->get('feature_tb');
	  return $query->result_array();
	 }
	 
	 public function getfeatureDataby($id){
	  $query = $this->db->get('feature_tb');
	  return $query->result_array();
	 }
	  public function updatefeatureData($dataToUpdate,$id){
		$this->db->where('id', $id);
		$this->db->update('feature_tb', $dataToUpdate);
	 }
	 
	 public function insertmapperData($dataToInsert)
	{
		$this->db->insert('plan_feature_relation_tb',$dataToInsert);
	}
	 public function updateFeaturemapper($dataToUpdate,$id){
		$this->db->where('id', $id);
		$this->db->update('plan_feature_relation_tb', $dataToUpdate);
	 }
	public function getmapperData(){
	  $this->db->select("pf.id,p.plan,u.user_type,f.feature_name,pf.status");
		$this->db->from('plan_feature_relation_tb pf');
		$this->db->join("plan_tb p","p.id=pf.plan_id",'left');
		$this->db->join("plan_user_type_tb u","p.user_type=u.id",'left');
		$this->db->join("feature_tb f","f.id=pf.feature_id",'left');
	 // $this->db->where('status', '1');
	  $query = $this->db->get();
	  return $query->result_array();
	 }
	 public function getFeaturemapperby($id){
		 $this->db->select("pf.*,p.user_type,p.plan");
		 $this->db->where('pf.id', $id);
		 $this->db->join("plan_tb p","p.id=pf.plan_id",'left');
	  $query = $this->db->get('plan_feature_relation_tb pf');
	  return $query->result_array();
	 }
	 public function deleteFeaturemapper($id){
		$this->db->delete('plan_feature_relation_tb', array('id' => $id)); 
	 }
}
