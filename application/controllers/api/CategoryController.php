<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CategoryController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Category_model');
    }

    private function generate_slug($string) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return $slug;
    }

    public function register() {
        $input = json_decode($this->input->raw_input_stream, true);

        if (!isset($input['category_name']) || empty($input['category_name'])) {
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Parameter category_name wajib diisi')));
        }

        $slug = $this->generate_slug($input['category_name']);

        $data = array(
            'category_name' => $input['category_name'],
            'slug'          => $slug
        );

        $this->Category_model->create($data);
        
        // Buat folder fisik
        $dir_path = FCPATH . 'assets/uploads/' . $slug;
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
            // Tambahkan index.html untuk security
            file_put_contents($dir_path . '/index.html', '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
        }

        return $this->output
            ->set_status_header(201)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'sukses')));
    }

    public function index() {
        $categories = $this->Category_model->get_all();
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $categories)));
    }

    public function show($id) {
        $category = $this->Category_model->get_by_id($id);
        if (!$category) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Kategori tidak ditemukan')));
        }
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => $category)));
    }

    public function update($id) {
        $input = json_decode($this->input->raw_input_stream, true);
        
        $category = $this->Category_model->get_by_id($id);
        if (!$category) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Kategori tidak ditemukan')));
        }

        $update_data = array();
        
        if (isset($input['category_name']) && !empty($input['category_name'])) {
            $update_data['category_name'] = $input['category_name'];
            $update_data['slug'] = $this->generate_slug($input['category_name']);
        }

        if (!empty($update_data)) {
            // Jika slug berubah, pindahkan folder fisik
            if (isset($update_data['slug']) && $update_data['slug'] !== $category['slug']) {
                $old_path = FCPATH . 'assets/uploads/' . $category['slug'];
                $new_path = FCPATH . 'assets/uploads/' . $update_data['slug'];
                
                if (is_dir($old_path) && !is_dir($new_path)) {
                    rename($old_path, $new_path);
                } elseif (!is_dir($new_path)) {
                    // Jika folder lama tidak ada (misal kategori lama dibuat sebelum sistem ini), buat folder baru
                    mkdir($new_path, 0777, true);
                    file_put_contents($new_path . '/index.html', '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
                }
            }
            
            $this->Category_model->update($id, $update_data);
        }

        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'sukses')));
    }

    public function destroy($id) {
        $category = $this->Category_model->get_by_id($id);
        if (!$category) {
            return $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(array('data' => 'Kategori tidak ditemukan')));
        }

        $this->Category_model->delete($id);
        return $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('data' => 'sukses')));
    }
}
