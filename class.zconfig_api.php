<?php 
// 对外提供api接口
/**
 * 
 */
class Zconfig_Api {
    /**
     * init 
     *
     * @return 
     */
    public static function init() {
        self::dispatch();
    }

    /**
     * dispatch 
     *
     * @return 
     */
    public static function dispatch() {
        $plugin = isset($_GET['plugin']) && $_GET['plugin'] == 'zconfig_api';
        if (!$plugin || empty($_GET['action'])) {
            return;
        }

        switch($_GET['action']) {
        case 'list':
            self::list($_GET);
            break;
        case 'config':
            self::config($_GET);
            break;

        default:
            print_r([
                'plugin' => 'zconfig_api',
                'action' => ['list', 'config'],
                'args' => [],
            ]);
            break;
        }

        exit;
    }

    /**
     * list 
     *
     * @return 
     */
    public static function list() {

    }

    /**
     * config 
     *
     * @return 
     */
    // /?plugin=zconfig_api&action=config&code=banner_list&sub_key=single&sub_value=%E7%94%B7
    public static function config() {
        if (!isset($_GET['code']) || empty($_GET['code'])) {
            echo json_encode([
                'code' => 402,
                'msg' => '参数错误',
                'data' => [],
            ]);
            return;
        }
        $code = $_GET['code'];
        $subKey = isset($_GET['sub_key']) ? $_GET['sub_key'] : ''; 
        $subValue = isset($_GET['sub_value']) ? $_GET['sub_value'] : ''; 
        $mysql = new Zconfig_Mysql();
        $data = $mysql->getTemplateByCode($code);
        $subData = json_decode(str_ireplace('\"', '"', $data->data), true);
        if (!empty($subKey)) {
            foreach($subData as $item) {
                if (isset($item[$subKey]) && $item[$subKey] == $subValue) {
                    $subData = $item;
                    break;
                }
            }
        } else {
            $subData = $data->multi ? $subData : current($subData);
        }

        echo json_encode([
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'id' => $data->id,
                'name' => $data->name,
                'code' => $data->code,
                'comment' => $data->comment,
                'data' => $subData,
            ]
        ]);
        return;
    }
};
