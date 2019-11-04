<?php
/**
 * Created by PhpStorm.
 * User: liwenlin
 * Date: 2018/9/10
 * Time: 下午2:28
 */

namespace App\Controller;
use Think\Controller;
class MapController extends BaseController
{
    function _initialize()
    {
        parent::_initialize();
    }
    public function latestTrack(){
        $add_time = date("Y-m-d",time());
        $startTime = strtotime($add_time);
        $endTime = strtotime($add_time) + 24*3600;
        $where['s.add_time'] = array('between',array($startTime,$endTime));
        $where["c.is_delete"] = 0;
        $where["m.is_delete"] = 0;
        $where["_string"] = "s.machine_imei = c.machine_imei and c.userid = m.userid";
        $subQuery = M("Site_log")->order('add_time desc')->buildSql();
        $list = M("Site_log")->table($subQuery.' s,__MACHINE__ c,__MEMBER__ m')->where($where)->field("s.*,c.machine_id,c.machine_imei,c.machine_name,c.userid,m.realname,m.job_number,m.mobile")->group("s.machine_imei")->select();
        // var_dump($result);
        if ($list ) {
            // $data=json_decode($list );
            foreach($list as $k =>$v){
                $list[$k]["add_time"] = date("Y-m-d H:i:s", $v["add_time"]);
            }
            $this->httpResponse(200,'数据获取成功',$list);
        }else{
            parent::httpResponse(400,'暂无数据');
        }
    }
    public function getMachineInfo(){
        if (IS_POST) {
           $where['m.machine_id'] = I('machine_id');
           $where['_string']='r.machine_id = m.machine_id';
           $result =M()
           ->table('__MACHINE__ m,__MEMBER__ r')
           ->field('m.machine_name,m.machine_id,m.machine_imei,r.*')
           ->where($where)
           ->find();
           if ($result) {
                parent::httpResponse(200,'数据获取成功',$result);
           }else{
                parent::httpResponse(400,'数据获取失败');
           }
        }
    }
    public function getCountInfo(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        //查员工总人数
        $where["is_delete"] = 0;
        $data["member_count"] = M('Member')->where($where)->count();
        //在岗人数
        $where["is_delete"] = 0;
        $where['update_time'] = array(array('gt',$beginToday),array('lt',$endToday),"and");
        $where['job_status'] = 1;
        $data["count_job"]=M('Member')->where($where)->count();
        //离岗人数
        $where["is_delete"] = 0;
        $where['update_time'] = array(array('gt',$beginToday),array('lt',$endToday),"and");
        $where['job_status'] = 0;
        $data["unline_count"]=M('Member')->where($where)->count();
        //请假人数
//        $whereQingjia["is_delete"] = 0;
//        $whereQingjia['leave_start_time'] = array('lt',time());
//        $whereQingjia['leave_end_time'] = array('gt',time());
//        $data["count_qingjia"]=M('Leave')->where($whereQingjia)->count();
//        //迟到人数
//        $whereChiDao["is_delete"] = 0;
//        $whereChiDao["leave_id"] = array("gt",0);
//        $whereChiDao['first_result'] =3;
//        $whereChiDao['first_time'] = array(array('lt',$beginToday),array('gt',$endToday),"and");
//        $data["count_chidao"]=M('Attendance')->where($whereChiDao)->count();
//        //旷工人数
//        $wherekg["is_delete"] = 0;
//        $wherekg['first_result'] =5;
//        $wherekg["leave_id"] = array("gt",0);
//        $wherekg['first_time'] = array(array('lt',$beginToday),array('gt',$endToday),"and");
//        $data["count_kg"]=M('Attendance')->where($wherekg)->count();
//        //早退人数
//        $wherezt["is_delete"] = 0;
//        $wherezt['second_result'] =4;
//        $wherezt["leave_id"] = array("gt",0);
//        $wherezt['second_time'] = array(array('lt',$beginToday),array('gt',$endToday),"and");
//        $data["count_zt"]=M('Attendance')->where($wherezt)->count();
        if ($data) {
            $this->httpResponse(200,'数据获取成功',$data);
        }else{
            parent::httpResponse(400,'数据获取失败');
        }
    }
    public function getMemberList(){
        $pageSize = I("pageSize") ? I("pageSize") : 20;
        $pageNum = I("pageNum") ? I("pageNum") : 1;
        $startCount = ($pageNum - 1) * $pageSize;
        $type = I("type");
        if($type == 1){
//            1 = 在岗员工
            $where["job_status"] = 1;
            $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
            $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
            $where['update_time'] = array(array('gt',$beginToday),array('lt',$endToday),"and");
        }
        if($type == 2){
            $where["job_status"] = 0;
        }
        $where["is_delete"] = 0;
        $list = M("Member")
            ->where($where)
            ->field("userid,realname,mobile,machine_id,job_number,reg_date,job_status,is_delete")
            ->order("job_status desc,reg_date asc")
            ->limit($startCount, $pageSize)
            ->select();
        if($list){
            foreach($list as $k=>$v ){
                $list[$k]["reg_date"]=date("Y-m-d H:i:s", $v["reg_date"]) ;
            }
            $this->httpResponse(200, "员工列表获取成功!", $list);
        } else {
            $msg = $pageNum == 1 ? "暂无员工" : "已无更多员工信息";
            $this->httpResponse(400, $msg);
        }
    }
    //获取员工详情
    public function getMemberDetail(){
        $where["userid"] = I("userid");
        $where["is_delete"] = 0;
        $memberInfo = M("Member")->where($where)->field("userid,job_number,realname,mobile,reg_date,job_status,update_time,is_delete,machine_id,sex")->find();
        if($memberInfo){
            //判断员工是否绑定设备
            if($memberInfo["machine_id"]>0){
                $whereMachine["machine_id"] = $memberInfo["machine_id"];
                $whereMachine["is_delete"] = 0;
                $machineInfo = M("Machine")->where($whereMachine)->field("machine_name,machine_imei,is_delete,area_id,schedules_id")->find();
                $memberInfo["machine_name"] = $machineInfo["machine_name"];
                $memberInfo["machine_imei"] = $machineInfo["machine_imei"];
                $memberInfo["area_id"] = $machineInfo["area_id"];
                $memberInfo["schedules_id"] = $machineInfo["schedules_id"];
            }
            $this->httpResponse(200, "获取员工信息成功!", $memberInfo);
        }else{
            $this->httpResponse(400, "获取员工信息失败!");
        }
    }
    //获取位置信息
    public function getMemberArea(){
        $where["m.userid"] = I("userid");
        $where["c.is_delete"] = 0;
        $where["m.is_delete"] = 0;
        $where["_string"] = "s.machine_imei = c.machine_imei and c.userid = m.userid";
        $info = M("Site_log")->table("__SITE_LOG__ s,__MACHINE__ c,__MEMBER__ m")->where($where)->field("s.*,c.machine_id,c.machine_imei,c.machine_name,c.userid,m.job_number,m.mobile")->order("s.add_time desc")->limit(1)->find();
        if($info){
            $info["add_time"] = date("Y-m-d H:i:s", $info["add_time"]);
            $this->httpResponse(200, "获取位置成功!", $info);
        }else{
            $this->httpResponse(400, "获取位置失败!");
        }
    }
    //获取考勤信息
    public function getAttendanceInfo(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $where['a.add_time'] = array(array('gt',$beginToday),array('lt',$endToday),"and");
        $where["m.userid"] = I("userid");
        $where["a.is_delete"] = $where["m.is_delete"] = 0;
        $where["_string"] = "a.userid = m.userid";
        $info = M("Attendance")->table("__ATTENDANCE__ a,__MEMBER__ m")->where($where)->field("a.*,m.realname,m.job_number,m.mobile")->order("a.add_time desc")->limit(1)->find();
        if($info){
            if($info["first_time"] >0){
                  $info["first_time"] = date("Y-m-d H:i:s", $info["first_time"]);
            }else{
                $info["first_time"] = "缺卡";
            }
            if($info["second_time"] >0){
                  $info["second_time"] = date("Y-m-d H:i:s", $info["second_time"]);
            }else{
                $info["second_time"] = "缺卡";
            }
            if($info["third_time"] >0){
                $info["third_time"] = date("Y-m-d H:i:s", $info["third_time"]);
            }else{
                $info["third_time"] = "缺卡";
            }
            if($info["fourth_time"] >0){
                $info["fourth_time"] = date("Y-m-d H:i:s", $info["fourth_time"]);
            }else{
                $info["fourth_time"] = "缺卡";
            }
            $this->httpResponse(200, "获取考勤信息成功!", $info);
        }else{
            $this->httpResponse(400, "暂无考勤信息!");
        }
    }
    //获取报警信息列表
    public function getWarningList(){
        $pageSize = I("pageSize") ? I("pageSize") : 20;
        $pageNum = I("pageNum") ? I("pageNum") : 1;
        $startCount = ($pageNum - 1) * $pageSize;
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $where['w.add_time'] = array(array('gt',$beginToday),array('lt',$endToday),"and");
        $where["m.is_delete"] = $where["c.is_delete"]= 0;
        $where["w.type"] = array("gt",1);
        $where["_string"] = "m.userid = w.uid and m.machine_id = c.machine_id";
        $list = M("Warning_message")
            ->table("__WARNING_MESSAGE__ w,__MEMBER__ m,__MACHINE__ c")
            ->where($where)
            ->field("w.*,m.userid,m.realname,m.mobile,m.machine_id,m.job_number,c.machine_imei,c.machine_name")
            ->order("w.add_time desc")
            ->limit($startCount, $pageSize)
            ->select();
        if($list){
            foreach($list as $k=>$v ){
                $list[$k]["add_time"]=date("Y-m-d H:i:s", $v["add_time"]) ;
            }
            $this->httpResponse(200, "报警列表获取成功!", $list);
        } else {
            $msg = $pageNum == 1 ? "暂无报警" : "已无更多报警信息";
            $this->httpResponse(400, $msg);
        }
    }
    //获取报警信息详情
    public function getWarningDetail(){
        $where["w.id"] = I("id");
        $where["m.is_delete"] = $where["c.is_delete"]= 0;
        $where["_string"] = "m.userid = w.uid and m.machine_id = c.machine_id";
        $info = M("Warning_message")
            ->table("__WARNING_MESSAGE__ w,__MEMBER__ m,__MACHINE__ c")
            ->where($where)
            ->field("w.*,m.userid,m.realname,m.mobile,m.machine_id,m.job_number,c.machine_imei,c.machine_name,c.machine_status")
            ->find();
        if($info){
            $info["add_time"]=date("Y-m-d H:i:s", $info["add_time"]) ;
            $this->httpResponse(200, "报警信息获取成功!", $info);
        } else {
            $this->httpResponse(400, "获取报警详情失败");
        }
    }
}