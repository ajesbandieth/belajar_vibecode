<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_model extends CI_Model {

    private $table = 'files';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function get_all($filters = array(), $limit = NULL, $offset = NULL) {
        $this->db->select('files.*, users.name as uploader_name, category.category_name');
        $this->db->from($this->table);
        $this->db->join('users', 'files.user_id = users.id');
        $this->db->join('category', 'files.category_id = category.id', 'left');
        $this->db->where('files.is_deleted', 0);

        if (!empty($filters['user_id'])) {
            $this->db->where('files.user_id', $filters['user_id']);
        }
        if (isset($filters['category_id'])) {
            if ($filters['category_id'] === 'uncategorized') {
                $this->db->where('files.category_id', NULL);
            } else {
                $this->db->where('files.category_id', $filters['category_id']);
            }
        }
        if (!empty($filters['type'])) {
            if ($filters['type'] === 'image') {
                $this->db->like('files.file_type', 'image/', 'after');
            } elseif ($filters['type'] === 'video') {
                $this->db->like('files.file_type', 'video/', 'after');
            }
        }

        $this->db->order_by('files.created_at', 'DESC');
        
        if ($limit !== NULL) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    public function count_all($filters = array()) {
        $this->db->from($this->table);
        $this->db->where('is_deleted', 0);

        if (!empty($filters['user_id'])) {
            $this->db->where('user_id', $filters['user_id']);
        }
        if (isset($filters['category_id'])) {
            if ($filters['category_id'] === 'uncategorized') {
                $this->db->where('category_id', NULL);
            } else {
                $this->db->where('category_id', $filters['category_id']);
            }
        }
        if (!empty($filters['type'])) {
            if ($filters['type'] === 'image') {
                $this->db->like('file_type', 'image/', 'after');
            } elseif ($filters['type'] === 'video') {
                $this->db->like('file_type', 'video/', 'after');
            }
        }

        return $this->db->count_all_results();
    }

    public function get_category_summary($user_id) {
        // This query gets categories that have files, plus the count and one sample file_path for cover
        $sql = "SELECT 
                    c.id, 
                    c.category_name, 
                    COUNT(f.id) as total_files,
                    (SELECT file_path FROM files WHERE category_id = c.id AND is_deleted = 0 AND file_type LIKE 'image/%' ORDER BY created_at DESC LIMIT 1) as cover_path
                FROM category c
                JOIN files f ON c.id = f.category_id
                WHERE f.user_id = ? AND f.is_deleted = 0 AND f.file_type LIKE 'image/%'
                GROUP BY c.id";
        
        $categories = $this->db->query($sql, array($user_id))->result_array();

        // Also get Uncategorized images
        $sql_uncategorized = "SELECT 
                                'uncategorized' as id, 
                                'Uncategorized' as category_name, 
                                COUNT(id) as total_files,
                                (SELECT file_path FROM files WHERE category_id IS NULL AND user_id = ? AND is_deleted = 0 AND file_type LIKE 'image/%' ORDER BY created_at DESC LIMIT 1) as cover_path
                             FROM files 
                             WHERE category_id IS NULL AND user_id = ? AND is_deleted = 0 AND file_type LIKE 'image/%'";
        
        $uncategorized = $this->db->query($sql_uncategorized, array($user_id, $user_id))->row_array();

        if ($uncategorized && $uncategorized['total_files'] > 0) {
            array_unshift($categories, $uncategorized);
        }

        return $categories;
    }

    public function get_by_id($id) {
        $this->db->select('files.*, users.name as uploader_name, category.category_name');
        $this->db->from($this->table);
        $this->db->join('users', 'files.user_id = users.id');
        $this->db->join('category', 'files.category_id = category.id', 'left');
        $this->db->where('files.id', $id);
        $this->db->where('files.is_deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, array('is_deleted' => 1));
    }
}
