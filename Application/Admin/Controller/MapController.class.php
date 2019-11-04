<?php
namespace Admin\Controller;
use Think\Auth;
class MapController extends BaseController {
	//区域划分列表
	public function areaList(){
		$where['is_delete'] = 0;
        $keywords = I('keywords');
        if($keywords){
            $where['area_name'] = array("like", "%" . $keywords . "%");
            $this->assign('keywords',$keywords);
        }
		$count = M('Area_map')->where($where)->count();
		$Page = new \Think\Page($count,20);
		$areaList = M('Area_map')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        if($areaList){
            foreach($areaList as $k=>$v){
                $areaList[$k]['coordinate'] = json_decode('['.$v['coordinate'].']',true);
            }
        }
		$this->assign("page",$Page->show());
        $this->assign("areaList",$areaList);
        $this->display("area_list");
	}

	// 百度地图
	public function mapFence(){
		if(IS_POST){
			$data['area_name'] = I('area_name');
			$data['employee_num'] = I('employee_num');
			$data['coordinate'] = htmlspecialchars_decode(I('coordinate'));
			$data['add_time'] = time();
			$result = M('Area_map')->add($data);
			if($result){
                $this->success($result);
            }else{
                $this->error("添加失败");
            }
		}else{
			$this->display('map_fence');
		}
	}

    //区域编辑
    public function mapEdit(){
        if(IS_POST){
            $where["id"] = I("id");
            $data['area_name'] = I('area_name');
            $data['employee_num'] = I('employee_num');
            $type = I('type');
            if($type == 2){
                $data['coordinate'] = htmlspecialchars_decode(I('coordinate'));
            }
            
            // var_dump($data);die;
            $result = M('Area_map')->where($where)->save($data);
            if($result){
                $this->success($where["id"]);
            }else{
                $this->error("编辑失败");
            }
        }else{
            $where["id"] = I("id");
            $areaInfo = M("Area_map")->where($where)->find();
            $this->assign("areaInfo",$areaInfo);
            $this->display("area_edit");
        }
    }

    //区域详情
    public function areaInfo(){
        $where["id"] = I("id");
        $areaInfo = M("Area_map")->where($where)->find();
        $this->assign("areaInfo",$areaInfo);
        $this->display("area_info");
    }

    //电子栅栏
    public function mapAllFence(){
        $where['is_delete'] = 0;
        $areaList = M('Area_map')->where($where)->select();
        if($areaList){
            foreach($areaList as $k=>$v){
                $areaList[$k]['coordinate'] = json_decode('['.$v['coordinate'].']',true);
            }
        }
        $areaList = json_encode($areaList);
        $this->assign("areaList",$areaList);
        $this->display('map_all_fence');
    }

    // 地图设备展示
    public function mapMachine(){
        $keywords = I('keywords');
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
        if($keywords){
            $where["m.realname"] = array('like', "%" . $keywords . "%");
            $this->assign('keywords',$keywords);
        }
        $add_time = date("Y-m-d",time());
        $startTime = strtotime($add_time);
        $endTime = strtotime($add_time) + 24*3600;
        $whereSql['s.add_time'] = array('between',array($startTime,$endTime));
        $whereSql["c.is_delete"] = 0;
        $whereSql["_string"] = "s.machine_imei = c.machine_imei and s.machine_id = c.machine_id";
        $subQuery = M("Site_log")->table("__SITE_LOG__ s,__MACHINE__ c")->where($whereSql)->field("s.*,c.machine_name,c.userid")->order('s.add_time desc')->buildSql();
        $list = M("Site_log")->table($subQuery . ' ss')->join("left join __MEMBER__ m on ss.userid = m.userid")->field("ss.*,m.realname,m.job_number,m.mobile,m.position,m.parent_id")->where($where)->group("ss.machine_id")->select();
        $list = json_encode($list);
        $newTime = date("Y-m-d",time());
        $this->assign('newTime',$newTime);
        $this->assign('list',$list);
        $this->display("map_machine");
    }

    //获取员工上班情况
    public function memberNumber(){
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday = $beginToday + 24*60*60-1;
        //总员工数
        $where["is_delete"] = 0;
        $data["count_member"]= M('Member')->where($where)->count();
        //设备总数
        $data["machine_count"]= M("Machine")->where(array("is_delete"=>0))->count();
        //当前在线设备数
        $wheremgc["machine_status"] = array(array("eq","Stop"),array("eq","Move"),"or");
        $wheremgc["is_delete"] = 0;
        $data["mount_guard_count"] = M("Machine")->where($wheremgc)->count();
        //今日在线设备数总计
        $whereTodayMgc["m.is_delete"] = 0;
        $whereTodayMgc["s.add_time"] = array("gt",$beginToday);
        $whereTodayMgc["_string"] = "m.machine_id = s.machine_id";
        $data["today_mount_guard_count"]= M("Site_log")->table("__SITE_LOG__ s,__MACHINE__ m")->where($whereTodayMgc)->count('distinct(s.machine_id)');
        //在岗人数
//        $whereGuard["m.is_delete"] = $where["c.is_delete"] = 0;
//        $whereGuard["c.machine_status"] = array(array("eq","Stop"),array("eq","Move"),"or");
//        $whereGuard["m.job_status"] = 1;
//        $whereGuard["_string"] = "m.machine_id = c.machine_id";
//        $data["on_guard_count"] = M("Member")->table("__MEMBER__ m,__MACHINE__ c")->where($whereGuard)->count();
        $data["on_guard_count"]= M("Member")->where(array("is_delete"=>0,"job_status"=>1))->count();
        //离岗人数
//        $whereNoGuard["m.is_delete"] = $where["c.is_delete"] = 0;
//        $whereNoGuard["c.machine_status"] = array(array("eq","Stop"),array("eq","Move"),"or");
//        $whereNoGuard["m.job_status"] = 0;
//        $whereNoGuard["_string"] = "m.machine_id = c.machine_id";
//        $data["out_guard_count"] = M("Member")->table("__MEMBER__ m,__MACHINE__ c")->where($whereNoGuard)->count();
        $data["out_guard_count"]= M("Member")->where(array("is_delete"=>0,"job_status"=>0))->count();
        //未上岗人数
        $data["no_guard_count"]= M("Member")->where(array("is_delete"=>0,"job_status"=>2))->count();
        $whereAttention["is_delete"] = 0;
        $whereAttention["add_time"] = array(array("gt",$beginToday),array("lt",$endToday));
        //上午
        //正常
        $data["am_t_normal_count"]= M('Attendance')->where($whereAttention)->where("first_result=1")->count();
        //迟到
        $data["am_late_count"]= M('Attendance')->where($whereAttention)->where("first_result=3")->count();
        //旷工
        $data["am_ab_count"]= M('Attendance')->where($whereAttention)->where("first_result=5")->count();
        //正常
        $data["am_d_normal_count"]= M('Attendance')->where($whereAttention)->where("second_result=2")->count();
        //早退
        $data["am_leave_count"]= M('Attendance')->where($whereAttention)->where("second_result=4")->count();
        //异常
        //上午异常
        $whereAmAttention["is_delete"] = 0;
        $whereAmAttention["add_time"] = array(array("gt",$beginToday),array("lt",$endToday));
        $whereAmAbnormal["first_result"] = array("gt",1);
        $whereAmAbnormal["second_result"] = array("gt",2);
        $whereAmAbnormal['_logic'] = 'or';
        $whereAmAttention['_complex'] = $whereAmAbnormal;
        $data["am_abnormal_count"]= M('Attendance')->where($whereAmAttention)->count();
        //下午
        //正常
        $data["pm_t_normal_count"]= M('Attendance')->where($whereAttention)->where("third_result=1")->count();
        //迟到
        $data["pm_late_count"]= M('Attendance')->where($whereAttention)->where("third_result=3")->count();
        //旷工
        $data["pm_ab_count"]= M('Attendance')->where($whereAttention)->where("third_result=5")->count();
        //正常
        $data["pm_d_normal_count"]= M('Attendance')->where($whereAttention)->where("fourth_result=2")->count();
        //早退
        $data["pm_leave_count"]= M('Attendance')->where($whereAttention)->where("fourth_result=4")->count();
        //下午异常
        $wherePmAttention["is_delete"] = 0;
        $wherePmAttention["add_time"] = array(array("gt",$beginToday),array("lt",$endToday));
        $wherePmAbnormal["third_result"] = array("gt",1);
        $wherePmAbnormal["fourth_result"] = array("gt",2);
        $wherePmAbnormal['_logic'] = 'or';
        $wherePmAttention['_complex'] = $wherePmAbnormal;
        $data["pm_abnormal_count"]= M('Attendance')->where($wherePmAttention)->count();
        if($data){
            $ret["status"] = 1;
            $ret["info"] = "获取数据成功";
            $ret["data"] = $data;
        }else{
            $ret["status"] = 0;
            $ret["info"] = "暂无数据";
        }
        $this->ajaxReturn($ret);
    }

    //实时获取设备位置
    public function getMapMachine()
    {
        $keywords = I('keywords');
        if($keywords){
            $where["m.realname"] = array('like', "%" . $keywords . "%");
        }
        $position = I("position");
        if($position){
            $where["m.position"] = $position;
        }
        $parent_id = I("parent_id");
        if($parent_id){
            $this->assign("parent_id",$parent_id);
            $whereParent["m.parent_id"] = $parent_id;
            $wherePositon["m.userid"] = $parent_id;
            $whereMember["_complex"] = array($whereParent,$wherePositon,'_logic'=>'or');
            $where["_complex"] = $whereMember;
        }
        $add_time = date("Y-m-d",time());
        $startTime = strtotime($add_time);
        $endTime = strtotime($add_time) + 24*3600;
        $whereSql['s.add_time'] = array('between',array($startTime,$endTime));
        $whereSql["c.is_delete"] = 0;
        $whereSql["_string"] = "s.machine_imei = c.machine_imei and s.machine_id = c.machine_id";
        $subQuery = M("Site_log")->table("__SITE_LOG__ s,__MACHINE__ c")->where($whereSql)->field("s.*,c.machine_name,c.userid")->order('s.add_time desc')->buildSql();
        $list = M("Site_log")->table($subQuery . ' ss')->join("left join __MEMBER__ m on ss.userid = m.userid")->field("ss.*,m.realname,m.job_number,m.mobile,m.position,m.parent_id")->where($where)->group("ss.machine_imei")->select();
        if($list){
            foreach($list as $k=>$v){
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
                $list[$k]["add_time"] = date("Y-m-d H:i:s",$v["add_time"]);
            }
            $ret["status"] = 1;
            $ret["info"] = "获取坐标数据成功";
            $ret["data"] = $list;
        }else{
            $ret["status"] = 0;
            $ret["info"] = "暂无坐标数据";
        }
        $this->ajaxReturn($ret);
    }


    // 区域删除
    public function areaDelete(){
        $id = I("id");
        $where["id"] = $id;
        $data['is_delete'] = 1;
        $result = M("Area_map")->where($where)->save($data);
        if($result){
            $dataM['area_id'] = 0;
            M('Machine')->where(array('area_id'=>$id))->save($dataM);
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

    //区域绑定设备（员工）列表
    public function machineList(){
        $area_id = I('id');
        $where['area_id'] = $area_id;
        $where['is_delete'] = 0;
        $machineList = M('Machine')->where($where)->select();
        if($machineList){
            foreach($machineList as $k=>$v){
                $machineList[$k]['userInfo'] = M('Member')->where(array('userid'=>$v['userid']))->field('realname,mobile,job_number')->find();
            }
        }
        $this->assign('machineList',$machineList);
        $this->assign('area_id',$area_id);
        $this->display('machine_list');
    }

    //绑定设备
    public function machineBind(){
        if(IS_POST){
            $area_id = I('area_id');
//            $whereMachine['area_id'] = $area_id;
//            $whereMachine['is_delete'] = 0;
//            $machineNum = M('Machine')->where($whereMachine)->count();
//            $areaInfo = M('Area_map')->where(array('area_id'=>$area_id))->find();
//            if($machineNum >= $areaInfo['employee_num']){
//                $this->error('该区域设备已绑满');
//            }
            $machine_imei = I('machine_imei');
            $where['machine_imei'] = $machine_imei;
            $data['area_id'] = $area_id;
            $result = M('Machine')->where($where)->save($data);
            if($result){
                $this->success("绑定成功");
            }else{
                $this->error('绑定失败');
            }
        }else{
            $area_id = I('area_id');
            $where['id'] = $area_id;
            $areaInfo = M("Area_map")->where($where)->find();
            //已绑定设备数
            $whereMachine['area_id'] = $area_id;
            $whereMachine['is_delete'] = 0;
            $areaInfo['machineNum'] = M('Machine')->where($whereMachine)->count();

            $this->assign($areaInfo);
            $this->display("machine_bind");
        }
    }

    //检查绑定及设备信息
    public function checkMachine()
    {
        $machine_imei = I("machine_imei") ? trim(I("machine_imei")) : $this->error("缺少参数");
        $where["machine_imei"] = $machine_imei;
        $where["is_delete"] = 0;
        $area_id = M("Machine")->where($where)->getField("area_id");
        if ($area_id) {
            $data["is_bind"] = 1;
            $data["area_name"] = M("Area_map")->where(array("id" => $area_id))->getField("area_name");
        } else {
            $data["is_bind"] = 0;
        }
        $whereM["machine_imei"] = $machine_imei;
        $whereM["is_delete"] = 0;
        $machineInfo =M("Machine")->where($whereM)->find();
        if($machineInfo){
            $data["machine"] = $machineInfo;
            $data['status'] = 1;
        }else{
            $data['status'] = 0;
        }
        $this->ajaxReturn($data);
    }

    //解除设备绑定
    public function machineUnbind(){
        $where['machine_id'] = I('machine_id');
        $data['area_id'] = 0;
        $result = M('Machine')->where($where)->save($data);
        if($result){
            $this->success("解绑成功");
        }else{
            $this->error("解绑失败");
        }
    }

	public function index(){
        $point=array(
            'lon'=>119.409641,
            'lat'=>36.849876
        );

        $arr=array(array
            (
                'lon'=>119.40091,
                'lat'=>36.854121
            ),
            array(
                'lon'=>119.421463,
                'lat'=>36.853775
            ),
            array(
                'lon'=>119.409102,
                'lat'=>36.841414
            )
        );
        $arra = '[{"lon":119.40091,"lat":36.854121},{"lon":119.421463,"lat":36.853775},{"lon":119.409102,"lat":36.841414}]';
        var_dump(json_decode($arra,true));return;
        // echo json_encode($arr);return;
        $result = $this->is_point_in_polygon($point, $arr);
        echo $result ? '在多边形内' : '不在多边形内';
    }





    /**
     * 判断一个坐标是否在一个多边形内（由多个坐标围成的）
     * 基本思想是利用射线法，计算射线与多边形各边的交点，如果是偶数，则点在多边形外，否则
     * 在多边形内。还会考虑一些特殊情况，如点在多边形顶点上，点在多边形边上等特殊情况。
     * @param $point 指定点坐标
     * @param $pts 多边形坐标 顺时针方向
     * @return bool
     */
    function is_point_in_polygon($point, $pts) {
        $N = count($pts);
        $boundOrVertex = true; //如果点位于多边形的顶点或边上，也算做点在多边形内，直接返回true
        $intersectCount = 0;//cross points count of x
        $precision = 2e-10; //浮点类型计算时候与0比较时候的容差
        $p1 = 0;//neighbour bound vertices
        $p2 = 0;
        $p = $point; //测试点

        $p1 = $pts[0];//left vertex
        for ($i = 1; $i <= $N; ++$i) {//check all rays
            // dump($p1);
            if ($p['lon'] == $p1['lon'] && $p['lat'] == $p1['lat']) {
                return $boundOrVertex;//p is an vertex
            }
            $p2 = $pts[$i % $N];//right vertex
            if ($p['lat'] < min($p1['lat'], $p2['lat']) || $p['lat'] > max($p1['lat'], $p2['lat'])) {//ray is outside of our interests
                $p1 = $p2;
                continue;//next ray left point
            }
            if ($p['lat'] > min($p1['lat'], $p2['lat']) && $p['lat'] < max($p1['lat'], $p2['lat'])) {//ray is crossing over by the algorithm (common part of)
                if($p['lon'] <= max($p1['lon'], $p2['lon'])){//x is before of ray
                    if ($p1['lat'] == $p2['lat'] && $p['lon'] >= min($p1['lon'], $p2['lon'])) {//overlies on a horizontal ray
                        return $boundOrVertex;
                    }
                    if ($p1['lon'] == $p2['lon']) {//ray is vertical
                        if ($p1['lon'] == $p['lon']) {//overlies on a vertical ray
                            return $boundOrVertex;
                        } else {//before ray
                            ++$intersectCount;
                        }
                    } else {//cross point on the left side
                        $xinters = ($p['lat'] - $p1['lat']) * ($p2['lon'] - $p1['lon']) / ($p2['lat'] - $p1['lat']) + $p1['lon'];//cross point of lng
                        if (abs($p['lon'] - $xinters) < $precision) {//overlies on a ray
                            return $boundOrVertex;
                        }
                        if ($p['lon'] < $xinters) {//before ray
                            ++$intersectCount;
                        }
                    }
                }
            } else {//special case when ray is crossing through the vertex
                if ($p['lat'] == $p2['lat'] && $p['lon'] <= $p2['lon']) {//p crossing over p2
                    $p3 = $pts[($i+1) % $N]; //next vertex
                    if ($p['lat'] >= min($p1['lat'], $p3['lat']) && $p['lat'] <= max($p1['lat'], $p3['lat'])) { //p.lat lies between p1.lat & p3.lat
                        ++$intersectCount;
                    } else {
                        $intersectCount += 2;
                    }
                }
            }
            $p1 = $p2;//next ray left point
        }

        if ($intersectCount % 2 == 0) {//偶数在多边形外
            return false;
        } else { //奇数在多边形内
            return true;
        }
    }

    public function getWarningMessage(){
        //当天时间戳
        $pointTime = strtotime("-1 day");
        $where = array("w.add_time"=>array("gt",$pointTime));
        $lastId = I('last_id');
        if(isset($lastId) && $lastId > 0){
            $where['w.id'] = array('gt',$lastId);
        }
        $where["w.type"] = array("gt",1);
        //获取最新报警信息
        $list = M('Warning_message')
            ->alias('w')
            ->join("left join __MEMBER__ m on m.userid = w.uid")
            ->field('w.*,m.realname,m.job_number,m.mobile')
            ->order('w.add_time desc')
            ->limit(5)
            ->where($where)
            ->select();
        if($list){
            foreach ($list as $i => $v){
                $list[$i]['add_time'] = mdate($v['add_time']);
                if($v['machine_id']){
                    $whereTwo['machine_id'] = $v['machine_id'];
                    $list[$i]['machine_imei'] = M('Machine')->where($whereTwo)->getField('machine_imei');
                }
            }
            $ret['status'] = 1;
            $ret['msg'] = '有新告警信息';
            $ret['data'] = $list;
            $this->ajaxReturn($ret);
        }else{
            $this->error('无新告警');
        }
    }
    // 地图设备展示
    public function map(){
        $position = I('position');
        $parent_id = I('parent_id');
        $keywords = I('keywords');
        if($position){
            $this->assign("position",$position);
            $where["m.position"] = $position;
        }
        if($parent_id){
            $this->assign("parent_id",$parent_id);
            $whereParent["m.parent_id"] = $parent_id;
            if($position != 1){
                $wherePositon["m.userid"] = $parent_id;
                $whereMember["_complex"] = array($whereParent,$wherePositon,'_logic'=>'or');
                $where["_complex"] = $whereMember;
            }else{
                $where["_complex"] = $whereParent;
            }
        }
        if($keywords){
            $where["m.realname"] = array('like', "%" . $keywords . "%");
            $this->assign('keywords',$keywords);
        }
        $add_time = date("Y-m-d",time());
        $startTime = strtotime($add_time);
        $endTime = strtotime($add_time) + 24*3600;
        $where['s.add_time'] = array('between',array($startTime,$endTime));
        $where["c.is_delete"] = 0;
        $where["m.is_delete"] = 0;
        $where["_string"] = "s.machine_imei = c.machine_imei and c.userid = m.userid";
//        var_dump($where);die;
        $subQuery = M("Site_log")->order('add_time desc')->buildSql();
        $list = M("Site_log")->table($subQuery.' s,__MACHINE__ c,__MEMBER__ m')->where($where)->field("s.*,c.machine_id,c.machine_imei,c.machine_name,c.userid,m.realname,m.job_number,m.mobile")->group("s.machine_imei")->select();
//        echo M("Site_log")->_sql();
        if($list){
            foreach($list as $k => $v){
                $list[$k]["add_time"] = date("Y-m-d H:i:s",$v["add_time"]);
            }
            $list = json_encode($list);
        }

        $newTime = date("Y-m-d",time());
        $this->assign('newTime',$newTime);
        $this->assign('list',$list);
        $this->display("map");
    }
}