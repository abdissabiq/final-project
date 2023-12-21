<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Adminpanel extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Madmin');
	}
	
	public function index(){
		$this->load->view('admin/login');
	}

	public function dashboard(){
		if(empty($this->session->userdata('userName'))){
			redirect('adminpanel');
		}
		$this->load->view('admin/layout/header');
		$this->load->view('admin/layout/menu');
		$this->load->view('admin/dashboard');
		$this->load->view('admin/layout/footer');
	}

	public function adminval()
	{
		$this->form_validation->set_rules('username', 'userName', 'trim|required');
		$this->form_validation->set_rules('password', 'password', 'trim|required');
		if ($this->form_validation->run() == false) {
			redirect('adminpanel');
		} else {
			$this->login();
		}
	}

	public function login(){
		$this->load->model('Madmin');
		$u= $this->input->post('username');
		$p= md5($this->input->post('password'));
		
		$cek = $this->Madmin->cek_login($u, $p)->row_array();
		if($cek){
			if ($cek['password'] == $p) {
				$data_session = array(
					'userName' => $u,
					'status' => 'login'
				);
				$this->session->set_userdata($data_session);
				redirect('adminpanel/dashboard');
			} else{	
				$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
		<strong>Password Salah!</strong> Pastikan password yang anda masukan benar!!.
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>');
	  $this->load->view('admin/login');
			}
		} else{
			$this->session->set_flashdata('message', '<div class="alert alert-warning alert-dismissible fade show" role="alert">
			<strong>Username Atau Password Salah!</strong> Pastikan username atau password yang anda masukan benar!!
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
			</div>');
			$this->load->view('admin/login');
		}
	}

	public function gantiPass(){
		if(empty($this->session->userdata('userName'))){
			redirect('adminpanel');
		}
		$userName = $this->session->userdata('userName');
		$data['admin'] = $this->db->get_where('tbl_admin', ['userName' => $userName])->row_array();
		$this->load->view('admin/layout/header');
		$this->load->view('admin/layout/menu');
		$this->load->view('admin/formEdit', $data);
		$this->load->view('admin/layout/footer');
	}

	public function editpass(){
		if(empty($this->session->userdata('userName'))){
			redirect('adminpanel');
		}
		$id = $this->input->post('id');	
		$pass = md5($this->input->post('password'));	
		$dataUpdate = array('password'=>$pass);
		$this->Madmin->update('tbl_admin', $dataUpdate, 'idAdmin', $id);
		$this->session->set_flashdata('berhasil', '<div class="alert alert-success" role="alert">
		Password berhasil diubah!
		</div>');
		redirect('adminpanel');
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect('adminpanel');
	}

}
