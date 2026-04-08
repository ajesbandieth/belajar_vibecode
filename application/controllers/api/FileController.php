<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FileController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('File_model');
        $this->load->model('Session_model');
        $this->load->helper(array('form', 'url'));
    }

    private function get_authenticated_user_id() {
        $auth_header = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth_header || strpos($auth_header, 'Bearer ') !== 0) {
            return false;
        }
        $token = substr($auth_header, 7);
        $session = $this->Session_model->get_by_token($token);
        return $session ? $session['user_id'] : false;
    }

    public function upload() {
        $user_id = $this->get_authenticated_user_id();
        if (!$user_id) {
            return $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Unauthorized')));
        }

        $category_id = $this->input->post('category_id');

        $config['upload_path']          = './assets/uploads/';
        $config['allowed_types']        = 'jpg|jpeg|png|gif|mp4|pdf|zip';
        $config['max_size']             = 512000; // ~500 MB in KB
        $config['encrypt_name']         = TRUE;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('file')) {
            return $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => $this->upload->display_errors('',''))));
        } else {
            $upload_data = $this->upload->data();

            $file_data = array(
                'user_id'       => $user_id,
                'category_id'   => empty($category_id) ? NULL : $category_id,
                'original_name' => $upload_data['orig_name'],
                'file_name'     => $upload_data['file_name'],
                'file_path'     => 'assets/uploads/' . $upload_data['file_name'],
                'file_size'     => $upload_data['file_size'] * 1024, // Convert KB to Bytes
                'file_type'     => $upload_data['file_type']
            );

            $this->File_model->create($file_data);

            return $this->output->set_status_header(201)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Upload sukses', 'file' => $file_data)));
        }
    }

    public function index() {
        $user_id = $this->get_authenticated_user_id();
        if (!$user_id) {
            return $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Unauthorized')));
        }

        // Filters Optional
        $filters = array();
        if ($this->input->get('category_id')) {
            $filters['category_id'] = $this->input->get('category_id');
        }

        $files = $this->File_model->get_all($filters);

        return $this->output->set_status_header(200)->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $files)));
    }

    public function destroy($id) {
        $user_id = $this->get_authenticated_user_id();
        if (!$user_id) {
            return $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Unauthorized')));
        }

        $file = $this->File_model->get_by_id($id);
        if (!$file) {
            return $this->output->set_status_header(404)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'File tidak ditemukan')));
        }

        // Verify Ownership
        if ($file['user_id'] != $user_id) {
            return $this->output->set_status_header(403)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Forbidden')));
        }

        // Delete File physically
        $physical_path = FCPATH . $file['file_path'];
        if (file_exists($physical_path)) {
            unlink($physical_path);
        }

        // Delete from DB
        $this->File_model->delete($id);

        return $this->output->set_status_header(200)->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'File berhasil dihapus')));
    }
}
