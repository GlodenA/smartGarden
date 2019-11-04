<?php
include "Uploader.class.php";
function ueditorAction_upload($action){

	$CONFIG = json_decode(C('UEDITOR_CONFIG'),true);
	/* 上传配置 */
	$base64 = "upload";
	switch (htmlspecialchars($action)) {
	    case 'uploadimage':
	        $config = array(
	            "pathFormat" => $CONFIG['imagePathFormat'],
	            "maxSize" => $CONFIG['imageMaxSize'],
	            "allowFiles" => $CONFIG['imageAllowFiles']
	        );
	        $fieldName = $CONFIG['imageFieldName'];
	        break;
	    case 'uploadscrawl':
	        $config = array(
	            "pathFormat" => $CONFIG['scrawlPathFormat'],
	            "maxSize" => $CONFIG['scrawlMaxSize'],
	            "allowFiles" => $CONFIG['scrawlAllowFiles'],
	            "oriName" => "scrawl.png"
	        );
	        $fieldName = $CONFIG['scrawlFieldName'];
	        $base64 = "base64";
	        break;
	    case 'uploadvideo':
	        $config = array(
	            "pathFormat" => $CONFIG['videoPathFormat'],
	            "maxSize" => $CONFIG['videoMaxSize'],
	            "allowFiles" => $CONFIG['videoAllowFiles']
	        );
	        $fieldName = $CONFIG['videoFieldName'];
	        break;
	    case 'uploadfile':
	    default:
	        $config = array(
	            "pathFormat" => $CONFIG['filePathFormat'],
	            "maxSize" => $CONFIG['fileMaxSize'],
	            "allowFiles" => $CONFIG['fileAllowFiles']
	        );
	        $fieldName = $CONFIG['fileFieldName'];
	        break;
	}

	/* 生成上传实例对象并完成上传 */
	$up = new Uploader($fieldName, $config, $base64);

	/**
	 * 得到上传文件所对应的各个参数,数组结构
	 * array(
	 *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
	 *     "url" => "",            //返回的地址
	 *     "title" => "",          //新文件名
	 *     "original" => "",       //原始文件名
	 *     "type" => ""            //文件类型
	 *     "size" => "",           //文件大小
	 * )
	 */

	/* 返回数据 */
	return json_encode($up->getFileInfo());
}
function ueditorAction_list($action){
	/* 判断类型 */
	switch ($action) {
	    /* 列出文件 */
	    case 'listfile':
	        $allowFiles = $CONFIG['fileManagerAllowFiles'];
	        $listSize = $CONFIG['fileManagerListSize'];
	        $path = $CONFIG['fileManagerListPath'];
	        break;
	    /* 列出图片 */
	    case 'listimage':
	    default:
	        $allowFiles = $CONFIG['imageManagerAllowFiles'];
	        $listSize = $CONFIG['imageManagerListSize'];
	        $path = $CONFIG['imageManagerListPath'];
	}
	$allowFiles = substr(str_replace(".", "|", join("", $allowFiles)), 1);

	/* 获取参数 */
	$size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
	$start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
	$end = $start + $size;

	/* 获取文件列表 */
	$path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
	$files = getfiles($path, $allowFiles);
	if (!count($files)) {
	    return json_encode(array(
	        "state" => "no match file",
	        "list" => array(),
	        "start" => $start,
	        "total" => count($files)
	    ));
	}

	/* 获取指定范围的列表 */
	$len = count($files);
	for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
	    $list[] = $files[$i];
	}
	//倒序
	//for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
	//    $list[] = $files[$i];
	//}

	/* 返回数据 */
	$result = json_encode(array(
	    "state" => "SUCCESS",
	    "list" => $list,
	    "start" => $start,
	    "total" => count($files)
	));

	return $result;
}
