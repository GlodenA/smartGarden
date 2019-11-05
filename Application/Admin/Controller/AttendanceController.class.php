<?php
// 考勤管理
namespace Admin\Controller;
class AttendanceController extends BaseController {
    public function __construct(){
        parent::__construct();
    }
//    考勤设置
    public function attendanceSetting(){
        if(IS_POST){
            $data = I("info");
            $data["uid"] = session("admin_uid");
            if(M("Attendance_setting")->find()){
                $data["update_time"] = time();
                if(M("Attendance_setting")->where(array("id"=>1))->save($data)){
                    $this->success("设置成功");
                }else{
                    $this->error("设置失败");
                }
            }else{
                $data["add_time"] = time();
                if(M("Attendance_setting")->add($data)){
                    $this->success("设置成功");
                }else{
                    $this->error("设置失败");
                }
            }
        }else{
            $settingInfo = M("Attendance_setting")->where(array("id"=>1))->find();
            $this->assign($settingInfo);
            $this->display("attendance_setting");
        }
    }
    //考勤列表
    public function attendanceList(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday = $beginToday + 24*60*60-1;
        $keywords = I('keywords');
        if($keywords){
            $where['m.job_number|m.realname'] = array('like', "%".$keywords."%");
            $this->assign('keywords',$keywords);
        }
        if(I('start_time')){
            $start_time = strtotime(I('start_time'));
            $this->assign('start_time',I('start_time'));
        }else{
            $start_time = $beginToday;
            $this->assign('start_time',date('Y-m-d', $start_time));
        }
        if(I('end_time')){
            $end_time = strtotime(I('end_time'))+24*3600-1;
            $this->assign('end_time',I('end_time'));
        }else{
            $end_time = $endToday;
            $this->assign('end_time',date('Y-m-d', $end_time));
        }
        if($start_time && $end_time){
            $where['a.add_time'] = array('between',array($start_time,$end_time));
        }elseif($start_time && !$end_time){
            $end_time = $start_time+24*3600-1;
            $where['a.add_time'] = array('between',array($start_time,$end_time));
        }elseif(!$start_time && $end_time){
            $start_time = $end_time-24*3600+1;
            $where['a.add_time'] = array('between',array($start_time,$end_time));

        }
        $status = I("status");
        if($status){
            $this->assign("status",$status);
            //上午
            if($status == 1){
                //上班打卡
                $where["a.first_result"] = 1;
            }
            if($status == 2){
                //上班迟到
                $where["a.first_result"] = 3;
            }
            if($status == 3){
                //上班旷工
                $where["a.first_result"] = 5;
            }
            if($status == 4){
                //上班早退
                $where["a.second_result"] = 4;
            }
            if($status == 5){
                //下班打卡
                $where["a.second_result"] = 2;
            }
            //下午
            if($status == 6){
                //上班打卡
                $where["a.third_result"] = 1;
            }
            if($status == 7){
                //上班迟到
                $where["a.third_result"] = 3;
            }
            if($status == 8){
                //上班旷工
                $where["a.third_result"] = 5;
            }
            if($status == 9){
                //上班早退
                $where["a.fourth_result"] = 4;
            }
            if($status == 10){
                //下班打卡
                $where["a.fourth_result"] = 2;
            }
            if($status == 11){
                //异常的
                $whereStatus["a.first_result"] = array("gt",1);
                $whereStatus["a.second_result"] = array("gt",2);
                $whereStatus["a.third_result"] = array("gt",1);
                $whereStatus["a.fourth_result"] = array("gt",2);
                $whereStatus['_logic'] = 'or';
                $where['_complex'] = $whereStatus;
            }
            if($status == 12){
                //上午异常
                $whereStatus["a.first_result"] = array("gt",1);
                $whereStatus["a.second_result"] = array("gt",2);
                $whereStatus['_logic'] = 'or';
                $where['_complex'] = $whereStatus;
            }
            if($status == 13){
                //下午异常
                $whereStatus["third_result"] = array("gt",1);
                $whereStatus["fourth_result"] = array("gt",2);
                $whereStatus['_logic'] = 'or';
                $where['_complex'] = $whereStatus;
            }
        }
        $where["a.is_delete"] = 0;
        $where["_string"] = "a.userid = m.userid";
        //查询职位列表
        $positionList = M("Member_position")->where(array("is_delete"=>0))->select();
        $ids = array();
        if($positionList){
            foreach($positionList as $k => $v){
                if($v["parent_id"]<1){
                    array_push($ids,$v["id"]);
                }
            }
        }
        $this->assign("positionList",$positionList);
        //查询职位列表
        $positionList = M("Member_position")->where(array("is_delete"=>0))->select();
        $this->assign("positionList",$positionList);
        $position = I("position");
        if($position){
            //根据当前所选职位，查询出相应直属领导列表
            $positionInfo = M("Member_position")->where(array("id"=>$position,"is_delete"=>0))->find();
            if($positionInfo["parent_id"]>0){
                //说明当前职位有其直属领导
                $managerList = M("Member")->where(array("position"=>$positionInfo["parent_id"],"is_delete"=>0))->field("userid,realname,job_number")->select();
                $this->assign("managerList",$managerList);
            }
            $this->assign("position",$position);
            $where["m.position"] = $position;
        }else{
            //获取负责人列表
            $wherePosition["mm.parent_id"] = 0;
            $wherePosition["mm.is_delete"] = 0;
            $wherePosition["mp.is_delete"] = 0;
            $wherePosition["_string"] = "mp.position = mm.id";
            $managerList = M("Member")->table("__MEMBER__ mp,__MEMBER_POSITION__ mm")->where($wherePosition)->field("userid,realname,job_number")->select();
            $this->assign("managerList",$managerList);
        }
        $parent_id = I("parent_id");
        if($parent_id){
            $this->assign("parent_id",$parent_id);
            $whereParent["m.parent_id"] = $parent_id;
            $wherePositon["m.userid"] = $parent_id;
            $whereMember["_complex"] = array($whereParent,$wherePositon,'_logic'=>'or');
            $where["_complex"] = $whereMember;
        }
        $count =M("Attendance")->table('__ATTENDANCE__ a,__MEMBER__ m')->where($where)->count();
//        var_dump(M("Attendance")->getLastSql());
        $Page = new \Think\Page($count,20);
        $list = M("Attendance")
            ->table('__ATTENDANCE__ a,__MEMBER__ m')
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field("a.*,m.realname,m.job_number,m.mobile,m.parent_id,m.position")
            ->order("a.add_time desc")
            ->select();
        foreach($list as $k=>$v){
            $warnMessage['type'] = array("lt",3);
            $warnMessage['uid'] = $v['userid'];
            $time = date('Y-m-d',$v['add_time']);
            $start = strtotime($time);
            $end = $start+24*3600-1;
            $warnMessage['add_time'] = array('between',array($start,$end));
            $list[$k]['leaveTimeCount'] = M('Warning_message')->where($warnMessage)->count();
            if($v["parent_id"]>0){
                $list[$k]["parent_name"] = M("Member")->where(array("userid"=>$v["parent_id"],"is_delete"=>0))->getField("realname");
            }else{
                $list[$k]["parent_name"] ="";
            }
            if($v["position"]>0){
                $list[$k]["position_name"] = M("Member_position")->where(array("id"=>$v["position"],"is_delete"=>0))->getField("name");
            }else{
                $list[$k]["position_name"] ="";
            }
        }
        $number = $Page->parameter["p"];
        if($number && $number > 0){
            $number = ($Page->parameter["p"] - 1)*20;
        }else{
            $number = 0;
        }
        $this->assign('number',$number);
        $this->assign("page",$Page->show());
        $this->assign("list",$list);
        $this->display("attendance_list");
    }

    //导出考勤列表
    public function exportAttendanceList(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday = $beginToday + 24*60*60-1;
        $keywords = I('keywords');
        if($keywords){
            $where['m.job_number|m.realname'] = array('like', "%".$keywords."%");
        }
        if(I('start_time')){
            $start_time = strtotime(I('start_time'));
        }else{
            $start_time = $beginToday;
        }
        if(I('end_time')){
            $end_time = strtotime(I('end_time'))+24*3600-1;
        }else{
            $end_time = $endToday;
        }
        if($start_time && $end_time){
            $where['a.add_time'] = array('between',array($start_time,$end_time));
        }elseif($start_time && !$end_time){
            $end_time = $start_time+24*3600-1;
            $where['a.add_time'] = array('between',array($start_time,$end_time));
        }elseif(!$start_time && $end_time){
            $start_time = $end_time-24*3600+1;
            $where['a.add_time'] = array('between',array($start_time,$end_time));

        }
        //考勤表列处理
        $headArr = array("员工号","姓名","养护经理");
        $dateArr1 = $this->prDates(date('Y-m-d', $start_time),date('Y-m-d', $end_time));
        foreach($dateArr1 as $d =>$dd){
            array_push($headArr,$dd);
        }
        //处理有考勤的日期
        $headArrCount = count($headArr);
        if($headArrCount>31){
            $this->error("最多导出一个月的考勤汇总，请重新选择");
        }
        $status = I("status");
        if($status){
            //上午
            if($status == 1){
                //上班打卡
                $where["a.first_result"] = 1;
            }
            if($status == 2){
                //上班迟到
                $where["first_result"] = 3;
            }
            if($status == 3){
                //上班旷工
                $where["a.first_result"] = 5;
            }
            if($status == 4){
                //上班早退
                $where["a.second_result"] = 4;
            }
            if($status == 5){
                //下班打卡
                $where["a.second_result"] = 2;
            }
            //下午
            if($status == 6){
                //上班打卡
                $where["a.third_result"] = 1;
            }
            if($status == 7){
                //上班迟到
                $where["a.third_result"] = 3;
            }
            if($status == 8){
                //上班旷工
                $where["a.third_result"] = 5;
            }
            if($status == 9){
                //上班早退
                $where["a.fourth_result"] = 4;
            }
            if($status == 10){
                //下班打卡
                $where["a.fourth_result"] = 2;
            }
            if($status == 11){
                //异常的
                $whereStatus["a.first_result"] = array("gt",1);
                $whereStatus["a.second_result"] = array("gt",2);
                $whereStatus["a.third_result"] = array("gt",1);
                $whereStatus["a.fourth_result"] = array("gt",2);
                $whereStatus['_logic'] = 'or';
                $where['_complex'] = $whereStatus;
            }
            if($status == 12){
                //上午异常
                $whereStatus["a.first_result"] = array("gt",1);
                $whereStatus["a.second_result"] = array("gt",2);
                $whereStatus['_logic'] = 'or';
                $where['_complex'] = $whereStatus;
            }
            if($status == 13){
                //下午异常
                $whereStatus["third_result"] = array("gt",1);
                $whereStatus["fourth_result"] = array("gt",2);
                $whereStatus['_logic'] = 'or';
                $where['_complex'] = $whereStatus;
            }
        }
        $where["a.is_delete"] = 0;
        $where["_string"] = "a.userid = m.userid";
        //查询职位列表
        $position = I("position");
        if($position){
            $where["m.position"] = $position;
        }
        $parent_id = I("parent_id");
        if($parent_id){
            $whereParent["m.parent_id"] = $parent_id;
            $wherePositon["m.userid"] = $parent_id;
            $whereMember["_complex"] = array($whereParent,$wherePositon,'_logic'=>'or');
            $where["_complex"] = $whereMember;
        }
        $list = M("Attendance")->table('__ATTENDANCE__ a,__MEMBER__ m')->where($where)->field("a.*,m.job_number,m.realname,m.position,m.parent_id")->order("m.parent_id asc,m.job_number asc")->select();
        if(!$list){
            $this->error("暂无数据导出");
        }
        $attList =array();
        foreach($list as $k=>$v){
            if($v["first_result"] == 1 && $v["second_result"] == 2 && $v["third_result"] == 1 && $v["fourth_result"] == 2){
                $list[$k]['result'] = '正常';
            }elseif($v["first_result"] == 0 && $v["second_result"] == 0 && $v["third_result"] == 0 && $v["fourth_result"] == 0){
                $list[$k]['result'] = '旷工';
            }else{
                if($status == 11){
                    //异常的
                    if(($v["first_result"] == 5 || $v["first_result"] == 0) && $v["second_result"] == 0){
                        $list[$k]['result'] = $list[$k]['result']."上午旷工\n";
                    }else{
                        if($v["first_result"] == 1){
                            $list[$k]['result'] = "";
                        }elseif($v["first_result"] == 3){
                            $list[$k]['result'] = "上班1迟到 ";
                        }elseif($v["first_result"] == 5){
                            $list[$k]['result'] = "上班1旷工 ";
                        }else{
                            $list[$k]['result'] = "上班1缺卡 ";
                        }
                        if($v["second_result"] == 2){
                            $list[$k]['result'] = $list[$k]['result']."";
                        }elseif($v["second_result"] == 4){
                            $list[$k]['result'] = $list[$k]['result']."下班1早退\n";
                        }else{
                            $list[$k]['result'] = $list[$k]['result']."下班1缺卡\n";
                        }
                    }
                    if(($v["third_result"] == 5 || $v["third_result"] == 0) && $v["fourth_result"] == 0){
                        $list[$k]['result'] = $list[$k]['result']."下午旷工";
                    }else{
                        if($v["third_result"] == 1){
                            $list[$k]['result'] =$list[$k]['result']."";
                        }elseif($v["third_result"] == 3){
                            $list[$k]['result'] = $list[$k]['result']."上班2迟到 ";
                        }elseif($v["third_result"] == 5){
                            $list[$k]['result'] = $list[$k]['result']."上班2旷工 ";
                        }else{
                            $list[$k]['result'] = $list[$k]['result']."上班2缺卡 ";
                        }
                        if($v["fourth_result"] == 2){
                            $list[$k]['result'] = $list[$k]['result']."";
                        }elseif($v["fourth_result"] == 4){
                            $list[$k]['result'] = $list[$k]['result']."下班2早退";
                        }else{
                            $list[$k]['result'] = $list[$k]['result']."下班2缺卡";
                        }
                    }
                }elseif($status == 12){
                    //上午异常
                    if(($v["first_result"] == 5 || $v["first_result"] == 0) && $v["second_result"] == 0){
                        $list[$k]['result'] = $list[$k]['result']."上午旷工\n";
                    }else{
                        if($v["first_result"] == 1){
                            $list[$k]['result'] = "";
                        }elseif($v["first_result"] == 3){
                            $list[$k]['result'] = "上班1迟到 ";
                        }elseif($v["first_result"] == 5){
                            $list[$k]['result'] = "上班1旷工 ";
                        }else{
                            $list[$k]['result'] = "上班1缺卡 ";
                        }
                        if($v["second_result"] == 2){
                            $list[$k]['result'] = $list[$k]['result']."";
                        }elseif($v["second_result"] == 4){
                            $list[$k]['result'] = $list[$k]['result']."下班1早退\n";
                        }else{
                            $list[$k]['result'] = $list[$k]['result']."下班1缺卡\n";
                        }
                    }
                }elseif($status == 13){
                    //下午异常
                    if(($v["third_result"] == 5 || $v["third_result"] == 0) && $v["fourth_result"] == 0){
                        $list[$k]['result'] = $list[$k]['result']."下午旷工";
                    }else{
                        if($v["third_result"] == 1){
                            $list[$k]['result'] =$list[$k]['result']."";
                        }elseif($v["third_result"] == 3){
                            $list[$k]['result'] = $list[$k]['result']."上班2迟到 ";
                        }elseif($v["third_result"] == 5){
                            $list[$k]['result'] = $list[$k]['result']."上班2旷工 ";
                        }else{
                            $list[$k]['result'] = $list[$k]['result']."上班2缺卡 ";
                        }
                        if($v["fourth_result"] == 2){
                            $list[$k]['result'] = $list[$k]['result']."";
                        }elseif($v["fourth_result"] == 4){
                            $list[$k]['result'] = $list[$k]['result']."下班2早退";
                        }else{
                            $list[$k]['result'] = $list[$k]['result']."下班2缺卡";
                        }
                    }
                }else{
                    if(($v["first_result"] == 5 || $v["first_result"] == 0) && $v["second_result"] == 0){
                        $list[$k]['result'] = $list[$k]['result']."上午旷工\n";
                    }else{
                        if($v["first_result"] == 1){
                            $list[$k]['result'] = "上班1正常 ";
                        }elseif($v["first_result"] == 3){
                            $list[$k]['result'] = "上班1迟到 ";
                        }elseif($v["first_result"] == 5){
                            $list[$k]['result'] = "上班1旷工 ";
                        }else{
                            $list[$k]['result'] = "上班1缺卡 ";
                        }
                        if($v["second_result"] == 2){
                            $list[$k]['result'] = $list[$k]['result']."下班1正常\n";
                        }elseif($v["second_result"] == 4){
                            $list[$k]['result'] = $list[$k]['result']."下班1早退\n";
                        }else{
                            $list[$k]['result'] = $list[$k]['result']."下班1缺卡\n";
                        }
                    }
                    if(($v["third_result"] == 5 || $v["third_result"] == 0) && $v["fourth_result"] == 0){
                        $list[$k]['result'] = $list[$k]['result']."下午旷工";
                    }else{
                        if($v["third_result"] == 1){
                            $list[$k]['result'] =$list[$k]['result']."上班2正常 ";
                        }elseif($v["third_result"] == 3){
                            $list[$k]['result'] = $list[$k]['result']."上班2迟到 ";
                        }elseif($v["third_result"] == 5){
                            $list[$k]['result'] = $list[$k]['result']."上班2旷工 ";
                        }else{
                            $list[$k]['result'] = $list[$k]['result']."上班2缺卡 ";
                        }
                        if($v["fourth_result"] == 2){
                            $list[$k]['result'] = $list[$k]['result']."下班2正常";
                        }elseif($v["fourth_result"] == 4){
                            $list[$k]['result'] = $list[$k]['result']."下班2早退";
                        }else{
                            $list[$k]['result'] = $list[$k]['result']."下班2缺卡";
                        }
                    }
                    if((($v["first_result"] == 5 || $v["first_result"] == 0) && $v["second_result"] == 0) && (($v["third_result"] == 5 || $v["third_result"] == 0) && $v["fourth_result"] == 0)){
                        $list[$k]['result'] = "旷工";
                    }
                }
            }
//            $warnMessage['type'] = array("lt",3);
//            $warnMessage['uid'] = $v['userid'];
//            $time = date('Y-m-d',$v["add_time"]);
//            $start_time = strtotime($time);
//            $end_time = $start_time+24*3600-1;
//            $warnMessage['add_time'] = array('between',array($start_time,$end_time));
//            $list[$k]['leaveTimeCount'] = M('Warning_message')->where($warnMessage)->count();
            if($v["remark"]){
                $list[$k]['result'] = $list[$k]['result']."\n".$v["remark"];
            }
            if($v["parent_id"]>0){
                $list[$k]["parent_name"] = M("Member")->where(array("userid"=>$v["parent_id"],"is_delete"=>0))->getField("realname");
            }else{
                $list[$k]["parent_name"] ="";
            }
        }
        foreach($list as $lii=>$liii){
            $attList[$liii['job_number']][] = $liii;
        }
        //考勤日期处理
        $num = 0;
        foreach($attList as $a=>$aa){
            //$dateNum代表列的日期开始列
            $dataNum = 3;
            foreach($aa as $b=>$bb){
                if($dataNum<$headArrCount){
                    $data[$num]["job_number"] = $bb["job_number"];
                    $data[$num]["realname"] = $bb["realname"];
                    $data[$num]["parent_name"] = $bb["parent_name"];
                    $data[$num][$headArr[$dataNum]] = $bb["result"];
                    $dataNum++;
                }
            }
            $num++;
        }
//        exit;
//        $headArr=array("员工号","姓名","考勤日期","上午上班打卡时间","打卡结果","上午下班打卡时间","打卡结果","下午上班打卡时间","打卡结果","下午下班打卡时间","打卡结果","备注");
        // 单元格宽度(必须使用"A"=>30的形式)
//        $widthExcel = array('A'=>20,'B'=>20,'C'=>20,'D'=>30,'E'=>20,'F'=>30,'G'=>20,'H'=>30,"I"=>20,"J"=>30,"K"=>20,"M"=>50);
//        adminLog("导出考勤列表");
        $sheetName = "考勤汇总";
        $this->getExcel($start_time,$end_time,$sheetName,$headArr,$data);
    }

    function prDates($start,$end){
        $dt_start = strtotime($start);
        $dt_end = strtotime($end);
        $dateArr = array();
        while ($dt_start<=$dt_end){
            array_push($dateArr,date('Y-m-d',$dt_start));
            $dt_start = strtotime('+1 day',$dt_start);
        }
        return $dateArr;
    }
    /**
     * excel导出 chen
     * @param $fileName 导出的文件名
     * @param $headArr excel头
     * @param $data 数据
     */
    function getExcel($start_time,$end_time,$sheetName,$headArr,$data){
        vendor("PHPExcel.PHPExcel");
        //对数据进行检验
        if(empty($data) || !is_array($data)){
            $this->error("暂无可导出数据");
        }
        $fileName = "考勤汇总".date("Y_m_d",$start_time);
        $date = date("Y_m_d",$end_time);
        $fileName .= "--{$date}.xls";
        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        $objPHPExcel->getActiveSheet(0)->setTitle($sheetName);   //设置sheet名称
        $styleThinBlackBorderOutline = array(
            'borders' => array(
                'allborders' => array( //设置全部边框
                    'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
                ),
            ),
        );
        $widthExcel = array('A'=>11,'B'=>15,'C'=>15);
        // 设置宽度
        if($widthExcel){
            foreach ($widthExcel as $k => $v) {
                $objPHPExcel->getActiveSheet(0)->getColumnDimension($k)->setWidth($v);
            }
        }
        $_row = 1;   //设置纵向单元格标识
        if($headArr){
            $_cnt = count($headArr);
            $objPHPExcel->getActiveSheet(0)->mergeCells('A'.$_row.':'.$cellName[$_cnt-1].$_row);   //合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$_row, '考勤汇总表  统计日期： '.date('Y-m-d', $start_time).' 至 '.date('Y-m-d', $end_time));  //设置合并后的单元格内容
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getFont()->setSize(18);
            //设置数据居中
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getAlignment()->setWrapText(true);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //设置单元格高度
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension($_row)->setRowHeight(50);
            $_row++;
            $objPHPExcel->getActiveSheet(0)->mergeCells('A'.$_row.':'.$cellName[$_cnt-1].$_row);   //合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$_row, '报表生成时间： '.date('Y-m-d H:i',time()).'        分管部室负责人：        ');  //设置合并后的单元格内容
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension($_row)->setRowHeight(30);
            $_row++;
            $i = 0;
            foreach($headArr AS $v){
                //设置列标题
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].$_row, $v);
                if($i>2){
                    $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($cellName[$i])->setWidth(25);//设置列宽度
                }
                //设置数据居中
                $objPHPExcel->setActiveSheetIndex(0)->getStyle($cellName[$i].$_row)->getAlignment()->setWrapText(true);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle($cellName[$i].$_row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->setActiveSheetIndex(0)->getStyle($cellName[$i].$_row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $i++;
            }
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension($_row)->setRowHeight(30);
            $_row++;
        }
        //填写数据
        if($data){
            $k = 0;
            foreach($data AS $_v){
                $j = 0;
                foreach($_v AS $_cell){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($k+$_row), $_cell);
                    //设置单元格高度
                    $objPHPExcel->setActiveSheetIndex(0)->getRowDimension($k+$_row)->setRowHeight(30);
                    //设置数据居中
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle($cellName[$j] . ($k+$_row))->getAlignment()->setWrapText(true);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle($cellName[$j] . ($k+$_row))->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->setActiveSheetIndex(0)->getStyle($cellName[$j] . ($k+$_row))->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $j++;
                }
                $k++;
            }
        }
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$cellName[count($headArr)-1].($k+$_row-1))->applyFromArray($styleThinBlackBorderOutline);
        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        // $objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();
        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        // memberLog("数据导出");
        exit;
    }
    //离岗详情
    public function leaveInfo(){
        $where['uid'] = I('userid');
        $add_time = I('add_time');
        $time = date('Y-m-d',$add_time);
        $start_time = strtotime($time);
        $end_time = $start_time+24*3600-1;
        $where['add_time'] = array('between',array($start_time,$end_time));
        $where['type'] = array("lt",3);
        $leaveInfo = M('Warning_message')->where($where)->select();
        $whereAttendance['add_time'] = array('between',array($start_time,$end_time));
        $whereAttendance['is_delete'] = 0;
        $whereAttendance['userid'] = I("userid");
        $attentionInfo = M("Attendance")->where($whereAttendance)->find();
        if($attentionInfo["second_result"] == 4 || $attentionInfo["fourth_result"] == 4){
            if($leaveInfo){
                foreach($leaveInfo as $k=>$v){
                    if($v["add_time"] == $attentionInfo["second_time"] || $v["add_time"] == $attentionInfo["fourth_time"]){
                        $leaveInfo[$k]["end_time"] = "";
                    }
                }
            }
        }
        $this->assign('leaveInfo',$leaveInfo);
        $this->display('attendance_leave');
    }

    //添加备注
    public function attendanceRemark(){
        if(IS_POST){
            $where['id'] = I('id');
            $data['remark'] = I('remark');
            M('Attendance')->where($where)->save($data);
            $this->success('备注设置成功');
        }else{
            $attendance_id = I('id');
            $where['a.id'] = $attendance_id;
            $where["_string"] = "a.userid = m.userid and m.machine_id = mm.machine_id";
            $attendanceInfo = M("Attendance")
                ->table("__ATTENDANCE__ a,__MEMBER__ m,__MACHINE__ mm")
                ->where($where)
                ->field("a.*,m.realname,m.job_number,m.machine_id,mm.machine_imei")
                ->find();
            $this->assign('attendanceInfo',$attendanceInfo);
            $this->display('attendance_remark');
        }

    }
    //请假申请
    public function leaveApply(){
        if(IS_POST){
            $info = I("info");
            $data["add_time"] = time();
            $data["leave_start_time"] = strtotime($info["start_time"]);
            $data["leave_end_time"] = strtotime($info["end_time"]);
            $where["userid"] = $data["userid"] = $info["userid"];
            $where["is_delete"] = 0;
            $time = substr($info["start_time"],0,10)." 00:00:00";
            $timeStr = strtotime($time);
            $where["leave_start_time"] =array("gt",$timeStr);
            if(M("Leave")->where($where)->find()){
                $this->error("当前员工已请假");
            }else{
                $result = M("Leave")->add($data);
                if($result){
                    $attendanceData["machine_id"] = $info["machine_id"];
                    $attendanceData["userid"] = $info["userid"];
                    $attendanceData["leave_id"] = $result;
                    $attendanceData["add_time"] = $attendanceData["update_time"] = $data["leave_start_time"];
                    $attendanceData["remark"] = "请假";
                    M("Attendance")->add($attendanceData);
                    $this->success("提交成功");
                }else{
                    $this->error("提交失败");
                }
            }
        }else{
            $this->display('leave_apply');
        }
    }
    //请假申请列表
    public function leaveList(){
        $keywords = I('keywords');
        if($keywords){
            $where['m.job_number|m.realname'] = array('like', "%".$keywords."%");
            $this->assign('keywords',$keywords);
        }
        if(I('start_time')){
            $start_time = strtotime(I('start_time'));
        }
        if(I('end_time')){
            $end_time = strtotime(I('end_time'))+24*3600-1;
        }
        if($start_time && $end_time){
            $where['l.leave_start_time'] = array('between',array($start_time,$end_time));
            $this->assign('start_time',I('start_time'));
            $this->assign('end_time',I('end_time'));
        }elseif($start_time && !$end_time){
            $end_time = $start_time+24*3600-1;
            $where['l.leave_start_time'] = array('between',array($start_time,$end_time));
            $this->assign('start_time',I('start_time'));
            $this->assign('end_time',I('start_time'));
        }elseif(!$start_time && $end_time){
            $start_time = $end_time-24*3600+1;
            $where['l.leave_start_time'] = array('between',array($start_time,$end_time));
            $this->assign('start_time',I('end_time'));
            $this->assign('end_time',I('end_time'));
        }
//        $start_time = strtotime(I('start_time'));
//        $end_time = strtotime(I('end_time'))+24*3600-1;
//        if($start_time && $end_time){
//            $where['l.leave_start_time'] = array('between',array($start_time,$end_time));
//            $this->assign('start_time',I('start_time'));
//            $this->assign('end_time',I('end_time'));
//        }
        $where["l.is_delete"] = $where["m.is_delete"] = 0;
        $where["_string"] = "m.userid = l.userid";
        $count = M("Leave")->table("__LEAVE__ l,__MEMBER__ m")->where($where)->count();
        $Page = new \Think\Page($count,20);
        $list = M("Leave")->table("__LEAVE__ l,__MEMBER__ m")->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field("l.*,m.realname,m.job_number,m.mobile")
            ->order("l.add_time desc")
            ->select();
        if($list){
            $this->assign("page",$Page->show());
            $this->assign("list",$list);
        }
        $this->display('leave_list');
    }
    //获取员工信息
    public function getMemberInfo(){
        $keywords = I("keywords");
        $where["job_number|mobile"] = $keywords;
        $where["is_delete"] = 0;
        $userInfo = M("Member")->where($where)->find();
        if($userInfo){
            $ret["status"] = 1;
            $ret["info"] = "获取成功";
            $ret["data"] = $userInfo;
        }else{
            $ret["status"] = 0;
            $ret["info"] = "暂无此员工";
        }
        $this->ajaxReturn($ret);
    }
    //请假删除
    public function leaveDelete(){
        $leaveId = I("id");
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $info = M("Leave")->where(array("id"=>$leaveId))->find();
        if($info["leave_end_time"]<$endToday && $info["leave_start_time"] > $beginToday){
            $this->error("当天的请假无法删除");
        }
        if(M("Leave")->where(array("id"=>$leaveId))->setField("is_delete",1)){
            M("Attendance")->where(array("leave_id"=>$leaveId,"userid"=>$info["userid"],"is_delete"=>0))->setField("is_delete",1);
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
}