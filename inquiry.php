<?php
 class inquiry extends Admin_Controller{
	function __construct(){
		parent::__construct();
		$this->data['title'] = ":: Auto desk ::";
		$this->load->model("inquiry_m");
	}
	
	function index(){
		$this->getdata();
	}
	
	public function getdata(){
		/*$this->load->helper("dtable");
		$this->datatables->select('id,name,address,email,city,state');
		$this->datatables->from('inquiry');
		$this->datatables->unset_column('id');
		$this->datatables->add_column('edit','$1', 'get_buttons(id,inquiry/add/,inquiry/delete/,null,null)');
		echo ($this->datatables->generate());
		*/
		$this->data['heading'] = "Inquiry details";
		$branch_id = $this->session->userdata("branch");
		$where = array("followup"=>'1',"branch_id"=>$branch_id);
		$where1 = array("followup"=>'2',"branch_id"=>$branch_id);
		$where2 = array("followup"=>'3',"branch_id"=>$branch_id);
		
		$this->data['flw1'] = $this->inquiry_m->getBy($where);
		$this->data['flw2'] = $this->inquiry_m->getBy($where1);
		$this->data['flw3'] = $this->inquiry_m->getBy($where2);
		$this->data['course'] = $this->db->get('course')->result();
		$this->data['qualification'] = $this->db->get('qualification')->result();
		$this->data['reference'] = $this->db->get('refer')->result();
 		
		$this->data['mainContent'] = "inquiry/view";
		$this->load->view("template/template",$this->data);
	}

	function add($id = NULL){
		$msg = ($id == NULL)?"Add":"Edit";
		$this->data['heading'] = $msg . " inquiry";
		$this->data['branch'] = $this->db->get('branch')->result();
		$this->data['course'] = $this->db->get('course')->result();
		$this->data['qualification'] = $this->db->get('qualification')->result();
		$this->data['reference'] = $this->db->get('refer')->result();
 		if(intval($id)){
			$id = intval($id);
			$this->data['inquiry'] = $this->inquiry_m->get($id);
			if(empty($this->data['inquiry'])){
				$this->data['inquiry'] = $this->inquiry_m->get_new();
				$this->data['error'] = 'inquiry could not found';
			}
		}else{
			$this->data['inquiry'] = $this->inquiry_m->get_new();
		}	
		$rule = $this->inquiry_m->_rules;
		$this->form_validation->set_rules($rule);
		if($this->form_validation->run() == TRUE){
			$data = array("branch_id"=>$this->input->post("branch_id"),"name"=>$this->input->post("name"),"address"=>$this->input->post("address"),"email"=>$this->input->post("email"),"city"=>$this->input->post("city"),"state"=>$this->input->post('state'),"followup"=>$this->input->post("followup"));
			$data['qualification_id'] = implode(",",$this->input->post('qualification_id'));
			$data['course_id'] = implode(",",$this->input->post('course_id'));
			$data['reference_id'] = implode(",",$this->input->post('reference_id'));
			if(empty($_POST['id'])){
				$this->db->insert('inquiry',$data);
			}else{
				$this->db->where('id',$_POST['id'])->update('inquiry',$data);
			}
			redirect(base_url() . 'inquiry');
		}
		$this->data['mainContent'] = "inquiry/add";
		$this->load->view("template/template",$this->data);
	}

	public function status(){
      $id = $this->uri->segment(3);
      $status = $this->uri->segment(4);
      $data = array("status"=>$status);
      $this->inquiry_m->save($data,$id);
      redirect(base_url() . 'inquiry');
  	}

  	function nextflow(){
  		$id = $this->uri->segment(3);
  		$val =$this->uri->segment(4);	
  		$data['followup'] = $val+1;
  		$this->inquiry_m->save($data,$id);
  		redirect(base_url().'inquiry/getdata');
  	}

	function delete(){
		$id = $this->uri->segment(3);
		$this->inquiry_m->delete($id);
		redirect(base_url() . "inquiry");		
	}
}
?>
