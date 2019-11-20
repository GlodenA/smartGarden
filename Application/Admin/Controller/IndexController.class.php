<?php
namespace Admin\Controller;
//use Think\Controller;
class IndexController extends BaseController {
    public function __construct(){
        parent::__construct();
    }
    //后台入口
    public function index(){
        layout(false);
        $this->display('index');
    }
    // 后台首页
    public function main(){
        //获取时间戳
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday = $beginToday + 24*60*60-1;
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $beginThisyear=mktime(0,0,0,1,1,date('Y'));
        $where_today['w.add_time']=array('gt', $beginToday);
        $where_today['_string']='w.uid=m.userid and a.userid=m.userid and w.type > 1';
        $list_today=M()
            ->table('__WARNING_MESSAGE__ w,__MEMBER__ a,__MACHINE__ m')
            ->where($where_today)
            ->field('w.type,w.uid,w.add_time,a.realname,a.job_number,m.machine_id,m.machine_name,m.machine_imei,a.position,a.parent_id')
            ->limit(10)
            ->order("w.add_time desc")
            ->select();
        $this->assign('list_today',$list_today);
        $newTime = date("Y-m-d",time());
        $this->assign('newTime',$newTime);
        //总员工数
        $where["is_delete"] = 0;
        $count_member = M('Member')->where($where)->count();
        $this->assign('count_member',$count_member);
        //设备总数
        $mahcineCount = M("Machine")->where(array("is_delete"=>0))->count();
        $this->assign("mahcine_count",$mahcineCount);
        //当前在线设备数
        $wheremgc["machine_status"] = array(array("eq","Stop"),array("eq","Move"),"or");
        $wheremgc["is_delete"] = 0;
        $mount_guard_count = M("Machine")->where($wheremgc)->count();
        $this->assign("mount_guard_count",$mount_guard_count);
        //今日在线设备数总计
        $whereTodayMgc["is_delete"] = 0;
        $whereTodayMgc["login_time"] = array("gt",$beginToday);
        $today_mount_guard_count = M("Machine")->where($whereTodayMgc)->count();
        $this->assign("today_mount_guard_count",$today_mount_guard_count);
        //在岗人数
        $out_guard_count = M("Member")->where(array("is_delete"=>0,"job_status"=>1))->count();
        $this->assign("on_guard_count",$out_guard_count);
        //离岗人数
        $out_guard_count = M("Member")->where(array("is_delete"=>0,"job_status"=>0))->count();
        $this->assign("out_guard_count",$out_guard_count);
        //未上岗人数
        $no_guard_count = M("Member")->where(array("is_delete"=>0,"job_status"=>2))->count();
        $this->assign("no_guard_count",$no_guard_count);
        $whereAttention["is_delete"] = 0;
        $whereAttention["add_time"] = array(array("gt",$beginToday),array("lt",$endToday));
        //上午
        //正常
        $am_t_normal_count = M('Attendance')->where($whereAttention)->where("first_result=1")->count();
        $this->assign("am_t_normal_count",$am_t_normal_count);
        //迟到
        $am_late_count = M('Attendance')->where($whereAttention)->where("first_result=3")->count();
        $this->assign("am_late_count",$am_late_count);
        //旷工
        $am_absenteeism_count = M('Attendance')->where($whereAttention)->where("first_result=5")->count();
        $this->assign("am_absenteeism_count",$am_absenteeism_count);
        //正常
        $am_d_normal_count = M('Attendance')->where($whereAttention)->where("second_result=2")->count();
        $this->assign("am_d_normal_count",$am_d_normal_count);
        //早退
        $am_leave_count = M('Attendance')->where($whereAttention)->where("second_result=4")->count();
        $this->assign("am_leave_count",$am_leave_count);
        //异常
        //上午异常
        $whereAmAttention["is_delete"] = 0;
        $whereAmAttention["add_time"] = array(array("gt",$beginToday),array("lt",$endToday));
        $whereAmAbnormal["first_result"] = array("neq",1);
        $whereAmAbnormal["second_result"] = array("neq",2);
        $whereAmAbnormal['_logic'] = 'or';
        $whereAmAttention['_complex'] = $whereAmAbnormal;
        $am_abnormal_count = M('Attendance')->where($whereAmAttention)->count();
        $this->assign("am_abnormal_count",$am_abnormal_count);
        //下午
        //正常
        $pm_t_normal_count = M('Attendance')->where($whereAttention)->where("third_result=1")->count();
        $this->assign("pm_t_normal_count",$pm_t_normal_count);
        //迟到
        $pm_late_count = M('Attendance')->where($whereAttention)->where("third_result=3")->count();
        $this->assign("pm_late_count",$pm_late_count);
        //旷工
        $pm_absenteeism_count = M('Attendance')->where($whereAttention)->where("third_result=5")->count();
        $this->assign("pm_absenteeism_count",$pm_absenteeism_count);
        //正常
        $pm_d_normal_count = M('Attendance')->where($whereAttention)->where("fourth_result=2")->count();
        $this->assign("pm_d_normal_count",$pm_d_normal_count);
        //早退
        $pm_leave_count = M('Attendance')->where($whereAttention)->where("fourth_result=4")->count();
        $this->assign("pm_leave_count",$pm_leave_count);
        //下午异常
        $wherePmAttention["is_delete"] = 0;
        $wherePmAttention["add_time"] = array(array("gt",$beginToday),array("lt",$endToday));
        $wherePmAbnormal["third_result"] = array("neq",1);
        $wherePmAbnormal["fourth_result"] = array("neq",2);
        $wherePmAbnormal['_logic'] = 'or';
        $wherePmAttention['_complex'] = $wherePmAbnormal;
        $pm_abnormal_count = M('Attendance')->where($wherePmAttention)->count();
        $this->assign("pm_abnormal_count",$pm_abnormal_count);
        $this->display('main');
    }
    //登录
    public function login(){

        if(IS_POST){
            $where['username'] = $where['mobile'] = $username = $_POST['form-username'];
            if(!$username || !$_POST['form-password']){
                $this->error("请输入完整信息");
            }
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
            //根据用户名获取用户信息
            $adminInfo = M('Admin')->where($map)->find();
            if(!$adminInfo){
                $this->error('管理员不存在');
            }else{
                if($adminInfo['status'] == 0){
                    $this->error('禁止登录');
                }
                $password = password($_POST['form-password'],$adminInfo['encrypt']);
                if($password==$adminInfo['password']){
                    //验证成功
                    cookie('admin_uid',''.$adminInfo['uid'].'');
                    session('admin_uid',''.$adminInfo['uid'].'');
                    session('admin_group_id',''.$adminInfo['group_id'].'');
                    session('admin_username',''.$adminInfo['username'].'');
                    session('admin_realname',''.$adminInfo['realname'].'');
                    session('admin_avatar',''.$adminInfo['avatar'].'');
                    // 更新登录时间
                    $updateData['last_date'] = time();
                    $updateData['last_ip'] = ip();
                    M('Admin')->where($where)->setField($updateData);
                    $this->success('登录成功',''.__MODULE__.'');
                }else{
                    $this->error('密码错误');
                }
            }
        }else{
            layout(false);
            $this->display('login');
        }
    }
    //退出
    public function logout(){
        cookie('admin_uid',null);
        session('admin_uid',null);
        session('admin_username',null);
        session('admin_realname',null);
        session('admin_group_id',null);
        session('admin_avatar',null);
        session('ADMIN_MENU_LIST',null);
        layout(false);
        // $this->success('退出成功');
        $this->redirect('Index/login');
    }
    public function verify(){
        $config = array(
            'fontSize'=>30,    // 验证码字体大小
            'length'=>4,     // 验证码位数
            'useNoise'=>false, // 关闭验证码杂点
        );
        ob_clean();
        $verify = new \Think\Verify($config);
        $verify->codeSet = '0123456789';
        $verify->entry(1);
    }
    public function getCurrentPos(){
        $menuId = I('menuId');
        echo self::currentPos($menuId);
        exit;
    }

    public function bigScreen(){
      $this->display('big-screen');
    }
}
