jQuery.tab = function (chooser) {
    $(chooser + " li").click(function () {
        var index = $(this).index();
        $(".tabContent").children().eq(index).show().siblings().hide();
    });
    $(".tabContent").children().eq(0).show().siblings().hide();
};

$(function () {
    $('.ajax_get').click(function () {
        var target = $(this).attr('href');
        $.get(target, '', function () {
            window.location.reload();
        });
        return false;
    });

    $('.danger_del').click(function () {
        var url = $(this).data('url');
        var message = $(this).data('message');
        if (!message){
            message = '删除该分类将会删除该分类下的所有内容，是否继续？';
        }
        layer.confirm(message, {
            btn: ['继续','放弃'] //按钮
        }, function(){
            $.ajax({
                url: url,
                type: 'GET',
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    if (result.code == 1) {
                        successRedirect(result.msg, result.url, 1);
                    } else {
                        zcAlert(result.msg,0);
                        layer.closeAll("loading");
                        btn.attr('disabled', false);
                    }
                },
                error: function () {
                    layer.msg('请求失败,网络连接超时', {icon: 5, time: 2000});
                    layer.closeAll("loading");
                    btn.attr('disabled', false);
                }
            });
        }, function(){
            layer.msg('操作已取消');
        });
    })

    $('.check_del').click(function () {
        var url = $(this).data('url');
        var message = $(this).data('message');
        if (!message){
            message ='将要删除此信息，是否继续？';
        }
        layer.confirm(message, {
            btn: ['继续','放弃'] //按钮
        }, function(){
            $.ajax({
                url: url,
                type: 'GET',
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    if (result.code == 1) {
                        successRedirect(result.msg, result.url, 1);
                    } else {
                        zcAlert(result.msg,0);
                        layer.closeAll("loading");
                        btn.attr('disabled', false);
                    }
                },
                error: function () {
                    layer.msg('请求失败,网络连接超时', {icon: 5, time: 2000});
                    layer.closeAll("loading");
                    btn.attr('disabled', false);
                }
            });
        }, function(){
            layer.msg('操作已取消');
        });
    });


    $('.batchSign').click(function () {
        var url = $(this).data('url');
        var btn = $(this);
        message ='系统将对标记的订单全部通过,是否继续?';
        layer.confirm(message, {
            btn: ['继续','放弃'] //按钮
        }, function(){
            $.ajax({
                url: url,
                type: 'GET',
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {
                    if (result.code == 1) {
                        successRedirect(result.msg, result.url, 1);
                    } else {
                        zcAlert(result.msg,0);
                        layer.closeAll("loading");
                        btn.attr('disabled', false);
                    }
                },
                error: function () {
                    layer.msg('请求失败,网络连接超时', {icon: 5, time: 2000});
                    layer.closeAll("loading");
                    btn.attr('disabled', false);
                }
            });
        }, function(){
            layer.msg('操作已取消');
        });
    });


    $('.signOrder').click(function () {
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.code == 1) {
                    window.location.href = result.url;
                    // successRedirect(result.msg, result.url, 1);
                } else {
                    zcAlert(result.msg,0);
                    layer.closeAll("loading");

                }
                },
                error: function () {
                    layer.msg('请求失败,网络连接超时', {icon: 5, time: 2000});
                    layer.closeAll("loading");

                }
            });

    });



    $("table").delegate(".check_all", "click", function () {
        $('.ids').prop('checked', $(this).prop('checked'));
        $('.ajax_post').prop('disabled', !$(this).prop('checked'));
    });


    $("table").delegate(".ids", "click", function () {
        $('.check_all').prop('checked', $('.ids:not(:checked)').length == 0);
        $('.ajax_post').prop('disabled', $('.ids:checked').length == 0);
    });


    $('.page_sub').click(function () {
        var p = $("input[name='p']").val();
        var url = $(this).attr('data-p');
        window.location.href = url + '?p=' + p;
    });


    $('.submit').click(function () {
        var btn = $(this);
        btn.attr('disabled', 'disabled');
        var load=layer.load(1, {
            shade: [0.3,'#999999'] //0.1透明度的白色背景
        });
        var form = btn.closest("form");
        var id = btn.parents('form').attr('id');
        var formData =  new FormData($('#'+id)[0]);
        var url = form.attr("action");
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.code == 1) {
                    successRedirect(result.msg, result.url, 1);
                }
                else if (result.code == 3){
                    topRedirect(result.msg, result.data, 1);
                }
                else {
                    zcAlert(result.msg,0);
                    layer.closeAll("loading");
                    btn.attr('disabled', false);
                }
            },
            error: function () {
                layer.msg('请求失败,网络连接超时', {icon: 5, time: 2000});
                layer.closeAll("loading");
                btn.attr('disabled', false);
            }
        });
        return false;
    });


    $('.bath_all').click(function () {
        var btn = $(this);
        btn.attr('disabled', 'disabled');
        var load=layer.load(1, {
            shade: [0.3,'#999999'] //0.1透明度的白色背景
        });
        var form = btn.closest("form");
        var id = btn.parents('form').attr('id');
        var formData =  new FormData($('#'+id)[0]);
        var url = form.attr("action");
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.code == 1) {
                    layer.closeAll("loading");
                    btn.attr('disabled', false);
                    $('.panel-body').html(result.data)
                } else {
                    zcAlert(result.msg,0);
                    layer.closeAll("loading");
                    btn.attr('disabled', false);
                }
            },
            error: function () {
                layer.msg('请求失败,网络连接超时', {icon: 5, time: 2000});
                layer.closeAll("loading");
                btn.attr('disabled', false);
            }
        });
        return false;
    });



    $('.change').click(function () {
        var load=layer.load(1, {
            shade: [0.3,'#999999'] //0.1透明度的白色背景
        });
        var url = $(this).data('url')
        $.ajax({
            url: url,
            type: 'get',
            success: function (result) {
                if (result.code == 1) {
                    successRedirect(result.msg, result.url, 1);
                } else {
                    zcAlert(result.msg,0);
                    layer.closeAll("loading");
                }
            },
            error: function () {
                layer.msg('请求失败,网络连接超时', {icon: 5, time: 2000});
                layer.closeAll("loading");
            }
        });
        return false;
    });

    $('.batch').click(function () {
        var btn = $(this);
        btn.attr('disabled', 'disabled');
        var load=layer.load(1, {
            shade: [0.3,'#999999']
        });
        var form = btn.closest("form");
        var formData = new FormData($("#form-order")[0]);
        var url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.code == 1) {
                    successRedirect(result.msg, result.url, 1);
                } else {
                    zcAlert(result.msg,0);
                    layer.closeAll("loading");
                    btn.attr('disabled', false);
                }
            },
            error: function () {
                layer.msg('请求失败,网络连接超时', {icon: 5, time: 2000});
                layer.closeAll("loading");
                btn.attr('disabled', false);
            }
        });
        return false;
    });


    $('.win_submit').click(function () {
        var btn = $(this);
        btn.attr('disabled', 'disabled');
        var load=layer.load(1, {
            shade: [0.3,'#999999']
        });
        var form = btn.closest("form");
        var formData = new FormData($("form")[0]);
        var url = form.attr("action");
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.code == 1) {
                    layer.msg(result.msg, {icon: 1, time: 2000});
                    setTimeout(function(){
                        window.parent.location.reload();
                        window.parent.layer.closeAll();
                    },2000);
                } else {
                    zcAlert(result.msg,0);
                    layer.closeAll("loading");
                    btn.attr('disabled', false);
                }
            },
            error: function () {
                layer.msg('请求失败,网络连接超时', {icon: 5, time: 2000});
                layer.closeAll("loading");
                btn.attr('disabled', false);
            }
        });
        return false;
    });


    $(".tabChange li").click(function () {
        var index = $(this).index();
        $(".tabContent").children().eq(index).show().siblings().hide();
    });
});



function successRedirect(mag,url){
    layer.msg(mag, {icon: 1, time: 2000});
    setTimeout(function(){
        window.location.href = url;
        layer.closeAll("loading");
    },2000);
}

function topRedirect(mag,url){
    layer.msg(mag, {icon: 1, time: 2000});
    setTimeout(function(){
        top.location.href = url;
        layer.closeAll("loading");
    },2000);
}

function Area_list(obj,boxId,cityname,districtname){

    var $this = $(obj);

    var typeVal = $this.attr("id"); // 联动类型
    var pid = $this.val(); // 要联动的父级ID



    $.post("/location/", {type : typeVal,id : pid,rdm : new Date().getTime()},function(data) {

        if (typeVal == "province") {	//选中“省”其中的值
            console.log(cityname);
            $htmlStr = '<select id="city" name="'+cityname+'"  class="select location2"  onchange="Area_list(this,\''+boxId+'\',\''+cityname+'\',\''+districtname+'\')" >';
            $htmlStr+="<option value=''>--请选择--</option>";
            $.each(data, function(i, item) {
                var opt = "<option value='" + item.id
                    + "'>" + item.name
                    + "</option>";
                $htmlStr += opt;
            });
            $htmlStr += "</select>";
            $("#"+boxId+" #city-block").html($htmlStr);
            $("#"+boxId+" #district-block").html("");

        }else if (typeVal == "city") {	//选中“市”其中的值
            $htmlStr = '<select id="district" name="'+districtname+'" class="select location2">';
            $.each(data, function(i, item) {
                var opt = "<option value='"
                    + item.id + "'>"
                    + item.name + "</option>";
                $htmlStr += opt;
            });
            $htmlStr += "</select>";
            $("#"+boxId+" #district-block").html($htmlStr);
        }
    }, "json");

}

function City_list(obj,boxId,cityname){

    var $this = $(obj);

    var typeVal = $this.attr("id"); // 联动类型
    var pid = $this.val(); // 要联动的父级ID

    $.post("/location/", {type : typeVal,id : pid,rdm : new Date().getTime()},function(data) {

        $htmlStr = '<select id="city2" name="'+cityname+'"  class="select location2"  >';
        $htmlStr+="<option>--请选择--</option>";
        $.each(data, function(i, item) {
            var opt = "<option value='" + item.id
                + "'>" + item.name
                + "</option>";
            $htmlStr += opt;
        });
        $htmlStr += "</select>";

        $("#"+boxId+" #city1").html($htmlStr);
    }, "json");
}

function takeName(name,len) {
    var Name = '';
    var myDate = new Date();
    var myTime = myDate.getFullYear()+myDate.getMonth()+myDate.getDate()+myDate.getHours()+myDate.getMinutes()+myDate.getSeconds();
    len = len || 1;
    if (name != ''){
        Name += name;
    }
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    var maxPos = chars.length;
    for (var i = 0; i < len; i++) {
        Name += chars.charAt(Math.floor(Math.random() * maxPos));
    }
    Name += myTime+Math.ceil(Math.random()*9999).toString();
    return Name;
}

function removeFile(fileId){
    var file = $("#"+fileId) ;
    file.after(file.clone().val(""));
    file.remove();
}