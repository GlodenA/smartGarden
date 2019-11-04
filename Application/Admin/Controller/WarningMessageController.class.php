<?php
/**
 * Created by PhpStorm.
 * User: 焱
 * Date: 2019/2/18
 * Time: 14:59
 */
namespace Admin\Controller;
class WarningMessageController extends BaseController
{
    public function __construct(){
        parent::__construct();
    }

    //告警列表
    public function warningMessageList(){
        $keywords = I('keywords');
        if($keywords){
            $where['a.job_number|a.realname'] = array('like', "%".$keywords."%");
            $this->assign('keywords',$keywords);
        }
        $type = I("type");
        if($type){
            $where["w.type"] = array(array("gt",1),array("eq",$type),"and");
            $this->assign('type',$type);
        }else{
            $where["w.type"] = array("gt",1);
        }
        if(I('start_time')){
            $start_time = strtotime(I('start_time'));
        }
        if(I('end_time')){
            $end_time = strtotime(I('end_time'))+24*3600-1;
        }
        if($start_time && $end_time){
            $where['w.add_time'] = array('between',array($start_time,$end_time));
            $this->assign('start_time',I('start_time'));
            $this->assign('end_time',I('end_time'));
        }elseif($start_time && !$end_time){
            $end_time = $start_time+24*3600-1;
            $where['w.add_time'] = array('between',array($start_time,$end_time));
            $this->assign('start_time',I('start_time'));
            $this->assign('end_time',I('start_time'));
        }elseif(!$start_time && $end_time){
            $start_time = $end_time-24*3600+1;
            $where['w.add_time'] = array('between',array($start_time,$end_time));
            $this->assign('start_time',I('end_time'));
            $this->assign('end_time',I('end_time'));
        }

        $where['_string']='w.uid=m.userid and a.userid=m.userid';
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
            $where["a.position"] = $position;
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
            $whereParent["a.parent_id"] = $parent_id;
            $wherePositon["a.userid"] = $parent_id;
            $whereMember["_complex"] = array($whereParent,$wherePositon,'_logic'=>'or');
            $where["_complex"] = $whereMember;
        }
        $count =M("Warning_message")->table('__WARNING_MESSAGE__ w,__MEMBER__ a,__MACHINE__ m')->where($where)->count();
        $Page = new \Think\Page($count,20);
        $warningMessageList = M()
            ->table('__WARNING_MESSAGE__ w,__MEMBER__ a,__MACHINE__ m')
            ->where($where)
            ->field('w.type,w.uid,w.add_time,a.realname,a.job_number,m.machine_id,m.machine_imei,m.machine_name,a.position,a.parent_id')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order("w.add_time desc")
            ->select();
        $number = $Page->parameter["p"];
        if($number && $number > 0){
            $number = ($Page->parameter["p"] - 1)*20;
        }else{
            $number = 0;
        }
        $this->assign('number',$number);
        $this->assign("page",$Page->show());
        $this->assign("warningMessageList",$warningMessageList);
        $this->display("warning_list");
    }
}