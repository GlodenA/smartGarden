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
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday = $beginToday + 24*60*60-1;
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
        if($start_time && $end_time){
            $where['w.add_time'] = array('between',array($start_time,$end_time));
        }elseif($start_time && !$end_time){
            $end_time = $start_time+24*3600-1;
            $where['w.add_time'] = array('between',array($start_time,$end_time));
        }elseif(!$start_time && $end_time){
            $start_time = $end_time-24*3600+1;
            $where['w.add_time'] = array('between',array($start_time,$end_time));
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
    //统计表导出
    function warningExcel(){
        $keywords = I('keywords');
        if($keywords){
            $where['a.job_number|a.realname'] = array('like', "%".$keywords."%");
        }
        $type = I("type");
        if($type){
            $where["w.type"] = array(array("gt",1),array("eq",$type),"and");
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
        }elseif($start_time && !$end_time){
            $end_time = $start_time+24*3600-1;
            $where['w.add_time'] = array('between',array($start_time,$end_time));
        }elseif(!$start_time && $end_time){
            $start_time = $end_time-24*3600+1;
            $where['w.add_time'] = array('between',array($start_time,$end_time));
        }
        $where['_string']='w.uid=m.userid and a.userid=m.userid';
        //查询职位列表
        $position = I("position");
        if($position){
            $where["a.position"] = $position;
        }
        $parent_id = I("parent_id");
        if($parent_id){
            $whereParent["a.parent_id"] = $parent_id;
            $wherePositon["a.userid"] = $parent_id;
            $whereMember["_complex"] = array($whereParent,$wherePositon,'_logic'=>'or');
            $where["_complex"] = $whereMember;
        }
        $list = M()
            ->table('__WARNING_MESSAGE__ w,__MEMBER__ a,__MACHINE__ m')
            ->where($where)
            ->field('w.type,w.uid,w.add_time,a.realname,a.job_number,m.machine_id,m.machine_imei,m.machine_name,a.position,a.parent_id')
            ->order("w.add_time desc")
            ->select();
        if($list){
            $headArr = array("员工号","姓名","养护经理","告警时间","告警详情","备注");
            // 单元格宽度(必须使用"A"=>30的形式)
            $widthExcel = array('A'=>11,'B'=>15,'C'=>15,'D'=>20,'E'=>16,'F'=>25);
            foreach($list as $k =>$v){
                $data[$k]["job_number"] = $v["job_number"];
                $data[$k]["realname"] = $v["realname"];
                if($v["parent_id"]>0){
                    $data[$k]["parent_name"] = M('Member')->where(array('userid'=>$v["parent_id"],"is_delete"=>0))->getField("realname");
                }else{
                    $data[$k]["parent_name"] = "";
                }
//                if($v["machine_id"]>0){
//                    $data[$k]["machine_imei"] = M('Machine')->where(array('userid'=>$v["userid"],'machine_id'=>$v["machine_id"],"is_delete"=>0))->getField("machine_imei");
//                    $data[$k]["machine_imei"] = $data[$k]["machine_imei"]."\t";
//                }else{
//                    $data[$k]["machine_imei"] = "";
//                }
                $data[$k]["add_time"] = date("Y-m-d H:i",$v["add_time"]);
                if($v["type"] == 2){
                    $data[$k]["type"] = "远离区域报警";
                }elseif($v["type"] == 3){
                    $data[$k]["type"] = "低电量报警";
                }elseif($v["type"] == 4){
                    $data[$k]["type"] = "迟到报警";
                }elseif($v["type"] == 5){
                    $data[$k]["type"] = "旷工报警";
                }elseif($v["type"] == 6){
                    $data[$k]["type"] = "早退报警";
                }elseif($v["type"] == 7){
                    $data[$k]["type"] = "怠工报警";
                }
                $data[$k]["remark"] = "";;
            }
            if($data){
                $sheetName = "告警统计";
                $this->getExcel($start_time,$end_time,$sheetName,$headArr,$data,$widthExcel);
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
    function getExcel($start_time,$end_time,$sheetName,$headArr,$data,$widthExcel){
        vendor("PHPExcel.PHPExcel");
        //对数据进行检验
        if(empty($data) || !is_array($data)){
            $this->error("暂无可导出数据");
        }
        $fileName = "告警统计表";
        $date = date("Y_m_d",time());
        $fileName .= "--{$date}.xls";
        if($start_time && $end_time){
            $title =  '告警统计表  统计日期： '.date('Y-m-d', $start_time).' 至 '.date('Y-m-d', $end_time);
        }else{
            $title = '告警统计表';
        }
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
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$_row,$title);  //设置合并后的单元格内容
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
        exit;
    }
}
