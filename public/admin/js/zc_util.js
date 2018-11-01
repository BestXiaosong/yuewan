/**
 * 中产盛世工具包
 * 含 FORM提交插件、提示对话框插件、弹窗插件
 */

//页面跳转
function redirectURL(urlTo){
	window.location.href = urlTo;
}

//bs后台，菜单栏折叠效果
$(function(){
	$(".dropdown-toggle").click(function(){
		var $dropdown = $(this).parent().find(".submenu");
		if($dropdown[0]){	//存在情况下
			if($dropdown.css("display") == 'none'){
				$dropdown.show();
			}else{
				$dropdown.hide();
			}
		}
	});
	
	$("#menu-toggler").click(function(){
		var $this = $(this);
		$this.toggleClass("display");
		$(".sidebar").toggleClass("display");
	});
	
});

//阻止JS冒泡
function stopDefault(e) {
	//如果提供了事件对象，则这是一个非IE浏览器 
	if(e && e.preventDefault) {
	　　//阻止默认浏览器动作(W3C)
	　　e.preventDefault();
	} else {
	　　//IE中阻止函数器默认动作的方式 
	　　window.event.returnValue = false; 
	}
	return false;
}

/**
 * OA审核FORM提交JS截取封装方法
 * @author 王琨
 */
function doSubmitCheckForm(obj,isPass,APP_PATH){
	$("[name='is_pass']").val(isPass);
	doSubmitForm(obj,APP_PATH);
}

/**
 * FORM提交JS截取封装方法
 * @author 王琨
 */
var lock = false;	//锁默认关闭
function doSubmitForm(obj,APP_PATH){
	
	var btn = $(obj);
	var form = btn.closest("form");
	var url_addr = form.attr("action");
	var params = form.serialize();
	
	//加锁判断
	if(lock == true){
		return;
	}
	$.ajax({ url:url_addr, data:params,type:"POST", dataType:"json",beforeSend:function(){
		lock = true;	//加锁
		btn.addClass('submit_loading');
	},success:function(result){
		//解锁---开始------
		lock = false;
		btn.removeClass('submit_loading');
		//解锁---结束------
		if(result.status == 1){	//成功的状态：仅提示
			zcAlert(result.msg, 1);
	    }else if(result.status == 2){	//JS回调函数流程
	    	eval(result.msg);
	    }else if(result.status == 3){	//[当前页]:刷新
	    	window.location.reload();
	    }else if(result.status == 4){	//[当前页]:跳转
	    	if(result.msg == null) { // 无需页面提示信息
				window.location.href = result.data; // 直接跳转
			}else{
				window.location.href = APP_PATH+"/public/redirect/?msg="+result.msg+"&url="+result.data; // 直接跳转
			}
	    }else if(result.status == 5) { // [当前页]：提示+刷新页面
	    	zcRefreshAlert(result.msg, 1);
        }else if(result.status == 6){	//[当前页]：提示+跳转页面
        	zcRedirectAlert(result.msg,result.data, 1);
	    }else if(result.status == 7){	//[父页]：刷新页面
	    	parent.window.location.reload();
	    }else if(result.status == 8){	//[父页]：跳转页面
	    	if(result.msg == null) { // 无需页面提示信息
				parent.location.href = result.data; // 直接跳转
			}else{
				parent.location.href = APP_PATH+"/public/redirect/?msg="+result.msg+"&url="+result.data; // 直接跳转
			}
	    }else if(result.status == 9) { // [父页]：提示+刷新页面
            zcParentRefreshAlert(result.msg,1);
        }else if(result.status == 10){	//[父页]：提示+跳转页面
	    	zcParentRedirectAlert(result.msg,result.data, 1);
	    }else if(result.status == 99) { // 刷新当前页（用于支付宝）
			var str = '<div style="display:block;">' + result.data + '</div>';
			$("body").append(str);
		}else{	//错误的状态：仅提示
	    	zcAlert(result.msg, 0);
	    }
	}});
	
}

//确认弹出窗操作-> 即弹出框，远程请求将要的操作
function toConfirm(tips,todoUrl) {
	
	layer.confirm(tips, {icon: 3}, function(index){
		$.get(todoUrl,function(result){
			layer.close(index);
			if(result.code == 1){	//成功的状态：提示+刷新
				zcAlert(result.msg, 1);
				window.location.reload();
		    }else if(result.code == 2){	//JS回调函数流程
		    	eval(result.msg);
		    }else if(result.code == 3){	//[当前页]:无操作
					
		    }else if(result.code == 4){	//[当前页]:跳转
		    	if(result.msg == null) { // 无需页面提示信息
					window.location.href = result.data; // 直接跳转
				}else{
					window.location.href = "/public/redirect/?msg="+result.msg+"&url="+result.data; // 直接跳转
				}
		    }else if(result.code == 5) { // [当前页]：提示+刷新页面
		    	zcRefreshAlert(result.msg, 1);
	        }else if(result.code == 6){	//[当前页]：提示+跳转页面
	        	zcRedirectAlert(result.msg,result.data, 1);
		    }else if(result.code == 7){	//[父页]：刷新页面
		    	parent.window.location.reload();
		    }else if(result.code == 8){	//[父页]：跳转页面
		    	if(result.msg == null) { // 无需页面提示信息
					parent.location.href = result.data; // 直接跳转
				}else{
					parent.location.href = APP_PATH+"/public/redirect/?msg="+result.msg+"&url="+result.data; // 直接跳转
				}
		    }else if(result.code == 9) { // [父页]：提示+刷新页面
	            zcParentRefreshAlert(result.msg,1);
	        }else if(result.code == 10){	//[父页]：提示+跳转页面
		    	zcParentRedirectAlert(result.msg,result.data, 1);
		    }else if(result.code == 99) { // 刷新当前页（用于支付宝）
				var str = '<div style="display:block;">' + result.data + '</div>';
				$("body").append(str);
			}else{	//错误的状态：仅提示
		    	zcAlert(result.msg, 0);
		    }
			
		},"JSON");
	});
}

/**
 * 信息提示框 msg 提示的信息内容 type 类型(0 失败 / 1 成功)
 */
function zcAlert(msg, type) {
	var typeIndex = 0; // 默认为FALSE的图标
	if (type == 1) {
		typeIndex = 1;
	}
	layer.alert(msg, {icon: typeIndex});
}

/**
 * 信息提示框，点击确认后，刷新【当前】页 msg 提示的信息内容 type 类型(0 失败 / 1 成功)
 */
function zcRefreshAlert(msg, type) {
	var typeIndex = 0; // 默认为FALSE的图标
	if (type == 1) {
		typeIndex = 1;
	}
	layer.alert(msg,{icon: typeIndex}, function(index){
		window.location.reload();
	});
}

/**
 * 信息提示框，点击确认后，跳转【当前】页 msg 提示的信息内容、url跳转的页面、 type 类型(0 失败 / 1 成功)
 */
function zcRedirectAlert(msg,url,type) {
	var typeIndex = 0; // 默认为FALSE的图标
	if (type == 1) {
		typeIndex = 1;
	}
	layer.alert(msg,{icon: typeIndex}, function(index){
		window.location.href = url;
	});
}



/**
 * 信息提示框，点击确认后，刷新【父】页 msg 提示的信息内容 type 类型(0 失败 / 1 成功)
 */
function zcParentRefreshAlert(msg, type) {
	var typeIndex = 0; // 默认为FALSE的图标
	if (type == 1) {
		typeIndex = 1;
	}
	layer.alert(msg,{icon: typeIndex}, function(index){
		parent.window.location.reload();
	});
}

/**
 * 信息提示框，点击确认后，跳转【父】面 msg 提示的信息内容、url跳转的页面、 type 类型(0 失败 / 1 成功)
 */
function zcParentRedirectAlert(msg,url,type) {
	var typeIndex = 0; // 默认为FALSE的图标
	if (type == 1) {
		typeIndex = 1;
	}
	layer.alert(msg,{icon: typeIndex}, function(index){
		parent.window.location.href = url;
	});
}

/**
 * 弹窗封装11111：使用方式如：<a href="__APP__/p/22/?name=4jcms&width=300&height=400"
 * title="弹窗测试" class="zcbox">测试</a>
 * 此方式必须得所有DOM加载完毕后，点击方可生效，如果在页面显示出来，但是DOM没有完全加载完，就用鼠标点击的话，则会以链接的方式。 优势：简单易用
 * titleVal 弹窗标题 width 宽度 height 高度 srcVal 页面地址
 */
$(function() {

	$(".zcbox").on("click", function() {
		var $this = $(this);
		var titleVal = $this.attr("title");
		var srcVal = $this.attr("href");
		var width = getUrlParam(srcVal, 'width');
		var height = getUrlParam(srcVal, 'height');
		
		openZcBox(srcVal, width, height,titleVal);
		return false;
	});
	
});

/**
 * 弹窗封装11111：使用方式，直接调用JS方法，该方式拟补了第一种的劣势。 titleVal 弹窗标题 优势：直接调用，没有DOM加载顺序 width
 * 宽度 height 高度 srcVal 页面地址
 */
function openZcBox(srcVal, width, height,titleVal) {
	
	if(width.indexOf("%") == -1){
		width = width+'px';
	}
	if(height.indexOf("%") == -1){
		height = height+'px';
	}
	
	layer.open({
		type : 2,
		title : titleVal,
		maxmin : true,
		shadeClose : true, // 开启点击遮罩关闭层
		area : [width, height],
		content: srcVal
	});
}

/**
 * 弹窗封装22222：使用方式，直接调用JS方法，该方式拟补了第一种的劣势。 titleVal 弹窗标题 优势：直接调用，没有DOM加载顺序 width
 */
function openLongBox(obj,srcVal, width,titleVal) {
	
	if(width.indexOf("%") == -1){
		width = width+'px';
	}
	
	layer.open({
		type : 2,
		title : titleVal,
		shadeClose : true, // 开启点击遮罩关闭层
		area : [width, '100%'],
		offset: 'rb',
		shift: 2,
		content: srcVal
	});
	
	$(".onhover").removeClass("onhover");
	$(obj).addClass("onhover");
	
}


//获取URL对应参数值
function getUrlParam(url, param_name) {
	var reg = new RegExp("(^|&|\\?)" + param_name + "=([^&]*)(&|$)"); // 构造一个含有目标参数的正则表达式对象
	var r = url.match(reg); // 匹配目标参数
	if (r != null)
		return unescape(r[2]);
	return null; // 返回参数值
}

