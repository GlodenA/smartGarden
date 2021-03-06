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
    //地图设备展示
    public function latestTrack(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $where["c.login_time"] = array("gt",$beginToday);
        $where["c.is_delete"] = 0;
        $where["m.is_delete"] = 0;
        $list = M("Machine")->alias("c")->join("left join __MEMBER__ m on c.userid = m.userid")->field("c.*,m.realname,m.job_number,m.mobile,m.position,m.parent_id")->where($where)->select();
        if($list){
            foreach($list as $k =>$v){
                if($v["parent_id"]>0){
                    $list[$k]["parent_name"] = M("Member")->where(array("userid"=>$v["parent_id"],"is_delete"=>0))->getField("realname");
                }else{
                    $list[$k]["parent_name"] ="null";
                }
                if($v["position"]>0){
                    $list[$k]["position_name"] = M("Member_position")->where(array("id"=>$v["position"],"is_delete"=>0))->getField("name");
                }else{
                    $list[$k]["position_name"] ="null";
                }
                $list[$k]["lat"] = $v["b_lat"];
                $list[$k]["lon"] = $v["b_lon"];
                $list[$k]["add_time"] = date("Y-m-d H:i:s", $v["server_utc_time"]);
            }
            $this->httpResponse(200,'数据获取成功',$list);
        }else{
            parent::httpResponse(400,'暂无数据');
        }
    }
    //获取设备信息
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
    //员工列表
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
                $list[$k]["reg_date"]=date("Y-m-d H:i:s", $v["reg_date"]);
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
    //获取设备位置信息
    public function getMemberArea(){
        $where["c.machine_id"] = I("machine_id");
        $where["c.is_delete"] = 0;
        $where["m.is_delete"] = 0;
        $info = M("Machine")->alias("c")->join("left join __MEMBER__ m on c.userid = m.userid")->field("c.*,m.realname,m.job_number,m.mobile,m.position,m.parent_id")->where($where)->find();
        if($info){
            if($info["parent_id"]>0){
                $info["parent_name"] = M("Member")->where(array("userid"=>$info["parent_id"],"is_delete"=>0))->getField("realname");
            }else{
                $info["parent_name"] ="";
            }
            if($info["position"]>0){
                $info["position_name"] = M("Member_position")->where(array("id"=>$info["position"],"is_delete"=>0))->getField("name");
            }else{
                $info["position_name"] ="";
            }
            $info["lat"] = $info["b_lat"];
            $info["lon"] = $info["b_lon"];
            $info["add_time"] = date("Y-m-d H:i:s",$info["server_utc_time"]);
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
                $info["first_time"] = "未打卡";
            }
            if($info["second_time"] >0){
                  $info["second_time"] = date("Y-m-d H:i:s", $info["second_time"]);
            }else{
                $info["second_time"] = "未打卡";
            }
            if($info["third_time"] >0){
                $info["third_time"] = date("Y-m-d H:i:s", $info["third_time"]);
            }else{
                $info["third_time"] = "未打卡";
            }
            if($info["fourth_time"] >0){
                $info["fourth_time"] = date("Y-m-d H:i:s", $info["fourth_time"]);
            }else{
                $info["fourth_time"] = "未打卡";
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
            ->field("w.*,m.userid,m.realname,m.mobile,m.machine_id,m.job_number,c.area_id,c.machine_imei,c.machine_name,c.machine_status")
            ->find();
        if($info){
            $info["add_time"]=date("Y-m-d H:i:s", $info["add_time"]) ;
            $this->httpResponse(200, "报警信息获取成功!", $info);
        } else {
            $this->httpResponse(400, "获取报警详情失败");
        }
    }
    //获取设备轨迹
    public function getMachineOrbit(){
        //获取设备信息
        header("Content-Type:text/html;charset=utf-8");
        $type = I("type");
        $machineId = I("machine_id");
        $machineInfo = M('Machine')->where(array("machine_id"=>$machineId,"is_delete"=>0))->find();
        $searchTime = I("searchTime");
        if($searchTime){
            $year = $searchTime;
        }else{
            $year = date("Y-m-d",time());
        }
        //3.根据设备信息中的绑定班组获取班组信息
        $whereSchedules["schedules_id"] = $machineInfo["schedules_id"];
        $whereSchedules["is_show"] = 1;
        $whereSchedules["is_delete"] = 0;
        $schedulesInfo = M("Schedules_setting")->where($whereSchedules)->find();
        if($schedulesInfo) {
            if ($schedulesInfo["time_id"]) {
                //获取时间段信息
                $timeIdArr = explode(",", $schedulesInfo["time_id"]);
                //两个时间段
                $timeInfoAm = M("Schedules_time")->where(array("id" => $timeIdArr[0], "is_show" => 1, "is_delete" => 0))->find();
                $timeInfoPm = M("Schedules_time")->where(array("id" => $timeIdArr[1], "is_show" => 1, "is_delete" => 0))->find();
                if ($timeInfoAm && $timeInfoPm) {
                    //5.查询考勤设置
                    $attendanceSetInfo = M("Attendance_setting")->where(array("id" => 1))->find();
                    //拼接时间段
                    $timeInfoAmStart = strtotime($year.' '.$timeInfoAm["start_time"])-$attendanceSetInfo["error_time"];
                    $timeInfoAmEnd = strtotime($year.' '.$timeInfoAm["end_time"])+$attendanceSetInfo["error_time"];
                    $timeInfoPmStart = strtotime($year.' '.$timeInfoPm["start_time"])-$attendanceSetInfo["error_time"];
                    $timeInfoPmEnd = strtotime($year.' '.$timeInfoPm["end_time"])+$attendanceSetInfo["error_time"];
                    $timeStart1 = date("Y-m-d",$timeInfoAmStart)."T".date("H:i:s",$timeInfoAmStart);
                    $timeStart2 = date("Y-m-d",$timeInfoPmStart)."T".date("H:i:s",$timeInfoPmStart);
                    //查询考勤信息
//                    $beginToday = strtotime($year);
//                    $endToday = $beginToday+24*60*60;
//                    $whereAttendance["a.userid"] = $machineInfo["userid"];
//                    $whereAttendance["a.is_delete"] = 0;
//                    $whereAttendance["a.machine_id"] = $machineInfo["machine_id"];
//                    $whereAttendance["m.is_delete"] = 0;
//                    $whereAttendance["a.add_time"] = array('between',array($beginToday,$endToday));
//                    $whereAttendance["_string"] = "m.userid = a.userid";
//                    $attendanceInfo = M("Attendance")->table("__ATTENDANCE__ a,__MEMBER__ m")->where($whereAttendance)->find();
//                    if($attendanceInfo){
//                        if($attendanceInfo["second_time"]>0){
//                            $timeEmd1 = date("Y-m-d",$attendanceInfo["second_time"])."T".date("H:i:s",$attendanceInfo["second_time"]);
//                        }else{
//                            $timeEmd1 = date("Y-m-d",$timeInfoAmEnd)."T".date("H:i:s",$timeInfoAmEnd);
//                        }
//                        if($attendanceInfo["fourth_time"]>0){
//                            $timeEmd2 = date("Y-m-d",$attendanceInfo["fourth_time"])."T".date("H:i:s",$attendanceInfo["fourth_time"]);
//                        }else{
//                            $timeEmd2 = date("Y-m-d",$timeInfoPmEnd)."T".date("H:i:s",$timeInfoPmEnd);
//                        }
//                    }else{
                        $timeEmd1 = date("Y-m-d",$timeInfoAmEnd)."T".date("H:i:s",$timeInfoAmEnd);
                        $timeEmd2 = date("Y-m-d",$timeInfoPmEnd)."T".date("H:i:s",$timeInfoPmEnd);
//                    }
                    //请求轨迹
                    $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
                    if($type == 0 || $type == 1){
                        $paramOne["DeviceID"] = $machineInfo["mid"];
                        $paramOne["Start"] =$timeStart1;
                        $paramOne["End"] = $timeEmd1;
                        $paramOne["TimeZone"] = "8:00";
                        $paramOne["ShowLBS"] = 0;
                        $result1 = $client->GetDevicesHistory($paramOne);
                        if ($result1) {
                            $result2 = object_to_array($result1);
                            $jsonstr1 = $result2["GetDevicesHistoryResult"];
                            $jsonstr2 = json_replace_key($jsonstr1);
                            $jsonArr1 = json_decode("[" . $jsonstr2 . "]", true);
                            $listOne = $jsonArr1[0]["devices"];
                            if($listOne){
                                foreach ($listOne as $k => $v) {
                                    $listOne[$k]["machine_imei"] = $machineInfo["machine_imei"];
                                }
                            }
                        }
                    }
                    if($type ==2 || $type == 0){
                        $paramTwo["DeviceID"] = $machineInfo["mid"];
                        $paramTwo["Start"] =$timeStart2;
                        $paramTwo["End"] = $timeEmd2;
                        $paramTwo["TimeZone"] = "8:00";
                        $paramTwo["ShowLBS"] = 0;
                        $result3 = $client->GetDevicesHistory($paramTwo);
                        if ($result3) {
                            $result4 = object_to_array($result3);
                            $jsonstr3 = $result4["GetDevicesHistoryResult"];
                            $jsonstr4 = json_replace_key($jsonstr3);
                            $jsonArr2 = json_decode("[" . $jsonstr4 . "]", true);
                            $listTwo = $jsonArr2[0]["devices"];
                            if($listTwo){
                                foreach ($listTwo as $k => $v) {
                                    $listTwo[$k]["machine_imei"] = $machineInfo["machine_imei"];
                                }
                            }
                        }
                    }
                }
            }
        }
        if($listOne || $listTwo){
            $ret["code"] = 200;
            $ret["info"] = "获取坐标数据成功";
            $ret["listOne"] = $listOne;
            $ret["listTwo"] = $listTwo;
        }else{
            $ret["code"] = 400;
            $ret["info"] = "暂无坐标数据";
        }
        $this->ajaxReturn($ret);
    }
}
