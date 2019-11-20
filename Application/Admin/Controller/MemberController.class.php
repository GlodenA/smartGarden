<?php
/**
 * Created by PhpStorm.
 * User: 焱
 * Date: 2018/8/29
 * Time: 14:59
 */

namespace Admin\Controller;
class MemberController extends BaseController
{
    //员工列表
    public function memberList()
    {
        $keywords = I('keywords');
        if($keywords){
            $where['mobile|realname|job_number'] = array('like', "%".$keywords."%");
            $this->assign("keywords", $keywords);
        }
        $status = I("status");
        if($status){
            $this->assign("status",$status);
            if($status == 1){
                //在岗
                $where["job_status"] = 1;
                $where["is_delete"] = 0;

            }
            if($status == 2){
                //未上岗
                $where["job_status"] = 2;
                $where["is_delete"] = 0;

            }
            if($status == 3){
                //离岗
                $where["job_status"] = 0;
                $where["is_delete"] = 0;

            }
            if($status == 4){
                //未上岗但有考勤
                //先查出状态为未上岗并且有考勤的
                $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
                $whereMember["m.job_status"] = 2;
                $whereMember["a.add_time"] = array("gt",$beginToday);
                $whereMember["m.is_delete"] = 0;
                $whereMember["_string"] = "m.userid = a.userid";
                $useridList = M("Member")->table("__MEMBER__ m,__ATTENDANCE__ a")->where($whereMember)->field("a.userid")->select();
                $useridArr = array();
                if($useridList){
                    foreach ($useridList as $key => $value) {
                        Array_push($useridArr,$value["userid"]);
                    }
                }
                $where["userid"] = array('in',$useridArr);
                $where["job_status"] = 2;
                $where["is_delete"] = 0;

            }
            if($status == 5){
                //未上岗且无考勤
                //先查出状态为未上岗并且有考勤的
                $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
                $whereMember["m.job_status"] = 2;
                $whereMember["a.add_time"] = array("gt",$beginToday);
                $whereMember["m.is_delete"] = 0;
                $whereMember["_string"] = "m.userid = a.userid";
                $useridList = M("Member")->table("__MEMBER__ m,__ATTENDANCE__ a")->where($whereMember)->field("a.userid")->select();
                $useridArr = array();
                if($useridList){
                    foreach ($useridList as $key => $value) {
                        Array_push($useridArr,$value["userid"]);
                    }
                }
                $where["userid"] = array('not in',$useridArr);
                $where["job_status"] = 2;
                $where["is_delete"] = 0;

            }
            if($status == 6){
                //离职
                $where["is_delete"] = 1;
            }
        }
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
            $where["position"] = $position;
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
            $whereParent["parent_id"] = $parent_id;
            $wherePositon["userid"] = $parent_id;
            $whereMember["_complex"] = array($whereParent,$wherePositon,'_logic'=>'or');
            $where["_complex"] = $whereMember;
        }
        $count = M('Member')->where($where)->count();
        $Page = new \Think\Page($count, 20);
        $list = M('Member')->limit($Page->firstRow . ',' . $Page->listRows)->where($where)->order("reg_date desc")->select();
        if ($list) {
            foreach ($list as $k => $v) {
                if($v["machine_id"]>0){
                    $whereMachine["userid"] = $v["userid"];
                    $whereMachine["machine_id"] = $v["machine_id"];
                    $whereMachine["is_delete"] = 0;
                    $list[$k]["machine_imei"] = M("Machine")->where($whereMachine)->getField("machine_imei");
                    $list[$k]["area_id"] = M("Machine")->where($whereMachine)->getField("area_id");
                }
                if($v["parent_id"]>0){
                    $list[$k]["parent_name"] = M("Member")->where(array("userid"=>$v["parent_id"],"is_delete"=>0))->getField("realname");
                }else{
                    $list[$k]["parent_name"] ="";
                }
            }
        }
        $number = $Page->parameter["p"];
        if($number && $number > 0){
            $number = ($Page->parameter["p"] - 1)*20;
        }else{
            $number = 0;
        }
        $this->assign('number',$number);
        $show = $Page->show();
        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display("member_list");
    }
    // 添加员工
    public function memberAdd()
    {
        if (IS_POST) {
            $data = $_POST['info'];
            $data['reg_date'] = $data['last_date'] = $data["update_time"] = time();
            $data['reg_ip'] = $data['last_ip'] = ip();
            $whereJ['job_number'] = $data['job_number'];
            $whereJ['is_delete'] = 0;
            $isIn2 = M('Member')->where($whereJ)->find();
            if ($isIn2) {
                $this->error('员工号已存在');
            }
            if (!$data['mobile'] && !$data['realname'] && !$data['job_number']) {
                $this->error('操作失败');
            } else {
                $result = M("Member")->data($data)->add();
                if ($result) {
                    adminLog("新增员工,id为" . $result);
                    $this->success('操作成功');
                } else {
                    $this->error('操作失败');
                }
            }
        } else {
            //查询职位列表
            $positionList = M("Member_position")->where(array("is_delete"=>0))->select();
            $this->assign("positionList",$positionList);
            $this->display("member_add");
        }
    }
    public function getMemberManager(){
        //职位id
        $id = I("parent_id");
        //获取当前职位信息
        $positionInfo = M("Member_position")->where(array("id"=>$id,"is_delete"=>0))->find();
        if($positionInfo["parent_id"]>0){
            //说明当前职位有其直属领导
            $positionList = M("Member")->where(array("position"=>$positionInfo["parent_id"],"is_delete"=>0))->field("userid,realname,job_number")->select();
            if($positionList){
                $ret['data'] = $positionList;
                $ret["status"] = "1";
                $ret["info"] = "获取成功";
                $this->ajaxReturn($ret);
            }else{
                $ret["status"] = "0";
                $ret["info"] = "获取失败";
                $this->ajaxReturn($ret);
            }
        }else{
            $ret["status"] = "0";
            $ret["info"] = "获取失败";
            $this->ajaxReturn($ret);
        }
    }
    public function getMemberManagers(){
        //职位id
        $id = I("parent_id");
        //获取当前职位信息
        $positionInfo = M("Member_position")->where(array("id"=>$id,"is_delete"=>0))->find();
        $html = '';
        if($positionInfo["parent_id"]>0){
            //说明当前职位有其直属领导
            $positionList = M("Member")->where(array("position"=>$positionInfo["parent_id"],"is_delete"=>0))->field("userid,realname,job_number")->select();
            if($positionList){
                foreach($positionList as $h){
                    $html .= "<option value='{$h['userid']}'>{$h['realname']}</option>";
                }
            }
        }
        echo $html;
    }
    //员工信息修改
    public function memberEdit()
    {
        if (IS_POST) {
            $userid  = intval(I("userid"));
            $data = $_POST['info'];
            $where["userid"] = array("neq",$userid);
            $where["is_delete"] = 0;
            $where["job_number"] = $data["job_number"];
            $where["position"] = $data["position"];
            if(M("Member")->where($where)->find()){
                $this->error("员工号已存在");
            }
            $positionInfo = M("Member_position")->where(array("id"=>$data["position"],"is_delete"=>0))->find();
            if($positionInfo["parent_id"] >0){
                $data["parent_id"] = $data["parent_id"];
            }else{
                $data["parent_id"] = $data["parent_id"];
            }
            $data["update_time"] = time();
            $result =M('Member')->where(array("userid"=>$userid))->save($data);
            if ($result) {
                adminLog("编辑员工，id为" . $userid);
                $this->success("操作成功");
            } else {
                $this->error('操作失败');
            }
        } else {
            $where['userid'] = I('userid');
            $memberInfo = M('Member')->where($where)->find();
            $this->assign($memberInfo);
            //查询职位列表
            $positionList = M("Member_position")->where(array("is_delete"=>0))->select();
            $this->assign("positionList",$positionList);
            //根据当前所选职位，查询出相应直属领导列表
            $positionInfo = M("Member_position")->where(array("id"=>$memberInfo["position"],"is_delete"=>0))->find();
            if($positionInfo["parent_id"]>0){
                //说明当前职位有其直属领导
                $managerList = M("Member")->where(array("position"=>$positionInfo["parent_id"],"is_delete"=>0))->field("userid,realname,job_number")->select();
                $this->assign("managerList",$managerList);
            }
            $this->display("member_edit");
        }
    }

    // 浏览员工信息
    public function memberInfo()
    {
        $where['userid'] = I("userid");
        $memberInfo = M('Member')->where($where)->find();
        if($memberInfo["parent_id"]>0){
            $memberInfo["parent_name"] =  M("Member")->where(array("userid"=>$memberInfo["parent_id"],"is_delete"=>0))->getField("realname");
        }else{
            $memberInfo["parent_name"] ="";
        }
        $this->assign($memberInfo);
        adminLog("查看用户" . $memberInfo["userid"] . "详情");
        $this->display("member_info");
    }

    /**
     * 员工删除
     */
    public function memberDelete()
    {
        if (IS_POST) {
            $userid = I("userid");
            M('Member')->where(array("userid" => $userid))->setField("is_delete", 1);
            M('Machine')->where(array("userid" => $userid,"is_delete"=>0))->setField("userid", 0);
            M('Machine_bind')->where(array("userid" => $userid,"status"=>1))->setField("status", 0);
            $this->success('操作成功');
        }
    }

    /**
     * 用户批量删除
     */
    public function membersDelete(){
        if(IS_POST){
            $userids = explode(",",$_POST['userids']);
            $data['is_delete'] = 1;
            $data["update_time"] = time();
            foreach ($userids as $key => $v) {
                M("Member")->where(array("userid"=>$v))->data($data)->save();
                M('Machine')->where(array("userid" => $v,"is_delete"=>0))->setField("userid", 0);
                M('Machine_bind')->where(array("userid" => $v,"status"=>1))->setField("status", 0);
                adminLog("员工删除,id为".$v);
            }
            $this->success('删除成功');
        }
    }

    //绑定设备
    public function machineBind()
    {
        if (IS_POST) {
            $userid = I("userid");
            $machine_imei = I("machine_imei");
            $memberInfo = M('Member')->where(array('userid'=>$userid))->find();
            $machineInfo = M('Machine')->where(array('machine_imei'=>$machine_imei,"is_delete"=>0))->find();
//            var_dump($machineInfo);
            $check["machine_id"] = $memberInfo['machine_id'];
            $check["status"] = 1 ;
            $machine_bind = M("Machine_bind")->where($check)->find();
//            var_dump($machine_bind);die;
            $data["machine_id"] = $machineInfo['machine_id'];
            $data["userid"] = $userid;
            $data["add_time"]= $data["update_time"] = time();
            $data["status"] = 1;
            $res = M("Machine_bind")->add($data);

            if($res){
                $memberWhere['userid'] = $userid;
                $memberData['machine_id'] = $machineInfo['machine_id'];
                M('Member')->where($memberWhere)->save($memberData);
                $machineData['userid'] = $userid;
                M('Machine')->where(array('machine_imei'=>$machine_imei,"is_delete"=>0))->save($machineData);
                if($machine_bind){
                    $set["machine_imei"] = $machine_imei;
                    $set["userid"] = $machine_bind['userid'];
                    $setData['status'] = 0;
                    M("Machine_bind")->where($set)->save($setData);

                    $machineWhere['machine_id'] = $machine_bind['machine_id'];
                    $machineDataOld['userid'] = 0;
                    M('Machine')->where($machineWhere)->save($machineDataOld);
                }
                $this->success("绑定成功");
            } else {
                $this->error("绑定失败");
            }
        } else {
            $userid = I("userid");
            $where["userid"] = $userid;
            $where["is_delete"] = 0;
            $imei = M("Machine")->where($where)->getField("machine_imei");
            if ($imei) {
                $machineInfo = M("Machine")->where(array("machine_imei" => $imei,"is_delete"=>0))->find();
                $this->assign("machineInfo", $machineInfo);
            } else {
                $this->assign("machine_imei", "");
            }
            $userinfo = M("Member")->where(array("userid" => $userid))->field("userid,realname,mobile,job_number")->find();
            $this->assign($userinfo);
            $this->display("machine_bind");
        }
    }
    //检查绑定及设备信息
    public function checkMachine()
    {
        $machine_imei = I("machine_imei") ? trim(I("machine_imei")) : $this->error("缺少参数");
        $where["machine_imei"] = $machine_imei;
        $where["is_delete"] = 0;
//        $where["status"] = 1;
        $machineInfo = M("Machine")->where($where)->field("machine_name,machine_imei,userid")->find();
        if ($machineInfo['userid']) {
            $data["is_bind"] = 1;
            $memberInfo =  M("Member")->where(array("userid" => $machineInfo['userid']))->field("realname,job_number")->find();
            $data["job_number"] = $memberInfo["job_number"];
            $data["realname"] = $memberInfo["realname"];
        } else {
            $data["is_bind"] = 0;
        }
        $data["machine_name"] = $machineInfo['machine_name'];
        $data["machine_imei"] = $machineInfo['machine_imei'];
        $data['status'] = 1;
        $this->ajaxReturn($data);
    }

    //解绑设备
    public function machineUnbind()
    {
        if (IS_POST) {
            $userid= I("userid") ? I("userid") : $this->error("缺少参数");
            $where["userid"] = $userid;
            $where["status"] = 1;
            $res = M("Machine_bind")->where($where)->setField("status", 0);
            $machineData['userid'] = 0;
            M('Machine')->where(array('userid'=>$userid,"is_delete"=>0))->save($machineData);
            $memberData['machine_id'] = 0;
            M('Member')->where(array('userid'=>$userid))->save($memberData);
            if($res) {
                $this->success("解绑成功");
            }else{
                $this->error("解绑失败");
            }
        }
    }

    public function importFile(){
        $file_name='./'.I("filename");
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
            $data['realname'] = $sheet->getCell("A".$i)->getValue();
            $data['mobile'] = $sheet->getCell("B".$i)->getValue();
            $where["job_number"] = $data['job_number'] = $sheet->getCell("C".$i)->getValue();
            $data['position'] = $sheet->getCell("D".$i)->getValue();
            $data['sex'] = $sheet->getCell("E".$i)->getValue();
            $where["is_delete"] = 0;
            $isIn = M("Member")->where($where)->find();
            if(!$isIn && $data['mobile'] && $data['job_number']){
                $data["reg_date"] = time();
                M('Member')->add($data);
            }
        }
        $this->success('导入成功');
    }
    /**
     * 员工批量切换养护经理
     */
    public function membersManagerChange(){
        if(IS_POST){
            $parent_id = I("parent_id");
            if($parent_id>0){
                $data["parent_id"] = $parent_id;
            }else{
                $data["parent_id"] = 0;
            }
            $userids = explode(",",$_POST['userids']);
            $data["update_time"] = time();
            foreach ($userids as $key => $v) {
                M("Member")->where(array("userid"=>$v))->data($data)->save();
                adminLog("员工切换管理人员,id为".$v);
            }
            $this->success('切换成功');
        }else{
            $userids = I("userids");
            $this->assign("userids",$userids);
            $wherePosition["mm.parent_id"] = 0;
            $wherePosition["mm.is_delete"] = 0;
            $wherePosition["mp.is_delete"] = 0;
            $wherePosition["_string"] = "mp.position = mm.id";
            $managerList = M("Member")->table("__MEMBER__ mp,__MEMBER_POSITION__ mm")->where($wherePosition)->field("userid,realname,job_number")->select();
            $this->assign("managerList",$managerList);
            $this->display("manager_change");
        }
    }
    /**
     * 职位列表
     */
    public function menuLists(){
        $tree = new \Org\Tree\Tree;
        $where["is_delete"]=0;
        $data = M("Member_position")->where($where)->order('id desc')->select();
        $menuList = $tree->makeTree($data);
        $this->assign("menuList",$menuList);
        adminLog("查看职位列表");
        $this->display("group_list");
    }
    // 菜单添加
    public function menuAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $result = M("Member_position")->add($data);
            if($result){
                $this->success("操作成功");
                adminLog("增加职位".$data["name"]);
            }else{
                $this->error("操作失败");
            }
        }else{
            $where['parent_id'] = 0;
            $data = M("Member_position")->where($where)->select();
            $this->assign("menuList",$data);
            layout(false);
            $this->display("group_add");
        }

    }
    // 职位编辑
    public function menuEdit(){
        if(IS_POST){
            $id = $where['id'] = I("id");
            $data = $_POST['info'];
            M("Member_position")->where($where)->save($data);
            adminLog("修改职位信息".$data["name"]);
            $this->success("操作成功");
        }else{
            $id = $where['id'] = I("id");
            $whereParent['parent_id'] = 0;
            $parentData = M("Member_position")->where($whereParent)->select();
            $this->assign("parentData",$parentData);
            $data = M("Member_position")->where($where)->find();
            $this->assign($data);
            layout(false);
            $this->display('group_edit');
        }
    }
    // 菜单删除
    public function menuDelete(){
        if(IS_POST){
            $id = I('id');
            $memberInfo = M("Member")->where(array("is_delete"=>0,"position"=>$id))->find();
            if($memberInfo){
                $this->error("当前职位下存在员工，无法删除！");
            }
            $whereData['parent_id'] = $id;
            $menuData = M("Member_position")->where($whereData)->find();
            if($menuData){
                $this->error("请先删除二级职位");
            }else{
                $where['id'] = $id;
                $data['is_delete'] =1;
                M("Member_position")->where($where)->save($data);
                adminLog("删除职位，ID为".$id);
                $this->success("删除成功");
            }
        }
    }
    //员工统计表导出
    function memberExcel(){
        $keywords = I('keywords');
        if($keywords){
            $where['mobile|realname|job_number'] = array('like', "%".$keywords."%");
        }
        $status = I("status");
        if($status){
            if($status == 1){
                //在岗
                $where["job_status"] = 1;
                $fileName = $title = "在岗员工统计表";
            }
            if($status == 2){
                //未上岗
                $where["job_status"] = 2;
                $fileName = $title = "未上岗员工统计表";
            }
            if($status == 3){
                //离岗
                $where["job_status"] = 0;
                $fileName = $title = "离岗员工统计表";
            }
            if($status == 4){
                //未上岗但有考勤
                //先查出状态为未上岗并且有考勤的
                $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
                $whereMember["m.job_status"] = 2;
                $whereMember["a.add_time"] = array("gt",$beginToday);
                $whereMember["m.is_delete"] = 0;
                $whereMember["_string"] = "m.userid = a.userid";
                $useridList = M("Member")->table("__MEMBER__ m,__ATTENDANCE__ a")->where($whereMember)->field("a.userid")->select();
                $useridArr = array();
                if($useridList){
                    foreach ($useridList as $key => $value) {
                        Array_push($useridArr,$value["userid"]);
                    }
                }
                $where["userid"] = array('in',$useridArr);
                $where["job_status"] = 2;
                $fileName = $title = "未上岗但有考勤员工统计表";
            }
            if($status == 5){
                //未上岗且无考勤
                //先查出状态为未上岗并且有考勤的
                $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
                $whereMember["m.job_status"] = 2;
                $whereMember["a.add_time"] = array("gt",$beginToday);
                $whereMember["m.is_delete"] = 0;
                $whereMember["_string"] = "m.userid = a.userid";
                $useridList = M("Member")->table("__MEMBER__ m,__ATTENDANCE__ a")->where($whereMember)->field("a.userid")->select();
                $useridArr = array();
                if($useridList){
                    foreach ($useridList as $key => $value) {
                        Array_push($useridArr,$value["userid"]);
                    }
                }
                $where["userid"] = array('not in',$useridArr);
                $where["job_status"] = 2;
                $fileName = $title = "未上岗且无考勤员工统计表";
            }
        }else{
            $fileName = $title = "员工统计表";
        }
        if(I("position")){
            $where["position"] = I("position");
        }
        if(I("parent_id")){
            $whereParent["parent_id"] = I("parent_id");
            $wherePositon["userid"] = I("parent_id");
            $whereMember["_complex"] = array($whereParent,$wherePositon,'_logic'=>'or');
            $where["_complex"] = $whereMember;
        }
        $where["is_delete"] = 0;
        $list = M("Member")->where($where)->field("userid,job_number,realname,mobile,machine_id,parent_id")->select();
        if($list){
            $headArr = array("员工号","姓名","手机号","设备IMEI","分管人员","备注");
            // 单元格宽度(必须使用"A"=>30的形式)
            $widthExcel = array('A'=>11,'B'=>15,'C'=>15,'D'=>20,'E'=>16,'F'=>25);
            foreach($list as $k =>$v){
                $data[$k]["job_number"] = $v["job_number"];
                $data[$k]["realname"] = $v["realname"];
                $data[$k]["mobile"] = $v["mobile"]."\t";
                if($v["machine_id"]>0){
                    $data[$k]["machine_imei"] = M('Machine')->where(array('userid'=>$v["userid"],'machine_id'=>$v["machine_id"],"is_delete"=>0))->getField("machine_imei");
                    $data[$k]["machine_imei"] = $data[$k]["machine_imei"]."\t";
                }else{
                    $data[$k]["machine_imei"] = "";
                }
                if($v["parent_id"]>0){
                    $data[$k]["parent_name"] = M('Member')->where(array('userid'=>$v["parent_id"],"is_delete"=>0))->getField("realname");
                }else{
                    $data[$k]["parent_name"] = "";
                }
            }
            if($data){
                $sheetName = "人员名单";
                $this->getExcel($fileName,$sheetName,$title,$headArr,$data,$widthExcel);
            }
            echo "success";
        }else{
            $this->error("暂无数据导出");
        }
    }
    /**
     * excel导出 chen
     * @param $fileName 导出的文件名
     * @param $sheetName 设置sheet名称
     * @param $headArr excel头
     * @param $data 数据
     */
    function getExcel($fileName,$sheetName,$title,$headArr,$data,$widthExcel){
        vendor("PHPExcel.PHPExcel");
        //对数据进行检验
        if(empty($data) || !is_array($data)){
            $this->error("暂无可导出数据");
        }
        $fileName = $fileName;
        $date = date("Y_m_d",time());
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
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$_row, $title."  统计日期：  ".date('Y-m-d', time()));  //设置合并后的单元格内容
            // $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getFont()->setName('黑体');
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getFont()->setBold(true);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getFont()->setSize(18);
            //设置数据居中
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getAlignment()->setWrapText(true);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle('A'.$_row)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
            //设置单元格高度
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension($_row)->setRowHeight(30);
            $_row++;
            $objPHPExcel->getActiveSheet(0)->mergeCells('A'.$_row.':'.$cellName[$_cnt-1].$_row);   //合并单元格
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$_row, '报表生成时间： '.date('Y-m-d H:i',time()).'          分管部室负责人：        ');  //设置合并后的单元格内容
            //设置单元格高度
            $objPHPExcel->setActiveSheetIndex(0)->getRowDimension($_row)->setRowHeight(30);
            $_row++;
            $i = 0;
            foreach($headArr AS $v){
                //设置列标题
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].$_row, $v);
                // $objPHPExcel->setActiveSheetIndex(0)->getColumnDimension($cellName[$i])->setWidth(14);//设置列宽度
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
    /**
     * excel导出 chen
     * @param $fileName 导出的文件名
     * @param $headArr excel头
     * @param $data 数据
     */
//     function getExcel($fileName,$headArr,$data,$widthExcel){
//         vendor("PHPExcel.PHPExcel");
//         //对数据进行检验
//         if(empty($data) || !is_array($data)){
//             die("data must be a array");
//         }
//         //检查文件名
//         if(empty($fileName)){
//             exit;
//         }
//         $date = date("Y_m_d",time());
//         $fileName .= "_{$date}.xls";
//         //创建PHPExcel对象，注意，不能少了\
//         $objPHPExcel = new \PHPExcel();
//         $objProps = $objPHPExcel->getProperties();
//         $styleThinBlackBorderOutline = array(
//             'borders' => array(
//                 'allborders' => array( //设置全部边框
//                     'style' => \PHPExcel_Style_Border::BORDER_THIN //粗的是thick
//                 ),

//             ),
//         );
//         // 设置宽度
//         if($widthExcel){
//             foreach ($widthExcel as $k => $v) {
//                 $objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth($v);
//             }
//         }
//         //设置表头
//         $key = ord("A");
//         foreach($headArr as $v){
//             $colum = chr($key);
//             $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
//             $key += 1;
//         }
//         $column = 2;
//         $objActSheet = $objPHPExcel->getActiveSheet();
//         foreach($data as $key => $rows){ //行写入
//             $span = ord("A");
//             foreach($rows as $keyName=>$value){// 列写入
//                 $j = chr($span);
// //                $objActSheet->setCellValue($j.$column, $value);
//                 $objActSheet->setCellValueExplicit($j.$column, $value, \PHPExcel_Cell_DataType::TYPE_STRING);
//                 $span++;
//             }
//             $column++;
//         }
//         $objPHPExcel->getActiveSheet()->getStyle('A1:D'.($column-1))->applyFromArray($styleThinBlackBorderOutline);
//         $fileName = iconv("utf-8", "gb2312", $fileName);
//         //重命名表
//         // $objPHPExcel->getActiveSheet()->setTitle('test');
//         //设置活动单指数到第一个表,所以Excel打开这是第一个表
//         $objPHPExcel->setActiveSheetIndex(0);
//         ob_end_clean();
//         ob_start();
//         header('Content-Type: application/vnd.ms-excel');
//         header("Content-Disposition: attachment;filename=\"$fileName\"");
//         header('Cache-Control: max-age=0');
//         $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//         $objWriter->save('php://output'); //文件通过浏览器下载
//         // memberLog("数据导出");
//         exit;
//     }

}
