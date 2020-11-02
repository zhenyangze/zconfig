<?php 

class Zconfig {
    public static function init() {
    }

    public static function getFieldTypeList() {
        return [
            1 => '单行文本',
            2 => '多行文本',
            3 => '单选框',
            4 => '多选框',
            5 => '媒体',
        ];
    }

    public static function registerStatic() {
        wp_register_script('zconfig-vue', plugins_url('static/vue.js', __FILE__), array(),'0.1', false);
        wp_register_script('zconfig-axios', plugins_url('static/axios.min.js', __FILE__), array(),'0.1', true);

        wp_register_script('zconfig-jquery', plugins_url('static/select2/jquery.min.js', __FILE__), array(),'0.1', true);
        wp_register_script('zconfig-select2', plugins_url('static/select2/select2.full.min.js', __FILE__), array(),'0.1', true);
        wp_register_style('zconfig-select2css', plugins_url('static/select2/select2.min.css', __FILE__), '','0.2');
    }
}
