<?php

namespace Api\Controller;

use Think\Controller;

class BaseController extends Controller
{
    function __construct()
    {
//        $this->checkToken();
    }



    /**
     * 检验ticket
     */
    public function checkTicket(){

        $ticket = I('ticket');
        $timestamp = I('timestamp');
        $retCode = '400';

        if(empty($ticket) || !isset($ticket)){
            $retMsg = '缺少参数 ticket';
            $this->httpResponse($retCode,$retMsg);
        }

        if(empty($timestamp) || !isset($timestamp)){
            $retMsg = '缺少参数 timestamp';
            $this->httpResponse($retCode,$retMsg);
        }

        $currentTime = time();
        if(($currentTime - $timestamp) > 120){
            $retMsg = '已超时的 timestamp';
            $this->httpResponse($retCode,$retMsg);
        }

        $checkTicket = md5('nb_lot='.$timestamp);
        if($ticket != $checkTicket){
            $this->httpResponse('400','不匹配的 ticket');
        }
    }

    /**
     * 校验用户token
     * @param POST token
     * @return mixed
     */
    public function checkToken(){



        $token = I('token');
        if(empty($token) || !isset($token)){
            $ret['status'] = 0;
            $ret['msg'] = '缺少参数: token';
            $this->ajaxReturn($ret);
        }

        $userInfo = M('Admin')->where(array('app_access_token'=>$token,'group_id'=>2))->field('password,encrypt',true)->find();
        if($userInfo){
            return $userInfo;
        }else{
            $ret['status'] = 0;
            $ret['msg'] = '无效的 token';
            $this->ajaxReturn($ret);
        }
    }


    /**
     * 统一接口返回
     * @param string $code
     * @param string $msg
     * @param array $data
     */
    public function httpResponse($code='0000', $msg='', $data=array()){
        $ret['code'] = $code;
        $ret['msg'] = $msg;
        if($data){
            $ret['data'] = $data;
        }
        $this->ajaxReturn($ret);
    }


}