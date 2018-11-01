/**
 * upload4j封装渲染组件
 */
$(function() {
	//多文件上传渲染

	$.each( $(".4jUploader"), function(i, n){
		analyzeUploadHtml(null,n);
	});

});

/**
 * upload4j解析类
 * 图片或文件设置的初始大小是3M和10M，如果超过了本机的php.ini配置文件里的大小就会上传失败，图片或文件无法显示。则需要设置php.ini,参考文章http://wangking717.iteye.com/blog/1947080
 * @param name  对象的ID，与obj参数属于或的关系
 * @param obj 对象obj本身，与id参数属于或的关系
 */
function analyzeUploadHtml(name,obj){

	var $this = null;
	if(obj == null && name != null){
		$this = $("[name='"+name+"']");
	}else if(obj != null && name == null){
		$this = $(obj);
	}
	if($this == null){
		alert("upload4j解析出错！");
		return;
	}

	var width4container = $this.width();	//容器宽度，用来设置文件重命名时，输入框的大小，不然会错位。
	var id = $this.attr("name");
	var type = $this.attr("type");
	var value = $this.attr("value");
	var extType = $this.attr("ext");
	var app_path = $this.attr("app_path");
	var root_path = $this.attr("root_path");
	var upload = $this.attr("upload");

	if(type == 'single' || type == 'multi'){

		var itemHtmlVal = "";
		var fileList = new Array();
		var isMulti = false;
		var fileSize4baidu = 10 * 1024 * 1024;	//默认为图片大小(10MB)，供百度webuploader使用
		var fileSize = "10MB";	//默认为图片大小(3MB)，供uploadify使用
		var fileExts = $this.attr("ext") == 'img'? "*.gif;*.jpg;*.jpeg;*.png":"*.*";
		var inputclass = width4container <= 500? "ipt small-ipt":"ipt";
		if(type == 'multi'){	//【多文件、图片】文件上传
			fileList = value.split("[]");
			isMulti = true;
		}else{	//【单文件、图片】上传
			fileList[0] = value;
		}

		if(extType == 'file'){	//file upload

			fileSize4baidu = 10 * 1024 * 1024;	//文件上传大小限制（10MB）
			fileSize = "10MB";	//文件上传大小限制（10MB）

			for(var i=0;i<fileList.length;++i){
				if(fileList[i] != ""){
					var obj = fileList[i].split(":");
					var fileExt = "";
					var ext = getExtname(obj[0]);
					if(ext != ""){
						fileExt = '<a href="javascript:;" class="file_ext">'+ext+'</a>&nbsp;&nbsp;&nbsp;';
					}
					var uploadID = hex_md5(fileList[i]);
					itemHtmlVal += '<div class="upload_file clearfix" id="'+uploadID+'">'+
						'<div class="fl">'+
						'<a href="'+upload+obj[0]+'" class="display_name_tag" target="_blank">'+obj[1]+ext+'</a>'+
						'<div class="input_name_tag">'+
						'<input type="text" id="'+uploadID+'_text" value="'+obj[1]+'" class="'+inputclass+'" />&nbsp;'+fileExt+
						'<a class="btn btn-info" href="javascript:saveFileName(\''+uploadID+'\',\''+id+'\',\''+obj[0]+'\',\''+ext+'\');">保存</a>&nbsp;'+
						'<a class="btn" href="javascript:cancelSetRename(\''+uploadID+'\');">取消</a>'+
						'</div>'+
						'</div>'+
						'<div class="fr">'+
						'<a href="javascript:setRename(\''+uploadID+'\');" class="file_func">重命名</a>&nbsp;&nbsp;&nbsp;&nbsp;'+
						'<span class="del_tag"><a href="javascript:deleteUploadFile('+uploadID+');" class="file_func">删除</a></span>'+
						'</div>'+
						'</div>';
				}
			}

		}else{	//image upload

			fileSize4baidu = 10 * 1024 * 1024;	//图片上传大小限制（3MB）
			fileSize = "10MB";	//图片上传大小限制（3MB）

			for (var i = 0; i < fileList.length; ++i) {
				if (fileList[i] != "") {
					var uploadID = hex_md5(fileList[i]);
					itemHtmlVal += '<span class="upload_thum" id="'+uploadID+'">'+
						'<img src="'+upload+fileList[i]+'" width="100" height="100" />'+
                        '<a data-id="'+id+'" data-upload="'+uploadID+'" data-path="'+fileList[i]+'" onclick="deleteUploadFile(this)" class="imgdel">删</a>'+
						'</span>';
				}
			}

		}



		var explorer =navigator.userAgent;
		
		if(explorer.indexOf("MSIE") >= 0){	//IE下统一用FLASH，使用uploadify组件
			var htmlVal = '<div class="multi_uploader">'+
				'<input type="hidden" id="'+id+'_text" name="'+id+'" value="'+value+'" />'+
				'<div class="head">'+
				'<input id="'+id+'" name="'+id+'" type="file" />'+
				'<span id="'+id+'_progress"></span>'+
				'</div>'+
				'<div id="'+id+'_filelist" class="filelist">'+itemHtmlVal+'</div>'+
				'</div>';

			$this.html(htmlVal);
			callUploadify(id,fileExts,isMulti,fileSize,$this.attr("timestamp"),$this.attr("salt"),root_path,width4container);


		}else {	//其他浏览器下统一用HTML5，使用百度WebUploader组件

			var buttonText = "";
			if (isMulti == true) {
				if (extType == "file") {
					buttonText = "多文件上传...";
				} else {
					buttonText = "多图片上传...";
				}
			} else {
				if (extType == "file") {
					buttonText = "单文件上传...";
				} else {
					buttonText = "单图片上传...";
				}
			}

			var htmlVal = '<div class="multi_uploader">' +
				'<input data-type="单文件上传" type="hidden" id="' + id + '_text" name="' + id + '" value="' + value + '" />' +
				'<div class="head clearfix">' +
				'<div id="' + id + '">' + buttonText + '</div>' +
				'<span id="' + id + '_progress" class="uploader_process"></span>' +
				'</div>' +
				'<div id="' + id + '_filelist" class="filelist">' + itemHtmlVal + '</div>' +
				'</div>';

			$this.html(htmlVal);
			// callUploadify(id, fileExts, isMulti, fileSize, $this.attr("timestamp"), $this.attr("salt"), root_path, width4container);
			callWebuploader(id,extType,isMulti,fileSize4baidu,$this.attr("timestamp"),$this.attr("salt"),root_path,width4container);
		}
	}




}

/**
 * 调用webuploader
 * @param id	要渲染的upload组建
 * @param extType	后缀限制，文件上传“file”、图片上传“img”
 * @param fileSize 图片大小3MB，文件大小10MB，对应php.ini服务端定义上传限制为10MB，参考http://www.jb51.net/article/18975.htm
 */
function callWebuploader(id, extType,isMulti,fileSize,timestamp,token,ROOT_PATH,width4container) {

	var acceptVal = null;
	var percentages = {};
	var total = 0;	//总共的文件数，供进度条使用
	if(extType == 'img'){
		acceptVal = {title: 'Images',extensions: 'gif,jpg,jpeg,bmp,png',mimeTypes: 'image/!*'};
	}
	var fileNumLimitVal;
	if(isMulti == false){
		fileNumLimitVal = 1;
	}

	var uploader = WebUploader.create({
		auto:true,
		swf:ROOT_PATH+'/webuploader.swf',
		server:ROOT_PATH+"/upload/index.html",
		pick:'#'+id,
		accept:acceptVal,
		fileNumLimit:fileNumLimitVal,
		fileSingleSizeLimit:fileSize,
		formData: {
			'extType' : extType,
			'timestamp': timestamp,
			'token': token
		}
	});

	// 当有文件添加进来的时候
	uploader.on('fileQueued', function(file){
		total++;
		$("#"+id+"_progress").html('<span style="color:gray;">文件上传中...</span>');
	});

	//文件上传过程中创建进度条实时显示。
	uploader.on('uploadProgress', function(file, percentage) {

		var loaded = 0,percent;
		percentages[file.id] = percentage;

		$.each(percentages, function(k,v) {
			loaded += v;
		});

		percent = Math.round((loaded / total) * 100);
		if(percent == 100){
			$("#"+id+"_progress").html('<span style="color:green;">'+percent + '%</span>');
		}else{
			$("#"+id+"_progress").html('<span style="color:blue;">'+percent + '%</span>');
		}

	});

	//文件上传成功，给item添加成功class, 用样式标记上传成功。
	uploader.on('uploadSuccess', function(file,result){

		if (result.status == 0) {
			alert(result.msg);
			return;
		}

		//以下则为成功上传流程
		var htmlVal = '';
		var uploadID = hex_md5(result.msg);
		if(extType == 'file'){

			//multi file upload
			// var obj = result.msg.split("[]");
			 var obj = result.msg;
			var filename = obj;
			// var filename = getFilename(obj[1]);
			if(filename == ''){
				alert("“"+obj[1]+"”，该文件没有名称，操作失败。")
				return;
			}
			var fileExt = "";
			// var ext = getExtname(obj[1]);
			var ext = obj;
			console.log(ext);
			console.log(filename)
			var inputclass = width4container <= 500? "ipt small-ipt":"ipt";
			if(ext != ""){
				fileExt = '<a href="javascript:;" class="file_ext">'+ext+'</a>&nbsp;&nbsp;&nbsp;';
			}
			htmlVal = '<div class="upload_file clearfix" id="'+uploadID+'">'+
				'<div class="fl">'+
				'<a href="'+ROOT_PATH+obj[0]+'" class="display_name_tag" target="_blank">'+obj[1]+'</a>'+
				'<div class="input_name_tag">'+
				'<input type="text" id="'+uploadID+'_text" value="'+filename+'" class="'+inputclass+'" />&nbsp;'+fileExt+
				'<a class="btn btn-info" href="javascript:saveFileName(\''+uploadID+'\',\''+id+'\',\''+obj[0]+'\',\''+ext+'\');">保存</a>&nbsp;'+
				'<a class="btn" href="javascript:cancelSetRename(\''+uploadID+'\');">取消</a>'+
				'</div>'+
				'</div>'+
				'<div class="fr">'+
				'<a href="javascript:setRename(\''+uploadID+'\');" class="file_func">重命名</a>&nbsp;&nbsp;&nbsp;&nbsp;'+
				'<span class="del_tag"><a href="javascript:deleteUploadFile('+uploadID+');" class="file_func">删除</a></span>'+
				'</div>'+
				'</div>';
		}else{	//multi image upload

			htmlVal = '<span class="upload_thum" id="'+uploadID+'">'+
				'<img src="'+ROOT_PATH+result.msg+'" width="100" height="100" />'+
				'<a class="imgdel" data-id="'+id+'" data-upload="'+uploadID+'" data-path="'+result.msg+'" onclick="deleteUploadFile(this)">删</a>'+
				'</span>';
		}

		if(isMulti == true){
			$("#"+id+"_filelist").append(htmlVal);
			var $inputText = $("#"+id+"_text");
			var val = $inputText.val();
			if($inputText.val() != ''){
				val += "[]";
			}
			if(extType == 'file'){	//file upload
				$inputText.val(val+getFilename(result.msg));
			}else{
				$inputText.val(val+result.msg);
			}

		}else{
			$("#"+id+"_filelist").html(htmlVal);
			var $inputText = $("#"+id+"_text");
			if(extType == 'file'){	//file upload
				$inputText.val(getFilename(result.msg));
			}else{
				$inputText.val(result.msg);
			}

		}

		uploader.removeFile(file);	//移除队列


	});


}

/**
 * 调用uploadify组件
 * @param id	要渲染的upload组建
 * @param fileExts	后缀限制，不限制，则赋''，反之可按照如下：'fileTypeExts' : '*.gif; *.jpg; *.png',
 * @param fileSize 图片大小3MB，文件大小10MB，对应php.ini服务端定义上传限制为10MB，参考http://www.jb51.net/article/18975.htm
 */
function callUploadify(id, fileExts,isMulti,fileSize,timestamp,token,ROOT_PATH,width4container) {
	var buttonText = "";
	if(isMulti == true){
		if(fileExts == "*.*"){
			buttonText = "多文件上传...";
		}else{
			buttonText = "多图片上传...";
		}
	}else{
		if(fileExts == "*.*"){
			buttonText = "单文件上传...";
		}else{
			buttonText = "单图片上传...";
		}
	}
	$("#"+id).uploadify({
		'multi'    : isMulti,
		'width'    : 110,
		'height'   : 31,
		'buttonText'  :	buttonText,
		'fileTypeExts' : fileExts,
		'queueSizeLimit': 20,
		'fileSizeLimit' : fileSize,
		'overrideEvents' : ['onSelectError','onDialogClose','onSelectError'],
		'swf'      : '/uploadify.swf',
		'uploader' : '/upload/index.html',
		formData: {
			'fileTypeExts' : fileExts,
			'timestamp': timestamp,
			'token': token
		},
		'onInit': function () {
			$(".uploadify-queue").hide();
		},
		'onFallback' : function() {
			alert('您未安装FLASH控件，无法上传组建！请安装FLASH控件后再试。');
		},
		'onSelectError' : function(file, errorCode, errorMsg) {
			var msgText = "上传失败\n";
			switch (errorCode) {
				case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
					//this.queueData.errorMsg = "每次最多上传 " + this.settings.queueSizeLimit + "个文件";
					msgText += "每次最多上传 " + this.settings.queueSizeLimit + "个文件";
					break;
				case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
					msgText += "文件大小超过限制( " + this.settings.fileSizeLimit + " )";
					break;
				case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
					msgText += "文件大小为0";
					break;
				case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
					msgText += "文件格式不正确，仅限 " + this.settings.fileTypeExts;
					break;
				default:
					msgText += "错误代码：" + errorCode + "\n" + errorMsg;
			}
			alert(msgText);

			return false;
		},
		'onUploadProgress' : function(file, bytesUploaded, bytesTotal, totalBytesUploaded, totalBytesTotal) {
			pos = Math.round((totalBytesUploaded/totalBytesTotal)*100);
			var str = "&nbsp;&nbsp;<strong style='color:#FFF;'>上传进度：" + pos + "%</strong>";
			if(pos >= 100){
				var str = "&nbsp;&nbsp;<strong style='color:#FFF;'>上传赋值中，请等待...</strong>";
			}
			$("#"+id+"_progress").html(str);
		},
		'onUploadSuccess' : function(file, data, response) {
			var result = $.parseJSON(data);
			if (result.status == 0) {
				alert(result.msg);
				return;
			}

			//以下则为成功上传流程
			var htmlVal = '';
			var uploadID = hex_md5(result.msg);
			if(fileExts == '*.*'){	//multi file upload
				var obj = result.msg.split(":");
				var filename = getFilename(obj[1]);
				if(filename == ''){
					$("#"+id+"_progress").html("");
					alert("“"+obj[1]+"”，该文件没有名称，操作失败。")
					return;
				}
				var inputclass = width4container <= 500? "ipt small-ipt":"ipt";
				var fileExt = "";
				var ext = getExtname(obj[1]);
				if(ext != ""){
					fileExt = '<a href="javascript:;" class="file_ext">'+ext+'</a>&nbsp;&nbsp;&nbsp;';
				}
				htmlVal = '<div class="upload_file clearfix" id="'+uploadID+'">'+
					'<div class="fl">'+
					'<a href="'+ROOT_PATH+obj[0]+'" class="display_name_tag" target="_blank">'+obj[1]+'</a>'+
					'<div class="input_name_tag">'+
					'<input type="text" id="'+uploadID+'_text" value="'+filename+'" class="'+inputclass+'" />&nbsp;'+fileExt+
					'<a class="btn btn-info" href="javascript:saveFileName(\''+uploadID+'\',\''+id+'\',\''+obj[0]+'\',\''+ext+'\');">保存</a>&nbsp;'+
					'<a class="btn" href="javascript:cancelSetRename(\''+uploadID+'\');">取消</a>'+
					'</div>'+
					'</div>'+
					'<div class="fr">'+
					'<a href="javascript:setRename(\''+uploadID+'\');" class="file_func">重命名</a>&nbsp;&nbsp;&nbsp;&nbsp;'+
					'<span class="del_tag"><a href="javascript:deleteUploadFile('+uploadID+');" class="file_func">删除</a></span>'+
					'</div>'+
					'</div>';
			}else{	//multi image upload

				htmlVal = '<span class="upload_thum" id="'+uploadID+'">'+
					'<img src="'+ROOT_PATH+result.msg+'" width="100" height="100" />'+
					'<a href="javascript:deleteUploadFile(\''+uploadID+'\',\''+id+'\',\''+result.msg+'\',2)" class="imgdel">删</a>'+
					'</span>';
				// \''+uploadID+'\',\''+id+'\',\''+fileList[i]+'\',\'\'
				// \''+uploadID+'\',\''+result.msg+'\'
			}

			if(isMulti == true){
				$("#"+id+"_filelist").append(htmlVal);
				var $inputText = $("#"+id+"_text");
				var val = $inputText.val();
				if($inputText.val() != ''){
					val += "[]";
				}
				if(fileExts == '*.*'){	//file upload
					$inputText.val(val+getFilename(result.msg));
				}else{
					$inputText.val(val+result.msg);
				}

			}else{
				$("#"+id+"_filelist").html(htmlVal);
				var $inputText = $("#"+id+"_text");
				if(fileExts == '*.*'){	//file upload
					$inputText.val(getFilename(result.msg));
				}else{
					$inputText.val(result.msg);
				}

			}

			$("#"+id+"_progress").html("");
		}
	});
}

/**
 * 设置文件重命名
 */
function setRename(uploadID){
	var $this = $("#"+uploadID);
	$this.find(".input_name_tag").show();
	$this.find(".display_name_tag").hide();
}

/**
 * 取消设置文件重命名
 */
function cancelSetRename(uploadID){
	var $this = $("#"+uploadID);
	$this.find(".input_name_tag").hide();
	$this.find(".display_name_tag").show();
}

/**
 * 文件上传模块：重命名对其保存的函数
 */
function saveFileName(uploadID,id,filename,ext){
	//<span class="del_tag">
	var $this = $("#"+uploadID);
	var $inputText = $("#"+id+"_text");
	var $display_name_tag = $this.find(".display_name_tag");
	var $del_tag = $this.find(".del_tag");

	var oldval = getFilename($display_name_tag.html());	//原始的文件名称
	var newval = $("#"+uploadID+"_text").val();	//新输入的文件名称

	if(newval.indexOf('<') > 0 || newval.indexOf('>') > 0 || newval.indexOf('/') > 0 || newval.indexOf(':') > 0 || newval.indexOf('*') > 0 || newval.indexOf('?') > 0 || newval.indexOf('"') > 0 || newval.indexOf("\\") > 0){
		alert('不能包含非法字符！');
		return false;
	}
	if(newval == ''){
		alert('请输入文件名！');
		return false;
	}

	var oldMatchStr = filename+':'+oldval;
	var newMatchStr = filename+':'+newval;
	var delStr = $del_tag.html();
	delStr = delStr.replace(oldval,newval);

	//替换HIDDEN存入的数据
	var inputval = $inputText.val();
	inputval = inputval.replace(oldMatchStr,newMatchStr);
	$inputText.val(inputval);
	//将文本替换成新的内容，并且进行隐藏输入框，显示新内容事件处理
	$display_name_tag.html(newval+ext);
	$del_tag.html(delStr);
	$this.find(".input_name_tag").hide();
	$this.find(".display_name_tag").show();

}

/**
 *文件删除
 */
function deleteUploadFile(obj){
	var uploadId = $(obj).data('upload');
   	    $("#"+uploadId).remove();
	var path = $(obj).data('path');
	var id = $(obj).data('id');
    var $inputText = $("#"+id+"_text");
    var val = $inputText.val();
    $.get('/upload/delete?file='+path, function(row){

    });
    // var fileVal = path;
    // if(filename != ''){
    //     fileVal = fileaddr+':'+filename;
    // }
    //
    // //双重替换关键字
    val = val.replace('[]'+path,'');
    val = val.replace(path,'');
    $inputText.val(val);
    // $('#'+id+'_progress').html('');
}


//获取文件名
function getFilename(val){
	arr = new Array();
	arr = val.split(".");
	var filename = "";
	for (var i=0;i<arr.length-1;i++){
		if(i != 0){
			filename += '.';
		}
		filename += arr[i];
	}
	return filename;
}

//获取文件后缀名
function getExtname(val){
	arr = new Array();
	arr = val.split(".");
	extname = arr[arr.length - 1];
	return "."+extname;
}

/*
 * MD加密插件
 * See http://pajhome.org.uk/crypt/md5 for more info.
 */

var hexcase = 0;  /* hex output format. 0 - lowercase; 1 - uppercase        */
var b64pad  = ""; /* base-64 pad character. "=" for strict RFC compliance   */
var chrsz   = 8;  /* bits per input character. 8 - ASCII; 16 - Unicode      */

/*
 * These are the functions you'll usually want to call
 * They take string arguments and return either hex or base-64 encoded strings
 */
function hex_md5(s){ return binl2hex(core_md5(str2binl(s), s.length * chrsz));}
function b64_md5(s){ return binl2b64(core_md5(str2binl(s), s.length * chrsz));}
function str_md5(s){ return binl2str(core_md5(str2binl(s), s.length * chrsz));}
function hex_hmac_md5(key, data) { return binl2hex(core_hmac_md5(key, data)); }
function b64_hmac_md5(key, data) { return binl2b64(core_hmac_md5(key, data)); }
function str_hmac_md5(key, data) { return binl2str(core_hmac_md5(key, data)); }

/*
 * Perform a simple self-test to see if the VM is working
 */
function md5_vm_test()
{
	return hex_md5("abc") == "900150983cd24fb0d6963f7d28e17f72";
}

/*
 * Calculate the MD5 of an array of little-endian words, and a bit length
 */
function core_md5(x, len)
{
	/* append padding */
	x[len >> 5] |= 0x80 << ((len) % 32);
	x[(((len + 64) >>> 9) << 4) + 14] = len;

	var a =  1732584193;
	var b = -271733879;
	var c = -1732584194;
	var d =  271733878;

	for(var i = 0; i < x.length; i += 16)
	{
		var olda = a;
		var oldb = b;
		var oldc = c;
		var oldd = d;

		a = md5_ff(a, b, c, d, x[i+ 0], 7 , -680876936);
		d = md5_ff(d, a, b, c, x[i+ 1], 12, -389564586);
		c = md5_ff(c, d, a, b, x[i+ 2], 17,  606105819);
		b = md5_ff(b, c, d, a, x[i+ 3], 22, -1044525330);
		a = md5_ff(a, b, c, d, x[i+ 4], 7 , -176418897);
		d = md5_ff(d, a, b, c, x[i+ 5], 12,  1200080426);
		c = md5_ff(c, d, a, b, x[i+ 6], 17, -1473231341);
		b = md5_ff(b, c, d, a, x[i+ 7], 22, -45705983);
		a = md5_ff(a, b, c, d, x[i+ 8], 7 ,  1770035416);
		d = md5_ff(d, a, b, c, x[i+ 9], 12, -1958414417);
		c = md5_ff(c, d, a, b, x[i+10], 17, -42063);
		b = md5_ff(b, c, d, a, x[i+11], 22, -1990404162);
		a = md5_ff(a, b, c, d, x[i+12], 7 ,  1804603682);
		d = md5_ff(d, a, b, c, x[i+13], 12, -40341101);
		c = md5_ff(c, d, a, b, x[i+14], 17, -1502002290);
		b = md5_ff(b, c, d, a, x[i+15], 22,  1236535329);

		a = md5_gg(a, b, c, d, x[i+ 1], 5 , -165796510);
		d = md5_gg(d, a, b, c, x[i+ 6], 9 , -1069501632);
		c = md5_gg(c, d, a, b, x[i+11], 14,  643717713);
		b = md5_gg(b, c, d, a, x[i+ 0], 20, -373897302);
		a = md5_gg(a, b, c, d, x[i+ 5], 5 , -701558691);
		d = md5_gg(d, a, b, c, x[i+10], 9 ,  38016083);
		c = md5_gg(c, d, a, b, x[i+15], 14, -660478335);
		b = md5_gg(b, c, d, a, x[i+ 4], 20, -405537848);
		a = md5_gg(a, b, c, d, x[i+ 9], 5 ,  568446438);
		d = md5_gg(d, a, b, c, x[i+14], 9 , -1019803690);
		c = md5_gg(c, d, a, b, x[i+ 3], 14, -187363961);
		b = md5_gg(b, c, d, a, x[i+ 8], 20,  1163531501);
		a = md5_gg(a, b, c, d, x[i+13], 5 , -1444681467);
		d = md5_gg(d, a, b, c, x[i+ 2], 9 , -51403784);
		c = md5_gg(c, d, a, b, x[i+ 7], 14,  1735328473);
		b = md5_gg(b, c, d, a, x[i+12], 20, -1926607734);

		a = md5_hh(a, b, c, d, x[i+ 5], 4 , -378558);
		d = md5_hh(d, a, b, c, x[i+ 8], 11, -2022574463);
		c = md5_hh(c, d, a, b, x[i+11], 16,  1839030562);
		b = md5_hh(b, c, d, a, x[i+14], 23, -35309556);
		a = md5_hh(a, b, c, d, x[i+ 1], 4 , -1530992060);
		d = md5_hh(d, a, b, c, x[i+ 4], 11,  1272893353);
		c = md5_hh(c, d, a, b, x[i+ 7], 16, -155497632);
		b = md5_hh(b, c, d, a, x[i+10], 23, -1094730640);
		a = md5_hh(a, b, c, d, x[i+13], 4 ,  681279174);
		d = md5_hh(d, a, b, c, x[i+ 0], 11, -358537222);
		c = md5_hh(c, d, a, b, x[i+ 3], 16, -722521979);
		b = md5_hh(b, c, d, a, x[i+ 6], 23,  76029189);
		a = md5_hh(a, b, c, d, x[i+ 9], 4 , -640364487);
		d = md5_hh(d, a, b, c, x[i+12], 11, -421815835);
		c = md5_hh(c, d, a, b, x[i+15], 16,  530742520);
		b = md5_hh(b, c, d, a, x[i+ 2], 23, -995338651);

		a = md5_ii(a, b, c, d, x[i+ 0], 6 , -198630844);
		d = md5_ii(d, a, b, c, x[i+ 7], 10,  1126891415);
		c = md5_ii(c, d, a, b, x[i+14], 15, -1416354905);
		b = md5_ii(b, c, d, a, x[i+ 5], 21, -57434055);
		a = md5_ii(a, b, c, d, x[i+12], 6 ,  1700485571);
		d = md5_ii(d, a, b, c, x[i+ 3], 10, -1894986606);
		c = md5_ii(c, d, a, b, x[i+10], 15, -1051523);
		b = md5_ii(b, c, d, a, x[i+ 1], 21, -2054922799);
		a = md5_ii(a, b, c, d, x[i+ 8], 6 ,  1873313359);
		d = md5_ii(d, a, b, c, x[i+15], 10, -30611744);
		c = md5_ii(c, d, a, b, x[i+ 6], 15, -1560198380);
		b = md5_ii(b, c, d, a, x[i+13], 21,  1309151649);
		a = md5_ii(a, b, c, d, x[i+ 4], 6 , -145523070);
		d = md5_ii(d, a, b, c, x[i+11], 10, -1120210379);
		c = md5_ii(c, d, a, b, x[i+ 2], 15,  718787259);
		b = md5_ii(b, c, d, a, x[i+ 9], 21, -343485551);

		a = safe_add(a, olda);
		b = safe_add(b, oldb);
		c = safe_add(c, oldc);
		d = safe_add(d, oldd);
	}
	return Array(a, b, c, d);

}

/*
 * These functions implement the four basic operations the algorithm uses.
 */
function md5_cmn(q, a, b, x, s, t)
{
	return safe_add(bit_rol(safe_add(safe_add(a, q), safe_add(x, t)), s),b);
}
function md5_ff(a, b, c, d, x, s, t)
{
	return md5_cmn((b & c) | ((~b) & d), a, b, x, s, t);
}
function md5_gg(a, b, c, d, x, s, t)
{
	return md5_cmn((b & d) | (c & (~d)), a, b, x, s, t);
}
function md5_hh(a, b, c, d, x, s, t)
{
	return md5_cmn(b ^ c ^ d, a, b, x, s, t);
}
function md5_ii(a, b, c, d, x, s, t)
{
	return md5_cmn(c ^ (b | (~d)), a, b, x, s, t);
}

/*
 * Calculate the HMAC-MD5, of a key and some data
 */
function core_hmac_md5(key, data)
{
	var bkey = str2binl(key);
	if(bkey.length > 16) bkey = core_md5(bkey, key.length * chrsz);

	var ipad = Array(16), opad = Array(16);
	for(var i = 0; i < 16; i++)
	{
		ipad[i] = bkey[i] ^ 0x36363636;
		opad[i] = bkey[i] ^ 0x5C5C5C5C;
	}

	var hash = core_md5(ipad.concat(str2binl(data)), 512 + data.length * chrsz);
	return core_md5(opad.concat(hash), 512 + 128);
}

/*
 * Add integers, wrapping at 2^32. This uses 16-bit operations internally
 * to work around bugs in some JS interpreters.
 */
function safe_add(x, y)
{
	var lsw = (x & 0xFFFF) + (y & 0xFFFF);
	var msw = (x >> 16) + (y >> 16) + (lsw >> 16);
	return (msw << 16) | (lsw & 0xFFFF);
}

/*
 * Bitwise rotate a 32-bit number to the left.
 */
function bit_rol(num, cnt)
{
	return (num << cnt) | (num >>> (32 - cnt));
}

/*
 * Convert a string to an array of little-endian words
 * If chrsz is ASCII, characters >255 have their hi-byte silently ignored.
 */
function str2binl(str)
{
	var bin = Array();
	var mask = (1 << chrsz) - 1;
	for(var i = 0; i < str.length * chrsz; i += chrsz)
		bin[i>>5] |= (str.charCodeAt(i / chrsz) & mask) << (i%32);
	return bin;
}

/*
 * Convert an array of little-endian words to a string
 */
function binl2str(bin)
{
	var str = "";
	var mask = (1 << chrsz) - 1;
	for(var i = 0; i < bin.length * 32; i += chrsz)
		str += String.fromCharCode((bin[i>>5] >>> (i % 32)) & mask);
	return str;
}

/*
 * Convert an array of little-endian words to a hex string.
 */
function binl2hex(binarray){
	var hex_tab = hexcase ? "0123456789ABCDEF" : "0123456789abcdef";
	var str = "";
	for(var i = 0; i < binarray.length * 4; i++)
	{
		str += hex_tab.charAt((binarray[i>>2] >> ((i%4)*8+4)) & 0xF) +
			hex_tab.charAt((binarray[i>>2] >> ((i%4)*8  )) & 0xF);
	}
	return str;
}

/*
 * Convert an array of little-endian words to a base-64 string
 */
function binl2b64(binarray){
	var tab = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
	var str = "";
	for(var i = 0; i < binarray.length * 4; i += 3)
	{
		var triplet = (((binarray[i   >> 2] >> 8 * ( i   %4)) & 0xFF) << 16)
			| (((binarray[i+1 >> 2] >> 8 * ((i+1)%4)) & 0xFF) << 8 )
			|  ((binarray[i+2 >> 2] >> 8 * ((i+2)%4)) & 0xFF);
		for(var j = 0; j < 4; j++)
		{
			if(i * 8 + j * 6 > binarray.length * 32) str += b64pad;
			else str += tab.charAt((triplet >> 6*(3-j)) & 0x3F);
		}
	}
	return str;
}

//MD5插件结束

/*Uploadify v3.2*/
;var swfobject=function(){var aq="undefined",aD="object",ab="Shockwave Flash",X="ShockwaveFlash.ShockwaveFlash",aE="application/x-shockwave-flash",ac="SWFObjectExprInst",ax="onreadystatechange",af=window,aL=document,aB=navigator,aa=false,Z=[aN],aG=[],ag=[],al=[],aJ,ad,ap,at,ak=false,aU=false,aH,an,aI=true,ah=function(){var a=typeof aL.getElementById!=aq&&typeof aL.getElementsByTagName!=aq&&typeof aL.createElement!=aq,e=aB.userAgent.toLowerCase(),c=aB.platform.toLowerCase(),h=c?/win/.test(c):/win/.test(e),j=c?/mac/.test(c):/mac/.test(e),g=/webkit/.test(e)?parseFloat(e.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,"$1")):false,d=!+"\v1",f=[0,0,0],k=null;if(typeof aB.plugins!=aq&&typeof aB.plugins[ab]==aD){k=aB.plugins[ab].description;if(k&&!(typeof aB.mimeTypes!=aq&&aB.mimeTypes[aE]&&!aB.mimeTypes[aE].enabledPlugin)){aa=true;d=false;k=k.replace(/^.*\s+(\S+\s+\S+$)/,"$1");f[0]=parseInt(k.replace(/^(.*)\..*$/,"$1"),10);f[1]=parseInt(k.replace(/^.*\.(.*)\s.*$/,"$1"),10);f[2]=/[a-zA-Z]/.test(k)?parseInt(k.replace(/^.*[a-zA-Z]+(.*)$/,"$1"),10):0;}}else{if(typeof af.ActiveXObject!=aq){try{var i=new ActiveXObject(X);if(i){k=i.GetVariable("$version");if(k){d=true;k=k.split(" ")[1].split(",");f=[parseInt(k[0],10),parseInt(k[1],10),parseInt(k[2],10)];}}}catch(b){}}}return{w3:a,pv:f,wk:g,ie:d,win:h,mac:j};}(),aK=function(){if(!ah.w3){return;}if((typeof aL.readyState!=aq&&aL.readyState=="complete")||(typeof aL.readyState==aq&&(aL.getElementsByTagName("body")[0]||aL.body))){aP();}if(!ak){if(typeof aL.addEventListener!=aq){aL.addEventListener("DOMContentLoaded",aP,false);}if(ah.ie&&ah.win){aL.attachEvent(ax,function(){if(aL.readyState=="complete"){aL.detachEvent(ax,arguments.callee);aP();}});if(af==top){(function(){if(ak){return;}try{aL.documentElement.doScroll("left");}catch(a){setTimeout(arguments.callee,0);return;}aP();})();}}if(ah.wk){(function(){if(ak){return;}if(!/loaded|complete/.test(aL.readyState)){setTimeout(arguments.callee,0);return;}aP();})();}aC(aP);}}();function aP(){if(ak){return;}try{var b=aL.getElementsByTagName("body")[0].appendChild(ar("span"));b.parentNode.removeChild(b);}catch(a){return;}ak=true;var d=Z.length;for(var c=0;c<d;c++){Z[c]();}}function aj(a){if(ak){a();}else{Z[Z.length]=a;}}function aC(a){if(typeof af.addEventListener!=aq){af.addEventListener("load",a,false);}else{if(typeof aL.addEventListener!=aq){aL.addEventListener("load",a,false);}else{if(typeof af.attachEvent!=aq){aM(af,"onload",a);}else{if(typeof af.onload=="function"){var b=af.onload;af.onload=function(){b();a();};}else{af.onload=a;}}}}}function aN(){if(aa){Y();}else{am();}}function Y(){var d=aL.getElementsByTagName("body")[0];var b=ar(aD);b.setAttribute("type",aE);var a=d.appendChild(b);if(a){var c=0;(function(){if(typeof a.GetVariable!=aq){var e=a.GetVariable("$version");if(e){e=e.split(" ")[1].split(",");ah.pv=[parseInt(e[0],10),parseInt(e[1],10),parseInt(e[2],10)];}}else{if(c<10){c++;setTimeout(arguments.callee,10);return;}}d.removeChild(b);a=null;am();})();}else{am();}}function am(){var g=aG.length;if(g>0){for(var h=0;h<g;h++){var c=aG[h].id;var l=aG[h].callbackFn;var a={success:false,id:c};if(ah.pv[0]>0){var i=aS(c);if(i){if(ao(aG[h].swfVersion)&&!(ah.wk&&ah.wk<312)){ay(c,true);if(l){a.success=true;a.ref=av(c);l(a);}}else{if(aG[h].expressInstall&&au()){var e={};e.data=aG[h].expressInstall;e.width=i.getAttribute("width")||"0";e.height=i.getAttribute("height")||"0";if(i.getAttribute("class")){e.styleclass=i.getAttribute("class");}if(i.getAttribute("align")){e.align=i.getAttribute("align");}var f={};var d=i.getElementsByTagName("param");var k=d.length;for(var j=0;j<k;j++){if(d[j].getAttribute("name").toLowerCase()!="movie"){f[d[j].getAttribute("name")]=d[j].getAttribute("value");}}ae(e,f,c,l);}else{aF(i);if(l){l(a);}}}}}else{ay(c,true);if(l){var b=av(c);if(b&&typeof b.SetVariable!=aq){a.success=true;a.ref=b;}l(a);}}}}}function av(b){var d=null;var c=aS(b);if(c&&c.nodeName=="OBJECT"){if(typeof c.SetVariable!=aq){d=c;}else{var a=c.getElementsByTagName(aD)[0];if(a){d=a;}}}return d;}function au(){return !aU&&ao("6.0.65")&&(ah.win||ah.mac)&&!(ah.wk&&ah.wk<312);}function ae(f,d,h,e){aU=true;ap=e||null;at={success:false,id:h};var a=aS(h);if(a){if(a.nodeName=="OBJECT"){aJ=aO(a);ad=null;}else{aJ=a;ad=h;}f.id=ac;if(typeof f.width==aq||(!/%$/.test(f.width)&&parseInt(f.width,10)<310)){f.width="310";}if(typeof f.height==aq||(!/%$/.test(f.height)&&parseInt(f.height,10)<137)){f.height="137";}aL.title=aL.title.slice(0,47)+" - Flash Player Installation";var b=ah.ie&&ah.win?"ActiveX":"PlugIn",c="MMredirectURL="+af.location.toString().replace(/&/g,"%26")+"&MMplayerType="+b+"&MMdoctitle="+aL.title;if(typeof d.flashvars!=aq){d.flashvars+="&"+c;}else{d.flashvars=c;}if(ah.ie&&ah.win&&a.readyState!=4){var g=ar("div");h+="SWFObjectNew";g.setAttribute("id",h);a.parentNode.insertBefore(g,a);a.style.display="none";(function(){if(a.readyState==4){a.parentNode.removeChild(a);}else{setTimeout(arguments.callee,10);}})();}aA(f,d,h);}}function aF(a){if(ah.ie&&ah.win&&a.readyState!=4){var b=ar("div");a.parentNode.insertBefore(b,a);b.parentNode.replaceChild(aO(a),b);a.style.display="none";(function(){if(a.readyState==4){a.parentNode.removeChild(a);}else{setTimeout(arguments.callee,10);}})();}else{a.parentNode.replaceChild(aO(a),a);}}function aO(b){var d=ar("div");if(ah.win&&ah.ie){d.innerHTML=b.innerHTML;}else{var e=b.getElementsByTagName(aD)[0];if(e){var a=e.childNodes;if(a){var f=a.length;for(var c=0;c<f;c++){if(!(a[c].nodeType==1&&a[c].nodeName=="PARAM")&&!(a[c].nodeType==8)){d.appendChild(a[c].cloneNode(true));}}}}}return d;}function aA(e,g,c){var d,a=aS(c);if(ah.wk&&ah.wk<312){return d;}if(a){if(typeof e.id==aq){e.id=c;}if(ah.ie&&ah.win){var f="";for(var i in e){if(e[i]!=Object.prototype[i]){if(i.toLowerCase()=="data"){g.movie=e[i];}else{if(i.toLowerCase()=="styleclass"){f+=' class="'+e[i]+'"';}else{if(i.toLowerCase()!="classid"){f+=" "+i+'="'+e[i]+'"';}}}}}var h="";for(var j in g){if(g[j]!=Object.prototype[j]){h+='<param name="'+j+'" value="'+g[j]+'" />';}}a.outerHTML='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'+f+">"+h+"</object>";ag[ag.length]=e.id;d=aS(e.id);}else{var b=ar(aD);b.setAttribute("type",aE);for(var k in e){if(e[k]!=Object.prototype[k]){if(k.toLowerCase()=="styleclass"){b.setAttribute("class",e[k]);}else{if(k.toLowerCase()!="classid"){b.setAttribute(k,e[k]);}}}}for(var l in g){if(g[l]!=Object.prototype[l]&&l.toLowerCase()!="movie"){aQ(b,l,g[l]);}}a.parentNode.replaceChild(b,a);d=b;}}return d;}function aQ(b,d,c){var a=ar("param");a.setAttribute("name",d);a.setAttribute("value",c);b.appendChild(a);}function aw(a){var b=aS(a);if(b&&b.nodeName=="OBJECT"){if(ah.ie&&ah.win){b.style.display="none";(function(){if(b.readyState==4){aT(a);}else{setTimeout(arguments.callee,10);}})();}else{b.parentNode.removeChild(b);}}}function aT(a){var b=aS(a);if(b){for(var c in b){if(typeof b[c]=="function"){b[c]=null;}}b.parentNode.removeChild(b);}}function aS(a){var c=null;try{c=aL.getElementById(a);}catch(b){}return c;}function ar(a){return aL.createElement(a);}function aM(a,c,b){a.attachEvent(c,b);al[al.length]=[a,c,b];}function ao(a){var b=ah.pv,c=a.split(".");c[0]=parseInt(c[0],10);c[1]=parseInt(c[1],10)||0;c[2]=parseInt(c[2],10)||0;return(b[0]>c[0]||(b[0]==c[0]&&b[1]>c[1])||(b[0]==c[0]&&b[1]==c[1]&&b[2]>=c[2]))?true:false;}function az(b,f,a,c){if(ah.ie&&ah.mac){return;}var e=aL.getElementsByTagName("head")[0];if(!e){return;}var g=(a&&typeof a=="string")?a:"screen";if(c){aH=null;an=null;}if(!aH||an!=g){var d=ar("style");d.setAttribute("type","text/css");d.setAttribute("media",g);aH=e.appendChild(d);if(ah.ie&&ah.win&&typeof aL.styleSheets!=aq&&aL.styleSheets.length>0){aH=aL.styleSheets[aL.styleSheets.length-1];}an=g;}if(ah.ie&&ah.win){if(aH&&typeof aH.addRule==aD){aH.addRule(b,f);}}else{if(aH&&typeof aL.createTextNode!=aq){aH.appendChild(aL.createTextNode(b+" {"+f+"}"));}}}function ay(a,c){if(!aI){return;}var b=c?"visible":"hidden";if(ak&&aS(a)){aS(a).style.visibility=b;}else{az("#"+a,"visibility:"+b);}}function ai(b){var a=/[\\\"<>\.;]/;var c=a.exec(b)!=null;return c&&typeof encodeURIComponent!=aq?encodeURIComponent(b):b;}var aR=function(){if(ah.ie&&ah.win){window.attachEvent("onunload",function(){var a=al.length;for(var b=0;b<a;b++){al[b][0].detachEvent(al[b][1],al[b][2]);}var d=ag.length;for(var c=0;c<d;c++){aw(ag[c]);}for(var e in ah){ah[e]=null;}ah=null;for(var f in swfobject){swfobject[f]=null;}swfobject=null;});}}();return{registerObject:function(a,e,c,b){if(ah.w3&&a&&e){var d={};d.id=a;d.swfVersion=e;d.expressInstall=c;d.callbackFn=b;aG[aG.length]=d;ay(a,false);}else{if(b){b({success:false,id:a});}}},getObjectById:function(a){if(ah.w3){return av(a);}},embedSWF:function(k,e,h,f,c,a,b,i,g,j){var d={success:false,id:e};if(ah.w3&&!(ah.wk&&ah.wk<312)&&k&&e&&h&&f&&c){ay(e,false);aj(function(){h+="";f+="";var q={};if(g&&typeof g===aD){for(var o in g){q[o]=g[o];}}q.data=k;q.width=h;q.height=f;var n={};if(i&&typeof i===aD){for(var p in i){n[p]=i[p];}}if(b&&typeof b===aD){for(var l in b){if(typeof n.flashvars!=aq){n.flashvars+="&"+l+"="+b[l];}else{n.flashvars=l+"="+b[l];}}}if(ao(c)){var m=aA(q,n,e);if(q.id==e){ay(e,true);}d.success=true;d.ref=m;}else{if(a&&au()){q.data=a;ae(q,n,e,j);return;}else{ay(e,true);}}if(j){j(d);}});}else{if(j){j(d);}}},switchOffAutoHideShow:function(){aI=false;},ua:ah,getFlashPlayerVersion:function(){return{major:ah.pv[0],minor:ah.pv[1],release:ah.pv[2]};},hasFlashPlayerVersion:ao,createSWF:function(a,b,c){if(ah.w3){return aA(a,b,c);}else{return undefined;}},showExpressInstall:function(b,a,d,c){if(ah.w3&&au()){ae(b,a,d,c);}},removeSWF:function(a){if(ah.w3){aw(a);}},createCSS:function(b,a,c,d){if(ah.w3){az(b,a,c,d);}},addDomLoadEvent:aj,addLoadEvent:aC,getQueryParamValue:function(b){var a=aL.location.search||aL.location.hash;if(a){if(/\?/.test(a)){a=a.split("?")[1];}if(b==null){return ai(a);}var c=a.split("&");for(var d=0;d<c.length;d++){if(c[d].substring(0,c[d].indexOf("="))==b){return ai(c[d].substring((c[d].indexOf("=")+1)));}}}return"";},expressInstallCallback:function(){if(aU){var a=aS(ac);if(a&&aJ){a.parentNode.replaceChild(aJ,a);if(ad){ay(ad,true);if(ah.ie&&ah.win){aJ.style.display="block";}}if(ap){ap(at);}}aU=false;}}};}();var SWFUpload;if(SWFUpload==undefined){SWFUpload=function(b){this.initSWFUpload(b);};}SWFUpload.prototype.initSWFUpload=function(c){try{this.customSettings={};this.settings=c;this.eventQueue=[];this.movieName="SWFUpload_"+SWFUpload.movieCount++;this.movieElement=null;SWFUpload.instances[this.movieName]=this;this.initSettings();this.loadFlash();this.displayDebugInfo();}catch(d){delete SWFUpload.instances[this.movieName];throw d;}};SWFUpload.instances={};SWFUpload.movieCount=0;SWFUpload.version="2.2.0 2009-03-25";SWFUpload.QUEUE_ERROR={QUEUE_LIMIT_EXCEEDED:-100,FILE_EXCEEDS_SIZE_LIMIT:-110,ZERO_BYTE_FILE:-120,INVALID_FILETYPE:-130};SWFUpload.UPLOAD_ERROR={HTTP_ERROR:-200,MISSING_UPLOAD_URL:-210,IO_ERROR:-220,SECURITY_ERROR:-230,UPLOAD_LIMIT_EXCEEDED:-240,UPLOAD_FAILED:-250,SPECIFIED_FILE_ID_NOT_FOUND:-260,FILE_VALIDATION_FAILED:-270,FILE_CANCELLED:-280,UPLOAD_STOPPED:-290};SWFUpload.FILE_STATUS={QUEUED:-1,IN_PROGRESS:-2,ERROR:-3,COMPLETE:-4,CANCELLED:-5};SWFUpload.BUTTON_ACTION={SELECT_FILE:-100,SELECT_FILES:-110,START_UPLOAD:-120};SWFUpload.CURSOR={ARROW:-1,HAND:-2};SWFUpload.WINDOW_MODE={WINDOW:"window",TRANSPARENT:"transparent",OPAQUE:"opaque"};SWFUpload.completeURL=function(e){if(typeof(e)!=="string"||e.match(/^https?:\/\//i)||e.match(/^\//)){return e;}var f=window.location.protocol+"//"+window.location.hostname+(window.location.port?":"+window.location.port:"");var d=window.location.pathname.lastIndexOf("/");if(d<=0){path="/";}else{path=window.location.pathname.substr(0,d)+"/";}return path+e;};SWFUpload.prototype.initSettings=function(){this.ensureDefault=function(c,d){this.settings[c]=(this.settings[c]==undefined)?d:this.settings[c];};this.ensureDefault("upload_url","");this.ensureDefault("preserve_relative_urls",false);this.ensureDefault("file_post_name","Filedata");this.ensureDefault("post_params",{});this.ensureDefault("use_query_string",false);this.ensureDefault("requeue_on_error",false);this.ensureDefault("http_success",[]);this.ensureDefault("assume_success_timeout",0);this.ensureDefault("file_types","*.*");this.ensureDefault("file_types_description","All Files");this.ensureDefault("file_size_limit",0);this.ensureDefault("file_upload_limit",0);this.ensureDefault("file_queue_limit",0);this.ensureDefault("flash_url","swfupload.swf");this.ensureDefault("prevent_swf_caching",true);this.ensureDefault("button_image_url","");this.ensureDefault("button_width",1);this.ensureDefault("button_height",1);this.ensureDefault("button_text","");this.ensureDefault("button_text_style","color: #000000; font-size: 16pt;");this.ensureDefault("button_text_top_padding",0);this.ensureDefault("button_text_left_padding",0);this.ensureDefault("button_action",SWFUpload.BUTTON_ACTION.SELECT_FILES);this.ensureDefault("button_disabled",false);this.ensureDefault("button_placeholder_id","");this.ensureDefault("button_placeholder",null);this.ensureDefault("button_cursor",SWFUpload.CURSOR.ARROW);this.ensureDefault("button_window_mode",SWFUpload.WINDOW_MODE.WINDOW);this.ensureDefault("debug",false);this.settings.debug_enabled=this.settings.debug;this.settings.return_upload_start_handler=this.returnUploadStart;this.ensureDefault("swfupload_loaded_handler",null);this.ensureDefault("file_dialog_start_handler",null);this.ensureDefault("file_queued_handler",null);this.ensureDefault("file_queue_error_handler",null);this.ensureDefault("file_dialog_complete_handler",null);this.ensureDefault("upload_start_handler",null);this.ensureDefault("upload_progress_handler",null);this.ensureDefault("upload_error_handler",null);this.ensureDefault("upload_success_handler",null);this.ensureDefault("upload_complete_handler",null);this.ensureDefault("debug_handler",this.debugMessage);this.ensureDefault("custom_settings",{});this.customSettings=this.settings.custom_settings;if(!!this.settings.prevent_swf_caching){this.settings.flash_url=this.settings.flash_url+(this.settings.flash_url.indexOf("?")<0?"?":"&")+"preventswfcaching="+new Date().getTime();}if(!this.settings.preserve_relative_urls){this.settings.upload_url=SWFUpload.completeURL(this.settings.upload_url);this.settings.button_image_url=SWFUpload.completeURL(this.settings.button_image_url);}delete this.ensureDefault;};SWFUpload.prototype.loadFlash=function(){var d,c;if(document.getElementById(this.movieName)!==null){throw"ID "+this.movieName+" is already in use. The Flash Object could not be added";}d=document.getElementById(this.settings.button_placeholder_id)||this.settings.button_placeholder;if(d==undefined){throw"Could not find the placeholder element: "+this.settings.button_placeholder_id;}c=document.createElement("div");c.innerHTML=this.getFlashHTML();d.parentNode.replaceChild(c.firstChild,d);if(window[this.movieName]==undefined){window[this.movieName]=this.getMovieElement();}};SWFUpload.prototype.getFlashHTML=function(){return['<object id="',this.movieName,'" type="application/x-shockwave-flash" data="',this.settings.flash_url,'" width="',this.settings.button_width,'" height="',this.settings.button_height,'" class="swfupload">','<param name="wmode" value="',this.settings.button_window_mode,'" />','<param name="movie" value="',this.settings.flash_url,'" />','<param name="quality" value="high" />','<param name="menu" value="false" />','<param name="allowScriptAccess" value="always" />','<param name="flashvars" value="'+this.getFlashVars()+'" />',"</object>"].join("");};SWFUpload.prototype.getFlashVars=function(){var c=this.buildParamString();var d=this.settings.http_success.join(",");return["movieName=",encodeURIComponent(this.movieName),"&amp;uploadURL=",encodeURIComponent(this.settings.upload_url),"&amp;useQueryString=",encodeURIComponent(this.settings.use_query_string),"&amp;requeueOnError=",encodeURIComponent(this.settings.requeue_on_error),"&amp;httpSuccess=",encodeURIComponent(d),"&amp;assumeSuccessTimeout=",encodeURIComponent(this.settings.assume_success_timeout),"&amp;params=",encodeURIComponent(c),"&amp;filePostName=",encodeURIComponent(this.settings.file_post_name),"&amp;fileTypes=",encodeURIComponent(this.settings.file_types),"&amp;fileTypesDescription=",encodeURIComponent(this.settings.file_types_description),"&amp;fileSizeLimit=",encodeURIComponent(this.settings.file_size_limit),"&amp;fileUploadLimit=",encodeURIComponent(this.settings.file_upload_limit),"&amp;fileQueueLimit=",encodeURIComponent(this.settings.file_queue_limit),"&amp;debugEnabled=",encodeURIComponent(this.settings.debug_enabled),"&amp;buttonImageURL=",encodeURIComponent(this.settings.button_image_url),"&amp;buttonWidth=",encodeURIComponent(this.settings.button_width),"&amp;buttonHeight=",encodeURIComponent(this.settings.button_height),"&amp;buttonText=",encodeURIComponent(this.settings.button_text),"&amp;buttonTextTopPadding=",encodeURIComponent(this.settings.button_text_top_padding),"&amp;buttonTextLeftPadding=",encodeURIComponent(this.settings.button_text_left_padding),"&amp;buttonTextStyle=",encodeURIComponent(this.settings.button_text_style),"&amp;buttonAction=",encodeURIComponent(this.settings.button_action),"&amp;buttonDisabled=",encodeURIComponent(this.settings.button_disabled),"&amp;buttonCursor=",encodeURIComponent(this.settings.button_cursor)].join("");};SWFUpload.prototype.getMovieElement=function(){if(this.movieElement==undefined){this.movieElement=document.getElementById(this.movieName);}if(this.movieElement===null){throw"Could not find Flash element";}return this.movieElement;};SWFUpload.prototype.buildParamString=function(){var f=this.settings.post_params;var d=[];if(typeof(f)==="object"){for(var e in f){if(f.hasOwnProperty(e)){d.push(encodeURIComponent(e.toString())+"="+encodeURIComponent(f[e].toString()));}}}return d.join("&amp;");};SWFUpload.prototype.destroy=function(){try{this.cancelUpload(null,false);var g=null;g=this.getMovieElement();if(g&&typeof(g.CallFunction)==="unknown"){for(var j in g){try{if(typeof(g[j])==="function"){g[j]=null;}}catch(h){}}try{g.parentNode.removeChild(g);}catch(f){}}window[this.movieName]=null;SWFUpload.instances[this.movieName]=null;delete SWFUpload.instances[this.movieName];this.movieElement=null;this.settings=null;this.customSettings=null;this.eventQueue=null;this.movieName=null;return true;}catch(i){return false;}};SWFUpload.prototype.displayDebugInfo=function(){this.debug(["---SWFUpload Instance Info---\n","Version: ",SWFUpload.version,"\n","Movie Name: ",this.movieName,"\n","Settings:\n","\t","upload_url:               ",this.settings.upload_url,"\n","\t","flash_url:                ",this.settings.flash_url,"\n","\t","use_query_string:         ",this.settings.use_query_string.toString(),"\n","\t","requeue_on_error:         ",this.settings.requeue_on_error.toString(),"\n","\t","http_success:             ",this.settings.http_success.join(", "),"\n","\t","assume_success_timeout:   ",this.settings.assume_success_timeout,"\n","\t","file_post_name:           ",this.settings.file_post_name,"\n","\t","post_params:              ",this.settings.post_params.toString(),"\n","\t","file_types:               ",this.settings.file_types,"\n","\t","file_types_description:   ",this.settings.file_types_description,"\n","\t","file_size_limit:          ",this.settings.file_size_limit,"\n","\t","file_upload_limit:        ",this.settings.file_upload_limit,"\n","\t","file_queue_limit:         ",this.settings.file_queue_limit,"\n","\t","debug:                    ",this.settings.debug.toString(),"\n","\t","prevent_swf_caching:      ",this.settings.prevent_swf_caching.toString(),"\n","\t","button_placeholder_id:    ",this.settings.button_placeholder_id.toString(),"\n","\t","button_placeholder:       ",(this.settings.button_placeholder?"Set":"Not Set"),"\n","\t","button_image_url:         ",this.settings.button_image_url.toString(),"\n","\t","button_width:             ",this.settings.button_width.toString(),"\n","\t","button_height:            ",this.settings.button_height.toString(),"\n","\t","button_text:              ",this.settings.button_text.toString(),"\n","\t","button_text_style:        ",this.settings.button_text_style.toString(),"\n","\t","button_text_top_padding:  ",this.settings.button_text_top_padding.toString(),"\n","\t","button_text_left_padding: ",this.settings.button_text_left_padding.toString(),"\n","\t","button_action:            ",this.settings.button_action.toString(),"\n","\t","button_disabled:          ",this.settings.button_disabled.toString(),"\n","\t","custom_settings:          ",this.settings.custom_settings.toString(),"\n","Event Handlers:\n","\t","swfupload_loaded_handler assigned:  ",(typeof this.settings.swfupload_loaded_handler==="function").toString(),"\n","\t","file_dialog_start_handler assigned: ",(typeof this.settings.file_dialog_start_handler==="function").toString(),"\n","\t","file_queued_handler assigned:       ",(typeof this.settings.file_queued_handler==="function").toString(),"\n","\t","file_queue_error_handler assigned:  ",(typeof this.settings.file_queue_error_handler==="function").toString(),"\n","\t","upload_start_handler assigned:      ",(typeof this.settings.upload_start_handler==="function").toString(),"\n","\t","upload_progress_handler assigned:   ",(typeof this.settings.upload_progress_handler==="function").toString(),"\n","\t","upload_error_handler assigned:      ",(typeof this.settings.upload_error_handler==="function").toString(),"\n","\t","upload_success_handler assigned:    ",(typeof this.settings.upload_success_handler==="function").toString(),"\n","\t","upload_complete_handler assigned:   ",(typeof this.settings.upload_complete_handler==="function").toString(),"\n","\t","debug_handler assigned:             ",(typeof this.settings.debug_handler==="function").toString(),"\n"].join(""));};SWFUpload.prototype.addSetting=function(d,f,e){if(f==undefined){return(this.settings[d]=e);}else{return(this.settings[d]=f);}};SWFUpload.prototype.getSetting=function(b){if(this.settings[b]!=undefined){return this.settings[b];}return"";};SWFUpload.prototype.callFlash=function(functionName,argumentArray){argumentArray=argumentArray||[];var movieElement=this.getMovieElement();var returnValue,returnString;try{returnString=movieElement.CallFunction('<invoke name="'+functionName+'" returntype="javascript">'+__flash__argumentsToXML(argumentArray,0)+"</invoke>");returnValue=eval(returnString);}catch(ex){throw"Call to "+functionName+" failed";}if(returnValue!=undefined&&typeof returnValue.post==="object"){returnValue=this.unescapeFilePostParams(returnValue);}return returnValue;};SWFUpload.prototype.selectFile=function(){this.callFlash("SelectFile");};SWFUpload.prototype.selectFiles=function(){this.callFlash("SelectFiles");};SWFUpload.prototype.startUpload=function(b){this.callFlash("StartUpload",[b]);};SWFUpload.prototype.cancelUpload=function(d,c){if(c!==false){c=true;}this.callFlash("CancelUpload",[d,c]);};SWFUpload.prototype.stopUpload=function(){this.callFlash("StopUpload");};SWFUpload.prototype.getStats=function(){return this.callFlash("GetStats");};SWFUpload.prototype.setStats=function(b){this.callFlash("SetStats",[b]);};SWFUpload.prototype.getFile=function(b){if(typeof(b)==="number"){return this.callFlash("GetFileByIndex",[b]);}else{return this.callFlash("GetFile",[b]);}};SWFUpload.prototype.addFileParam=function(e,d,f){return this.callFlash("AddFileParam",[e,d,f]);};SWFUpload.prototype.removeFileParam=function(d,c){this.callFlash("RemoveFileParam",[d,c]);};SWFUpload.prototype.setUploadURL=function(b){this.settings.upload_url=b.toString();this.callFlash("SetUploadURL",[b]);};SWFUpload.prototype.setPostParams=function(b){this.settings.post_params=b;this.callFlash("SetPostParams",[b]);};SWFUpload.prototype.addPostParam=function(d,c){this.settings.post_params[d]=c;this.callFlash("SetPostParams",[this.settings.post_params]);};SWFUpload.prototype.removePostParam=function(b){delete this.settings.post_params[b];this.callFlash("SetPostParams",[this.settings.post_params]);};SWFUpload.prototype.setFileTypes=function(d,c){this.settings.file_types=d;this.settings.file_types_description=c;this.callFlash("SetFileTypes",[d,c]);};SWFUpload.prototype.setFileSizeLimit=function(b){this.settings.file_size_limit=b;this.callFlash("SetFileSizeLimit",[b]);};SWFUpload.prototype.setFileUploadLimit=function(b){this.settings.file_upload_limit=b;this.callFlash("SetFileUploadLimit",[b]);};SWFUpload.prototype.setFileQueueLimit=function(b){this.settings.file_queue_limit=b;this.callFlash("SetFileQueueLimit",[b]);};SWFUpload.prototype.setFilePostName=function(b){this.settings.file_post_name=b;this.callFlash("SetFilePostName",[b]);};SWFUpload.prototype.setUseQueryString=function(b){this.settings.use_query_string=b;this.callFlash("SetUseQueryString",[b]);};SWFUpload.prototype.setRequeueOnError=function(b){this.settings.requeue_on_error=b;this.callFlash("SetRequeueOnError",[b]);};SWFUpload.prototype.setHTTPSuccess=function(b){if(typeof b==="string"){b=b.replace(" ","").split(",");}this.settings.http_success=b;this.callFlash("SetHTTPSuccess",[b]);};SWFUpload.prototype.setAssumeSuccessTimeout=function(b){this.settings.assume_success_timeout=b;this.callFlash("SetAssumeSuccessTimeout",[b]);};SWFUpload.prototype.setDebugEnabled=function(b){this.settings.debug_enabled=b;this.callFlash("SetDebugEnabled",[b]);};SWFUpload.prototype.setButtonImageURL=function(b){if(b==undefined){b="";}this.settings.button_image_url=b;this.callFlash("SetButtonImageURL",[b]);};SWFUpload.prototype.setButtonDimensions=function(f,e){this.settings.button_width=f;this.settings.button_height=e;var d=this.getMovieElement();if(d!=undefined){d.style.width=f+"px";d.style.height=e+"px";}this.callFlash("SetButtonDimensions",[f,e]);};SWFUpload.prototype.setButtonText=function(b){this.settings.button_text=b;this.callFlash("SetButtonText",[b]);};SWFUpload.prototype.setButtonTextPadding=function(c,d){this.settings.button_text_top_padding=d;this.settings.button_text_left_padding=c;this.callFlash("SetButtonTextPadding",[c,d]);};SWFUpload.prototype.setButtonTextStyle=function(b){this.settings.button_text_style=b;this.callFlash("SetButtonTextStyle",[b]);};SWFUpload.prototype.setButtonDisabled=function(b){this.settings.button_disabled=b;this.callFlash("SetButtonDisabled",[b]);};SWFUpload.prototype.setButtonAction=function(b){this.settings.button_action=b;this.callFlash("SetButtonAction",[b]);};SWFUpload.prototype.setButtonCursor=function(b){this.settings.button_cursor=b;this.callFlash("SetButtonCursor",[b]);};SWFUpload.prototype.queueEvent=function(d,f){if(f==undefined){f=[];}else{if(!(f instanceof Array)){f=[f];}}var e=this;if(typeof this.settings[d]==="function"){this.eventQueue.push(function(){this.settings[d].apply(this,f);});setTimeout(function(){e.executeNextEvent();},0);}else{if(this.settings[d]!==null){throw"Event handler "+d+" is unknown or is not a function";}}};SWFUpload.prototype.executeNextEvent=function(){var b=this.eventQueue?this.eventQueue.shift():null;if(typeof(b)==="function"){b.apply(this);}};SWFUpload.prototype.unescapeFilePostParams=function(l){var j=/[$]([0-9a-f]{4})/i;var i={};var k;if(l!=undefined){for(var h in l.post){if(l.post.hasOwnProperty(h)){k=h;var g;while((g=j.exec(k))!==null){k=k.replace(g[0],String.fromCharCode(parseInt("0x"+g[1],16)));}i[k]=l.post[h];}}l.post=i;}return l;};SWFUpload.prototype.testExternalInterface=function(){try{return this.callFlash("TestExternalInterface");}catch(b){return false;}};SWFUpload.prototype.flashReady=function(){var b=this.getMovieElement();if(!b){this.debug("Flash called back ready but the flash movie can't be found.");return;}this.cleanUp(b);this.queueEvent("swfupload_loaded_handler");};SWFUpload.prototype.cleanUp=function(f){try{if(this.movieElement&&typeof(f.CallFunction)==="unknown"){this.debug("Removing Flash functions hooks (this should only run in IE and should prevent memory leaks)");for(var h in f){try{if(typeof(f[h])==="function"){f[h]=null;}}catch(e){}}}}catch(g){}window.__flash__removeCallback=function(c,b){try{if(c){c[b]=null;}}catch(a){}};};SWFUpload.prototype.fileDialogStart=function(){this.queueEvent("file_dialog_start_handler");};SWFUpload.prototype.fileQueued=function(b){b=this.unescapeFilePostParams(b);this.queueEvent("file_queued_handler",b);};SWFUpload.prototype.fileQueueError=function(e,f,d){e=this.unescapeFilePostParams(e);this.queueEvent("file_queue_error_handler",[e,f,d]);};SWFUpload.prototype.fileDialogComplete=function(d,f,e){this.queueEvent("file_dialog_complete_handler",[d,f,e]);};SWFUpload.prototype.uploadStart=function(b){b=this.unescapeFilePostParams(b);this.queueEvent("return_upload_start_handler",b);};SWFUpload.prototype.returnUploadStart=function(d){var c;if(typeof this.settings.upload_start_handler==="function"){d=this.unescapeFilePostParams(d);c=this.settings.upload_start_handler.call(this,d);}else{if(this.settings.upload_start_handler!=undefined){throw"upload_start_handler must be a function";}}if(c===undefined){c=true;}c=!!c;this.callFlash("ReturnUploadStart",[c]);};SWFUpload.prototype.uploadProgress=function(e,f,d){e=this.unescapeFilePostParams(e);this.queueEvent("upload_progress_handler",[e,f,d]);};SWFUpload.prototype.uploadError=function(e,f,d){e=this.unescapeFilePostParams(e);this.queueEvent("upload_error_handler",[e,f,d]);};SWFUpload.prototype.uploadSuccess=function(d,e,f){d=this.unescapeFilePostParams(d);this.queueEvent("upload_success_handler",[d,e,f]);};SWFUpload.prototype.uploadComplete=function(b){b=this.unescapeFilePostParams(b);this.queueEvent("upload_complete_handler",b);};SWFUpload.prototype.debug=function(b){this.queueEvent("debug_handler",b);};SWFUpload.prototype.debugMessage=function(h){if(this.settings.debug){var f,g=[];if(typeof h==="object"&&typeof h.name==="string"&&typeof h.message==="string"){for(var e in h){if(h.hasOwnProperty(e)){g.push(e+": "+h[e]);}}f=g.join("\n")||"";g=f.split("\n");f="EXCEPTION: "+g.join("\nEXCEPTION: ");SWFUpload.Console.writeLine(f);}else{SWFUpload.Console.writeLine(h);}}};SWFUpload.Console={};SWFUpload.Console.writeLine=function(g){var e,f;try{e=document.getElementById("SWFUpload_Console");if(!e){f=document.createElement("form");document.getElementsByTagName("body")[0].appendChild(f);e=document.createElement("textarea");e.id="SWFUpload_Console";e.style.fontFamily="monospace";e.setAttribute("wrap","off");e.wrap="off";e.style.overflow="auto";e.style.width="700px";e.style.height="350px";e.style.margin="5px";f.appendChild(e);}e.value+=g+"\n";e.scrollTop=e.scrollHeight-e.clientHeight;}catch(h){alert("Exception: "+h.name+" Message: "+h.message);}};(function(c){var b={init:function(d,e){return this.each(function(){var n=c(this);var m=n.clone();var j=c.extend({id:n.attr("id"),swf:"uploadify.swf",uploader:"uploadify.php",auto:true,buttonClass:"",buttonCursor:"hand",buttonImage:null,buttonText:"SELECT FILES",checkExisting:false,debug:false,fileObjName:"Filedata",fileSizeLimit:0,fileTypeDesc:"All Files",fileTypeExts:"*.*",height:30,itemTemplate:false,method:"post",multi:true,formData:{},preventCaching:true,progressData:"percentage",queueID:false,queueSizeLimit:999,removeCompleted:true,removeTimeout:3,requeueErrors:false,successTimeout:30,uploadLimit:0,width:120,overrideEvents:[]},d);var g={assume_success_timeout:j.successTimeout,button_placeholder_id:j.id,button_width:j.width,button_height:j.height,button_text:null,button_text_style:null,button_text_top_padding:0,button_text_left_padding:0,button_action:(j.multi?SWFUpload.BUTTON_ACTION.SELECT_FILES:SWFUpload.BUTTON_ACTION.SELECT_FILE),button_disabled:false,button_cursor:(j.buttonCursor=="arrow"?SWFUpload.CURSOR.ARROW:SWFUpload.CURSOR.HAND),button_window_mode:SWFUpload.WINDOW_MODE.TRANSPARENT,debug:j.debug,requeue_on_error:j.requeueErrors,file_post_name:j.fileObjName,file_size_limit:j.fileSizeLimit,file_types:j.fileTypeExts,file_types_description:j.fileTypeDesc,file_queue_limit:j.queueSizeLimit,file_upload_limit:j.uploadLimit,flash_url:j.swf,prevent_swf_caching:j.preventCaching,post_params:j.formData,upload_url:j.uploader,use_query_string:(j.method=="get"),file_dialog_complete_handler:a.onDialogClose,file_dialog_start_handler:a.onDialogOpen,file_queued_handler:a.onSelect,file_queue_error_handler:a.onSelectError,swfupload_loaded_handler:j.onSWFReady,upload_complete_handler:a.onUploadComplete,upload_error_handler:a.onUploadError,upload_progress_handler:a.onUploadProgress,upload_start_handler:a.onUploadStart,upload_success_handler:a.onUploadSuccess};if(e){g=c.extend(g,e);}g=c.extend(g,j);var o=swfobject.getFlashPlayerVersion();var h=(o.major>=9);if(h){window["uploadify_"+j.id]=new SWFUpload(g);var i=window["uploadify_"+j.id];n.data("uploadify",i);var l=c("<div />",{id:j.id,"class":"uploadify",css:{height:j.height+"px",width:j.width+"px"}});c("#"+i.movieName).wrap(l);l=c("#"+j.id);l.data("uploadify",i);var f=c("<div />",{id:j.id+"-button","class":"uploadify-button "+j.buttonClass});if(j.buttonImage){f.css({"background-image":"url('"+j.buttonImage+"')","text-indent":"-9999px"});}f.html('<span class="uploadify-button-text">'+j.buttonText+"</span>").css({height:j.height+"px","line-height":j.height+"px",width:j.width+"px"});l.append(f);c("#"+i.movieName).css({position:"absolute","z-index":1});if(!j.queueID){var k=c("<div />",{id:j.id+"-queue","class":"uploadify-queue"});l.after(k);i.settings.queueID=j.id+"-queue";i.settings.defaultQueue=true;}i.queueData={files:{},filesSelected:0,filesQueued:0,filesReplaced:0,filesCancelled:0,filesErrored:0,uploadsSuccessful:0,uploadsErrored:0,averageSpeed:0,queueLength:0,queueSize:0,uploadSize:0,queueBytesUploaded:0,uploadQueue:[],errorMsg:"Some files were not added to the queue:"};i.original=m;i.wrapper=l;i.button=f;i.queue=k;if(j.onInit){j.onInit.call(n,i);}}else{if(j.onFallback){j.onFallback.call(n);}}});},cancel:function(d,f){var e=arguments;this.each(function(){var l=c(this),i=l.data("uploadify"),j=i.settings,h=-1;if(e[0]){if(e[0]=="*"){var g=i.queueData.queueLength;c("#"+j.queueID).find(".uploadify-queue-item").each(function(){h++;if(e[1]===true){i.cancelUpload(c(this).attr("id"),false);}else{i.cancelUpload(c(this).attr("id"));}c(this).find(".data").removeClass("data").html(" - Cancelled");c(this).find(".uploadify-progress-bar").remove();c(this).delay(1000+100*h).fadeOut(500,function(){c(this).remove();});});i.queueData.queueSize=0;i.queueData.queueLength=0;if(j.onClearQueue){j.onClearQueue.call(l,g);}}else{for(var m=0;m<e.length;m++){i.cancelUpload(e[m]);c("#"+e[m]).find(".data").removeClass("data").html(" - Cancelled");c("#"+e[m]).find(".uploadify-progress-bar").remove();c("#"+e[m]).delay(1000+100*m).fadeOut(500,function(){c(this).remove();});}}}else{var k=c("#"+j.queueID).find(".uploadify-queue-item").get(0);$item=c(k);i.cancelUpload($item.attr("id"));$item.find(".data").removeClass("data").html(" - Cancelled");$item.find(".uploadify-progress-bar").remove();$item.delay(1000).fadeOut(500,function(){c(this).remove();});}});},destroy:function(){this.each(function(){var f=c(this),d=f.data("uploadify"),e=d.settings;d.destroy();if(e.defaultQueue){c("#"+e.queueID).remove();}c("#"+e.id).replaceWith(d.original);if(e.onDestroy){e.onDestroy.call(this);}delete d;});},disable:function(d){this.each(function(){var g=c(this),e=g.data("uploadify"),f=e.settings;if(d){e.button.addClass("disabled");if(f.onDisable){f.onDisable.call(this);}}else{e.button.removeClass("disabled");if(f.onEnable){f.onEnable.call(this);}}e.setButtonDisabled(d);});},settings:function(e,g,h){var d=arguments;var f=g;this.each(function(){var k=c(this),i=k.data("uploadify"),j=i.settings;if(typeof(d[0])=="object"){for(var l in g){setData(l,g[l]);}}if(d.length===1){f=j[e];}else{switch(e){case"uploader":i.setUploadURL(g);break;case"formData":if(!h){g=c.extend(j.formData,g);}i.setPostParams(j.formData);break;case"method":if(g=="get"){i.setUseQueryString(true);}else{i.setUseQueryString(false);}break;case"fileObjName":i.setFilePostName(g);break;case"fileTypeExts":i.setFileTypes(g,j.fileTypeDesc);break;case"fileTypeDesc":i.setFileTypes(j.fileTypeExts,g);break;case"fileSizeLimit":i.setFileSizeLimit(g);break;case"uploadLimit":i.setFileUploadLimit(g);break;case"queueSizeLimit":i.setFileQueueLimit(g);break;case"buttonImage":i.button.css("background-image",settingValue);break;case"buttonCursor":if(g=="arrow"){i.setButtonCursor(SWFUpload.CURSOR.ARROW);}else{i.setButtonCursor(SWFUpload.CURSOR.HAND);}break;case"buttonText":c("#"+j.id+"-button").find(".uploadify-button-text").html(g);break;case"width":i.setButtonDimensions(g,j.height);break;case"height":i.setButtonDimensions(j.width,g);break;case"multi":if(g){i.setButtonAction(SWFUpload.BUTTON_ACTION.SELECT_FILES);}else{i.setButtonAction(SWFUpload.BUTTON_ACTION.SELECT_FILE);}break;}j[e]=g;}});if(d.length===1){return f;}},stop:function(){this.each(function(){var e=c(this),d=e.data("uploadify");d.queueData.averageSpeed=0;d.queueData.uploadSize=0;d.queueData.bytesUploaded=0;d.queueData.uploadQueue=[];d.stopUpload();});},upload:function(){var d=arguments;this.each(function(){var f=c(this),e=f.data("uploadify");e.queueData.averageSpeed=0;e.queueData.uploadSize=0;e.queueData.bytesUploaded=0;e.queueData.uploadQueue=[];if(d[0]){if(d[0]=="*"){e.queueData.uploadSize=e.queueData.queueSize;e.queueData.uploadQueue.push("*");e.startUpload();}else{for(var g=0;g<d.length;g++){e.queueData.uploadSize+=e.queueData.files[d[g]].size;e.queueData.uploadQueue.push(d[g]);}e.startUpload(e.queueData.uploadQueue.shift());}}else{e.startUpload();}});}};var a={onDialogOpen:function(){var d=this.settings;this.queueData.errorMsg="Some files were not added to the queue:";this.queueData.filesReplaced=0;this.queueData.filesCancelled=0;if(d.onDialogOpen){d.onDialogOpen.call(this);}},onDialogClose:function(d,f,g){var e=this.settings;this.queueData.filesErrored=d-f;this.queueData.filesSelected=d;this.queueData.filesQueued=f-this.queueData.filesCancelled;this.queueData.queueLength=g;if(c.inArray("onDialogClose",e.overrideEvents)<0){if(this.queueData.filesErrored>0){alert(this.queueData.errorMsg);}}if(e.onDialogClose){e.onDialogClose.call(this,this.queueData);}if(e.auto){c("#"+e.id).uploadify("upload","*");}},onSelect:function(h){var i=this.settings;var f={};for(var g in this.queueData.files){f=this.queueData.files[g];if(f.uploaded!=true&&f.name==h.name){var e=confirm('The file named "'+h.name+'" is already in the queue.\nDo you want to replace the existing item in the queue?');if(!e){this.cancelUpload(h.id);this.queueData.filesCancelled++;return false;}else{c("#"+f.id).remove();this.cancelUpload(f.id);this.queueData.filesReplaced++;}}}var j=Math.round(h.size/1024);var o="KB";if(j>1000){j=Math.round(j/1000);o="MB";}var l=j.toString().split(".");j=l[0];if(l.length>1){j+="."+l[1].substr(0,2);}j+=o;var k=h.name;if(k.length>25){k=k.substr(0,25)+"...";}itemData={fileID:h.id,instanceID:i.id,fileName:k,fileSize:j};if(i.itemTemplate==false){i.itemTemplate='<div id="${fileID}" class="uploadify-queue-item">					<div class="cancel">						<a href="javascript:$(\'#${instanceID}\').uploadify(\'cancel\', \'${fileID}\')">X</a>					</div>					<span class="fileName">${fileName} (${fileSize})</span><span class="data"></span>					<div class="uploadify-progress">						<div class="uploadify-progress-bar"><!--Progress Bar--></div>					</div>				</div>';}if(c.inArray("onSelect",i.overrideEvents)<0){itemHTML=i.itemTemplate;for(var m in itemData){itemHTML=itemHTML.replace(new RegExp("\\$\\{"+m+"\\}","g"),itemData[m]);}c("#"+i.queueID).append(itemHTML);}this.queueData.queueSize+=h.size;this.queueData.files[h.id]=h;if(i.onSelect){i.onSelect.apply(this,arguments);}},onSelectError:function(d,g,f){var e=this.settings;if(c.inArray("onSelectError",e.overrideEvents)<0){switch(g){case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:if(e.queueSizeLimit>f){this.queueData.errorMsg+="\nThe number of files selected exceeds the remaining upload limit ("+f+").";}else{this.queueData.errorMsg+="\nThe number of files selected exceeds the queue size limit ("+e.queueSizeLimit+").";}break;case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:this.queueData.errorMsg+='\nThe file "'+d.name+'" exceeds the size limit ('+e.fileSizeLimit+").";break;case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:this.queueData.errorMsg+='\nThe file "'+d.name+'" is empty.';break;case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:this.queueData.errorMsg+='\nThe file "'+d.name+'" is not an accepted file type ('+e.fileTypeDesc+").";break;}}if(g!=SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED){delete this.queueData.files[d.id];}if(e.onSelectError){e.onSelectError.apply(this,arguments);}},onQueueComplete:function(){if(this.settings.onQueueComplete){this.settings.onQueueComplete.call(this,this.settings.queueData);}},onUploadComplete:function(f){var g=this.settings,d=this;var e=this.getStats();this.queueData.queueLength=e.files_queued;if(this.queueData.uploadQueue[0]=="*"){if(this.queueData.queueLength>0){this.startUpload();}else{this.queueData.uploadQueue=[];if(g.onQueueComplete){g.onQueueComplete.call(this,this.queueData);}}}else{if(this.queueData.uploadQueue.length>0){this.startUpload(this.queueData.uploadQueue.shift());}else{this.queueData.uploadQueue=[];if(g.onQueueComplete){g.onQueueComplete.call(this,this.queueData);}}}if(c.inArray("onUploadComplete",g.overrideEvents)<0){if(g.removeCompleted){switch(f.filestatus){case SWFUpload.FILE_STATUS.COMPLETE:setTimeout(function(){if(c("#"+f.id)){d.queueData.queueSize-=f.size;d.queueData.queueLength-=1;delete d.queueData.files[f.id];c("#"+f.id).fadeOut(500,function(){c(this).remove();});}},g.removeTimeout*1000);break;case SWFUpload.FILE_STATUS.ERROR:if(!g.requeueErrors){setTimeout(function(){if(c("#"+f.id)){d.queueData.queueSize-=f.size;d.queueData.queueLength-=1;delete d.queueData.files[f.id];c("#"+f.id).fadeOut(500,function(){c(this).remove();});}},g.removeTimeout*1000);}break;}}else{f.uploaded=true;}}if(g.onUploadComplete){g.onUploadComplete.call(this,f);}},onUploadError:function(e,i,h){var f=this.settings;var g="Error";switch(i){case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:g="HTTP Error ("+h+")";break;case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:g="Missing Upload URL";break;case SWFUpload.UPLOAD_ERROR.IO_ERROR:g="IO Error";break;case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:g="Security Error";break;case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:alert("The upload limit has been reached ("+h+").");g="Exceeds Upload Limit";break;case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:g="Failed";break;case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:break;case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:g="Validation Error";break;case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:g="Cancelled";this.queueData.queueSize-=e.size;this.queueData.queueLength-=1;if(e.status==SWFUpload.FILE_STATUS.IN_PROGRESS||c.inArray(e.id,this.queueData.uploadQueue)>=0){this.queueData.uploadSize-=e.size;}if(f.onCancel){f.onCancel.call(this,e);}delete this.queueData.files[e.id];break;case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:g="Stopped";break;}if(c.inArray("onUploadError",f.overrideEvents)<0){if(i!=SWFUpload.UPLOAD_ERROR.FILE_CANCELLED&&i!=SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED){c("#"+e.id).addClass("uploadify-error");}c("#"+e.id).find(".uploadify-progress-bar").css("width","1px");if(i!=SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND&&e.status!=SWFUpload.FILE_STATUS.COMPLETE){c("#"+e.id).find(".data").html(" - "+g);}}var d=this.getStats();this.queueData.uploadsErrored=d.upload_errors;if(f.onUploadError){f.onUploadError.call(this,e,i,h,g);}},onUploadProgress:function(g,m,j){var h=this.settings;var e=new Date();var n=e.getTime();var k=n-this.timer;if(k>500){this.timer=n;}var i=m-this.bytesLoaded;this.bytesLoaded=m;var d=this.queueData.queueBytesUploaded+m;var p=Math.round(m/j*100);var o="KB/s";var l=0;var f=(i/1024)/(k/1000);f=Math.floor(f*10)/10;if(this.queueData.averageSpeed>0){this.queueData.averageSpeed=Math.floor((this.queueData.averageSpeed+f)/2);}else{this.queueData.averageSpeed=Math.floor(f);}if(f>1000){l=(f*0.001);this.queueData.averageSpeed=Math.floor(l);o="MB/s";}if(c.inArray("onUploadProgress",h.overrideEvents)<0){if(h.progressData=="percentage"){c("#"+g.id).find(".data").html(" - "+p+"%");}else{if(h.progressData=="speed"&&k>500){c("#"+g.id).find(".data").html(" - "+this.queueData.averageSpeed+o);}}c("#"+g.id).find(".uploadify-progress-bar").css("width",p+"%");}if(h.onUploadProgress){h.onUploadProgress.call(this,g,m,j,d,this.queueData.uploadSize);}},onUploadStart:function(d){var e=this.settings;var f=new Date();this.timer=f.getTime();this.bytesLoaded=0;if(this.queueData.uploadQueue.length==0){this.queueData.uploadSize=d.size;}if(e.checkExisting){c.ajax({type:"POST",async:false,url:e.checkExisting,data:{filename:d.name},success:function(h){if(h==1){var g=confirm('A file with the name "'+d.name+'" already exists on the server.\nWould you like to replace the existing file?');if(!g){this.cancelUpload(d.id);c("#"+d.id).remove();if(this.queueData.uploadQueue.length>0&&this.queueData.queueLength>0){if(this.queueData.uploadQueue[0]=="*"){this.startUpload();}else{this.startUpload(this.queueData.uploadQueue.shift());}}}}}});}if(e.onUploadStart){e.onUploadStart.call(this,d);}},onUploadSuccess:function(f,h,d){var g=this.settings;var e=this.getStats();this.queueData.uploadsSuccessful=e.successful_uploads;this.queueData.queueBytesUploaded+=f.size;if(c.inArray("onUploadSuccess",g.overrideEvents)<0){c("#"+f.id).find(".data").html(" - Complete");}if(g.onUploadSuccess){g.onUploadSuccess.call(this,f,h,d);}}};c.fn.uploadify=function(d){if(b[d]){return b[d].apply(this,Array.prototype.slice.call(arguments,1));}else{if(typeof d==="object"||!d){return b.init.apply(this,arguments);}else{c.error("The method "+d+" does not exist in $.uploadify");}}};})($);

/* WebUploader 0.1.5 */
!function(a,b){var c,d={},e=function(a,b){var c,d,e;if("string"==typeof a)return h(a);for(c=[],d=a.length,e=0;d>e;e++)c.push(h(a[e]));return b.apply(null,c)},f=function(a,b,c){2===arguments.length&&(c=b,b=null),e(b||[],function(){g(a,c,arguments)})},g=function(a,b,c){var f,g={exports:b};"function"==typeof b&&(c.length||(c=[e,g.exports,g]),f=b.apply(null,c),void 0!==f&&(g.exports=f)),d[a]=g.exports},h=function(b){var c=d[b]||a[b];if(!c)throw new Error("`"+b+"` is undefined");return c},i=function(a){var b,c,e,f,g,h;h=function(a){return a&&a.charAt(0).toUpperCase()+a.substr(1)};for(b in d)if(c=a,d.hasOwnProperty(b)){for(e=b.split("/"),g=h(e.pop());f=h(e.shift());)c[f]=c[f]||{},c=c[f];c[g]=d[b]}return a},j=function(c){return a.__dollar=c,i(b(a,f,e))};"object"==typeof module&&"object"==typeof module.exports?module.exports=j():"function"==typeof define&&define.amd?define(["jquery"],j):(c=a.WebUploader,a.WebUploader=j(),a.WebUploader.noConflict=function(){a.WebUploader=c})}(window,function(a,b,c){return b("dollar-third",[],function(){var b=a.__dollar||a.jQuery||a.Zepto;if(!b)throw new Error("jQuery or Zepto not found!");return b}),b("dollar",["dollar-third"],function(a){return a}),b("promise-third",["dollar"],function(a){return{Deferred:a.Deferred,when:a.when,isPromise:function(a){return a&&"function"==typeof a.then}}}),b("promise",["promise-third"],function(a){return a}),b("base",["dollar","promise"],function(b,c){function d(a){return function(){return h.apply(a,arguments)}}function e(a,b){return function(){return a.apply(b,arguments)}}function f(a){var b;return Object.create?Object.create(a):(b=function(){},b.prototype=a,new b)}var g=function(){},h=Function.call;return{version:"0.1.5",$:b,Deferred:c.Deferred,isPromise:c.isPromise,when:c.when,browser:function(a){var b={},c=a.match(/WebKit\/([\d.]+)/),d=a.match(/Chrome\/([\d.]+)/)||a.match(/CriOS\/([\d.]+)/),e=a.match(/MSIE\s([\d\.]+)/)||a.match(/(?:trident)(?:.*rv:([\w.]+))?/i),f=a.match(/Firefox\/([\d.]+)/),g=a.match(/Safari\/([\d.]+)/),h=a.match(/OPR\/([\d.]+)/);return c&&(b.webkit=parseFloat(c[1])),d&&(b.chrome=parseFloat(d[1])),e&&(b.ie=parseFloat(e[1])),f&&(b.firefox=parseFloat(f[1])),g&&(b.safari=parseFloat(g[1])),h&&(b.opera=parseFloat(h[1])),b}(navigator.userAgent),os:function(a){var b={},c=a.match(/(?:Android);?[\s\/]+([\d.]+)?/),d=a.match(/(?:iPad|iPod|iPhone).*OS\s([\d_]+)/);return c&&(b.android=parseFloat(c[1])),d&&(b.ios=parseFloat(d[1].replace(/_/g,"."))),b}(navigator.userAgent),inherits:function(a,c,d){var e;return"function"==typeof c?(e=c,c=null):e=c&&c.hasOwnProperty("constructor")?c.constructor:function(){return a.apply(this,arguments)},b.extend(!0,e,a,d||{}),e.__super__=a.prototype,e.prototype=f(a.prototype),c&&b.extend(!0,e.prototype,c),e},noop:g,bindFn:e,log:function(){return a.console?e(console.log,console):g}(),nextTick:function(){return function(a){setTimeout(a,1)}}(),slice:d([].slice),guid:function(){var a=0;return function(b){for(var c=(+new Date).toString(32),d=0;5>d;d++)c+=Math.floor(65535*Math.random()).toString(32);return(b||"wu_")+c+(a++).toString(32)}}(),formatSize:function(a,b,c){var d;for(c=c||["B","K","M","G","TB"];(d=c.shift())&&a>1024;)a/=1024;return("B"===d?a:a.toFixed(b||2))+d}}}),b("mediator",["base"],function(a){function b(a,b,c,d){return f.grep(a,function(a){return!(!a||b&&a.e!==b||c&&a.cb!==c&&a.cb._cb!==c||d&&a.ctx!==d)})}function c(a,b,c){f.each((a||"").split(h),function(a,d){c(d,b)})}function d(a,b){for(var c,d=!1,e=-1,f=a.length;++e<f;)if(c=a[e],c.cb.apply(c.ctx2,b)===!1){d=!0;break}return!d}var e,f=a.$,g=[].slice,h=/\s+/;return e={on:function(a,b,d){var e,f=this;return b?(e=this._events||(this._events=[]),c(a,b,function(a,b){var c={e:a};c.cb=b,c.ctx=d,c.ctx2=d||f,c.id=e.length,e.push(c)}),this):this},once:function(a,b,d){var e=this;return b?(c(a,b,function(a,b){var c=function(){return e.off(a,c),b.apply(d||e,arguments)};c._cb=b,e.on(a,c,d)}),e):e},off:function(a,d,e){var g=this._events;return g?a||d||e?(c(a,d,function(a,c){f.each(b(g,a,c,e),function(){delete g[this.id]})}),this):(this._events=[],this):this},trigger:function(a){var c,e,f;return this._events&&a?(c=g.call(arguments,1),e=b(this._events,a),f=b(this._events,"all"),d(e,c)&&d(f,arguments)):this}},f.extend({installTo:function(a){return f.extend(a,e)}},e)}),b("uploader",["base","mediator"],function(a,b){function c(a){this.options=d.extend(!0,{},c.options,a),this._init(this.options)}var d=a.$;return c.options={},b.installTo(c.prototype),d.each({upload:"start-upload",stop:"stop-upload",getFile:"get-file",getFiles:"get-files",addFile:"add-file",addFiles:"add-file",sort:"sort-files",removeFile:"remove-file",cancelFile:"cancel-file",skipFile:"skip-file",retry:"retry",isInProgress:"is-in-progress",makeThumb:"make-thumb",md5File:"md5-file",getDimension:"get-dimension",addButton:"add-btn",predictRuntimeType:"predict-runtime-type",refresh:"refresh",disable:"disable",enable:"enable",reset:"reset"},function(a,b){c.prototype[a]=function(){return this.request(b,arguments)}}),d.extend(c.prototype,{state:"pending",_init:function(a){var b=this;b.request("init",a,function(){b.state="ready",b.trigger("ready")})},option:function(a,b){var c=this.options;return arguments.length>1?void(d.isPlainObject(b)&&d.isPlainObject(c[a])?d.extend(c[a],b):c[a]=b):a?c[a]:c},getStats:function(){var a=this.request("get-stats");return a?{successNum:a.numOfSuccess,progressNum:a.numOfProgress,cancelNum:a.numOfCancel,invalidNum:a.numOfInvalid,uploadFailNum:a.numOfUploadFailed,queueNum:a.numOfQueue,interruptNum:a.numofInterrupt}:{}},trigger:function(a){var c=[].slice.call(arguments,1),e=this.options,f="on"+a.substring(0,1).toUpperCase()+a.substring(1);return b.trigger.apply(this,arguments)===!1||d.isFunction(e[f])&&e[f].apply(this,c)===!1||d.isFunction(this[f])&&this[f].apply(this,c)===!1||b.trigger.apply(b,[this,a].concat(c))===!1?!1:!0},destroy:function(){this.request("destroy",arguments),this.off()},request:a.noop}),a.create=c.create=function(a){return new c(a)},a.Uploader=c,c}),b("runtime/runtime",["base","mediator"],function(a,b){function c(b){this.options=d.extend({container:document.body},b),this.uid=a.guid("rt_")}var d=a.$,e={},f=function(a){for(var b in a)if(a.hasOwnProperty(b))return b;return null};return d.extend(c.prototype,{getContainer:function(){var a,b,c=this.options;return this._container?this._container:(a=d(c.container||document.body),b=d(document.createElement("div")),b.attr("id","rt_"+this.uid),b.css({position:"absolute",top:"0px",left:"0px",width:"1px",height:"1px",overflow:"hidden"}),a.append(b),a.addClass("webuploader-container"),this._container=b,this._parent=a,b)},init:a.noop,exec:a.noop,destroy:function(){this._container&&this._container.remove(),this._parent&&this._parent.removeClass("webuploader-container"),this.off()}}),c.orders="html5,flash",c.addRuntime=function(a,b){e[a]=b},c.hasRuntime=function(a){return!!(a?e[a]:f(e))},c.create=function(a,b){var g,h;if(b=b||c.orders,d.each(b.split(/\s*,\s*/g),function(){return e[this]?(g=this,!1):void 0}),g=g||f(e),!g)throw new Error("Runtime Error");return h=new e[g](a)},b.installTo(c.prototype),c}),b("runtime/client",["base","mediator","runtime/runtime"],function(a,b,c){function d(b,d){var f,g=a.Deferred();this.uid=a.guid("client_"),this.runtimeReady=function(a){return g.done(a)},this.connectRuntime=function(b,h){if(f)throw new Error("already connected!");return g.done(h),"string"==typeof b&&e.get(b)&&(f=e.get(b)),f=f||e.get(null,d),f?(a.$.extend(f.options,b),f.__promise.then(g.resolve),f.__client++):(f=c.create(b,b.runtimeOrder),f.__promise=g.promise(),f.once("ready",g.resolve),f.init(),e.add(f),f.__client=1),d&&(f.__standalone=d),f},this.getRuntime=function(){return f},this.disconnectRuntime=function(){f&&(f.__client--,f.__client<=0&&(e.remove(f),delete f.__promise,f.destroy()),f=null)},this.exec=function(){if(f){var c=a.slice(arguments);return b&&c.unshift(b),f.exec.apply(this,c)}},this.getRuid=function(){return f&&f.uid},this.destroy=function(a){return function(){a&&a.apply(this,arguments),this.trigger("destroy"),this.off(),this.exec("destroy"),this.disconnectRuntime()}}(this.destroy)}var e;return e=function(){var a={};return{add:function(b){a[b.uid]=b},get:function(b,c){var d;if(b)return a[b];for(d in a)if(!c||!a[d].__standalone)return a[d];return null},remove:function(b){delete a[b.uid]}}}(),b.installTo(d.prototype),d}),b("lib/dnd",["base","mediator","runtime/client"],function(a,b,c){function d(a){a=this.options=e.extend({},d.options,a),a.container=e(a.container),a.container.length&&c.call(this,"DragAndDrop")}var e=a.$;return d.options={accept:null,disableGlobalDnd:!1},a.inherits(c,{constructor:d,init:function(){var a=this;a.connectRuntime(a.options,function(){a.exec("init"),a.trigger("ready")})}}),b.installTo(d.prototype),d}),b("widgets/widget",["base","uploader"],function(a,b){function c(a){if(!a)return!1;var b=a.length,c=e.type(a);return 1===a.nodeType&&b?!0:"array"===c||"function"!==c&&"string"!==c&&(0===b||"number"==typeof b&&b>0&&b-1 in a)}function d(a){this.owner=a,this.options=a.options}var e=a.$,f=b.prototype._init,g=b.prototype.destroy,h={},i=[];return e.extend(d.prototype,{init:a.noop,invoke:function(a,b){var c=this.responseMap;return c&&a in c&&c[a]in this&&e.isFunction(this[c[a]])?this[c[a]].apply(this,b):h},request:function(){return this.owner.request.apply(this.owner,arguments)}}),e.extend(b.prototype,{_init:function(){var a=this,b=a._widgets=[],c=a.options.disableWidgets||"";return e.each(i,function(d,e){(!c||!~c.indexOf(e._name))&&b.push(new e(a))}),f.apply(a,arguments)},request:function(b,d,e){var f,g,i,j,k=0,l=this._widgets,m=l&&l.length,n=[],o=[];for(d=c(d)?d:[d];m>k;k++)f=l[k],g=f.invoke(b,d),g!==h&&(a.isPromise(g)?o.push(g):n.push(g));return e||o.length?(i=a.when.apply(a,o),j=i.pipe?"pipe":"then",i[j](function(){var b=a.Deferred(),c=arguments;return 1===c.length&&(c=c[0]),setTimeout(function(){b.resolve(c)},1),b.promise()})[e?j:"done"](e||a.noop)):n[0]},destroy:function(){g.apply(this,arguments),this._widgets=null}}),b.register=d.register=function(b,c){var f,g={init:"init",destroy:"destroy",name:"anonymous"};return 1===arguments.length?(c=b,e.each(c,function(a){return"_"===a[0]||"name"===a?void("name"===a&&(g.name=c.name)):void(g[a.replace(/[A-Z]/g,"-$&").toLowerCase()]=a)})):g=e.extend(g,b),c.responseMap=g,f=a.inherits(d,c),f._name=g.name,i.push(f),f},b.unRegister=d.unRegister=function(a){if(a&&"anonymous"!==a)for(var b=i.length;b--;)i[b]._name===a&&i.splice(b,1)},d}),b("widgets/filednd",["base","uploader","lib/dnd","widgets/widget"],function(a,b,c){var d=a.$;return b.options.dnd="",b.register({name:"dnd",init:function(b){if(b.dnd&&"html5"===this.request("predict-runtime-type")){var e,f=this,g=a.Deferred(),h=d.extend({},{disableGlobalDnd:b.disableGlobalDnd,container:b.dnd,accept:b.accept});return this.dnd=e=new c(h),e.once("ready",g.resolve),e.on("drop",function(a){f.request("add-file",[a])}),e.on("accept",function(a){return f.owner.trigger("dndAccept",a)}),e.init(),g.promise()}},destroy:function(){this.dnd&&this.dnd.destroy()}})}),b("lib/filepaste",["base","mediator","runtime/client"],function(a,b,c){function d(a){a=this.options=e.extend({},a),a.container=e(a.container||document.body),c.call(this,"FilePaste")}var e=a.$;return a.inherits(c,{constructor:d,init:function(){var a=this;a.connectRuntime(a.options,function(){a.exec("init"),a.trigger("ready")})}}),b.installTo(d.prototype),d}),b("widgets/filepaste",["base","uploader","lib/filepaste","widgets/widget"],function(a,b,c){var d=a.$;return b.register({name:"paste",init:function(b){if(b.paste&&"html5"===this.request("predict-runtime-type")){var e,f=this,g=a.Deferred(),h=d.extend({},{container:b.paste,accept:b.accept});return this.paste=e=new c(h),e.once("ready",g.resolve),e.on("paste",function(a){f.owner.request("add-file",[a])}),e.init(),g.promise()}},destroy:function(){this.paste&&this.paste.destroy()}})}),b("lib/blob",["base","runtime/client"],function(a,b){function c(a,c){var d=this;d.source=c,d.ruid=a,this.size=c.size||0,this.type=!c.type&&this.ext&&~"jpg,jpeg,png,gif,bmp".indexOf(this.ext)?"image/"+("jpg"===this.ext?"jpeg":this.ext):c.type||"application/octet-stream",b.call(d,"Blob"),this.uid=c.uid||this.uid,a&&d.connectRuntime(a)}return a.inherits(b,{constructor:c,slice:function(a,b){return this.exec("slice",a,b)},getSource:function(){return this.source}}),c}),b("lib/file",["base","lib/blob"],function(a,b){function c(a,c){var f;this.name=c.name||"untitled"+d++,f=e.exec(c.name)?RegExp.$1.toLowerCase():"",!f&&c.type&&(f=/\/(jpg|jpeg|png|gif|bmp)$/i.exec(c.type)?RegExp.$1.toLowerCase():"",this.name+="."+f),this.ext=f,this.lastModifiedDate=c.lastModifiedDate||(new Date).toLocaleString(),b.apply(this,arguments)}var d=1,e=/\.([^.]+)$/;return a.inherits(b,c)}),b("lib/filepicker",["base","runtime/client","lib/file"],function(b,c,d){function e(a){if(a=this.options=f.extend({},e.options,a),a.container=f(a.id),!a.container.length)throw new Error("按钮指定错误");a.innerHTML=a.innerHTML||a.label||a.container.html()||"",a.button=f(a.button||document.createElement("div")),a.button.html(a.innerHTML),a.container.html(a.button),c.call(this,"FilePicker",!0)}var f=b.$;return e.options={button:null,container:null,label:null,innerHTML:null,multiple:!0,accept:null,name:"file"},b.inherits(c,{constructor:e,init:function(){var c=this,e=c.options,g=e.button;g.addClass("webuploader-pick"),c.on("all",function(a){var b;switch(a){case"mouseenter":g.addClass("webuploader-pick-hover");break;case"mouseleave":g.removeClass("webuploader-pick-hover");break;case"change":b=c.exec("getFiles"),c.trigger("select",f.map(b,function(a){return a=new d(c.getRuid(),a),a._refer=e.container,a}),e.container)}}),c.connectRuntime(e,function(){c.refresh(),c.exec("init",e),c.trigger("ready")}),this._resizeHandler=b.bindFn(this.refresh,this),f(a).on("resize",this._resizeHandler)},refresh:function(){var a=this.getRuntime().getContainer(),b=this.options.button,c=b.outerWidth?b.outerWidth():b.width(),d=b.outerHeight?b.outerHeight():b.height(),e=b.offset();c&&d&&a.css({bottom:"auto",right:"auto",width:c+"px",height:d+"px"}).offset(e)},enable:function(){var a=this.options.button;a.removeClass("webuploader-pick-disable"),this.refresh()},disable:function(){var a=this.options.button;this.getRuntime().getContainer().css({top:"-99999px"}),a.addClass("webuploader-pick-disable")},destroy:function(){var b=this.options.button;f(a).off("resize",this._resizeHandler),b.removeClass("webuploader-pick-disable webuploader-pick-hover webuploader-pick")}}),e}),b("widgets/filepicker",["base","uploader","lib/filepicker","widgets/widget"],function(a,b,c){var d=a.$;return d.extend(b.options,{pick:null,accept:null}),b.register({name:"picker",init:function(a){return this.pickers=[],a.pick&&this.addBtn(a.pick)},refresh:function(){d.each(this.pickers,function(){this.refresh()})},addBtn:function(b){var e=this,f=e.options,g=f.accept,h=[];if(b)return d.isPlainObject(b)||(b={id:b}),d(b.id).each(function(){var i,j,k;k=a.Deferred(),i=d.extend({},b,{accept:d.isPlainObject(g)?[g]:g,swf:f.swf,runtimeOrder:f.runtimeOrder,id:this}),j=new c(i),j.once("ready",k.resolve),j.on("select",function(a){e.owner.request("add-file",[a])}),j.init(),e.pickers.push(j),h.push(k.promise())}),a.when.apply(a,h)},disable:function(){d.each(this.pickers,function(){this.disable()})},enable:function(){d.each(this.pickers,function(){this.enable()})},destroy:function(){d.each(this.pickers,function(){this.destroy()}),this.pickers=null}})}),b("lib/image",["base","runtime/client","lib/blob"],function(a,b,c){function d(a){this.options=e.extend({},d.options,a),b.call(this,"Image"),this.on("load",function(){this._info=this.exec("info"),this._meta=this.exec("meta")})}var e=a.$;return d.options={quality:90,crop:!1,preserveHeaders:!1,allowMagnify:!1},a.inherits(b,{constructor:d,info:function(a){return a?(this._info=a,this):this._info},meta:function(a){return a?(this._meta=a,this):this._meta},loadFromBlob:function(a){var b=this,c=a.getRuid();this.connectRuntime(c,function(){b.exec("init",b.options),b.exec("loadFromBlob",a)})},resize:function(){var b=a.slice(arguments);return this.exec.apply(this,["resize"].concat(b))},crop:function(){var b=a.slice(arguments);return this.exec.apply(this,["crop"].concat(b))},getAsDataUrl:function(a){return this.exec("getAsDataUrl",a)},getAsBlob:function(a){var b=this.exec("getAsBlob",a);return new c(this.getRuid(),b)}}),d}),b("widgets/image",["base","uploader","lib/image","widgets/widget"],function(a,b,c){var d,e=a.$;return d=function(a){var b=0,c=[],d=function(){for(var d;c.length&&a>b;)d=c.shift(),b+=d[0],d[1]()};return function(a,e,f){c.push([e,f]),a.once("destroy",function(){b-=e,setTimeout(d,1)}),setTimeout(d,1)}}(5242880),e.extend(b.options,{thumb:{width:110,height:110,quality:70,allowMagnify:!0,crop:!0,preserveHeaders:!1,type:"image/jpeg"},compress:{width:1920,height:1600,quality:90,allowMagnify:!1,crop:!1,preserveHeaders:!0}}),b.register({name:"image",makeThumb:function(a,b,f,g){var h,i;return a=this.request("get-file",a),a.type.match(/^image/)?(h=e.extend({},this.options.thumb),e.isPlainObject(f)&&(h=e.extend(h,f),f=null),f=f||h.width,g=g||h.height,i=new c(h),i.once("load",function(){a._info=a._info||i.info(),a._meta=a._meta||i.meta(),1>=f&&f>0&&(f=a._info.width*f),1>=g&&g>0&&(g=a._info.height*g),i.resize(f,g)}),i.once("complete",function(){b(!1,i.getAsDataUrl(h.type)),i.destroy()}),i.once("error",function(a){b(a||!0),i.destroy()}),void d(i,a.source.size,function(){a._info&&i.info(a._info),a._meta&&i.meta(a._meta),i.loadFromBlob(a.source)})):void b(!0)},beforeSendFile:function(b){var d,f,g=this.options.compress||this.options.resize,h=g&&g.compressSize||0,i=g&&g.noCompressIfLarger||!1;return b=this.request("get-file",b),!g||!~"image/jpeg,image/jpg".indexOf(b.type)||b.size<h||b._compressed?void 0:(g=e.extend({},g),f=a.Deferred(),d=new c(g),f.always(function(){d.destroy(),d=null}),d.once("error",f.reject),d.once("load",function(){var a=g.width,c=g.height;b._info=b._info||d.info(),b._meta=b._meta||d.meta(),1>=a&&a>0&&(a=b._info.width*a),1>=c&&c>0&&(c=b._info.height*c),d.resize(a,c)}),d.once("complete",function(){var a,c;try{a=d.getAsBlob(g.type),c=b.size,(!i||a.size<c)&&(b.source=a,b.size=a.size,b.trigger("resize",a.size,c)),b._compressed=!0,f.resolve()}catch(e){f.resolve()}}),b._info&&d.info(b._info),b._meta&&d.meta(b._meta),d.loadFromBlob(b.source),f.promise())}})}),b("file",["base","mediator"],function(a,b){function c(){return f+g++}function d(a){this.name=a.name||"Untitled",this.size=a.size||0,this.type=a.type||"application/octet-stream",this.lastModifiedDate=a.lastModifiedDate||1*new Date,this.id=c(),this.ext=h.exec(this.name)?RegExp.$1:"",this.statusText="",i[this.id]=d.Status.INITED,this.source=a,this.loaded=0,this.on("error",function(a){this.setStatus(d.Status.ERROR,a)})}var e=a.$,f="WU_FILE_",g=0,h=/\.([^.]+)$/,i={};return e.extend(d.prototype,{setStatus:function(a,b){var c=i[this.id];"undefined"!=typeof b&&(this.statusText=b),a!==c&&(i[this.id]=a,this.trigger("statuschange",a,c))},getStatus:function(){return i[this.id]},getSource:function(){return this.source},destroy:function(){this.off(),delete i[this.id]}}),b.installTo(d.prototype),d.Status={INITED:"inited",QUEUED:"queued",PROGRESS:"progress",ERROR:"error",COMPLETE:"complete",CANCELLED:"cancelled",INTERRUPT:"interrupt",INVALID:"invalid"},d}),b("queue",["base","mediator","file"],function(a,b,c){function d(){this.stats={numOfQueue:0,numOfSuccess:0,numOfCancel:0,numOfProgress:0,numOfUploadFailed:0,numOfInvalid:0,numofDeleted:0,numofInterrupt:0},this._queue=[],this._map={}}var e=a.$,f=c.Status;return e.extend(d.prototype,{append:function(a){return this._queue.push(a),this._fileAdded(a),this},prepend:function(a){return this._queue.unshift(a),this._fileAdded(a),this},getFile:function(a){return"string"!=typeof a?a:this._map[a]},fetch:function(a){var b,c,d=this._queue.length;for(a=a||f.QUEUED,b=0;d>b;b++)if(c=this._queue[b],a===c.getStatus())return c;return null},sort:function(a){"function"==typeof a&&this._queue.sort(a)},getFiles:function(){for(var a,b=[].slice.call(arguments,0),c=[],d=0,f=this._queue.length;f>d;d++)a=this._queue[d],(!b.length||~e.inArray(a.getStatus(),b))&&c.push(a);return c},removeFile:function(a){var b=this._map[a.id];b&&(delete this._map[a.id],a.destroy(),this.stats.numofDeleted++)},_fileAdded:function(a){var b=this,c=this._map[a.id];c||(this._map[a.id]=a,a.on("statuschange",function(a,c){b._onFileStatusChange(a,c)}))},_onFileStatusChange:function(a,b){var c=this.stats;switch(b){case f.PROGRESS:c.numOfProgress--;break;case f.QUEUED:c.numOfQueue--;break;case f.ERROR:c.numOfUploadFailed--;break;case f.INVALID:c.numOfInvalid--;break;case f.INTERRUPT:c.numofInterrupt--}switch(a){case f.QUEUED:c.numOfQueue++;break;case f.PROGRESS:c.numOfProgress++;break;case f.ERROR:c.numOfUploadFailed++;break;case f.COMPLETE:c.numOfSuccess++;break;case f.CANCELLED:c.numOfCancel++;break;case f.INVALID:c.numOfInvalid++;break;case f.INTERRUPT:c.numofInterrupt++}}}),b.installTo(d.prototype),d}),b("widgets/queue",["base","uploader","queue","file","lib/file","runtime/client","widgets/widget"],function(a,b,c,d,e,f){var g=a.$,h=/\.\w+$/,i=d.Status;return b.register({name:"queue",init:function(b){var d,e,h,i,j,k,l,m=this;if(g.isPlainObject(b.accept)&&(b.accept=[b.accept]),b.accept){for(j=[],h=0,e=b.accept.length;e>h;h++)i=b.accept[h].extensions,i&&j.push(i);j.length&&(k="\\."+j.join(",").replace(/,/g,"$|\\.").replace(/\*/g,".*")+"$"),m.accept=new RegExp(k,"i")}return m.queue=new c,m.stats=m.queue.stats,"html5"===this.request("predict-runtime-type")?(d=a.Deferred(),this.placeholder=l=new f("Placeholder"),l.connectRuntime({runtimeOrder:"html5"},function(){m._ruid=l.getRuid(),d.resolve()}),d.promise()):void 0},_wrapFile:function(a){if(!(a instanceof d)){if(!(a instanceof e)){if(!this._ruid)throw new Error("Can't add external files.");a=new e(this._ruid,a)}a=new d(a)}return a},acceptFile:function(a){var b=!a||!a.size||this.accept&&h.exec(a.name)&&!this.accept.test(a.name);return!b},_addFile:function(a){var b=this;return a=b._wrapFile(a),b.owner.trigger("beforeFileQueued",a)?b.acceptFile(a)?(b.queue.append(a),b.owner.trigger("fileQueued",a),a):void b.owner.trigger("error","Q_TYPE_DENIED",a):void 0},getFile:function(a){return this.queue.getFile(a)},addFile:function(a){var b=this;a.length||(a=[a]),a=g.map(a,function(a){return b._addFile(a)}),b.owner.trigger("filesQueued",a),b.options.auto&&setTimeout(function(){b.request("start-upload")},20)},getStats:function(){return this.stats},removeFile:function(a,b){var c=this;a=a.id?a:c.queue.getFile(a),this.request("cancel-file",a),b&&this.queue.removeFile(a)},getFiles:function(){return this.queue.getFiles.apply(this.queue,arguments)},fetchFile:function(){return this.queue.fetch.apply(this.queue,arguments)},retry:function(a,b){var c,d,e,f=this;if(a)return a=a.id?a:f.queue.getFile(a),a.setStatus(i.QUEUED),void(b||f.request("start-upload"));for(c=f.queue.getFiles(i.ERROR),d=0,e=c.length;e>d;d++)a=c[d],a.setStatus(i.QUEUED);f.request("start-upload")},sortFiles:function(){return this.queue.sort.apply(this.queue,arguments)},reset:function(){this.owner.trigger("reset"),this.queue=new c,this.stats=this.queue.stats},destroy:function(){this.reset(),this.placeholder&&this.placeholder.destroy()}})}),b("widgets/runtime",["uploader","runtime/runtime","widgets/widget"],function(a,b){return a.support=function(){return b.hasRuntime.apply(b,arguments)},a.register({name:"runtime",init:function(){if(!this.predictRuntimeType())throw Error("Runtime Error")},predictRuntimeType:function(){var a,c,d=this.options.runtimeOrder||b.orders,e=this.type;if(!e)for(d=d.split(/\s*,\s*/g),a=0,c=d.length;c>a;a++)if(b.hasRuntime(d[a])){this.type=e=d[a];break}return e}})}),b("lib/transport",["base","runtime/client","mediator"],function(a,b,c){function d(a){var c=this;a=c.options=e.extend(!0,{},d.options,a||{}),b.call(this,"Transport"),this._blob=null,this._formData=a.formData||{},this._headers=a.headers||{},this.on("progress",this._timeout),this.on("load error",function(){c.trigger("progress",1),clearTimeout(c._timer)})}var e=a.$;return d.options={server:"",method:"POST",withCredentials:!1,fileVal:"file",timeout:12e4,formData:{},headers:{},sendAsBinary:!1},e.extend(d.prototype,{appendBlob:function(a,b,c){var d=this,e=d.options;d.getRuid()&&d.disconnectRuntime(),d.connectRuntime(b.ruid,function(){d.exec("init")}),d._blob=b,e.fileVal=a||e.fileVal,e.filename=c||e.filename},append:function(a,b){"object"==typeof a?e.extend(this._formData,a):this._formData[a]=b},setRequestHeader:function(a,b){"object"==typeof a?e.extend(this._headers,a):this._headers[a]=b},send:function(a){this.exec("send",a),this._timeout()},abort:function(){return clearTimeout(this._timer),this.exec("abort")},destroy:function(){this.trigger("destroy"),this.off(),this.exec("destroy"),this.disconnectRuntime()},getResponse:function(){return this.exec("getResponse")},getResponseAsJson:function(){return this.exec("getResponseAsJson")},getStatus:function(){return this.exec("getStatus")},_timeout:function(){var a=this,b=a.options.timeout;b&&(clearTimeout(a._timer),a._timer=setTimeout(function(){a.abort(),a.trigger("error","timeout")},b))}}),c.installTo(d.prototype),d}),b("widgets/upload",["base","uploader","file","lib/transport","widgets/widget"],function(a,b,c,d){function e(a,b){var c,d,e=[],f=a.source,g=f.size,h=b?Math.ceil(g/b):1,i=0,j=0;for(d={file:a,has:function(){return!!e.length},shift:function(){return e.shift()},unshift:function(a){e.unshift(a)}};h>j;)c=Math.min(b,g-i),e.push({file:a,start:i,end:b?i+c:g,total:g,chunks:h,chunk:j++,cuted:d}),i+=c;return a.blocks=e.concat(),a.remaning=e.length,d}var f=a.$,g=a.isPromise,h=c.Status;f.extend(b.options,{prepareNextFile:!1,chunked:!1,chunkSize:5242880,chunkRetry:2,threads:3,formData:{}}),b.register({name:"upload",init:function(){var b=this.owner,c=this;this.runing=!1,this.progress=!1,b.on("startUpload",function(){c.progress=!0}).on("uploadFinished",function(){c.progress=!1}),this.pool=[],this.stack=[],this.pending=[],this.remaning=0,this.__tick=a.bindFn(this._tick,this),b.on("uploadComplete",function(a){a.blocks&&f.each(a.blocks,function(a,b){b.transport&&(b.transport.abort(),b.transport.destroy()),delete b.transport}),delete a.blocks,delete a.remaning})},reset:function(){this.request("stop-upload",!0),this.runing=!1,this.pool=[],this.stack=[],this.pending=[],this.remaning=0,this._trigged=!1,this._promise=null},startUpload:function(b){var c=this;if(f.each(c.request("get-files",h.INVALID),function(){c.request("remove-file",this)}),b)if(b=b.id?b:c.request("get-file",b),b.getStatus()===h.INTERRUPT)f.each(c.pool,function(a,c){c.file===b&&c.transport&&c.transport.send()}),b.setStatus(h.QUEUED);else{if(b.getStatus()===h.PROGRESS)return;b.setStatus(h.QUEUED)}else f.each(c.request("get-files",[h.INITED]),function(){this.setStatus(h.QUEUED)});if(!c.runing){c.runing=!0;var d=[];f.each(c.pool,function(a,b){var e=b.file;e.getStatus()===h.INTERRUPT&&(d.push(e),c._trigged=!1,b.transport&&b.transport.send())});for(var b;b=d.shift();)b.setStatus(h.PROGRESS);b||f.each(c.request("get-files",h.INTERRUPT),function(){this.setStatus(h.PROGRESS)}),c._trigged=!1,a.nextTick(c.__tick),c.owner.trigger("startUpload")}},stopUpload:function(b,c){var d=this;if(b===!0&&(c=b,b=null),d.runing!==!1){if(b){if(b=b.id?b:d.request("get-file",b),b.getStatus()!==h.PROGRESS&&b.getStatus()!==h.QUEUED)return;return b.setStatus(h.INTERRUPT),f.each(d.pool,function(a,c){c.file===b&&(c.transport&&c.transport.abort(),d._putback(c),d._popBlock(c))}),a.nextTick(d.__tick)}d.runing=!1,this._promise&&this._promise.file&&this._promise.file.setStatus(h.INTERRUPT),c&&f.each(d.pool,function(a,b){b.transport&&b.transport.abort(),b.file.setStatus(h.INTERRUPT)}),d.owner.trigger("stopUpload")}},cancelFile:function(a){a=a.id?a:this.request("get-file",a),a.blocks&&f.each(a.blocks,function(a,b){var c=b.transport;c&&(c.abort(),c.destroy(),delete b.transport)}),a.setStatus(h.CANCELLED),this.owner.trigger("fileDequeued",a)},isInProgress:function(){return!!this.progress},_getStats:function(){return this.request("get-stats")},skipFile:function(a,b){a=a.id?a:this.request("get-file",a),a.setStatus(b||h.COMPLETE),a.skipped=!0,a.blocks&&f.each(a.blocks,function(a,b){var c=b.transport;c&&(c.abort(),c.destroy(),delete b.transport)}),this.owner.trigger("uploadSkip",a)},_tick:function(){var b,c,d=this,e=d.options;return d._promise?d._promise.always(d.__tick):void(d.pool.length<e.threads&&(c=d._nextBlock())?(d._trigged=!1,b=function(b){d._promise=null,b&&b.file&&d._startSend(b),a.nextTick(d.__tick)},d._promise=g(c)?c.always(b):b(c)):d.remaning||d._getStats().numOfQueue||d._getStats().numofInterrupt||(d.runing=!1,d._trigged||a.nextTick(function(){d.owner.trigger("uploadFinished")}),d._trigged=!0))},_putback:function(a){var b;a.cuted.unshift(a),b=this.stack.indexOf(a.cuted),~b||this.stack.unshift(a.cuted)},_getStack:function(){for(var a,b=0;a=this.stack[b++];){if(a.has()&&a.file.getStatus()===h.PROGRESS)return a;(!a.has()||a.file.getStatus()!==h.PROGRESS&&a.file.getStatus()!==h.INTERRUPT)&&this.stack.splice(--b,1)}return null},_nextBlock:function(){var a,b,c,d,f=this,h=f.options;return(a=this._getStack())?(h.prepareNextFile&&!f.pending.length&&f._prepareNextFile(),a.shift()):f.runing?(!f.pending.length&&f._getStats().numOfQueue&&f._prepareNextFile(),b=f.pending.shift(),c=function(b){return b?(a=e(b,h.chunked?h.chunkSize:0),f.stack.push(a),a.shift()):null},g(b)?(d=b.file,b=b[b.pipe?"pipe":"then"](c),b.file=d,b):c(b)):void 0},_prepareNextFile:function(){var a,b=this,c=b.request("fetch-file"),d=b.pending;c&&(a=b.request("before-send-file",c,function(){return c.getStatus()===h.PROGRESS||c.getStatus()===h.INTERRUPT?c:b._finishFile(c)}),b.owner.trigger("uploadStart",c),c.setStatus(h.PROGRESS),a.file=c,a.done(function(){var b=f.inArray(a,d);~b&&d.splice(b,1,c)}),a.fail(function(a){c.setStatus(h.ERROR,a),b.owner.trigger("uploadError",c,a),b.owner.trigger("uploadComplete",c)}),d.push(a))},_popBlock:function(a){var b=f.inArray(a,this.pool);this.pool.splice(b,1),a.file.remaning--,this.remaning--},_startSend:function(b){var c,d=this,e=b.file;return e.getStatus()!==h.PROGRESS?void(e.getStatus()===h.INTERRUPT&&d._putback(b)):(d.pool.push(b),d.remaning++,b.blob=1===b.chunks?e.source:e.source.slice(b.start,b.end),c=d.request("before-send",b,function(){e.getStatus()===h.PROGRESS?d._doSend(b):(d._popBlock(b),a.nextTick(d.__tick))}),void c.fail(function(){1===e.remaning?d._finishFile(e).always(function(){b.percentage=1,d._popBlock(b),d.owner.trigger("uploadComplete",e),a.nextTick(d.__tick)}):(b.percentage=1,d.updateFileProgress(e),d._popBlock(b),a.nextTick(d.__tick))}))},_doSend:function(b){var c,e,g=this,i=g.owner,j=g.options,k=b.file,l=new d(j),m=f.extend({},j.formData),n=f.extend({},j.headers);b.transport=l,l.on("destroy",function(){delete b.transport,g._popBlock(b),a.nextTick(g.__tick)}),l.on("progress",function(a){b.percentage=a,g.updateFileProgress(k)}),c=function(a){var c;return e=l.getResponseAsJson()||{},e._raw=l.getResponse(),c=function(b){a=b},i.trigger("uploadAccept",b,e,c)||(a=a||"server"),a},l.on("error",function(a,d){b.retried=b.retried||0,b.chunks>1&&~"http,abort".indexOf(a)&&b.retried<j.chunkRetry?(b.retried++,l.send()):(d||"server"!==a||(a=c(a)),k.setStatus(h.ERROR,a),i.trigger("uploadError",k,a),i.trigger("uploadComplete",k))}),l.on("load",function(){var a;return(a=c())?void l.trigger("error",a,!0):void(1===k.remaning?g._finishFile(k,e):l.destroy())}),m=f.extend(m,{id:k.id,name:k.name,type:k.type,lastModifiedDate:k.lastModifiedDate,size:k.size}),b.chunks>1&&f.extend(m,{chunks:b.chunks,chunk:b.chunk}),i.trigger("uploadBeforeSend",b,m,n),l.appendBlob(j.fileVal,b.blob,k.name),l.append(m),l.setRequestHeader(n),l.send()},_finishFile:function(a,b,c){var d=this.owner;return d.request("after-send-file",arguments,function(){a.setStatus(h.COMPLETE),d.trigger("uploadSuccess",a,b,c)}).fail(function(b){a.getStatus()===h.PROGRESS&&a.setStatus(h.ERROR,b),d.trigger("uploadError",a,b)
}).always(function(){d.trigger("uploadComplete",a)})},updateFileProgress:function(a){var b=0,c=0;a.blocks&&(f.each(a.blocks,function(a,b){c+=(b.percentage||0)*(b.end-b.start)}),b=c/a.size,this.owner.trigger("uploadProgress",a,b||0))}})}),b("widgets/validator",["base","uploader","file","widgets/widget"],function(a,b,c){var d,e=a.$,f={};return d={addValidator:function(a,b){f[a]=b},removeValidator:function(a){delete f[a]}},b.register({name:"validator",init:function(){var b=this;a.nextTick(function(){e.each(f,function(){this.call(b.owner)})})}}),d.addValidator("fileNumLimit",function(){var a=this,b=a.options,c=0,d=parseInt(b.fileNumLimit,10),e=!0;d&&(a.on("beforeFileQueued",function(a){return c>=d&&e&&(e=!1,this.trigger("error","Q_EXCEED_NUM_LIMIT",d,a),setTimeout(function(){e=!0},1)),c>=d?!1:!0}),a.on("fileQueued",function(){c++}),a.on("fileDequeued",function(){c--}),a.on("reset",function(){c=0}))}),d.addValidator("fileSizeLimit",function(){var a=this,b=a.options,c=0,d=parseInt(b.fileSizeLimit,10),e=!0;d&&(a.on("beforeFileQueued",function(a){var b=c+a.size>d;return b&&e&&(e=!1,this.trigger("error","Q_EXCEED_SIZE_LIMIT",d,a),setTimeout(function(){e=!0},1)),b?!1:!0}),a.on("fileQueued",function(a){c+=a.size}),a.on("fileDequeued",function(a){c-=a.size}),a.on("reset",function(){c=0}))}),d.addValidator("fileSingleSizeLimit",function(){var a=this,b=a.options,d=b.fileSingleSizeLimit;d&&a.on("beforeFileQueued",function(a){return a.size>d?(a.setStatus(c.Status.INVALID,"exceed_size"),this.trigger("error","F_EXCEED_SIZE",d,a),!1):void 0})}),d.addValidator("duplicate",function(){function a(a){for(var b,c=0,d=0,e=a.length;e>d;d++)b=a.charCodeAt(d),c=b+(c<<6)+(c<<16)-c;return c}var b=this,c=b.options,d={};c.duplicate||(b.on("beforeFileQueued",function(b){var c=b.__hash||(b.__hash=a(b.name+b.size+b.lastModifiedDate));return d[c]?(this.trigger("error","F_DUPLICATE",b),!1):void 0}),b.on("fileQueued",function(a){var b=a.__hash;b&&(d[b]=!0)}),b.on("fileDequeued",function(a){var b=a.__hash;b&&delete d[b]}),b.on("reset",function(){d={}}))}),d}),b("lib/md5",["runtime/client","mediator"],function(a,b){function c(){a.call(this,"Md5")}return b.installTo(c.prototype),c.prototype.loadFromBlob=function(a){var b=this;b.getRuid()&&b.disconnectRuntime(),b.connectRuntime(a.ruid,function(){b.exec("init"),b.exec("loadFromBlob",a)})},c.prototype.getResult=function(){return this.exec("getResult")},c}),b("widgets/md5",["base","uploader","lib/md5","lib/blob","widgets/widget"],function(a,b,c,d){return b.register({name:"md5",md5File:function(b,e,f){var g=new c,h=a.Deferred(),i=b instanceof d?b:this.request("get-file",b).source;return g.on("progress load",function(a){a=a||{},h.notify(a.total?a.loaded/a.total:1)}),g.on("complete",function(){h.resolve(g.getResult())}),g.on("error",function(a){h.reject(a)}),arguments.length>1&&(e=e||0,f=f||0,0>e&&(e=i.size+e),0>f&&(f=i.size+f),f=Math.min(f,i.size),i=i.slice(e,f)),g.loadFromBlob(i),h.promise()}})}),b("runtime/compbase",[],function(){function a(a,b){this.owner=a,this.options=a.options,this.getRuntime=function(){return b},this.getRuid=function(){return b.uid},this.trigger=function(){return a.trigger.apply(a,arguments)}}return a}),b("runtime/html5/runtime",["base","runtime/runtime","runtime/compbase"],function(b,c,d){function e(){var a={},d=this,e=this.destroy;c.apply(d,arguments),d.type=f,d.exec=function(c,e){var f,h=this,i=h.uid,j=b.slice(arguments,2);return g[c]&&(f=a[i]=a[i]||new g[c](h,d),f[e])?f[e].apply(f,j):void 0},d.destroy=function(){return e&&e.apply(this,arguments)}}var f="html5",g={};return b.inherits(c,{constructor:e,init:function(){var a=this;setTimeout(function(){a.trigger("ready")},1)}}),e.register=function(a,c){var e=g[a]=b.inherits(d,c);return e},a.Blob&&a.FileReader&&a.DataView&&c.addRuntime(f,e),e}),b("runtime/html5/blob",["runtime/html5/runtime","lib/blob"],function(a,b){return a.register("Blob",{slice:function(a,c){var d=this.owner.source,e=d.slice||d.webkitSlice||d.mozSlice;return d=e.call(d,a,c),new b(this.getRuid(),d)}})}),b("runtime/html5/dnd",["base","runtime/html5/runtime","lib/file"],function(a,b,c){var d=a.$,e="webuploader-dnd-";return b.register("DragAndDrop",{init:function(){var b=this.elem=this.options.container;this.dragEnterHandler=a.bindFn(this._dragEnterHandler,this),this.dragOverHandler=a.bindFn(this._dragOverHandler,this),this.dragLeaveHandler=a.bindFn(this._dragLeaveHandler,this),this.dropHandler=a.bindFn(this._dropHandler,this),this.dndOver=!1,b.on("dragenter",this.dragEnterHandler),b.on("dragover",this.dragOverHandler),b.on("dragleave",this.dragLeaveHandler),b.on("drop",this.dropHandler),this.options.disableGlobalDnd&&(d(document).on("dragover",this.dragOverHandler),d(document).on("drop",this.dropHandler))},_dragEnterHandler:function(a){var b,c=this,d=c._denied||!1;return a=a.originalEvent||a,c.dndOver||(c.dndOver=!0,b=a.dataTransfer.items,b&&b.length&&(c._denied=d=!c.trigger("accept",b)),c.elem.addClass(e+"over"),c.elem[d?"addClass":"removeClass"](e+"denied")),a.dataTransfer.dropEffect=d?"none":"copy",!1},_dragOverHandler:function(a){var b=this.elem.parent().get(0);return b&&!d.contains(b,a.currentTarget)?!1:(clearTimeout(this._leaveTimer),this._dragEnterHandler.call(this,a),!1)},_dragLeaveHandler:function(){var a,b=this;return a=function(){b.dndOver=!1,b.elem.removeClass(e+"over "+e+"denied")},clearTimeout(b._leaveTimer),b._leaveTimer=setTimeout(a,100),!1},_dropHandler:function(a){var b,f,g=this,h=g.getRuid(),i=g.elem.parent().get(0);if(i&&!d.contains(i,a.currentTarget))return!1;a=a.originalEvent||a,b=a.dataTransfer;try{f=b.getData("text/html")}catch(j){}return f?void 0:(g._getTansferFiles(b,function(a){g.trigger("drop",d.map(a,function(a){return new c(h,a)}))}),g.dndOver=!1,g.elem.removeClass(e+"over"),!1)},_getTansferFiles:function(b,c){var d,e,f,g,h,i,j,k=[],l=[];for(d=b.items,e=b.files,j=!(!d||!d[0].webkitGetAsEntry),h=0,i=e.length;i>h;h++)f=e[h],g=d&&d[h],j&&g.webkitGetAsEntry().isDirectory?l.push(this._traverseDirectoryTree(g.webkitGetAsEntry(),k)):k.push(f);a.when.apply(a,l).done(function(){k.length&&c(k)})},_traverseDirectoryTree:function(b,c){var d=a.Deferred(),e=this;return b.isFile?b.file(function(a){c.push(a),d.resolve()}):b.isDirectory&&b.createReader().readEntries(function(b){var f,g=b.length,h=[],i=[];for(f=0;g>f;f++)h.push(e._traverseDirectoryTree(b[f],i));a.when.apply(a,h).then(function(){c.push.apply(c,i),d.resolve()},d.reject)}),d.promise()},destroy:function(){var a=this.elem;a&&(a.off("dragenter",this.dragEnterHandler),a.off("dragover",this.dragOverHandler),a.off("dragleave",this.dragLeaveHandler),a.off("drop",this.dropHandler),this.options.disableGlobalDnd&&(d(document).off("dragover",this.dragOverHandler),d(document).off("drop",this.dropHandler)))}})}),b("runtime/html5/filepaste",["base","runtime/html5/runtime","lib/file"],function(a,b,c){return b.register("FilePaste",{init:function(){var b,c,d,e,f=this.options,g=this.elem=f.container,h=".*";if(f.accept){for(b=[],c=0,d=f.accept.length;d>c;c++)e=f.accept[c].mimeTypes,e&&b.push(e);b.length&&(h=b.join(","),h=h.replace(/,/g,"|").replace(/\*/g,".*"))}this.accept=h=new RegExp(h,"i"),this.hander=a.bindFn(this._pasteHander,this),g.on("paste",this.hander)},_pasteHander:function(a){var b,d,e,f,g,h=[],i=this.getRuid();for(a=a.originalEvent||a,b=a.clipboardData.items,f=0,g=b.length;g>f;f++)d=b[f],"file"===d.kind&&(e=d.getAsFile())&&h.push(new c(i,e));h.length&&(a.preventDefault(),a.stopPropagation(),this.trigger("paste",h))},destroy:function(){this.elem.off("paste",this.hander)}})}),b("runtime/html5/filepicker",["base","runtime/html5/runtime"],function(a,b){var c=a.$;return b.register("FilePicker",{init:function(){var a,b,d,e,f=this.getRuntime().getContainer(),g=this,h=g.owner,i=g.options,j=this.label=c(document.createElement("label")),k=this.input=c(document.createElement("input"));if(k.attr("type","file"),k.attr("name",i.name),k.addClass("webuploader-element-invisible"),j.on("click",function(){k.trigger("click")}),j.css({opacity:0,width:"100%",height:"100%",display:"block",cursor:"pointer",background:"#ffffff"}),i.multiple&&k.attr("multiple","multiple"),i.accept&&i.accept.length>0){for(a=[],b=0,d=i.accept.length;d>b;b++)a.push(i.accept[b].mimeTypes);k.attr("accept",a.join(","))}f.append(k),f.append(j),e=function(a){h.trigger(a.type)},k.on("change",function(a){var b,d=arguments.callee;g.files=a.target.files,b=this.cloneNode(!0),b.value=null,this.parentNode.replaceChild(b,this),k.off(),k=c(b).on("change",d).on("mouseenter mouseleave",e),h.trigger("change")}),j.on("mouseenter mouseleave",e)},getFiles:function(){return this.files},destroy:function(){this.input.off(),this.label.off()}})}),b("runtime/html5/util",["base"],function(b){var c=a.createObjectURL&&a||a.URL&&URL.revokeObjectURL&&URL||a.webkitURL,d=b.noop,e=d;return c&&(d=function(){return c.createObjectURL.apply(c,arguments)},e=function(){return c.revokeObjectURL.apply(c,arguments)}),{createObjectURL:d,revokeObjectURL:e,dataURL2Blob:function(a){var b,c,d,e,f,g;for(g=a.split(","),b=~g[0].indexOf("base64")?atob(g[1]):decodeURIComponent(g[1]),d=new ArrayBuffer(b.length),c=new Uint8Array(d),e=0;e<b.length;e++)c[e]=b.charCodeAt(e);return f=g[0].split(":")[1].split(";")[0],this.arrayBufferToBlob(d,f)},dataURL2ArrayBuffer:function(a){var b,c,d,e;for(e=a.split(","),b=~e[0].indexOf("base64")?atob(e[1]):decodeURIComponent(e[1]),c=new Uint8Array(b.length),d=0;d<b.length;d++)c[d]=b.charCodeAt(d);return c.buffer},arrayBufferToBlob:function(b,c){var d,e=a.BlobBuilder||a.WebKitBlobBuilder;return e?(d=new e,d.append(b),d.getBlob(c)):new Blob([b],c?{type:c}:{})},canvasToDataUrl:function(a,b,c){return a.toDataURL(b,c/100)},parseMeta:function(a,b){b(!1,{})},updateImageHead:function(a){return a}}}),b("runtime/html5/imagemeta",["runtime/html5/util"],function(a){var b;return b={parsers:{65505:[]},maxMetaDataSize:262144,parse:function(a,b){var c=this,d=new FileReader;d.onload=function(){b(!1,c._parse(this.result)),d=d.onload=d.onerror=null},d.onerror=function(a){b(a.message),d=d.onload=d.onerror=null},a=a.slice(0,c.maxMetaDataSize),d.readAsArrayBuffer(a.getSource())},_parse:function(a,c){if(!(a.byteLength<6)){var d,e,f,g,h=new DataView(a),i=2,j=h.byteLength-4,k=i,l={};if(65496===h.getUint16(0)){for(;j>i&&(d=h.getUint16(i),d>=65504&&65519>=d||65534===d)&&(e=h.getUint16(i+2)+2,!(i+e>h.byteLength));){if(f=b.parsers[d],!c&&f)for(g=0;g<f.length;g+=1)f[g].call(b,h,i,e,l);i+=e,k=i}k>6&&(l.imageHead=a.slice?a.slice(2,k):new Uint8Array(a).subarray(2,k))}return l}},updateImageHead:function(a,b){var c,d,e,f=this._parse(a,!0);return e=2,f.imageHead&&(e=2+f.imageHead.byteLength),d=a.slice?a.slice(e):new Uint8Array(a).subarray(e),c=new Uint8Array(b.byteLength+2+d.byteLength),c[0]=255,c[1]=216,c.set(new Uint8Array(b),2),c.set(new Uint8Array(d),b.byteLength+2),c.buffer}},a.parseMeta=function(){return b.parse.apply(b,arguments)},a.updateImageHead=function(){return b.updateImageHead.apply(b,arguments)},b}),b("runtime/html5/imagemeta/exif",["base","runtime/html5/imagemeta"],function(a,b){var c={};return c.ExifMap=function(){return this},c.ExifMap.prototype.map={Orientation:274},c.ExifMap.prototype.get=function(a){return this[a]||this[this.map[a]]},c.exifTagTypes={1:{getValue:function(a,b){return a.getUint8(b)},size:1},2:{getValue:function(a,b){return String.fromCharCode(a.getUint8(b))},size:1,ascii:!0},3:{getValue:function(a,b,c){return a.getUint16(b,c)},size:2},4:{getValue:function(a,b,c){return a.getUint32(b,c)},size:4},5:{getValue:function(a,b,c){return a.getUint32(b,c)/a.getUint32(b+4,c)},size:8},9:{getValue:function(a,b,c){return a.getInt32(b,c)},size:4},10:{getValue:function(a,b,c){return a.getInt32(b,c)/a.getInt32(b+4,c)},size:8}},c.exifTagTypes[7]=c.exifTagTypes[1],c.getExifValue=function(b,d,e,f,g,h){var i,j,k,l,m,n,o=c.exifTagTypes[f];if(!o)return void a.log("Invalid Exif data: Invalid tag type.");if(i=o.size*g,j=i>4?d+b.getUint32(e+8,h):e+8,j+i>b.byteLength)return void a.log("Invalid Exif data: Invalid data offset.");if(1===g)return o.getValue(b,j,h);for(k=[],l=0;g>l;l+=1)k[l]=o.getValue(b,j+l*o.size,h);if(o.ascii){for(m="",l=0;l<k.length&&(n=k[l],"\x00"!==n);l+=1)m+=n;return m}return k},c.parseExifTag=function(a,b,d,e,f){var g=a.getUint16(d,e);f.exif[g]=c.getExifValue(a,b,d,a.getUint16(d+2,e),a.getUint32(d+4,e),e)},c.parseExifTags=function(b,c,d,e,f){var g,h,i;if(d+6>b.byteLength)return void a.log("Invalid Exif data: Invalid directory offset.");if(g=b.getUint16(d,e),h=d+2+12*g,h+4>b.byteLength)return void a.log("Invalid Exif data: Invalid directory size.");for(i=0;g>i;i+=1)this.parseExifTag(b,c,d+2+12*i,e,f);return b.getUint32(h,e)},c.parseExifData=function(b,d,e,f){var g,h,i=d+10;if(1165519206===b.getUint32(d+4)){if(i+8>b.byteLength)return void a.log("Invalid Exif data: Invalid segment size.");if(0!==b.getUint16(d+8))return void a.log("Invalid Exif data: Missing byte alignment offset.");switch(b.getUint16(i)){case 18761:g=!0;break;case 19789:g=!1;break;default:return void a.log("Invalid Exif data: Invalid byte alignment marker.")}if(42!==b.getUint16(i+2,g))return void a.log("Invalid Exif data: Missing TIFF marker.");h=b.getUint32(i+4,g),f.exif=new c.ExifMap,h=c.parseExifTags(b,i,i+h,g,f)}},b.parsers[65505].push(c.parseExifData),c}),b("runtime/html5/jpegencoder",[],function(){function a(a){function b(a){for(var b=[16,11,10,16,24,40,51,61,12,12,14,19,26,58,60,55,14,13,16,24,40,57,69,56,14,17,22,29,51,87,80,62,18,22,37,56,68,109,103,77,24,35,55,64,81,104,113,92,49,64,78,87,103,121,120,101,72,92,95,98,112,100,103,99],c=0;64>c;c++){var d=y((b[c]*a+50)/100);1>d?d=1:d>255&&(d=255),z[P[c]]=d}for(var e=[17,18,24,47,99,99,99,99,18,21,26,66,99,99,99,99,24,26,56,99,99,99,99,99,47,66,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99,99],f=0;64>f;f++){var g=y((e[f]*a+50)/100);1>g?g=1:g>255&&(g=255),A[P[f]]=g}for(var h=[1,1.387039845,1.306562965,1.175875602,1,.785694958,.5411961,.275899379],i=0,j=0;8>j;j++)for(var k=0;8>k;k++)B[i]=1/(z[P[i]]*h[j]*h[k]*8),C[i]=1/(A[P[i]]*h[j]*h[k]*8),i++}function c(a,b){for(var c=0,d=0,e=new Array,f=1;16>=f;f++){for(var g=1;g<=a[f];g++)e[b[d]]=[],e[b[d]][0]=c,e[b[d]][1]=f,d++,c++;c*=2}return e}function d(){t=c(Q,R),u=c(U,V),v=c(S,T),w=c(W,X)}function e(){for(var a=1,b=2,c=1;15>=c;c++){for(var d=a;b>d;d++)E[32767+d]=c,D[32767+d]=[],D[32767+d][1]=c,D[32767+d][0]=d;for(var e=-(b-1);-a>=e;e++)E[32767+e]=c,D[32767+e]=[],D[32767+e][1]=c,D[32767+e][0]=b-1+e;a<<=1,b<<=1}}function f(){for(var a=0;256>a;a++)O[a]=19595*a,O[a+256>>0]=38470*a,O[a+512>>0]=7471*a+32768,O[a+768>>0]=-11059*a,O[a+1024>>0]=-21709*a,O[a+1280>>0]=32768*a+8421375,O[a+1536>>0]=-27439*a,O[a+1792>>0]=-5329*a}function g(a){for(var b=a[0],c=a[1]-1;c>=0;)b&1<<c&&(I|=1<<J),c--,J--,0>J&&(255==I?(h(255),h(0)):h(I),J=7,I=0)}function h(a){H.push(N[a])}function i(a){h(a>>8&255),h(255&a)}function j(a,b){var c,d,e,f,g,h,i,j,k,l=0,m=8,n=64;for(k=0;m>k;++k){c=a[l],d=a[l+1],e=a[l+2],f=a[l+3],g=a[l+4],h=a[l+5],i=a[l+6],j=a[l+7];var o=c+j,p=c-j,q=d+i,r=d-i,s=e+h,t=e-h,u=f+g,v=f-g,w=o+u,x=o-u,y=q+s,z=q-s;a[l]=w+y,a[l+4]=w-y;var A=.707106781*(z+x);a[l+2]=x+A,a[l+6]=x-A,w=v+t,y=t+r,z=r+p;var B=.382683433*(w-z),C=.5411961*w+B,D=1.306562965*z+B,E=.707106781*y,G=p+E,H=p-E;a[l+5]=H+C,a[l+3]=H-C,a[l+1]=G+D,a[l+7]=G-D,l+=8}for(l=0,k=0;m>k;++k){c=a[l],d=a[l+8],e=a[l+16],f=a[l+24],g=a[l+32],h=a[l+40],i=a[l+48],j=a[l+56];var I=c+j,J=c-j,K=d+i,L=d-i,M=e+h,N=e-h,O=f+g,P=f-g,Q=I+O,R=I-O,S=K+M,T=K-M;a[l]=Q+S,a[l+32]=Q-S;var U=.707106781*(T+R);a[l+16]=R+U,a[l+48]=R-U,Q=P+N,S=N+L,T=L+J;var V=.382683433*(Q-T),W=.5411961*Q+V,X=1.306562965*T+V,Y=.707106781*S,Z=J+Y,$=J-Y;a[l+40]=$+W,a[l+24]=$-W,a[l+8]=Z+X,a[l+56]=Z-X,l++}var _;for(k=0;n>k;++k)_=a[k]*b[k],F[k]=_>0?_+.5|0:_-.5|0;return F}function k(){i(65504),i(16),h(74),h(70),h(73),h(70),h(0),h(1),h(1),h(0),i(1),i(1),h(0),h(0)}function l(a,b){i(65472),i(17),h(8),i(b),i(a),h(3),h(1),h(17),h(0),h(2),h(17),h(1),h(3),h(17),h(1)}function m(){i(65499),i(132),h(0);for(var a=0;64>a;a++)h(z[a]);h(1);for(var b=0;64>b;b++)h(A[b])}function n(){i(65476),i(418),h(0);for(var a=0;16>a;a++)h(Q[a+1]);for(var b=0;11>=b;b++)h(R[b]);h(16);for(var c=0;16>c;c++)h(S[c+1]);for(var d=0;161>=d;d++)h(T[d]);h(1);for(var e=0;16>e;e++)h(U[e+1]);for(var f=0;11>=f;f++)h(V[f]);h(17);for(var g=0;16>g;g++)h(W[g+1]);for(var j=0;161>=j;j++)h(X[j])}function o(){i(65498),i(12),h(3),h(1),h(0),h(2),h(17),h(3),h(17),h(0),h(63),h(0)}function p(a,b,c,d,e){for(var f,h=e[0],i=e[240],k=16,l=63,m=64,n=j(a,b),o=0;m>o;++o)G[P[o]]=n[o];var p=G[0]-c;c=G[0],0==p?g(d[0]):(f=32767+p,g(d[E[f]]),g(D[f]));for(var q=63;q>0&&0==G[q];q--);if(0==q)return g(h),c;for(var r,s=1;q>=s;){for(var t=s;0==G[s]&&q>=s;++s);var u=s-t;if(u>=k){r=u>>4;for(var v=1;r>=v;++v)g(i);u=15&u}f=32767+G[s],g(e[(u<<4)+E[f]]),g(D[f]),s++}return q!=l&&g(h),c}function q(){for(var a=String.fromCharCode,b=0;256>b;b++)N[b]=a(b)}function r(a){if(0>=a&&(a=1),a>100&&(a=100),x!=a){var c=0;c=Math.floor(50>a?5e3/a:200-2*a),b(c),x=a}}function s(){a||(a=50),q(),d(),e(),f(),r(a)}var t,u,v,w,x,y=(Math.round,Math.floor),z=new Array(64),A=new Array(64),B=new Array(64),C=new Array(64),D=new Array(65535),E=new Array(65535),F=new Array(64),G=new Array(64),H=[],I=0,J=7,K=new Array(64),L=new Array(64),M=new Array(64),N=new Array(256),O=new Array(2048),P=[0,1,5,6,14,15,27,28,2,4,7,13,16,26,29,42,3,8,12,17,25,30,41,43,9,11,18,24,31,40,44,53,10,19,23,32,39,45,52,54,20,22,33,38,46,51,55,60,21,34,37,47,50,56,59,61,35,36,48,49,57,58,62,63],Q=[0,0,1,5,1,1,1,1,1,1,0,0,0,0,0,0,0],R=[0,1,2,3,4,5,6,7,8,9,10,11],S=[0,0,2,1,3,3,2,4,3,5,5,4,4,0,0,1,125],T=[1,2,3,0,4,17,5,18,33,49,65,6,19,81,97,7,34,113,20,50,129,145,161,8,35,66,177,193,21,82,209,240,36,51,98,114,130,9,10,22,23,24,25,26,37,38,39,40,41,42,52,53,54,55,56,57,58,67,68,69,70,71,72,73,74,83,84,85,86,87,88,89,90,99,100,101,102,103,104,105,106,115,116,117,118,119,120,121,122,131,132,133,134,135,136,137,138,146,147,148,149,150,151,152,153,154,162,163,164,165,166,167,168,169,170,178,179,180,181,182,183,184,185,186,194,195,196,197,198,199,200,201,202,210,211,212,213,214,215,216,217,218,225,226,227,228,229,230,231,232,233,234,241,242,243,244,245,246,247,248,249,250],U=[0,0,3,1,1,1,1,1,1,1,1,1,0,0,0,0,0],V=[0,1,2,3,4,5,6,7,8,9,10,11],W=[0,0,2,1,2,4,4,3,4,7,5,4,4,0,1,2,119],X=[0,1,2,3,17,4,5,33,49,6,18,65,81,7,97,113,19,34,50,129,8,20,66,145,161,177,193,9,35,51,82,240,21,98,114,209,10,22,36,52,225,37,241,23,24,25,26,38,39,40,41,42,53,54,55,56,57,58,67,68,69,70,71,72,73,74,83,84,85,86,87,88,89,90,99,100,101,102,103,104,105,106,115,116,117,118,119,120,121,122,130,131,132,133,134,135,136,137,138,146,147,148,149,150,151,152,153,154,162,163,164,165,166,167,168,169,170,178,179,180,181,182,183,184,185,186,194,195,196,197,198,199,200,201,202,210,211,212,213,214,215,216,217,218,226,227,228,229,230,231,232,233,234,242,243,244,245,246,247,248,249,250];this.encode=function(a,b){b&&r(b),H=new Array,I=0,J=7,i(65496),k(),m(),l(a.width,a.height),n(),o();var c=0,d=0,e=0;I=0,J=7,this.encode.displayName="_encode_";for(var f,h,j,q,s,x,y,z,A,D=a.data,E=a.width,F=a.height,G=4*E,N=0;F>N;){for(f=0;G>f;){for(s=G*N+f,x=s,y=-1,z=0,A=0;64>A;A++)z=A>>3,y=4*(7&A),x=s+z*G+y,N+z>=F&&(x-=G*(N+1+z-F)),f+y>=G&&(x-=f+y-G+4),h=D[x++],j=D[x++],q=D[x++],K[A]=(O[h]+O[j+256>>0]+O[q+512>>0]>>16)-128,L[A]=(O[h+768>>0]+O[j+1024>>0]+O[q+1280>>0]>>16)-128,M[A]=(O[h+1280>>0]+O[j+1536>>0]+O[q+1792>>0]>>16)-128;c=p(K,B,c,t,v),d=p(L,C,d,u,w),e=p(M,C,e,u,w),f+=32}N+=8}if(J>=0){var P=[];P[1]=J+1,P[0]=(1<<J+1)-1,g(P)}i(65497);var Q="data:image/jpeg;base64,"+btoa(H.join(""));return H=[],Q},s()}return a.encode=function(b,c){var d=new a(c);return d.encode(b)},a}),b("runtime/html5/androidpatch",["runtime/html5/util","runtime/html5/jpegencoder","base"],function(a,b,c){var d,e=a.canvasToDataUrl;a.canvasToDataUrl=function(a,f,g){var h,i,j,k,l;return c.os.android?("image/jpeg"===f&&"undefined"==typeof d&&(k=e.apply(null,arguments),l=k.split(","),k=~l[0].indexOf("base64")?atob(l[1]):decodeURIComponent(l[1]),k=k.substring(0,2),d=255===k.charCodeAt(0)&&216===k.charCodeAt(1)),"image/jpeg"!==f||d?e.apply(null,arguments):(i=a.width,j=a.height,h=a.getContext("2d"),b.encode(h.getImageData(0,0,i,j),g))):e.apply(null,arguments)}}),b("runtime/html5/image",["base","runtime/html5/runtime","runtime/html5/util"],function(a,b,c){var d="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D";return b.register("Image",{modified:!1,init:function(){var a=this,b=new Image;b.onload=function(){a._info={type:a.type,width:this.width,height:this.height},a._metas||"image/jpeg"!==a.type?a.owner.trigger("load"):c.parseMeta(a._blob,function(b,c){a._metas=c,a.owner.trigger("load")})},b.onerror=function(){a.owner.trigger("error")},a._img=b},loadFromBlob:function(a){var b=this,d=b._img;b._blob=a,b.type=a.type,d.src=c.createObjectURL(a.getSource()),b.owner.once("load",function(){c.revokeObjectURL(d.src)})},resize:function(a,b){var c=this._canvas||(this._canvas=document.createElement("canvas"));this._resize(this._img,c,a,b),this._blob=null,this.modified=!0,this.owner.trigger("complete","resize")},crop:function(a,b,c,d,e){var f=this._canvas||(this._canvas=document.createElement("canvas")),g=this.options,h=this._img,i=h.naturalWidth,j=h.naturalHeight,k=this.getOrientation();e=e||1,f.width=c,f.height=d,g.preserveHeaders||this._rotate2Orientaion(f,k),this._renderImageToCanvas(f,h,-a,-b,i*e,j*e),this._blob=null,this.modified=!0,this.owner.trigger("complete","crop")},getAsBlob:function(a){var b,d=this._blob,e=this.options;if(a=a||this.type,this.modified||this.type!==a){if(b=this._canvas,"image/jpeg"===a){if(d=c.canvasToDataUrl(b,a,e.quality),e.preserveHeaders&&this._metas&&this._metas.imageHead)return d=c.dataURL2ArrayBuffer(d),d=c.updateImageHead(d,this._metas.imageHead),d=c.arrayBufferToBlob(d,a)}else d=c.canvasToDataUrl(b,a);d=c.dataURL2Blob(d)}return d},getAsDataUrl:function(a){var b=this.options;return a=a||this.type,"image/jpeg"===a?c.canvasToDataUrl(this._canvas,a,b.quality):this._canvas.toDataURL(a)},getOrientation:function(){return this._metas&&this._metas.exif&&this._metas.exif.get("Orientation")||1},info:function(a){return a?(this._info=a,this):this._info},meta:function(a){return a?(this._meta=a,this):this._meta},destroy:function(){var a=this._canvas;this._img.onload=null,a&&(a.getContext("2d").clearRect(0,0,a.width,a.height),a.width=a.height=0,this._canvas=null),this._img.src=d,this._img=this._blob=null},_resize:function(a,b,c,d){var e,f,g,h,i,j=this.options,k=a.width,l=a.height,m=this.getOrientation();~[5,6,7,8].indexOf(m)&&(c^=d,d^=c,c^=d),e=Math[j.crop?"max":"min"](c/k,d/l),j.allowMagnify||(e=Math.min(1,e)),f=k*e,g=l*e,j.crop?(b.width=c,b.height=d):(b.width=f,b.height=g),h=(b.width-f)/2,i=(b.height-g)/2,j.preserveHeaders||this._rotate2Orientaion(b,m),this._renderImageToCanvas(b,a,h,i,f,g)},_rotate2Orientaion:function(a,b){var c=a.width,d=a.height,e=a.getContext("2d");switch(b){case 5:case 6:case 7:case 8:a.width=d,a.height=c}switch(b){case 2:e.translate(c,0),e.scale(-1,1);break;case 3:e.translate(c,d),e.rotate(Math.PI);break;case 4:e.translate(0,d),e.scale(1,-1);break;case 5:e.rotate(.5*Math.PI),e.scale(1,-1);break;case 6:e.rotate(.5*Math.PI),e.translate(0,-d);break;case 7:e.rotate(.5*Math.PI),e.translate(c,-d),e.scale(-1,1);break;case 8:e.rotate(-.5*Math.PI),e.translate(-c,0)}},_renderImageToCanvas:function(){function b(a,b,c){var d,e,f,g=document.createElement("canvas"),h=g.getContext("2d"),i=0,j=c,k=c;for(g.width=1,g.height=c,h.drawImage(a,0,0),d=h.getImageData(0,0,1,c).data;k>i;)e=d[4*(k-1)+3],0===e?j=k:i=k,k=j+i>>1;return f=k/c,0===f?1:f}function c(a){var b,c,d=a.naturalWidth,e=a.naturalHeight;return d*e>1048576?(b=document.createElement("canvas"),b.width=b.height=1,c=b.getContext("2d"),c.drawImage(a,-d+1,0),0===c.getImageData(0,0,1,1).data[3]):!1}return a.os.ios?a.os.ios>=7?function(a,c,d,e,f,g){var h=c.naturalWidth,i=c.naturalHeight,j=b(c,h,i);return a.getContext("2d").drawImage(c,0,0,h*j,i*j,d,e,f,g)}:function(a,d,e,f,g,h){var i,j,k,l,m,n,o,p=d.naturalWidth,q=d.naturalHeight,r=a.getContext("2d"),s=c(d),t="image/jpeg"===this.type,u=1024,v=0,w=0;for(s&&(p/=2,q/=2),r.save(),i=document.createElement("canvas"),i.width=i.height=u,j=i.getContext("2d"),k=t?b(d,p,q):1,l=Math.ceil(u*g/p),m=Math.ceil(u*h/q/k);q>v;){for(n=0,o=0;p>n;)j.clearRect(0,0,u,u),j.drawImage(d,-n,-v),r.drawImage(i,0,0,u,u,e+o,f+w,l,m),n+=u,o+=l;v+=u,w+=m}r.restore(),i=j=null}:function(b){var c=a.slice(arguments,1),d=b.getContext("2d");d.drawImage.apply(d,c)}}()})}),b("runtime/html5/transport",["base","runtime/html5/runtime"],function(a,b){var c=a.noop,d=a.$;return b.register("Transport",{init:function(){this._status=0,this._response=null},send:function(){var b,c,e,f=this.owner,g=this.options,h=this._initAjax(),i=f._blob,j=g.server;g.sendAsBinary?(j+=(/\?/.test(j)?"&":"?")+d.param(f._formData),c=i.getSource()):(b=new FormData,d.each(f._formData,function(a,c){b.append(a,c)}),b.append(g.fileVal,i.getSource(),g.filename||f._formData.name||"")),g.withCredentials&&"withCredentials"in h?(h.open(g.method,j,!0),h.withCredentials=!0):h.open(g.method,j),this._setRequestHeader(h,g.headers),c?(h.overrideMimeType&&h.overrideMimeType("application/octet-stream"),a.os.android?(e=new FileReader,e.onload=function(){h.send(this.result),e=e.onload=null},e.readAsArrayBuffer(c)):h.send(c)):h.send(b)},getResponse:function(){return this._response},getResponseAsJson:function(){return this._parseJson(this._response)},getStatus:function(){return this._status},abort:function(){var a=this._xhr;a&&(a.upload.onprogress=c,a.onreadystatechange=c,a.abort(),this._xhr=a=null)},destroy:function(){this.abort()},_initAjax:function(){var a=this,b=new XMLHttpRequest,d=this.options;return!d.withCredentials||"withCredentials"in b||"undefined"==typeof XDomainRequest||(b=new XDomainRequest),b.upload.onprogress=function(b){var c=0;return b.lengthComputable&&(c=b.loaded/b.total),a.trigger("progress",c)},b.onreadystatechange=function(){return 4===b.readyState?(b.upload.onprogress=c,b.onreadystatechange=c,a._xhr=null,a._status=b.status,b.status>=200&&b.status<300?(a._response=b.responseText,a.trigger("load")):b.status>=500&&b.status<600?(a._response=b.responseText,a.trigger("error","server")):a.trigger("error",a._status?"http":"abort")):void 0},a._xhr=b,b},_setRequestHeader:function(a,b){d.each(b,function(b,c){a.setRequestHeader(b,c)})},_parseJson:function(a){var b;try{b=JSON.parse(a)}catch(c){b={}}return b}})}),b("runtime/html5/md5",["runtime/html5/runtime"],function(a){var b=function(a,b){return a+b&4294967295},c=function(a,c,d,e,f,g){return c=b(b(c,a),b(e,g)),b(c<<f|c>>>32-f,d)},d=function(a,b,d,e,f,g,h){return c(b&d|~b&e,a,b,f,g,h)},e=function(a,b,d,e,f,g,h){return c(b&e|d&~e,a,b,f,g,h)},f=function(a,b,d,e,f,g,h){return c(b^d^e,a,b,f,g,h)},g=function(a,b,d,e,f,g,h){return c(d^(b|~e),a,b,f,g,h)},h=function(a,c){var h=a[0],i=a[1],j=a[2],k=a[3];h=d(h,i,j,k,c[0],7,-680876936),k=d(k,h,i,j,c[1],12,-389564586),j=d(j,k,h,i,c[2],17,606105819),i=d(i,j,k,h,c[3],22,-1044525330),h=d(h,i,j,k,c[4],7,-176418897),k=d(k,h,i,j,c[5],12,1200080426),j=d(j,k,h,i,c[6],17,-1473231341),i=d(i,j,k,h,c[7],22,-45705983),h=d(h,i,j,k,c[8],7,1770035416),k=d(k,h,i,j,c[9],12,-1958414417),j=d(j,k,h,i,c[10],17,-42063),i=d(i,j,k,h,c[11],22,-1990404162),h=d(h,i,j,k,c[12],7,1804603682),k=d(k,h,i,j,c[13],12,-40341101),j=d(j,k,h,i,c[14],17,-1502002290),i=d(i,j,k,h,c[15],22,1236535329),h=e(h,i,j,k,c[1],5,-165796510),k=e(k,h,i,j,c[6],9,-1069501632),j=e(j,k,h,i,c[11],14,643717713),i=e(i,j,k,h,c[0],20,-373897302),h=e(h,i,j,k,c[5],5,-701558691),k=e(k,h,i,j,c[10],9,38016083),j=e(j,k,h,i,c[15],14,-660478335),i=e(i,j,k,h,c[4],20,-405537848),h=e(h,i,j,k,c[9],5,568446438),k=e(k,h,i,j,c[14],9,-1019803690),j=e(j,k,h,i,c[3],14,-187363961),i=e(i,j,k,h,c[8],20,1163531501),h=e(h,i,j,k,c[13],5,-1444681467),k=e(k,h,i,j,c[2],9,-51403784),j=e(j,k,h,i,c[7],14,1735328473),i=e(i,j,k,h,c[12],20,-1926607734),h=f(h,i,j,k,c[5],4,-378558),k=f(k,h,i,j,c[8],11,-2022574463),j=f(j,k,h,i,c[11],16,1839030562),i=f(i,j,k,h,c[14],23,-35309556),h=f(h,i,j,k,c[1],4,-1530992060),k=f(k,h,i,j,c[4],11,1272893353),j=f(j,k,h,i,c[7],16,-155497632),i=f(i,j,k,h,c[10],23,-1094730640),h=f(h,i,j,k,c[13],4,681279174),k=f(k,h,i,j,c[0],11,-358537222),j=f(j,k,h,i,c[3],16,-722521979),i=f(i,j,k,h,c[6],23,76029189),h=f(h,i,j,k,c[9],4,-640364487),k=f(k,h,i,j,c[12],11,-421815835),j=f(j,k,h,i,c[15],16,530742520),i=f(i,j,k,h,c[2],23,-995338651),h=g(h,i,j,k,c[0],6,-198630844),k=g(k,h,i,j,c[7],10,1126891415),j=g(j,k,h,i,c[14],15,-1416354905),i=g(i,j,k,h,c[5],21,-57434055),h=g(h,i,j,k,c[12],6,1700485571),k=g(k,h,i,j,c[3],10,-1894986606),j=g(j,k,h,i,c[10],15,-1051523),i=g(i,j,k,h,c[1],21,-2054922799),h=g(h,i,j,k,c[8],6,1873313359),k=g(k,h,i,j,c[15],10,-30611744),j=g(j,k,h,i,c[6],15,-1560198380),i=g(i,j,k,h,c[13],21,1309151649),h=g(h,i,j,k,c[4],6,-145523070),k=g(k,h,i,j,c[11],10,-1120210379),j=g(j,k,h,i,c[2],15,718787259),i=g(i,j,k,h,c[9],21,-343485551),a[0]=b(h,a[0]),a[1]=b(i,a[1]),a[2]=b(j,a[2]),a[3]=b(k,a[3])},i=function(a){var b,c=[];for(b=0;64>b;b+=4)c[b>>2]=a.charCodeAt(b)+(a.charCodeAt(b+1)<<8)+(a.charCodeAt(b+2)<<16)+(a.charCodeAt(b+3)<<24);return c},j=function(a){var b,c=[];for(b=0;64>b;b+=4)c[b>>2]=a[b]+(a[b+1]<<8)+(a[b+2]<<16)+(a[b+3]<<24);return c},k=function(a){var b,c,d,e,f,g,j=a.length,k=[1732584193,-271733879,-1732584194,271733878];for(b=64;j>=b;b+=64)h(k,i(a.substring(b-64,b)));for(a=a.substring(b-64),c=a.length,d=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],b=0;c>b;b+=1)d[b>>2]|=a.charCodeAt(b)<<(b%4<<3);if(d[b>>2]|=128<<(b%4<<3),b>55)for(h(k,d),b=0;16>b;b+=1)d[b]=0;return e=8*j,e=e.toString(16).match(/(.*?)(.{0,8})$/),f=parseInt(e[2],16),g=parseInt(e[1],16)||0,d[14]=f,d[15]=g,h(k,d),k},l=function(a){var b,c,d,e,f,g,i=a.length,k=[1732584193,-271733879,-1732584194,271733878];for(b=64;i>=b;b+=64)h(k,j(a.subarray(b-64,b)));for(a=i>b-64?a.subarray(b-64):new Uint8Array(0),c=a.length,d=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0],b=0;c>b;b+=1)d[b>>2]|=a[b]<<(b%4<<3);if(d[b>>2]|=128<<(b%4<<3),b>55)for(h(k,d),b=0;16>b;b+=1)d[b]=0;return e=8*i,e=e.toString(16).match(/(.*?)(.{0,8})$/),f=parseInt(e[2],16),g=parseInt(e[1],16)||0,d[14]=f,d[15]=g,h(k,d),k},m=["0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"],n=function(a){var b,c="";for(b=0;4>b;b+=1)c+=m[a>>8*b+4&15]+m[a>>8*b&15];return c},o=function(a){var b;for(b=0;b<a.length;b+=1)a[b]=n(a[b]);return a.join("")},p=function(a){return o(k(a))},q=function(){this.reset()};return"5d41402abc4b2a76b9719d911017c592"!==p("hello")&&(b=function(a,b){var c=(65535&a)+(65535&b),d=(a>>16)+(b>>16)+(c>>16);return d<<16|65535&c}),q.prototype.append=function(a){return/[\u0080-\uFFFF]/.test(a)&&(a=unescape(encodeURIComponent(a))),this.appendBinary(a),this},q.prototype.appendBinary=function(a){this._buff+=a,this._length+=a.length;var b,c=this._buff.length;for(b=64;c>=b;b+=64)h(this._state,i(this._buff.substring(b-64,b)));return this._buff=this._buff.substr(b-64),this},q.prototype.end=function(a){var b,c,d=this._buff,e=d.length,f=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];for(b=0;e>b;b+=1)f[b>>2]|=d.charCodeAt(b)<<(b%4<<3);return this._finish(f,e),c=a?this._state:o(this._state),this.reset(),c},q.prototype._finish=function(a,b){var c,d,e,f=b;if(a[f>>2]|=128<<(f%4<<3),f>55)for(h(this._state,a),f=0;16>f;f+=1)a[f]=0;c=8*this._length,c=c.toString(16).match(/(.*?)(.{0,8})$/),d=parseInt(c[2],16),e=parseInt(c[1],16)||0,a[14]=d,a[15]=e,h(this._state,a)},q.prototype.reset=function(){return this._buff="",this._length=0,this._state=[1732584193,-271733879,-1732584194,271733878],this},q.prototype.destroy=function(){delete this._state,delete this._buff,delete this._length},q.hash=function(a,b){/[\u0080-\uFFFF]/.test(a)&&(a=unescape(encodeURIComponent(a)));var c=k(a);return b?c:o(c)},q.hashBinary=function(a,b){var c=k(a);return b?c:o(c)},q.ArrayBuffer=function(){this.reset()},q.ArrayBuffer.prototype.append=function(a){var b,c=this._concatArrayBuffer(this._buff,a),d=c.length;for(this._length+=a.byteLength,b=64;d>=b;b+=64)h(this._state,j(c.subarray(b-64,b)));return this._buff=d>b-64?c.subarray(b-64):new Uint8Array(0),this},q.ArrayBuffer.prototype.end=function(a){var b,c,d=this._buff,e=d.length,f=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
	for(b=0;e>b;b+=1)f[b>>2]|=d[b]<<(b%4<<3);return this._finish(f,e),c=a?this._state:o(this._state),this.reset(),c},q.ArrayBuffer.prototype._finish=q.prototype._finish,q.ArrayBuffer.prototype.reset=function(){return this._buff=new Uint8Array(0),this._length=0,this._state=[1732584193,-271733879,-1732584194,271733878],this},q.ArrayBuffer.prototype.destroy=q.prototype.destroy,q.ArrayBuffer.prototype._concatArrayBuffer=function(a,b){var c=a.length,d=new Uint8Array(c+b.byteLength);return d.set(a),d.set(new Uint8Array(b),c),d},q.ArrayBuffer.hash=function(a,b){var c=l(new Uint8Array(a));return b?c:o(c)},a.register("Md5",{init:function(){},loadFromBlob:function(a){var b,c,d=a.getSource(),e=2097152,f=Math.ceil(d.size/e),g=0,h=this.owner,i=new q.ArrayBuffer,j=this,k=d.mozSlice||d.webkitSlice||d.slice;c=new FileReader,(b=function(){var l,m;l=g*e,m=Math.min(l+e,d.size),c.onload=function(b){i.append(b.target.result),h.trigger("progress",{total:a.size,loaded:m})},c.onloadend=function(){c.onloadend=c.onload=null,++g<f?setTimeout(b,1):setTimeout(function(){h.trigger("load"),j.result=i.end(),b=a=d=i=null,h.trigger("complete")},50)},c.readAsArrayBuffer(k.call(d,l,m))})()},getResult:function(){return this.result}})}),b("runtime/flash/runtime",["base","runtime/runtime","runtime/compbase"],function(b,c,d){function e(){var a;try{a=navigator.plugins["Shockwave Flash"],a=a.description}catch(b){try{a=new ActiveXObject("ShockwaveFlash.ShockwaveFlash").GetVariable("$version")}catch(c){a="0.0"}}return a=a.match(/\d+/g),parseFloat(a[0]+"."+a[1],10)}function f(){function d(a,b){var c,d,e=a.type||a;c=e.split("::"),d=c[0],e=c[1],"Ready"===e&&d===j.uid?j.trigger("ready"):f[d]&&f[d].trigger(e.toLowerCase(),a,b)}var e={},f={},g=this.destroy,j=this,k=b.guid("webuploader_");c.apply(j,arguments),j.type=h,j.exec=function(a,c){var d,g=this,h=g.uid,k=b.slice(arguments,2);return f[h]=g,i[a]&&(e[h]||(e[h]=new i[a](g,j)),d=e[h],d[c])?d[c].apply(d,k):j.flashExec.apply(g,arguments)},a[k]=function(){var a=arguments;setTimeout(function(){d.apply(null,a)},1)},this.jsreciver=k,this.destroy=function(){return g&&g.apply(this,arguments)},this.flashExec=function(a,c){var d=j.getFlash(),e=b.slice(arguments,2);return d.exec(this.uid,a,c,e)}}var g=b.$,h="flash",i={};return b.inherits(c,{constructor:f,init:function(){var a,c=this.getContainer(),d=this.options;c.css({position:"absolute",top:"-8px",left:"-8px",width:"9px",height:"9px",overflow:"hidden"}),a='<object id="'+this.uid+'" type="application/x-shockwave-flash" data="'+d.swf+'" ',b.browser.ie&&(a+='classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" '),a+='width="100%" height="100%" style="outline:0"><param name="movie" value="'+d.swf+'" /><param name="flashvars" value="uid='+this.uid+"&jsreciver="+this.jsreciver+'" /><param name="wmode" value="transparent" /><param name="allowscriptaccess" value="always" /></object>',c.html(a)},getFlash:function(){return this._flash?this._flash:(this._flash=g("#"+this.uid).get(0),this._flash)}}),f.register=function(a,c){return c=i[a]=b.inherits(d,g.extend({flashExec:function(){var a=this.owner,b=this.getRuntime();return b.flashExec.apply(a,arguments)}},c))},e()>=11.4&&c.addRuntime(h,f),f}),b("runtime/flash/filepicker",["base","runtime/flash/runtime"],function(a,b){var c=a.$;return b.register("FilePicker",{init:function(a){var b,d,e=c.extend({},a);for(b=e.accept&&e.accept.length,d=0;b>d;d++)e.accept[d].title||(e.accept[d].title="Files");delete e.button,delete e.id,delete e.container,this.flashExec("FilePicker","init",e)},destroy:function(){this.flashExec("FilePicker","destroy")}})}),b("runtime/flash/image",["runtime/flash/runtime"],function(a){return a.register("Image",{loadFromBlob:function(a){var b=this.owner;b.info()&&this.flashExec("Image","info",b.info()),b.meta()&&this.flashExec("Image","meta",b.meta()),this.flashExec("Image","loadFromBlob",a.uid)}})}),b("runtime/flash/transport",["base","runtime/flash/runtime","runtime/client"],function(b,c,d){var e=b.$;return c.register("Transport",{init:function(){this._status=0,this._response=null,this._responseJson=null},send:function(){var a,b=this.owner,c=this.options,d=this._initAjax(),f=b._blob,g=c.server;d.connectRuntime(f.ruid),c.sendAsBinary?(g+=(/\?/.test(g)?"&":"?")+e.param(b._formData),a=f.uid):(e.each(b._formData,function(a,b){d.exec("append",a,b)}),d.exec("appendBlob",c.fileVal,f.uid,c.filename||b._formData.name||"")),this._setRequestHeader(d,c.headers),d.exec("send",{method:c.method,url:g,forceURLStream:c.forceURLStream,mimeType:"application/octet-stream"},a)},getStatus:function(){return this._status},getResponse:function(){return this._response||""},getResponseAsJson:function(){return this._responseJson},abort:function(){var a=this._xhr;a&&(a.exec("abort"),a.destroy(),this._xhr=a=null)},destroy:function(){this.abort()},_initAjax:function(){var b=this,c=new d("XMLHttpRequest");return c.on("uploadprogress progress",function(a){var c=a.loaded/a.total;return c=Math.min(1,Math.max(0,c)),b.trigger("progress",c)}),c.on("load",function(){var d,e=c.exec("getStatus"),f=!1,g="";return c.off(),b._xhr=null,e>=200&&300>e?f=!0:e>=500&&600>e?(f=!0,g="server"):g="http",f&&(b._response=c.exec("getResponse"),b._response=decodeURIComponent(b._response),d=a.JSON&&a.JSON.parse||function(a){try{return new Function("return "+a).call()}catch(b){return{}}},b._responseJson=b._response?d(b._response):{}),c.destroy(),c=null,g?b.trigger("error",g):b.trigger("load")}),c.on("error",function(){c.off(),b._xhr=null,b.trigger("error","http")}),b._xhr=c,c},_setRequestHeader:function(a,b){e.each(b,function(b,c){a.exec("setRequestHeader",b,c)})}})}),b("runtime/flash/blob",["runtime/flash/runtime","lib/blob"],function(a,b){return a.register("Blob",{slice:function(a,c){var d=this.flashExec("Blob","slice",a,c);return new b(d.uid,d)}})}),b("runtime/flash/md5",["runtime/flash/runtime"],function(a){return a.register("Md5",{init:function(){},loadFromBlob:function(a){return this.flashExec("Md5","loadFromBlob",a.uid)}})}),b("preset/all",["base","widgets/filednd","widgets/filepaste","widgets/filepicker","widgets/image","widgets/queue","widgets/runtime","widgets/upload","widgets/validator","widgets/md5","runtime/html5/blob","runtime/html5/dnd","runtime/html5/filepaste","runtime/html5/filepicker","runtime/html5/imagemeta/exif","runtime/html5/androidpatch","runtime/html5/image","runtime/html5/transport","runtime/html5/md5","runtime/flash/filepicker","runtime/flash/image","runtime/flash/transport","runtime/flash/blob","runtime/flash/md5"],function(a){return a}),b("webuploader",["preset/all"],function(a){return a}),c("webuploader")});
