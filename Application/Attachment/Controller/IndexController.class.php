<?php
namespace Attachment\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function __construct(){
        parent::__construct();
        $this->attachmentDb = M('Attachment');
    }
	//后台单个图片上传,接受名为file
    public function adminUploadOne(){
		$upload = new \Think\Upload();
	    $upload->maxSize   =     1048576;
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
	    $upload->rootPath  =     './'.C('UPLOAD_PATH');
	    $upload->savePath  =     '';
	    $upload->saveName = time().mt_rand();
	    // 上传文件
	    $info = $upload->upload();
	    if(!$info) {
	        $this->ajaxReturn($upload->getError());
	    }else{

	    	$info['status'] = "success";
	    	// $key = array_keys($_FILES)[0];
	    	$key = 'file';
	    	$info['path'] = C('UPLOAD_PATH').$info[''.$key.'']['savepath'].$info[''.$key.'']['savename'];
	    	// 插入附件表
	    	$data['admin_uid'] = session('admin_uid');
	    	$data['name'] = $info[$key]['name'];
	    	$data['savename'] = $info[$key]['savename'];
	    	$data['size'] = $info[$key]['size'];
	    	$data['ext'] = $info[$key]['ext'];
	    	$data['type'] = $info[$key]['type'];
	    	$data['path'] = C('UPLOAD_PATH').$info[$key]['savepath'];
	    	$data['url'] = $info['path'];
	    	$data['addtime'] = time();
	    	$this->attachmentDb->add($data);
	    	$this->ajaxReturn($info);
	    }
    }
    public function editAvatar(){
        $upload = new \Think\Upload();
        $upload->maxSize   =     1048576;
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath  =     './'.C('UPLOAD_PATH');
        $upload->savePath  =     '';
        $upload->saveName = time().mt_rand();
        // 上传文件
        $info = $upload->upload();
        if(!$info) {
            $this->ajaxReturn($upload->getError());
        }else{

            $info['status'] = "success";
            // $key = array_keys($_FILES)[0];
            $key = 'file';
            $info['path'] = C('UPLOAD_PATH').$info[''.$key.'']['savepath'].$info[''.$key.'']['savename'];
            $this->ajaxReturn($info,'text');
        }
    }
    public function adminUploadVideo(){
	 	$upload = new \Think\Upload();
        $upload->maxSize   =     52428800;
        $upload->exts      =     array('rmvb','mp4','avi','wmv','mpg','flv','rmvb','mov');
        $upload->rootPath  =     './'.C('UPLOAD_PATH');
        $upload->savePath  =     '';
        $upload->saveName = time().mt_rand();
        // 上传文件
        $info = $upload->upload();
        if(!$info) {
            $this->ajaxReturn($upload->getError());
        }else{

            $info['status'] = "success";
            // $key = array_keys($_FILES)[0];
            $key = 'file';
            $info['path'] = C('UPLOAD_PATH').$info[''.$key.'']['savepath'].$info[''.$key.'']['savename'];
            $this->ajaxReturn($info);
        }
    }
    //编辑器内图片上传
    public function editorUpload(){
		$upload = new \Think\Upload();
	    $upload->maxSize   =     0;
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
	    $upload->rootPath  =     './'.C('UPLOAD_PATH');
	    $upload->savePath  =     '';
	    $upload->saveName = time().mt_rand();
	    // 上传文件
	    $info = $upload->upload();
	    if(!$info) {
	        $this->error($upload->getError());
	    }else{
	    	$info['status'] = "success";
	    	// $key = array_keys($_FILES)[0];
	    	$key = 'upload';
	    	$info['path'] = "/".C('UPLOAD_PATH').$info[''.$key.'']['savepath'].$info[''.$key.'']['savename'];
	    	$fn = intval($_GET['CKEditorFuncNum']);
	    	if($fn){
	    		$info['thumb'] = reduce($info['path'],480,480);
	    		$this->mkhtml($fn,$info['thumb']);
	    	}else{
	    		$this->ajaxReturn($info);
	    	}
	    }
    }
    // 图片上传
    public function upload(){
		$upload = new \Think\Upload();
	    $upload->maxSize   =     1048576;
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
	    $upload->rootPath  =     './'.C('UPLOAD_PATH');
	    $upload->savePath  =     '';
	    $upload->saveName = time().mt_rand();
	    // 上传文件
	    $info = $upload->upload();
	    if(!$info) {
	        $this->ajaxReturn($upload->getError());
	    }else{

	    	$info['status'] = "success";
	    	// $key = array_keys($_FILES)[0];
	    	$key = 'file';
	    	$info['path'] = C('UPLOAD_PATH').$info[''.$key.'']['savepath'].$info[''.$key.'']['savename'];
	    	// 插入附件表
	    	$data['userid'] = session('userid');
	    	$data['name'] = $info[$key]['name'];
	    	$data['savename'] = $info[$key]['savename'];
	    	$data['size'] = $info[$key]['size'];
	    	$data['ext'] = $info[$key]['ext'];
	    	$data['type'] = $info[$key]['type'];
	    	$data['path'] = C('UPLOAD_PATH').$info[$key]['savepath'];
	    	$data['url'] = $info['path'];
	    	$data['addtime'] = time();
	    	$this->attachmentDb->add($data);
	    	$fn = intval($_GET['CKEditorFuncNum']);
	    	if($fn){
	    		$this->mkhtml($fn,$data['url']);
	    	}else{
	    		$this->ajaxReturn($info);
	    	}
	    }
    }
    public function appUpload(){
		$upload = new \Think\Upload();
	    $upload->maxSize   =     1048576;
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
	    $upload->rootPath  =     './'.C('UPLOAD_PATH');
	    $upload->savePath  =     '';
	    $upload->saveName = time().mt_rand();
	    // 上传文件
	    $info = $upload->upload();
	    if(!$info) {
	    	$this->ajaxReturn($upload->getError());
	    }else{
	    	$info['path'] =  C('UPLOAD_PATH').$info['file']['savepath'].$info['file']['savename'];
	    	$info['thumb'] = thumb($info['path'],200,200);
	    	$this->ajaxReturn($info);
	    }
    }
    /**
	 * ck编辑器返回
	 * @param $fn
	 * @param $fileurl 路径
	 * @param $message 显示信息
	 */
	function mkhtml($fn,$fileurl,$message) {
		$str='<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$fn.', \''.$fileurl.'\', \''.$message.'\');</script>';
		exit($str);
	}

	//后台单个图片上传,接受名为file
	public function excelUpload(){
		$upload = new \Think\Upload();
		$upload->maxSize   =     0;
		$upload->exts      =     array('xls', 'xlsx');
		$upload->rootPath  =     './'.C('UPLOAD_PATH');
		$upload->savePath  =     '';
		$upload->saveName = time().mt_rand();
		// 上传文件
		$info = $upload->upload();
		if(!$info) {
			$this->ajaxReturn($upload->getError());
		}else{

			$info['status'] = "success";
			// $key = array_keys($_FILES)[0];
			$key = 'file';
			$info['path'] = C('UPLOAD_PATH').$info[''.$key.'']['savepath'].$info[''.$key.'']['savename'];
			// 插入附件表
			$data['admin_uid'] = session('admin_uid');
			$data['name'] = $info[$key]['name'];
			$data['savename'] = $info[$key]['savename'];
			$data['size'] = $info[$key]['size'];
			$data['ext'] = $info[$key]['ext'];
			$data['type'] = $info[$key]['type'];
			$data['path'] = C('UPLOAD_PATH').$info[$key]['savepath'];
			$data['url'] = $info['path'];
			$data['addtime'] = time();
			$this->attachmentDb->add($data);
			$this->ajaxReturn($info);
		}
	}
}