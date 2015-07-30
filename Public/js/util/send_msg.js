function createRequest(){
		var xmlHttp;
		if (window.XMLHttpRequest){
		    xmlHttp = new XMLHttpRequest();
		}
		else if (window.ActiveXObject)
		{
		    try{
		        xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		    }
		    catch (e)
		    {
		        try{
		            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
		        }
		        catch (e) {}
		    }
		}
		return xmlHttp;
	}

function request(xmlHttp,data,url)
{
	xmlHttp.open("POST",url, false);
	xmlHttp.setRequestHeader('Content-type','application/x-www-form-urlencoded');	
	xmlHttp.send(data);

	//alert(xmlHttp.responseText);
}

function send_message(data,url){
	var xmlHttp = createRequest();
  	request(xmlHttp,data,url);
  	if(xmlHttp.responseText==200){
    	document.location.reload();
  	}
}



function request_message(id,value,obj,attach){
	var to = 'to='+id+"&";
	var type = 'type='+value+"&";
	var obj = "obj="+obj+"&";

	if(attach){
		var attach = "attach="+attach+"&";
	}

	var data = to+type+obj+attach;
	if(value=="JOIN"){
		$('#join_input').val(data);
		$('#request_info').show();
		$('.modal-footer').find('div').hide();
		$('.modal-footer').find('div').filter('#joinRequest').show();
		modalShow('alert_content','myModal','请输入想对项目管理员说的话:');

	}
	else if(value=="INVEST"){
		$('#invest_input').val(data);
		$('#request_info').hide();
		$('.modal-footer').find('div').hide();
		$('.modal-footer').find('div').filter('#investRequest').show();
		modalShow('alert_content','myModal','您在取得项目创始人联系方式的同时, 您的联系方式也将发送给对方');
	}

	else if(value=="REQUIRE"){
		$('#require_input').val(data);
		$('#request_info').show();
		$('.modal-footer').find('div').hide();
		$('.modal-footer').find('div').filter('#requireRequest').show();
		modalShow('alert_content','myModal','请输入想对投资人说的话:');
	}
	else if(value=="MEMBERS"){
		$('#members_input').val(data);
		$('#editField').hide();
		$('.modal-footer').find('div').hide();
		$('.modal-footer').find('div').filter('#membersRequest').show();
		modalShow('alert_content','myModal','添加成功!<br><br>是否将成员更新的消息发送给投资人以及项目关注者?');
	}
	else if(value=="FINANCIAL"){
		$('#financial_input').val(data);
		$('#editField').hide();
		$('.modal-footer').find('div').hide();
		$('.modal-footer').find('div').filter('#financialRequest').show();
		modalShow('alert_content','myModal','添加成功!<br><br>是否将融资更新的消息发送给投资人以及项目关注者?');
	}
	else if(value=="ARTICLES"){
		$('#articles_input').val(data);
		$('.modal-footer').find('div').hide();
		$('.modal-footer').find('div').filter('#articlesRequest').show();
		modalShow('alert_content','myModal','编辑成功!<br><br>是否将资讯更新的消息发送给相关领域的投资者?');
	}
	else if(value=="PROJECTS"){
		$('#projects_input').val(data);
		$('.modal-footer').find('div').hide();
		$('.modal-footer').find('div').filter('#projectsRequest').show();
		modalShow('alert_content','myModal','添加成功!<br><br>是否将新增创业项目的消息发送给相关领域的投资者?');
	}
	
}
