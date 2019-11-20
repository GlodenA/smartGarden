<?php
/**
 * Created by PhpStorm.
 * User: 焱
 * Date: 2018/8/29
 * Time: 14:59
 */

namespace Admin\Controller;
use Think\Log;
use Think\Model;
class CustomerController extends BaseController
{
    public function __construct(){
        parent::__construct();
        $this->customerDb = M('Admin');
        $this->groupDb = M("Auth_group");
        $this->customerAccessDb =M("Auth_group_access");
        $this->adminDb = M('Admin');
    }
    //查询客户信息列表
    public function customerAuthList(){
        $uid=I("uid");
        $username=I("username");
        //判断是否为空放进条件中
        $where["status"]="1";
        if($uid){
            $where["uid"]=$uid;
        }
        if($username){
            $where["username"]=$username;
        }
        if (session("admin_uid") != '1'){
            $where["parent_id"]=$uid;
        }
        $count = $this->customerDb->where($where)->count();
        $Page = new \Think\Page($count,5);
        $list = $this->customerDb->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('uid desc')->select();
        foreach ($list as $key => $v) {
            $whereGroup['id'] = $v['group_id'];
            $list[$key]['group_name'] = $this->groupDb->where($whereGroup)->getField("title");
        }
        $number = $Page->parameter["p"];
        if($number && $number > 0){
            $number = ($Page->parameter["p"] - 1)*5;
        }else{
            $number = 0;
        }
        $this->assign('number',$number);
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('uid',$uid);
        $this->assign('username',$username);
        $this->assign('list',$list);
        $this->display("Customer_Auth");
    }
    //查询客户信息列表
    public function customerPWDList(){
        $uid=I("uid");
        $username=I("username");
        //判断是否为空放进条件中
        $where["status"]="1";
        if($uid){
            $where["uid"]=$uid;
        }
        if($username){
            $where["username"]=$username;
        }
        if (session("admin_uid") != '1'){
            $where["parent_id"]=$uid;
        }
        $count = $this->customerDb->where($where)->count();
        $Page = new \Think\Page($count,5);
        $list = $this->customerDb->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('uid desc')->select();
        foreach ($list as $key => $v) {
            $whereGroup['id'] = $v['group_id'];
            $list[$key]['group_name'] = $this->groupDb->where($whereGroup)->getField("title");
        }
        $number = $Page->parameter["p"];
        if($number && $number > 0){
            $number = ($Page->parameter["p"] - 1)*5;
        }else{
            $number = 0;
        }
        $this->assign('number',$number);
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('uid',$uid);
        $this->assign('username',$username);
        $this->assign('list',$list);
        $this->display("Customer_PWDList");
    }
    //查询客户信息列表
    public function customerList(){
        $uid=I("uid");
        $username=I("username");
        //判断是否为空放进条件中
        $where["status"]="1";
        if($uid){
            $where["uid"]=$uid;
        }
        if($username){
            $where["username"]=$username;
        }
        if (session("admin_uid") != '1'){
            $where["parent_id"]=$uid;
        }
        $count = $this->customerDb->where($where)->count();
        $Page = new \Think\Page($count,10);
        $list = $this->customerDb->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('uid desc')->select();
        foreach ($list as $key => $v) {
            $whereGroup['id'] = $v['group_id'];
            $list[$key]['group_name'] = $this->groupDb->where($whereGroup)->getField("title");
        }
        $number = $Page->parameter["p"];
        if($number && $number > 0){
            $number = ($Page->parameter["p"] - 1)*10;
        }else{
            $number = 0;
        }
        $this->assign('number',$number);
        $show = $Page->show();
        $this->assign('page',$show);
        $this->assign('uid',$uid);
        $this->assign('username',$username);
        $this->assign('list',$list);
        $this->display("Customer_List");
    }
    // 管理员信息浏览
    public function customerInfo(){
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
    public function customerDelete(){
        $uid = intval($_POST['uid']);
        if($uid == '1'){
            $this->error("当前管理员不允许删除");
        }
        if($uid == session("admin_uid")){
            $this->error("当前管理员不允许删除");
        }
        $where['uid'] = $uid;
        $username=  $this->adminDb->data($where)->getField("username");
        $this->adminDb->data($where)->setField("is_delete",1);
        $this->groupAccessDb->data($where)->delete();
        $this->success('删除成功');
    }

    /**
     * 客户增加
     */
    public function adminAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $password = password($_POST['password']);
            $data['password'] = $password['password'];
            $data['encrypt'] = $password['encrypt'];
            $data['reg_date'] = $data['last_date'] = $data['update_time'] = time();
            $data['reg_ip'] = $data['last_ip'] = ip();
            $data['parent_id'] = session("admin_uid");//获取当前登录的管理员id作为父id
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
            $this->display("customerAdd");
        }
    }
    /**
     * 管理员编辑
     */
    public function customerEdit(){
        if(IS_POST){
            $uid = I("uid");
            $whereData['uid'] = $uid;
            if(!checkuserinfo($_POST['info'])){
                $this->error("提交信息不合法");
            }
            $data = $_POST['info'];
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
    //密码修改
    function changePWD(){
        if (IS_POST) {
            $uid = I("uid");
            $parent_id = I("parent_id");
            $customer=$this->adminDb->where(array("id" => $uid))->find();
            if($customer["password"]!=I("password")){
                $this->error('操作失败');
            }
            if($uid == '1' && session("admin_uid") != '1'){
                $this->error("不允许修改该管理员密码");
            }
            if($parent_id != session("admin_uid") && session("admin_uid") !='1'){
                $this->error("不允许修改该管理员密码");
            }
            $password=I("password");
            $this->adminDb->where(array("uid" => $uid))->setField("password", $password);
            $this->success('操作成功');
        }
    }
    //密码重置
    function resetPWD(){//重置的密码是给的默认密码么
        $uid = I("uid");
        $parent_id = I("parent_id");
        //获取当前职位信息
        if($uid == '1' && session("admin_uid") != '1'){
            $this->error("不允许修改该管理员密码");
        }
        if($parent_id != session("admin_uid") && session("admin_uid") !='1'){
            $this->error("不允许修改该管理员密码");
        }
        $this->adminDb->where(array("id" => $uid))->setField("password", "123456");
        $this->success('密码重置成功');

    }
    //
    /**
     * 修改权限
     */
    public function authEdit(){
        if(IS_POST){
            $uid = I("uid");
            $whereData['uid'] = $uid;
            $whereData1['uid'] = $uid;
            $parent_id = I("parent_id");
            $data = $_POST['info'];
            //管理组判断更新
            if($uid == "1"){
                $this->error("不允许修改该管理员权限");
            }
            if($parent_id != session("admin_uid") && session("admin_uid") !='1'){
                $this->error("不允许修改该管理员权限");
            }
            $this->customerAccessDb->where($whereData1)->setField("group_id",$data["group_id"]);
            //更新管理员信息
            $this->customerDb->where($whereData)->setField("group_id",$data["group_id"]);
            $this->success("操作成功");
            $this->display("customerEdit");
        }else{
            $uid = I("uid");
            //管理员信息读取
            $whereData['uid'] = $uid;
            $customerInfo = $this->customerDb->where($whereData)->find();
            $customerInfo['group_id'] = get_auth_group($uid,1);
            $this->assign($customerInfo);

            //全部用户组获取
            $where['status']= 1;
            $groupInfo = $this->groupDb->where($where)->select();
            $this->assign("groupInfo",$groupInfo);
            layout(false);
            $this->display("customerEdit");
        }
    }
}