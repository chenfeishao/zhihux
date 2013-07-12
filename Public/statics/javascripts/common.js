/**
 * 公共JS
 * 需要jQuery支持
 */
//页面加载初始化
$(function(){
	_initVerify();
	_initHeaderLogin();
});

//验证码刷新
function _initVerify(){
	var time = new Date().getTime();
	$(".verify-code").click(function(){
		$(this).attr('src',$(this).attr('src')+time);
	});
}

//弹出提示
function _showSiteMessage(type,message,callback){
	var m = $("#__showMessage");
	if(type && message){
		m.attr("class","alert alert-"+type);
		m.find("strong").html(message);
		setTimeout(function(){
			$("#__showMessage").attr("class","alert alert-hidden");
			if (typeof(callback) == "function" ) {
				callback();
			};
		},3000);
	}
}

function _initHeaderLogin(){
	var form = $("#header-login-form");
	form.find("button:eq(0)").click(function(){
		if ($.trim(form.find("input[name='u']").val()).length<=0 || $.trim(form.find("input[name='p']").val()).length<=0) {
			//_showSiteMessage("error","请输入账号和密码");
			return false;
		};
		if (__LoginErrorCount>=4) {
			_showSiteMessage("error","登录错误次数过多，请稍后重试");
			return false;
		};
		$.post(form.attr("action"),form.serialize(),function(r){
			if (r.status) {
				if (r.status==1) {
					_showSiteMessage("success","登录成功，3秒后跳转",function(){window.location.href = window.location.href});
				}else{
					_showSiteMessage("error",r.message);
				};
			}else
				_showSiteMessage("error","登录异常");
		},'json');
	});
}