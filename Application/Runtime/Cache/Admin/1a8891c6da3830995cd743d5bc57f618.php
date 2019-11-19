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
      <!-- Vue, element, 间距工具类 相关 -->
      <link rel="stylesheet" href="/smartGarden/Public/Admin/Css//util/flex.css">
      <link href="https://unpkg.com/basscss@8.0.2/css/basscss.min.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/npm/vue@2.6.0"></script>
      <link rel="stylesheet" href="/smartGarden/Public/Admin/element/index.css">
      <script src="https://unpkg.com/element-ui/lib/index.js"></script>
      <!-- Vue, element, 间距工具类 相关 -->
    </head>
    <body class="overflow-hidden">

<div class="padding-md" id="GROUPLIST">
	<div class="smart-widget widget-dark-blue">
		<div class="smart-widget-inner">
			<div class="smart-widget-body">
				<el-breadcrumb separator="/">
          <el-breadcrumb-item>
            <a href="">首页</a>
          </el-breadcrumb-item>
          <el-breadcrumb-item>
            员工管理
          </el-breadcrumb-item>
          <el-breadcrumb-item>
            员工职位
          </el-breadcrumb-item>
        </el-breadcrumb>
        <el-divider></el-divider>
				<el-table :data="tableData">
					<el-table-column prop="id" label="ID"></el-table-column>
					<el-table-column prop="name" label="名称"></el-table-column>
					<el-table-column label="操作">

					</el-table-column>
				</el-table>
			</div>
		</div>
	</div>
</div>
<script>
	const tableData = []
</script>
<?php if(is_array($menuList)): foreach($menuList as $key=>$v): ?><script type="text/javascript">
		tableData.push({
			id: "<?php echo ($v['id']); ?>",
			name: "<?php echo ($v['name']); ?>",
		})
	</script>
	<?php if(is_array($v['children'])): foreach($v['children'] as $key=>$r): ?><script type="text/javascript">
			tableData.push({
				id: "<?php echo ($r['id']); ?>",
				name: "<?php echo ($r['name']); ?>",
				isChild: true
			})
		</script><?php endforeach; endif; endforeach; endif; ?>
<script type="text/javascript">
	const GROUPLIST = new Vue({
		el: '#GROUPLIST',
		data(){
			return {
				tableData
			}
		},
		mounted(){
			console.log(this.tableData);
		}
	})
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