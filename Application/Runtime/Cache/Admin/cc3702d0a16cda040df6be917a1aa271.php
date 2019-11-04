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
</style>
    </head>
    <body class="overflow-hidden">
    	<div class="wrapper preload">
    		<header class="top-nav">
				<div class="top-nav-inner">
					<div class="nav-header">
						<button type="button" class="navbar-toggle pull-left sidebar-toggle" id="sidebarToggleSM">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<ul class="nav-notification pull-right">
							<li>
								<a href="/WFGarden/manager.php?s=" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog fa-lg"></i></a>
								<span class="badge badge-danger bounceIn">1</span>
								<ul class="dropdown-menu dropdown-sm pull-right user-dropdown">
									<li class="user-avatar">
										<img src="<?php if(session('admin_avatar')): echo session('admin_avatar'); else: echo C('ADMIN_IMAGE_PATH');?>/profile/profile1.jpg<?php endif; ?>" alt="" class="img-circle">
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

						<!-- <a href="/WFGarden/manager.php?s=" class="brand">
							<img src="<?php echo C('ADMIN_IMAGE_PATH');?>/cyyl_logo.png" />
						</a> -->
					</div>
					<div class="nav-container">
						<button type="button" class="navbar-toggle pull-left sidebar-toggle" id="sidebarToggleLG">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<div class="pull-right m-right-sm">
							<div class="user-block hidden-xs">
								<a href="#" id="userToggle" data-toggle="dropdown">
									<img src="<?php if(session('admin_avatar')): echo session('admin_avatar'); else: echo C('ADMIN_IMAGE_PATH');?>/avatar.jpg<?php endif; ?>" alt="" class="img-circle inline-block user-profile-pic">
									<div class="user-detail inline-block">
										<?php if(session('admin_realname')): echo session('admin_realname'); else: echo session('admin_username'); endif; ?>
										<i class="fa fa-angle-down"></i>
									</div>
								</a>
								<div class="panel border dropdown-menu user-panel">
									<div class="panel-body paddingTB-sm">
										<ul>
											<!--<li>-->
												<!--<a href="javascript:;" onclick="openItem(6,'manager.php?s=/Admin/myAdminEdit')">-->
													<!--<i class="fa fa-edit fa-lg"></i><span class="m-left-xs">我的资料</span>-->
												<!--</a>-->
											<!--</li>-->
											<li>
												<a href="manager.php?s=/Index/logout">
													<i class="fa fa-power-off fa-lg"></i><span class="m-left-xs">退出登录</span>
												</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<ul class="nav-notification">
                                <li>
                                    <a href="#" data-toggle="dropdown"><i class="fa fa-bell fa-lg"></i></a>
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
                                        <!--<li><a href="#">查看全部告警信息</a></li>-->
                                    </ul>
                                </li>

                            </ul>
						</div>
					</div>
				</div><!-- ./top-nav-inner -->
			</header>
<aside class="sidebar-menu fixed">
    <div class="sidebar-inner scrollable-sidebar">
        <div class="main-menu">
            <ul class="accordion">
                <li class="bg-palette1 open">
                    <a href="/WFGarden/manager.php?s=">
                        <span class="menu-content block">
                            <span class="menu-icon"><i class="block fa fa-dashboard fa-lg"></i></span>
                            <span class="text m-left-sm">控制台</span>
                        </span>
                        <span class="menu-content-hover block">首页</span>
                    </a>
                </li>
                <?php $__MENU__ = session('ADMIN_MENU_LIST'); ?>
                <?php if(is_array($__MENU__)): foreach($__MENU__ as $key=>$v): if($v['children']): ?><li class="openable bg-palette1" menu-id=<?php echo ($v['id']); ?>>
                            <a href="/WFGarden/manager.php?s=/<?php echo ($v['c']); ?>/<?php echo ($v['a']); ?>/<?php echo ($v['data']); ?>">
                                <span class="menu-content block">
                                    <span class="menu-icon"><i class="block fa fa-<?php echo ($v['icon_class']); ?> fa-lg"></i></span>
                                    <span class="text m-left-sm"><?php echo ($v['name']); ?></span>
                                    <span class="submenu-icon"></span>
                                </span>
                                <span class="menu-content-hover block"><?php echo ($v['name']); ?></span>
                            </a>
                            <ul class="submenu">
                                <?php if(is_array($v['children'])): foreach($v['children'] as $key=>$r): ?><li><a href="javascript:;" onclick="openItem(<?php echo ($r['id']); ?>,'/WFGarden/manager.php?s=/<?php echo ($r['c']); ?>/<?php echo ($r['a']); ?>/<?php echo ($r['data']); ?>')" id="menu-<?php echo ($r['id']); ?>"><span class="submenu-label"><?php echo ($r['name']); ?></span></a></li><?php endforeach; endif; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="openable bg-palette1" menu-id=<?php echo ($v['id']); ?>>
                            <a href="javascript:;" onclick="openItem(<?php echo ($r['id']); ?>,'/WFGarden/manager.php?s=/<?php echo ($r['c']); ?>/<?php echo ($r['a']); ?>/<?php echo ($r['data']); ?>')" id="menu-<?php echo ($r['id']); ?>">
                                <span class="menu-content block">
                                    <span class="menu-icon"><i class="block fa fa-<?php echo ($v['icon_class']); ?> fa-lg"></i></span>
                                    <span class="text m-left-sm"><?php echo ($v['name']); ?></span>
                                    <span class="submenu-icon"></span>
                                </span>
                                <span class="menu-content-hover block"><?php echo ($v['name']); ?></span>
                            </a>
                        </li><?php endif; endforeach; endif; ?>
            </ul>
        </div>
    </div>
</aside>
<div id="container" class="main-container">
	<!-- <div class="breadcrumb">
		<span class="primary-font"><i class="fa fa-home"></i> 当前位置：</span>
		<span id="current_pos"></span>
	</div> -->
    <iframe name="center_frame" id="content-wrapper" src="/WFGarden/manager.php?s=/Index/main" frameborder="false" scrolling="auto" style="border:none;" width="100%" height="auto" frameborder="0" ></iframe>
</div>

            <footer class="footer">
                <!-- <img src="<?php echo C('ADMIN_IMAGE_PATH');?>/logo-w.png" /> -->
                <p class="no-margin">
                    &copy; <strong>中国联合网络通信有限公司潍坊分公司</strong>. ALL Rights Reserved.
                </p>
            </footer>
        </div>
       <!-- <audio id="alarmAudio" muted src="/WFGarden/Public/Admin/File/alarm.mp3" style="display:none">不支持的浏览器</audio>-->
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
                url:'/WFGarden/manager.php?s=/Map/getWarningMessage',
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
                                        '<span class="m-left-xs">'+data[i].realname+',工号:'+data[i].job_number+'静止报警</span>'+
                                        '<span class="time text-muted">'+data[i].add_time+'</span>'+
                                        '</a>';
                            }

                        }
                        $('#message-list').html(html);
                        loadAudioFile('/WFGarden/Public/Admin/File/alarm.mp3');
                    }
//                    else{
//                        document.getElementById("reddot").style.display="none";
//                    }
                }
            });
        }


        // loadAudioFile('/WFGarden/Public/Admin/File/alarm.mp3');


    </script>
</html>