// var user_register={
// 	validateMessage : new Array('邮箱必须填写','昵称必须填写','密码必须填写','确认密码必须填写','验证码必须填写','邮箱格式错误','确认密码与密码不同','昵称必须2到10个字符','密码长度不能少于6位'),
// 	validateInput : function(i){
// 		var input = $.trim($("#zh_userregister input[name='"+i+"']").val());
// 		if(input.length<=0){
// 			switch(i){
// 				case 'userEmail':
// 					return this.validateMessage[0];
// 				break;
// 				case 'userName':
// 					return this.validateMessage[1];
// 				break;
// 				case 'userPass':
// 					return this.validateMessage[2];
// 				break;
// 				case 'userConfirmPass':
// 					return this.validateMessage[3];
// 				break;
// 				case 'verifyCode':
// 					return this.validateMessage[4];
// 				break;
// 			}
// 		}
// 		else{
// 			if(i=='userEmail'){
// 				var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
// 			    if(!reg.test(input))
// 			    	return this.validateMessage[5];
// 			}else if(i=='userConfirmPass'){
// 				if(input!=$.trim($("#zh_userregister input[name='userPass']").val()))
// 					return this.validateMessage[6];
// 			}else if(i=='userName'){
// 				if(input.length<2 || input.length>10)
// 					return this.validateMessage[7];
// 				else{
// 					$.post($('input[name="userName"]').attr('rel'),{username:input},function(result){
// 						alert(result.hasvalue);
// 						if (result.hasvalue) return '昵称已存在';
// 					},'json');
// 				}

// 			}else if(i=='userPass'){
// 				if(input.length<6 || input.length>32)
// 					return this.validateMessage[8];
// 			}
// 		}
// 	},
// 	initValidate : function(){
// 		$("#zh_userregister").find("input").each(function(){
// 			var group=$(this).parent().parent();
// 			$(this).focus(function(){
// 				group.removeClass('success').removeClass('error');
// 				$(this).parent().find('.help-inline').text('');
// 			});
// 			$(this).blur(function(){
// 				var validInfo = user_register.validateInput($(this).attr('name'));
// 				if(validInfo.length>0){
// 					group.addClass('error');
// 					$(this).parent().find('.help-inline').text(validInfo);
// 				}else{
// 					group.addClass('success');
// 					$(this).parent().find('.help-inline').text('');
// 				}
// 			});
// 		});
// 		$("#zh_userregister .btn-primary").click(function(){
// 			var error ='';
// 			$("#zh_userregister").find("input").each(function(){
// 				var s = user_register.validateInput($(this).attr('name'));
// 				error+=s;
// 				if(s.length>0){
// 					$(this).blur();
// 					return false;
// 				}
// 			});
// 			if($.trim(error).length==0)
// 				$("#zh_userregister").submit();
// 		});
// 	}
// };
// var userregex = {
// 	require : function(i){
// 		return i.length>0;
// 	},
// 	length : function(i,min,max){
// 		return (i.length<=min && i.length<=max);
// 	},
// 	ajax : function(name){
// 		var s = $('input[name="'+name+'"]');
// 		$.post(s.attr('rel'),{value:$.trim(s.val())},function(r){
// 			return r.valid;
// 		},'json');
// 	}
// }
// function usercheck(name){
// 	var input = $.trim($('input[name='+name+']').val());
// 	switch(name){
// 		case 'userEmail':
// 			var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
// 			if (!userregex.require(input))
// 				return '邮箱必须填写';
// 			else if(!reg.test(input))
// 			    return '邮箱格式错误';
// 			else if (!userregex.ajax(name))
// 				return '邮箱已经被注册';
// 			else
// 				return '';
// 		break;
// 		case 'userName':
// 			if (!userregex.require(input))
// 				return '昵称必须填写';
// 			else if(!userregex.length(input,2,10))
// 			    return '昵称必须2到10个字符';
// 			else if (!userregex.ajax(name))
// 				return '昵称已经被注册';
// 			else
// 				return '';
// 		break;
// 		case 'userPass':
// 			if (!userregex.require(input))
// 				return '密码必须填写';
// 			else if(!userregex.length(input,6,36))
// 			    return '密码长度不能少于6位';
// 			else
// 				return '';
// 		break;
// 		case 'userConfirmPass':
// 			if (!userregex.require(input))
// 				return '确认密码必须填写';
// 			else if(input!=$.trim($('input[name="userPass"]').val()))
// 			    return '确认密码与密码不同';
// 			else
// 				return '';
// 		break;
// 		case 'verifyCode':
// 			if (!userregex.require(input))
// 				return '验证码必须填写';
// 			else{
// 				$.post($('input[name="verifyCode"]').attr('rel'),{value:input},function(r){
// 					if (r.valid) 
// 						return '';
// 					else
// 						return '验证码错误';
// 				},'json');
// 			}
// 			// else if (!userregex.ajax(name))
// 			// 	return '验证码错误';
// 			// else
// 			// 	return '';
// 		break;
// 	};
// };
// $(function(){
// 	$("#zh_userregister").find("input").each(function(){
// 		var group=$(this).parent().parent();
// 		$(this).focus(function(){
// 			group.removeClass('success').removeClass('error');
// 			$(this).parent().find('.help-inline').text('');
// 		});
// 		$(this).blur(function(){
// 			var validInfo = usercheck($(this).attr('name'));
// 			if(validInfo.length>0){
// 				group.addClass('error');
// 				$(this).parent().find('.help-inline').text(validInfo);
// 			}else{
// 				group.addClass('success');
// 				$(this).parent().find('.help-inline').text('');
// 			}
// 		});
// 	});
// 	$("#zh_userregister .btn-primary").click(function(){
// 		var error ='';
// 		$("#zh_userregister").find("input").each(function(){
// 			var s = usercheck($(this).attr('name'));
// 			error+=s;
// 			if(s.length>0){
// 				$(this).blur();
// 				return false;
// 			}
// 		});
// 		if($.trim(error).length==0)
// 			$("#zh_userregister").submit();
// 	});
// });

$().ready(function(){
	$('#zh_userregister').validate({
		rules : {
			userEmail : {
				required : true,
				email : true,
				remote : $('#userEmail').attr('rel')
			},
			userName : {
				required : true,
				rangelength : [2,10],
				remote : $('#userName').attr('rel')   
			},
			userPass : {
				required : true,
				minlength : 6,
			},
			userConfirmPass : {
				required : true,
				equalTo : "#userPass"
			},
			verifyCode : {
				required : true,
				remote : $('#verifyCode').attr('rel')
			}
		},
		messages : {
			userEmail : {
				required : '邮箱必须填写',
				email : '邮箱格式不正确',
				remote : '该邮箱已经被注册'
			},
			userName : {
				required : '昵称必须填写',
				rangelength : '昵称必须2到10个字符',
				remote : '该昵称已经被注册'   
			},
			userPass : {
				required : '密码必须填写',
				minlength : '密码长度不能少于6位',
			},
			userConfirmPass : {
				required : '确认密码必须填写',
				equalTo : '确认密码与密码不同'
			},
			verifyCode : {
				required : '验证码必须填写',
				remote : '验证码错误'
			} 
		},
		errorPlacement : function(error,element){
			element.parent().parent().removeClass('success').addClass('error');
			element.parent().find('span').text(error.html());
			//error.appendTo(element.parent());
		},
		success : function(label){
			//alert(label.parent().html());
			//label.parent().parent().removeClass('error').addClass('success');
			//alert(label.attr('for'));
			var s = label.attr('for');
			$('#'+s).parent().parent().removeClass('error').addClass('success');
		},
		submitHandler : function(form){
			// alert(form);
			// var subtn = $('button[name="registeruser"]');
			// subtn.text('正在提交...');
			// subtn.attr('disabled','disabled');
			// $.post(form.attr('action'),form.serialize(),function(r){
			// 	subtn.removeAttr('disabled');
			// 	if (r!='success') {
			// 		_showSiteMessage('error',r);
			// 		return false;
			// 	}
			// },'json');
			form.submit();
		}
	});
});