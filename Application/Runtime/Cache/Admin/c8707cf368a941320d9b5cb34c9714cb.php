<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo C('WEB_NAME');?>-后台管理</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon" href="/favicon.ico" />
    	<link rel="bookmark" href="/favicon.ico" />
	    <link href="<?php echo C('ADMIN_CSS_PATH');?>/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo C('ADMIN_CSS_PATH');?>/font-awesome.min.css" rel="stylesheet">
		<!-- <link href="<?php echo C('ADMIN_CSS_PATH');?>/public.css" rel="stylesheet"> -->
		<link href="<?php echo C('ADMIN_CSS_PATH');?>/common.css" rel="stylesheet">
		<script src="<?php echo C('ADMIN_JS_PATH');?>/jquery-1.11.1.min.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/bootstrap.min.js" type="text/javascript"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/common.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/jquery.slimscroll.min.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/admin_template.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/admin.js"></script>
	    <script src="<?php echo C('ADMIN_JS_PATH');?>/layer/layer.js"></script>

    </head>
    <body class="overflow-hidden">

<style>
    img{
        max-width: inherit;
    }
</style>
<div class="padding-md">
    <div>
        <ul class="breadcrumb">
            当前位置：
            <li><a href="/WFGarden/manager.php?s=/Index/main"> 主页</a></li>
            <li><a href="/WFGarden/manager.php?s=/Machine/machineList">设备管理</a></li>
            <li><a href="#">设备位置</a></li>
        </ul>
    </div>
    <div class="smart-widget widget-dark-blue">
        <div class="smart-widget-inner">
            <div class="smart-widget-body">
                <form class="form-inline no-margin form-border" action="/WFGarden/manager.php?s=/Machine/machineMap" method="post">
                    <!--<div class="form-group" style="padding-bottom: 0">-->
                        <!--<input type="hidden" class="form-control" name="machine_imei" value="<?php echo ($searchInfo["machine_imei"]); ?>" id="imei">-->
                        <!--<input type="date" class="form-control" name="start_date" value="<?php echo ($searchInfo["start_date"]); ?>" placeholder="开始时间"> - -->
                        <!--<input type="date" class="form-control " name="end_date" value="<?php echo ($searchInfo["end_date"]); ?>" placeholder="截止时间">-->
                    <!--</div>-->
                    <!--<button type="button" class="btn btn-success marginTB-xs margin-r-10" onclick="fnSearch()">搜索</button>-->
                    <button type="button" onclick="window.location.reload();" class="btn btn-primary marginTB-xs"><i class="fa fa-refresh m-right-xs"></i>刷新</button>
                    <a href="javascript:history.back(-1)" class="btn btn-sm btn-info">返回上一页</a>
                    <div class="form-group" style="float:right;">设备IMEI：<?php echo ($machine_imei); if($memberInfo): ?>&nbsp;&nbsp;员工：<?php echo ($memberInfo["realname"]); endif; ?></div>
                </form>
            </div>
        </div>
        <div class="smart-widget-inner">
            <div class="smart-widget-body" id="map" style="padding: 15px;">

            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=3.0&ak=NENpvHSwTNZ6ftZOKdfiiPDxGKKPHjtg"></script>
<script type="text/javascript">
    var clientHeight = document.body.clientHeight;
    document.getElementById("map").style.height = clientHeight-150+'px';
    var imei = '<?php echo ($machine_imei); ?>';
    var machineId = '<?php echo ($machine_id); ?>';
    var parent_name = '<?php echo ((isset($parent_name) && ($parent_name !== ""))?($parent_name):暂无); ?>';
    var position_name = '<?php echo ((isset($position_name) && ($position_name !== ""))?($position_name):暂无); ?>';
    var realname = '<?php echo ((isset($realname) && ($realname !== ""))?($realname):暂无); ?>';
    var job_number = '<?php echo ((isset($job_number) && ($job_number !== ""))?($job_number):暂无); ?>';
    var mobile = '<?php echo ((isset($mobile) && ($mobile !== ""))?($mobile):暂无); ?>';
    var lat =  "<?php echo ($lat); ?>";
    var lon = "<?php echo ($lon); ?>";
    var add_time = "<?php echo ($add_time); ?>";
    var rank = "<?php echo C('CITY_RANK');?>";
    var cityName = "<?php echo C('CITY_NAME');?>";
    var map = new BMap.Map('map');

    $(function(){
        map.centerAndZoom(new BMap.Point("<?php echo C('CITY_CENTER_LON');?>","<?php echo C('CITY_CENTER_LAT');?>"),rank);                    // 初始化地图,设置中心点坐标和地图级别。
        map.addControl(new BMap.ScaleControl());                    // 添加比例尺控件
        map.addControl(new BMap.OverviewMapControl());              //添加缩略地图控件
        map.setCenter(cityName);     // 设置地图显示的城市 此项是必须设置的
        map.enableScrollWheelZoom();
        map.addControl(new BMap.NavigationControl({ type: BMAP_NAVIGATION_CONTROL_LARGE ,anchor: BMAP_ANCHOR_TOP_LEFT, offset: new BMap.Size(40, 250)}));
        var bdary = new BMap.Boundary();
        bdary.get(cityName, function (rs) {       //获取行政区域
            map.clearOverlays();        //清除地图覆盖物
            //添加遮罩层
            //思路：利用行政区划点的集合与外围自定义东南西北形成一个环形遮罩层
            //1.获取选中行政区划边框点的集合  rs.boundaries[0]
            var strs = new Array();
            strs = rs.boundaries[0].split(";");
            var EN = "";    //行政区划东北段点的集合
            var NW = ""; //行政区划西北段点的集合
            var WS = ""; //行政区划西南段点的集合
            var SE = ""; //行政区划东南段点的集合
            var pt_e = strs[0]; //行政区划最东边点的经纬度
            var pt_n = strs[0]; //行政区划最北边点的经纬度
            var pt_w = strs[0]; //行政区划最西边点的经纬度
            var pt_s = strs[0]; //行政区划最南边点的经纬度
            var n1 = ""; //行政区划最东边点在点集合中的索引位置
            var n2 = ""; //行政区划最北边点在点集合中的索引位置
            var n3 = ""; //行政区划最西边点在点集合中的索引位置
            var n4 = ""; //行政区划最南边点在点集合中的索引位置

            //2.循环行政区划边框点集合找出最东南西北四个点的经纬度以及索引位置
            for (var n = 0; n < strs.length; n++) {
                var pt_e_f = parseFloat(pt_e.split(",")[0]);
                var pt_n_f = parseFloat(pt_n.split(",")[1]);
                var pt_w_f = parseFloat(pt_w.split(",")[0]);
                var pt_s_f = parseFloat(pt_s.split(",")[1]);

                var sPt = new Array();
                try {
                    sPt = strs[n].split(",");
                    var spt_j = parseFloat(sPt[0]);
                    var spt_w = parseFloat(sPt[1]);
                    if (pt_e_f < spt_j) {   //东
                        pt_e = strs[n];
                        pt_e_f = spt_j;
                        n1 = n;
                    }
                    if (pt_n_f < spt_w) {  //北
                        pt_n_f = spt_w;
                        pt_n = strs[n];
                        n2 = n;
                    }

                    if (pt_w_f > spt_j) {   //西
                        pt_w_f = spt_j;
                        pt_w = strs[n];
                        n3 = n;
                    }
                    if (pt_s_f > spt_w) {   //南
                        pt_s_f = spt_w;
                        pt_s = strs[n];
                        n4 = n;
                    }
                }
                catch (err) {
                    alert(err);
                }
            }
            //3.得出东北、西北、西南、东南四段行政区划的边框点的集合
            if (n1 < n2) {     //第一种情况 最东边点在索引前面
                for (var o = n1; o <= n2; o++) {
                    EN += strs[o] + ";"
                }
                for (var o = n2; o <= n3; o++) {
                    NW += strs[o] + ";"
                }
                for (var o = n3; o <= n4; o++) {
                    WS += strs[o] + ";"
                }
                for (var o = n4; o < strs.length; o++) {
                    SE += strs[o] + ";"
                }
                for (var o = 0; o <= n1; o++) {
                    SE += strs[o] + ";"
                }
            } else {   //第二种情况 最东边点在索引后面
                for (var o = n1; o < strs.length; o++) {
                    EN += strs[o] + ";"
                }
                for (var o = 0; o <= n2; o++) {
                    EN += strs[o] + ";"
                }
                for (var o = n2; o <= n3; o++) {
                    NW += strs[o] + ";"
                }
                for (var o = n3; o <= n4; o++) {
                    WS += strs[o] + ";"
                }
                for (var o = n4; o <= n1; o++) {
                    SE += strs[o] + ";"
                }
            }

            //4.自定义外围边框点的集合
            var EN_JW = "180, 90;";         //东北角
            var NW_JW = "-180,  90;";       //西北角
            var WS_JW = "-180, -90;";       //西南角
            var SE_JW = "180, -90;";        //东南角
            //4.添加环形遮罩层
            var ply1 = new BMap.Polygon(rs.boundaries[0] + SE_JW + SE_JW + WS_JW + NW_JW + EN_JW + SE_JW,{ strokeColor: "none", fillColor: "rgb(246,246,246)", strokeOpacity: 0 }); //建立多边形覆盖物
            map.addOverlay(ply1);  //遮罩物是半透明的，如果需要纯色可以多添加几层
            var ply2 = new BMap.Polygon(rs.boundaries[0] + SE_JW + SE_JW + WS_JW + NW_JW + EN_JW + SE_JW,{ strokeColor: "none", fillColor: "rgb(246,246,246)", strokeOpacity: 0 }); //建立多边形覆盖物
            map.addOverlay(ply2);  //遮罩物是半透明的，如果需要纯色可以多添加几层
//            var ply3 = new BMap.Polygon(rs.boundaries[0] + SE_JW + SE_JW + WS_JW + NW_JW + EN_JW + SE_JW,{ strokeColor: "none", fillColor: "rgb(246,246,246)", strokeOpacity: 0 }); //建立多边形覆盖物
//            map.addOverlay(ply3);  //遮罩物是半透明的，如果需要纯色可以多添加几层
//            var ply4 = new BMap.Polygon(rs.boundaries[0] + SE_JW + SE_JW + WS_JW + NW_JW + EN_JW + SE_JW,{ strokeColor: "none", fillColor: "rgb(246,246,246)", strokeOpacity: 0 }); //建立多边形覆盖物
//            map.addOverlay(ply4);  //遮罩物是半透明的，如果需要纯色可以多添加几层
            _fnInitMap();
        });
    });
    var opts = {
        width : 250,     // 信息窗口宽度
        height: 200,     // 信息窗口高度
        title : "" , // 信息窗口标题
        enableMessage:true//设置允许信息窗发送短息
    };

    function _fnInitMap() {
        if(imei){
            var point = new BMap.Point(lon,lat);
            map.centerAndZoom(point, rank);
            // 创建标注
            var marker = new BMap.Marker(point);  // 创建标注
            map.addOverlay(marker);
            var content = '设备imei:'+imei+'<br/>员工姓名:'+realname+'<br/>员工手机号:'+mobile+'<br/>工号:'+job_number+'<br/>职位:'+position_name+'<br/>管理人员:'+parent_name+'<br/>采集时间:'+add_time+'<br/><a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/'+machineId+'" class="home-nav-title">查看轨迹</a>';
            addClickHandler(content,marker);
        }else{
            map.centerAndZoom(new BMap.Point("<?php echo C('CITY_CENTER_LON');?>","<?php echo C('CITY_CENTER_LAT');?>"),rank);
        }
    }

    function getLocalTime(nS) {
        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
    }
    function addClickHandler(content,marker){
        marker.addEventListener("click",function(e){
            openInfo(content,e)}
        );
    }
    function openInfo(content,e){
        var p = e.target;
        var point = new BMap.Point(p.getPosition().lng, p.getPosition().lat);
        var infoWindow = new BMap.InfoWindow(content,opts);  // 创建信息窗口对象
        map.openInfoWindow(infoWindow,point); //开启信息窗口
    }



    function fnSearch(){
        var url = '/WFGarden/manager.php?s=/Machine/machineMap';
        var query  = $('form').find('.form-control').serialize();
        if( url.indexOf('?')>0 ){
            url += '&' + query;
        }else{
            url += '?' + query;
        }
        window.location.href = url;
    };

</script>

            <!-- <footer class="footer">
                <img src="<?php echo C('ADMIN_IMAGE_PATH');?>/logo_common.png" />
                <p class="no-margin">
                    &copy; 2016 <strong>北京点萌科技有限公司</strong>. ALL Rights Reserved. <a href="http://www.dianm.cc" target="_blank">www.dianm.cc</a>
                </p>
            </footer> -->
            <!-- <a href="#" class="scroll-to-top hidden-print"><i class="fa fa-chevron-up fa-lg"></i></a> -->
            <!-- <a href="javascript:history.go(-1)" class="history-back" data-toggle="tooltip" data-placement="left" title="返回上一页"><i class="fa fa-chevron-left fa-lg"></i></a> -->
    </body>
</html>