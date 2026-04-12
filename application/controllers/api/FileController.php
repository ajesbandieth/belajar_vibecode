<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FileController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('File_model');
        $this->load->model('Category_model');
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
        $file_name_input = $this->input->post('file_name');

        // Tentukan upload path berdasarkan kategori
        $upload_dir = 'assets/uploads/';
        $category_slug = '';
        
        if (!empty($category_id)) {
            $category = $this->Category_model->get_by_id($category_id);
            if ($category) {
                $category_slug = $category['slug'];
                $upload_dir .= $category_slug . '/';
                
                // Pastikan folder kategori ada
                $full_dir_path = FCPATH . $upload_dir;
                if (!is_dir($full_dir_path)) {
                    mkdir($full_dir_path, 0777, true);
                    file_put_contents($full_dir_path . '/index.html', '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
                }
            }
        }

        $config['upload_path']          = './' . $upload_dir;
        $config['allowed_types']        = 'jpg|jpeg|png|gif|mp4|pdf|zip';
        $config['max_size']             = 512000; // ~500 MB in KB
        $config['encrypt_name']         = TRUE;

        $this->load->library('upload', $config);

        $uploaded_files = array();
        $errors = array();

        // Check if files exist
        if (!isset($_FILES['original_name']['name']) || empty($_FILES['original_name']['name'][0])) {
            return $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'No files were uploaded.')));
        }

        $files_count = count($_FILES['original_name']['name']);

        for ($i = 0; $i < $files_count; $i++) {
            if ($_FILES['original_name']['error'][$i] == 4) continue; // No file uploaded in this slot

            // Create a fake single file array for CI3 Upload library
            $_FILES['single_file']['name']     = $_FILES['original_name']['name'][$i];
            $_FILES['single_file']['type']     = $_FILES['original_name']['type'][$i];
            $_FILES['single_file']['tmp_name'] = $_FILES['original_name']['tmp_name'][$i];
            $_FILES['single_file']['error']    = $_FILES['original_name']['error'][$i];
            $_FILES['single_file']['size']     = $_FILES['original_name']['size'][$i];

            // Fix File Extension Spoofing / Mismatch (e.g. JPG file containing PNG data)
            if (isset($_FILES['single_file']['tmp_name']) && file_exists($_FILES['single_file']['tmp_name'])) {
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $real_mime = @$finfo->file($_FILES['single_file']['tmp_name']);
                $mime_map = array(
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/gif'  => 'gif',
                    'video/mp4'  => 'mp4',
                    'application/pdf' => 'pdf',
                    'application/zip' => 'zip'
                );
                if ($real_mime && isset($mime_map[$real_mime])) {
                    $correct_ext = $mime_map[$real_mime];
                    $current_name = $_FILES['single_file']['name'];
                    $pathinfo = pathinfo($current_name);
                    $current_ext = strtolower(isset($pathinfo['extension']) ? $pathinfo['extension'] : '');
                    
                    if ($current_ext !== $correct_ext && !(in_array($current_ext, ['jpg', 'jpeg']) && $correct_ext == 'jpg')) {
                        // Force the correct extension
                        $_FILES['single_file']['name'] = $pathinfo['filename'] . '.' . $correct_ext;
                    }
                }
            }

            if (!$this->upload->do_upload('single_file')) {
                $errors[] = $_FILES['single_file']['name'] . ' : ' . $this->upload->display_errors('','');
            } else {
                $upload_data = $this->upload->data();

                // If multiple files, we ignore the manual string $file_name_input because it only applies to one.
                // We'll use the original name for each file if multiple are uploaded.
                $final_file_name = ($files_count == 1 && !empty($file_name_input)) ? $file_name_input : $upload_data['orig_name'];

                $file_data = array(
                    'user_id'       => $user_id,
                    'category_id'   => empty($category_id) ? NULL : $category_id,
                    'original_name' => $upload_data['orig_name'],
                    'file_name'     => $final_file_name,
                    'file_path'     => $upload_dir . $upload_data['file_name'],
                    'file_size'     => $upload_data['file_size'] * 1024,
                    'file_type'     => $upload_data['file_type']
                );

                $this->File_model->create($file_data);
                $uploaded_files[] = $file_data;
            }
        }

        if (empty($uploaded_files) && !empty($errors)) {
            // ALL failed
            return $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => implode(", ", $errors))));
        } else if (!empty($errors)) {
            // Partial success
            return $this->output->set_status_header(201)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Berhasil sebagian. '. implode(", ", $errors), 'files' => $uploaded_files)));
        } else {
            // Full success
            return $this->output->set_status_header(201)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Upload sukses', 'files' => $uploaded_files)));
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
        if ($this->input->get('type')) {
            $filters['type'] = $this->input->get('type');
        }

        // Pagination parameters
        $page = (int)$this->input->get('page');
        $limit = (int)$this->input->get('limit');
        if ($page < 1) $page = 1;
        if ($limit < 1) $limit = 20;
        $offset = ($page - 1) * $limit;

        $total_items = $this->File_model->count_all($filters);
        $files = $this->File_model->get_all($filters, $limit, $offset);

        return $this->output->set_status_header(200)->set_content_type('application/json')
            ->set_output(json_encode(array(
                'data' => $files,
                'pagination' => array(
                    'total_items' => $total_items,
                    'total_pages' => ceil($total_items / $limit),
                    'current_page' => $page,
                    'limit' => $limit
                )
            )));
    }

    public function summary() {
        $user_id = $this->get_authenticated_user_id();
        if (!$user_id) {
            return $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Unauthorized')));
        }

        $summary = $this->File_model->get_category_summary($user_id);

        return $this->output->set_status_header(200)->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $summary)));
    }

    public function update($id) {
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

        if ($file['user_id'] != $user_id) {
            return $this->output->set_status_header(403)->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Forbidden')));
        }

        $data = json_decode($this->input->raw_input_stream, true);
        if (!$data) $data = $this->input->post();

        $update_data = array();
        if (isset($data['file_name'])) {
            $update_data['file_name'] = $data['file_name'];
        }
        if (isset($data['category_id'])) {
            $update_data['category_id'] = empty($data['category_id']) ? NULL : $data['category_id'];
        }

        if (!empty($update_data)) {
            $this->File_model->update($id, $update_data);
        }

        return $this->output->set_status_header(200)->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'Update sukses')));
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

        // Soft Delete (only update DB)
        $this->File_model->delete($id);

        return $this->output->set_status_header(200)->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'File berhasil dihapus')));
    }
}
