<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

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
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		if ($this->input->get('format') === 'json') {
			$data = array(
				'message' => 'Welcome to CodeIgniter 3 API',
				'status' => 'success',
				'tech_stack' => array(
					'PHP' => '5.6',
					'Framework' => 'CodeIgniter 3.1.13',
					'Webserver' => 'Apache'
				)
			);
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode($data));
		}

		$this->load->view('welcome_message');
	}
}
