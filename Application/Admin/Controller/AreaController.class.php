<?php
// 地区管理
namespace Admin\Controller;
class AreaController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->areaDb = M('Area');
    }
    public function getAreaChildLists(){
        if(IS_POST){
            $where['parent_id'] = intval($_POST['pid']);
            $childLists = $this->areaDb->where($where)->select();
            if($childLists){
                $this->ajaxReturn($childLists);
            }
        }
    }
}