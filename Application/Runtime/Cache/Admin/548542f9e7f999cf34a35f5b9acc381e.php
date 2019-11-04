<?php if (!defined('THINK_PATH')) exit();?><form class="form-horizontal no-margin" method="post" id="form">
	<input type="hidden" name="id" value="<?php echo ($id); ?>">
	<div class="form-group">
		<label class="control-label col-md-3">管理组名称</label>
		<div class="col-md-5">
			<input type="text" class="form-control input-sm" name="info[title]" value="<?php echo ($title); ?>" required>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3">描述</label>
		<div class="col-md-5">
			<input type="text" class="form-control input-sm" name="info[description]" value="<?php echo ($description); ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3"></label>
		<div class="col-md-5">
			<div class="radio inline-block">
				<div class="custom-radio m-right-xs">
					<input type="radio" id="display1" name="info[status]" value="1" <?php if($status == 1): ?>checked<?php endif; ?>>
					<label for="display1"></label>
				</div>
				<div class="inline-block vertical-top">正常</div>
				<div class="custom-radio m-right-xs">
					<input type="radio" id="display2" name="info[status]" value="0" <?php if($status == 0): ?>checked<?php endif; ?>>
					<label for="display2"></label>
				</div>
				<div class="inline-block vertical-top">禁止</div>
			</div>
		</div>
	</div>
	<div class="text-center m-top-md">
		<button type="submit" class="btn btn-info">提交</button>
	</div>
</form>
<script src="<?php echo C('ADMIN_JS_PATH');?>/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(function(){
        $("#form").validate({
	        submitHandler:function(form){
	            confirm();
	        }
	    });
    })
	function confirm() {
		DMS.ajaxPost("/WFGarden/manager.php?s=/Group/groupEdit",$('#form').serialize(),function(ret){
			if(ret.status==1){
            	DMS.success("提交成功",0,function(){
					if(ret.url){
            			window.location.href = ret.url;
            		}else{
            			window.location.href = window.location.href;
            		}
				});
            }else{
            	DMS.error(''+ret.info+'',0);
            }
		})
    }
</script>