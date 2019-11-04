<?php

namespace App\Controller;
use Think\Controller\RestController;

class BaseController extends RestController
{
//     function __construct()
//     {
// //        $this->checkToken();
//     }
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

    function httpResponse($code = '0000',$msg = '请求成功',$data,$returnType = 'json'){
        
        $ret['code'] = strval($code);
        $ret['msg'] = $msg;
        if($data){
            $ret['data'] = $data;
        }
        $this->ajaxReturn($ret,$returnType);
    }
}