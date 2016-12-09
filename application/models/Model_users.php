<?php

class Model_users extends CI_Model {

    private $primary_key;
    private $main_table;

    public function __construct() {
        parent::__construct();
        //$this->load->database();
        $this->main_table = "users";
        $this->primary_key = "iUserId";
    }

    function insert($data = array()) {
        /*if (!isset($data['iAddedById']) || $data['iAddedById'] == "") {
            $data['iAddedById'] = $this->session->userdata('iUserId');
        }*/
        if (!isset($data['dAddedDate']) || $data['dAddedDate'] == "") {
            $data['dAddedDate'] = date('Y-m-d H:i:s');
        }
        /*if (!isset($data['iModifiedById']) || $data['iModifiedById'] == "") {
            $data['iModifiedById'] = $this->session->userdata('iUserId');
        } */       
        if (!isset($data['dModifiedDate']) || $data['dModifiedDate'] == "") {
            $data['dModifiedDate'] = date('Y-m-d H:i:s');
        }
        
        $this->db->insert($this->main_table, $data);
        $insertId = $this->db->insert_id();
        return $insertId;
    }

    function update($data = array(), $where) {
        /*if (!isset($data['iModifiedById']) || $data['iModifiedById'] == "") {
            $data['iModifiedById'] = $this->session->userdata('iUserId');
        }*/        
        if (!isset($data['dModifiedDate']) || $data['dModifiedDate'] == "") {
            $data['dModifiedDate'] = date('Y-m-d H:i:s');
        }
        
        if (intval($where)) {
            $this->db->where($this->primary_key, $where);
        } else {
            $this->db->where($where);
        }
        return $this->db->update($this->main_table, $data);
    }

    function delete($where) {
        if (intval($where)) {
            $this->db->where($this->primary_key, $where);
        } else {
            $this->db->where($where);
        }
        return $this->db->delete($this->main_table);
    }

    function getData($condition = "", $fields = "", $join_arr = array(), $groupby = "", $orderby = "", $limit = "", $having = "") {
        if ($fields == "") {
            $fields = "*";
        }
        $this->db->select($fields, false);
        $this->db->from($this->main_table);
        if ($condition != "") {
            if (intval($condition)) {
                $this->db->where($this->primary_key, $condition);
            } else {
                $this->db->where($condition);
            }
        }
        if ($orderby != "") {
            $this->db->order_by($orderby);
        }
        if ($limit != "") {
            list($offset, $limit) = @explode(",", $limit);
            $this->db->limit($offset, $limit);
        }
        if ($having != "") {
            $this->db->having($having);
        }
        if (is_array($join_arr) && count($join_arr) > 0) {
            foreach ($join_arr as $ky => $vl) {
                $this->db->join($vl['table'], $vl['condition'], $vl['jointype']);
            }
        }
        if ($groupby != "") {
            $this->db->group_by($groupby);
        }
        $list_data = $this->db->get()->result_array();
        //echo $this->db->last_query(); exit;

        return $list_data;
    }

    function getDataWithPaging($condition = "", $fields = "", $join_arr = array(), $groupby = "", $orderby = "", $climit = "", $having = "", $paging_array = array()) {
        $this->db->_escape_char = '';
        $this->load->library('pagination_ajax');

        if ($fields == '') {
            $fields = "$this->main_table.*";
        }

        $this->db->start_cache();
        if (is_array($join_arr) && count($join_arr) > 0) {
            foreach ($join_arr as $ky => $vl) {
                $this->db->join($vl['table'], $vl['condition'], $vl['jointype']);
            }
        }
        if (trim($condition) != '') {
            $this->db->where($condition);
        }
        if (trim($groupby) != '') {
            $this->db->group_by($groupby);
        }
        if (trim($having) != '') {
            $this->db->having($having);
        }
        if ($orderby != '') {
            $this->db->order_by($orderby);
        }
        $this->db->from($this->main_table);
        $this->db->stop_cache();

        $per_page = (isset($paging_array['per_page'])) ? $paging_array['per_page'] : $this->config->item('REC_LIMIT_FRONT');
        $page = (isset($paging_array['page'])) ? $paging_array['page'] : 0;
        if (is_array($paging_array) && count(array_filter($paging_array)) > 0) {
            if (trim($having) != '') {
                $this->db->select("$fields");
            } else {
                $this->db->select("count($this->main_table" . '.' . "$this->primary_key) as tot");
            }

            $list_data = $this->db->get()->result_array();

            if (trim($groupby) != '') {
                $num_totrec = @ count($list_data);
            } else {
                $num_totrec = $list_data[0]['tot'];
            }

            if ($orderby != '') {
                $this->db->order_by($orderby);
            }
            $paging_array['total_rows'] = $num_totrec;
            $this->pagination_ajax->initialize($paging_array);
        } else if ($climit != "") {
            list($limit, $offset) = @ explode(',', $climit);
            $per_page = $limit;
            $page = $offset;
        }

        $offset = (intval($page) * 1);
        $limit = $per_page;
        $this->db->limit($limit, $offset);

        $this->db->select($fields);
        $dtls = $this->db->get()->result_array();
        //echo $this->db->last_query(); exit;

        $this->pages = $this->pagination_ajax->create_links();

        $this->db->flush_cache();
        return $dtls;
    }

}
