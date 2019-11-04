<?php
/**
 * Created by PhpStorm.
 * User: liwenlin
 * Date: 2017/10/26
 * Time: 下午3:11
 */
// 获取管理人员名称
function getMemberManager($parent_id){
    if(!$parent_id)return;
    $name = M('Member')->where(array('userid'=>$parent_id))->getField("realname");
    if($name){
        return $name;
    }
}
// 获取职位名称
function getPositionName($position){
    if(!$position)return;
    $name = M('Member_position')->where(array('id'=>$position))->getField("name");
    if($name){
        return $name;
    }
}
/**
 * 管理员操作记录
 * @param $log_url 操作URL
 * @param $log_info 记录信息
 */
function adminLog($log_info){
    $add['log_time'] = time();
    $add['uid'] = session('admin_uid');
    $add['log_info'] = $log_info;
    $add['log_ip'] = ip();
    $add['log_url'] = __ACTION__;
    M('admin_log')->add($add);
}
/**
 * 检查管理员名称
 * @param array $data 管理员数据
 */
function checkuserinfo($data) {
    if(!is_array($data)){
        return false;
    } elseif (!is_adminname($data['username'])){
        return false;
    }
    return $data;
}
/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username 要检查的用户名
 * @return 	TRUE or FALSE
 */
function is_adminname($username) {
    $strlen = strlen($username);
    if(is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username)){
        return false;
    } elseif ( 20 < $strlen || $strlen < 2 ) {
        return false;
    }
    return true;
}
/**
 * 检测输入中是否含有错误字符
 *
 * @param char $string 要检查的字符串名称
 * @return TRUE or FALSE
 */
function is_badword($string) {
    $badwords = array("\\",'&',' ',"'",'"','/','*',',','<','>',"\r","\t","\n","#");
    foreach($badwords as $value){
        if(strpos($string, $value) !== FALSE) {
            return TRUE;
        }
    }
    return FALSE;
}
/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email) {
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}
/**
 * 获取管理员的权限分组
 * @param string $uid 管理员id
 * @param string $type  0=获取管理组名称  1=获取id
 * @return string
 */
function get_auth_group($uid,$type="0"){
    if(!$uid){
        return false;
    }
    $groupDb = M("Auth_group");
    $groupAccessDb = M("Auth_group_access");
    $whereGroupAccess['uid'] = $uid;
    $groupId = $groupAccessDb->where($whereGroupAccess)->getField("group_id");
    if($type==1){
        return $groupId;
    }
    if($groupId){
        $whereGroup['id'] = $groupId;
        $groupTitle = $groupDb->where($whereGroup)->getField("title");
        return $groupTitle;
    }else{
        return false;
    }
}

function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}
function is_administrator($uid = null){
    if(!$uid) return false;
}

// 获取管理员姓名
function get_admin_name($uid){
    if(!$uid)return;
    $admin_name = M('Admin')->where(array('uid'=>$uid))->getField("realname");
    if($admin_name){
        return $admin_name;
    }
}

// 发送消息
/*
$type  1= 个人 2=全体
*/
function sendMessageToMember($title,$content,$userid,$type){
    $data["receive_userid"] = intval($userid);
    $data["title"] = $title;
    $data["content"] = $content;
    $data["add_time"] = time();
    $data["send_uid"] = 1;
    $data["type"] = $type;
    $logId = M("Message_log")->data($data)->add();
    if($logId){
        return true;
    }else{
        return false;
    }
}
/**
 * 获取本周时间
 * @param int $time 开始时间,结束时间
 * @author
 */
function weekTime($time =array()){
    $stimestamp = strtotime($time['start_time']);
    $etimestime = strtotime($time['end_time']);
    //计算日期段内有多少天
    $days = ($etimestime - $stimestamp)/86400+1;
    //保存每天日期
    $date = array();
    /* for循环本周一到周日 */
    for($i=0; $i<$days; $i++){
        $date[] = date("Y-m-d",$stimestamp+(86400*$i));
    }
    return $date;
}
