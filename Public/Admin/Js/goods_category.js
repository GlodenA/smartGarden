$(document).ready(function(){
	$("a.cates").click(function(){
		var parentId = $(this).attr("data-id");
		var status = $(this).attr("data-status");
		if(status==0){
			$.get("getGoodsCategoryChildLists/pid/"+parentId, function(ret){
				if(ret){
                	var html = '';
                	for (var i = 0; i < ret.length; i++) {
                		html += '<tr id="data-'+ret[i].id+'">';
                			html += '<td>'+ret[i].sort+'</td>';
							html += '<td>'+ret[i].id+'</td>';
							if(ret[i].child_ids){
								html += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;<a href="javascript:;" class="cates" data-status="0" data-id="'+ret[i].id+'">[+]</a>'+ret[i].cate_name+'</td>';
							}else{
								html += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└&nbsp;&nbsp;'+ret[i].cate_name+'</td>';
							}

							html += '<td>';
							if(ret[i].status == 1){
								html += '<span class="label label-success">是</span>';
							}else{
								html += '<span class="label label-danger">否</span>';
							}
							html += '</td>';
							html += '<td>';
								html += '<a href="__CONTROLLER__/goodsCategoryAdd/pid/'+ret[i].id+'" class="btn btn-manager btn-warning btn-xs" data-toggle="tooltip" data-placement="top" title="添加子分类"><i class="fa fa-plus"></i></a>&nbsp;';
								html += '<a href="javascript:categoryEdit('+ret[i].id+')" class="btn btn-manager btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="修改"><i class="fa fa-pencil"></i></a>&nbsp;';
								html += '<a href="javascript:menuDelete('+ret[i].id+')" class="btn btn-manager btn-danger btn-xs" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-trash-o"></i></a>';
							html += '</td>';
                		html += '</tr>';

                	}
                	$("#data-"+parentId).after(html);
                	$('a[data-id="'+parentId+'"]').attr("data-status",1);
                	// catesClick();
                	 $.getScript("goods_categofy.js");
                }
			});
		}else{
			$('a[data-id="'+parentId+'"]').attr("data-status",0);
		}
	})
});