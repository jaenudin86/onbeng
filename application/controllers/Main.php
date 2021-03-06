<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('url', 'header'));
		$this->load->library(array('session', 'encrypt'));
		date_default_timezone_set('Asia/Jakarta');
    if($this->session->userdata('logged'))
    {
      redirect('marker');
    }
	}

	public function index()
	{
    set_title('Main Page Onbeng - Online Bengkel');
		$this->load->view('meta');
		$this->load->view('script');
		$this->load->view('query_data');
		$this->load->view('main/view');
	}


	public function login($json = null)
	{
		$user = $this->security->xss_clean($this->input->post('user'));
		$pass = $this->security->xss_clean($this->input->post('pass'));

    if(!empty($user) && !empty($pass))
    {
      $pass   = hash('ripemd160', hash('sha1', $pass));
		  $ph     = password_hash($pass, PASSWORD_BCRYPT);
			$query  = $this->db->select('id, pass');
      $query  = $this->db->from('beo_admin');
      $query  = $this->db->where('user', $user);
      $query  = $this->db->get();
      if($query->num_rows() > 0)
      {
        $result = $query->row()->pass;
        if(password_verify($result, $ph))
        {
          $sess = array(
            'user' => $user,
            'ip'   => $this->input->ip_address()
            );

          $this->session->set_userdata('logged', $sess);

          $data = array(
            'last_login' => date('Y-m-d H:i:s')
            );
          $update = $this->db->where('user', $user);
          $update = $this->db->update('beo_admin', $data);
          $output = array(
              'ok'  => 1,
              'msg' => 'success',
              'href'=> base_url('marker')
              );
          if($json == 'json'){
            $output['token'] = $this->encrypt->encode($query->row()->id);
          }
          echo json_encode($output);
        }else
        {
          echo json_encode(
            array(
              'ok'  => 0,
              'msg' => 'Wrong password'
              )
            );
        }
      }else{
        echo json_encode(
          array(
            'ok'  => 0,
            'msg' => 'Username not found'
            )
          );
      }
		}else
		{
			echo json_encode(
				array(
	        'ok'  => 0,
	        'msg' => 'Empty data'
	        )
				);
		}
	}

	public function load($id)
	{
		switch ($id) {
			case '1':
				$this->load->view('main/how-to-use');
				break;
			case '2':
				$this->load->view('main/about');
				break;			
			default:
				echo json_encode(array(
	        'ok'  => 0,
	        'msg' => 'Error page'
        	)
				);
				break;
		}
	}
	
}