<?php if (!defined('THINK_PATH')) exit();?><form class="form-horizontal no-margin" method="post" id="form">
	<input type="hidden" name="id" value="<?php echo ($id); ?>">
	<div class="form-group">
		<label class="control-label col-md-3"></label>
		<div class="col-md-5">
			<select class="form-control m-top-md" name="info[parent_id]">
				<option value="0">作为管理人员</option>
				<?php if(is_array($parentData)): foreach($parentData as $key=>$v): ?><option value="<?php echo ($v['id']); ?>" <?php if($v['id']==$parent_id){ ?>selected=""<?php } ?>><?php echo ($v['name']); ?></option><?php endforeach; endif; ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-md-3">职位名称</label>
		<div class="col-md-5">
			<input type="text" class="form-control input-sm" name="info[name]" value="<?php echo ($name); ?>">
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
		DMS.ajaxPost("/WFGarden/manager.php?s=/Member/menuEdit",$('#form').serialize(),function(ret){
			if(ret.status==1){
				DMS.success("提交成功",0,function(){
					if(ret.url){
						window.location.href = ret.url;
					}else{
						window.location.href = window.location.href;
					}
				});
			}else{
				DMS.error("提交失败",0);
			}
		})
	}
</script>