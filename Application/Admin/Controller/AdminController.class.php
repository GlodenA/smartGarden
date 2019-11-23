<?php
namespace Admin\Controller;
use Think\Auth;
class AdminController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->adminDb = M('Admin');
        $this->siteSettingDb = M('Site_setting');
        $this->groupDb = M("Auth_group");
        $this->groupAccessDb = M("Auth_group_access");
    }
    /**
    * 管理员列表
    */
    public function index(){
        $count = $this->adminDb->where(array('is_delete'=>0))->count();
        $Page = new \Think\Page($count,20);
        $list = $this->adminDb->limit($Page->firstRow.','.$Page->listRows)->where(array('is_delete'=>0))->order('uid desc')->select();
        foreach ($list as $key => $v) {
            $whereGroup['id'] = $v['group_id'];
            $list[$key]['group_name'] = $this->groupDb->where($whereGroup)->getField("title");
        }
        $show = $Page->show();
        $this->assign('page',$show);
//        $this->assign('list',$list);
        $whereData['status'] = 1;
        $groupInfo = $this->groupDb->where($whereData)->select();
        $this->assign("groupInfo",$groupInfo);
        $this->display("admin_list");
    }
    /**
    * 管理员增加
    */
    public function adminAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $password = password($_POST['password']);
            $data['password'] = $password['password'];
            $data['encrypt'] = $password['encrypt'];
            $data['reg_date'] = $data['last_date'] = $data['update_time'] = time();
            $data['reg_ip'] = $data['last_ip'] = ip();
            //判断用户名是否存在
            $where['username'] = $data['username'];
            $isIn = $this->adminDb->where($where)->find();
            if($isIn){
                $this->error('账号已存在');
                return false;
            }
            if(!$data['username'] || !$data['password']){
                $this->error('操作失败');
            }else{
                $result = $this->adminDb->data($data)->add();
                //添加至管理组表
                $groupAccessData['uid'] = $result;
                $groupAccessData['group_id'] = $data["group_id"];
                $this->groupAccessDb->data($groupAccessData)->add();
                if($result){
                    $this->success('操作成功');
                }else{
                    $this->error('操作失败');
                }
            }
        }else{
            $whereData['status'] = 1;
            $groupInfo = $this->groupDb->where($whereData)->select();
            $this->assign("groupInfo",$groupInfo);
            layout(false);
            $this->display("admin_add");
        }
    }

    /**
    * 管理员编辑
    */
    public function adminEdit(){
        if(IS_POST){
            $info = I('info');
            $uid = $info['uid'];
            $whereData['uid'] = $uid;
            if(!checkuserinfo($_POST['info'])){
                $this->error("提交信息不合法");
            }
            $data = I("info");
            if(isset($_POST['password']) && !empty($_POST['password'])){
                $password = password($_POST['password']);
                $passwordData['password'] = $password['password'];
                $passwordData['encrypt'] = $password['encrypt'];
                $this->adminDb->where($whereData)->save($passwordData);
            }
            //管理组判断更新
            if(get_auth_group($uid,1) !=  $data['group_id']){
                $this->groupAccessDb->where($whereData)->setField("group_id",1);
            }
            //更新管理员信息
            $data['update_date'] = time();
            $this->adminDb->where($whereData)->save($data);
            // 如果为当前管理员更新本地缓存
            if($uid == session('admin_uid')){
                session('admin_username',''.$data['username'].'');
                session('admin_realname',''.$data['realname'].'');
                session('admin_avatar',''.$data['avatar'].'');
            }
            $this->success("操作成功");
        }else{
            $uid = I("uid");
            //管理员信息读取
            $whereData['uid'] = $uid;
            $adminInfo = $this->adminDb->where($whereData)->find();
            $adminInfo['group_id'] = get_auth_group($uid,1);
            $this->assign($adminInfo);

            //全部用户组获取
            $whereData['status'] = 1;
            $groupInfo = $this->groupDb->where($whereData)->select();
            $this->assign("groupInfo",$groupInfo);
            layout(false);
            $this->display("admin_edit");
        }
    }
    //  当前管理员我的资料修改
    public function myAdminEdit(){
        if(IS_POST){
            $whereData['uid'] = $uid = session('admin_uid');
            if($uid != session('admin_uid')){
                $this->error("非法操作");
            }
            if(!checkuserinfo($_POST['info'])){
                $this->error("提交信息不合法");
            }
            $data = $_POST['info'];
            if(isset($_POST['password']) && !empty($_POST['password'])){
                $password = password($_POST['password']);
                $data['password'] = $password['password'];
                $data['encrypt'] = $password['encrypt'];
            }
            //管理组判断更新
            if(get_auth_group($uid,1) !=  $data['group_id']){
                $this->groupAccessDb->where($whereData)->setField("group_id",$data['group_id']);
            }
            //更新管理员信息
            $data['update_date'] = time();
            $this->adminDb->where($whereData)->save($data);
            // 如果为当前管理员更新本地缓存
            session('admin_username',''.$data['username'].'');
            session('admin_realname',''.$data['realname'].'');
            session('admin_avatar',''.$data['avatar'].'');
            $this->success('操作成功');
        }else{
            //管理员信息读取
            $whereData['uid'] = $uid = session('admin_uid');
            $adminInfo = $this->adminDb->where($whereData)->find();
            $adminInfo['group_id'] = get_auth_group($uid,1);
            $this->assign($adminInfo);

            //全部用户组获取
            $whereData['status'] = 1;
            $groupInfo = $this->groupDb->where($whereData)->select();
            $this->assign("groupInfo",$groupInfo);
            $this->display("my_admin");
        }
    }
    // 管理员信息浏览
    public function adminInfo(){
        //管理员信息读取
        $uid = intval($_GET['uid']);
        $whereData['uid'] = $uid;
        $adminInfo = $this->adminDb->where($whereData)->find();
        $adminInfo['group_name'] = get_auth_group($adminInfo['uid'],0);
        $this->assign($adminInfo);

        //全部用户组获取
        $whereData['status'] = 1;
        $groupInfo = $this->groupDb->where($whereData)->select();
        $this->assign("groupInfo",$groupInfo);
        layout(false);
        $this->display("admin_info");
    }
    /**
    *管理员删除
    */
    public function adminDelete(){
        $uid = I("uid");
        if($uid == '1'){
            $this->error("当前管理员不允许删除");
        }
        if($uid == session("admin_uid")){
            $this->error("当前管理员不允许删除");
        }
        $where['uid'] = $uid;
        $this->groupAccessDb->data($where)->delete();
        $this->adminDb->data($where)->delete();
        $this->success('删除成功');
    }
    // 站点配置
    public function siteSetting(){
        if(IS_POST){
            $data = $_POST['info'];
            $info = $this->siteSettingDb->find();
            if($info){
                $where['id'] = $info['id'];
                $result = $this->siteSettingDb->where($where)->save($data);
            }else{
                $result = $this->siteSettingDb->add($data);
            }
            if($result){
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }else{
            $info = $this->siteSettingDb->find();
            $this->assign($info);
            $this->display("site_setting");
        }
    }

    public function getIndex(){
        $count = $this->adminDb->where(array('is_delete'=>0))->count();
        $listRows=10;
        $firstRow = $listRows*(I("page")-1);
        $list = $this->adminDb->limit($firstRow.','.$listRows)->where(array('is_delete'=>0))->order('uid desc')->select();
        foreach ($list as $key => $v) {
            $whereGroup['id'] = $v['group_id'];
            $list[$key]['group_name'] = $this->groupDb->where($whereGroup)->getField("title");
        }
        $ret["totalNumber"] = $count;
        $ret["list"]=$list;
        $this.$this->ajaxReturn($ret);
    }
}
