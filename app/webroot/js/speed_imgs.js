/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
"use strict";
var global = {},
        cur_fold_id = 0,
        mode = 'product',
        finish = 0,
//mode("list", "move")
        operator_mode = "list",
//type("fold","img");
        list_type = "fold",
        DELAY = 200, clicks = 0, timer = null,
        click_node = true,
        has_load_tree = false;

var actived_folds = [], actived_images = [], postData = {"data": {"is_paging": 'true'}};
var tree;

var initStart = function() {
    list_type = "fold";
    $(".img-ope").hide();
    $(".fold-ope").hide();
    $("#mk-bt-add-fold").show();
};

var setOpeForFold = function() {
    list_type = "fold";
    $(".img-ope").hide();
    $(".fold-ope").show();
};

var setOpeForImg = function() {
    list_type = "img";
    $(".fold-ope").hide();
    $(".img-ope").show();
};

var setOpeForMix = function() {
    list_type = "mix";
    $("#btns input").hide();
    $("#mk-bt-upload").show();
    $("#mk-bt-add-fold").show();
};
//   $(".mk-scroll-tips").jCarouselLite({
//       auto: 800,
//    speed: 1000,
//         vertical: true
//    });

var _upload_images = function() {
    if (operator_mode !== 'list')
        return false;
    $('#fileupload').fileupload({
        url: '/uploadlers/send.json',
        formData: {mode: mode, fold_id: cur_fold_id},
        maxFileSize: 1000000,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        maxNumberOfFiles: 100
    }).bind("fileuploadstop", function(e, data) {
        loadTree('list', cur_fold_id, {"data": {"is_paging": 'true'}});
        if (list_type !== 'img') {
            setOpeForImg();
        }
    });

    $("#dialog-message").dialog({
        modal: true,
        position: {my: "top-2", at: "top", of: window},
        draggable: false,
        width: 960,
        height: 600,
        resizable: false,
        show: 'blind',
        buttons: {
            "退 出": function() {
                $(this).dialog("close");
            }
        }
    });
};

var _add_fold = function() {
    if (operator_mode !== 'list')
        return false;
    $("#dialog-fold").dialog({
        resizable: false,
        position: {my: "top-2", at: "top", of: window},
        draggable: false,
        height: 250,
        width: 700,
        modal: true, show: 'blind',
        buttons: [{
                text: "创建新相册",
                click: function() {
                    create_fold(cur_fold_id, $("#mk-fold-name").val());
                    $(this).dialog("close");
                }
            },
            {text: "取消",
                click: function() {
                    $(this).dialog("close");
                }
            }
        ]
    });
};

var _init_move = function() {
    if (!check_actived_items())
        return false;
    _start_move();
};

var _start_move = function() {
    operator_mode = "move";
    actived_folds = [];
    actived_images = [];

    $("#mk-confirm-move").bind('click', function() {

        if (list_type === 'fold') {
            moveFolds();
        } else if (list_type === 'img') {
            moveImages();
        }
    });
    $("#mk-select-root-a").bind('click', function(e) {
        e.preventDefault();
        $("#mk-target-fold").val("/");
        $("#mk-target-fold-id").val(0);
    });
    $("#mk-cancel-move").bind('click', _close_move);
    $("#mk-items-move-div").slideDown();

};
var _close_move = function() {
    operator_mode = "list";
    $("#mk-confirm-move").unbind('click');
    $("#mk-select-root-a").unbind('click');
    $("#mk-cancel-move").unbind('click');
    $("#mk-items-move-div").slideUp();
};


var moveFolds = function() {
    var fold_ids = actived_folds.length > 0 ? actived_folds : get_actived_folds();
    if (fold_ids.length == 0) {
        show_tips("forbin", "请先选择需要移动的相册");
        return;
    }
    var target_fold_id = $("#mk-target-fold-id").val();
    _move_folds(fold_ids, target_fold_id);
    _close_move();
};

var moveImages = function() {
    var img_ids = actived_images.length > 0 ? actived_images : get_actived_images();
    if (img_ids.length == 0) {
        show_tips("forbin", "请先选择需要移动的图片");
        return;
    }
    var target_fold_id = $("#mk-target-fold-id").val();
    _move_images(img_ids, target_fold_id);
    _close_move();
};

var _move_folds = function(fold_ids, target_id) {
    show_tips("loading", "移动中，请稍等.......");
    var o = {"fold_ids": fold_ids, "target_id": target_id};
    $.ajax({
        url: '/speedfolds/move/.json',
        type: 'post',
        data: o,
        dataType: 'json',
        success: function(data) {
            if (data.err == 0) {
                clear_activited_items();
            } else if (data.err == 1) {
                show_tips("fail", data.msg);
            } else if (data.err == 2) {
                var msg = "";
                for (var i = 0; i < data.msg.length; i++) {
                    if (data.msg[i].err == 0) {
                        msg += "相册 [" + $("#fold_" + data.msg[i]['id']).attr("title") + "]  已经移动! <br/>";
                        $("#fold_" + data.msg[i]['id']).remove();
                    } else {
                        msg += "相册 [" + $("#fold_" + data.msg[i]['id']).attr("title") + "] 不能移到子目录或自身! <br/>";
                    }
                }
                show_tips("ok", msg);
            }
        }
    });
};

var _move_images = function(images, target_id) {
    var o = {"images": images, "target_id": target_id};
    show_tips("loading", "图片移动中......");
    $.ajax({
        url: '/images/move/.json',
        type: 'post',
        data: o,
        dataType: 'json',
        success: function(data) {
            var msg = "";
            if (data.err == 0) {
                clear_activited_items(data.images);
                show_tips("ok", data.msg);
            } else if (data.err == 1) {
                show_tips("fail", data.msg);
            } else if (data.err == 2) {
                var msg = "";
                for (var i = 0; i < data.msg.length; i++) {
                    if (data.msg[i].err == 0) {
                        msg += "相册 [" + $("#img_" + data.msg[i]['id']).attr("title") + "]  已经移动! <br/>";
                        $("#fold_" + data.msg[i]['id']).remove();
                    } else {
                        msg += "相册 [" + $("#img_" + data.msg[i]['id']).attr("title") + "] 不能移到! <br/>";
                    }
                }
                show_tips("ok", msg);
            }
            _close_move();

        }
    });
};

var get_actived_folds = function() {
    var ids = [];
    $("#mk-list-content").find(".mk-img-active").each(function() {
        ids.push($(this).data("foldid"));
    });
    return ids;
};

var get_actived_images = function() {
    var ids = [];
    $("#mk-list-content").find(".mk-img-active").each(function() {
        ids.push($(this).data("imgid"));
    });
    return ids;
};

var clear_activited_items = function(items) {
    if (typeof items == 'undefined') {
        items = $("#mk-list-content").find(".mk-img-active").remove();
    } else {
        var item_prex = list_type == 'fold' ? "fold_" : "img_";
        for (var i = 0; i < items.length; i++) {
            $("#" + item_prex + items[i]).remove();
        }
    }

};


var create_fold = function(parent_id, fold_name) {
    show_tips("loading", "相册创建中.......");
    var o = {'data': {'pid': parent_id, 'name': fold_name}};
    $.ajax({
        url: '/speedfolds/add.json',
        type: 'post',
        data: o,
        dataType: 'json',
        success: function(data) {
            if (data.err == 0) {
                loadTree('list', parent_id, {"data": {"is_paging": 'true'}});
                if (list_type !== 'fold') {
                    setOpeForFold();
                }
                show_tips("ok", "相册 [" + fold_name + "] 创建成功");
            } else {
                show_tips("fail", "data.msg");
            }

//                var fold = tmpl("template-fold", data);
//                if ($("#mk-fold-return").length > 0) {
//                    $('#mk-fold-return').after(fold);
//                } else {
//                    $('#mk-list-content').prepend(fold);
//                }
//                bind_folds("fold_" + data.fold.id);
        }
    });
};


var check_actived_items = function() {
    if ($("#mk-list-content").find(".mk-img-active").length === 0) {
        show_tips("forbin", "请先选择操作项目。红色框代表选择。");
        return false;
    }
    return true;
};



var deleteManyFolds = function(p) {
    if (operator_mode !== 'list')
        return false;
    var confirm_result = 0;
    if (typeof p == 'number') {
        confirm_result = parseInt(p);
    }

    if (confirm_result === 0) {
        if (!check_actived_items())
            return false;
        _confirm_operate("确认删除选择的相册？ 请先确认所选择的相册及子相册内没有图片", deleteManyFolds);
        return false;
    } else if (confirm_result < 0) {
        return false;
    }

    var ids = [];
    $("#mk-list-content").find(".mk-img-active").each(function() {
        ids.push($(this).data("foldid"));
    });
    _delete_folds(ids);
};

var deleteFold = function(p) {
    var confirm_result = 0;
    if (typeof p == 'number') {
        confirm_result = parseInt(p);
    }

    if (confirm_result === 0) {
        _confirm_operate("确认删除选择的相册？ 请先确认所选择的相册及子相册内没有图片", deleteFold);
        return false;
    } else if (confirm_result < 0) {
        return false;
    }
    var ids = actived_folds;
    _delete_folds(ids);
};

var deleteManyImages = function(p) {
    if (operator_mode !== 'list')
        return false;
    var confirm_result = 0;
    if (typeof p == 'number') {
        confirm_result = parseInt(p);
    }

    if (confirm_result === 0) {
        if (!check_actived_items())
            return false;
        _confirm_operate("确认删除选择图片？ 删除的图片将无法恢复.", deleteManyImages);
        return false;
    } else if (confirm_result < 0) {
        return false;
    }

    var ids = [];
    var fold_id;
    $("#mk-list-content").find(".mk-img-active").each(function() {
        if (typeof fold_id == 'undefined') {
            fold_id = $(this).data('foldid');
        }
        ids.push($(this).data("imgid"));
    });
    _delete_images(ids, fold_id);
};
var deleteImage = function(p) {
    var confirm_result = 0;
    if (typeof p == 'number') {
        confirm_result = parseInt(p);
    }

    if (confirm_result === 0) {
        _confirm_operate("确认删除选择图片？ 删除的图片将无法恢复.", deleteImage);
        return false;
    } else if (confirm_result < 0) {
        return false;
    }
    var fold_id = actived_folds[0];
    var ids = actived_images;
    _delete_images(ids, fold_id);
};

var _confirm_operate = function(content, callback) {
    $("#dialog-confirm").find("#dialog-content").html(content).end().dialog({
        resizable: false,
        position: {my: "top-2", at: "top", of: window},
        draggable: false,
        height: 250,
        width: 700,
        modal: true, show: 'blind',
        buttons: {
            "确认": function() {
                $(this).dialog("close");
                callback(1);
            },
            "取消": function() {
                $(this).dialog("close");
            }
        }
    });

};

var _delete_images = function(ids, fold_id) {
    var o = {'ids': ids, 'fold_id': fold_id};
    show_tips("loading", "图片删除中......");
    $.ajax({
        url: '/images/delete/.json',
        type: 'post',
        data: o,
        dataType: 'json',
        success: function(data) {
            var msg = "";
            for (var i = 0; i < data.length; i++) {
                if (data[i].err == 0) {
                    msg += "图片 [" + $("#img_" + data[i]['id']).attr('title') + "] 已经成功删除!<br/>";
                    $("#img_" + data[i]['id']).remove();
                } else {
                    msg += "图片 [" + $("#img_" + data[i]['id']).attr('title') + "] 没有被删除，请重试!<br/>";
                }
            }
            show_tips("ok", msg);
        }
    });
};

var clear_check_sel = function() {
    $("#mk-sel-all, #mk-sel-reverse").attr("checked", false);
};

var _delete_folds = function(ids) {
    show_tips("loading", "相册删除中......");
    var o = {'ids': ids};
    $.ajax({
        url: '/speedfolds/delete/.json',
        type: 'post',
        data: o,
        dataType: 'json',
        success: function(data) {
            var msg = "";
            for (var i = 0; i < data.length; i++) {
                if (data[i].err == 0) {
                    msg += "相册 [" + $("#fold_" + data[i]['id']).attr("title") + "] 已经成功删除!<br/>";
                    $("#fold_" + data[i]['id']).remove();
                } else {
                    msg += "相册 [" + $("#fold_" + data[i]['id']).attr("title") + "] 没有被删除，请先删除图片!<br/>";
                }
            }
            show_tips("ok", msg);

        }
    });
};

var show_dialog = function(content) {
    $("#dialog-common").find("#dialog-content").html(content).end().dialog({
        resizable: false,
        position: {my: "top-2", at: "top", of: window},
        draggable: false,
        height: 250,
        width: 700,
        modal: true, show: 'blind',
        buttons: {
            "我知道了": function() {
                $(this).dialog("close");
            }
        }
    });
};


var loadImages = function(fold_id, url) {
    setOpeForImg();
    cur_fold_id = fold_id;
    clear_check_sel();
    if (typeof url === 'undefined')
        var url = '/images/search/' + fold_id + '/.json';
    show_tips("loading", "加载中.......");
    var xhr = _ajax_load_data(url, postData, 'post', 'json');
    xhr.done(function(data, textStatus, jqXHR) {
        _show_album(data);
        _add_history_data(data);
        hide_tips();
    });
};

var loadTree = function(type, fold_id, postData, url) {
    clear_check_sel();
    cur_fold_id = fold_id;
    if (typeof postData === 'undefined')
        postData = {};

    if (typeof url === 'undefined')
        var url = '/speedfolds/search/' + fold_id + '/.json';

    show_tips("loading", "加载中.......");

    var xhr = _ajax_load_data(url, postData, 'post', 'json');
    xhr.done(function(data, textStatus, jqXHR) {
        if (type === 'all') {
            $("#mk-tree").html(tmpl('template-tree', data));
            initTree();
        } else if (type === 'list') {
            _show_album(data);
            _add_history_data(data);
        }
        hide_tips();
    });
};

var _add_history_data = function(data) {
    var path = "/album/root";
    if (data.track.length > 0) {
        path = "/album/dir=root/path=";
        var album_path = "";
        for (var i = 0; i < data.track.length; i++) {
            album_path += ":" + data.track[i].Speedfold.name;
        }

        path = path + encodeURIComponent(album_path);

    }
    if (data.page && data.page>1) {
        path += "/" + data.page;
    }
    History.pushState(data, "SPEED TRADE 相册空间", path);
};

function _show_album(data) {
    if (data.listType === 'folds') {
        _output_folds(data);
    } else if (data.listType === 'imgs') {
        _output_images(data);
    }
}

var _output_folds = function(data) {
    data.folds.length > 0 ? setOpeForFold() : setOpeForMix();
    $("#mk-list-content").html(tmpl('template-fold-list', data));
    bind_folds();
};

var _output_images = function(data) {
    data.images.length > 0 ? setOpeForImg() : setOpeForMix();
    $("#mk-list-content").html(tmpl('template-image-list', data));
    bind_images();
};

var initTree = function() {
    tree = $("#mk-tree").jstree({"ui": {"select_limit": 1}, "plugins": ["themes", "json_data", "ui", "search", "adv_search"],
        "json_data": {
            "ajax": {
                "url": "/speedfolds/search/.json?type=l",
                "data": function(n) {
                    return {id: n.attr ? n.attr("id") : 0};
                }
            }
        }
    }).bind("select_node.jstree", true, function(event, data) {
        if (click_node === false) {
            click_node = true;
            return;
        }
        var fold_id = parseInt(data.rslt.obj.attr('id'));
        if (operator_mode === 'move') {
            var sel_node = data.rslt.obj;
            var path = tree.jstree('get_path', sel_node);
            var target = "/" + path.join("/") + "/";
            $("#mk-target-fold").val(target);
            $("#mk-target-fold-id").val(fold_id);
            return true;
        } else {
            loadTree('list', fold_id, {"data": {"is_paging": 'true'}});
        }
    });
};

var _hover_image = function() {
    $(this).toggleClass("mk-img-hover");
    $(this).find('.mk-img-ope').toggle();
};
var _click_image = function() {
    clicks++;
    if (clicks === 1) {
        var t = $(this);
        timer = setTimeout(function() {
            t.toggleClass("mk-img-active");
            clicks = 0;
        }, DELAY);
    } else {
        clearTimeout(timer);
        clicks = 0;
    }

};
var _dblclick_image = function() {
    if (operator_mode !== 'list')
        return false;
};

var _click_image_del = function(e) {
    e.stopPropagation();
    if (operator_mode !== 'list')
        return false;
    var node = e.target;
    var id = $(node).closest(".mk-speed-img").data("imgid");
    var fold_id = $(node).closest(".mk-speed-img").data("foldid");
    actived_folds = [fold_id];
    actived_images = [id];
    deleteImage();
};

var _click_image_move = function(e) {
    e.stopPropagation();
    if (operator_mode !== 'list')
        return false;
    _start_move();
    var node = e.target;
    var id = $(node).closest(".mk-speed-img").data("imgid");
    actived_images = [id];
};

var bind_images = function() {
    $(".mk-speed-img").hover(_hover_image)
            .click(_click_image)
            .dblclick(_dblclick_image)
            .find(".mk-img-ope .del").click(_click_image_del).end()
            .find(".mk-img-ope .move").click(_click_image_move);
    bind_nave();
};

var _hover_fold = function() {
    $(this).toggleClass("mk-img-hover");
    $(this).find('.mk-img-ope').toggle();
};
var _click_fold = function() {
    clicks++;
    if (clicks === 1) {
        var t = $(this);
        timer = setTimeout(function() {
            t.toggleClass("mk-img-active");
            clicks = 0;
        }, DELAY);
    } else {
        clearTimeout(timer);
        clicks = 0;
    }
};

var _dblclick_fold = function() {
    if (operator_mode !== 'list')
        return false;
    var fold_id = $(this).data('foldid');
    loadTree('list', fold_id, {"data": {"is_paging": 'true'}});
};

var _click_fold_del = function(e) {
    e.stopPropagation();
    if (operator_mode !== 'list')
        return false;
    var node = e.target;
    var fold_id = $(node).closest(".mk-speed-img").data("foldid");
    actived_folds = [fold_id];
    deleteFold();
};
var _click_fold_move = function(e) {
    e.stopPropagation();
    if (operator_mode !== 'list')
        return false;
    _start_move();
    var node = e.target;
    var id = $(node).closest(".mk-speed-img").data("foldid");
    actived_folds = [id];
};
var bind_folds = function(fold_id) {
    var folds;
    if (typeof fold === 'undefined') {
        folds = $(".mk-speed-img");
    } else {
        folds = $("#" + fold_id);
    }
    folds.hover(_hover_fold)
            .click(_click_fold)
            .dblclick(_dblclick_fold)
            .find(".mk-img-ope .del").click(_click_fold_del).end()
            .find(".mk-img-ope .move").click(_click_fold_move);
    bind_nave();
};

var _page_nav = function(e) {
    e.preventDefault();
    if (operator_mode !== 'list')
        return false;
    var href = $(this).attr("href") + "/.json";
    list_type === 'fold' ? loadTree('list', null, {'data': {'is_paging': true}}, href) : loadImages(null, href);
};
var _header_nav = function(e) {
    e.preventDefault();
    if (operator_mode !== 'list')
        return false;
    var fold_id = $(this).data("ppid");
    loadTree('list', fold_id, {'data': {'is_paging': true}});
};

var _check_all_items = function() {
    $("#mk-sel-reverse").attr("checked", false);
    var is_all = $(this).is(":checked");
    $("#mk-list-content").find(".mk-speed-img").each(function() {
        is_all ? $(this).addClass("mk-img-active") : $(this).removeClass("mk-img-active");
    });
};
var _reverse_check_items = function() {
    $("#mk-sel-all").attr("checked", false);
    $("#mk-list-content").find(".mk-speed-img").each(function() {
        $(this).hasClass("mk-img-active") ? $(this).removeClass("mk-img-active") : $(this).addClass("mk-img-active");
    });
};
var _refresh_fold = function(e) {
    e.preventDefault();
    if (operator_mode !== 'list')
        return false;
    var node = e.target;
    var pid = $(node).data("pid");
    var has_sub = $(node).data("sub");
    loadTree('list', pid, {'data': {'is_paging': true}});
};

var bind_nave = function() {
    $(".paging").find("span a").click(_page_nav);
    $("#mk-item-list-navi").find("a").click(_header_nav);
    $("#mk-sel-all").click(_check_all_items);
    $("#mk-sel-reverse").click(_reverse_check_items);
    $(".mk-refresh-fold").click(_refresh_fold);
};


var show_login = function(is_float, target_node, is_ajax) {
    $.getJSON(global.pc_login + "/login?callback=?", {a: "removeLoginDiv", login_url: global.pc_host + "/users/login", ajax_login: is_ajax}).done(function(json) {
        $("#login_dialog").html(json.content);
        if (is_float) {
            $("#login_dialog").dialog({modal: true,
                position: {my: "top-2", at: "top", of: window},
                draggable: false,
                dialogClass: "login-float",
                width: 570,
                height: 300,
                resizable: false,
                show: 'blind'});
            bind_login();
        } else {
            $("#login_dialog").appedTo(target_node);
        }
    }).fail(function(jqxhr, textStatus, error) {
        var err = textStatus + ', ' + error;
        console.log("Request Failed: " + err);
    });
};

var bind_login = function() {
    $("#mk-login-btn").click(function() {
        var o = {};
        o.login_account = $("input[name=login_account]").val();
        o.login_password = $("input[name=login_password]").val();
        var login_action = $("#login_form").attr("action");
        $.ajax({
            url: login_action,
            type: 'post',
            data: o,
            dataType: 'json',
            success: function(data) {
                if (data.err === 0) {
                    $("#login_dialog").find(".login_err_tips").html("").hide();
                    $("#login_dialog").dialog("close");
                } else {
                    $("#login_dialog").find(".login_err_tips").html(data.msg).show();
                }
            }
        });
    });
};

var show_tips = function(type, msg, sec) {
    $(".mk-msg-tip").stop(true, true);
    if (typeof sec === 'undefined') {
        sec = 2000;
    }
    if (type === 'ok') {
        var html = "<em class='sprite-ic  mk-tips-ok'></em> " +
                msg +
                "<em class='close-tips' style='display: none;'> </em>";

    } else if (type === 'forbin') {
        var html = "<em class='sprite-ic mk-tips-forbin' ></em> " +
                msg +
                "<em class='sprite-iz' style='display: none;'> </em>";
    } else if (type === 'fail') {
        var html = "<em class='sprite-ic mk-tips-fail' ></em> " +
                msg +
                "<em class='sprite-iz' style='display: none;'> </em>";
    } else if (type === 'loading') {
        var html = "<em class='sprite-ic  mk-tips-loading'></em> " +
                msg +
                "<em class='sprite-iz' style='display: none;'> </em>";
    }
    $(".mk-msg-tip .toast-msg").html(html);
    if (type === 'loading') {
        $(".mk-msg-tip").fadeIn(300);
    } else {
        $(".mk-msg-tip").fadeIn(300).delay(sec).fadeOut(400);
    }

};

var hide_tips = function() {
    $(".mk-msg-tip").fadeOut(400);
};

var clearPageCache = function() {
    var xhr = $.getJSON('/users/pageCache.json', function(json) {
        for (var e in json.data)
        {
            global[e] = json.data[e]; //全局变量o
            if (json.data[e])
                $('.' + e).show();
            else
                $('.' + e).hide();
        }
    });
    return xhr;
};

var _ajax_load_data = function(url, postdData, postType, dataType) {
    var xhr = $.ajax({
        url: url,
        type: postType,
        data: postdData,
        dataType: dataType,
        error: function(objRequest) {
            $(".mk-msg-tip").hide();
            if (objRequest.status === 403) {
                show_login(1, null, true);
            }
        }
    });
    return xhr;
};

$(function() {
    clearPageCache();
    var id = typeof start_album === 'undefined' ? 0 : start_album;
    if((typeof start_page !== 'undefined')){
        postData.data.page =start_page;
    }
   
    loadTree('list', id, postData);
    initTree();

    $("#mk-bt-upload").button().click(_upload_images);
    $("#mk-bt-add-fold").button().click(_add_fold);
    $("#mk-bt-del-folds").button().click(deleteManyFolds);
    $("#mk-bt-del-images").button().click(deleteManyImages);
    $("#mk-bt-move-folds,#mk-bt-move-images").button().click(_init_move);

    if (!History.enabled) {
        throw new Error('History.js is disabled');
    }

    History.options.debug = true;
    History.Adapter.bind(window, 'statechange', function(e) {
        click_node = false;
        var State = History.getState();
        var data = State.data;
        _show_album(data);
        $("#mk-tree").jstree("select_node", "#" + data.pid, true);
    });
});
