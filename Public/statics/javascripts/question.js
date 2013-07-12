
//初始化问题领域选择
function _initQuestionCategoryChoose(){
	$("#dropdown-choose-category li a").click(function(){
		if($(this).attr("value")!="0"){
			$("#categoryName").val($(this).html());
			$("#category").val($(this).attr("value"));
		}
	});
}

function _initAddQuestionValidate(){
	var form = $("#add-question-form");
	var m = $("#validate-info");
	$("#btn_add_question").click(function(){
		m.html("").hide();
		if ($(".alert-info").html()) {
			m.html("请先登录").show();
			return false;
		}else if ($.trim($("input[name='category']").val()).length<=0 || $.trim($("input[name='categoryName']").val()).length<=0){
			m.html("请选择一个领域").show();
			return false;
		}else if(!editorQuestionContent.hasContents()){
			m.html("请输入问题内容").show();
			return false;
		}else if(!editorAnswerContent.hasContents()){
			m.html("请输入答案").show();
			return false;
		}
	});
}

function _initQuestionListLoad(){
	$("#btnLoadingMore").click(function(){
		$("#qLoading").show();
		$("#btnLoadingMore").hide();
		$("input[name='p']").val(parseInt($("input[name='p']").val())+1);
		$.get($("#qSearchParameter").attr("action"),$("#qSearchParameter").serialize(),function(r){
			if(r.success){
				if(r.html.length>0){
					$("#itemContainer").html(r.html);
					$("#qLoading").hide();
					$("#btnLoadingMore").show();
					_initQuestionManager();
				}else
					$("#qLoading").removeClass('alert-info').addClass('alert-warming').html('没有更多相关问题了');
			}else{
				$("#qLoading").removeClass('alert-info').addClass('alert-error').html('加载数据失败，请刷新重试');
			}
		},'json');
	});
	$("#btnLoadingMore").click();
}

function _initQuestionManager(){
	var loadingHtml = '<div class="i"><span class="label label-info" >正在加载...</span></div>';
	var emptyDataHtml = '<div class="i"><span class="label label-success" >暂无评论</span></div>';
	var errorDataHtml = '<div class="i"><span class="label label-error" >加载失败</span></div>';
	$(".q-c-each-link a").unbind();
	$(".q-c-each-link a").click(function(){
		var qId = parseInt($(this).parent().attr("vel"));
		var action = $(this).attr("for").toLowerCase();
		switch(action){
			case "addanswer":
				$("#addAnswerWindow #questionID").val(qId);
				$("#answerValidateInfo").html("").hide();
				$("#addAnswerWindow").modal("show");
			break;
			case "addfollow":
				var fc = $(this).find("span[for='followCount']");
				var followCount = parseInt(fc.html());
				fc.html("loading");
				$.post(addFollowUrl,{id:qId},function(r){
					if(r.success){
						_showSiteMessage("success","关注成功");
						fc.html(followCount+1);
					}else{
						_showSiteMessage("error",r.message);
						fc.html(followCount);
					}
				},'json');
			break;
			case "addgoodreputation":
				var gc = $(this).find("span[for='goodReputation']");
				var goodReputation = parseInt(gc.html());
				gc.html("loading");
				$.post(addGoodReputationUrl,{id:qId},function(r){
					if(r.success){
						_showSiteMessage("success",":-D");
						gc.html(goodReputation+1);
					}else{
						_showSiteMessage("error",r.message);
						gc.html(goodReputation);
					}
				},'json');
			break;
			case "addbadreputation":
				var bc = $(this).find("span[for='badReputation']");
				var badReputation = parseInt(bc.html());
				bc.html("loading");
				$.post(addBadReputationUrl,{id:qId},function(r){
					if(r.success){
						_showSiteMessage("success",":-(");
						bc.html(badReputation+1);
					}else{
						_showSiteMessage("error",r.message);
						bc.html(badReputation);
					}
				},'json');
			break;
			case "comment":
				$(this).parent().parent().find(".q-c-each-comment").toggle();
				var container = $(this).parent().parent().find(".q-c-each-comment .commentEntities");
				if($.trim(container.html()).length<=0){
					container.html(loadingHtml);
					$.get(loadCommentUrl,{id:qId},function(r){
						if(r.success){
							var html ="";
							if(r.data.length>0){
								for (var i = r.data.length - 1; i >= 0; i--) {
									html+='<div class="q-c-each-comment-v"><a for="user" href="'+r.data[i].userLink+'">'+r.data[i].userName
									+'</a>'+r.data[i].commentContent+'   <span class="time">'+r.data[i].showTime
									+'</span><a for="replyComment" rel="'+r.data[i].userName+'" href="javascript:void(0)">回复</a></div>';
								};
							}else
								html = emptyDataHtml;
							container.html(html);
							_initQuestionCommentManager();
						}else{
							container.html(errorDataHtml);
						}
					},'json');
				}
			break;
		}
	});
	_initQuestionCommentManager();
}

function _initQuestionCommentManager(){
	$("textarea[for='comment']").unbind();
	$("textarea[for='comment']").focus(function(){
		$(this).removeClass("ce").addClass("o");
	});
	$("textarea[for='comment']").blur(function(){
		if($.trim($(this).val()).length==0)
			$(this).removeClass("o").addClass("c");
	});
	$(".q-c-each-comment-v a[for='replyComment']").unbind();
	$(".q-c-each-comment-v a[for='replyComment']").click(function(){
		var t = $(this).parent().parent().parent().find("textarea[for='comment']");
		t.text("回复 @"+$(this).attr("rel")+"：");
		t.focus();
	});
	$(".q-c-each-comment-v .btn").unbind();
	$(".q-c-each-comment-v .btn").click(function(){
		var qId = $(this).attr("vel");
		var textareac = $(this).parent().find("textarea[for='comment']");
		var content = $.trim(textareac.val());
		var commentContainer = $(this).parent().parent().find(".commentEntities");
		var control = $(this);
		if(content.length<=0)
			return false;
		else{
			$(this).attr("disabled","disabled");
			$.post(addCommentUrl,{questionID:qId,commentContent:content},function(r){
				if(r.success){
					//padding 修改为个人中心
					var html = '<div class="q-c-each-comment-v"><a for="user" href="javascript:void(0)">'+__LoginUserName+'</a>'+content+'</div>';
					commentContainer.find(".i").remove();
					commentContainer.append(html);
					_showSiteMessage("success","评论成功");
					textareac.val('');
					textareac.blur();
				}else{
					_showSiteMessage("error",r.message);
				}
				control.removeAttr("disabled");
			},'json');
		}
	});
}

function _initAddAnswerWindow(){
	$("#addAnswerWindow #btnAddAnswer").click(function(){
		var i= $("#answerValidateInfo");
		var form = $("#form_addAnswer");
		i.attr("class","label-important").hide();
		var qId = parseInt($("#questionID").val());
		if(!__IsLogin){
			i.html("请先登录").show();
			return false;
		}else if(qId>0){
			if(editorAnswerContent.hasContents()){
				editorAnswerContent.sync(); 
				editorAnswerExplanation.sync();
				$.post(form.attr("action"),form.serialize(),function(r){
					if(r.success){
						i.removeClass("label-important").addClass("label-success").html("答案提交成功，等待题主评分").show();
						setTimeout(function(){
							$("#addAnswerWindow").modal("hide");
							var ac = $(".q-c-each-link[vel='"+qId+"'] a span[for='answerCount']");
							ac.html(parseInt(ac.html())+1);
						},1000);
					}else
						i.attr("class","label-important").html(r.message).show();
				},'json');
			}else{
				i.attr("class","label-important").html("请输入答案").show();
				return false;
			}
		}else{
			i.attr("class","label-important").html("请先选择一个问题回答").show();
			return false;
		}
	});
}

$(function(){
	_initQuestionCategoryChoose();
	$("input[name='p']").val(0);
	_initQuestionListLoad();
});
