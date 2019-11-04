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
            <li><a href="/WFGarden/manager.php?s=/Attendance/attendanceList">考勤管理</a></li>
        </ul>
    </div>
    <div class="smart-widget" style="margin-bottom: 1px;">
        <div class="smart-widget-inner">
            <div class="smart-widget-body">
                <div class="header-text">
                    考勤管理
                </div>
                <div class="info-line clearfix">
                    <form class="navtop-form form-inline pull-left" id="search-form" action="/WFGarden/manager.php?s=/Attendance/attendanceList" method="post">
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
                            <label for="status">考勤状态</label>
                            <select class="form-control searchbody time-input" name="status" id="status">
                                <option value="">--全部--</option>
                                <option value="1" <?php if($status == 1): ?>selected<?php endif; ?> >上午正常上班</option>
                                <option value="2" <?php if($status == 2): ?>selected<?php endif; ?> >上午迟到</option>
                                <option value="3" <?php if($status == 3): ?>selected<?php endif; ?> >上午旷工</option>
                                <option value="5" <?php if($status == 5): ?>selected<?php endif; ?> >上午正常下班</option>
                                <option value="4" <?php if($status == 4): ?>selected<?php endif; ?> >上午早退</option>
                                <option value="12" <?php if($status == 12): ?>selected<?php endif; ?> >上午异常</option>
                                <option value="6" <?php if($status == 6): ?>selected<?php endif; ?> >下午正常上班</option>
                                <option value="7" <?php if($status == 7): ?>selected<?php endif; ?> >下午迟到</option>
                                <option value="8" <?php if($status == 8): ?>selected<?php endif; ?> >下午旷工</option>
                                <option value="10" <?php if($status == 10): ?>selected<?php endif; ?> >下午正常下班</option>
                                <option value="9" <?php if($status == 9): ?>selected<?php endif; ?> >下午早退</option>
                                <option value="13" <?php if($status == 13): ?>selected<?php endif; ?> >下午异常</option>
                                <option value="11" <?php if($status == 11): ?>selected<?php endif; ?> >异常</option>
                            </select>
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
                        <a id="search" url="/WFGarden/manager.php?s=/Attendance/attendanceList" class="btn btn-sm btn-info">
                            查询
                        </a>
                        <a url="/WFGarden/manager.php?s=/Attendance/exportAttendanceList" id="export" class="btn btn-sm btn-info">
                            导出Excel
                        </a>
                    </form>
                    <!--<div class="searchTop-form form-inline pull-right" id="form">-->
                        <!--<div class="form-group">-->
                            <!--<input type="text" id="filename" class="form-control" readonly="">-->
                        <!--</div>-->
                        <!--<a href="javascript:adminAdd();" class="btn btn-sm btn-info">-->
                            <!--选择文件-->
                        <!--</a>-->
                    <!--</div>-->
                </div>
                <div class="smart-widget-inner">
                    <table class="table table-striped table-bordered" id="dataTable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>工号</th>
                            <th>姓名</th>
                            <th>职位</th>
                            <th>管理人员</th>
                            <th>考勤日期</th>
                            <th>上午上班打卡时间</th>
                            <th>打卡结果</th>
                            <th>上午下班打卡时间</th>
                            <th>打卡结果</th>
                            <th>下午上班打卡时间</th>
                            <th>打卡结果</th>
                            <th>下午下班打卡时间</th>
                            <th>打卡结果</th>
                            <th>离岗次数</th>
                            <th>备注</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($list)): foreach($list as $k=>$v): ?><tr id="data-<?php echo ($v['id']); ?>">
                                <td><?php echo ($k +1 + $number); ?></td>
                                <td><?php echo ($v['job_number']); ?></td>
                                <td><?php echo ($v['realname']); ?></td>
                                <td><?php echo ($v['position_name']); ?></td>
                                <td>
                                    <?php echo ($v['parent_name']); ?>
                                </td>
                                <td><?php echo (date("Y-m-d",$v['add_time'])); ?></td>
                                <td><?php if($v['first_time']): echo (date("Y-m-d H:i:s",$v['first_time'])); else: ?>暂未打卡<?php endif; ?></td>
                                <td>
                                    <?php switch($v["first_result"]): case "1": ?>正常<?php break;?>
                                        <?php case "3": ?>迟到<?php break;?>
                                        <?php case "5": ?>旷工<?php break;?>
                                        <?php default: ?>暂无结果<?php endswitch;?>
                                </td>
                                <td><?php if($v['second_time']): echo (date("Y-m-d H:i:s",$v['second_time'])); else: ?>暂未打卡<?php endif; ?></td>
                                <td><?php switch($v["second_result"]): case "2": ?>正常<?php break;?>
                                    <?php case "4": ?>早退<?php break;?>
                                    <?php default: ?>暂无结果<?php endswitch;?></td>
                                <td><?php if($v['third_time']): echo (date("Y-m-d H:i:s",$v['third_time'])); else: ?>暂未打卡<?php endif; ?></td>
                                <td>
                                    <?php switch($v["third_result"]): case "1": ?>正常<?php break;?>
                                        <?php case "3": ?>迟到<?php break;?>
                                        <?php case "5": ?>旷工<?php break;?>
                                        <?php default: ?>暂无结果<?php endswitch;?>
                                </td>
                                <td><?php if($v['fourth_time']): echo (date("Y-m-d H:i:s",$v['fourth_time'])); else: ?>暂未打卡<?php endif; ?></td>
                                <td>
                                    <?php switch($v["fourth_result"]): case "2": ?>正常<?php break;?>
                                        <?php case "4": ?>早退<?php break;?>
                                        <?php default: ?>暂无结果<?php endswitch;?>
                                </td>
                                <td class="hidden-xs">
                                    <?php echo ($v['leaveTimeCount']); ?>
                                </td>
                                <td class="hidden-xs" <?php if($v['remark']): ?>onclick="layer.open({title: '备注', content: '<?php echo ($v['remark']); ?>'})"<?php endif; ?>>
                                    <?php echo str_cut($v['remark'],5);?>
                                </td>
                                <td>
                                    <a href="javascript:Attendanceedit('<?php echo ($v["id"]); ?>');"><i class="fa fa-pencil-square-o m-right-xs"></i>编辑</a>
                                    <?php if($v['leaveTimeCount'] > 0): ?>&nbsp;|&nbsp;<a href="javascript:leaveInfo('<?php echo ($v["userid"]); ?>','<?php echo ($v["add_time"]); ?>');" class="text-success"><i class="fa fa-info-circle m-right-xs"></i>离岗详情</a><?php endif; ?>
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
        //导出功能
        $("#export").click(function () {
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
    function Attendanceedit(id){
        DMS.ajaxShow("考勤编辑","/WFGarden/manager.php?s=/Attendance/attendanceRemark/id/"+id);
    }
    function leaveInfo(userid,add_time){
        layer.open({
            type: 2,
            title:"离岗详情",
            shadeClose: true,
            shade: 0.2,
            scrollbar: false,
            area: ['800px', '600px'],
            content: "/WFGarden/manager.php?s=/Attendance/leaveInfo/userid/"+userid+"/add_time/"+add_time
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