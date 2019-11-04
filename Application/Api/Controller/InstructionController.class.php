<?php
/**
 * 指令控制器
 * User: liwenlin
 * Date: 2018/8/15
 * Time: 上午10:05
 */
namespace Api\Controller;

use Think\Log;

class InstructionController extends BaseController
{


    /**
     * 指令结果上传
     * @author liwenlin
     * @param
     * @return $str
     */
    public function upInstructionRecord(){

//        $memberInfo = parent::checkToken();
//        $saveData['userid'] = $memberInfo['userid'];
        $saveData['device_number'] = I('cgsn');
        $saveData['instruction'] = I('instruction');
        $saveData['lon'] = I('lon');
        $saveData['lat'] = I('lat');
        $saveData['province'] = I('province');
        $saveData['city'] = I('city');
        $saveData['district'] = I('district');
        $saveData['street'] = I('street');
        $saveData['street_number'] = I('street_number');
        $saveData['address'] = I('address');
        $saveData['result'] = I('result');
        $saveData['add_time'] = time();

        $id = M('Instruction_record')->add($saveData);
        if($id){
            $ret['status'] = 1;
            $ret['msg'] = '添加成功';
            $this->ajaxReturn($ret);
        }else{
            $ret['status'] = -1;
            $ret['msg'] = '添加失败';
            $this->ajaxReturn($ret);
        }
    }

    /**
     * 校验设备imei
     */
    public function checkMachine()
    {
        $userinfo = parent::checkToken();

        $imei = I('cgsn');
        if(empty($imei)){
            $ret['status'] = 0;
            $ret['info'] = '设备串号不能喂空';
            $this->ajaxReturn($ret);
        }
        $id = M('Machine')->where(array('machine_imei'=>$imei))->getField('machine_id');
        if($id){

            $bindId = M('Machine_bind')->where(array('uid'=>$userinfo['uid'],'machine_imei'=>$imei))->getField('id');
            if(!$bindId){
                M('Machine_bind')->add(array('uid'=>$userinfo['uid'],'machine_imei'=>$imei));
            }

            $ret['status'] = 1;
            $ret['msg'] = '平台设备';
            $this->ajaxReturn($ret);
        }else{
            $ret['status'] = 0;
            $ret['msg'] = '未知设备';
            $this->ajaxReturn($ret);
        }
    }

    /**
     * 指令结果上传_v2
     * @author liwenlin
     * @param
     * @return $str
     */
    public function upInstructionRecord_v2(){
        $userinfo = parent::checkToken();
        $data = json_decode(htmlspecialchars_decode(I('data')),true);
        if (is_array($data)){
            $recordDb = M('Instruction_record');
            foreach ($data as $v) {
                $data = array(
                    'uid' => $userinfo['uid'],
                    'device_number' => $v['cgsn'],
                    'instruction' => $v['instruction'],
                    'lon' => $v['lon'],
                    'lat' => $v['lat'],
                    'province' => $v['province'],
                    'city' => $v['city'],
                    'district' => $v['district'],
                    'street' => $v['street'],
                    'street_number' => $v['streetNumber'],
                    'address' => $v['address'],
                    'earfcn'=> $v['physical_cell_id'],
                    'physical_cell_id'=> $v['physical_cell_id'],
                    'primarycell'=> $v['primarycell'],
                    'rsrp'=> $v['rsrp'],
                    'rsrq'=>$v['rsrq'],
                    'rssi'=> $v['rssi'],
                    'snr'=> $v['snr'],
                    'cops'=> $v['cops'],
                    'cereg'=> $v['cereg'],
                    'result'=> $v['result'],
                    'is_hand'=> $v['is_hand'] ? 1 : 0,
                    'add_time'=> $v['current_time']
                );
                $recordDb->add($data);
            }
            $ret['status'] = 1;
            $ret['info'] = '添加成功';
            $this->ajaxReturn($ret);
        }else{
            $ret['status'] = 0;
            $ret['info'] = '数据格式错误';
            $this->ajaxReturn($ret);
        }

//        $data = json_decode(I('data'));
//
////        $memberInfo = parent::checkToken();
////        $saveData['userid'] = $memberInfo['userid'];
//        $saveData['device_number'] = I('cgsn');
//        $saveData['instruction'] = I('instruction');
//        $saveData['lon'] = I('lon');
//        $saveData['lat'] = I('lat');
//        $saveData['province'] = I('province');
//        $saveData['city'] = I('city');
//        $saveData['district'] = I('district');
//        $saveData['street'] = I('street');
//        $saveData['street_number'] = I('street_number');
//        $saveData['address'] = I('address');
////        $saveData['result'] = I('result');
//        $saveData['add_time'] = time();
//
//        $id = M('Instruction_record')->add($saveData);
//        if($id){
//            $ret['status'] = 1;
//            $ret['msg'] = '添加成功';
//            $this->ajaxReturn($ret);
//        }else{
//            $ret['status'] = -1;
//            $ret['msg'] = '添加失败';
//            $this->ajaxReturn($ret);
//        }
    }

    public function getVerifyCode(){
        $deviceId = I('device_id');
        $config = array(
            'fontSize'=>30,    // 验证码字体大小
            'length'=>4,     // 验证码位数
            'useNoise'=>false, // 关闭验证码杂点
        );
//        ob_clean();
        $verify = new \Think\Verify($config);
        $verify->codeSet = '0123456789';
        $verify->entry($deviceId,'base64');
    }

}

