<?php
/**
 * Created by PhpStorm.
 * User: cjw设备管理
 * Date: 2018-11-16
 * Time: 15:25
 */
namespace Admin\Controller;
use http\Message;
use Think\Log;
class MachineController extends BaseController{
    public function __construct(){
        parent::__construct();
    }
    //设备列表
    public function machineList(){
        if(I("keywords")){
            $where['c.machine_imei|m.realname'] = array('like', '%'.I('keywords').'%');
            $this->assign("keywords", I("keywords"));
        }
        $machine_status = I("machine_status");
        if($machine_status){
            if($machine_status == 1){
                //在线
                $where['c.machine_status'] = array(array("eq","Stop"),array("eq","Move"),"or");
            }
            if($machine_status == 2){
                //离线
                $where['c.machine_status'] = array("eq","Offline");
            }
            $this->assign("machine_status", $machine_status);
        }
        $where["c.is_delete"] = 0;
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
        $count =M("Machine")->alias('c')->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")->where($where)->count();
        $Page = new \Think\Page($count,30);
        $machineList = M("Machine")
            ->alias('c')
            ->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field("c.*,m.realname,m.job_number,m.mobile,m.parent_id,m.position")
            ->order("c.add_time desc")
            ->select();
        if($machineList){
//            $work_day = '';
            foreach($machineList as $k=>$v){
                $machineList[$k]['schedulesInfo'] = M("Schedules_setting")->where(array('schedules_id'=>$v['schedules_id']))->find();
//                if($v['schedules_id'] > 0){
//                    $machineList[$k]['schedulesInfo']['work_day'] = explode(",",$machineList[$k]['schedulesInfo']['work_day']);
//                    if($machineList[$k]['schedulesInfo']['work_day'] || $machineList[$k]['schedulesInfo']['work_day'] == 0){
//                        foreach ($machineList[$k]['schedulesInfo']['work_day'] as $kkk => $vvv) {
//                            if($vvv == 1){
//                                $work_day = $work_day."周一";
//                            }elseif($vvv == 2){
//                                $work_day = $work_day." 周二";
//                            }elseif($vvv == 3){
//                                $work_day = $work_day." 周三";
//                            }elseif($vvv == 4){
//                                $work_day = $work_day." 周四";
//                            }elseif($vvv == 5){
//                                $work_day = $work_day." 周五";
//                            }elseif($vvv == 6){
//                                $work_day = $work_day." 周六";
//                            }elseif($vvv == 0){
//                                $work_day = $work_day." 周日";
//                            }
//                        }
//                    }
//                    $machineList[$k]['schedulesInfo']['work_day'] = $work_day;
//                    $work_day = '';
//                    $machineList[$k]['schedulesInfo']['time_id'] = explode(",",$machineList[$k]['schedulesInfo']['time_id']);
//                    $machineList[$k]['schedulesInfo']['timeList'] = array();
//                    foreach ($machineList[$k]['schedulesInfo']['time_id'] as $kk => $vv) {
//                        $arr = M("Schedules_time")->where(array('is_delete'=>0,'id'=>$vv))->find();
//                        if($arr){
//                            $machineList[$k]['schedulesInfo']['timeList'][] = $arr;
//                        }
//                    }
//                }
                if($v['area_id']){
                    $machineList[$k]['area_name'] = M('Area_map')->where(array('id'=>$v['area_id']))->getField('area_name');
                }
                if($v["parent_id"]>0){
                    $machineList[$k]["parent_name"] = M("Member")->where(array("userid"=>$v["parent_id"],"is_delete"=>0))->getField("realname");
                }else{
                    $machineList[$k]["parent_name"] ="";
                }
                if($v["position"]>0){
                    $machineList[$k]["position_name"] = M("Member_position")->where(array("id"=>$v["position"],"is_delete"=>0))->getField("name");
                }else{
                    $machineList[$k]["position_name"] ="";
                }
                if($v["machine_status"] == "Offline"){
                    $machineList[$k]["machine_status"] = "离线";
                }elseif($v["machine_status"] == "Stop" || $v["machine_status"] == "Move"){
                    $machineList[$k]["machine_status"] = "在线";
                }else{
                    $machineList[$k]["machine_status"] = "未知";
                }
            }
        }
        $number = $Page->parameter["p"];
        if($number && $number > 0){
            $number = ($Page->parameter["p"] - 1)*30;
        }else{
            $number = 0;
        }

        $this->assign('number',$number);
        $this->assign("page",$Page->show());
        $this->assign("machineList",$machineList);
        $this->display("machine_list");
    }
    //今日在线设备列表
    public function machineOnLineList(){
        //获取时间戳
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday = $beginToday + 24*60*60-1;
        //当前设备数
        if(I("keywords")){
            $where['c.machine_imei|m.realname'] = array('like', '%'.I('keywords').'%');
            $this->assign("keywords", I("keywords"));
        }
        $machine_status = I("machine_status");
        if($machine_status){
            if($machine_status == 1){
                //在线
                $where['c.machine_status'] = array(array("eq","Stop"),array("eq","Move"),"or");
            }
            if($machine_status == 2){
                //离线
                $where['c.machine_status'] = array("eq","Offline");
            }
            $this->assign("machine_status", $machine_status);
        }
        $where["c.login_time"] = array("gt",$beginToday);
        $where["c.is_delete"] = 0;
//        $count = M("Site_log")->table("__SITE_LOG__ s,__MACHINE__ c")->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")->where($where)->count('distinct(machine_imei)');
        // $where["_string"] = "s.machine_imei = c.machine_imei";
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
        $count = M("Machine")->alias("c")->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")->where($where)->count();
        $Page = new \Think\Page($count,30);
        $machineList = M("Machine")
            ->alias("c")
            ->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")
            ->where($where)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field("c.*,m.realname,m.job_number,m.mobile,m.position,m.parent_id")
            ->order("c.update_time desc,c.add_time desc")
            ->select();
        if($machineList){
//            $work_day = '';
            foreach($machineList as $k=>$v){
                $machineList[$k]['schedulesInfo'] = M("Schedules_setting")->where(array('schedules_id'=>$v['schedules_id']))->find();
                if($v['schedules_id'] > 0){
                    $machineList[$k]['schedulesInfo']['work_day'] = explode(",",$machineList[$k]['schedulesInfo']['work_day']);
//                    if($machineList[$k]['schedulesInfo']['work_day'] || $machineList[$k]['schedulesInfo']['work_day'] == 0){
//                        foreach ($machineList[$k]['schedulesInfo']['work_day'] as $kkk => $vvv) {
//                            if($vvv == 1){
//                                $work_day = $work_day."周一";
//                            }elseif($vvv == 2){
//                                $work_day = $work_day." 周二";
//                            }elseif($vvv == 3){
//                                $work_day = $work_day." 周三";
//                            }elseif($vvv == 4){
//                                $work_day = $work_day." 周四";
//                            }elseif($vvv == 5){
//                                $work_day = $work_day." 周五";
//                            }elseif($vvv == 6){
//                                $work_day = $work_day." 周六";
//                            }elseif($vvv == 0){
//                                $work_day = $work_day." 周日";
//                            }
//                        }
//                    }
//                    $machineList[$k]['schedulesInfo']['work_day'] = $work_day;
//                    $work_day = '';
//                    $machineList[$k]['schedulesInfo']['time_id'] = explode(",",$machineList[$k]['schedulesInfo']['time_id']);
//                    $machineList[$k]['schedulesInfo']['timeList'] = array();
//                    foreach ($machineList[$k]['schedulesInfo']['time_id'] as $kk => $vv) {
//                        $arr = M("Schedules_time")->where(array('is_delete'=>0,'id'=>$vv))->find();
//                        if($arr){
//                            $machineList[$k]['schedulesInfo']['timeList'][] = $arr;
//                        }
//
//                    }
                }
                if($v['area_id']){
                    $machineList[$k]['area_name'] = M('Area_map')->where(array('id'=>$v['area_id']))->getField('area_name');
                }
                if($v["parent_id"]>0){
                    $machineList[$k]["parent_name"] = M("Member")->where(array("userid"=>$v["parent_id"],"is_delete"=>0))->getField("realname");
                }else{
                    $machineList[$k]["parent_name"] ="";
                }
                if($v["position"]>0){
                    $machineList[$k]["position_name"] = M("Member_position")->where(array("id"=>$v["position"],"is_delete"=>0))->getField("name");
                }else{
                    $machineList[$k]["position_name"] ="";
                }
                if($v["machine_status"] == "Offline"){
                    $machineList[$k]["machine_status"] = "离线";
                }elseif($v["machine_status"] == "Stop" || $v["machine_status"] == "Move"){
                    $machineList[$k]["machine_status"] = "在线";
                }else{
                    $machineList[$k]["machine_status"] = "未知";
                }
            }
        }
        $number = $Page->parameter["p"];
        if($number && $number > 0){
            $number = ($Page->parameter["p"] - 1)*30;
        }else{
            $number = 0;
        }
        $this->assign('number',$number);
        $this->assign("page",$Page->show());
        $this->assign("machineList",$machineList);
        $this->display("machine_online_list");
    }
    //当前没有轨迹的设备列表
    public function machineOffLineList(){
        //获取时间戳
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday = $beginToday + 24*60*60-1;
        $where["s.add_time"] = array(array("gt",$beginToday),array("lt",$endToday));
        $where["c.is_delete"] = 0;
        $where["_string"] = "s.machine_imei = c.machine_imei";
        $machineOnList = M("Site_log")
            ->table("__SITE_LOG__ s,__MACHINE__ c")
            ->where($where)
            ->field("s.*,c.machine_id")
            ->group("s.machine_imei")
            ->select();
        //地图上显示的设备
        $arrOff = array();
        if($machineOnList){
            foreach($machineOnList as $kk => $vv){
                array_push($arrOff,$vv["machine_id"]);
            }
            if(count($arrOff)>0){
                $whereOff["c.machine_id"] = array("not in",$arrOff);
            }
        }
        if(I("keywords")){
            $whereOff['c.machine_imei|m.realname'] = array('like', '%'.I('keywords').'%');
            $this->assign("keywords", I("keywords"));
        }
        $whereOff["c.is_delete"] = 0;
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
            $whereOff["m.position"] = $position;
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
            $whereOff["_complex"] = $whereMember;
        }
        $count =M("Machine")->alias('c')->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")->where($whereOff)->count();
        $Page = new \Think\Page($count,30);
        $machineList = M("Machine")
            ->alias('c')
            ->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")
            ->where($whereOff)
            ->limit($Page->firstRow.','.$Page->listRows)
            ->field("c.*,m.realname,m.job_number,m.mobile,m.position,m.parent_id")
            ->order("c.update_time desc,c.add_time desc")
            ->select();
        if($machineList){
            $work_day = '';
            foreach($machineList as $k=>$v){
                $machineList[$k]['schedulesInfo'] = M("Schedules_setting")->where(array('schedules_id'=>$v['schedules_id']))->find();
                if($v['schedules_id'] > 0){
                    $machineList[$k]['schedulesInfo']['work_day'] = explode(",",$machineList[$k]['schedulesInfo']['work_day']);
                    if($machineList[$k]['schedulesInfo']['work_day'] || $machineList[$k]['schedulesInfo']['work_day'] == 0){
                        foreach ($machineList[$k]['schedulesInfo']['work_day'] as $kkk => $vvv) {
                            if($vvv == 1){
                                $work_day = $work_day."周一";
                            }elseif($vvv == 2){
                                $work_day = $work_day." 周二";
                            }elseif($vvv == 3){
                                $work_day = $work_day." 周三";
                            }elseif($vvv == 4){
                                $work_day = $work_day." 周四";
                            }elseif($vvv == 5){
                                $work_day = $work_day." 周五";
                            }elseif($vvv == 6){
                                $work_day = $work_day." 周六";
                            }elseif($vvv == 0){
                                $work_day = $work_day." 周日";
                            }
                        }
                    }
                    $machineList[$k]['schedulesInfo']['work_day'] = $work_day;
                    $work_day = '';
                    $machineList[$k]['schedulesInfo']['time_id'] = explode(",",$machineList[$k]['schedulesInfo']['time_id']);
                    $machineList[$k]['schedulesInfo']['timeList'] = array();
                    foreach ($machineList[$k]['schedulesInfo']['time_id'] as $kk => $vv) {
                        $arr = M("Schedules_time")->where(array('is_delete'=>0,'id'=>$vv))->find();
                        if($arr){
                            $machineList[$k]['schedulesInfo']['timeList'][] = $arr;
                        }

                    }
                }
                if($v['area_id']){
                    $machineList[$k]['area_name'] = M('Area_map')->where(array('id'=>$v['area_id']))->getField('area_name');
                }
                if($v["parent_id"]>0){
                    $machineList[$k]["parent_name"] = M("Member")->where(array("userid"=>$v["parent_id"],"is_delete"=>0))->getField("realname");
                }else{
                    $machineList[$k]["parent_name"] ="";
                }
                if($v["position"]>0){
                    $machineList[$k]["position_name"] = M("Member_position")->where(array("id"=>$v["position"],"is_delete"=>0))->getField("name");
                }else{
                    $machineList[$k]["position_name"] ="";
                }
            }
        }
        $number = $Page->parameter["p"];
        if($number && $number > 0){
            $number = ($Page->parameter["p"] - 1)*30;
        }else{
            $number = 0;
        }
        $this->assign('number',$number);
        $this->assign("page",$Page->show());
        $this->assign("machineList",$machineList);
        $this->display("machine_offline_list");
    }
    //添加设备
    public function machineAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $data["add_time"] = $data["update_time"] = time();
            $data["uid"] = session("admin_uid");
            $info = M("Machine")->where(array("machine_imei"=>$data["machine_imei"],"is_delete"=>0))->find();
            if($info){
                $this->error("该设备已存在");
            }
            $result = M("Machine")->add($data);
            if($result){
                adminLog("设备添加，id为".$result);
                $this->success("添加成功");
            }else{
                $this->error("添加失败");
            }
        }else{
            $this->display("machine_add");
        }
    }
    //设备编辑
    public function machineEdit(){
        if(IS_POST){
            $data = $_POST["info"];
            $where['machine_id'] = I('machine_id');
            $data["update_time"] = time();
            $result = M("Machine")->where($where)->save($data);
            if($result){
                adminLog("设备添加，id为".$where["machine_id"]);
                $this->success("编辑成功");
            }else{
                $this->error("编辑失败");
            }
        }else{
            $where["c.machine_id"] = $_GET["machine_id"];
            $machineInfo = M("Machine")
                ->alias('c')
                ->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")
                ->where($where)
                ->field("c.*,m.realname,m.mobile,m.job_number")
                ->find();
            $this->assign("info",$machineInfo);
            $this->display("machine_edit");
        }
    }
    //设备删除
    public function machineDelete(){
        if(IS_POST){
            $where["machine_id"] = $_POST["machine_id"];
            //查设备信息
            $oldMachineInfo = M("Machine")->where($where)->find();
            if(M("Machine")->where($where)->setField("is_delete",1)){
                adminLog("设备添加，id为".$where["machine_id"]);
                //修改关联表
                M("Machine")->where(array("machine_id"=>$oldMachineInfo["machine_id"]))->setField("userid",0);
                if($oldMachineInfo["userid"]>0){
                    M("Machine_bind")->where(array("machine_id"=>$oldMachineInfo["machine_id"],"status"=>1))->setField("status",0);
                    M("Member")->where(array("userid"=>$oldMachineInfo["userid"],"is_delete"=>0))->setField("machine_id",0);
                }
                $this->success("删除成功");
            }else{
                $this->error("删除失败");
            }
        }
    }
    //导入设备
    public function importFile(){
        $file_name = I("filename");
        $extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        vendor("PHPExcel.PHPExcel");
        if($extension =='xls'){
            $objReader= \PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel=$objReader->load($file_name,$encode='utf-8');
        }else if ($extension =='xlsx') {
            $objReader= \PHPExcel_IOFactory::createReader('Excel2007');
            $objPHPExcel=$objReader->load($file_name,$encode='utf-8');
        }
        $sheet=$objPHPExcel->getSheet(0);
        $highestRow=$sheet->getHighestRow();
        $highestColumn=$sheet->getHighestColumn();
        for($i=2;$i<$highestRow+1;$i++){
            $sheet = $objPHPExcel->getActiveSheet();
            $where["machine_imei"] = $data['machine_imei'] = $sheet->getCell("A".$i)->getValue();
            $data['machine_name'] = $sheet->getCell("B".$i)->getValue();
//                $data['cgmr'] = $sheet->getCell("C".$i)->getValue();
//                $data['cgmm'] = $sheet->getCell("D".$i)->getValue();
            $data['uid'] = session('admin_uid');
            $data['add_time'] = $data["update_time"] = time();
            $where["is_delete"] = 0;
            $isIn = M("Machine")->where($where)->find();
            if(!$isIn && $data['machine_imei']){
                M('Machine')->add($data);
            }
        }
        adminLog("批量导入设备");
        $this->success('导入成功');
    }
    // 设备地图展示
    public function machineMap(){
        $where["c.machine_id"] = I("machine_id");
        // $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        // $where["c.login_time"] = array("gt",$beginToday);
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
            $info["add_time"] = $info["server_utc_time"];
        }
        $ret["info"]=$info;
        $this->ajaxReturn($ret);
//        $this->assign($info);
//        $this->display("machine_map");
    }



    // 初始化设备轨迹页面
    public function machineOrbit(){
        //获取设备信息
        $machineId = I("machine_id");
        $where['is_delete'] = 0;
        $where['machine_id'] = $machineId;
        $machineInfo = M('Machine')->where($where)->find();
        $this->assign("machineInfo",$machineInfo);
        if($machineInfo["userid"]>0){
            $memberInfo = M("Member")->where(array("userid"=>$machineInfo["userid"],"is_delete"=>0))->find();
            if($memberInfo){
                $this->assign("memberInfo",$memberInfo);
            }
        }
        if($machineInfo["area_id"]>0){
            $areaInfo = M("Area_map")->where(array("id"=>$machineInfo["area_id"],"is_delete"=>0,"is_show"=>1))->find();
            if($areaInfo){
                $this->assign("areaInfo",$areaInfo);
            }
        }
        // $schedulesSetting = M('Schedules_setting')->where(array('schedules_id'=>$machineInfo['schedules_id']))->find();
        // $schedulesSetting['time_id'] = explode(",",$schedulesSetting['time_id']);
        // $timeList = array();
        // foreach ($schedulesSetting['time_id'] as $kk => $vv) {
        //     $arr = M("Schedules_time")->where(array('is_delete'=>0,'id'=>$vv))->find();
        //     if($arr){
        //         $timeList[] = $arr;
        //     }
        // }
        // $this->assign('timeList',$timeList);
        $searchTime = I("searchTime");
        if(!$searchTime){
            $searchTime = date("Y-m-d",time());
        }
        $this->assign('searchTime',$searchTime);
        $this->display("machine_orbit");
    }

    //获取设备轨迹
    public function getMachineOrbit(){
        //获取设备信息
        header("Content-Type:text/html;charset=utf-8");
//        $type = I("type");
        $machineId = I("machine_id");
        $machineInfo = M('Machine')->where(array("machine_id"=>$machineId,"is_delete"=>0))->find();

        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));

        $start_time = $beginToday- 96*60*60+1;
            //$this->assign('start_time',I('start_time'));
        $end_time = $beginToday-24*60*60-1;
        Log::write("wdd".$start_time);
        Log::write($beginToday);
//        if(I('start_time')){
//            $start_time = strtotime(I('start_time'));
//            //$this->assign('start_time',I('start_time'));
//        }
//        if(I('end_time')){
//            if($start_time == I('end_time')){
//                $end_time = strtotime(I('end_time'))+24*3600;
//            }else{
//                $end_time = strtotime(I('end_time'));
//            }
//            //$this->assign('end_time',I('end_time'));
//        }
        //请求轨迹
        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
        $paramOne["DeviceID"] = $machineInfo["mid"];
        $paramOne["Start"] =$start_time;
        $paramOne["End"] = $end_time;
        $paramOne["TimeZone"] = "8:00";
        $paramOne["ShowLBS"] = 0;
        $result1 = $client->GetDevicesHistory($paramOne);
        if ($result1) {
            $result2 = object_to_array($result1);
            $jsonstr1 = $result2["GetDevicesHistoryResult"];
            $jsonstr2 = json_replace_key($jsonstr1);
            $jsonArr1 = json_decode("[" . $jsonstr2 . "]", true);
            $listOne = $jsonArr1[0]["devices"];
        }
        //3.根据设备信息中的绑定班组获取班组信息
//        $whereSchedules["schedules_id"] = $machineInfo["schedules_id"];
//        $whereSchedules["is_show"] = 1;
//        $whereSchedules["is_delete"] = 0;
//        $schedulesInfo = M("Schedules_setting")->where($whereSchedules)->find();
//        if($schedulesInfo) {
//            if ($schedulesInfo["time_id"]) {
//                //获取时间段信息
////                if(strpos($schedulesInfo["time_id"],',')!==false){
//                    //存在多个时间段,进行打卡操作
//                    $timeIdArr = explode(",", $schedulesInfo["time_id"]);
//                    //两个时间段
//                    $timeInfoAm = M("Schedules_time")->where(array("id" => $timeIdArr[0], "is_show" => 1, "is_delete" => 0))->find();
//                    $timeInfoPm = M("Schedules_time")->where(array("id" => $timeIdArr[1], "is_show" => 1, "is_delete" => 0))->find();
//                    if ($timeInfoAm && $timeInfoPm) {
//                        //5.查询考勤设置
//                        $attendanceSetInfo = M("Attendance_setting")->where(array("id" => 1))->find();
//                        //拼接时间段
//                        $timeInfoAmStart = strtotime($year.' '.$timeInfoAm["start_time"])-$attendanceSetInfo["error_time"];
//                        $timeInfoAmEnd = strtotime($year.' '.$timeInfoAm["end_time"])+$attendanceSetInfo["error_time"];
//                        $timeInfoPmStart = strtotime($year.' '.$timeInfoPm["start_time"])-$attendanceSetInfo["error_time"];
//                        $timeInfoPmEnd = strtotime($year.' '.$timeInfoPm["end_time"])+$attendanceSetInfo["error_time"];
//                        $timeStart1 = date("Y-m-d",$timeInfoAmStart)."T".date("H:i:s",$timeInfoAmStart);
//                        $timeStart2 = date("Y-m-d",$timeInfoPmStart)."T".date("H:i:s",$timeInfoPmStart);
//                        $timeEmd1 = date("Y-m-d",$timeInfoAmEnd)."T".date("H:i:s",$timeInfoAmEnd);
//                        $timeEmd2 = date("Y-m-d",$timeInfoPmEnd)."T".date("H:i:s",$timeInfoPmEnd);
//                        //请求轨迹
//                        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
//                        if($type == 0 || $type == 1){
//                            $paramOne["DeviceID"] = $machineInfo["mid"];
//                            $paramOne["Start"] =$timeStart1;
//                            $paramOne["End"] = $timeEmd1;
//                            $paramOne["TimeZone"] = "8:00";
//                            $paramOne["ShowLBS"] = 0;
//                            $result1 = $client->GetDevicesHistory($paramOne);
//                            if ($result1) {
//                                $result2 = object_to_array($result1);
//                                $jsonstr1 = $result2["GetDevicesHistoryResult"];
//                                $jsonstr2 = json_replace_key($jsonstr1);
//                                $jsonArr1 = json_decode("[" . $jsonstr2 . "]", true);
//                                $listOne = $jsonArr1[0]["devices"];
//                            }
//                        }
//                        if($type ==2 || $type == 0){
//                            $paramTwo["DeviceID"] = $machineInfo["mid"];
//                            $paramTwo["Start"] =$timeStart2;
//                            $paramTwo["End"] = $timeEmd2;
//                            $paramTwo["TimeZone"] = "8:00";
//                            $paramTwo["ShowLBS"] = 0;
//                            $result3 = $client->GetDevicesHistory($paramTwo);
//                            if ($result3) {
//                                $result4 = object_to_array($result3);
//                                $jsonstr3 = $result4["GetDevicesHistoryResult"];
//                                $jsonstr4 = json_replace_key($jsonstr3);
//                                $jsonArr2 = json_decode("[" . $jsonstr4 . "]", true);
//                                $listTwo = $jsonArr2[0]["devices"];
//                            }
//                        }
//                    }
//                }
//            }
//        }
        if($listOne){
            $ret["status"] = 1;
            $ret["info"] = "获取坐标数据成功";
            $ret["listOne"] = $listOne;
        }else{
            $ret["status"] = 0;
            $ret["info"] = "暂无坐标数据";
        }
        $this->ajaxReturn($ret);
    }

    //获取设备轨迹用户划区域（围栏）
    public function machineArea(){
        if(IS_POST){
            $area_id = I('area_id');
            $data['area_name'] = I('area_name');
            $data['employee_num'] = I('employee_num');
            $data['coordinate'] = htmlspecialchars_decode(I('coordinate'));
            if($area_id){
                //已有区域，更新原有区域
                $where['area_id'] = $area_id;
                $result = M('Area_map')->where($where)->save($data);
            }else{
                //没有区域，添加并绑定设备
                $data['add_time'] = time();
                $result = M('Area_map')->add($data);
                $machineData['area_id'] = $result;
                $machineWhere['machine_id'] = I('machine_id');
                M('Machine')->where($machineWhere)->save($machineData);
            }
            if($result){
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{
            $machine_id = I("machine_id");
            $where['machine_id'] = $machine_id;
            $where['is_delete'] = 0;
            $machineInfo = M('Machine')->where($where)->find();
            if($machineInfo["userid"]>0){
                $memberInfo = M("Member")->where(array("userid"=>$machineInfo["userid"],"is_delete"=>0))->find();
                if($memberInfo){
                    $this->assign("memberInfo",$memberInfo);
                }
            }
            if($machineInfo["area_id"]>0){
                $areaInfo = M("Area_map")->where(array("id"=>$machineInfo["area_id"],"is_delete"=>0))->find();
                if($areaInfo){
                    $this->assign("areaInfo",$areaInfo);
                }
            }
            $this->assign($machineInfo);
            $searchTime = date("Y-m-d",time());
            $this->assign('searchTime',$searchTime);
            $this->display('machine_area');
        }
    }

    //    员工绑定
    public function memberBind(){
        if(IS_POST){
            $info = I("info");
            $type = $info["type"];
            $machine_id = $info["machine_id"];
            $userid= $info["new_userid"];
            $data["userid"] = $userid;
            $data["machine_id"] = $machine_id;
            $data["add_time"] = $data["update_time"] = time();
            if($type == 1){
                //换绑
                //判断换绑员工是否已绑定设备
                $oldMachine = I("machine_imei");
                if($oldMachine){
                    $oldMachineInfo = M("Machine")->where(array("machine_imei"=>$oldMachine,"is_delete"=>0))->find();
                    if($oldMachineInfo){
                        M("Machine")->where(array("machine_id"=>$oldMachineInfo["machine_id"],"is_delete"=>0))->setField("userid",0);
                        M("Machine_bind")->where(array("machine_id"=>$oldMachineInfo["machine_id"],"status"=>1))->setField("status",0);
                        M("Member")->where(array("userid"=>$oldMachineInfo["userid"],"is_delete"=>0))->setField("machine_id",0);
                    }
                }
                //判断当前设备原先是否绑定
                $bindInfo = M("Machine_bind")->where(array("machine_id"=>$machine_id,"status"=>1))->find();
                if($bindInfo){
                    //1.解除原设备绑定信息
                    $where["machine_id"] = $machine_id;
                    $where["status"] = 1;
                    M("Machine_bind")->where($where)->setField("status",0);
                    //1.1修改用户表
                    $memberData["machine_id"] = 0;
                    $memberData["update_time"] = time();
                    M("Member")->where(array("userid"=>$bindInfo["userid"]))->save($memberData);
                }
                //创建新的绑定关系
                //2.换绑新员工
                //2.1修改用户表
                $memberData["machine_id"] = $machine_id;
                $memberData["update_time"] = time();
                M("Member")->where(array("userid"=>$userid,"is_delete"=>0))->save($memberData);
                //2.2修改设备表
                $machineData["userid"] = $userid;
                $machineData["update_time"] = time();
                M("Machine")->where(array("machine_id"=>$machine_id,"is_delete"=>0))->save($machineData);
                if(M("Machine_bind")->add($data)){
                    adminLog("换绑员工");
                    $this->success("换绑成功");
                }else{
                    $this->error("换绑失败");
                }
            }else{
                //第一次绑定
                $ret = M("Machine_bind")->add($data);
                if($ret){
                    //修改用户表
                    $memberData["machine_id"] = $data["machine_id"];
                    $memberData["update_time"] = time();
                    M("Member")->where(array("userid"=>$data["userid"]))->save($memberData);
                    //修改设备表
                    $machineData["userid"] = $data["userid"];
                    $machineData["update_time"] = time();
                    M("Machine")->where(array("machine_id"=>$data["machine_id"]))->save($machineData);
                    adminLog("绑定员工");
                    $this->success("绑定成功");
                }else{
                    $this->error("绑定失败");
                }
            }
        }else{
            $machine_id = I("machine_id");
            $where["b.machine_id"] = $machine_id;
            $where["b.status"] = 1;
            $where["c.is_delete"] = $where["m.is_delete"] = 0;
            $where["_string"] = "b.userid = m.userid and c.machine_id = b.machine_id";
            $machineInfo = M("Machine_bind")->table('__MACHINE_BIND__ b,__MACHINE__ c,__MEMBER__ m')->where($where)->field("b.*,c.machine_imei,c.machine_name,m.realname,m.job_number,m.mobile")->find();
            if(!$machineInfo){
                $machineInfo = M("Machine")->where(array("machine_id"=>$machine_id))->find();
            }
            $this->assign($machineInfo);
            $this->display("member_bind");
        }
    }
    //    获取用户信息
    public function getMemberInfo(){
        $keywords = I("keywords");
        $where["job_number"] = $keywords;
        $where["is_delete"] = 0;
        $userInfo = M("Member")->where($where)->find();
        if($userInfo["machine_id"]>0){
            $whereInfo["m.job_number"] = $keywords;
            $whereInfo["m.is_delete"] = 0;
            $whereInfo["c.is_delete"] = 0;
            $whereInfo["_string"] = "c.userid = m.userid and m.machine_id = c.machine_id";
            $info = M("Member")->table("__MEMBER__ m,__MACHINE__ c")->where($whereInfo)->field("m.userid,m.realname,m.mobile,m.job_number,c.machine_name,c.machine_imei,c.machine_id")->find();
            if($info){
                $ret["status"] = 1;
                $ret["info"] = "获取成功";
                $ret["data"] = $info;
            }else{
                $ret["status"] = 0;
                $ret["info"] = "暂无";
                $ret["data"] = $userInfo;
            }
        }else{
            $ret["status"] = 0;
            $ret["info"] = "暂无";
            $ret["data"] = $userInfo;
        }
        $this->ajaxReturn($ret);
    }

    //绑定班组
    public function schedulesBind(){
        if(IS_POST){
            $machine_id = I('machine_id');
            $schedules_id = I('schedules_id');
            //var_dump($machine_id);var_dump($schedules_id);die;
            if(!$schedules_id){
               $this->error('请选择班组');
            }
            $where['machine_id'] = $machine_id;
            $data['schedules_id'] = $schedules_id;
            $result = M('Machine')->where($where)->save($data);
            if($result){
                $this->success('设置成功');
            }else{
                $this->error('设置失败');
            }

        }else{
            $machine_id = I('machine_id');
            $machineInfo = M('Machine')->where(array('machine_id'=>$machine_id))->find();
            $this->assign($machineInfo);
//            var_dump($machine_id);die;
            $where['is_delete'] = 0;
            $list = M("Schedules_setting")->where($where)->order('schedules_id desc')->select();
            if($list){
                $work_day = '';
                foreach($list as $k=>$v){
                    $list[$k]['work_day'] = explode(",",$v['work_day']);
                    foreach ($list[$k]['work_day']as $kkk => $vvv) {
                        if($vvv == 1){
                            $work_day = $work_day."周一";
                        }elseif($vvv == 2){
                            $work_day = $work_day." 周二";
                        }elseif($vvv == 3){
                            $work_day = $work_day." 周三";
                        }elseif($vvv == 4){
                            $work_day = $work_day." 周四";
                        }elseif($vvv == 5){
                            $work_day = $work_day." 周五";
                        }elseif($vvv == 6){
                            $work_day = $work_day." 周六";
                        }elseif($vvv == 0){
                            $work_day = $work_day." 周日";
                        }
                    }
                    $list[$k]['work_day'] = $work_day;
                    $work_day = '';
                    $list[$k]['time_id'] = explode(",",$v['time_id']);
                    foreach ($list[$k]['time_id']as $kk => $vv) {
                        $list[$k]['timeList'][] = M("Schedules_time")->where(array('is_delete'=>0,'id'=>$vv))->find();
                    }
                }
            }
            $this->assign('list',$list);
            $this->display('schedules_bind');
        }
    }

    //区域绑定
    public function areaBind(){
        if (IS_POST) {
            $machine_id = I("machine_id") ? I("machine_id") : $this->error("缺少参数");
            $where['machine_id'] = $machine_id;
            $data = $_POST['info'];
//            $whereCount['area_id'] = $data['area_id'];
//            $whereCount['is_delete'] = 0;
//            $areaCount = M('Machine')->where($whereCount)->count();
//            $whereMap['id'] = $data['area_id'];
//            $areaInfo = M('Area_map')->where($whereMap)->find();
//            if($areaCount >= $areaInfo['employee_num']){
//                $this->error("该区域绑定员工数已达上限");
//            }

            $res = M('Machine')->where($where)->save($data);
            if($res) {
                $this->success("绑定成功");
            }else{
                $this->error("绑定失败");
            }
        }else{
            $machine_id = I("machine_id") ? I("machine_id") : $this->error("缺少参数");
            $where['is_delete'] = 0;
            $areaList = M('Area_map')->where($where)->select();
            $this->assign('areaList',$areaList);
            $this->assign('machine_id',$machine_id);
            $this->display("area_bind");
        }
    }

    //批量绑定区域
    public function areaBinds(){
        if (IS_POST) {
            $machineids = I("machineids") ? I("machineids") : $this->error("缺少参数");
            $machineids = explode(",",$machineids);
            foreach ($machineids as $key => $v) {
                $data = $_POST['info'];
                $where['machine_id'] = $v;
                $res = M('Machine')->where($where)->save($data);
            }
            if($res) {
                $this->success("绑定成功");
            }else{
                $this->error("绑定失败");
            }
        }else{
            if(I("keywords")){
                $where['area_name'] = array('like', '%'.I('keywords').'%');
                $this->assign("keywords", I("keywords"));
            }
            $machineids = I("machineids") ? I("machineids") : $this->error("缺少参数");
            $where['is_delete'] = 0;
            $areaList = M('Area_map')->where($where)->select();
            $this->assign('areaList',$areaList);
            $this->assign('machineids',$machineids);
            $this->display("area_binds");
        }
    }
    public function getAreaList(){
        if(I("keywords")){
            $where['area_name'] = array('like', '%'.I('keywords').'%');
            $this->assign("keywords", I("keywords"));
        }
        $where['is_delete'] = 0;
        $areaList = M('Area_map')->where($where)->select();
        $html = '';
        if($areaList){
            foreach($areaList as $h){
                $html .= "<option value='{$h['id']}'>{$h['area_name']}</option>";
            }
        }
        echo $html;
    }
    // 解除区域
    public function areaUnbind(){
        if (IS_POST) {
            $machine_id = I("machine_id") ? I("machine_id") : $this->error("缺少参数");
            $where["machine_id"] = $machine_id;
            $data["area_id"] = 0;
            $res = M('Machine')->where($where)->save($data);
            if($res) {
                $this->success("解绑成功");
            }else{
                $this->error("解绑失败");
            }
        }
    }
    //设备命令
    public function machineCommand(){
        $this->display("machine_command");
    }
    //设备设置中心号码
    public function centerTelSet(){
        if (IS_POST) {
            $data = $_POST["info"];
            $machine_imei = $data["machine_imei"] ? $data["machine_imei"] : $this->error("请上传设备IMEI");
            $center_tel = $data["center_tel"] ? $data["center_tel"] : $this->error("请上传中心号码");
            $where['machine_imei'] = $machine_imei;
            $where['is_delete'] = 0;
            $info = M('Machine')->where($where)->find();
            if(!$info){
                $this->error("查无此设备");
            }
            if($info["machine_status"] != 1 ){
                $this->error("设备未在线，无法设置");
            }
            $client = stream_socket_client('tcp://0.0.0.0:5678', $errno, $errmsg, 1);
            $uid = $info["machine_id"];
            $a = mb_strlen($center_tel,'UTF8');
            $b = $a+7;
            $d =dechex($b);
            $f = mb_strlen($d,'UTF8');
            if($f == 1){
                $num = "000".$d;
            }elseif($f == 2){
                $num = "00".$d;
            }elseif($f == 3){
                $num = "0".$d;
            }else{
                $num = $d;
            }
            $commandstr ='S168#'.$machine_imei.'#0000#'.$num.'#CENTER,'.$center_tel.'$';
            $data = array('uid'=>$uid, 'percent'=>$commandstr);
            fwrite($client, json_encode($data)."\n");
            $result = fread($client, 8192);
            if(strpos($result,'ok') !== false){
                $this->success("发送成功");
            }else{
                $this->error("发送失败");
            }
        }else{
            $this->display("center_tel_set");
        }
    }
    //设备初始化
    public function machineInitialize(){
        if (IS_POST) {
            $data = $_POST["info"];
            $machine_imei = $data["machine_imei"] ? $data["machine_imei"] : $this->error("请上传设备IMEI");
            $where['machine_imei'] = $machine_imei;
            $where['is_delete'] = 0;
            $info = M('Machine')->where($where)->find();
            if(!$info){
                $this->error("查无此设备");
            }
            if($info["machine_status"] != 1 ){
                $this->error("设备未在线，无法设置");
            }
            $client = stream_socket_client('tcp://0.0.0.0:5678', $errno, $errmsg, 1);
            $uid = $info["machine_id"];
            $commandstr ='S168#'.$machine_imei.'#0000#0007#FACTORY$';
            $data = array('uid'=>$uid, 'percent'=>$commandstr);
            fwrite($client, json_encode($data)."\n");
            $result = fread($client, 8192);
            if(strpos($result,'ok') !== false){
                $this->success("发送成功");
            }else{
                $this->error("发送失败");
            }
        }else{
            $this->display("machine_initialize");
        }
    }
    //批量删除
    public function machineDels(){
        if(IS_POST){
            $machineids = explode(",",$_POST['machineids']);
            $data['is_delete'] = 1;
            foreach ($machineids as $key => $v) {
                //查设备信息
                $oldMachineInfo = M("Machine")->where(array("machine_id"=>$v))->find();
                $data["userid"] = 0;
                $data["update_time"] = time();
                M("Machine")->where(array("machine_id"=>$v))->data($data)->save();
                adminLog("设备删除,id为".$v);
                //修改关联表
                if($oldMachineInfo["userid"]>0){
                    M("Machine_bind")->where(array("machine_id"=>$oldMachineInfo["machine_id"],"status"=>1))->setField("status",0);
                    M("Member")->where(array("userid"=>$oldMachineInfo["userid"],"is_delete"=>0))->setField("machine_id",0);
                }
            }
            $this->success('删除成功');
        }
    }
    //批量解除区域
    public function areaDels(){
        if(IS_POST){
            $machineids = explode(",",$_POST['machineids']);
            foreach ($machineids as $key => $v) {
                $data["area_id"] = 0;
                $data["update_time"] = time();
                M('Machine')->where(array("machine_id"=>$v))->save($data);
            }
            $this->success('解除成功');
        }
    }
    //切换运行时间
    public function changeTime(){
        if(IS_POST){
            $num = I("num");
            //获取全部设备
            $machineList = M("Machine")->where(array("is_delete"=>0))->select();
            foreach ($machineList as $k => $v) {
                $data["schedules_id"] = $num;
                $data["update_time"] = time();
                M('Machine')->where(array("machine_id"=>$v["machine_id"]))->save($data);
            }
            M("Attendance_setting")->where(array("id"=>1))->setField("operation_season",$num);
            $this->success('切换成功');
        }
    }
    //一键切换班组
    public function changeSchedules(){
        if(IS_POST){
            $schedules_id = I('schedules_id');
            if(!$schedules_id){
                $this->error('请选择班组');
            }
            $data["schedules_id"] = $schedules_id;
            $data["update_time"] = time();
            $machineids = I("machineids");
            if($machineids){
                $machineids = explode(",",$machineids);
                foreach ($machineids as $key => $v) {
                    M('Machine')->where(array("machine_id"=>$v))->save($data);
                }
            }else{
                //获取全部设备
                $machineList = M("Machine")->where(array("is_delete"=>0))->select();
                foreach ($machineList as $k => $v) {
                    M('Machine')->where(array("machine_id"=>$v["machine_id"]))->save($data);
                }
            }
            $this->success('切换成功');
        }else{
            $machineids = I("machineids");
            $this->assign("machineids",$machineids);
            $where['is_delete'] = 0;
            $where['is_show'] = 1;
            $list = M("Schedules_setting")->where($where)->order('schedules_id desc')->select();
            if($list){
                $work_day = '';
                foreach($list as $k=>$v){
                    $list[$k]['work_day'] = explode(",",$v['work_day']);
                    foreach ($list[$k]['work_day']as $kkk => $vvv) {
                        if($vvv == 1){
                            $work_day = $work_day."周一";
                        }elseif($vvv == 2){
                            $work_day = $work_day." 周二";
                        }elseif($vvv == 3){
                            $work_day = $work_day." 周三";
                        }elseif($vvv == 4){
                            $work_day = $work_day." 周四";
                        }elseif($vvv == 5){
                            $work_day = $work_day." 周五";
                        }elseif($vvv == 6){
                            $work_day = $work_day." 周六";
                        }elseif($vvv == 0){
                            $work_day = $work_day." 周日";
                        }
                    }
                    $list[$k]['work_day'] = $work_day;
                    $work_day = '';
                    $list[$k]['time_id'] = explode(",",$v['time_id']);
                    foreach ($list[$k]['time_id']as $kk => $vv) {
                        $list[$k]['timeList'][] = M("Schedules_time")->where(array('is_delete'=>0,'id'=>$vv))->find();
                    }
                }
            }
            $this->assign('list',$list);
            $this->display('schedules_change');
        }
    }


    //设备列表
    public function getMachineList(){

        //关键字
        $keyword= I('keyword');
        if($keyword){
            $where['c.machine_imei|m.realname'] = array('like', '%'.$keyword.'%');
        }
        //设备状态
        $machine_status = I("status");
        if($machine_status){
            if($machine_status == 1){
                //在线
                $where['c.machine_status'] = array(array("eq","Stop"),array("eq","Move"),"or");
            }
            if($machine_status == 2){
                //离线
                $where['c.machine_status'] = array("eq","Offline");
            }
        }
        $where["c.is_delete"] = 0;

        //职位
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
        $count =M("Machine")->alias('c')->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")->where($where)->count();
        $listRows=10;
        $firstRow = $listRows*(I("page")-1);

        $machineList = M("Machine")
            ->alias('c')
            ->join("LEFT JOIN __MEMBER__ m ON c.userid = m.userid")
            ->where($where)
            ->limit($firstRow.','.$listRows)
            ->field("c.*,m.realname,m.job_number,m.mobile,m.parent_id,m.position")
            ->order("c.add_time desc")
            ->select();
        if($machineList){
            foreach($machineList as $k=>$v){
                $machineList[$k]['schedules_name'] = M("Schedules_setting")->where(array('schedules_id'=>$v['schedules_id']))->getField("schedules_name");
                if($v['area_id']){
                    $machineList[$k]['area_name'] = M('Area_map')->where(array('id'=>$v['area_id']))->getField('area_name');
                }
                if($v["parent_id"]>0){
                    $machineList[$k]["parent_name"] = M("Member")->where(array("userid"=>$v["parent_id"],"is_delete"=>0))->getField("realname");
                }else{
                    $machineList[$k]["parent_name"] ="";
                }
                if($v["position"]>0){
                    $machineList[$k]["position_name"] = M("Member_position")->where(array("id"=>$v["position"],"is_delete"=>0))->getField("name");
                }else{
                    $machineList[$k]["position_name"] ="";
                }
                if($v["machine_status"] == "Offline"){
                    $machineList[$k]["machine_status"] = 2;
                }elseif($v["machine_status"] == "Stop" || $v["machine_status"] == "Move"){
                    $machineList[$k]["machine_status"] = 1;
                }else{
                    $machineList[$k]["machine_status"] = 3;
                }
            }
        }


        $wheremap["uid"] = session("admin_uid");
        $wheremap["is_delete"] = 0;
        $mapConfigInfo=M("Map_config")->where($wheremap)->select();
        if($mapConfigInfo)
        {
            $center["lng"] =$mapConfigInfo[0]["lng"];//119.40;
            $center["lat"] =$mapConfigInfo[0]["lat"];//36.85;
            $mapData["center"]=$center;
            $mapData["zoom"]=$mapConfigInfo[0]["rank"];//15;
            $ret["mapData"]=$mapData;
        }
        else
        {
            $ret["mapData"]="";
        }

        $ret["totalNumber"]= $count;
        $ret["machineList"]= $machineList;
        $this->ajaxReturn($ret);
    }

    //添加设备
    public function machineAddSubmit(){
        if(IS_POST){
            $data = $_POST['info'];
            $data["add_time"] = $data["update_time"] = time();
            $data["uid"] = session("admin_uid");
            $info = M("Machine")->where(array("machine_imei"=>$data["machine_imei"],"is_delete"=>0))->find();
            if($info){
                $this->error("添加失败，该设备已存在");
            }
            $result = M("Machine")->add($data);
            if($result){
                $this->success("添加成功");
                adminLog("添加设备".$data["machine_imei"]);
            }else{
                $this->error("添加失败");
            }
        }else{
            $this->display("machine_add");
        }
    }

    public function getAreaListNew(){
        if(I("keywords")){
            $where['area_name'] = array('like', '%'.I('keywords').'%');
        }
        $where['is_delete'] = 0;
        $areaList = M('Area_map')->where($where)->select();
        $this->assign('areaList',$areaList);

        if($areaList){
            foreach($areaList as $k=>$v){
                $areaList[$k]['coordinate'] = json_decode('['.$v['coordinate'].']',true);
            }
        }

        $ret["areaList"]= $areaList;
        $this->ajaxReturn($ret);
    }
    //批量绑定区域
    public function areaBindsNew(){
        if (IS_POST) {
            $machineList = I("machineList") ? I("machineList") : $this->error("缺少参数");
            $data["area_id"]=I("area_id");
//            Log::write("machineList".$machineList);
            foreach ($machineList as $key => $v) {
                $res = M('Machine')->where(array('machine_id'=>$v['machine_id']))->save($data);
            }
//            Log::write("$res".$res);
            if($res) {
                $this->success("绑定成功");
            }else{
                $this->error("绑定失败");
            }
        }else{
            if(I("keywords")){
                $where['area_name'] = array('like', '%'.I('keywords').'%');
                $this->assign("keywords", I("keywords"));
            }
            $machineList = I("machineList") ? I("machineList") : $this->error("缺少参数");
            $where['is_delete'] = 0;
            $areaList = M('Area_map')->where($where)->select();
            $this->assign('areaList',$areaList);
            $this->assign('machineList',$machineList);
        }
    }
    public function areaBindsByone(){
        $machine_id = I("machine_id") ? I("machine_id") : $this->error("缺少参数");
        $data["area_id"]=I("area_id");
        $res = M('Machine')->where(array('machine_id'=>$machine_id))->save($data);

        if($res) {
            $this->success("绑定成功");
        }else{
            $this->error("绑定失败");
        }
    }
    //设备位置
    public function machineMapNew(){
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
            $info["add_time"] = $info["server_utc_time"];
        }
        $this->assign($info);
        $ret["info"]= $info;
        $this->ajaxReturn($ret);
    }
    //批量切换班组
    public function batchChangeSchedules(){
        if(IS_POST){
            $schedules_id = I('scheduleIdList');
            if(!$schedules_id){
                $this->error('请选择班组');
            }
            $data["schedules_id"] = $schedules_id;
            $data["update_time"] = time();
            $machineList = I("machineList");
            if($machineList){
                foreach ($machineList as $key => $v) {
                    M('Machine')->where(array("machine_id"=>$v["machine_id"]))->save($data);
                }
            }
            $this->success('切换成功');
        }else{
//            $machineids = I("machineList");
//            $this->assign("machineids",$machineids);
            $where['is_delete'] = 0;
            $where['is_show'] = 1;
            $scheduleList = M("Schedules_setting")->where($where)->order('schedules_id desc')->select();
            if($scheduleList){
                $work_day = '';
                foreach($scheduleList as $k=>$v){
//                    $list[$k]['work_day'] = explode(",",$v['work_day']);
                    foreach ($scheduleList[$k]['work_day']as $kkk => $vvv) {
                        if($vvv == 1){
                            $work_day = $work_day."周一";
                        }elseif($vvv == 2){
                            $work_day = $work_day." 周二";
                        }elseif($vvv == 3){
                            $work_day = $work_day." 周三";
                        }elseif($vvv == 4){
                            $work_day = $work_day." 周四";
                        }elseif($vvv == 5){
                            $work_day = $work_day." 周五";
                        }elseif($vvv == 6){
                            $work_day = $work_day." 周六";
                        }elseif($vvv == 0){
                            $work_day = $work_day." 周日";
                        }
                    }
                    $scheduleList[$k]['work_day'] = $work_day;
                    $work_day = '';
//                    $scheduleList[$k]['time_id'] = explode(",",$v['time_id']);
                    foreach ($scheduleList[$k]['time_id']as $kk => $vv) {
                        $scheduleList[$k]['timeList'][] = M("Schedules_time")->where(array('is_delete'=>0,'id'=>$vv))->find();
                    }
                }
            }
            $this->assign('scheduleList',$scheduleList);
        }
    }
}
