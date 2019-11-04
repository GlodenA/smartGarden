<?php
/**
 * Created by PhpStorm.
 * User: liwenlin
 * Date: 2018/9/10
 * Time: 下午2:28
 */

namespace App\Controller;
use Think\Controller;
class UserController extends BaseController
{


    public function _initialize()
    {

    }

    public function userLogin()
    {
        if (IS_POST) {

            $memberDb = M('Admin');

            $mobile = $_POST['username'];
            $password = $_POST['password'];
            // $verify = $_POST['verify'];
            // $deviceId = I('device_id');

            // if (!isMobile($mobile)) {
            //     parent::httpResponse(0, '手机号码格式错误');
            // }
            // if (empty($password) || (strlen($password) < 6 || strlen($password) > 16)) {
            //     parent::httpResponse(0, '密码错误');
            // }

            $whereMember['mobile|username'] = $mobile;
            // $whereMember['group_id'] = 2;
            //根据用户名检查用户是否存在
            $info = $memberDb->where($whereMember)->find();
            if (!$info) {
                parent::httpResponse(400, '该用户不存在');
            } else {
                // if(!check_verify($verify,$deviceId)){
                //     parent::httpResponse(0, '验证码错误');
                // }
                //存在、验证密码是否正确
                $where["uid"] = $info["uid"];
                $password = password($password, $info['encrypt']);
                if ($password == $info['password']) {
                    $random = rand(0, 1000);
                    $token = md5('NONO' . $random . $mobile . time());
                    $updateData['app_access_token'] = $token;
                    $updateData['last_date'] = time();
                    $updateData['last_ip'] = ip();
                    $memberDb->where($whereMember)->save($updateData);
                    $infoData = $memberDb->where($where)->field("password,encrypt", true)->find();
                    parent::httpResponse(200, '登录成功', $infoData);
                } else {
                    parent::httpResponse(400, '密码错误');
                }
            }
        }
    }

    // 修改密码
    public function changePassword()
    {
        if (IS_POST) {

            $mobile = $_POST['mobile'];
            $code = $_POST['code'];

            $this->checkSmsCode($mobile, $code);

            $password = I('password');

            if (empty($password) || (strlen($password) < 6 || strlen($password) > 16)) {
                parent::httpResponse(0, '请输入6-16位密码');
            }

            $whereMember['mobile'] = $mobile;
            $password = password($_POST['password']);
            $passwordData['password'] = $password['password'];
            $passwordData['encrypt'] = $password['encrypt'];
            if (M('Admin')->where($whereMember)->save($passwordData)) {
                parent::httpResponse(1, '密码修改成功');
            } else {
                parent::httpResponse(0, '新旧密码不能相同');
            }

        }
    }

    public function checkMobile(){
        //短信发送类型1:注册 2:修改密码
        $type = I('type') ? I('type') : 1;
        //手机号码
        $mobile = I('mobile');

        if(!isMobile($mobile)){
            parent::httpResponse(0, '手机号码格式错误');
        }

        switch ($type){
            case 1:
                $uid = M('Admin')->where(array('mobile' => $mobile))->getField('uid');
                if($uid){
                    parent::httpResponse(0, '手机号码已注册');
                }
                $this->sendSms($mobile);
                break;
            case 2:
                $uid = M('Admin')->where(array('mobile' => $mobile))->getField('uid');
                if(!$uid){
                    parent::httpResponse(0, '手机号码未注册');
                }
                $this->sendSms($mobile);
                break;
            default:
                parent::httpResponse(0, '无效的类型');
                break;
        }
    }

    //发送短信验证码
    public function sendSms_bak($mobile)
    {

        $smsDb = M('Sms');

        $lastSendTime = $smsDb->where(array('mobile' => $mobile))->order('id desc')->getField('send_time');
        if ($lastSendTime) {
            $diff = time() - $lastSendTime;
            if ($diff < 120) {
                parent::httpResponse(0, '请于' . (120 - intval($diff)) . '秒后再次发送');
            }
        }

        $info = M('Module_extend')->find();
        $options['accountsid'] = $info["ucpaas_account_sid"];
        $options['token'] = $info["ucpaas_auth_token"];
        $appId = $info["ucpaas_app_id"];
        //初始化 $options必填
        $code = random(6, '123456789');
        vendor('Ucpaas.Ucpaas');
        $ucpass = new \Ucpaas($options);
        $to = "" . $mobile . "";
        $templateId = '373419';
        $param = "" . $code . ",5";




        $resultArr = $ucpass->SendSms($appId, $templateId, $param, $mobile);
        $resultArr = json_decode($resultArr);
//        $this->ajaxReturn($resultArr);
        $smsCode = $resultArr->code;
        if ($smsCode == '000000') {

            $smsData['mobile'] = $mobile;
            $smsData['code'] = $code;
            $smsData['send_time'] = time();
            $smsDb->data($smsData)->add();

            parent::httpResponse(1, '发送成功');

        } else if ($smsCode == '105122') {
            $msg = "发送数量超出限制";
        } else {
            $msg = "发送失败";
        }
        parent::httpResponse(0, $msg);


    }
    public function sendSms()
    {
        include_once(__DIR__."/CCPRestSmsSDK.php");


        $smsDb = M('Sms');
        $mobile = I('mobile');
        if(!isMobile($mobile)){
            parent::httpResponse(0, '请输入正确的手机号码');
        }

        $lastSendTime = $smsDb->where(array('mobile' => $mobile))->order('id desc')->getField('send_time');
        if ($lastSendTime) {
            $diff = time() - $lastSendTime;
            if ($diff < 120) {
                parent::httpResponse(0, '请于' . (120 - intval($diff)) . '秒后再次发送');
            }
        }




        //主帐号,对应开官网发者主账号下的 ACCOUNT SID
        $accountSid= '8a216da86002167f016002274b540009';

        //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
        $accountToken= '5fdddd8a0bfc46138243235293848aaa';

        //应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
        //在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
        $appId='8a216da8662360a40166287b6bb20541';

        //请求地址
        //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
        //生产环境（用户应用上线使用）：app.cloopen.com
        $serverIP='app.cloopen.com';


        //请求端口，生产环境和沙盒环境一致
        $serverPort='8883';

        //REST版本号，在官网文档REST介绍中获得。
        $softVersion='2013-12-26';


        $to = "" . $mobile . "";

        // 初始化REST SDK
//        global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
        $rest = new \REST($serverIP,$serverPort,$softVersion);
        $rest->setAccount($accountSid,$accountToken);
        $rest->setAppId($appId);

        // 发送模板短信
//        echo "Sending TemplateSMS to $to <br/>";
        $code = random(6, '123456789');
        $result = $rest->sendTemplateSMS($to,array(strval($code),'2分钟'),'347820');
        if($result == NULL ) {
            $ret['code'] = '0';
            $ret['msg'] = '发送失败';
            $this->ajaxReturn($ret);
        }
        if($result->statusCode!=0) {
            $ret['code'] = $result->statusCode;
            $ret['msg'] = $result->statstatusMsgusCode;
            $this->ajaxReturn($ret);
        }else{
            $smsData['mobile'] = $mobile;
            $smsData['code'] = $code;
            $smsData['send_time'] = time();
            $smsDb->data($smsData)->add();
            $ret['code'] = 1;
            $ret['msg'] = '发送成功';
            $this->ajaxReturn($ret);
        }


//        $info = M('Module_extend')->find();
//        $options['accountsid'] = $info["ucpaas_account_sid"];
//        $options['token'] = $info["ucpaas_auth_token"];
//        $appId = $info["ucpaas_app_id"];
//        //初始化 $options必填
//        $code = random(6, '123456789');
//        vendor('Ucpaas.Ucpaas');
//        $ucpass = new \Ucpaas($options);
//        $to = "" . $mobile . "";
//        $templateId = '373419';
//        $param = "" . $code . ",5";
//
//
//
//
//        $resultArr = $ucpass->SendSms($appId, $templateId, $param, $mobile);
//        $resultArr = json_decode($resultArr);
////        $this->ajaxReturn($resultArr);
//        $smsCode = $resultArr->code;
//        if ($smsCode == '000000') {
//
//            $smsData['mobile'] = $mobile;
//            $smsData['code'] = $code;
//            $smsData['send_time'] = time();
//            $smsDb->data($smsData)->add();
//
//            parent::httpResponse(1, '发送成功');
//
//        } else if ($smsCode == '105122') {
//            $msg = "发送数量超出限制";
//        } else {
//            $msg = "发送失败";
//        }
//        parent::httpResponse(0, $msg);


    }

    /**
     * 检查短信验证码
     * @param $mobile
     * @param $smsCode
     */
    public function checkSmsCode($mobile, $smsCode)
    {
        if(empty($smsCode) || strlen($smsCode) != 6){
            parent::httpResponse(0, '无效的验证码');
        }
        $smsDb = M('Sms');

        $where["mobile"] = $mobile;
        $where["code"] = $smsCode;

        $result = $smsDb->where($where)->find();
        if ($result) {
            if ($result["status"] == 0) {
                parent::httpResponse(0, '验证码失效');
            }
            $sendtime = $result["send_time"];
            $diff = time() - (int)substr($sendtime, 0, 10);
            $min = floor($diff / 60);
            if ($min > 5) {
                parent::httpResponse(0, '验证码超时');
            } else {
                $upWhere["id"] = $result["id"];
                $upWhere["status"] = 0;
                $smsDb->save($upWhere);
            }
        } else {
            parent::httpResponse(0, '验证码错误');
        }
    }


    public function checkImei(){
        $userInfo = parent::checkToken();
        $imei = I('imei');

        $deviceId = M('Machine')->where(array('uid'=>$userInfo['uid'],'machine_imei'=>$imei))->getField('machine_id');

        if(!$deviceId){
            parent::httpResponse(0,'尚未绑定该设备');
        }else{
            parent::httpResponse(1,'设备已绑定');
        }
    }

}