<?php


/**
 * 
 */
class Zconfig_Mysql {

    /**
     * 
     */
    protected $table = 'zconfig';
    /**
     * 
     */
    protected $prefix = '';

    /**
     * __construct 
     *
     * @return 
     */
    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . $this->table;   
    }

    public function getTemplateByCode($code = '') {
        global $wpdb;
        $sql = $wpdb->prepare("select * from " . $this->table . ' where code = %s', $code);
        return $wpdb->get_row($sql);
    }

    /**
     * getTemplateList 
     *
     * @param $args
     *
     * @return 
     */
    public function getTemplateList($args = []) {
        global $wpdb;

        // name like 
        // code like 
        // page
        // page_size
        //$sql = $wpdb->prepare("select * from " . $this->table, '');

        $where = [];
        if (!empty($args['name'])) {
            $where[] = 'name like "%' . esc_sql($args['name']) . '%"';
        }
        if (!empty($args['code'])) {
            $where[] = 'code like "%' . esc_sql($args['name']) . '%"';
        }

        if (!empty($where)) {
            $where = ' where ' . implode(' and ', $where);
        } else {
            $where = '';
        }

        // 分页
        $page = isset($args['page']) ? $args['page'] : 1;//当前是第几页
        if ($page < 1) {
            $page = 1;
        }
        $pagesize = isset($args['page_size']) ? $args['page_size'] : 10;//每页显示记录数量
        $pages = ($page - 1) * $pagesize;//偏移量

        $sql = "select * from " . $this->table . $where . " limit {$pages},{$pagesize}";
        return $wpdb->get_results($sql);
    }

    /**
     * getTemplateCount 
     *
     * @return 
     */
    public function getTemplateCount() {
        global $wpdb;
        $data = $wpdb->get_row('select count(1) as  count from ' . $this->table);
        if (!empty($data)) {
            return $data->count;
        }

        return 0;
    }

    /**
     * deleteTempalteById 
     *
     * @param $id
     *
     * @return 
     */
    public function deleteTempalteById($id) {
        if (!is_numeric($id)) {
            return false;
        }
        global $wpdb;
        $nums = $wpdb->delete($this->table, ['id' => $id], ['%d']);

        return $nums;
    }

    /**
     * getTemplateDetail 
     *
     * @param $id
     *
     * @return 
     */
    public function getTemplateDetail($id) {
        global $wpdb;

        return $wpdb->get_row('select * from ' . $this->table . ' where id = ' . $id);
    }

    /**
     * formatData 
     *
     * @return 
     */
    public function  formatData()
    {

    }

    /**
     * saveTemplate 
     *
     * @param $data
     *
     * @return 
     */
    public function saveTemplate($data = []) {
        global $wpdb;
        $saveData = [];
        $fieldType = [];
        $fields = ['name', 'code', 'comment', 'multi', 'template', 'data'];
        foreach($fields as $field) {
            if (!isset($data[$field])) {
                continue;
            }
            if ($field == 'multi') {
                $fieldType[]  = '%d'; 
                $saveData[$field] = $data[$field] == 'true' ? 1 : 0;
            } else {
                $saveData[$field] = $data[$field];
                $fieldType[]  = '%s'; 
            }
        }
        if (empty($saveData)) {
            return false;
        }
        if (isset($data['id']) && $data['id'] > 0) {
            // 更新操作
            $id = $data['id'];
            $id = $wpdb->update($this->table, $saveData, ['id' => $id], $fieldType, ['%d']);
        } else {
            // 新增操作
            $wpdb->insert($this->table, $saveData, $fieldType);
            $id = $wpdb->insert_id;
        }

        return $id;
        }
    }
