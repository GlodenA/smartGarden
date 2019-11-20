<?php
namespace Admin\Controller;
use Think\Controller;
// use Think\Log;
class CronController extends Controller{

    //初始化处理
    public function attendanceInitialize(){
        //每周日晚九点半换班
        $todayWeek = fnRetWeek();
        if($todayWeek == 0){
            if(date('H:i',time()) == '21:15') {
                //北海公园陈岐新（夜班）userid:379
                $where["m.position"] = 4;
                $where["m.parent_id"] = 379;
                $where["m.is_delete"] = $where["c.is_delete"] = 0;
                $where["_string"] = "m.userid = c.userid";
                $list = M("Member")->table("__MEMBER__ m,__MACHINE__ c")->where($where)->field("m.userid,m.realname,m.position,m.parent_id,c.machine_id,c.schedules_id")->select();
                foreach($list as $k =>$v){
                    switch($v["schedules_id"]){
                        case 17:
                            //早班
                            M("Machine")->where(array("machine_id"=>$v["machine_id"]))->setField("schedules_id",19);
                            break;
                        case 18:
                            //中班
                            M("Machine")->where(array("machine_id"=>$v["machine_id"]))->setField("schedules_id",17);
                            break;
                        case 19:
                            //晚班
                            M("Machine")->where(array("machine_id"=>$v["machine_id"]))->setField("schedules_id",18);
                            break;
                        default:
                            break;
                    }
                }
            }
            if(date('H:i',time()) == '22:15') {
                //潍河于勤明441
                $where["m.position"] = 4;
                $where["m.parent_id"] = 441;
                $where["m.is_delete"] = $where["c.is_delete"] = 0;
                $where["_string"] = "m.userid = c.userid";
                $list = M("Member")->table("__MEMBER__ m,__MACHINE__ c")->where($where)->field("m.userid,m.realname,m.position,m.parent_id,c.machine_id,c.schedules_id")->select();
                foreach($list as $k =>$v){
                    switch($v["schedules_id"]){
                        case 13:
                            //白班
                            M("Machine")->where(array("machine_id"=>$v["machine_id"]))->setField("schedules_id",14);
                            break;
                        case 14:
                            //中班
                            M("Machine")->where(array("machine_id"=>$v["machine_id"]))->setField("schedules_id",13);
                            break;
                        case 10:
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        if(date('H:i',time()) == '23:00') {
            M("Member")->where(array('is_delete' => 0))->data(array("job_status" =>2,"update_time"=>time()))->save();
        }

        //删除七天前的位置日志
        $whereWm["add_time"] = array("lt",time()-7*24*60*60);
        M("Warning_message")->where($whereWm)->delete();
    }
    //定时任务
    public function getMachineInfoList() {
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
                foreach($list1 as $k => $v) {

                    $groupIdList = M("Group")->order("group_id desc")->select();
                    foreach ($groupIdList as $k => $value) {

                        \Think\Log::record('group_id = ' .' '. $groupIdList[$k]["group_id"]);
                        if($v["groupID"] == $groupIdList[$k]["group_id"])
                        {
                            $hasGGroupId = true;
                        }
                    }

                    if ($hasGGroupId) {

                            \Think\Log::record('group_id = ' .' '. $groupIdList[$k]["group_id"],'INFO');

                            $data = "";
                            $where[$k]["machine_imei"] = $v["sn"];
                            $where[$k]["is_delete"] = 0;
                            $isIn[$k] = M("Machine")->where($where[$k])->find();
                            $electricityArr = explode("-", $v["dataContext"]);
                            $data["electricity"] = $electricityArr[0];
                            $data["mid"] = $v["id"];
                            $data["machine_status"] = $v["status"];
                            $data["server_utc_time"] = $v["serverUtcDate"];
                            $data["device_utc_time"] = $v["deviceUtcDate"];
                            $data["b_lat"] = $v["baiduLat"];
                            $data["b_lon"] = $v["baiduLng"];
                            $data["login_time"] = strtotime($v["deviceUtcDate"]);
                            //1.进行设备处理
                            //1.1添加或更新设备信息
                            if (!$isIn[$k]) {
                                $data["machine_name"] = $v["name"];
                                $data["machine_imei"] = $v["sn"];
                                $data["mid"] = $v["id"];
                                $data["add_time"] = $data["update_time"] = time();
                                M("Machine")->data($data)->add();
                            } else {
                                //设备信息更新
                                $data["update_time"] = time();
                                M("Machine")->where($where[$k])->data($data)->save();
                            }
                            //2.获取设备信息
                            $machineInfo = M("Machine")->where($where[$k])->find();
                            //判断设备是否绑定区域
                            if (!$machineInfo["area_id"]) {
                                //未绑定区域，无需打卡
                                continue;
                            }
                            //获取设备绑定区域信息
                            $areaInfo = M("Area_map")->where(array("id" => $machineInfo["area_id"], "is_show" => 1, "is_delete" => 0))->find();
                            //判断区域是否存在
                            if (!$areaInfo) {
                                //关联区域不存在，无需打卡
                                continue;
                            }
                            //设备是否绑定员工
                            if (!$machineInfo["userid"]) {
                                //设备未绑定员工，无需打卡
                                continue;
                            }
                            $emplyeeInfo = M("Member")->where(array("userid" => $machineInfo["userid"], "is_delete" => 0))->field("userid,realname")->find();
                            if (!$emplyeeInfo) {
                                //员工不存在，无需打卡
                                continue;
                            }
                            //根据设备信息中的绑定班组获取班组信息
                            $whereSchedules["schedules_id"] = $machineInfo["schedules_id"];
                            $whereSchedules["is_show"] = 1;
                            $whereSchedules["is_delete"] = 0;
                            $schedulesInfo = M("Schedules_setting")->where($whereSchedules)->find();
                            if (!$schedulesInfo) {
                                //班组信息不存在，无需打卡
                                continue;
                            }
                            //根据班组信息做出相应判断
                            if (!$schedulesInfo["work_day"]) {
                                //未设置工作日，无需打卡
                                continue;
                            }
                            if (!$schedulesInfo["time_id"]) {
                                //未设置时间段，无需打卡
                                continue;
                            }
                            if (strpos($schedulesInfo["work_day"], ',') !== false) {
                                //4.2多天判断今天是否是工作日
                                $todayWeek = fnRetWeek();
                                $todayWeekArr = explode(",", $schedulesInfo["work_day"]);
                                if (in_array($todayWeek, $todayWeekArr) == false) {
                                    //今天不是工作日，无需打卡
                                    continue;
                                }
                            } else {
                                //4.2工作日仅一天判断今天是否是工作日
                                $todayWeek = fnRetWeek();
                                if ($todayWeek != $schedulesInfo["work_day"]) {
                                    //今天不是工作日，无需打卡
                                    continue;
                                }
                            }
                            //8.3判断设备是否在区域内
                            $areaInfoStr = $areaInfo["coordinate"];
                            $areaListStr = "[" . $areaInfoStr . "]";
                            $areaListArr = json_decode($areaListStr, true);
                            //判断设备是否在围栏内并上传其位置
                            $point = array(
                                'lon' => $v["baiduLng"],
                                'lat' => $v["baiduLat"]
                            );
                            $isInPoint = is_point_in_polygon($point, $areaListArr);
                            $year = date("Y-m-d", time());
                            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                            $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
                            //时间段判断
                            if (strpos($schedulesInfo["time_id"], ',') !== false) {
                                //存在多个时间段,进行打卡操作
                                //多个时间段
                                $timeIdArr = explode(",", $schedulesInfo["time_id"]);
                                //两个时间段
                                $timeInfoAm = M("Schedules_time")->where(array("id" => $timeIdArr[0], "is_show" => 1, "is_delete" => 0))->find();
                                if (!$timeInfoAm) {
                                    //对应时间段已删除
                                    continue;
                                }
                                $timeInfoPm = M("Schedules_time")->where(array("id" => $timeIdArr[1], "is_show" => 1, "is_delete" => 0))->find();
                                if (!$timeInfoPm) {
                                    //对应时间段已删除
                                    continue;
                                }
                                //拼接时间段
                                $timeInfoAmStart = strtotime($year . ' ' . $timeInfoAm["start_time"]);
                                $timeInfoAmEnd = strtotime($year . ' ' . $timeInfoAm["end_time"]);
                                $timeInfoPmStart = strtotime($year . ' ' . $timeInfoPm["start_time"]);
                                $timeInfoPmEnd = strtotime($year . ' ' . $timeInfoPm["end_time"]);
                                //5.查询考勤设置
                                $attendanceSetInfo = M("Attendance_setting")->where(array("id" => 1))->find();
                                //6.判断是否在工作时间内
                                $timeStart1 = $timeInfoAmStart - $attendanceSetInfo["error_time"];
                                $timeEmd1 = $timeInfoAmEnd + $attendanceSetInfo["error_time"];
                                $timeStart2 = $timeInfoPmStart - $attendanceSetInfo["error_time"];
                                $timeEmd2 = $timeInfoPmEnd + $attendanceSetInfo["error_time"];
                                //7.1判断今日考勤是否已经结束
                                $whereAttendance["userid"] = $machineInfo["userid"];
                                $whereAttendance["machine_id"] = $machineInfo["machine_id"];
                                $whereAttendance["is_delete"] = 0;
                                $whereAttendance["add_time"] = array('between', array($beginToday, $endToday));
                                $attendanceInfo = M("Attendance")->where($whereAttendance)->find();
                                if (!$attendanceInfo) {
                                    //没有考勤数据，创建新的考勤数据
                                    $attendanceData["userid"] = $machineInfo["userid"];
                                    $attendanceData["machine_id"] = $machineInfo["machine_id"];
                                    $attendanceData["add_time"] = $attendanceData["update_time"] = time();
                                    $attendanceInfoId = M("Attendance")->data($attendanceData)->add();
                                    $attendanceInfo = M("Attendance")->where(array("id" => $attendanceInfoId))->find();
                                }
                                //判断是否存在异常考勤数据
                                if (strtotime($v["deviceUtcDate"]) > ($timeEmd2 + 2 * 60 * 60)) {
                                    //若定位时间大于下班时间则停止二次确认
                                    continue;
                                }
                                //如果当前时间大于设备允许迟到时间进行二次确认
                                $locus_end_time = $timeInfoAmStart + $attendanceSetInfo["late_time"];
                                if (time() > $locus_end_time) {
                                    //异常二次确认
                                    $updateData = "";
                                    //设备定位时间大于迟到时间
                                    if (strtotime($v["deviceUtcDate"]) > $locus_end_time) {
                                        //判断是否存在未二次确认的异常考勤数据
                                        if ($attendanceInfo["first_result"] != 1 && $attendanceInfo["first_status"] != 1) {
                                            if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $timeStart1, $locus_end_time, 1, $areaInfoStr, "first_time", "first_result", "first_status")) {
                                                //继续往下判断，设备定位时间大于旷工时间,去判断迟到
                                                $locus_end_time = $timeInfoAmStart + $attendanceSetInfo["absenteeism_time"];
                                                if (strtotime($v["deviceUtcDate"]) > $locus_end_time) {
                                                    $locus_start_time = $timeInfoAmStart + $attendanceSetInfo["late_time"];
                                                    if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $locus_start_time, $locus_end_time, 3, $areaInfoStr, "first_time", "first_result", "first_status")) {
                                                        //继续往下判断，设备定位时间大于下班时间,去判断旷工
                                                        if (strtotime($v["deviceUtcDate"]) > $timeInfoAmEnd) {
                                                            $locus_start_time = $timeInfoAmStart + $attendanceSetInfo["absenteeism_time"];
                                                            if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $locus_start_time, $timeInfoAmEnd, 5, $areaInfoStr, "first_time", "first_result", "first_status")) {
                                                                //第一次打卡期间内无轨迹
                                                                $updateData["first_result"] = 5;
                                                                $updateData["first_status"] = 1;
                                                                M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data($updateData)->save();
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        //设备定位时间大于第一次打卡下班时间
                                        if (strtotime($v["deviceUtcDate"]) > $timeEmd1) {
                                            if ($attendanceInfo["second_result"] != 2 && $attendanceInfo["second_status"] != 1) {
                                                //判断早退时间内的轨迹，若有则判断失误，改判正常下班，若无则说明之前的判断正确
                                                $locus_start_time = $timeInfoAmEnd - $attendanceSetInfo["leave_time"];
                                                if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $locus_start_time, $timeInfoAmEnd, 2, $areaInfoStr, "second_time", "second_result", "second_status")) {
                                                    $updateData["second_status"] = 1;
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data($updateData)->save();
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($attendanceInfo["num"] == 2 && time() < $timeStart2) {
                                    //第一次打卡考勤已结束
                                    continue;
                                }
                                //设备定位时间大于第二次上班允许打卡时间开始进行二次确认
                                $locus_end_time = $timeInfoPmStart + $attendanceSetInfo["late_time"];
                                if (time() > $locus_end_time) {
                                    //异常二次确认
                                    $updateData = "";
                                    //设备定位时间大于迟到时间
                                    if (strtotime($v["deviceUtcDate"]) > $locus_end_time) {
                                        //判断是否存在未二次确认的异常考勤数据
                                        if ($attendanceInfo["third_result"] != 1 && $attendanceInfo["third_status"] != 1) {
                                            if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $timeStart2, $locus_end_time, 1, $areaInfoStr, "third_time", "third_result", "third_status")) {
                                                //继续往下判断，设备定位时间大于旷工时间,去判断迟到
                                                $locus_end_time = $timeInfoPmStart + $attendanceSetInfo["absenteeism_time"];
                                                if (strtotime($v["deviceUtcDate"]) > $locus_end_time) {
                                                    $locus_start_time = $timeInfoPmStart + $attendanceSetInfo["late_time"];
                                                    if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $locus_start_time, $locus_end_time, 3, $areaInfoStr, "third_time", "third_result", "third_status")) {
                                                        //继续往下判断，设备定位时间大于下班时间,去判断旷工
                                                        if (strtotime($v["deviceUtcDate"]) > $timeInfoPmEnd) {
                                                            $locus_start_time = $timeInfoPmStart + $attendanceSetInfo["absenteeism_time"];
                                                            if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $locus_start_time, $timeInfoPmEnd, 5, $areaInfoStr, "third_time", "third_result", "third_status")) {
                                                                //第二次打卡期间内无轨迹
                                                                $updateData["third_result"] = 5;
                                                                $updateData["third_status"] = 1;
                                                                M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data($updateData)->save();
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        //设备定位时间大于第二次允许打卡下班时间
                                        if (strtotime($v["deviceUtcDate"]) > $timeEmd2) {
                                            if ($attendanceInfo["fourth_result"] != 2 && $attendanceInfo["fourth_status"] != 1) {
                                                //判断早退时间内的轨迹，若有则判断失误，改判正常下班，若无则说明之前的判断正确
                                                $locus_start_time = $timeInfoPmEnd - $attendanceSetInfo["leave_time"];
                                                if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $locus_start_time, $timeInfoPmEnd, 2, $areaInfoStr, "fourth_time", "fourth_result", "fourth_status")) {
                                                    $updateData["fourth_status"] = 1;
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data($updateData)->save();
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($attendanceInfo["num"] == 4) {
                                    //第二次打卡结束，今日考勤完成
                                    continue;
                                }
                                if ($v["status"] == "Offline") {
                                    //设备离线
                                    continue;
                                }
                                if (time() < $timeStart1 || (time() > $timeEmd1 && time() < $timeStart2) || time() > $timeEmd2) {
                                    //设备不在工作时间内
                                    continue;
                                }
                                //说明有考勤数据并且考勤还未完成
                                //10.在实际工作时间内，进行报警判断
                                if ((time() > $timeInfoAmStart && time() < $timeInfoAmEnd) || (time() > $timeInfoPmStart && time() < $timeInfoPmEnd)) {
                                    //10.1低电量报警判断
                                    if ($electricityArr[0] < $attendanceSetInfo["electric_quantity"]) {
                                        //需要低电量报警，查询最新报警时间
                                        $whereElectricity["machine_id"] = $machineInfo["machine_id"];
                                        $whereElectricity["type"] = 3;
                                        $electricityInfo = M("Warning_message")->where($whereElectricity)->order("add_time desc")->find();
                                        $electricityData["uid"] = $machineInfo["userid"];
                                        $electricityData["machine_id"] = $machineInfo["machine_id"];
                                        $electricityData["add_time"] = time();
                                        $electricityData["type"] = 3;
                                        if ($electricityInfo) {
                                            //已经报警过，判断报警间隔
                                            $diffElectric = time() - $electricityInfo["add_time"];
                                            if ($diffElectric > $attendanceSetInfo["electric_time"]) {
                                                //距离上次低电量报警已超所设置时间，再次触发报警
                                                M("Warning_message")->data($electricityData)->add();
                                            }
                                        } else {
                                            //还未进行低电量报警，触发报警
                                            M("Warning_message")->data($electricityData)->add();
                                        }
                                    }
                                    //10.2长时间静止报警(打过一次卡并且在区域内)
                                    if ($v["isStop"] == 1 && time() > ($timeInfoAmStart + $attendanceSetInfo["still_time"])) {
                                        $still_time = $attendanceSetInfo["still_time"] / 60;
                                        //在工作时间内，已经打过上班卡，在区域内
                                        if (($v["stopTimeMinute"] > $still_time) && $isInPoint && ($attendanceInfo['num'] == 1 || $attendanceInfo['num'] == 3)) {
                                            //需要静止报警，查询最新报警时间
                                            $whereStill["machine_id"] = $machineInfo["machine_id"];
                                            $whereStill["type"] = 7;
                                            $whereStill["add_time"] = array("gt", $beginToday);
                                            $stillInfo = M("Warning_message")->where($whereStill)->order("add_time desc")->find();
                                            $stillData["uid"] = $machineInfo["userid"];
                                            $stillData["machine_id"] = $machineInfo["machine_id"];
                                            $stillData["add_time"] = time();
                                            $stillData["type"] = 7;
                                            if ($stillInfo) {
                                                //已经报警过，判断报警间隔
                                                $diffElectric = time() - $stillInfo["add_time"];
                                                if ($diffElectric > $attendanceSetInfo["still_time_interval"]) {
                                                    //距离上次静止报警已超所设置时间，再次触发报警
                                                    M("Warning_message")->data($stillData)->add();
                                                }
                                            } else {
                                                //还未进行静止报警，触发报警
                                                M("Warning_message")->data($stillData)->add();
                                            }
                                        }
                                    }
                                }
                                //若静止时间大于半小时，则不进行打卡操作
                                if ($v["stopTimeMinute"] > 30) {
                                    continue;
                                }
                                //11.考勤打卡
                                //11.1报警判断
                                $wherePolice["machine_id"] = $machineInfo["machine_id"];
                                $wherePolice["type"] = array("lt", 3);
                                $wherePolice["add_time"] = array("gt", $beginToday);
                                $wherePolice["end_time"] = array("lt", 1);
                                $policeInfo = M("Warning_message")->where($wherePolice)->order("add_time desc")->find();
                                //12.设备进入区域
                                if ($isInPoint) {
                                    //12.1判断是不是在上午允许上班打卡时间内
                                    $timeAmStart = $timeInfoAmStart - $attendanceSetInfo['error_time'];
                                    $timeAmEnd = $timeInfoAmStart + $attendanceSetInfo['late_time'];
                                    if (time() < $timeAmEnd && time() > $timeAmStart) {
                                        //在上午上班打卡允许时间内（大于允许误差时间小于允许迟到时间）
                                        if ($attendanceInfo["num"] > 0) {
                                            //说明已经进行上班打卡处理（可能打卡之后员工离开区域，即上班时间与允许迟到时间内离开区域）
                                            if ($policeInfo) {
                                                //闭合报警记录更新员工状态
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => time()))->save();
                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            }
                                            //说明员工进入区域后，一直未曾离开
                                            continue;
                                        } else {
                                            //员工上班打卡
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('first_result' => 1, "num" => 1, 'first_time' => time(), 'update_time' => time()))->save();
                                        }
                                        continue;
                                    }
                                    //12.2判断在上午上班时间内（若还未打卡，则说明员工迟到或旷工）
                                    if (time() > $timeAmEnd && time() < $timeInfoAmEnd) {
                                        if ($policeInfo) {
                                            M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => time()))->save();
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                        }
                                        //在上午上班时间内
                                        if ($attendanceInfo["num"] > 0) {
                                            //说明已经进行上班打卡处理
                                            if ($attendanceInfo["first_result"] == 5 && !$attendanceInfo["first_time"]) {
                                                //说明其上班时间未到，超出旷工时间，更新旷工时间
                                                M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('first_time' => time(), 'update_time' => time()))->save();
                                            }
                                            continue;
                                        } else {
                                            //没打过卡,先插入离岗记录，判断旷工或迟到
                                            //判断有无报警记录
                                            if (!$policeInfo) {
                                                //插入离岗记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoAmStart, 'end_time' => time()))->add();
                                            }
                                            //改变用户状态
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            $diffTime = time() - $timeInfoAmStart;
                                            if ($attendanceSetInfo['absenteeism_time'] < $diffTime) {
                                                //时间差大于允许旷工时间,判定旷工,更新员工状态,更新考勤数据，触发旷工报警
                                                M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('first_time' => time(), "first_result" => 5, "num" => 1, 'update_time' => time()))->save();
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                                continue;
                                            } else {
                                                if ($attendanceSetInfo['leave_time'] < $diffTime) {
                                                    //时间差大于允许迟到时间,判定迟到,更新考勤数据，触发迟到报警
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('first_time' => time(), "first_result" => 3, "num" => 1, 'update_time' => time()))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 4))->add();
                                                    continue;
                                                }
                                            }
                                        }
                                        continue;
                                    }
                                    //12.3上午下班时间，下班打卡判断
                                    if ((time() > $timeInfoAmEnd || time() == $timeInfoAmEnd) && time() < $timeEmd1) {
                                        if ($attendanceInfo["num"] > 1) {
                                            //上午考勤已经结束
                                            continue;
                                        } elseif ($attendanceInfo["num"] == 1) {
                                            //上午下班打卡，早退判断
                                            if ($policeInfo) {
                                                //有最新的一条报警记录
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => $timeInfoAmEnd))->save();
                                                $diffTime = $timeInfoAmEnd - $policeInfo["add_time"];
                                                if ($attendanceSetInfo['leave_time'] < $diffTime) {
                                                    //超出允许早退时间,判定早退,修改员工状态
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => $policeInfo["add_time"], 'update_time' => time(), 'second_result' => 4, "num" => 2))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 6))->add();
                                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                                    continue;
                                                }
                                            }
                                            //正常下班打卡
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => time(), 'update_time' => time(), 'second_result' => 2, "num" => 2))->save();
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                            continue;
                                        } else {
                                            //上午未打过卡
                                            if ($policeInfo) {
                                                //有最新的一条报警记录
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => $timeInfoAmEnd))->save();
                                            } else {
                                                //插入离岗记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoAmStart, 'end_time' => $timeInfoAmEnd))->add();
                                            }
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('update_time' => time(), 'first_result' => 5, "num" => 2))->save();
                                            M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                            continue;
                                        }
                                        continue;
                                    }
                                    //12.4判断是不是在下午允许上班打卡时间内
                                    $timePmStart = $timeInfoPmStart - $attendanceSetInfo['error_time'];
                                    $timePmEnd = $timeInfoPmStart + $attendanceSetInfo['late_time'];
                                    if (time() < $timePmEnd && time() > $timePmStart) {
                                        //在下午上班打卡允许时间内（大于允许误差时间小于允许迟到时间）
                                        if ($policeInfo) {
                                            if ($policeInfo["add_time"] < $timePmStart) {
                                                //闭合报警记录
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => $timeInfoAmEnd))->save();
                                            } else {
                                                //闭合报警记录
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => time()))->save();
                                            }
                                        }
                                        if ($attendanceInfo["num"] > 2) {
                                            //说明已经进行上班打卡处理（可能打卡之后员工离开区域，即上班时间与允许迟到时间内离开区域）
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            continue;
                                        } else {
                                            //员工上班打卡
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('third_time' => time(), "third_result" => 1, "num" => 3, 'update_time' => time()))->save();
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            continue;
                                        }
                                        continue;
                                    }
                                    //12.5在下午上班时间内（大于允许迟到时间小于下午下班时间）
                                    if (time() > $timePmEnd && time() < $timeInfoPmEnd) {
                                        if ($policeInfo) {
                                            if ($policeInfo["add_time"] < $timePmStart) {
                                                //闭合报警记录
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => $timeInfoAmEnd))->save();
                                            } else {
                                                //闭合报警记录
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => time()))->save();
                                            }
                                        }
                                        //在下午上班时间内
                                        if ($attendanceInfo["num"] > 2) {
                                            //说明已经进行上班打卡处理
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            if ($attendanceInfo["third_result"] == 5 && !$attendanceInfo["third_time"]) {
                                                //说明其上班时间未到，超出旷工时间，更新旷工时间
                                                M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('third_time' => time(), 'num' => 3, 'update_time' => time()))->save();
                                            }
                                            continue;
                                        } else {
                                            //没打过卡,先插入离岗记录，判断旷工或迟到
                                            //判断有无报警记录
                                            if (!$policeInfo) {
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoPmStart, 'end_time' => time()))->add();
                                            }
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            $diffTime = time() - $timeInfoPmStart;
                                            if ($attendanceSetInfo['absenteeism_time'] < $diffTime) {
                                                //时间差大于允许旷工时间,判定旷工,更新员工状态,更新考勤数据
                                                M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('third_time' => time(), "third_result" => 5, "num" => 3, 'update_time' => time()))->save();
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                                continue;
                                            } else {
                                                if ($attendanceSetInfo['leave_time'] < $diffTime) {
                                                    //迟到处理
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('third_time' => time(), "third_result" => 3, "num" => 3, 'update_time' => time()))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 4))->add();
                                                    continue;
                                                }
                                            }
                                        }
                                        continue;
                                    }
                                    //12.6下午下班时间内，下班打卡判断
                                    if ((time() > $timeInfoPmEnd || time() == $timeInfoPmEnd) && time() < $timeEmd2) {
                                        if ($attendanceInfo["num"] > 3) {
                                            //下午考勤已经结束
                                            continue;
                                        } elseif ($attendanceInfo["num"] == 3) {
                                            //说明已经打过下午上班卡，下午下班打卡，早退判断
                                            if ($policeInfo) {
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => $timeInfoPmEnd))->save();
                                                $diffTime = $timeInfoPmEnd - $policeInfo["add_time"];
                                                if ($attendanceSetInfo['leave_time'] < $diffTime) {
                                                    //超出允许早退时间,判定早退,修改员工状态
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('fourth_time' => $policeInfo["add_time"], 'update_time' => time(), 'fourth_result' => 4, "num" => 4))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 6))->add();
                                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                                    continue;
                                                }
                                            }
                                            //正常下班打卡
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('fourth_time' => time(), 'update_time' => time(), 'fourth_result' => 2, "num" => 4))->save();
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                            continue;
                                        } else {
                                            //下午未打过卡，即设备上传信号时已经是下班点
                                            if ($policeInfo) {
                                                if ($policeInfo["add_time"] < $timeInfoPmStart) {
                                                    //闭合上午报警记录
                                                    M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => $timeInfoAmEnd))->save();
                                                } else {
                                                    //闭合下午报警记录
                                                    M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => $timeInfoPmEnd))->save();
                                                }
                                            } else {
                                                //插入离岗记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoPmStart, 'end_time' => $timeInfoPmEnd))->add();
                                            }
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('update_time' => time(), 'third_result' => 5, "num" => 4))->save();
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                            M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                            continue;
                                        }
                                        continue;
                                    }
                                } else {
                                    //13.设备没有进入区域
                                    //13.1上午工作时间内
                                    if (time() > $timeInfoAmStart && time() < $timeInfoAmEnd) {
                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                        if ($attendanceInfo["num"] > 0) {
                                            //员工已经上班打卡
                                            if ($policeInfo) {
                                                if ($policeInfo["type"] == 1) {
                                                    //员工一直在区域外,判断是否触发报警
                                                    $diffTime = time() - $policeInfo["add_time"];
                                                    if ($attendanceSetInfo['distance_time'] < $diffTime) {
                                                        //远离区域已达规定时间,修改报警状态并修改员工状态
                                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('type' => 2))->save();
                                                    }
                                                    //未达到规定报警时间，无需触发报警
                                                }
                                                //已经触发报警，无需再次触发
                                                continue;
                                            } else {
                                                //员工离开工作区域，添加报警记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time()))->add();
                                            }
                                            continue;
                                        } else {
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
                                                if ($attendanceSetInfo['absenteeism_time'] < $diffTime) {
                                                    //时间差大于允许旷工时间,判定旷工,更新考勤数据
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array("first_result" => 5, "num" => 1, 'update_time' => time()))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                                }
                                                continue;
                                            } else {
                                                //已经到了上班时间，员工未到，添加报警记录,判断是否旷工，更新员工状态为离岗
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoAmStart))->add();
                                                //判断是否是旷工
                                                $diffTime = time() - $timeInfoAmStart;
                                                if ($diffTime > $attendanceSetInfo['absenteeism_time']) {
                                                    //时间差大于允许旷工时间,判定旷工,添加旷工报警,更新考勤数据
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array("first_result" => 5, "num" => 1, 'update_time' => time()))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                                }
                                                continue;
                                            }
                                        }
                                        continue;
                                    }
                                    //13.2上午下班时间，下班打卡判断
                                    if (time() > $timeInfoAmEnd && time() < $timeEmd1) {
                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                        if ($attendanceInfo["num"] > 0) {
                                            if ($attendanceInfo["num"] == 2) {
                                                //上午考勤结束
                                                continue;
                                            } else {
                                                //已经打过上班卡
                                                if (!$attendanceInfo["first_time"]) {
                                                    //若第一次打卡时间为空，则说明员工一直在区域外，判断是否有报警数据
                                                    if ($policeInfo) {
                                                        //有报警数据，说明设备上传过位置信息
                                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoAmEnd))->save();
                                                    }
                                                    //修改考勤表打卡次数
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array("num" => 2, 'update_time' => time()))->save();
                                                    continue;
                                                } else {
                                                    //员工从区域内离开，判断是否早退及报警数据处理
                                                    if ($policeInfo) {
                                                        //有最新的一条报警记录
                                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoAmEnd))->save();
                                                        //判断是否是早退
                                                        $diffTime = $timeInfoAmEnd - $policeInfo["add_time"];
                                                        if ($attendanceSetInfo['leave_time'] < $diffTime) {
                                                            //超出允许早退时间,判定早退,修改员工状态
                                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => $policeInfo["add_time"], 'update_time' => time(), 'second_result' => 4, "num" => 2))->save();
                                                            M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 6))->add();
                                                        } else {
                                                            //正常下班打卡
                                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => time(), 'update_time' => time(), 'second_result' => 2, "num" => 2))->save();
                                                        }
                                                        continue;
                                                    } else {
                                                        //无报警，下班打卡
                                                        M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => time(), 'update_time' => time(), 'second_result' => 2, "num" => 2))->save();
                                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                                    }
                                                }
                                                continue;
                                            }
                                        } else {
                                            //员工一上午没上班，旷工处理
                                            if ($policeInfo) {
                                                //有报警数据，说明设备上传过位置信息
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoAmEnd))->save();
                                            } else {
                                                //没有报警数据，说明员工还未打过卡，即第一次传递位置信号,插入离岗记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoAmStart, 'end_time' => $timeInfoAmEnd))->add();
                                            }
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('update_time' => time(), 'first_result' => 5, "num" => 2))->save();
                                            M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                            continue;
                                        }
                                        continue;
                                    }
                                    //13.3下午工作时间内
                                    if (time() > $timeInfoPmStart && time() < $timeInfoPmEnd) {
                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                        if ($attendanceInfo["num"] > 2) {
                                            if ($policeInfo) {
                                                //员工一直在区域外,判断是否触发报警
                                                $diffTime = time() - $policeInfo["add_time"];
                                                if ($policeInfo["type"] == 1) {
                                                    //判断是否符合远离区域报警
                                                    if ($attendanceSetInfo['distance_time'] < $diffTime) {
                                                        //远离区域已达规定时间,修改报警状态并修改员工状态
                                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('type' => 2))->save();
                                                    }
                                                }
                                            } else {
                                                //员工离开工作区域，添加报警记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time()))->add();
                                                continue;
                                            }
                                            continue;
                                        } else {
                                            //员工一直没来上班，已经添加过报警记录
                                            if ($policeInfo) {
                                                if ($policeInfo["add_time"] < ($timeInfoPmStart - 1)) {
                                                    //闭合上午报警记录
                                                    M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoAmEnd))->save();
                                                } else {
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
                                                    if ($attendanceSetInfo['absenteeism_time'] < $diffTime) {
                                                        //时间差大于允许旷工时间,判定旷工,更新员工状态,更新考勤数据
                                                        M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array("third_result" => 5, "num" => 3, 'update_time' => time()))->save();
                                                        M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                                    }
                                                }
                                                continue;
                                            } else {
                                                //已经到了上班时间，员工未到，添加报警记录，更新员工状态为离岗
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoPmStart))->add();
                                                //判断是否是旷工
                                                $diffTime = time() - $timeInfoPmStart;
                                                if ($diffTime > $attendanceSetInfo['absenteeism_time']) {
                                                    //时间差大于允许旷工时间,判定旷工,更新员工状态,更新考勤数据
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array("third_result" => 5, "num" => 3, 'update_time' => time()))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                                }
                                                continue;
                                            }
                                        }
                                        continue;
                                    }
                                    //13.4下午下班时间内
                                    if (time() > $timeInfoPmEnd && time() < $timeEmd2) {
                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                        if ($attendanceInfo["num"] > 2) {
                                            if ($attendanceInfo["num"] == 4) {
                                                //考勤结束
                                                continue;
                                            } else {
                                                if (!$attendanceInfo["third_time"]) {
                                                    //若第一次打卡时间为空，则说明员工一直在区域外，判断是否有报警数据
                                                    if ($policeInfo) {
                                                        //闭合报警数据
                                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoPmEnd))->save();
                                                    }
                                                    //修改考勤表打卡次数
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array("num" => 4, 'update_time' => time()))->save();
                                                    continue;
                                                } else {
                                                    //员工从区域内离开，判断是否早退及报警数据处理
                                                    if ($policeInfo) {
                                                        //有最新的一条报警记录
                                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoPmEnd))->save();
                                                        //判断是否是早退
                                                        $diffTime = $timeInfoPmEnd - $policeInfo["add_time"];
                                                        if ($attendanceSetInfo['leave_time'] < $diffTime) {
                                                            //超出允许早退时间,判定早退
                                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('fourth_time' => $policeInfo["add_time"], 'update_time' => time(), 'fourth_result' => 4, "num" => 4))->save();
                                                            M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 6))->add();
                                                        } else {
                                                            //正常下班打卡
                                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('fourth_time' => time(), 'update_time' => time(), 'fourth_result' => 2, "num" => 4))->save();
                                                        }
                                                        continue;
                                                    } else {
                                                        //下班打卡
                                                        M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('fourth_time' => time(), 'update_time' => time(), 'fourth_result' => 2, "num" => 4))->save();
                                                    }
                                                }
                                                continue;
                                            }
                                            continue;
                                        } else {
                                            //员工一下午没来上班，旷工处理
                                            if ($policeInfo) {
                                                if ($policeInfo["add_time"] < ($timeInfoPmStart - 1)) {
                                                    //闭合报警记录
                                                    M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoAmEnd))->save();
                                                } else {
                                                    M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoPmEnd))->save();
                                                }
                                            } else {
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoPmStart, 'end_time' => $timeInfoPmEnd))->add();
                                            }
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('update_time' => time(), 'third_result' => 5, "num" => 4))->save();
                                            M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                            continue;
                                        }
                                        continue;
                                    }
                                }
                            } else {
                                //就一个时间段,进行保安打卡
                                $timeInfo = M("Schedules_time")->where(array("id" => $schedulesInfo["time_id"], "is_show" => 1, "is_delete" => 0))->find();
                                if (!$timeInfo) {
                                    //对应时间段已删除，无需打卡
                                    continue;
                                }
                                //如果是周天，并且时间已经大于设定时间22:10，早班和中班停止考勤
                                $todayWeek = fnRetWeek();
                                if ($todayWeek == 0) {
                                    //今天是周日，中班提前打卡，当前时间大于设定值，白班中班则不进行打卡操作
                                    if (($machineInfo["schedules_id"] == 14 || $machineInfo["schedules_id"] == 13) && time() > strtotime($year . ' 22:10:00')) {
                                        //潍河停止打卡
                                        continue;
                                    }
                                    if (($machineInfo["schedules_id"] == 17 || $machineInfo["schedules_id"] == 18) && time() > strtotime($year . ' 21:10:00')) {
                                        //潍河停止打卡
                                        continue;
                                    }
                                }
                                //判断是否跨天
                                if ($this->checkOneDay($timeInfo['start_time'], $timeInfo['end_time'])) {
                                    //跨天
                                    if (time() > (strtotime($year . ' ' . $timeInfo["end_time"]) + 2 * 60 * 60)) {
                                        $timeInfoAmStart = strtotime($year . ' ' . $timeInfo["start_time"]);
                                        $timeInfoAmEnd = strtotime(date('Y-m-d', strtotime('+1 day')) . ' ' . $timeInfo["end_time"]);
                                    } else {
                                        $timeInfoAmStart = strtotime(date('Y-m-d', strtotime('-1 day')) . ' ' . $timeInfo["start_time"]);
                                        $timeInfoAmEnd = strtotime($year . ' ' . $timeInfo["end_time"]);
                                    }
                                } else {
                                    //不跨天拼接时间段
                                    if ($todayWeek == 0) {
                                        //今天是周日，中班提前打卡
                                        if ($machineInfo["schedules_id"] == 14 && time() < strtotime($year . ' 22:15:00')) {
                                            //中班
                                            $timeInfoAmStart = strtotime($year . ' ' . $timeInfo["start_time"]);
                                            $timeInfoAmEnd = strtotime($year . ' ' . $timeInfo["end_time"]) - 1.5 * 60 * 60;
                                        } elseif ($machineInfo["schedules_id"] == 18 && time() < strtotime($year . ' 21:15:00')) {
                                            //中班
                                            $timeInfoAmStart = strtotime($year . ' ' . $timeInfo["start_time"]);
                                            $timeInfoAmEnd = strtotime($year . ' ' . $timeInfo["end_time"]) - 1.5 * 60 * 60;
                                        } else {
                                            $timeInfoAmStart = strtotime($year . ' ' . $timeInfo["start_time"]);
                                            $timeInfoAmEnd = strtotime($year . ' ' . $timeInfo["end_time"]);
                                        }
                                    } else {
                                        $timeInfoAmStart = strtotime($year . ' ' . $timeInfo["start_time"]);
                                        $timeInfoAmEnd = strtotime($year . ' ' . $timeInfo["end_time"]);
                                    }
                                }
                                //查询考勤设置
                                $attendanceSetInfo = M("Attendance_setting")->where(array("id" => 1))->find();
                                //拼接允许工作时间段
                                $timeStart1 = $timeInfoAmStart - $attendanceSetInfo["error_time"];
                                $timeEmd1 = $timeInfoAmEnd + $attendanceSetInfo["error_time"];
                                if (time() > $timeStart1) {
                                    //获取考勤信息
                                    $whereAttendance["userid"] = $machineInfo["userid"];
                                    $whereAttendance["machine_id"] = $machineInfo["machine_id"];
                                    $whereAttendance["is_delete"] = 0;
                                    $whereAttendance["add_time"] = array('between', array($timeStart1, $timeEmd1));
                                    $attendanceInfo = M("Attendance")->where($whereAttendance)->find();
                                    if (!$attendanceInfo) {
                                        //没有考勤数据，创建新的考勤数据
                                        $attendanceData["userid"] = $machineInfo["userid"];
                                        $attendanceData["machine_id"] = $machineInfo["machine_id"];
                                        $attendanceData["add_time"] = $attendanceData["update_time"] = time() + 10;
                                        $attendanceInfoId = M("Attendance")->data($attendanceData)->add();
                                        $attendanceInfo = M("Attendance")->where(array("id" => $attendanceInfoId))->find();
                                    }
                                }
                                if (strtotime($v["deviceUtcDate"]) > ($timeEmd1 + 60 * 60)) {
                                    //若定位时间大于下班时间则停止二次确认
                                    continue;
                                }
                                //如果当前时间大于设备允许迟到时间进行二次确认
                                $locus_end_time = $timeInfoAmStart + $attendanceSetInfo["late_time"];
                                if (time() > $locus_end_time) {
                                    //异常二次确认
                                    $updateData = "";
                                    //设备定位时间大于迟到时间
                                    if (strtotime($v["deviceUtcDate"]) > $locus_end_time) {
                                        //判断是否存在未二次确认的异常考勤数据
                                        if ($attendanceInfo["first_result"] != 1 && $attendanceInfo["first_status"] != 1) {
    //                                        function checkAttendance($mid,$attendanceId,$locus_start_time,$locus_end_time,$check_result,$areaInfoStr,$str1,$str2,$str3){
                                            if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $timeStart1, $locus_end_time, 1, $areaInfoStr, "first_time", "first_result", "first_status")) {
                                                //继续往下判断，设备定位时间大于旷工时间,去判断迟到
                                                $locus_end_time = $timeInfoAmStart + $attendanceSetInfo["absenteeism_time"];
                                                if (strtotime($v["deviceUtcDate"]) > $locus_end_time) {
                                                    $locus_start_time = $timeInfoAmStart + $attendanceSetInfo["late_time"];
                                                    if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $locus_start_time, $locus_end_time, 3, $areaInfoStr, "first_time", "first_result", "first_status")) {
                                                        //继续往下判断，设备定位时间大于下班时间,去判断旷工
                                                        if (strtotime($v["deviceUtcDate"]) > $timeInfoAmEnd) {
                                                            $locus_start_time = $timeInfoAmStart + $attendanceSetInfo["absenteeism_time"];
                                                            if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $locus_start_time, $timeInfoAmEnd, 5, $areaInfoStr, "first_time", "first_result", "first_status")) {
                                                                //第一次打卡期间内无轨迹
                                                                $updateData["first_result"] = 5;
                                                                $updateData["first_status"] = 1;
                                                                M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data($updateData)->save();
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        //设备定位时间大于第一次打卡下班时间
                                        if (strtotime($v["deviceUtcDate"]) > $timeEmd1) {
                                            if ($attendanceInfo["second_result"] != 2 && $attendanceInfo["second_status"] != 1) {
                                                //判断早退时间内的轨迹，若有则判断失误，改判正常下班，若无则说明之前的判断正确
                                                $locus_start_time = $timeInfoAmEnd - $attendanceSetInfo["leave_time"];
                                                if (!$this->checkAttendance($machineInfo["mid"], $attendanceInfo["id"], $locus_start_time, $timeInfoAmEnd, 2, $areaInfoStr, "second_time", "second_result", "second_status")) {
                                                    $updateData["second_status"] = 1;
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data($updateData)->save();
                                                }
                                            }
                                        }
                                    }
                                }
                                if ($attendanceInfo["num"] == 2) {
                                    //今日考勤已结束
                                    continue;
                                }
                                if ($v["status"] == "Offline") {
                                    //设备离线，停止执行
                                    continue;
                                }
                                if (time() < $timeStart1 || time() > $timeEmd1) {
                                    //设备不在工作时间内
                                    continue;
                                }
                                //10.1低电量报警判断
                                if ($electricityArr[0] < $attendanceSetInfo["electric_quantity"]) {
                                    //需要低电量报警，查询最新报警时间
                                    $whereElectricity["machine_id"] = $machineInfo["machine_id"];
                                    $whereElectricity["type"] = 3;
                                    $electricityInfo = M("Warning_message")->where($whereElectricity)->order("add_time desc")->find();
                                    $electricityData["uid"] = $machineInfo["userid"];
                                    $electricityData["machine_id"] = $machineInfo["machine_id"];
                                    $electricityData["add_time"] = time();
                                    $electricityData["type"] = 3;
                                    if ($electricityInfo) {
                                        //已经报警过，判断报警间隔
                                        $diffElectric = time() - $electricityInfo["add_time"];
                                        if ($diffElectric > $attendanceSetInfo["electric_time"]) {
                                            //距离上次低电量报警已超所设置时间，再次触发报警
                                            M("Warning_message")->data($electricityData)->add();
                                        }
                                    } else {
                                        //还未进行低电量报警，触发报警
                                        M("Warning_message")->data($electricityData)->add();
                                    }
                                }
                                //若静止时间大于半小时，则不进行打卡操作
                                if ($v["stopTimeMinute"] > 30) {
                                    continue;
                                }
                                //报警数据获取，创建时间大于上班打卡时间
                                $wherePolice["machine_id"] = $machineInfo["machine_id"];
                                $wherePolice["type"] = array("lt", 3);
                                $wherePolice["add_time"] = array("gt", $timeInfoAmStart);
                                $wherePolice["end_time"] = array("lt", 1);
                                $policeInfo = M("Warning_message")->where($wherePolice)->order("add_time desc")->find();
                                //12.设备进入区域
                                if ($isInPoint) {
                                    //12.1判断是不是在允许上班打卡时间内
                                    $timeAmStart = $timeInfoAmStart - $attendanceSetInfo['error_time'];
                                    $timeAmEnd = $timeInfoAmStart + $attendanceSetInfo['late_time'];
                                    if (time() < $timeAmEnd && time() > $timeAmStart) {
                                        //在上午上班打卡允许时间内（大于允许误差时间小于允许迟到时间）
                                        if ($attendanceInfo["num"] > 0) {
                                            //说明已经进行上班打卡
                                            if ($policeInfo) {
                                                //闭合报警记录更新员工状态
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => time()))->save();
                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            }
                                            continue;
                                        } else {
                                            //员工上班打卡
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('first_result' => 1, "num" => 1, 'first_time' => time(), 'update_time' => time()))->save();
                                        }
                                        continue;
                                    }
                                    //12.2判断在上午上班时间内（若还未打卡，则说明员工迟到或旷工）
                                    if (time() > $timeInfoAmStart && time() < $timeInfoAmEnd) {
                                        //在上班时间内
                                        if ($attendanceInfo["num"] > 0) {
                                            //说明已经进行上班打卡处理
                                            if ($policeInfo) {
                                                //闭合报警记录更新员工状态
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => time()))->save();
                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            }
                                            if ($attendanceInfo["first_result"] == 5 && !$attendanceInfo["first_time"]) {
                                                //说明其上班时间未到，超出旷工时间，更新旷工时间
                                                M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('first_time' => time(), 'update_time' => time()))->save();
                                            }
                                            continue;
                                        } else {
                                            //没打过卡,先插入离岗记录，判断旷工或迟到
                                            //判断有无报警记录
                                            if ($policeInfo) {
                                                //有最新的一条报警记录
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => time()))->save();
                                            } else {
                                                //插入离岗记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoAmStart, 'end_time' => time()))->add();
                                            }
                                            //更新员工状态
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 1, "update_time" => time()))->save();
                                            $diffTime = time() - $timeInfoAmStart;
                                            if ($attendanceSetInfo['absenteeism_time'] < $diffTime) {
                                                //时间差大于允许旷工时间,判定旷工,更新考勤数据，触发旷工报警
                                                M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('first_time' => time(), "first_result" => 5, "num" => 1, 'update_time' => time()))->save();
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                                continue;
                                            } else {
                                                if ($attendanceSetInfo['late_time'] < $diffTime) {
                                                    //时间差大于允许迟到时间,判定迟到,更新考勤数据，触发迟到报警
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('first_time' => time(), "first_result" => 3, "num" => 1, 'update_time' => time()))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 4))->add();
                                                    continue;
                                                }
                                            }
                                        }
                                        continue;
                                    }
                                    //12.3上午下班时间，下班打卡判断
                                    if (time() > $timeInfoAmEnd && time() < $timeEmd1) {
                                        if ($attendanceInfo["num"] > 1) {
                                            //已经打了下班卡
                                            continue;
                                        } elseif ($attendanceInfo["num"] == 1) {
                                            //进行下班打卡，早退判断
                                            if ($policeInfo) {
                                                //有最新的一条报警记录
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => $timeInfoAmEnd))->save();
                                                //判断是否早退
                                                $diffTime = $timeInfoAmEnd - $policeInfo["add_time"];
                                                if ($attendanceSetInfo['leave_time'] < $diffTime) {
                                                    //超出允许早退时间,判定早退,修改员工状态
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => $policeInfo["add_time"], 'update_time' => time(), 'second_result' => 4, "num" => 2))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 6))->add();
                                                    M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                                    continue;
                                                }
                                            }
                                            //正常下班打卡
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => time(), 'update_time' => time(), 'second_result' => 2, "num" => 2))->save();
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                            continue;
                                        } else {
                                            //上午未打过卡
                                            if ($policeInfo) {
                                                //有最新的一条报警记录
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array("end_time" => $timeInfoAmEnd))->save();
                                            } else {
                                                //插入离岗记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoAmStart, 'end_time' => $timeInfoAmEnd))->add();
                                            }
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('update_time' => time(), 'first_result' => 5, "num" => 2))->save();
                                            M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                            continue;
                                        }
                                        continue;
                                    }
                                } else {
                                    //13.设备在区域外
                                    //13.1上午工作时间内
                                    if (time() > $timeInfoAmStart && time() < $timeInfoAmEnd) {
                                        if ($attendanceInfo["num"] > 0) {
                                            //员工已经上班打卡
                                            if ($policeInfo) {
                                                if ($policeInfo["type"] == 1) {
                                                    //员工一直在区域外,判断是否触发报警
                                                    $diffTime = time() - $policeInfo["add_time"];
                                                    if ($attendanceSetInfo['distance_time'] < $diffTime) {
                                                        //远离区域已达规定时间,修改报警状态并修改员工状态
                                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('type' => 2))->save();
                                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                                    }
                                                    //未达到规定报警时间，无需触发报警
                                                }
                                                //已经触发报警，无需再次触发
                                                continue;
                                            } else {
                                                //员工离开工作区域，添加报警记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time()))->add();
                                            }
                                            continue;
                                        } else {
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
                                                if ($attendanceSetInfo['absenteeism_time'] < $diffTime) {
                                                    //时间差大于允许旷工时间,判定旷工,更新考勤数据
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array("first_result" => 5, "num" => 1, 'update_time' => time()))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                                }
                                                continue;
                                            } else {
                                                //已经到了上班时间，员工未到，添加报警记录,判断是否旷工，更新员工状态为离岗
                                                M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoAmStart))->add();
                                                //判断是否是旷工
                                                $diffTime = time() - $timeInfoAmStart;
                                                if ($diffTime > $attendanceSetInfo['absenteeism_time']) {
                                                    //时间差大于允许旷工时间,判定旷工,添加旷工报警,更新考勤数据
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array("first_result" => 5, "num" => 1, 'update_time' => time()))->save();
                                                    M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                                }
                                                continue;
                                            }
                                        }
                                        continue;
                                    }
                                    //13.2下班时间，下班打卡判断
                                    if (time() > $timeInfoAmEnd && time() < $timeEmd1) {
                                        if ($attendanceInfo["num"] > 0) {
                                            if ($attendanceInfo["num"] == 2) {
                                                //考勤结束
                                                continue;
                                            } else {
                                                //已经打过上班卡
                                                if (!$attendanceInfo["first_time"]) {
                                                    //若第一次打卡时间为空，则说明员工一直在区域外，判断是否有报警数据
                                                    if ($policeInfo) {
                                                        //有报警数据，说明设备上传过位置信息
                                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoAmEnd))->save();
                                                    }
                                                    //修改考勤表打卡次数
                                                    M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array("num" => 2, 'update_time' => time()))->save();
                                                    continue;
                                                } else {
                                                    //员工从区域内离开，判断是否早退及报警数据处理
                                                    if ($policeInfo) {
                                                        //有最新的一条报警记录
                                                        M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoAmEnd))->save();
                                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                                        //判断是否是早退
                                                        $diffTime = $timeInfoAmEnd - $policeInfo["add_time"];
                                                        if ($attendanceSetInfo['leave_time'] < $diffTime) {
                                                            //超出允许早退时间,判定早退,修改员工状态
                                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => $policeInfo["add_time"], 'update_time' => time(), 'second_result' => 4, "num" => 2))->save();
                                                            M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 6))->add();
                                                        } else {
                                                            //正常下班打卡
                                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => time(), 'update_time' => time(), 'second_result' => 2, "num" => 2))->save();
                                                        }
                                                        continue;
                                                    } else {
                                                        //无报警，下班打卡
                                                        M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('second_time' => time(), 'update_time' => time(), 'second_result' => 2, "num" => 2))->save();
                                                        M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                                    }
                                                }
                                                continue;
                                            }
                                        } else {
                                            //员工一上午没上班，旷工处理
                                            if ($policeInfo) {
                                                //有报警数据，说明设备上传过位置信息
                                                M("Warning_message")->where(array("id" => $policeInfo["id"]))->data(array('end_time' => $timeInfoAmEnd))->save();
                                            } else {
                                                //没有报警数据，说明员工还未打过卡，即第一次传递位置信号,插入离岗记录
                                                M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => $timeInfoAmStart, 'end_time' => $timeInfoAmEnd))->add();
                                            }
                                            M("Attendance")->where(array("id" => $attendanceInfo["id"]))->data(array('update_time' => time(), 'first_result' => 5, "num" => 2))->save();
                                            M("Member")->where(array('userid' => $machineInfo["userid"]))->data(array("job_status" => 0, "update_time" => time()))->save();
                                            M("Warning_message")->data(array('uid' => $machineInfo["userid"], 'machine_id' => $machineInfo["machine_id"], 'add_time' => time(), 'type' => 5))->add();
                                            continue;
                                        }
                                        continue;
                                    }
                                }
                                continue;
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
    /**
     * 判断是否跨天
     * @return bool
     */
    public function checkOneDay($startTime = '00:00:00',$endTime = '08:00:00'){
        if(!$startTime || !$endTime){
            return false;
        }
        $startHour = substr($startTime,0,2);
        $endHour = substr($endTime,0,2);
        return intval($endHour) < intval($startHour) ? true : false;
    }
    /**
     * 根据轨迹二次确认考勤
     * @param $mid 设备mid
     * @param $attendanceId 考勤id
     * @param $locus_start_time 轨迹开始时间
     * @param $locus_end_time 轨迹结束时间
     * @param $check_result 确认结果（1，2=正常，3=迟到，4=早退，5=旷工）
     * @param $areaInfoStr 区域坐标
     * @param $str1 第几次打卡
     * @param $str2 打卡结果
     * @param $str3 确认结果
     * @return bool
     */
    function checkAttendance($mid,$attendanceId,$locus_start_time,$locus_end_time,$check_result,$areaInfoStr,$str1,$str2,$str3){
        libxml_disable_entity_loader(false);
        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
        //获取上午迟到之前的轨迹
        $paramOne["DeviceID"] = $mid;
        $paramOne["Start"] =date("Y-m-d",$locus_start_time)."T".date("H:i:s",$locus_start_time);
        $paramOne["End"] = date("Y-m-d",$locus_end_time)."T".date("H:i:s",$locus_end_time);
        $paramOne["TimeZone"] = "8:00";
        $paramOne["ShowLBS"] = 0;
        $locusResult = $client->GetDevicesHistory($paramOne);
        if ($locusResult) {
            $locusResult1 = object_to_array($locusResult);
            $locusStr1 = $locusResult1["GetDevicesHistoryResult"];
            $locusStr2 = json_replace_key($locusStr1);
            $locusArr1 = json_decode("[" . $locusStr2 . "]", true);
            $locusList = $locusArr1[0]["devices"];
            if($locusList){
                $areaListStr = "[".$areaInfoStr."]";
                $areaListArr = json_decode($areaListStr,true);
                //根据轨迹二次确认
                foreach($locusList as $kkk => $vvv){
                    $locusPoint=array(
                        'lon'=>$vvv["baiduLng"],
                        'lat'=>$vvv["baiduLat"]
                    );
                    $isInLocusPoint = is_point_in_polygon($locusPoint, $areaListArr);
                    if($isInLocusPoint){
                        //在区域内
                        $updateData[$str1] = strtotime($vvv["deviceUtcDate"]);
                        $updateData[$str2] = $check_result;
                        $updateData[$str3] = 1;
                        M("Attendance")->where(array("id"=>$attendanceId))->data($updateData)->save();
                        return true;
                    }
                }
                return false;
            }else{
                return false;
            }
        }
    }
}
