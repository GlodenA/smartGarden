<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Log;
class AaController extends Controller{

    //初始化处理
    public function attendanceInitialize(){
        \Think\Log::write("bcdefg");
        if(date('H:i',time()) == '23:00') {
            \Think\Log::write("abf");
            M("Member")->where(array('is_delete' => 0))->data(array("job_status" =>2,"update_time"=>time()))->save();
            M("Attendance")->where(array("is_delete" =>0, "num" => 0))->data(array("first_result"=>5,"third_result"=>5,"num"=>4,'update_time'=>time()))->save();
        }
        if(date('H:i',time()) == '15:00') {
            \Think\Log::write("abd");
            $whereMember["m.is_delete"] = $whereMember["c.is_delete"] = 0;
            $whereMember["m.machine_id"] = array("gt",0);
            $whereMember["_string"] = "m.machine_id = c.machine_id";
            $memberList = M("Member")->table("__MEMBER__ m,__MACHINE__ c")->where($whereMember)->field("m.userid,c.machine_id")->select();
            if($memberList){
                foreach($memberList as $k =>$v){
                    //判断今天是否已经插入考勤
                    $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
                    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
                    $whereAttendance["add_time"] = array('between',array($beginToday,$endToday));
                    $attendanceInfo = M("Attendance")->where($whereAttendance)->find();
                    if(!$attendanceInfo){
                        M("Attendance")->data(array('add_time'=>time(),'update_time'=>time(),'userid'=>$v["userid"],'machine_id'=>$v["machine_id"]))->add();
                    }
                }
            }
            //删除七天前的位置日志
            $whereLoc["add_time"] = array("lt",time()-7*24*60*60);
            M("Loc_log")->where($whereLoc)->delete();
        }
    }
    //定时任务
    public function getMachineInfoList() {
        \Think\Log::write("abcdefg");
        libxml_disable_entity_loader(false);
        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
        $param["UserID"] ="3222";
        $param["isFirst"] ="True";
        $param["TimeZones"] ="8:00";
        $param["DeviceID"] ="0";
        $result1 = $client->GetDevicesByUserID($param);
        if($result1) {
            $result2 = object_to_array($result1);
            $jsonstr1 = $result2["GetDevicesByUserIDResult"];
            $jsonstr2 = json_replace_key($jsonstr1);
            $jsonArr1 = json_decode("[".$jsonstr2."]",true);
            $list1 = $jsonArr1[0]["devices"];
            if($list1){
                foreach($list1 as $k => $v){
                    if($v["groupID"] == 153){
                        $where[$k]["mid"] = $v["id"];
                        $where[$k]["machine_imei"] = $v["sn"];
                        $where[$k]["is_delete"] = 0;
                        $isIn[$k] =M("Machine")->where($where[$k])->find();
                        $electricityArr = explode("-",$v["dataContext"]);
                        $data["electricity"] =$electricityArr[0];
                        $data["machine_status"] = $v["status"];
                        //1.进行设备处理
                        //1.1添加或更新设备信息
                        if(!$isIn[$k]){
                            $data["machine_name"] = $v["name"];
                            $data["machine_imei"] = $v["sn"];
                            $data["mid"] = $v["id"];
                            $data["add_time"] = $data["update_time"] = time();
                            M("Machine")->data($data)->add();
                        }else{
                            $data["update_time"] = time();
                            M("Machine")->data($data)->save();
                        }
                        //1.2设备位置上传记录
                        $locData["machine_imei"] = $v["sn"];
                        $locData["electricity"] =$electricityArr[0];
                        $locData["machine_status"] = $v["status"];
                        $locData["server_utc_date"] = $v["serverUtcDate"];
                        $locData["device_utc_date"] = $v["deviceUtcDate"];
                        $locData["baidu_lat"] = $v["baiduLat"];
                        $locData["baidu_lng"] = $v["baiduLng"];
                        $locData["data_context"] = $v["dataContext"];
                        $locData["is_stop"] = $v["isStop"];

                        $locData["stop_time_minute"] = $v["stopTimeMinute"];

                        $locData["status"] = $v["status"];

                        $locData["add_time"] = time();

                        M("Loc_log")->data($locData)->add();

                        //1.3判断设备反馈位置是否准确
                        if($v["dataType"] != 3){
                            //定位信息不准确

                            continue;
                        }
                        if($v["baiduLat"]<34 || $v["baiduLat"] >40){

                            //定位信息不准确

                            continue;

                        }

                        //2.获取设备信息

                        $machineInfo = M("Machine")->where($where[$k])->find();

                        //3.根据设备信息中的绑定班组获取班组信息

                        $whereSchedules["schedules_id"] = $machineInfo["schedules_id"];

                        $whereSchedules["is_show"] = 1;

                        $whereSchedules["is_delete"] = 0;

                        $schedulesInfo = M("Schedules_setting")->where($whereSchedules)->find();

                        if(!$schedulesInfo){

                            continue;

                        }

                        //4.根据班组信息做出相应判断

                        //4.1判断是否设置工作日和时间段

                        if(!$schedulesInfo["work_day"]){

                            //未设置工作日

                            continue;

                        }

                        if(!$schedulesInfo["time_id"]){

                            //未设置时间段

                            continue;

                        }

                        //4.2判断今天是否是工作日

                        $todayWeek = fnRetWeek();

                        $todayWeekArr = explode(",",$schedulesInfo["work_day"]);

                        if(in_array($todayWeek,$todayWeekArr) == false){

                            //今天不是工作日

                            continue;

                        }

                        //4.3获取时间段信息

                        $year = date("Y-m-d",time());

                        $timeIdArr = explode(",",$schedulesInfo["time_id"]);

                        //两个时间段

                        $timeInfoAm = M("Schedules_time")->where(array("id"=>$timeIdArr[0],"is_show"=>1,"is_delete"=>0))->find();

                        if(!$timeInfoAm){

                            //对应时间段已删除

                            continue;

                        }

                        $timeInfoPm = M("Schedules_time")->where(array("id"=>$timeIdArr[1],"is_show"=>1,"is_delete"=>0))->find();

                        if(!$timeInfoPm){

                            //对应时间段已删除

                            continue;

                        }

                        //拼接时间段

                        $timeInfoAmStart = strtotime($year.' '.$timeInfoAm["start_time"]);

                        $timeInfoAmEnd = strtotime($year.' '.$timeInfoAm["end_time"]);

                        $timeInfoPmStart = strtotime($year.' '.$timeInfoPm["start_time"]);

                        $timeInfoPmEnd = strtotime($year.' '.$timeInfoPm["end_time"]);

                        //5.查询考勤设置

                        $attendanceSetInfo = M("Attendance_setting")->where(array("id"=>1))->find();

                        //6.判断是否在工作时间内

                        $timeStart1 = $timeInfoAmStart-$attendanceSetInfo["error_time"];

                        $timeEmd1 = $timeInfoAmEnd+$attendanceSetInfo["error_time"];

                        $timeStart2 = $timeInfoPmStart-$attendanceSetInfo["error_time"];

                        $timeEmd2 = $timeInfoPmEnd+$attendanceSetInfo["error_time"];

                        if(time() < $timeStart1 || (time() >$timeEmd1 && time() < $timeStart2) || time() > $timeEmd2){

                            //设备不在工作时间内

                            continue;

                        }

                        if(time() <$timeEmd1 || time() == $timeEmd1){

                            //上午

                            $siteData["type"] = 1;

                        }

                        if(time()>$timeStart2 || time() == $timeStart2){

                            //下午

                            $siteData["type"] = 2;

                        }

                        //7.判断是否拥有考勤

                        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));

                        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

                        //7.1判断今日考勤是否已经结束

                        if($machineInfo["userid"]){

                            $emplyeeInfo = M("Member")->where(array("userid"=>$machineInfo["userid"],"is_delete"=>0))->field("userid,realname")->find();

                            if($emplyeeInfo){

                                $whereAttendance["userid"] = $machineInfo["userid"];

                                $whereAttendance["machine_id"] = $machineInfo["machine_id"];

                                $whereAttendance["is_delete"] = 0;

                                $whereAttendance["add_time"] = array('between',array($beginToday,$endToday));

                                $attendanceInfo = M("Attendance")->where($whereAttendance)->find();

                                if($attendanceInfo){

                                    if($attendanceInfo["num"] == 2){

                                        //已经打卡两次并且打卡时间在上午下班时间内，上午考勤结束

                                        if(time()<$timeEmd1){

                                            continue;

                                        }

                                    }

                                    if($attendanceInfo["num"] == 4){

                                        //已经打卡四次，今日考勤结束

                                        continue;

                                    }

                                }

                            }

                        }

                        //位置信息

                        $siteData["machine_id"] = $machineInfo["machine_id"];

                        $siteData["machine_imei"] = $v["sn"];

                        $siteData["lat"] = $v["baiduLat"];

                        $siteData["lon"] = $v["baiduLng"];

                        $siteData["add_time"] = time();

                        //8.判断设备是否绑定区域

                        if(!$machineInfo["area_id"]){

                            //未绑定区域

                            $siteData["status"] = 0;

                            M("Site_log")->data($siteData)->add();

                            continue;

                        }

                        //8.1获取设备绑定区域信息

                        $areaInfo = M("Area_map")->where(array("id"=>$machineInfo["area_id"],"is_show"=>1,"is_delete"=>0))->find();

                        //8.2判断区域是否存在

                        if(!$areaInfo){

                            //关联区域不存在

                            $siteData["status"] = 0;

                            M("Site_log")->data($siteData)->add();

                            continue;

                        }

                        //8.3判断设备是否在区域内

                        $areaInfoStr = $areaInfo["coordinate"];

                        $areaListStr = "[".$areaInfoStr."]";

                        $areaListArr = json_decode($areaListStr,true);

                        //判断设备是否在围栏内并上传其位置

                        $point=array(

                            'lon'=>$v["baiduLng"],

                            'lat'=>$v["baiduLat"]

                        );

                        $isInPoint = is_point_in_polygon($point, $areaListArr);

                        if($isInPoint){

                            //在区域内

                            $siteData["status"] = 1;

                            M("Site_log")->data($siteData)->add();

                        }else{

                            //不在区域内

                            $siteData["status"] = 0;

                            M("Site_log")->data($siteData)->add();

                        }

                        //9.判断设备是否绑定员工

                        if(!$machineInfo["userid"]){

                            //设备未绑定员工

                            continue;

                        }

                        //9.1获取员工信息

                        $emplyeeInfo = M("Member")->where(array("userid"=>$machineInfo["userid"],"is_delete"=>0))->field("userid,realname")->find();

                        if(!$emplyeeInfo){

                            //员工已被删除

                            continue;

                        }

                        //9.2判断是否拥有考勤

                        $whereAttendance["userid"] = $machineInfo["userid"];

                        $whereAttendance["machine_id"] = $machineInfo["machine_id"];

                        $whereAttendance["is_delete"] = 0;

                        $whereAttendance["add_time"] = array('between',array($beginToday,$endToday));

                        $attendanceInfo = M("Attendance")->where($whereAttendance)->find();

                        if(!$attendanceInfo){

                            //没有考勤数据，创建新的考勤数据

                            $attendanceData["userid"] = $machineInfo["userid"];

                            $attendanceData["machine_id"] = $machineInfo["machine_id"];

                            $attendanceData["add_time"] = $attendanceData["update_time"] = time();

                            $attendanceInfo["id"] = M("Attendance")->data($attendanceData)->add();

                        }

                        //说明有考勤数据并且考勤还未完成

                        //10.在实际工作时间内，进行报警判断

                        if((time()>$timeInfoAmStart && time() < $timeInfoAmEnd) || (time()>$timeInfoPmStart && time() < $timeInfoPmEnd)){

                            //10.1低电量报警判断

                            if($electricityArr[0] < $attendanceSetInfo["electric_quantity"]){

                                //需要低电量报警，查询最新报警时间

                                $whereElectricity["machine_id"] = $machineInfo["machine_id"];

                                $whereElectricity["type"] = 3;

                                $electricityInfo = M("Warning_message")->where($whereElectricity)->order("add_time desc")->find();

                                $electricityData["uid"] = $machineInfo["userid"];

                                $electricityData["machine_id"] = $machineInfo["machine_id"];

                                $electricityData["add_time"] = time();

                                $electricityData["type"] = 3;

                                if($electricityInfo){

                                    //已经报警过，判断报警间隔

                                    $diffElectric = time() - $electricityInfo["add_time"];

                                    if($diffElectric > $attendanceSetInfo["electric_time"]){

                                        //距离上次低电量报警已超所设置时间，再次触发报警

                                        M("Warning_message")->data($electricityData)->add();

                                    }

                                }else{

                                    //还未进行低电量报警，触发报警

                                    M("Warning_message")->data($electricityData)->add();

                                }

                            }

                            //10.2长时间静止报警

                            if($v["isStop"] == 1){

                                if($v["stopTimeMinute"] > $attendanceSetInfo["still_time"]){

                                    //需要静止报警，查询最新报警时间

                                    $whereStill["machine_id"] = $machineInfo["machine_id"];

                                    $whereStill["type"] = 7;

                                    $stillInfo = M("Warning_message")->where($whereStill)->order("add_time desc")->find();

                                    $stillData["uid"] = $machineInfo["userid"];

                                    $stillData["machine_id"] = $machineInfo["machine_id"];

                                    $stillData["add_time"] = time();

                                    $stillData["type"] = 7;

                                    if($stillInfo){

                                        //已经报警过，判断报警间隔

                                        $diffElectric = time() - $stillInfo["add_time"];

                                        if($diffElectric > $attendanceSetInfo["still_time_interval"]){

                                            //距离上次静止报警已超所设置时间，再次触发报警

                                            M("Warning_message")->data($stillData)->add();

                                        }

                                    }else{

                                        //还未进行静止报警，触发报警

                                        M("Warning_message")->data($stillData)->add();

                                    }

                                }

                            }

                        }

                        //11.考勤打卡

                        //11.1报警判断

                        $wherePolice["machine_id"] = $machineInfo["machine_id"];

                        $wherePolice["type"] = array("lt",3);

                        $wherePolice["add_time"] = array("gt",$beginToday);

                        $wherePolice["end_time"] = array("lt",1);

                        $policeInfo = M("Warning_message")->where($wherePolice)->order("add_time desc")->find();

                        //12.设备进入区域

                        if($isInPoint){

                            //12.1判断是不是在上午允许上班打卡时间内

                            $timeAmStart = $timeInfoAmStart-$attendanceSetInfo['error_time'];

                            $timeAmEnd = $timeInfoAmStart+$attendanceSetInfo['late_time'];

                            if(time()<$timeAmEnd && time() > $timeAmStart){

                                //在上午上班打卡允许时间内（大于允许误差时间小于允许迟到时间）

                                if($attendanceInfo["num"] > 0){

                                    //说明已经进行上班打卡处理（可能打卡之后员工离开区域，即上班时间与允许迟到时间内离开区域）

                                    if ($policeInfo) {

                                        //闭合报警记录更新员工状态

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>time()))->save();

                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>1,"update_time"=>time()))->save();

                                    }

                                    //说明员工进入区域后，一直未曾离开

                                    continue;

                                }else{

                                    //员工上班打卡

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>1,"update_time"=>time()))->save();

                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('first_result'=>1,'first_time'=>time(),'update_time'=>time()))->save();

                                }

                                continue;

                            }

                            //12.2判断在上午上班时间内（若还未打卡，则说明员工迟到或旷工）

                            if(time()>($timeAmEnd-1) && time() < $timeInfoAmEnd){

                                //在上午上班时间内

                                if($attendanceInfo["num"] > 0){

                                    //说明已经进行上班打卡处理

                                    if ($policeInfo) {

                                        //闭合报警记录更新员工状态

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>time()))->save();

                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>1,"update_time"=>time()))->save();

                                    }

                                    if($attendanceInfo["first_result"] == 5 && !$attendanceInfo["first_time"]){

                                        //说明其上班时间未到，超出旷工时间，更新旷工时间

                                        M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('first_time'=>time(),'update_time'=>time()))->save();

                                    }

                                    continue;

                                }else{

                                    //没打过卡,先插入离岗记录，判断旷工或迟到

                                    //判断有无报警记录

                                    if ($policeInfo) {

                                        //有最新的一条报警记录

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>time()))->save();

                                    }else{

                                        //插入离岗记录

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>$timeInfoAmStart,'end_time'=>time()))->add();

                                    }

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>1,"update_time"=>time()))->save();

                                    $diffTime = time() - $timeInfoAmStart;

                                    if($attendanceSetInfo['absenteeism_time'] < $diffTime){

                                        //时间差大于允许旷工时间,判定旷工,更新员工状态,更新考勤数据，触发旷工报警

                                        M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('first_time'=>time(),"first_result"=>5,"num"=>1,'update_time'=>time()))->save();

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                        continue;

                                    }else{

                                        //时间差大于允许迟到时间,判定迟到,更新考勤数据，触发迟到报警

                                        M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('first_time'=>time(),"first_result"=>3,"num"=>1,'update_time'=>time()))->save();

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>4))->add();

                                        continue;

                                    }

                                }

                            }

                            //12.3上午下班时间，下班打卡判断

                            if((time()>$timeInfoAmEnd || time() == $timeInfoAmEnd) && time()<$timeEmd1){

                                if($attendanceInfo["num"] > 1){

                                    //上午考勤已经结束

                                    continue;

                                }elseif($attendanceInfo["num"] == 1){

                                    //上午下班打卡，早退判断

                                    if ($policeInfo) {

                                        //有最新的一条报警记录

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>$timeInfoAmEnd))->save();

                                        $diffTime = $timeInfoAmEnd - $policeInfo["add_time"];

                                        if($attendanceSetInfo['leave_time'] < $diffTime) {

                                            //超出允许早退时间,判定早退,修改员工状态

                                            M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('second_time'=>$policeInfo["add_time"],'update_time'=>time(),'second_result'=>4,"num"=>2))->save();

                                            M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>6))->add();

                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                            continue;

                                        }

                                    }

                                    //正常下班打卡

                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('second_time'=>time(),'update_time'=>time(),'second_result'=>2,"num"=>2))->save();

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                    continue;

                                }else{

                                    //上午未打过卡

                                    if ($policeInfo) {

                                        //有最新的一条报警记录

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>$timeInfoAmEnd))->save();

                                    }else{

                                        //插入离岗记录

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>$timeInfoAmStart,'end_time'=>$timeInfoAmEnd))->add();

                                    }

                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('update_time'=>time(),'first_result'=>5,"num"=>2))->save();

                                    M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                    continue;

                                }

                                continue;

                            }

                            //12.4判断是不是在下午允许上班打卡时间内

                            $timePmStart = $timeInfoPmStart-$attendanceSetInfo['error_time'];

                            $timePmEnd = $timeInfoPmStart+$attendanceSetInfo['late_time'];

                            if(time()<$timePmEnd && time() >$timePmStart){

                                //在下午上班打卡允许时间内（大于允许误差时间小于允许迟到时间）

                                if ($policeInfo) {

                                    if($policeInfo["add_time"]<$timeInfoAmEnd){

                                        //闭合报警记录

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>$timeInfoAmEnd))->save();

                                    }else{

                                        //闭合报警记录

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>time()))->save();

                                    }

                                }

                                if($attendanceInfo["num"] > 2){

                                    //说明已经进行上班打卡处理（可能打卡之后员工离开区域，即上班时间与允许迟到时间内离开区域）

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>1,"update_time"=>time()))->save();

                                    continue;

                                }else{

                                    //员工上班打卡

                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('third_time'=>time(),"third_result"=>1,"num"=>3,'update_time'=>time()))->save();

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>1,"update_time"=>time()))->save();

                                    continue;

                                }

                                continue;

                            }

                            //12.5在下午上班时间内（大于允许迟到时间小于下午下班时间）

                            if(time()>($timePmEnd-1) && time() < $timeInfoPmEnd){

                                if ($policeInfo) {

                                    if($policeInfo["add_time"]<$timeInfoAmEnd){

                                        //闭合报警记录

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>$timeInfoAmEnd))->save();

                                    }else{

                                        //闭合报警记录

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>time()))->save();

                                    }

                                }

                                //在下午上班时间内

                                if($attendanceInfo["num"] > 2){

                                    //说明已经进行上班打卡处理

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>1,"update_time"=>time()))->save();

                                    if($attendanceInfo["third_result"] == 5 && !$attendanceInfo["third_time"]){

                                        //说明其上班时间未到，超出旷工时间，更新旷工时间

                                        M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('third_time'=>time(),'num'=>3,'update_time'=>time()))->save();

                                    }

                                    continue;

                                }else{

                                    //没打过卡,先插入离岗记录，判断旷工或迟到

                                    //判断有无报警记录

                                    if (!$policeInfo) {

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>$timeInfoPmStart,'end_time'=>time()))->add();

                                    }

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>1,"update_time"=>time()))->save();

                                    $diffTime = time() - $timeInfoPmStart;

                                    if($attendanceSetInfo['absenteeism_time'] < $diffTime){

                                        //时间差大于允许旷工时间,判定旷工,更新员工状态,更新考勤数据

                                        M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('third_time'=>time(),"third_result"=>5,"num"=>3,'update_time'=>time()))->save();

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                        continue;

                                    }else{

                                        //迟到处理

                                        M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('third_time'=>time(),"third_result"=>3,"num"=>3,'update_time'=>time()))->save();

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>4))->add();

                                        continue;

                                    }

                                }

                            }

                            //12.6下午下班时间内，下班打卡判断

                            if((time()>$timeInfoPmEnd || time() == $timeInfoPmEnd) && time()<$timeEmd2){

                                if($attendanceInfo["num"] > 3){

                                    //下午考勤已经结束

                                    continue;

                                }elseif($attendanceInfo["num"] == 3){

                                    //说明已经打过下午上班卡，下午下班打卡，早退判断

                                    if ($policeInfo) {

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>$timeInfoPmEnd))->save();

                                        $diffTime = $timeInfoPmEnd - $policeInfo["add_time"];

                                        if($attendanceSetInfo['leave_time'] < $diffTime) {

                                            //超出允许早退时间,判定早退,修改员工状态

                                            M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('fourth_time'=>$policeInfo["add_time"],'update_time'=>time(),'fourth_result'=>4,"num"=>4))->save();

                                            M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>6))->add();

                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                            continue;

                                        }

                                    }

                                    //正常下班打卡

                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('fourth_time'=>time(),'update_time'=>time(),'fourth_result'=>2,"num"=>4))->save();

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                    continue;

                                }else{

                                    //下午未打过卡，即设备上传信号时已经是下班点

                                    if ($policeInfo) {

                                        if($policeInfo["add_time"]<($timeInfoPmStart-1)){

                                            //闭合上午报警记录

                                            M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>$timeInfoAmEnd))->save();

                                        }else{

                                            //闭合下午报警记录

                                            M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time"=>$timeInfoPmEnd))->save();

                                        }

                                    }else{

                                        //插入离岗记录

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>$timeInfoPmStart,'end_time'=>$timeInfoPmEnd))->add();

                                    }

                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('update_time'=>time(),'third_result'=>5,"num"=>4))->save();

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                    M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                    continue;

                                }

                                continue;

                            }

                        }else{

                            //13.设备没有进入区域

                            //13.1上午工作时间内

                            if(time()>($timeInfoAmStart-1) && time() < $timeInfoAmEnd){

                                if($attendanceInfo["num"] >0){

                                    //员工已经上班打卡

                                    if ($policeInfo) {

                                        if($policeInfo["type"] == 1){

                                            //员工一直在区域外,判断是否触发报警

                                            $diffTime = time() - $policeInfo["add_time"];

                                            if ($attendanceSetInfo['distance_time'] < $diffTime) {

                                                //远离区域已达规定时间,修改报警状态并修改员工状态

                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('type' => 2))->save();

                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                            }

                                            //未达到规定报警时间，无需触发报警

                                        }

                                        //已经触发报警，无需再次触发

                                        continue;

                                    }else{

                                        //员工离开工作区域，添加报警记录

                                        M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time()))->add();

                                    }

                                    continue;

                                }else{

                                    //员工一直没来上班，判断是否存在报警记录

                                    if ($policeInfo) {

                                        //判断是否需要修改报警状态

                                        if ($policeInfo["type"] == 1) {

                                            //判断是否符合远离区域报警

                                            $diffTime = time() - $policeInfo["add_time"];

                                            if ($attendanceSetInfo['distance_time'] < $diffTime) {

                                                //远离区域已达规定时间,修改报警状态并修改员工状态

                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('type' => 2))->save();

                                            }

                                            //还未达到规定时间，无需触发报警

                                        }

                                        //判断是否是旷工

                                        $diffTime = time() - $timeInfoAmStart;

                                        if($attendanceSetInfo['absenteeism_time'] < $diffTime){

                                            //时间差大于允许旷工时间,判定旷工,更新考勤数据

                                            M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array("first_result"=>5,"num"=>1,'update_time'=>time()))->save();

                                            M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                        }

                                        continue;

                                    }else{

                                        //已经到了上班时间，员工未到，添加报警记录,判断是否旷工，更新员工状态为离岗

                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>$timeInfoAmStart))->add();

                                        //判断是否是旷工

                                        $diffTime = time() - $timeInfoAmStart;

                                        if($diffTime > $attendanceSetInfo['absenteeism_time']){

                                            //时间差大于允许旷工时间,判定旷工,添加旷工报警,更新考勤数据

                                            M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array("first_result"=>5,"num"=>1,'update_time'=>time()))->save();

                                            M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                        }

                                        continue;

                                    }

                                }

                            }

                            //13.2上午下班时间，下班打卡判断

                            if((time()>$timeInfoAmEnd || time() == $timeInfoAmEnd) && time()< $timeEmd1){

                                if($attendanceInfo["num"] > 0){

                                    if($attendanceInfo["num"] == 2){

                                        //上午考勤结束

                                        continue;

                                    }else{

                                        //已经打过上班卡

                                        if(!$attendanceInfo["first_time"]){

                                            //若第一次打卡时间为空，则说明员工一直在区域外，判断是否有报警数据

                                            if($policeInfo){

                                                //有报警数据，说明设备上传过位置信息

                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time'=>$timeInfoAmEnd))->save();

                                            }

                                            //修改考勤表打卡次数

                                            M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array("num"=>2,'update_time'=>time()))->save();

                                            continue;

                                        }else{

                                            //员工从区域内离开，判断是否早退及报警数据处理

                                            if ($policeInfo) {

                                                //有最新的一条报警记录

                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time'=>$timeInfoAmEnd))->save();

                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                                //判断是否是早退

                                                $diffTime = $timeInfoAmEnd - $policeInfo["add_time"];

                                                if($attendanceSetInfo['leave_time'] < $diffTime) {

                                                    //超出允许早退时间,判定早退,修改员工状态

                                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('second_time'=>$policeInfo["add_time"],'update_time'=>time(),'second_result'=>4,"num"=>2))->save();

                                                    M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>6))->add();

                                                }else{

                                                    //正常下班打卡

                                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('second_time'=>time(),'update_time'=>time(),'second_result'=>2,"num"=>2))->save();

                                                }

                                                continue;

                                            }else{

                                                //无报警，下班打卡

                                                M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('second_time'=>time(),'update_time'=>time(),'second_result'=>2,"num"=>2))->save();

                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                            }

                                        }

                                        continue;

                                    }

                                }else{

                                    //员工一上午没上班，旷工处理

                                    if($policeInfo){

                                        //有报警数据，说明设备上传过位置信息

                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time'=>$timeInfoAmEnd))->save();

                                    }else{

                                        //没有报警数据，说明员工还未打过卡，即第一次传递位置信号,插入离岗记录

                                        M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoAmStart,'end_time'=>$timeInfoAmEnd))->add();

                                    }

                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('update_time'=>time(),'first_result'=>5,"num"=>2))->save();

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                    M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                    continue;

                                }

                                continue;

                            }

                            //13.3下午工作时间内

                            if(time()>($timeInfoPmStart-1) && time() < $timeInfoPmEnd){

                                if($attendanceInfo["num"] >2){

                                    if ($policeInfo) {

                                        //员工一直在区域外,判断是否触发报警

                                        $diffTime = time() - $policeInfo["add_time"];

                                        if ($policeInfo["type"] == 1) {

                                            //判断是否符合远离区域报警

                                            if ($attendanceSetInfo['distance_time'] < $diffTime) {

                                                //远离区域已达规定时间,修改报警状态并修改员工状态

                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('type' => 2))->save();

                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                            }

                                        }

                                    }else{

                                        //员工离开工作区域，添加报警记录

                                        M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time()))->add();

                                        continue;

                                    }

                                    continue;

                                }else{

                                    //员工一直没来上班，已经添加过报警记录

                                    if ($policeInfo) {

                                        if($policeInfo["add_time"]<($timeInfoPmStart-1)){

                                            //闭合上午报警记录

                                            M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoAmEnd))->save();

                                        }else{

                                            $diffTime = time() - $policeInfo["add_time"];

                                            //1.判断是否需要修改报警状态

                                            if ($policeInfo["type"] == 1) {

                                                //判断是否符合远离区域报警

                                                if ($attendanceSetInfo['distance_time'] < $diffTime) {

                                                    //远离区域已达规定时间,修改报警状态并修改员工状态

                                                    M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('type' => 2))->save();

                                                }

                                            }

                                            //2.判断是否是旷工

                                            if($attendanceSetInfo['absenteeism_time'] < $diffTime){

                                                //时间差大于允许旷工时间,判定旷工,更新员工状态,更新考勤数据

                                                M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array("third_result"=>5,"num"=>3,'update_time'=>time()))->save();

                                                M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                            }

                                        }

                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                        continue;

                                    }else{

                                        //已经到了上班时间，员工未到，添加报警记录，更新员工状态为离岗

                                        M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>$timeInfoPmStart))->add();

                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                        //判断是否是旷工

                                        $diffTime = time() - $timeInfoPmStart;

                                        if($diffTime > $attendanceSetInfo['absenteeism_time']){

                                            //时间差大于允许旷工时间,判定旷工,更新员工状态,更新考勤数据

                                            M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array("third_result"=>5,"num"=>3,'update_time'=>time()))->save();

                                            M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                        }

                                        continue;

                                    }

                                }

                                continue;

                            }

                            //13.4下午下班时间内

                            if((time()>$timeInfoPmEnd || time() == $timeInfoPmEnd) && time()< $timeEmd2){

                                if($attendanceInfo["num"] > 2){

                                    if($attendanceInfo["num"] == 4){

                                        //考勤结束

                                        continue;

                                    }else{

                                        if(!$attendanceInfo["third_time"]){

                                            //若第一次打卡时间为空，则说明员工一直在区域外，判断是否有报警数据

                                            if($policeInfo){

                                                //闭合报警数据

                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time'=>$timeInfoPmEnd))->save();

                                            }

                                            //修改考勤表打卡次数

                                            M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array("num"=>4,'update_time'=>time()))->save();

                                            continue;

                                        }else{

                                            //员工从区域内离开，判断是否早退及报警数据处理

                                            if ($policeInfo) {

                                                //有最新的一条报警记录

                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time'=>$timeInfoPmEnd))->save();

                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                                //判断是否是早退

                                                $diffTime = $timeInfoPmEnd - $policeInfo["add_time"];

                                                if($attendanceSetInfo['leave_time'] < $diffTime) {

                                                    //超出允许早退时间,判定早退

                                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('fourth_time'=>$policeInfo["add_time"],'update_time'=>time(),'fourth_result'=>4,"num"=>4))->save();

                                                    M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>6))->add();

                                                }else{

                                                    //正常下班打卡

                                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('fourth_time'=>time(),'update_time'=>time(),'fourth_result'=>2,"num"=>4))->save();

                                                }

                                                continue;

                                            }else{

                                                //下班打卡

                                                M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('fourth_time'=>time(),'update_time'=>time(),'fourth_result'=>2,"num"=>4))->save();

                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                            }



                                        }

                                        continue;

                                    }

                                    continue;

                                }else{

                                    //员工一下午没来上班，旷工处理

                                    if ($policeInfo) {

                                        if($policeInfo["add_time"]<($timeInfoPmStart-1)){

                                            //闭合报警记录

                                            M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time'=>$timeInfoAmEnd))->save();

                                        }else{

                                            M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time'=>$timeInfoPmEnd))->save();

                                        }

                                    }else{

                                        M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoPmStart,'end_time'=>$timeInfoPmEnd))->add();

                                    }

                                    M("Attendance")->where(array("id"=>$attendanceInfo["id"]))->data(array('update_time'=>time(),'third_result'=>5,"num"=>4))->save();

                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" =>0,"update_time"=>time()))->save();

                                    M("Warning_message")->data(array('uid'=>$machineInfo["userid"],'machine_id'=>$machineInfo["machine_id"],'add_time'=>time(),'type'=>5))->add();

                                    continue;

                                }

                                continue;

                            }

                        }

                    }

                }

            }else{

                //获取到的信息为空

                return;

            }

        }else{

            //跨域请求失败

            return;

        }

    }

}


