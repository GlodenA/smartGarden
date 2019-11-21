<?php
// 后台菜单管理
namespace Admin\Controller;
class SchedulesController extends BaseController {
	//排班列表
	public function schedulesList(){
//		$keywords = I('keywords');
//        if($keywords){
//            $where['schedules_name'] = array('like',"%".$keywords."%");
//            $this->assign("keywords",$keywords);
//        }
//		$where['is_delete'] = 0;
//		$count = M("Schedules_setting")->where($where)->count();
//        $Page = new \Think\Page($count,20);
//        $list = M("Schedules_setting")->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('schedules_id desc')->select();
//        if($list){
//            $work_day = '';
//        	foreach($list as $k=>$v){
//	        	$list[$k]['work_day'] = explode(",",$v['work_day']);
//	        	foreach ($list[$k]['work_day']as $kkk => $vvv) {
//	        		if($vvv == 1){
//	        			$work_day = $work_day."周一";
//	        		}elseif($vvv == 2){
//	        			$work_day = $work_day." 周二";
//	        		}elseif($vvv == 3){
//	        			$work_day = $work_day." 周三";
//	        		}elseif($vvv == 4){
//	        			$work_day = $work_day." 周四";
//	        		}elseif($vvv == 5){
//	        			$work_day = $work_day." 周五";
//	        		}elseif($vvv == 6){
//	        			$work_day = $work_day." 周六";
//	        		}elseif($vvv == 0){
//	        			$work_day = $work_day." 周日";
//	        		}
//	        	}
//	        	$list[$k]['work_day'] = $work_day;
//	        	$work_day = '';
//	        	$list[$k]['time_id'] = explode(",",$v['time_id']);
//	        	foreach ($list[$k]['time_id']as $kk => $vv) {
//	        		$list[$k]['timeList'][] = M("Schedules_time")->where(array('is_delete'=>0,'id'=>$vv))->find();
//	        	}
//	        }
//        }
//        $show = $Page->show();
//        $this->assign('page',$show);
        $this->assign('list','');
		$this->display('schedules_list');
	}
    public function getSchedulesList(){
        $keywords = I('keywords');
        if($keywords){
            $where['schedules_name'] = array('like',"%".$keywords."%");
            $this->assign("keywords",$keywords);
        }

        $where['is_delete'] = 0;
        $count = M("Schedules_setting")->where($where)->count();
        $listRows=10;
        $firstRow = $listRows*(I("page")-1);
        $list = M("Schedules_setting")->limit($firstRow.','.$listRows)->where($where)->order('schedules_id desc')->select();
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
        $ret["totalNumber"]=$count;
        $ret["list"] = $list;
        $this->ajaxReturn($ret);
    }

	//班组添加
	public function schedulesAdd(){
		if(IS_POST){
            $data = $_POST['info'];
            $data['work_day'] = implode(",",$data['work_day']);
            if(count($data['time_id']) > 2){
                $this->error("时间段最多选择两个，并且要合理");
            }
            $data['time_id'] = implode(",",$data['time_id']);
            $result = M('Schedules_setting')->add($data);
            if($result){
                $this->success("设置成功");
                adminLog("添加班组".$data["schedules_name"]);
            }else{
                $this->error("设置失败");
            }
        }else{
        	$whereTime['is_delete'] = 0;
        	$whereTime['is_show'] = 1;
        	$timeList = M("Schedules_time")->where($whereTime)->order('id asc')->select();
        	$this->assign('timeList',$timeList);
            $this->display("schedules_add");
        }
	}
	public function getAllTimeList(){
        $whereTime['is_delete'] = 0;
        $whereTime['is_show'] = 1;
        $timeList = M("Schedules_time")->where($whereTime)->order('id asc')->select();
        $ret["timeList"]=$timeList;
        $this->ajaxReturn($ret);
    }
    //获取编辑班组信息
    public function getEditSchedules(){
        $schedules_id = I('schedules_id');
        $where['schedules_id'] = $schedules_id;
        $schedulesInfo = M('Schedules_setting')->where($where)->find();
        $schedulesInfo['work_day'] = explode(',',$schedulesInfo['work_day']);
        $schedulesInfo['time_id'] = explode(',',$schedulesInfo['time_id']);
        // var_dump($schedulesInfo);die;
        //$this->assign($schedulesInfo);
        $whereTime['is_delete'] = 0;
        $whereTime['is_show'] = 1;
        $timeList = M("Schedules_time")->where($whereTime)->order('id asc')->select();
        $ret["schedulesInfo"] = $schedulesInfo;
        $ret["list"] = $timeList;
        $this->ajaxReturn($ret);

    }
    //编辑班组
	public function schedulesEdit(){
		if(IS_POST){
			$schedules_id = I('schedules_id');
        	$where['schedules_id'] = $schedules_id;
            $data = $_POST['info'];
            if(count($data['time_id']) > 2){
                $this->error("时间段最多选择两个，并且要合理");
            }
            $data['work_day'] = implode(",",$data['work_day']);
            $data['time_id'] = implode(",",$data['time_id']);
            $result = M('Schedules_setting')->where($where)->save($data);
            if($result){
                $this->success("设置成功");
                adminLog("编辑时间段".$data["name"]);
            }else{
                $this->error("设置失败或者未修改");
            }
        }else{
        	$schedules_id = I('schedules_id');
        	$where['schedules_id'] = $schedules_id;
        	$schedulesInfo = M('Schedules_setting')->where($where)->find();
            $schedulesInfo['work_day'] = explode(',',$schedulesInfo['work_day']);
            $schedulesInfo['time_id'] = explode(',',$schedulesInfo['time_id']);
            // var_dump($schedulesInfo);die;
        	$this->assign($schedulesInfo);
            $whereTime['is_delete'] = 0;
            $whereTime['is_show'] = 1;
            $timeList = M("Schedules_time")->where($whereTime)->order('id asc')->select();
            $this->assign('timeList',$timeList);
            $this->display("schedules_edit");
        }
	}


	//删除班组
	public function schedulesDelete(){
		$schedules_id = I('schedules_id');
        $where['schedules_id'] = $schedules_id;
        $data['is_delete'] =1;
        $result = M('Schedules_setting')->where($where)->save($data);
        if($result){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
	}


	//时间列表
	public function timeList(){
//		$keywords = I('keywords');
//    if($keywords){
//        $where['title'] = array('like',"%".$keywords."%");
//        $this->assign("keywords",$keywords);
//    }
//		$where['is_delete'] = 0;
//		$count = M("Schedules_time")->where($where)->count();
//    $Page = new \Think\Page($count,10);
//        $listRows=10;
//        $firstRow = $listRows*(I("currentPage")-1);
//    $list = M("Schedules_time")->limit($firstRow.','.$listRows)->where($where)->order('id asc')->select();
//    $show = $Page->show();
//    $this->assign('page',$show);
//		$this->assign('total', $count);
    $this->assign('list',[]);
//		$getPage = function(){
//			return $this->nowPage;
//		};
//		$getPageMethod = $getPage->bindTo($Page, \Think\Page::class);
//		$currentPage = $getPageMethod();
//		$this->assign('currentPage', $currentPage);
		$this->display('time_list');
	}
	public function getTimeList(){

        $keywords = I('keywords');
        if($keywords){
            $where['title'] = array('like',"%".$keywords."%");
            $this->assign("keywords",$keywords);
        }
        $where['is_delete'] = 0;
        $count = M("Schedules_time")->where($where)->count();
//    $Page = new \Think\Page($count,10);
        $listRows=10;
        $firstRow = $listRows*(I("currentPage")-1);
        $list = M("Schedules_time")->limit($firstRow.','.$listRows)->where($where)->order('id asc')->select();
        $ret["totalNumber"]=$count;
        $ret["list"]=$list;
        $this->ajaxReturn($ret);
}

	//添加时间段
	public function timeAdd(){
		if(IS_POST){
            $data = $_POST['info'];
            // $data['start_time'] = strtotime($data['start_time']);
            // $data['end_time'] = strtotime($data['end_time']);
            $result = M('Schedules_time')->add($data);
            $this->success("设置成功");
            adminLog("添加时间段".$data["name"]);
        }else{
            $this->display("time_add");
        }
	}

	//编辑时间段
	public function timeEdit(){
		if(IS_POST){
			$id = I('id');
        	$where['id'] = $id;
            $data = $_POST['info'];
            // $data['start_time'] = strtotime($data['start_time']);
            // $data['end_time'] = strtotime($data['end_time']);
            $result = M('Schedules_time')->where($where)->save($data);
            if($result){
                $this->success("设置成功");
                adminLog("添加时间段".$data["name"]);
            }else{
                $this->error("设置失败");
            }
        }else{
        	$id = I('id');
        	$where['id'] = $id;
        	$timeInfo = M('Schedules_time')->where($where)->find();
        	$this->assign($timeInfo);
            $this->display("time_edit");
        }
	}

	//删除时间段
	public function timeDelete(){
		$id = I('id');
        $where['id'] = $id;
        $data['is_delete'] =1;
        $result = M('Schedules_time')->where($where)->save($data);
        if($result){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
	}

	public function groupAttendence(){
		$this->display('group_attendence');
	}

}
