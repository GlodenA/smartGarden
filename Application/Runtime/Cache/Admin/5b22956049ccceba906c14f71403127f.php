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

<link href="<?php echo C('ADMIN_JS_PATH');?>/layui/css/layui.css" rel="stylesheet">
<div class="padding-md">
    <!-- 面包屑导航 -->
    <div>
        <ul class="breadcrumb">
            当前位置：
            <li><a href="/WFGarden/manager.php?s=/Index/main">主页</a></li>
            <li><a href="/WFGarden/manager.php?s=/WarningMessage/warningMessageList">告警管理</a></li>
        </ul>
    </div>
    <div class="smart-widget" style="margin-bottom: 1px;">
        <div class="smart-widget-inner">
            <div class="smart-widget-body">
                <div class="header-text">
                    告警管理
                </div>
                <div class="info-line clearfix">
                    <form class="navtop-form form-inline pull-left" id="search-form" action="/WFGarden/manager.php?s=/WarningMessage/warningMessageList" method="post">
                        <div class="form-group">
                            <label for="keywords">关键字：</label>
                            <input type="text" class="form-control time-input" id="keywords" name="keywords" value="<?php echo ($keywords); ?>" placeholder="根据员工号或者姓名查询">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control time-input" name="start_time" id="start_time" value="<?php echo ($start_time); ?>" placeholder="起始时间">
                        </div>
                        <div class="form-group">
                            <label for="end_time">～</label>
                            <input type="text" class="form-control time-input" name="end_time" id="end_time" value="<?php echo ($end_time); ?>" placeholder="结束时间">
                        </div>
                        <div class="form-group">
                            <label for="position">职位</label>
                            <select class="form-control searchbody time-input" name="position" id="position" onchange="changePosition(this)">
                                <option value="0">--全部--</option>
                                <?php if(is_array($positionList)): $i = 0; $__LIST__ = $positionList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pl): $mod = ($i % 2 );++$i;?><option value="<?php echo ($pl["id"]); ?>"<?php if($position == $pl['id']): ?>selected<?php endif; ?>><?php echo ($pl["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="form-group" id="member_manager">
                            <label for="parent_id">管理人员</label>
                            <select class="form-control searchbody time-input" name="parent_id" id="parent_id">
                                <option value="0">--全部--</option>
                                <?php if(is_array($managerList)): $i = 0; $__LIST__ = $managerList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sl): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sl["userid"]); ?>"<?php if($parent_id == $sl['userid']): ?>selected<?php endif; ?>><?php echo ($sl["realname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">报警类型</label>
                            <select class="form-control searchbody time-input" name="type" id="type">
                                <option value="0">--全部--</option>
                                <option value="2"<?php if($type == 2): ?>selected<?php endif; ?>>远离区域报警</option>
                                <option value="3"<?php if($type == 3): ?>selected<?php endif; ?>>低电量报警</option>
                                <option value="4"<?php if($type == 4): ?>selected<?php endif; ?>>迟到报警</option>
                                <option value="5"<?php if($type == 5): ?>selected<?php endif; ?>>旷工报警</option>
                                <option value="6"<?php if($type == 6): ?>selected<?php endif; ?>>早退报警</option>
                                <option value="7"<?php if($type == 7): ?>selected<?php endif; ?>>静止报警</option>
                            </select>
                        </div>
                        <a id="search" url="/WFGarden/manager.php?s=/WarningMessage/warningMessageList" class="btn btn-sm btn-info">
                            查询
                        </a>
                    </form>
                </div>
                <div class="smart-widget-inner">
                    <table class="table table-striped table-bordered" id="dataTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>设备名称</th>
                            <th>设备号</th>
                            <th>绑定员工姓名</th>
                            <th>工号</th>
                            <th>职位</th>
                            <th>管理人员</th>
                            <th>告警时间</th>
                            <th>告警类型</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($warningMessageList)): foreach($warningMessageList as $k=>$v): ?><tr id="data-<?php echo ($v['id']); ?>">
                                <td><?php echo ($k +1 + $number); ?></td>
                                <td><?php echo ($v['machine_name']); ?></td>
                                <td><?php echo ($v['machine_imei']); ?></td>
                                <td><a href="javascript:userInfo('<?php echo ($v["uid"]); ?>');"><?php echo ($v['realname']); ?></a></td>
                                <td><?php echo ($v['job_number']); ?></td>
                                <td><?php echo (getPositionName($v['position'])); ?></td>
                                <td><?php echo (getMemberManager($v['parent_id'])); ?></td>
                                <td>
                                    <?php echo (date("Y-m-d H:i:s",$v['add_time'])); ?>
                                </td>
                                <td>
                                    <?php if($v['type'] == 1 ): ?>远离工作区域
                                        <?php elseif($v['type'] == 2): ?>
                                        <a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">远离区域报警</a>
                                        <?php elseif($v['type'] == 3): ?>
                                        低电量报警
                                        <?php elseif($v['type'] == 4): ?>
                                        <a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">迟到报警</a>
                                        <?php elseif($v['type'] == 5): ?>
                                        <a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">旷工报警</a>
                                        <?php elseif($v['type'] == 6): ?>
                                        <a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">早退报警</a>
                                        <?php elseif($v['type'] == 7): ?>
                                        <a href="/WFGarden/manager.php?s=/Machine/machineOrbit/machine_id/<?php echo ($v['machine_id']); ?>/searchTime/<?php echo (date('Y-m-d',$v['add_time'])); ?>">静止报警</a><?php endif; ?>
                                </td>
                            </tr><?php endforeach; endif; ?>
                        </tbody>
                    </table>
                    <div class="content text-right">
                        <ul class="pagination">
                            <?php echo ($page); ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="<?php echo C('ADMIN_JS_PATH');?>/layui/layui.js"></script>
<script src="<?php echo C('ADMIN_JS_PATH');?>/time.js"></script>
<script type="text/javascript">
    $(function() {
        //搜索功能
        $("#search").click(function () {
            var url = $(this).attr('url');
            var query = $('#search-form').find('.time-input').serialize();

            if (url.indexOf('?') > 0) {
                url += '&' + query;
            } else {
                url += '?' + query;
            }
            window.location.href = url;
        });
    })
    $(document).ready(function(){
        layui.use('laydate', function(){
            var laydate = layui.laydate;
            var start = laydate.render({
                elem: '#start_time',
                type: 'date',
                theme: 'grid',
                festival: true, //显示节日
                calendar: true,
                max:0,
                done: function(value, date){
                    endMax = end.config.max;
                    end.config.min = date;
                    end.config.min.month = date.month -1;
                    end.config.min.date = date.date;
                }
            });

            var end = laydate.render({
                elem: '#end_time',
                type: 'date',
                theme: 'grid',
                festival: true, //显示节日
                calendar: true,
                max:0,
                done: function(value, date){
                    start.config.max = date;
                    start.config.max.month = date.month -1;
                    start.config.max.date = date.date;
                }
            });
        });
    });

    function leaveInfo(userid,first_time,second_time){
        layer.open({
            type: 2,
            title:"离岗详情",
            shadeClose: true,
            shade: 0.2,
            scrollbar: false,
            area: ['800px', '600px'],
            content: "/WFGarden/manager.php?s=/WarningMessage/leaveInfo/userid/"+userid+"/first_time/"+first_time+"/second_time/"+second_time
        });
    }
    function userInfo(userid){
        layer.open({
            type: 2,
            title:"员工详情",
            shadeClose: true,
            shade: 0.2,
            scrollbar: false,
            area: ['800px', '400px'],
            content: "/WFGarden/manager.php?s=/Member/memberInfo/userid/"+userid
        });
    }
    function changePosition(obj){
        var option = obj.value;
        var item = document.getElementById("member_manager");
        if(option>0){
            var url = '/WFGarden/manager.php?s=/Member/getMemberManager';
            $.post(url, {'parent_id':option}, function(ret){
                if(ret.status == 1){
//                    item.style.display = "inline-block";
                    item.querySelector("select").innerHTML = "";
                    item.querySelector("select").innerHTML= '<option value="">--全部--</option>';
                    for(i in ret.data){
                        item.querySelector("select").innerHTML += '<option value='+ret.data[i].userid+'<?php if($parent_id == '+ret.data[i].userid+'): ?>selected<?php endif; ?>>'+ret.data[i].realname+'</option>';
                    }
                } else{
//                    item.style.display = "none";
                    item.querySelector("select").innerHTML = "";
                    item.querySelector("select").innerHTML= '<option value="">--全部--</option>';
                }
            });
        }
//        else{
//            item.style.display = "none";
//        }
    }
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