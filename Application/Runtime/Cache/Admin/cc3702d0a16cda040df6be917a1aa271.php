<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title><?php echo C('WEB_NAME');?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="bookmark" href="/favicon.ico" />
  <link href="<?php echo C('ADMIN_CSS_PATH');?>/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo C('ADMIN_CSS_PATH');?>/font-awesome.min.css" rel="stylesheet">
  <link href="<?php echo C('ADMIN_CSS_PATH');?>/common.css" rel="stylesheet">
  <script src="<?php echo C('ADMIN_JS_PATH');?>/jquery-1.11.1.min.js"></script>
  <script src="<?php echo C('ADMIN_JS_PATH');?>/bootstrap.min.js" type="text/javascript"></script>
  <script src="<?php echo C('ADMIN_JS_PATH');?>/common.js"></script>
  <script src="<?php echo C('ADMIN_JS_PATH');?>/jquery.slimscroll.min.js"></script>
  <script src="<?php echo C('ADMIN_JS_PATH');?>/layer/layer.js"></script>
  <!-- <script src="<?php echo C('ADMIN_JS_PATH');?>/admin_template.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/admin.js"></script> -->
  <!-- Vue, element, 间距工具类 相关 -->
  <link rel="stylesheet" href="/smartGarden/Public/Admin/Css//util/flex.css">
  <link href="https://unpkg.com/basscss@8.0.2/css/basscss.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
  <link rel="stylesheet" href="/smartGarden/Public/Admin/element/index.css">
  <script src="https://unpkg.com/element-ui/lib/index.js"></script>
  <!-- Vue, element, 间距工具类 相关 -->
  <style>
    .reddot:after {
      content: "";
      position: absolute;
      top: 14px;
      right: 5px;
      padding: 3px;
      z-index: 9999999;
      background: #d9534f;
      border-radius: 50%;
      font-size: 0;
      line-height: 0;
      border: 1px solid #d43f3a;
    }
    .tooltip-dropdown li a{
      display: block;
      color: white;
      padding: 6px 12px;
      cursor: pointer;
    }
    .tooltip-dropdown li a:hover{
      color: #33D792;
    }
  </style>
</head>

<body class="overflow-hidden">
  <div class="wrapper preload">
    <header class="top-nav" id="HEADER">
      <div class="top-nav-inner">
        <div class="nav-header">
          <img src="/smartGarden/Public/Admin/Image/index/logo.png" alt="">
          <button type="button" class="navbar-toggle pull-left sidebar-toggle" id="sidebarToggleSM">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <ul class="nav-notification pull-right">
            <li>
              <a href="/smartGarden/manager.php?s=" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog fa-lg"></i></a>
              <span class="badge badge-danger bounceIn">1</span>
              <ul class="dropdown-menu dropdown-sm pull-right user-dropdown">
                <li class="user-avatar">
                  <img src="<?php if( session('admin_avatar')): echo session('admin_avatar');?>
                  <?php else: echo C('ADMIN_IMAGE_PATH');?>/profile/profile1.jpg<?php endif; ?>" alt="" class="img-circle">
                  <div class="user-content">
                    <h5 class="no-m-bottom"><?php echo session('admin_realname');?></h5>
                    <div class="m-top-xs">
                      <!--<a href="javascript:;" onclick="openItem(6,'manager.php?s=/Admin/myAdminEdit')" class="m-right-sm">我的资料</a>-->
                      <a href="manager.php?s=/Index/logout">退出</a>
                    </div>
                  </div>
                </li>
              </ul>
            </li>
          </ul>

          <!-- <a href="/smartGarden/manager.php?s=" class="brand">
							<img src="<?php echo C('ADMIN_IMAGE_PATH');?>/cyyl_logo.png" />
						</a> -->
        </div>
        <div class="nav-container">
          <div class="pull-right m-right-sm">
            <ul class="nav-notification">
              <li>
                <a href="#" data-toggle="dropdown"><i class="fa fa-bell fa-lg" style="color:#333;"></i></a>
                <span class="reddot" id="reddot"></span>
                <ul class="dropdown-menu notification dropdown-3 pull-right" style="min-width:300px;">
                  <!--<li><a href="#">员工脱离工作区域告警</a></li>-->
                  <li id="message-list">
                    <a>
                      <span class="notification-icon bg-warning">
                        <i class="fa fa-warning"></i>
                      </span>
                      <span class="m-left-xs">暂无新报警</span>
                      <span class="time text-muted">现在</span>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
            <div class="user-block hidden-xs">
              <el-tooltip placement="bottom">
                <ul slot="content" class="tooltip-dropdown">
                  <li>
                    <a style="">
                      修改密码
                    </a>
                  </li>
                  <li>
                    <a href="manager.php?s=/Index/logout" >
                      退出登录
                    </a>
                  </li>
                </ul>
                <a href="#" id="userToggle" data-toggle="dropdown">
                  <div class="user-detail inline-block mr2">
                    <?php if(session('admin_realname')): echo session('admin_realname');?>
                      <?php else: echo session('admin_username'); endif; ?>
                      <i class="el-icon-caret-bottom" style="color: #33D792;font-size:18px;"></i>
                    </div>
                    <img src="<?php if( session('admin_avatar')): echo session('admin_avatar');?>
                      <?php else: echo C('ADMIN_IMAGE_PATH');?>/avatar.jpg<?php endif; ?>" alt="" class="img-circle inline-block user-profile-pic">
                </a>
              </el-tooltip>
            </div>
          </div>
        </div>
      </div><!-- ./top-nav-inner -->
    </header>
<script>
const header = new Vue({
  el: '#HEADER'
})
</script>

<style media="screen">
  #SIDEMENU {
    background-color: #03454b;
  }

  #SIDEMENU ul.yl-side-menu>li {
    position: relative;
    overflow: visible;
  }

  #SIDEMENU ul.yl-side-menu>li>a,
  #SIDEMENU ul.yl-side-menu ul.yl-side-menu--sub-menu>li>a {
    padding: 18px 24px;
    color: #fff;
    cursor: pointer;
  }

  #SIDEMENU ul.yl-side-menu>li:hover>a {
    color: #33D792;
    background-color: #00383d;
  }

  #SIDEMENU ul.yl-side-menu li ul.yl-side-menu--sub-menu {
    position: absolute;
    top: 0;
    right: 0;
    background-color: #00383d;
    width: 100%;
    transition: all ease-in-out .2s;
    transform: translateX(100%);
    z-index: 2;
    display: none;
  }

  #SIDEMENU ul.yl-side-menu li:hover ul.yl-side-menu--sub-menu {
    display: block;
  }

  #SIDEMENU ul.yl-side-menu ul.yl-side-menu--sub-menu>li>a {
    display: block;
  }

  #SIDEMENU ul.yl-side-menu ul.yl-side-menu--sub-menu>li>a:hover {
    color: #33D792;
  }
</style>
<aside class="sidebar-menu fixed" id="SIDEMENU">
  <div>
    <div>
      <ul class="yl-side-menu">
        <li>
          <a href="/smartGarden/manager.php?s=" class="flex items-center">
            <i class="el-icon-s-home"></i>
            <span class="ml2">首页</span>
          </a>
        </li>
        <?php $__MENU__ = session('ADMIN_MENU_LIST'); ?>
        <?php if(is_array($__MENU__)): foreach($__MENU__ as $key=>$v): ?><li menu-id=<?php echo ($v['id']); ?>>
            <a class="flex justify-between items-center">
              <?php if($v['children']): else: ?>
                href="/smartGarden/manager.php?s=/<?php echo ($v['c']); ?>/<?php echo ($v['a']); ?>/<?php echo ($v['data']); ?>"<?php endif; ?>

              <div class="flex items-center">
                <i class="fa fa-<?php echo ($v['icon_class']); ?> fa-lg"></i>
                <span class="ml2"><?php echo ($v['name']); ?></span>
              </div>
              <?php if($v['children']): ?><i class="el-icon-caret-right"></i><?php endif; ?>
            </a>
            <?php if($v['children']): ?><ul class="yl-side-menu--sub-menu">
                <?php if(is_array($v['children'])): foreach($v['children'] as $key=>$r): ?><li>
                    <a href="javascript:;" onclick="openItem(<?php echo ($r['id']); ?>,'/smartGarden/manager.php?s=/<?php echo ($r['c']); ?>/<?php echo ($r['a']); ?>/<?php echo ($r['data']); ?>')" id="menu-<?php echo ($r['id']); ?>">
                      <span><?php echo ($r['name']); ?></span>
                    </a>
                  </li><?php endforeach; endif; ?>
              </ul><?php endif; ?>
          </li><?php endforeach; endif; ?>
        <li>
<<<<<<< HEAD
          <a href="/smartGarden/manager.php?s=/Index/bigScreen" class="flex items-center" target="_blank">
=======
          <a href="/manager.php?s=/Index/bigScreen" class="flex items-center" target="_blank">
>>>>>>> 8b8a78bac57e707262b86e0dc575d9db0058656a
            <i class="el-icon-full-screen"></i>
            <span class="ml2">大屏展示</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</aside>
<script>
  const sideMenu = new Vue({
    el: '#SIDEMENU',
    data() {
      return {

      }
    }
  })
</script>

<div id="container" class="main-container">
	<!-- <div class="breadcrumb">
		<span class="primary-font"><i class="fa fa-home"></i> 当前位置：</span>
		<span id="current_pos"></span>
	</div> -->
    <iframe name="center_frame" id="content-wrapper" src="/smartGarden/manager.php?s=/Index/main" frameborder="false" scrolling="auto" style="border:none;" width="100%" height="auto" frameborder="0" ></iframe>
</div>

        </div>
    </body>
    <script type="text/javascript">
         var context = new (window.AudioContext || window.webkitAudioContext)();
            var source = null;
            var audioBuffer = null;
            function stopSound() {
                if (source) {
                    source.noteOff(0); //立即停止
                }
            }
            function playSound() {
                source = context.createBufferSource();
                source.buffer = audioBuffer;
                source.loop = false;
                source.connect(context.destination);
                source.start(0); //立即播放
            }
            function initSound(arrayBuffer) {
                context.decodeAudioData(arrayBuffer, function(buffer) { //解码成功时的回调函数
                    audioBuffer = buffer;
                    playSound();
                }, function(e) { //解码出错时的回调函数
                    console.log('Error decoding file', e);
                });
            }
            function loadAudioFile(url) {
                var xhr = new XMLHttpRequest(); //通过XHR下载音频文件
                xhr.open('GET', url, true);
                xhr.responseType = 'arraybuffer';
                xhr.onload = function(e) { //下载完成
                    initSound(this.response);
                };
                xhr.send();
            }
        $(function(){
            var getWindowSize = function(){
                return ["Height","Width"].map(function(name){
                  return window["inner"+name] ||
                    document.compatMode === "CSS1Compat" && document.documentElement[ "client" + name ] || document.body[ "client" + name ]
                });
            }
            var str = getWindowSize();
            var strs = new Array();
                strs = str.toString().split(",");
            var heights = strs[0],Body = $('body');
            var headerH = $('.top-nav').outerHeight();
            var currentH = $('#breadcrumb').outerHeight();
            var footerH = $('.footer').outerHeight();
            $('#content-wrapper').attr("height",""+window.innerHeight-headerH-currentH-footerH-5+"");
            if(strs[1]<980){
                Body.attr('scroll','');
            }else{
                Body.attr('scroll','no');
            }
            setInterval(function(){
                getWarningMessage();
            },5000);
        })
         var html = '';
        function getWarningMessage() {
            $.ajax({
                url:'/smartGarden/manager.php?s=/Map/getWarningMessage',
                data:{last_id:localStorage.getItem('last_id')},
                dataType: 'json',
                type:'post',
                success:function(ret){
                    if(ret.status == 1){
                        var data = ret.data;
                        localStorage.setItem('last_id',data[0].id);
                        for(var i in data){
                            if(data[i].type == 2){
                                html +=
                                        '<a>'+
                                        '<span class="notification-icon bg-warning">'+
                                        '<i class="fa fa-warning"></i>'+
                                        '</span>'+
                                        '<span class="m-left-xs">'+data[i].realname+',工号:'+data[i].job_number+'远离区域报警</span>'+
                                        '<span class="time text-muted">'+data[i].add_time+'</span>'+
                                        '</a>';
                            }else if(data[i].type == 4){
                                html +=
                                        '<a>'+
                                        '<span class="notification-icon bg-warning">'+
                                        '<i class="fa fa-warning"></i>'+
                                        '</span>'+
                                        '<span class="m-left-xs">'+data[i].realname+',工号:'+data[i].job_number+'迟到报警</span>'+
                                        '<span class="time text-muted">'+data[i].add_time+'</span>'+
                                        '</a>';
                            }else if(data[i].type == 5){
                                html +=
                                        '<a>'+
                                        '<span class="notification-icon bg-warning">'+
                                        '<i class="fa fa-warning"></i>'+
                                        '</span>'+
                                        '<span class="m-left-xs">'+data[i].realname+',工号:'+data[i].job_number+'旷工报警</span>'+
                                        '<span class="time text-muted">'+data[i].add_time+'</span>'+
                                        '</a>';
                            }else if(data[i].type == 6){
                                html +=
                                        '<a>'+
                                        '<span class="notification-icon bg-warning">'+
                                        '<i class="fa fa-warning"></i>'+
                                        '</span>'+
                                        '<span class="m-left-xs">'+data[i].realname+',工号:'+data[i].job_number+'早退报警</span>'+
                                        '<span class="time text-muted">'+data[i].add_time+'</span>'+
                                        '</a>';
                            } else if(data[i].type == 3){
                                html +=
                                        '<a>'+
                                        '<span class="notification-icon bg-warning">'+
                                        '<i class="fa fa-warning"></i>'+
                                        '</span>'+
                                        '<span class="m-left-xs">'+data[i].realname+',工号:'+data[i].job_number+'设备电量过低</span>'+
                                        '<span class="time text-muted">'+data[i].add_time+'</span>'+
                                        '</a>';
                            }else if(data[i].type == 7){
                                html +=
                                        '<a>'+
                                        '<span class="notification-icon bg-warning">'+
                                        '<i class="fa fa-warning"></i>'+
                                        '</span>'+
                                        '<span class="m-left-xs">'+data[i].realname+',工号:'+data[i].job_number+'怠工报警</span>'+
                                        '<span class="time text-muted">'+data[i].add_time+'</span>'+
                                        '</a>';
                            }
                        }
                        $('#message-list').html(html);
                        loadAudioFile('/smartGarden/Public/Admin/File/alarm.mp3');
                    }
                }
            });
        }
    </script>
</html>