<?php
// 后台菜单管理
namespace Admin\Controller;
class MenuController extends BaseController {
    public function __construct(){
        parent::__construct();
        $this->menuDb = M('Admin_menu');
    }
    /**
    * 菜单列表
    */
    public function menuLists(){
        $tree = new \Org\Tree\Tree;
        $where["is_delete"]=0;
        $data = $this->menuDb->where($where)->order('sort desc,id desc')->select();
        $menuList = $tree->makeTree($data);
        $this->assign("menuList",$menuList);
        adminLog("查看菜单列表");
        $this->display("menu_list");
    }
    // 菜单添加
    public function menuAdd(){
        if(IS_POST){
            $data = $_POST['info'];
            $result = $this->menuDb->add($data);
            if($result){
                $this->success("操作成功");
                adminLog("增加后台菜单".$data["name"]);
            }else{
                $this->error("操作失败");
            }
        }else{
            $where['parent_id'] = 0;
            $data = $this->menuDb->where($where)->select();
            $this->assign("menuList",$data);
            layout(false);
            $this->display("menu_add");
        }

    }
    // 菜单编辑
    public function menuEdit(){
        if(IS_POST){
            $id = $where['id'] = I("id");
            $data = $_POST['info'];
            $this->menuDb->where($where)->save($data);
            adminLog("修改后台菜单".$data["name"]);
            $this->success("操作成功");
        }else{
            $id = $where['id'] = I("id");
            $whereParent['parent_id'] = 0;
            $parentData = $this->menuDb->where($whereParent)->select();
            $this->assign("parentData",$parentData);
            $data = $this->menuDb->where($where)->find();
            $this->assign($data);
            layout(false);
            $this->display('menu_edit');
        }
    }
    // 菜单删除
    public function menuDelete(){
        if(IS_POST){
            $id = I('id');
            $whereData['parent_id'] = $id;
            $menuData = $this->menuDb->where($whereData)->find();
            if($menuData){
                $this->error("请先删除子菜单");
            }else{
                $where['id'] = $id;
                $data['is_delete'] =1;
                $this->menuDb->where($where)->save($data);
                adminLog("删除后台菜单，ID为".$id);
                $this->success("删除成功");
            }
        }
    }
}