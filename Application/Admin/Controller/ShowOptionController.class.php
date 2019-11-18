<?php
/**
 * Created by PhpStorm.
 * User: 焱
 * Date: 2018/8/29
 * Time: 14:59
 */

namespace Admin\Controller;
class ShowOptionController extends BaseController
{
    public function __construct(){
        parent::__construct();
        $this->showOptionDb = M('Show_option');
        $this->userOptionDb = M('User_option');
    }

    // 管理组设置菜单
    public function optionSelect(){
        if(IS_POST){
            $whereGroup['uid'] = I('uid');
            $ids = I('ids');
            if($ids){
                $this->userOptionDb->where($whereGroup)->setField("menu_ids",$ids);
                $this->success("操作成功");
            }else{
                $this->error("操作失败");
            }
        }else{//查询用户展示的表，查询所有展示的表
            $where['uid'] = I("uid");
            $userOptionData = $this->userOptionDb->where($where)->find();
            $userOptionData['option_ids']  =  explode(',',$userOptionData['option_ids']);
            // dump($groupData);
            $this->assign($userOptionData);//用户现在所展示的
            // 获取
            $where["status"] = 0;
            $OptionList = $this->showOptionDb->where($where)->order('id desc')->select();
            $this->assign("optionList",$OptionList);//所有展示的。
            $this->display("index");
        }
    }
}