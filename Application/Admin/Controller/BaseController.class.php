<?php
//定义后台,判断管理员
namespace Admin\Controller;

use Think\Controller;
use Think\Auth;
use Think\Log;
define('IN_ADMIN', true);

class BaseController extends Controller
{

    public function _initialize() {
        self::check_admin();
        $this->getMenus();
    }

    final public function checkRule()
    {
        $auth = new Auth();
        $methodName = CONTROLLER_NAME . '/' . ACTION_NAME;
        $authRuleDb = M('Auth_rule');
        $where['name'] = $methodName;
        if ($authRuleDb->where($where)->find()) {
            $authAction = array(
                'login','logout','verify','index','signUp','changePassword'
            );
            if (!$auth->check($methodName, session('admin_uid')) && session('admin_uid') != 1 && !in_array(ACTION_NAME,$authAction)) {
                $this->display('ErrorPage/error404');
                exit;
                // echo '<script>alert("没有权限");</script>';exit;
                // exit('<script>alert("没有权限");</script>');
            }
        }
    }

    //验证登录
    final public function check_admin()
    {
        Log::write("123456");
        if (MODULE_NAME == 'Admin' && CONTROLLER_NAME == 'Index' && in_array(ACTION_NAME, array('login','logout','verify','signUp','changePassword'))) {
            return true;
        } else {
            //验证管理员
            $admin_uid = session('admin_uid');
            if (!$admin_uid) {
                $this->redirect('Index/login');
                return;
            }
            // $this->getMenus();
            $this->checkRule();
        }
    }

    final public function getMenus()
    {
        $menuDb = M('Admin_menu');
        $groupDb = M('Auth_group');
        $groupAccessDb = M('Auth_group_access');
        $admin_uid = session('admin_uid');
        if ($admin_uid) {
            // 获取用户组
            $whereAdmin['uid'] = $admin_uid;
            $group_id = $groupAccessDb->where($whereAdmin)->getField("group_id");
            $whereGroup['id'] = $group_id;
            $menu_ids = $groupDb->where($whereGroup)->getField("menu_ids");

            $tree = new \Org\Tree\Tree;
            $whereMenu['is_show'] = 1;
            $whereMenu['is_delete'] = 0;
            if ($admin_uid != 1) {
                $whereMenu['id'] = array('IN', $menu_ids);
            }
            $data = $menuDb->where($whereMenu)->order('sort asc,id asc')->select();
            $menuList = $tree->makeTree($data);
            session('ADMIN_MENU_LIST', $menuList);
            return $menuList;
        }
    }

    final public function currentPos($id)
    {
        $menudb = M('Admin_menu');
        $where['id'] = $id;
        $r = $menudb->where($where)->find();
        $str = '';
        if ($r['parent_id']) {
            $str = self::currentPos($r['parent_id']);
        }
        return $str . L($r['name']) . ' > ';
    }
}