/**
 * 
 */
$(function(){	
	$(window).bind("scroll", backToTopFun);
	$('.back-to-top').click(function() {
        $("html, body").animate({ scrollTop: 0 }, 120);
        return false; 
	});
	//添加收藏
	$('.add_favorite').click(function(){
		var that = $(this),fid = that.data('fid'),type = that.data('type'),slug = that.data('slug');
		if(type===undefined || slug===undefined){
			return false;
		}
		$.post(window.siteUrl+'action/forum?do=favorite',{fid:fid,type:type,slug:slug}).success(function(rs){
			if(rs.status==1){
				if(fid===undefined || fid==''){
					that.text('取消收藏').data('fid',rs.fid);
				}else{
					that.text('加入收藏').data('fid','');
				}
				showAlert(rs.msg,'',1500);
			}else{
				showAlert(rs.msg,'error',3000);
			}
		});
		return false;
	});
	//发送验证码
	$('.btn-sendverify').click(function(){
		var that = $(this),url=that.data('url'), target=that.data('target');
		if(url===undefined || target===undefined){
			return false;
		}
		var mail = $(target).val();
		if(mail==''){
			return false;
		}
		that.attr('disabled','disabled');
		var i = 120;
		var timer = function(){
			console.log(i);
			if(i>0){
				that.text(i+'秒后可重新获取验证码');
				i--;
				setTimeout(function(){timer();},1000);
			}else{
				that.text('获取验证码');
				that.removeAttr('disabled');
			}
		}
		timer();
		$.post(url,{'do':'sendverify', 'mail':mail}).success(function(rs){
			if(rs.status==1){
				showAlert('邮件已发送','success',1500);
			}
		});
	});
	//回复按钮
	$('.reply-btn').click(function(){
		var name = $(this).data('author');
		var form = $('#comment-form');
		text = form.find('#textarea').text();
		name = '@'+name+' ';
		if(text.length>0){
			name = text+'\n'+name
		}
		form.find('#textarea').text(name);
		return false;
	});
	$('img.captcha').click(function(){
		var src = $(this).data('src');
		if(src===undefined){
			src = $(this).attr('src');
			$(this).data('src',src);
		}
		$(this).attr('src',src+'?'+Math.random());
		return false;
	});
	//ajaxLoadComments();
	
	backToTopFun();

	showNotice();
	
});
//At回复
function replyAt(name){
	if(name===undefined || name=='') return false;
	
	name ='@'+name+' ';
	var text = $('#textarea').val();
	text += text=='' ? name : '\n'+name;
	$('#textarea').val(text);
	return false;
}
//showAlert('成功登录','success',3000)
function showNotice(){
	if(window.notice=='') return false;
	showAlert(window.notice.msg,window.notice.type,3000);
}
function showAlert(msg,type,time){
	var type = type==='error' ? 'alert-error' :'';
	var html = '<div id="ui-alert" class="'+type+'">';
		html += msg;
		html += '</div>';
	$(html).prependTo($('body')).fadeOut(time);
	setTimeout(function(){
		$('#ui-alert').remove();
	},time);
}
function backToTopFun() {
    var st = $(document).scrollTop(), winh = $(window).height(),backToTopEle = $('.back-to-top');
    (st > 120)? backToTopEle.show(): backToTopEle.hide();
    //IE6下的定位
    if (!window.XMLHttpRequest) {
        backToTopEle.css("top", st + winh - 166);
    }
};
function ajaxLoadComments(){
	var time = $('#lastCommentTime').data('last');
	if(time===undefined || time ==''){
		return false;
	}
	$.get(window.siteUrl+'action/forum?do=loadcomments&t='+time).success(function(rs){
		if(rs.status==1){
			$('#lastCommentTime').data('last',rs.last);
			appendComments(rs.data);
		}
		setTimeout(function(){
			ajaxLoadComments();
		},60000);
	});
}
function appendComments(list){
	var html = '';
	$.each(list,function(k,v){
		html += '<div class="cell 1">';
		html += '<a href="'+v.permalink+'"><img class="avatar" src="'+v.authorAvatar+'" width="32"></a>';
		html += '<a href="'+v.permalink+'"> '+v.authorName+'</a> : '+v.content;
		html += '</div>';
	});
	$(html).prependTo($('#lastCommentList'));
	$.each($('#lastCommentList .cell'),function(k,v){
		if(k>9){
			$(v).remove();
		}
	});
}