<?php
class Zconfig_Admin {
    public static function init() {
        add_action('admin_init', ['Zconfig_Admin', 'registerApi']);
        add_action('admin_menu', ['Zconfig_Admin', 'addMenu']); 
    }

    public static function addMenu() {
        add_menu_page("高级配置", "配置", "administrator", 'zconfig_index', ['Zconfig_Admin', 'custom_menu_page'], '', 25);
        add_submenu_page('zconfig_index', '模板', '模板', 'administrator', 'zconfig_template', ['Zconfig_Admin', 'custom_template_page']);
    }

    public static function custom_menu_page() {
        // 填充数据
        include(ZCONFIG_PATH . '/views/zconfig.html');
    }

    public static function registerApi() {
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        if (!in_array($action, ['save', 'list'])) {
            return;
        }
        $mysql = new Zconfig_Mysql();
        switch($action) {
        case 'save':
            // 保存的时候分两种情况，一种是有id，一种是没有id
            try {
                $id = $mysql->saveTemplate($_POST);
                echo json_encode([
                    'code' => $id >= 0 ? 200 : 500,
                    'msg' => $id >= 0 ? '更新成功' : '更新失败,请检查模板标识是否重复',
                    'data' => [
                        'id' => $id,
                    ]
                ]);
            } catch(Exception $e) {
                $id = false;
                return json_encode([
                    'code' => 500,
                    'msg' => $e->getMessage(),
                    'data' => [
                        'id' => $id,
                    ]
                ]);
            }
            break;
        case 'list':
            // 获取所有的列表数据
            $templateList = $mysql->getTemplateList();

            echo json_encode([
                'code' => 200,
                'msg' => '获取成功',
                'data' => $templateList,
            ]);
            break;
        }
        exit;
    }

    public static function custom_template_page() {
        // 根据不同的参数调用不同的模板
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        $mysql = new Zconfig_Mysql();

        switch($action) {
        case 'edit':
        case 'add':
            if (isset($_GET['id']) && $_GET['id'] > 0) {
                $template = $mysql->getTemplateDetail($_GET['id']);
            } else {
                $template = null;
            }
            include(ZCONFIG_PATH . '/views/template_edit.html');
            break;
        case 'delete':
            // 触发删除的时候，加个提示框，然后跳转回去
            $nums = $mysql->deleteTempalteById($_GET['id']);
            if ($nums) {
                echo "<script>alert('删除成功');window.location.href = document.referrer;</script>";
            } else {
                echo "<script>alert('删除失败');window.history.back();</script>";
            }
            break;
        default:
            // 展示模板
            // 获取列表数据 
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            if ($page < 1) {
                $page = 1;
            }
            $pageSize = get_option('posts_per_page');
            $templateList = $mysql->getTemplateList([
                'page' => $page,
                'name' => isset($_GET['name']) ? $_GET['name'] : '',
                'code' => isset($_GET['code']) ? $_GET['code'] : '',
                'page_size' => $pageSize,
            ]);
            $templateCount = $mysql->getTemplateCount();
            $pageNum = ceil($templateCount/$pageSize);

            $homeUrl = self::getPageUrl($_GET, '1');
            $preUrl = self::getPageUrl($_GET, max($page - 1, 1));
            $nextUrl = self::getPageUrl($_GET, min($page + 1, $pageNum));
            $lastUrl = self::getPageUrl($_GET, $pageNum);

            include(ZCONFIG_PATH . '/views/template_list.html');
            break;
        }
    }

    public static function getPageUrl($args, $page) {
        $baseUrl = '/wp-admin/admin.php';
        $args['page'] = $page;

        return $baseUrl . '?' . http_build_query($args);
    }
}
