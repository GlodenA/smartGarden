<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo C('WEB_NAME');?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<!--<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />-->
<meta name="renderer" content="webkit" />
<script type="text/javascript" src="<?php echo C('HOME_JS_PATH');?>/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="<?php echo C('HOME_JS_PATH');?>/main.min.js"></script>
<script type="text/javascript" src="<?php echo C('HOME_JS_PATH');?>/onepage.min.js"></script>
<link type="text/css" rel="stylesheet" href="<?php echo C('HOME_CSS_PATH');?>/features.css" />
<link type="text/css" rel="stylesheet" href="<?php echo C('HOME_CSS_PATH');?>/yty.css" />
<link rel="shortcut icon" href="/favicon.ico" />
<meta name="description" content="<?php echo C('WEB_NAME');?>" />
<meta name="keywords" content="物联网智能环卫系统,环卫系统,物联网,智能环卫系统" />
<script type="text/javascript">
$(function(){

	function resize(){
		var window_height = $(window).height();
		var window_height_unit = window_height + 'px';

		//$(".oenpage-main, .oenpage-wrap, .oenpage-section").css("height", window_height_unit);

		var six_buttons_session_height = $(".six-buttons-section").height();
		$(".six-banner").css("height", (window_height-six_buttons_session_height) + "px");

		// var gen_height = 870;
		// if (window_height<gen_height){
		// 	//var rote = Math.round(window_height / gen_height * 100);
		// 	//$(".section-img").css("background-size", "auto " + rote + "%");

		// 	var rote = window_height / gen_height;
		// 	$(".section-img").each(function(index){
		// 		var $this = $(this);
		// 		var org_height = $this.data("height");
		// 		if(org_height != undefined){
		// 			$this.css("background-size", "auto " + Math.round(rote * org_height) + "px");
		// 		}
		// 	});

		// }
		// else{
		// 	$(".section-img").css("background-size", "auto auto");
		// }
		
	};
	resize();
	$(window).resize(resize);

	//banner 图片切换
	(function(){

		var last_out_timehandle = null;
		function bun_reset(){
			last_out_timehandle = null;
			var last_number = $(".six-buttons-section .nav").data("last_number");
			if(last_number != undefined){
				$(".six-banner-" + last_number + "-section").hide();
				$(".six-banner-" + last_number + "-section .section-img").hide();
				$(".features-icons-six-" + last_number).removeClass("hover");
				$('.six-buttons-section .nav li').removeClass('hover');
			}

			$(".six-banner-section").show();
			$(".six-banner-section .section-img").fadeIn("slow");
			$(".six-buttons-section").removeClass('selected');
			$(".six-buttons-section .nav").data("last_number", null);
		}


		$(".six-buttons-section .nav li").hover(function(){
			var $this = $(this);
			if (last_out_timehandle != null){
				clearTimeout(last_out_timehandle);
				last_out_timehandle = null;
			}

			var last_number = $(".six-buttons-section .nav").data("last_number");
			var number = "0" + ($this.index() + 1);

			if(last_number != undefined && last_number==number){
				return;
			}

			if(last_number != undefined){
				$(".six-banner-" + last_number + "-section").hide();
				$(".six-banner-" + last_number + "-section .section-img").hide();
				$(".features-icons-six-" + last_number).removeClass("hover");
				$('.six-buttons-section .nav li').removeClass('hover');
			}
			$(".six-banner-section").hide();
			$(".six-banner-section .section-img").hide();

			$(".six-banner-" + number + "-section").show();
			$(".six-banner-" + number + "-section .section-img").fadeIn("slow");
			$(".features-icons-six-" + number).addClass("hover");
			$this.addClass("hover");

			
			$(".six-buttons-section").addClass('selected');

			$(".six-buttons-section .nav").data("last_number", number);
			//event.stopPropagation();
		}, function(){
			//last_out_timehandle = setTimeout(bun_reset, 10000);
		});

		//banner 图片点击复原
		$(".six-buttons-section ul").click(function(){
			event.stopPropagation();
			return;
		});

		$(".six-buttons-section").click(function(){
			if (last_out_timehandle != null){
				clearTimeout(last_out_timehandle);
				last_out_timehandle = null;
			}
			bun_reset();
		});
	})();

	//健康
	// var oneScroll_health = $(".onepage-health").OnePageScroll({
	// 	sectionSelector: ".health-01-section, .health-02-section, .health-03-section",
	// 	direction: "horizontal", 
	// 	afterMove: function(targetid){
	// 		$('.health-buttons-section .nav i').removeClass('hover').eq(targetid).addClass('hover');
	// 	},
	// 	quietPeriod: 0,updateURL: false, pagination: false, isChild: true
	// });
	
	var health_change = {
		curIndex: 0,
		$section: $(".health-01-section, .health-02-section, .health-03-section,.health-04-section"),
		moveUp: function(){
			if(this.curIndex <= 0){return false;}
			this.moveTo(this.curIndex - 1);
			return true;
		},
		moveDown: function(){
			if((this.curIndex+1) >= this.$section.length){return false;}
			this.moveTo(this.curIndex + 1);
			return true;
		},
		moveTo: function(target){
			if(target == this.curIndex)return;
			this.$section.eq(this.curIndex).hide();
			var $parent = this.$section.parent(),
			$curIndex = this.curIndex;
			this.$section.eq(target).fadeIn("slow",function(){
				$parent.removeClass("health-0"+ ($curIndex + 1) +"-bg");
				$parent.addClass("health-0"+ (target + 1) +"-bg");
			});
			$('.health-buttons-section .nav i').removeClass('hover').eq(target).addClass('hover');
			this.curIndex = target;
		}
	};
	$(".health-buttons-section .nav li").mouseover(function(){
		health_change.moveTo($(this).index());
	});

	//子女端
	var oneScroll_appchild = $(".onepage-appchild").OnePageScroll({
		sectionSelector: ".child-01-section, .child-02-section",
		direction: "horizontal",
		afterMove: function(targetid){
			$('.child-buttons-section .nav i').removeClass('hover').eq(targetid).addClass('hover');
		},
		quietPeriod: 0,updateURL: false, pagination: false, isChild: true
	});
	$(".child-buttons-section .nav li").mouseover(function(){
			oneScroll_appchild.moveTo($(this).index());
	});


	//charge 切换
	var oneScroll_charge = $(".onepage-charge").OnePageScroll({
		sectionSelector: ".charge-01-section, .charge-02-section, .charge-03-section,.charge-04-section,.charge-05-section,.charge-06-section",
		direction: "horizontal",
		afterMove: function(targetid){
			$('.charge-buttons-section .nav i').removeClass('hover').eq(targetid).addClass('hover');
		},
		quietPeriod: 0,updateURL: false, pagination: false, isChild: true
	});
	$(".charge-buttons-section .nav li").mouseover(function(){
			oneScroll_charge.moveTo($(this).index());
	});

	// 联系人演示动画
	var contacts_animate_time_handle = null;
	var contacts_animate = function(){
		//var offsets = [0, -400, -800, -1200, -800, -400];
		var offsets = [0, -400];
		var length = offsets.length;
		var curindex = 1;

		function change(){
			curindex++;
			if(curindex >= offsets.length){
				curindex = 0;
			}
			$(".contacts-section .flow").animate({"left":offsets[curindex] + "px"},1000);

			//contacts_animate_time_handle = setTimeout(change, 3000);
		}

		if(contacts_animate_time_handle != null){
			clearTimeout(contacts_animate_time_handle);
			contacts_animate_time_handle =  null;
		}

		$(".contacts-section .flow").css({"left":offsets[curindex] + "px"});
		contacts_animate_time_handle = setTimeout(change, 2000);
	}



	var last_color_style = "";
	function onepage_afterMove(targetid, direction){
		var pagination_style = $(".onepage-section").eq(targetid).attr("data-pagination-style");
		if(last_color_style != ""){
			$(".onepage-pagination").removeClass(last_color_style);
		}
		if(pagination_style != undefined){
			$(".onepage-pagination").addClass(pagination_style);
			last_color_style = pagination_style;
		}

		//联系人
		if(targetid == 4){
			contacts_animate();
		}
		else{
			if(contacts_animate_time_handle != null){
				clearTimeout(contacts_animate_time_handle);
				contacts_animate_time_handle =  null;
			}
		}

		return true;
	}

	var onepage_isdown = false;
	var onepage_margintop = 0;
	var onepage_margintop_offset = 400;
	function onepage_beforeMove(targetid, direction){
		// 控制最底部
		if(targetid == 6 && direction=="down"){
			$(".onepage-section").eq(targetid-1).css({"height":"auto"});
			var section_height = $(".onepage-section").eq(targetid-1).height();
			$(".onepage-wrapper:first").css({'height': section_height});
			var max_margintop = section_height - $(window).height() + 60;
			if (onepage_margintop < max_margintop){
				var offset = Math.round($(window).height() * 0.75);
				onepage_margintop += offset;
				if(onepage_margintop > max_margintop)onepage_margintop = max_margintop;
				//$(".header").hide();
				$(".onepage-wrapper:first").animate({marginTop: "-"+ onepage_margintop +"px"},"slow");

			}
			
			onepage_isdown = true;
			return false;
		}
		else if(onepage_isdown && direction=="up"){
			if (onepage_margintop > 0){
				var offset = Math.round($(window).height() * 0.75);
				onepage_margintop -= offset;
				if(onepage_margintop < 0)onepage_margintop = 0;
				$(".onepage-wrapper:first").animate({marginTop: "-"+ onepage_margintop +"px"},"slow");
			}

			if(onepage_margintop == 0){
				onepage_isdown = false;
				//$(".header").show();
			}

			return false;
		}
		else if(onepage_isdown && $.isNumeric(direction)){
			$(".onepage-wrapper:first").css("margin-top","0px");
			//$(".header").show();
			onepage_isdown = false;
			onepage_margintop = 0;
		}
		
		// 健康
		// if((targetid == 3 && direction=="up") || (targetid == 5 && direction=="down")){
		// 	if(direction == "up" && health_change.moveUp()){
		// 		return false;
		// 	}
		// 	else if(direction == "down" && health_change.moveDown()){
		// 		return false;
		// 	}
			
		// }

		// //子女端
		// if((targetid == 3 && direction=="up") || (targetid == 5 && direction=="down")){
		// 	if(direction == "up" && oneScroll_appchild.moveUp()){
		// 		return false;
		// 	}
		// 	else if(direction == "down" && oneScroll_appchild.moveDown()){
		// 		return false;
		// 	}
			
		// }

		// //子女端
		// if((targetid == 4 && direction=="up") || (targetid == 5 && direction=="down")){
		// 	if(direction == "up" && oneScroll_charge.moveUp()){
		// 		return false;
		// 	}
		// 	else if(direction == "down" && oneScroll_charge.moveDown()){
		// 		return false;
		// 	}
			
		// }
		
		return true;
	}

	var oneScroll = $(".OnePageScroll").OnePageScroll({
		sectionSelector: ".onepage-section",
		quietPeriod: 0,
		afterMove: onepage_afterMove,
		beforeMove: onepage_beforeMove,
		updateURL: !0,
		bigfooter: true
	});

	
});

</script>
<style type="text/css">
	.card-bg{
		background-color: rgba(0,0,0,0);
	}
</style>
</head>
<body>
	<!--[inc=head_nav][begin][1423906716:]-->
	<div class="section header">
		<div class="section-inner">
			<div class="logo">
				<a href="#" ><img src="<?php echo C('HOME_IMAGE_PATH');?>/logo.png" alt="极简手机"></a>
			</div>
			<div class="nav-more"><i class="icon-menu-more"></i></div>
			<div class="nav">
				<ul>
					<!-- <li class="nomobile"><a href="#">特色功能</a></li> -->
					<li><a href="http://testdw.ttshunt.com/manager.php" target="_Blank" class="buy">快速演示</a></li>
				</ul>
			</div>
		</div>
	</div>
	<!--[inc=head_nav][end]-->
	<script type="text/javascript">
		$(".header").removeClass('fixed').addClass('fixed');
	</script>
	
	<div class="OnePageScroll">
		<!-- 1 -->
		<div class="onepage-section" data-title="简介" data-pagination-style="white">
			<div class="section sms-section">
				<div class="section-img" data-height="770">
					<div class="section-inner">
						<!-- <div class="copy">
							<h2 class="title">物联网智能环卫系统</h2>
							<h3 class="subtitle">大大提高作业效率，保障作业效果，实现城乡环卫作业水平统一。</h3>
						</div> -->
					</div>
				</div>
			</div>
		</div>
		<!-- 2 -->
		<div class="onepage-section" data-title="平台亮点">
			<div class="section six-banner-section six-banner">
				<div class="section-img" data-height="461">
					<div class="section-inner">
						<div class="copy">
							<h2>七大功能特点</h2>
							<h3>落实“定人、定岗、定量、定责”机制，优化保洁作业模式，建立并完善精细化、规范化、标准化的道路保洁作业体系、质量体系、管理体系，全面提升城市功能品质，让城市更加整洁、优美。 </h3>
						</div>
					</div>
				</div>
			</div>
			<div class="section six-banner-01-section six-banner">
				<div class="section-img" data-height="572">
					<div class="section-inner">
						<div class="copy">
							<h2>环卫工作规范化管理</h2>
							<h3>利用“物联网”，让环卫工作，可标准化，可量化，可视化，达到环卫工作管理的规范化和信息化</h3>
							<img src="<?php echo C('HOME_IMAGE_PATH');?>/guifan.png" style="margin: 50px 0 50px">
						</div>
						<div class="row">
							<a class="col-md-6 yty-margin-b-10" href="">
								<div class="car-det yty-list aui-media-list card-bg">
							        <div class="yty-list-item yty-list-item-middle">
							            <div class="aui-media-list-item-inner">
							                <div class="yty-list-item-media cad-bg-icon" >
							                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/1-guifan.png" class="yty-list-img-sm">
							                </div>
							                <div class="yty-list-item-inner yty-padded-l-10">
							                    <div class="yty-list-item-text">
							                        <div class="yty-list-item-title aui-font-size-16">规范用工</div>
							                    </div>
							                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
							                        灵活设置不同岗位的职责和监管要求，规范用工
							                    </div>
							                </div>
							            </div>
							        </div>
               					</div>
               				</a>
               				<a class="col-md-6 yty-margin-b-10" href="">
								<div class="car-det yty-list aui-media-list card-bg">
							        <div class="yty-list-item yty-list-item-middle">
							            <div class="aui-media-list-item-inner">
							                <div class="yty-list-item-media cad-bg-icon" >
							                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/1-quanfugai.png" class="yty-list-img-sm">
							                </div>
							                <div class="yty-list-item-inner yty-padded-l-10">
							                    <div class="yty-list-item-text">
							                        <div class="yty-list-item-title aui-font-size-16">路段全覆盖</div>
							                    </div>
							                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
							                        根据路段要求设定排班制度，确保重点路段全时覆盖、无缝对接
							                    </div>
							                </div>
							            </div>
							        </div>
               					</div>
               				</a>
               				<a class="col-md-6 yty-margin-b-10" href="">
								<div class="car-det yty-list aui-media-list card-bg">
							        <div class="yty-list-item yty-list-item-middle">
							            <div class="aui-media-list-item-inner">
							                <div class="yty-list-item-media cad-bg-icon" >
							                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/1-zhize.png" class="yty-list-img-sm">
							                </div>
							                <div class="yty-list-item-inner yty-padded-l-10">
							                    <div class="yty-list-item-text">
							                        <div class="yty-list-item-title aui-font-size-16">规定作业职责</div>
							                    </div>
							                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
							                        灵活设置每个人的工作区域，划定作业职责
							                    </div>
							                </div>
							            </div>
							        </div>
               					</div>
               				</a>	
               				<a class="col-md-6 yty-margin-b-10" href="">
								<div class="car-det yty-list aui-media-list card-bg">
							        <div class="yty-list-item yty-list-item-middle">
							            <div class="aui-media-list-item-inner">
							                <div class="yty-list-item-media cad-bg-icon" >
							                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/1-biaozhun.png" class="yty-list-img-sm">
							                </div>
							                <div class="yty-list-item-inner yty-padded-l-10">
							                    <div class="yty-list-item-text">
							                        <div class="yty-list-item-title aui-font-size-16">工作量标准</div>
							                    </div>
							                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
							                        根据作业职责，灵活设定作业路线、作业圈数和公里数的工作量标准
							                    </div>
							                </div>
							            </div>
							        </div>
               					</div>
               				</a>	
               			</div>
					</div>
				</div>
			</div>
			<div class="section six-banner-02-section six-banner">
				<div class="section-img" data-height="709">
					<div class="section-inner">
						<div class="copy">
							<h2>环卫工作流程可监管</h2>
							<h3>环卫工作的整个流程，从上班到岗到下班打卡，全流程数据监管</h3>
							<img src="<?php echo C('HOME_IMAGE_PATH');?>/jianguan.png" style="margin: 50px 0 50px">
						</div>
						<div class="row">
							<a class="col-md-6 yty-margin-b-10" href="">
								<div class="car-det yty-list aui-media-list card-bg">
							        <div class="yty-list-item yty-list-item-middle">
							            <div class="aui-media-list-item-inner">
							                <div class="yty-list-item-media cad-bg-icon" >
							                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/2-guiji.png" class="yty-list-img-sm">
							                </div>
							                <div class="yty-list-item-inner yty-padded-l-10">
							                    <div class="yty-list-item-text">
							                        <div class="yty-list-item-title aui-font-size-16">定位轨迹</div>
							                    </div>
							                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
							                        实时查看人员在岗分布，确保重点路段有人值守；查看人员作业轨迹，辅助检查舞弊行为
							                    </div>
							                </div>
							            </div>
							        </div>
               					</div>
               				</a>
               				<a class="col-md-6 yty-margin-b-10" href="">
								<div class="car-det yty-list aui-media-list card-bg">
							        <div class="yty-list-item yty-list-item-middle">
							            <div class="aui-media-list-item-inner">
							                <div class="yty-list-item-media cad-bg-icon" >
							                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/2-kaoqin.png" class="yty-list-img-sm">
							                </div>
							                <div class="yty-list-item-inner yty-padded-l-10">
							                    <div class="yty-list-item-text">
							                        <div class="yty-list-item-title aui-font-size-16">自动考勤</div>
							                    </div>
							                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
							                        自动循环排班，无需打卡，自动监控人员的迟到、早退和旷工行为，自动汇总月度出勤报表
							                    </div>
							                </div>
							            </div>
							        </div>
               					</div>
               				</a>
               				<a class="col-md-6 yty-margin-b-10" href="">
								<div class="car-det yty-list aui-media-list card-bg">
							        <div class="yty-list-item yty-list-item-middle">
							            <div class="aui-media-list-item-inner">
							                <div class="yty-list-item-media cad-bg-icon" >
							                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/2-weigui.png" class="yty-list-img-sm">
							                </div>
							                <div class="yty-list-item-inner yty-padded-l-10">
							                    <div class="yty-list-item-text">
							                        <div class="yty-list-item-title aui-font-size-16">自动违规监管</div>
							                    </div>
							                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
							                        脱离工作区域、违规停留自动报警，方便管理人员第一时间发现问题进行监管，解决主干道看不见工人、主观不作为的问题
							                    </div>
							                </div>
							            </div>
							        </div>
               					</div>
               				</a>	
               				<a class="col-md-6 yty-margin-b-10" href="">
								<div class="car-det yty-list aui-media-list card-bg">
							        <div class="yty-list-item yty-list-item-middle">
							            <div class="aui-media-list-item-inner">
							                <div class="yty-list-item-media cad-bg-icon" >
							                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/2-jiance.png" class="yty-list-img-sm">
							                </div>
							                <div class="yty-list-item-inner yty-padded-l-10">
							                    <div class="yty-list-item-text">
							                        <div class="yty-list-item-title aui-font-size-16">工作量自动监测</div>
							                    </div>
							                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
							                        自动记录保洁人员的作业时间和作业圈数、公里数，直观查看工作量完成情况
							                    </div>
							                </div>
							            </div>
							        </div>
               					</div>
               				</a>	
               			</div>
					</div>
				</div>
			</div>
			<div class="section six-banner-03-section six-banner">
				<div class="section-img" data-height="446">
					<div class="section-inner">
						<div class="copy">
							<h2>人员信息统一管理</h2>
							<h3>把参与环卫工作的所有人员信息、负责区域，统一纳入系统，让人、岗、区域统一，实现环卫工作管理的智能化</h3>
						</div>
					</div>
				</div>
			</div>
			<div class="section six-banner-04-section six-banner">
				<div class="section-img" data-height="467">
					<div class="section-inner">
						<div class="copy">
							<h2>在岗监督</h2>
							<h3>督查人员通过系统后台可实时查看各人员区域的在岗情况，通过系统可迅速核对，更快捷有效的提高督查人员的工作效率</h3>
						</div>
					</div>
				</div>
			</div>
			<div class="section six-banner-05-section six-banner">
				<div class="section-img" data-height="415">
					<div class="section-inner">
						<div class="copy">
							<h2>智能化监管</h2>
							<h3>系统提供“智慧一张图”APP等多种展现形式，可实现在线管理、在岗督查、轨迹、区域、工作统计汇报等，实现智能化监管</h3>
						</div>
					</div>
				</div>
			</div>
			<div class="section six-banner-06-section six-banner">
				<div class="section-img" data-height="562">
					<div class="section-inner">
						<div class="copy">
							<h2>环卫综合调度指挥</h2>
							<h3>“智慧一张图”和GIS地图，全面展示整个环卫工作的全流程，人、岗、区域、车辆等相关运行信息，可直观展示，方便管理人员的综合调度指挥</h3>
						</div>
					</div>
				</div>
			</div>
			<div class="section six-banner-07-section six-banner">
				<div class="section-img" data-height="562">
					<div class="section-inner">
						<div class="copy">
							<h2>环卫工作分析研判</h2>
							<h3>通过系统把环卫工作的数据统一起来，为环卫工作的精准数据分析和辅助决策提供数据支撑，让环卫工作在改进时有了精准的数据支持</h3>
						</div>
					</div>
				</div>
			</div>

			<div class="section six-buttons-section">
				<div class="section-inner">
					<div class="nav">
						<ul>
							<li><i class="features-icons-six-01"></i><h2>环卫规范化</h2></li>
							<li><i class="features-icons-six-02"></i><h2>工作流程可监管</h2></li>
							<li><i class="features-icons-six-03"></i><h2>信息管理</h2></li>
							<li><i class="features-icons-six-04"></i><h2>在岗监督</h2></li>
							<li><i class="features-icons-six-05"></i><h2>智能化监管</h2></li>
							<li><i class="features-icons-six-06"></i><h2>综合调度指挥</h2></li>
							<li><i class="features-icons-six-07"></i><h2>分析研判</h2></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="onepage-section health-01-bg" data-title="功能介绍" data-pagination-style="white">
			<div class="section health-01-section">
				<div class="section-img" data-height="731">
					<div class="section-inner">
						<div class="copy">
							<h2 class="title">智慧一张图</h2>
							<h3 class="subtitle">系统提供“智慧一张图”能够将环卫工作的数据都展现在一张图上，更加直观的看到人员考勤在岗情况，区域划分情况、统计分析等，实现了“可视化、智能化”</h3>
						</div>
					</div>
				</div>
			</div>
			<div class="section health-02-section">
				<div class="section-img" data-height="731">
					<div class="section-inner">
						<div class="copy">
							<h2 class="title">工作流程监管</h2>
							<h3 class="subtitle">通过系统可直观展示环卫工作的全流程数据，管理层能够方便的进行工作流程的监管，切实提升环卫工作的整体工作质量和水平。</h3>
						</div>
					</div>
				</div>
			</div>
			<div class="section health-03-section">
				<div class="section-img" data-height="731">
					<div class="section-inner">
						<div class="copy">
							<h2 class="title">数据支撑</h2>
							<h3 class="subtitle">能够集中展示系统运行数据的统计分析，让管理者更加直观的掌握环卫工作全流程信息，更加方便的进行综合指挥调度</h3>
						</div>
					</div>
				</div>
			</div>

			<div class="section health-buttons-section">
				<div class="section-inner">
					<div class="nav">
						<ul>
							<li><i class="features-icons-health-01 hover"></i></li>
							<li><i class="features-icons-health-02"></i></li>
							<li><i class="features-icons-health-03"></i></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<div class="onepage-section" data-title="客户价值">
			<div class="section phone-section">
				<div class="section-img">
					<div class="section-inner">
						<div class="copy">
							<h2 class="title" style="margin-bottom: 50px">系统为客户提升价值</h2>
							<div class="car-det yty-list aui-media-list card-bg yty-margin-b-10">
						        <div class="yty-list-item yty-list-item-middle">
						            <div class="aui-media-list-item-inner">
						                <div class="yty-list-item-media cad-bg-icon" >
						                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/3-xinxihua.png" class="yty-list-img-sm">
						                </div>
						                <div class="yty-list-item-inner yty-padded-l-10">
						                    <div class="yty-list-item-text">
						                        <div class="yty-list-item-title aui-font-size-16">环卫工作信息化</div>
						                    </div>
						                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
						                        把环卫工作全流程进行数据化管理，实现信息统计<br>分析，作业情况直观可见
						                    </div>
						                </div>
						            </div>
						        </div>
           					</div>
           					<div class="car-det yty-list aui-media-list card-bg yty-margin-b-10">
						        <div class="yty-list-item yty-list-item-middle">
						            <div class="aui-media-list-item-inner">
						                <div class="yty-list-item-media cad-bg-icon" >
						                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/3-jilu.png" class="yty-list-img-sm">
						                </div>
						                <div class="yty-list-item-inner yty-padded-l-10">
						                    <div class="yty-list-item-text">
						                        <div class="yty-list-item-title aui-font-size-16">作业监管透明化</div>
						                    </div>
						                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
						                        实时监控环卫工作状态，了解作业历史，作业量，<br>人岗情况
						                    </div>
						                </div>
						            </div>
						        </div>
           					</div>
           					<div class="car-det yty-list aui-media-list card-bg yty-margin-b-10">
						        <div class="yty-list-item yty-list-item-middle">
						            <div class="aui-media-list-item-inner">
						                <div class="yty-list-item-media cad-bg-icon" >
						                    <img src="<?php echo C('HOME_IMAGE_PATH');?>/3-fenxi.png" class="yty-list-img-sm">
						                </div>
						                <div class="yty-list-item-inner yty-padded-l-10">
						                    <div class="yty-list-item-text">
						                        <div class="yty-list-item-title aui-font-size-16">分析辅助决策数据化</div>
						                    </div>
						                    <div class="yty-list-item-text yty-margin-t-5 aui-font-size-14">
						                        通过系统的数据分析为决策和工作优化提高，提供<br>了数据支撑
						                    </div>
						                </div>
						            </div>
						        </div>
           					</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<div class="onepage-section" data-title="后台展示">
			<div class="onepage-charge">
				<div class="section charge-01-section">
					<div class="section-img" data-height="567">
						<div class="section-inner">
							<div class="copy">
								<h2 class="title">设备管理</h2>
								<h3 class="subtitle">添加设备,设备批量管理,设备维护,设备换绑或解除区域,删除设备</h3>
								<img src="<?php echo C('HOME_IMAGE_PATH');?>/charge-01.png" style="margin-top: 50px">
							</div>
						</div>
					</div>
				</div>
				<div class="section charge-01-section">
					<div class="section-img" data-height="567">
						<div class="section-inner">
							<div class="copy">
								<h2 class="title">班组管理</h2>
								<h3 class="subtitle">添加与修改上班时间段,添加班组,编辑班组,删除班组</h3>
								<img src="<?php echo C('HOME_IMAGE_PATH');?>/charge-02.png" style="margin-top: 50px">
							</div>
						</div>
					</div>
				</div>
				<div class="section charge-01-section">
					<div class="section-img" data-height="567">
						<div class="section-inner">
							<div class="copy">
								<h2 class="title">考勤管理</h2>
								<h3 class="subtitle">考勤时间设置,员工考勤记录,员工考勤查询,员工考勤批量导出,告警管理</h3>
								<img src="<?php echo C('HOME_IMAGE_PATH');?>/charge-03.png" style="margin-top: 50px">
							</div>
						</div>
					</div>
				</div>
				<div class="section charge-01-section">
					<div class="section-img" data-height="567">
						<div class="section-inner">
							<div class="copy">
								<h2 class="title">区域管理</h2>
								<h3 class="subtitle">区域添加,地图区域绘制</h3>
								<img src="<?php echo C('HOME_IMAGE_PATH');?>/charge-04.png" style="margin-top: 50px">
							</div>
						</div>
					</div>
				</div>
				<div class="section charge-01-section">
					<div class="section-img" data-height="567">
						<div class="section-inner">
							<div class="copy">
								<h2 class="title">员工管理</h2>
								<h3 class="subtitle">员工添加,员工信息管理,员工职位管理,员工轨迹查询</h3>
								<img src="<?php echo C('HOME_IMAGE_PATH');?>/charge-05.png" style="margin-top: 50px">
							</div>
						</div>
					</div>
				</div>
				
			</div>
			<div class="section charge-buttons-section">
				<div class="section-inner">
					<div class="nav" data-last_number="01">
						<ul>
							<li data-number="01"><i class="hover"></i></li>
							<li data-number="02"><i></i></li>
							<li data-number="03"><i></i></li>
							<li data-number="04"><i></i></li>
							<li data-number="05"><i></i></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<div class="onepage-section" data-title="APP展示" data-pagination-style="white">
			<div class="section tools-section">
				<div class="section-inner">
					<div class="copy">
						<h2 class="title">APP功能展示</h2>
						<h3 class="subtitle">高配置、全功能，专为环卫保洁作业打造的考勤APP终端</h3>
					</div>
				</div>
			</div>

			<div class="section tools-01-section">
				<div class="section-inner">
					<div class="wrap">
						<div class="left">
							<div class="copy">
								<h2 class="title">实时跟踪</h2>
								<h3 class="subtitle">实施监管员工位置信息</h3>
							</div>
						</div>
						<div class="right">
							<div class="copy">
								<h2 class="title">报警信息</h2>
								<h3 class="subtitle">实时查看报警信息,及时处理问题</h3>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- <div class="section tools-02-section">
				<div class="section-inner">
					<div class="wrap">
						<div class="left">
							<div class="copy">
								<h2 class="title">员工信息</h2>
								<h3 class="subtitle">随时随地查看员工基本信息。</h3>
							</div>
						</div>
						<div class="right">
							<div class="copy">
								<h2 class="title">考勤信息</h2>
								<h3 class="subtitle">员工考勤信息实时查询</h3>
							</div>
						</div>
					</div>
				</div>
			</div> -->
		</div>
	</div>
	
	<!--[inc=footer][begin][1422630015:]-->
	<div class="section footer-section">
		<div class="section-inner">
			<div class="left">
				<a href="#" >http://testdw.ttshunt.com/</a>
			</div>
			<div class="right">
				Copyright &copy; 中国联合网络通信有限公司潍坊分公司. ALL Rights Reserved.
			</div>
		</div>
	</div>
	<!--[inc=footer][end]-->
</body>
</html>