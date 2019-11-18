<?php
namespace Admin\Controller;
use Think\Controller;
// use Think\Log;
class WorkerManController extends Controller{
    public function ceshi(){
        $id = I("id");
        var_dump($id);
        $where["id"] = $id;
        $where["is_show"] = 1;
        $where["is_delete"] = 0;
        var_dump($where);
        $timeInfo = M("Schedules_time")->where($where)->find();
        var_dump($timeInfo);
        if(!$timeInfo){
            var_dump("无视");
            //对应时间段已删除，无需打卡
            return;
        }
        
        $year = date("Y-m-d",time());
        //判断是否跨天
        if($this->checkOneDay($timeInfo['start_time'],$timeInfo['end_time'])){
            echo "111111";
            $time = I("times") ? I("times") :time();
            //跨天
            if($time>(strtotime($year.' '.$timeInfo["end_time"])+2*60*60)){
                $timeInfoAmStart = strtotime($year.' '.$timeInfo["start_time"]);
                $timeInfoAmEnd = strtotime(date('Y-m-d',strtotime('+1 day')).' '.$timeInfo["end_time"]);
            }else{
                $timeInfoAmStart = strtotime(date('Y-m-d',strtotime('-1 day')).' '.$timeInfo["start_time"]);
                $timeInfoAmEnd = strtotime($year.' '.$timeInfo["end_time"]);
            }
        }else{
            echo "2222";
            //不跨天拼接时间段
            $timeInfoAmStart = strtotime($year.' '.$timeInfo["start_time"]);
            $timeInfoAmEnd = strtotime($year.' '.$timeInfo["end_time"]);
        }
        //查询考勤设置
        $attendanceSetInfo = M("Attendance_setting")->where(array("id"=>1))->find();
        var_dump($timeInfoAmStart);
        var_dump($timeInfoAmEnd);
        var_dump(date("Y-m-d H:i:s",$timeInfoAmStart));
        var_dump(date("Y-m-d H:i:s",$timeInfoAmEnd));

        //拼接允许工作时间段
        $timeStart1 = $timeInfoAmStart-$attendanceSetInfo["error_time"];
        $timeEmd1 = $timeInfoAmEnd+$attendanceSetInfo["error_time"];
        return;
        // $beginToday = 1565904861;
        // $whereTodayMgc["m.is_delete"] = 0;
        // $whereTodayMgc["s.add_time"] = array("gt",$beginToday);
        // $whereTodayMgc["_string"] = "m.machine_id = s.machine_id";
        // $today_mount_guard_count = M("Site_log")->table("__SITE_LOG__ s,__MACHINE__ m")->where($whereTodayMgc)->count('distinct(s.machine_id)');
        // // $this->assign("today_mount_guard_count",$today_mount_guard_count);
        // // var_dump(111);
        // // 今日在线设备数总计
        // // $beginToday = 1565904861;
        // // $whereTodayMgc["m.is_delete"] = 0;
        // // $whereTodayMgc["s.add_time"] = array("gt",$beginToday);
        // // $whereTodayMgc["_string"] = "m.machine_id = s.machine_id";
        // // $subQuery = M("Site_log")->table("__SITE_LOG__ s,__MACHINE__ m")->field("s.machine_id")->group('s.machine_id')->where($whereTodayMgc)->select();
        // // $today_mount_guard_count = count($subQuery);
        // var_dump($today_mount_guard_count);
    }
    /**
     * 判断是否跨天
     * @return bool
     */
    function checkOneDay($startTime = '00:00:00',$endTime = '08:00:00'){
        if(!$startTime || !$endTime){
            return false;
        }
        $startHour = substr($startTime,0,2);
        $endHour = substr($endTime,0,2);
        return intval($endHour) < intval($startHour) ? true : false;
    }
    function app(){
        header('Content-type:text/html;charset=utf-8');
        libxml_disable_entity_loader(false);
        $client = new \SoapClient("http://www.fangdao8.com:8082/openapiv3.asmx?wsdl");
//        $param = '<v:Envelope xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns:d="http://www.w3.org/2001/XMLSchema" xmlns:c="http://schemas.xmlsoap.org/soap/encoding/" xmlns:v="http://schemas.xmlsoap.org/soap/envelope/"><v:Header /><v:Body><GetDevicesHistory xmlns="http://tempuri.org/" id="o0" c:root="1"><StartTime i:type="d:string">2019/06/11 00:00:00</StartTime><SelectCount i:type="d:int">10000</SelectCount><TimeZones i:type="d:string">8:00</TimeZones><MapType i:type="d:string">BaiDu</MapType><EndTime i:type="d:string">2019/06/11 08:51:59</EndTime><Key i:type="d:string">7DU2DJFDR8321</Key><DeviceID i:type="d:int">251437</DeviceID><ShowLBS i:type="d:string">1</ShowLBS></GetDevicesHistory></v:Body></v:Envelope>';
//        $aa = $client->__getFunctions();
//        var_dump($aa);
        $param["DeviceID"] = 251437;
        $param["StartTime"] ="2019/06/11 00:00:00";
        $param["EndTime"] = "2019/06/11 21:00:00";
        $param["TimeZones"] = "8:00";
        $param["ShowLBS"] = 1;
        $param["SelectCount"] = 10000;
        $param["Key"] = "7DU2DJFDR8321";
        $param["MapType"] = "Baidu";
        $result = $client->GetDevicesHistory($param);
        var_dump($result);
        exit;
    }

    //json中的Key增加上引号.
    function json_replace_key($str)
    {
        /*  //该版本没有办法解决value中带时分秒之间的冒号问题
        if(preg_match('/\w:/', $str))
            $str = preg_replace('/(\w+):/is', '"$1":', $str);

        return $str;
        */
        $str = trim( $str );
        $str = ltrim( $str, '(' );
        $str = rtrim( $str, ')' );
        $a = preg_split('#(?<!\\\\)\"#', $str );
        for( $i=0; $i < count( $a ); $i+=2 )
        {
            $s = $a[$i];
            $s = preg_replace('#([^\s\{\}\:\,]+):#', '"\1":', $s );
            $a[$i] = $s;
        }
        return implode( '"', $a );
    }
    //获取单个设备的轨迹
    public function getGuiJi(){

        libxml_disable_entity_loader(false);
        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
        $paramOne["TimeZone"] = "8:00";
        $paramOne["ShowLBS"] = 0;
        $paramOne["DeviceID"] = 235068;
        $paramOne["Start"] = "2019-08-05T09:09:00";
        $paramOne["End"] = "2019-08-05T16:00:00";
        $result1 = $client->GetDevicesHistory($paramOne);
        if ($result1) {
            $result2 = object_to_array($result1);
            $jsonstr1 = $result2["GetDevicesHistoryResult"];
            $jsonstr2 = json_replace_key($jsonstr1);
            $jsonArr1 = json_decode("[" . $jsonstr2 . "]", true);
            $listOne = $jsonArr1[0]["devices"];
            var_dump($listOne);
        }
    }
    public function guiji(){
        libxml_disable_entity_loader(false);
        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
        $paramOne["TimeZone"] = "8:00";
        $paramOne["ShowLBS"] = 0;
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $whereAttendance["is_delete"] = 0;
        $whereAttendance["first_result"] = 5;
        $whereAttendance["add_time"] = array('between',array($beginToday,$endToday));
        $attendanceList = M("Attendance")->where($whereAttendance)->select();
        if($attendanceList){
            foreach($attendanceList as $k=>$v){
                $machineInfo = M("Machine")->where(array("machine_id"=>$v["machine_id"]))->find();
                //8.1获取设备绑定区域信息
                $areaInfo = M("Area_map")->where(array("id"=>$machineInfo["area_id"],"is_show"=>1,"is_delete"=>0))->find();
                if(!$areaInfo){
                    continue;
                }
                //8.3判断设备是否在区域内
                $areaInfoStr = $areaInfo["coordinate"];
                $areaListStr = "[".$areaInfoStr."]";
                $areaListArr = json_decode($areaListStr,true);

                $paramOne["DeviceID"] =$machineInfo["mid"];
                $paramOne["Start"] = "2019-08-06T06:09:00";
                $paramOne["End"] = "2019-08-06T06:41:00";
                $result1 = $client->GetDevicesHistory($paramOne);
                if ($result1) {
                    $result2 = object_to_array($result1);
                    $jsonstr1 = $result2["GetDevicesHistoryResult"];
                    $jsonstr2 = json_replace_key($jsonstr1);
                    $jsonArr1 = json_decode("[" . $jsonstr2 . "]", true);
                    $listOne = $jsonArr1[0]["devices"];
                    if($listOne){
                        foreach($listOne as $kk => $vv){
                            var_dump(11);
                            //判断设备是否在围栏内并上传其位置
                            $point=array(
                                'lon'=>$vv["baiduLng"],

                                'lat'=>$vv["baiduLat"]
                            );
                            $isInPoint = is_point_in_polygon($point, $areaListArr);
                            if($isInPoint){
                                //在区域内,修改考勤
                                $data["first_time"] = strtotime($vv["deviceUtcDate"]);
                                $data["first_result"] = 1;
                                M("Attendance")->where(array("id"=>$v["id"]))->data($data)->save();
                                var_dump("success");
                                break;
                            }
                        }
                    }
                }
            }
        }
//        http://lumei.1yxz.com/manager.php/workerMan/guiji

    }
    public function chidao(){
        $still_time = 1800/60;
        var_dump($still_time);exit;
        libxml_disable_entity_loader(false);
        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
        $paramOne["TimeZone"] = "8:00";
        $paramOne["ShowLBS"] = 0;
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $whereAttendance["is_delete"] = 0;
        $whereAttendance["first_result"] = 5;
        $whereAttendance["add_time"] = array('between',array($beginToday,$endToday));
        $attendanceList = M("Attendance")->where($whereAttendance)->select();
        if($attendanceList){
            foreach($attendanceList as $k=>$v){
                $machineInfo = M("Machine")->where(array("machine_id"=>$v["machine_id"]))->find();
                //8.1获取设备绑定区域信息
                $areaInfo = M("Area_map")->where(array("id"=>$machineInfo["area_id"],"is_show"=>1,"is_delete"=>0))->find();
                if(!$areaInfo){
                    continue;
                }
                //8.3判断设备是否在区域内
                $areaInfoStr = $areaInfo["coordinate"];
                $areaListStr = "[".$areaInfoStr."]";
                $areaListArr = json_decode($areaListStr,true);

                $paramOne["DeviceID"] =$machineInfo["mid"];
                $paramOne["Start"] = "2019-08-06T06:41:00";
                $paramOne["End"] = "2019-08-06T07:31:00";
                $result1 = $client->GetDevicesHistory($paramOne);
                if ($result1) {
                    $result2 = object_to_array($result1);
                    $jsonstr1 = $result2["GetDevicesHistoryResult"];
                    $jsonstr2 = json_replace_key($jsonstr1);
                    $jsonArr1 = json_decode("[" . $jsonstr2 . "]", true);
                    $listOne = $jsonArr1[0]["devices"];
                    if($listOne){
                        foreach($listOne as $kk => $vv){
                            var_dump(11);
                            //判断设备是否在围栏内并上传其位置
                            $point=array(
                                'lon'=>$vv["baiduLng"],

                                'lat'=>$vv["baiduLat"]
                            );
                            $isInPoint = is_point_in_polygon($point, $areaListArr);
                            if($isInPoint){
                                //在区域内,修改考勤
                                $data["first_time"] = strtotime($vv["deviceUtcDate"]);
                                $data["first_result"] = 3;
                                M("Attendance")->where(array("id"=>$v["id"]))->data($data)->save();
                                var_dump("success");
                                break;
                            }
                        }
                    }
                }
            }
        }
//        http://lumei.1yxz.com/manager.php/workerMan/guiji

    }
    public function crons() {
        \Think\Log::write('执行任务定时任务！');
        header('Content-type:text/html;charset=utf-8');
        libxml_disable_entity_loader(false);
        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
//        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/ExceptionMessageAjax.asmx?wsdl");

        $param["UserID"] ="3222";
        $param["isFirst"] ="True";
        $param["TimeZones"] ="8:00";
        $param["DeviceID"] ="0";
        $result = $client->GetDevicesByUserID($param);
        if($result) {
//            $this->ajaxReturn($result);
            $result = $this->object_to_array($result);
//            $result = json_decode($result->return, true);
//            var_dump($result);
            $jsonstr = $result["GetDevicesByUserIDResult"];
            $jsonstr = $this->json_replace_key($jsonstr);
//            var_dump($jsonstr);
            $jsonArr = json_decode("[".$jsonstr."]",true);
//            var_dump($jsonArr);
//            var_dump($jsonArr[0]["devices"]);
            $list = $jsonArr[0]["devices"];
//            var_dump($list);exit;
            $count = 0;
            if($list){
                foreach($list as $k=>$v){
//                    var_dump($v["groupID"]);
//                    var_dump($v["sn"]);
                    if($v["groupID"] != 153){
                        var_dump($v);
                        if($v["status"] != "Offline"){
                            $count++;
                        }
                    }
                }
            }
            exit();
            foreach($list as $k => $v){
                if($v["groupID"] == 153){
                    var_dump($v);
//                    $where[$k]["mid"] = $v["id"];
//                    $where[$k]["machine_imei"] = $v["sn"];
//                    $where[$k]["is_delete"] = 0;
//                    $isIn[$k] =M("Machine")->where($where[$k])->find();
//                    $electricityArr = explode("-",$v["dataContext"]);
//                    $data["electricity"] =$electricityArr[0];
//                    $data["machine_status"] = $v["status"];
//                    if(!$isIn[$k]){
//                        $data["machine_name"] = $v["name"];
//                        $data["machine_imei"] = $v["sn"];
//                        $data["mid"] = $v["id"];
//                        $data["add_time"] = $data["update_time"] = time();
//                        M("Machine")->data($data)->add();
//                    }else{
//                        $data["update_time"] = time();
//                        M("Machine")->data($data)->save();
//                    }
//                    $siteData["machine_imei"] = $v["sn"];
//                    $siteData["lat"] = $v["baiduLat"];
//                    $siteData["lon"] = $v["baiduLng"];
//                    $siteData["add_time"] = time();
//                    M("Site_log")->data($siteData)->add();
//                    \Think\Log::write('上传完成！');
                }
            }
//            $this->httpResponse(200,'请求成功',$result);
        }else{
            echo false;
//            $this->httpResponse(404,'验证服务请求失败');
        }
    }
    public function index(){
        var_dump("jhksdahkfhdsa");
//        1559952960
//        SELECT * FROM `yl_site_log` WHERE `machine_id` = 412 AND `add_time` BETWEEN '1559959852' AND '1559967052'
//        $where["machine_id"] = 412;
//        $where["add_time"] = array('between',array("1559959852","1559963452"));
//        $list = M("Site_log")->where($where)->select();
//        var_dump(M("Site_log")->getLastSql());
//        var_dump($list);
//        $list = M("Loc_log")->select();
//        foreach($list as $k =>$v){
//            if($v["machine_imei"] == "358511029005417"){
//                $whereSite["machine_id"] = 412;
//            }else{
//                $whereSite["machine_id"] = 411;
//            }
//            $whereSite["machine_imei"] = $v["machine_imei"];
//            $siteData["server_utc_date"] = $v["server_utc_date"];
//
//            $siteData["device_utc_date"] = $v["device_utc_date"];
//            $whereSite["lat"] = $v["baidu_lat"];
//            $whereSite["lon"] = $v["baidu_lng"];
//            M("Site_log")->where($whereSite)->data($siteData)->save();
//        }

//        M("Warning_message")->where(array("id" => '2052'))->data(array("end_time"=>"1221212"))->save();
        return;
//        $whereElectricity["machine_id"] = 368;
//        $whereElectricity["type"] = 3;
//        $whereElectricity["add_time"] = array("gt","1555282800");
//        $electricityInfo = M("Warning_message")->where($whereElectricity)->order("add_time")->find();
//        var_dump($electricityInfo);
//var_dump($electricityInfo["id"]);
//        return;
        echo '执行任务定时任务！';
//        var_dump(phpinfo());exit;
        header('Content-type:text/html;charset=utf-8');
        libxml_disable_entity_loader(false);
        $client = new \SoapClient("http://123.57.45.188:8081/Ajax/DevicesAjax.asmx?wsdl");
        var_dump($client);
//        $param["txtUserName"] ="天泰顺通" ;
//        $param["txtUserPassword"] ="123456";
//        $param["chkUserPass"] ="0";
//        $param["loginType"] ="0";
//        $param["hidGMT"] ="0";
        $param["UserID"] ="3222";
        $param["isFirst"] ="True";
        $param["TimeZones"] ="8:00";
        $param["DeviceID"] ="0";
        $result = $client->GetDevicesByUserID($param);
//        var_dump($result);
        if($result) {
//            $this->ajaxReturn($result);
            $result = $this->object_to_array($result);
//            $result = json_decode($result->return, true);
//            var_dump($result);
            $jsonstr = $result["GetDevicesByUserIDResult"];
            $jsonstr = $this->json_replace_key($jsonstr);
//            var_dump($jsonstr);
            $jsonArr = json_decode("[".$jsonstr."]",true);
//            var_dump($jsonArr);
//            var_dump($jsonArr[0]["devices"]);
            $list = $jsonArr[0]["devices"];
//            var_dump($list);
            foreach($list as $k => $v){
                if($v["groupID"] == 153){
                    var_dump($v);
                    $where[$k]["mid"] = $v["id"];
                    $where[$k]["machine_imei"] = $v["sn"];
                    $where[$k]["is_delete"] = 0;
                    $result[$k] =M("Machine")->where($where)->find();
                    if(!$result[$k]){
                        $data["machine_name"] = $v["name"];
                        $data["machine_imei"] = $v["sn"];
                        $data["mid"] = $v["id"];
                        M("Machine")->data($data)->add();
                    }
                }
            }
//            $this->httpResponse(200,'请求成功',$result);
        }else{
            echo false;
//            $this->httpResponse(404,'验证服务请求失败');
        }
        return;
//        $url = "http://123.57.45.188:8081/UrlLogin.aspx/?txtUserName=天泰顺通,txtUserPassword=123456,chkUserPass=0,loginType=0,hidGMT=8";
        $param["txtUserName"] ="天泰顺通" ;
        $param["txtUserPassword"] ="123456";
        $param["chkUserPass"] ="0";
        $param["loginType"] ="0";
        $param["hidGMT"] ="0";
        $url = "http://123.57.45.188:8081/Ajax/DevicesAjax.asmx/GetDevicesByUserID/?UserID=3222,DeviceID=0";
//        $ret = $this->http($url,$param);
//        $ret = $this->http($url,json_encode($param));
        $ret = $this->http($url);
        Header("Content-Type:text/html;charset=utf-8");
        var_dump($ret);
        $retArr = $this->object_to_array($ret);
        var_dump($retArr);
        return;
        var_dump(strtotime('-7 days'));return;
        $wifi = "WIFI:12,06-74-9c-8c-63-18,-51,06-74-9c-8c-6a-2c,-66,06-74-9c-8c-06-68,-68,06-74-9c-8c-0b-34,-73,06-74-9c-8f-0d-be,-75,06-74-9c-8c-4d-60,-76,06-74-9c-8c-6c-ac,-81,06-74-9c-8c-53-a4,-84,06-74-9c-8c-4d-dc,-85,ec-26-ca-2c-1a-72,-87,28-f3-66-9d-d3-91,-87,40-31-3c-15-5f-dc,-92";
        $data = "S168#358511029005769#001e#01b9#LOCA:W;CELL:6,1cc,1,1552,d26f,30,1552,d901,11,1552,d271,14,1552,e59f,15,1552,d900,17,1552,d270,19;GDATA:V,7,190506170228,36.841000,119.398010,1,0,9;ALERT:0000;STATUS:14,100;".$wifi."$";
        if(strrchr($data,"$")!="$"){
            var_dump($data);
            return;
        }
        var_dump("客户端反馈信息时间".date("Y-m-d H:i",time())."-----信息为-------------------".$data);
        //去除返回数据中的$符
        $str = substr($data,0,strlen($data)-1);
        //根据#拆分字符串成数组
        $arr = explode("#",$str);
        //定义设备id和设备imei
        $mid = $arr[0];
        $imei = $arr[1];
        if(!$imei){
//        var_dump("没有设备imei");
            return;
        }
        if(mb_strlen($imei,'UTF8') <10){
//        var_dump("错误协议内容");
            return;
        }
        //获取参数部分
        $str2 = $arr[4];
//    var_dump("参数部分=====".$arr[4]);
        //将参数部分根据分号拆分成数组
        $arr2 = explode(";",$str2);
//    var_dump($arr2[0]);
        foreach($arr2 as $k=>$v){
            $arr3[] = explode(":",$v);
        }
        $arr4 = array();
        if(strpos($arr3[0][0],',') !== false){
            $arr3[0][0] =array_pop(explode(',',$arr3[0][0]));
        }
        foreach($arr3 as $kk=>$vv){
            $arr4[$vv[0]] = $vv[1];
        }
//        var_dump($arr4);
        if($arr4["LOCA"]){
//            var_dump($arr4["LOCA"]);
            if($arr4["LOCA"] == "W"){
//                var_dump($arr4["WIFI"]);
                //将参数部分根据分号拆分成数组
                $wifiArr = explode(",",$arr4["WIFI"]);
//                var_dump($wifiArr);
                if($wifiArr[0]>0){
                    var_dump($wifiArr[1]);
                    $i = 0;
                    foreach($wifiArr as $k=>$v){
                        if($k >0){
                            if($k%2==0)
                            {
                                //信号强度
//                                echo "oushu";
                                $wifiInfo[$i]["signal"] = $wifiArr[$k];
                                $i++;
                            } else
                            {
                                //mac地址
//                                echo "jishu";
                                $wifiInfo[$i]["info"] = str_replace("-",":",$wifiArr[$k]);
                                $getWifiStr[$k] = "http://api.cellocation.com:81/wifi/?mac=".$wifiInfo[$i]["info"]."&output=json";
                                $retWifi[$k] = http($getWifiStr[$k]);
                                //get请求获得gps坐标
                                if($retWifi[$k]){
                                    //对象转换成数组
                                    $retWifiArr[$k] = $this->object_to_array($retWifi[$k]);
                                    var_dump($retWifiArr[$k]);
                                    //判断获取gps坐标是否成功
                                    if($retWifiArr[$k]["errcode"] == 0){
                                        var_dump($retWifiArr[$k]["lat"]);
                                        var_dump($retWifiArr[$k]["lon"]);
                                        $waitLat = $retWifiArr[$k]["lat"];
                                        $waitLon = $retWifiArr[$k]["lon"];
                                        break;
                                    }

                                }

                            }
                        }
                    }
                    if($retWifiArr){
                        //已进行wifi信号处理.将得到的gps坐标转换成百度坐标
                        $getBaiduStr = "http://api.map.baidu.com/geoconv/v1/?coords=".$waitLon.",".$waitLat."&from=1&to=5&ak=NENpvHSwTNZ6ftZOKdfiiPDxGKKPHjtg";
                        $retBaidu = http($getBaiduStr);
                        var_dump($retBaidu);
                    }
//                    $getWifiStr = "http://api.cellocation.com:81/wifi/?mac=".$wifiInfo."&output=json";
//                    $retWifi = http($getWifiStr);
//                    var_dump($retWifi);
                }else{
                    echo $wifiArr[0];
                }
            }
            if($arr4["LOCA"] == "G"){
                echo 222;
            }
        }
        //gps坐标转换成百度坐标
//        $getBaiduStr = "http://api.map.baidu.com/geoconv/v1/?coords=119.463425,36.851856&from=1&to=5&ak=NENpvHSwTNZ6ftZOKdfiiPDxGKKPHjtg";
//        $retBaidu = http($getBaiduStr);
//        var_dump($retBaidu);
        exit;
        $machineArr = array("358511029001479","358511029001480","358511029001481","358511029001482","358511029001200","358511029001201","358511029001202","358511029001203");
//        $machineArr = array("358511029001479","358511029001480");
        $siteLogArr = array(array("119.122803,36.724728","119.122884,36.724713","119.123261,36.724757","119.122951,36.724738","119.123755,36.724811","119.123912,36.724811","119.12411,36.724829","119.123607,36.724778","119.123185,36.724757","119.122875,36.724724"),array("119.122655,36.725093","119.122762,36.724898","119.122875,36.72484","119.122992,36.724843","119.123198,36.724858","119.123737,36.724898","119.124007,36.724916","119.123495,36.724872","119.123198,36.724858","119.123198,36.724858"),array("119.120261,36.724825","119.120746,36.724807","119.121155,36.724793","119.122241,36.724825","119.122542,36.724963","119.122134,36.724804","119.120678,36.724796","119.120678,36.724796","119.120211,36.72501","119.120211,36.72501"),array("119.122664,36.724308","119.122686,36.723646","119.122673,36.722963","119.122673,36.722113","119.122673,36.722113","119.122664,36.718486","119.122682,36.719127","119.122695,36.720414","119.122673,36.721716","119.122691,36.72378"),array("119.122345,36.724659","119.122547,36.724453","119.122587,36.724015","119.122578,36.722923","119.122592,36.721488","119.122592,36.721488","119.122592,36.71853","119.122587,36.719698","119.122583,36.72075","119.122587,36.72335"),array("119.128354,36.725263","119.128278,36.726022","119.128296,36.726398","119.128292,36.727259","119.128345,36.728148","119.128336,36.728705","119.128323,36.728134","119.128296,36.727125","119.128287,36.726145","119.128278,36.725592"),array("119.120207,36.724702","119.119897,36.724033","119.119551,36.723325","119.11899,36.722193","119.118325,36.721271","119.117966,36.720616","119.117817,36.720157","119.11837,36.721003","119.118981,36.722027","119.119856,36.723451"),array("119.12075,36.718385","119.121644,36.7184","119.122426,36.718418","119.122426,36.718418","119.123162,36.718223","119.123459,36.718255","119.122754,36.718219","119.122565,36.718432","119.121882,36.718418","119.120997,36.718392"));
        foreach($machineArr as $k =>$v){
            $timeStart = time();
            foreach($siteLogArr as $kk =>$vv){
                foreach($vv as $kkk=>$vvv){
                    if($k == $kk){
                        $data["machine_imei"] = $v;
                        $siteLog = $vvv;
                        $siteArr = explode(",",$siteLog);
                        $data["lat"] = $siteArr[1];
                        $data["lon"] = $siteArr[0];
                        $data["add_time"] = $timeStart;
                        //在区域内
                        $data["status"] = 1;
                        //上午
                        $data["type"] = 2;
                        $timeStart = $timeStart+120;
                        M("Site_log")->data($data)->add();
                    }
                }
            }
        }
        echo "success";
        exit;
//        $data["machine_imei"] = "122346546465";
//        $data["info"] = "ksjdahfjklhsdkjahfkhsdkjlahfj,dhskj,ahckjbdsnhkj,,,,,,:.,,,";
//        var_dump($data);
//        $result = M("Loc_log")->add($data);
//        var_dump($result);
//        exit;
//        $client = stream_socket_client('tcp://0.0.0.0:5678', $errno, $errmsg, 1);
//        $uid="10";
//        $commandstr ='S168#358511029001246#000f#0009#GSENSOR,1$';
//        $data = array('uid'=>$uid, 'percent'=>$commandstr);
//        echo json_encode($data);
//        fwrite($client, json_encode($data)."\n");
//        echo fread($client, 8192);
//        exit;
//
//        $machine_id = I("machine_id") ? I("machine_id") : 7;
//        $machineInfo = M("Machine")->where(array("machine_id"=>$machine_id))->find();
//        $machine_imei = $machineInfo["machine_id"];
//        if($machineInfo["machine_status"] != 1 ){
//            $this->error("设备未在线，无法发送命令");
//        }
//        if(!$machineInfo["mid"]){
//            $this->error("设备信息不全，无法发送命令");
//        }
//        $client = stream_socket_client('tcp://0.0.0.0:5678', $errno, $errmsg, 1);
//        $commandstr = $machineInfo["mid"].'#'.$machineInfo["machine_imei"].'#000c#0005#JUST$';
//        var_dump($commandstr);exit;
//        $data = array('uid'=>$machine_id, 'percent'=>$commandstr);
//        fwrite($client, json_encode($data)."\n");
//        $result = fread($client, 8192);
//        if($result == "ok"){
//            $this->success("发送成功");
//        }else{
//            $this->error("发送失败");
//        }
//
//        $dingwei = substr("0.0000" , 0 , 1);
//        var_dump($dingwei);
//        if($dingwei == "0") {
//            var_dump("shi");
//            return;
//        }
//        return;
//            $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
//        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
//        $database = new \medoo(array('database_type' => 'mysql','charset'=>'utf8mb4' , 'database_name' => 'cyyldb', 'server' => 'localhost', 'port' => '3306', 'username' => 'root', 'password' => ''));
//        $policeInfo = $database->get("yl_warning_message", "*", array(
//            "AND" => array("machine_id" => 1, "type[<]" => 3, "add_time[>]" => $beginToday,"end_time[<]" => 1),
//            "ORDER" => "add_time DESC",
//            "LIMIT" => 1
//        ));
//        var_dump($policeInfo);
//        return;
//        $userids = $database->select("yl_attendance","userid", array(
//            "AND"=>array("is_delete"=>0,array("add_time[<>]" => array($beginToday,$endToday))),
//        ));
//        if($userids){
//            $memberList = $database->select("yl_member",array("userid","machine_id"), array(
//                "AND"=>array("is_delete"=>0,array("userid[!]" => $userids))
//            ));
//        }else{
//            $memberList = $database->select("yl_member",array("userid","machine_id"), array("is_delete"=>0));
//            var_dump($database->last_query());
//            var_dump($memberList);
//        }
//        if($memberList){
//            foreach($memberList as $k =>$v){
//                if($v["machine_id"]){
//                    $database->insert('yl_attendance',array('add_time'=>time(),'update_time'=>time(),'userid'=>$v["userid"],'machine_id'=>$v["machine_id"]));
//                }
//            }
//        }
//        $attendanceInfo = $database->get("yl_attendance","*",array("AND"=>array("machine_id"=>19,"is_delete"=>0,array("update_time[<>]" => array($beginToday,$endToday)))));
//                    var_dump($database->last_query());
//                    var_dump($database->error());
//        return;
        $data = "S168#358511029005769#001e#01b9#LOCA:W;CELL:6,1cc,1,1550,ac0d,2c,1550,a2d5,1f,1550,dc2e,20,1550,79d8,22,1550,79d7,22,1550,cf8d,25;GDATA:V,0,190401195139,0.000000,0.000000,0,0,0;ALERT:0000;STATUS:65,100;WIFI:12,b0-75-d5-5e-ca-7e,-53,a8-25-eb-f1-84-5c,-58,8c-a6-df-ed-b2-22,-69,b0-75-d5-5e-c7-d9,-80,88-df-9e-a7-3e-d0,-82,50-bd-5f-e7-89-6a,-82,2c-b2-1a-eb-3b-02,-83,7c-b5-40-11-ab-e5,-85,a4-56-02-b1-f5-c7,-86,02-19-be-81-69-54,-86,fc-7c-02-b5-fe-cb,-87,d0-c7-c0-99-f3-9a,-87$";
        if(strrchr($data,"$")!="$"){
            echo "Y";
        }else{
            echo "N";
        }
        //去除返回数据中的$符
        $str = substr($data,0,strlen($data)-1);
        //根据#拆分字符串成数组
        $arr = explode("#",$str);
        var_dump($arr);
        var_dump($arr[4]);
        exit;
//        $where["add_time"] = array("gt","1548604800");
//        $where["machine_imei"] = "358511029679336";
//        $info = M("Site_log")->where($where)->order("add_time desc")->limit(1)->find();
//        var_dump($info);
//        var_dump($info["add_time"]);return;
//        $database = new \medoo(array('database_type' => 'mysql','charset'=>'utf8mb4' , 'database_name' => 'cyyldb', 'server' => 'localhost', 'port' => '3306', 'username' => 'root', 'password' => ''));
//        $siteSql = "SELECT * FROM yl_site_log WHERE machine_imei = 358511029679336  AND add_time > 1548604800 ORDER BY add_time DESC LIMIT 1";
//        $siteInfo = $database->query($siteSql);
//        $siteInfo = $database->get("yl_site_log", "*", array(
//            "AND"=>array("machine_imei"=>358511029679336,"add_time[>]" => 1548604800),
//            "ORDER" => "add_time DESC",
//            "LIMIT" => 1
//        ));
//        var_dump($database->last_query());
//        var_dump($siteInfo);
//        var_dump($siteInfo["lat"]);
//        return;
//        $where["c.is_delete"] = 0;
//        $where["m.is_delete"] = 0;
//        $where["_string"] = "s.machine_imei = c.machine_imei and c.userid = m.userid";
//        $subQuery = M("Site_log")->order('add_time desc')->buildSql();
//        $list = M("Site_log")->table($subQuery.' s,__MACHINE__ c,__MEMBER__ m')->where($where)->field("s.*,c.machine_id,c.machine_imei,c.machine_name,c.userid,m.job_number,m.mobile")->group("s.machine_imei")->select();
//        echo M("Site_log")->_sql();
//        var_dump($list);
//        exit;
//        $getBaiduStr = "http://api.cellocation.com:81/cell/?mcc=460&mnc=0&lac=21355&ci=1337&output=json";
//        http://api.cellocation.com:81/loc/?cl=460,0,21355,1337,25;460,0,21560,62219,12;460,0,21612,42782,13;460,0,21355,1332,14;460,0,21355,62229,14;460,0,21347,53154,15&wl=b6:ef:fa:87:3b:8c,-18;04:4f:4c:a4:db:e8,-60;20:82:c0:3f:e4:ef,-64;40:a5:ef:7d:21:02,-82;78:d3:8d:d6:ba:20,-86;78:d3:8d:d6:ba:21,-89&output=json
//        $getStr = "http://api.cellocation.com:81/loc/?cl=460,0,21355,1337,25;460,0,21560,62219,12;460,0,21612,42782,13;460,0,21355,1332,14;460,0,21355,62229,14;460,0,21347,53154,15&wl=b6:ef:fa:87:3b:8c,-18;04:4f:4c:a4:db:e8,-60;20:82:c0:3f:e4:ef,-64;40:a5:ef:7d:21:02,-82;78:d3:8d:d6:ba:20,-86;78:d3:8d:d6:ba:21,-89&output=json";
//        $getStr = "http://api.cellocation.com:81/loc/?cl=460,0,21612,42782,20&wl=b6:ef:fa:87:3b:8c,-28;20:82:c0:3f:e4:ef,-61&output=json";
//        $ret = $this->http($getStr);
//        var_dump($ret);
//        $retArr = $this->object_to_array($ret);
//        var_dump($retArr);
//        echo $retArr["lat"];
//        //gps坐标转换成百度坐标
//        $getBaiduStr = "http://api.map.baidu.com/geoconv/v1/?coords=".$retArr["lon"].",".$retArr["lat"]."&from=1&to=5&ak=NENpvHSwTNZ6ftZOKdfiiPDxGKKPHjtg";
//        $retBaidu = http($getBaiduStr);
//        $retBaiduArr = $this->object_to_array($retBaidu);
//        var_dump($retBaiduArr);
//        if($retBaiduArr["status"] == 0){
//            var_dump("gps坐标转换百度坐标成功");
//            //纬度
//            $lat = (string)$retBaiduArr["result"][0]["y"];
//            echo $lat;
//            //经度
//            $lon = (string)$retBaiduArr["result"][0]["x"];
//            $lon;
//        }else{
//            return;
//        }
//        exit;
//        $retIn = array(1);
//        $retOutStart = array(1);
//        if(in_array(1,$retIn) && in_array(2,$retOutStart)){
//            //在工作时间内
//            echo "222";
//        }else{
//            echo "111111";
//        }
//        exit;
//        //获取时间段
//        $timeIdArr = explode(",","1,2,3");
////        $timeInfo = M("Schedules_time")->where(array("id"=>1,"is_show"=>1,"is_delete"=>0))->find();
////        var_dump($timeInfo);
//        $timeArr = array();
//        foreach($timeIdArr as $k){
//            echo $k;
//            $timeInfo[$k] = M("Schedules_time")->where(array("id"=>$k,"is_show"=>1,"is_delete"=>0))->find();
////            var_dump($timeInfo[$k]);
//            array_push($timeArr,$timeInfo[$k]);
////                        $yearStart = $year.' '.$timeInfo["start_time"];
////                        $startTime = strtotime($yearStart);
////                        $yearEnd = $year.' '.$timeInfo["start_time"];
////                        $endTime = strtotime($yearEnd
////                        );
//        }
//        var_dump($timeArr);
//        $retR = array();
//        $year = date("Y-m-d",time());
//        foreach($timeArr as $t){
//            echo $t["end_time"];
//            $yearEnd = $year.' '.$t["end_time"];
//            echo $yearEnd;
//            $endTime = strtotime($yearEnd);
//            var_dump($endTime);
//            if($endTime>time()){
//                array_push($retR,1);
//            }else{
//                array_push($retR,0);
//            }
//        }
//        var_dump($retR);
//        if(in_array(2,$retR)){
//            echo "Match found";
//        }else{
//            echo "Match not found";
//        }

//        echo date("Y-m-d H:i:s");
//        $year = date("Y-m-d",time());
//        $yearStart = $year.' '."08:00:00";
//        echo $yearStart;
//        $timeIdArr = explode(",","a,b,c,d");
//        foreach($timeIdArr as $k){
//            var_dump($k);
//        }
//        exit();36.755489,119.152275
//        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
//        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
//        var_dump($beginToday);
//        var_dump($endToday);
//        var_dump($this->fnRetWeek());
//        $schedulesInfo["work_day"] = "1,2,3,4,5";
//        if(strpos($schedulesInfo["work_day"],$this->fnRetWeek()) == false){
//            echo "buzai";
//        }else{
//            echo  "zai";
//        }
//         建立socket连接到内部推送端口
// 推送的数据，包含uid字段，表示是给这个uid推送
//        S168#358511029679336#0000#0004#JUST$
//        S168#358511029679336#0000#0005#PARAM$
//        S168#358511029679336#0000#0008#ACK^LOCA$
//        S168#358511029679336#0000#0008#UP,60,120$
//        S168#358511029679336#0000#0008#LPD,08,30,16,30$
//        S168#358511029679336#0000#0008#URGENT,1$
        $client = stream_socket_client('tcp://0.0.0.0:5678', $errno, $errmsg, 1);
        $center_tel = "15054461599";
        $machine_imei ="122222222222222";
        $a = mb_strlen("15054461599",'UTF8');
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
        echo $commandstr;exit;
//        $num = substr(time(),-4);
        $commandstr ='S168#358511029001418#0000#0008#URGENT,1$';
        $data = array('uid'=>$uid, 'percent'=>$commandstr);
        echo json_encode($data);
        fwrite($client, json_encode($data)."\n");
        echo fread($client, 8192);
        exit;

    }
    //判断当前时间戳是周几
    function fnRetWeek(){
        $time = time();
        $week = date("w",$time);
        return $week."";
    }
    /**
     * http请求
     * @param  string  $url    请求地址
     * @param  boolean|string|array $params 请求数据
     * @param  integer $ispost 0/1，是否post
     * @param  array  $header
     * @param  $verify 是否验证ssl
     * return string|boolean          出错时返回false
     */
    function http($url, $params = false, $ispost = 0, $header = array(), $verify = false) {
        $httpInfo = array();
        $ch = curl_init();
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //忽略ssl证书
        if($verify === true){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if (is_array($params)) {
                $params = http_build_query($params);
            }
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            trace("cURL Error: " . curl_errno($ch) . ',' . curl_error($ch), 'error');
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
            trace($httpInfo, 'error');
            return false;
        }else{
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }

        $resultInfo = json_decode($response);
        if($resultInfo){
            $resultInfo->httpCode = $httpCode;
            return $resultInfo;
        }else{
            return $response;
        }
    }
    //对象转数组
    function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)$this->object_to_array($v);
            }
        }

        return $obj;
    }
    /** 兼容key没有双引括起来的JSON字符串解析
     * @param String $str JSON字符串
     * @param boolean $mod true:Array,false:Object
     * @return Array/Object
     */
    function ext_json_decode($str, $mode=false){
        if(preg_match('/\w:/', $str)){
            $str = preg_replace('/(\w+):/is', '"$1":', $str);
        }
        return json_decode($str, $mode);
    }
}

