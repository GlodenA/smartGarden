<?php
/**
 * Created by PhpStorm.
 * User: liwenlin
 * Date: 2018/9/10
 * Time: 下午2:28
 */

namespace App\Controller;
use Think\Controller;
class AlarmController extends BaseController
{


    public function _initialize()
    {

    }

    public function getAlarmInfo(){
        $pageSize = I('pageSize') ?I('pageSize') : 20;
        $pageNum = I('pageNum') ? I('pageNum') : 1;
        $startCount = ($pageNum - 1) * $pageSize; 
        $where['_string']='w.uid = m.userid';
        $alarmList = M()
        ->table('__MEMBER__ m,__WARNING__MESSAGE__ w')
        ->where($where)
        ->field("m.*,w.add_time,w.type")
        ->limit($startCount,$pageSize)
        ->select();
        if($alarmList){
            foreach ($alarmList as $i => $v) {
                // $resMsg[$i]["surface_plot"] = thumb($v["surface_plot"],320,240);
                $alarmList[$i]["add_time"] = date("Y-m-d H:i:s",$v["add_time"]);
            }
            $this->httpResponse(200,"信息获取成功",$alarmList);
        }else{
            $msg = $pageNum == 1 ? "无消息内容" : "已无更多消息内容";
            $this->httpResponse(400,$msg);
        }
    }
   
}