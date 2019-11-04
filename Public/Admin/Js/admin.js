var DMS = {
	// 警告弹窗
	alert: function (text){
	    if ($('.alert-box').length){
	        $('.alert-box').remove();
	    }
	    if ($('.modal-backdrop').length){
	        $('.modal-backdrop').remove();
	    }
	    $('body').append(DM_TEMPLATE("alertBox",text));
	    $('.alert-box').modal({backdrop: 'static', keyboard: false});
	},
	success: function (text,time,callback){
	    if ($('.alert-box').length){
	        $('.alert-box').remove();
	    }
	    if ($('.modal-backdrop').length){
	        $('.modal-backdrop').remove();
	    }
	    $('body').append(DM_TEMPLATE("success",text));
	    $('.alert-box').modal({backdrop: 'static', keyboard: false});
	    if(time || time > 0){
	    	setTimeout(function(){
	    		$(".alert-box").modal('hide');
	    		if (callback){
    				callback();
    			}
	    	}, time)
	    }else{
	    	$('.alert-box .yes').click(function(){
    			if (callback){
    				callback();
    			}
    			$(".alert-box").modal('hide');
    			return false;
    		});
	    }
	},
	error: function (text,time,callback){
	    if ($('.alert-box').length){
	        $('.alert-box').remove();
	    }
	    if ($('.modal-backdrop').length){
	        $('.modal-backdrop').remove();
	    }
	    // $('body').append(Hogan.compile(DM_TEMPLATE.error).render({
	    //     message: text
	    // }));
	    $('body').append(DM_TEMPLATE("error",text));
	    $('.alert-box').modal({backdrop: 'static', keyboard: false});
	    if(time || time > 0){
	    	setTimeout(function(){
	    		$(".alert-box").modal('hide');
	    		if (callback){
    				callback();
    			}
	    	}, time)
	    }else{
	    	$('.alert-box .yes').click(function(){
    			if (callback){
    				callback();
    			}
    			$(".alert-box").modal('hide');
    			return false;
    		});
	    }
	},
	/**
	 *	公共弹窗
	 */
	dialog: function (text,callback){
	    var template = DM_TEMPLATE("confirmBox",text);
	    if (template){
	        if ($('.alert-box').length){
	        	$('.alert-box').remove();
	        }
	        if ($('.modal-backdrop').length){
		        $('.modal-backdrop').remove();
		    }
	        $("body").append(template).show();
	        $('.confirm-box .yes').click(function(){
    			if (callback){
    				callback();
    			}
    			$(".alert-box").modal('hide');
    			return false;
    		});
	        $('.alert-box').modal({backdrop: 'static', keyboard: false});
	    }
	},
	// 提示
	orderTip: function (text,callback){
	    var template = DM_TEMPLATE("orderTipBox",text);
	    if (template){
	        if ($('.alert-box').length){
	        	$('.alert-box').remove();
	        }
	        if ($('.modal-backdrop').length){
		        $('.modal-backdrop').remove();
		    }
	        $("body").append(template).show();
	        $('.tip-box .yes').click(function(){
    			if (callback){
    				callback();
    			}
    			$(".alert-box").modal('hide');
    			return false;
    		});
	        $('.alert-box').modal({backdrop: 'static', keyboard: false});
	    }
	},
	// load url请求类
	loadUrl: function (message,url){
	    var template = DM_TEMPLATE("loadUrl",message);
	    if (template){
	        if ($('.alert-box').length){
	        	$('.alert-box').remove();
	        }
	        if ($('.modal-backdrop').length){
		        $('.modal-backdrop').remove();
		    }
	        $("body").append(template).show();
	        $('.confirm-box .yes').click(function(){
    			if (callback){
    				callback();
    			}
    			$(".alert-box").modal('hide');
    			return false;
    		});
	        $('.alert-box').modal({backdrop: 'static', keyboard: false});
	        $("#ajax-box").load(url,function(ret){
	        	var ret = JSON.parse(ret);
	        	if(ret.status != 1){
	        		$("#ajax-box").html(ret.info);
	        	}
	        });
	    }
	},
	// ajax
	ajaxShow: function (message,url){
	    var template = DM_TEMPLATE("ajax",message);
	    if (template){
	        if ($('.alert-box').length){
	        	$('.alert-box').remove();
	        }
	        if ($('.modal-backdrop').length){
		        $('.modal-backdrop').remove();
		    }
	        $("body").append(template).show();
	        $('.confirm-box .yes').click(function(){
    			$(".alert-box").modal('hide');
    			return false;
    		});
	        $('.alert-box').modal({backdrop: 'static', keyboard: false});
        	$("#ajax-box").load(url);
	        // $("#ajax-box").load(url,function(ret){
	        // 	if(ret){
	        // 		var ret = JSON.parse(ret);
		       //  	if(ret.status == 0){
		       //  		$("#ajax-box").text(ret.info);
		       //  	}
	        // 	}
	        // });
	    }
	},
	// ajax post请求
	ajaxPost: function (url,data,callback){
	    $.ajax({
            type : "POST",
            url:url,
            data : data,
            dataType: 'json',
            cache: false,
            success: function(ret) {
                if (callback){
    				callback(ret);
    			}
            },
            error: function(a,b,c){
            	console.log(a.responseText);
            	// DMS.error("操作失败",0);
            }
        });
	},
	// 公用隐藏方法
	hide: function (){
	    if ($('.alert-box').length){
	    	$('.alert-box').remove();
	    }
	    if ($('.modal-backdrop').length){
	        $('.modal-backdrop').remove();
	    }
	},
	// 重新加载
	reload:function(url){
    	if(url){
    		$("#container").load(url);
    	}else{
    		$("#container").load($(".submenu li > a.selected").attr("href"));
    	}
	},
	// 打开页面
	openPage:function(url){
		if(!url) return false;
		if(!$(".loading").length){
			var html = '<div class="loading"><i class="fa fa-spinner fa-spin m-right-xs"></i></div>';
			$("body").append(html);
		}
		$("#container").load(url,function(){
			if($(".loading").length){
				$(".loading").remove();
			}
		});
	},
	// loading
	loading: function (text){
		if(!text){ text = "请稍候"};
	    if ($('.alert-box').length){
	        $('.alert-box').remove();
	    }
	    if ($('.modal-backdrop').length){
	        $('.modal-backdrop').remove();
	    }
	    $('body').append(DM_TEMPLATE("loadingBox",text));
	    $('.alert-box').modal({backdrop: 'static', keyboard: false});
	}

}
