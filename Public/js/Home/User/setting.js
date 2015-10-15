	function queryCheckCode(val,type){
		var mobile = $(val).val();
		var xmlHttp = createRequest();
		if(type=="MMM"){
			request(xmlHttp,'mobile='+mobile,'queryCheckCode');
			if(xmlHttp.responseText == 200){
	  			return true;
	  		}

			else if(xmlHttp.responseText == 409){
	  			$(val).val('');
			    $(val).attr('placeholder','该手机已被注册');
			    $(val).focus();
			    return false;
	  		}
		}
		else if(type="PPP"){
			request(xmlHttp,'mobile='+mobile,'queryCheckCode1');
			if(xmlHttp.responseText == 200){
	  			return true;
	  		}
			else{

			}
		}
		
	}


  	function submitMobile(url){
  		if(checkPhone('#phone') && $('#phone').val()!=$('#mobile').val()){
  			var mobile = "key1="+$('#phone').val()+"&";
	  		var code = "key2="+$('#checkCode').val()+"&";
	  		var data = mobile+code;
	  		var xmlHttp = createRequest();
	  		request(xmlHttp,data,'saveMobile');
	  		if(xmlHttp.responseText == 200){
	  			window.location.href = url;
	  		}
	  		else if(xmlHttp.responseText == 409){
				$('#checkCode').val('');
			    $('#checkCode').attr('placeholder','验证码错误');
			    $('#checkCode').focus();
			}

  		}
  		else{
  			$('#phone').val('');
			$('#phone').attr('placeholder','修改号码与原手机号相同');
			$('#phone').focus();
  		}
  		
  	}


  	function submitEmail(id){
  		if(checkEmail(id)){
  			var email = "key1="+$(id).val()+"&";
  			var xmlHttp = createRequest();
  			request(xmlHttp,email,'saveEmail');
  			if(xmlHttp.responseText == 200){
  				modalShow("alert_content","myModal",'激活邮件已发送，请前往邮箱查收并激活。');
  			}
  			else if(xmlHttp.responseText == 409){
	  			$(id).val('');
			    $(id).attr('placeholder','该邮箱已被注册');
			    $(id).focus();
	  		}
  		}
  		
  	}

  	function submitSecure(){
  		if(checkPwd('#pwd2','#pwd3') && checkValue('#pwd1','64','6','密码不能少于6位') && checkValue('#checkCode2',6,1,'请输入短信验证码')){
  			if($('#pwd1').val()==$('#pwd2').val()){
  				modalShow("alert_content","myModal",'修改密码与原密码一致，请重新输入');
  			}
  			else{
  				var data = "key1="+CryptoJS.SHA1($('#pwd1').val())+'&';
	  			data += "key2="+CryptoJS.SHA1($('#pwd2').val())+'&';
	  			data += "key3="+CryptoJS.SHA1($('#pwd3').val())+'&';
	  			data += 'key4='+$('#checkCode2').val()+'&';
	  			data += "key5="+$('#mobile').val()+"&"
	  			var xmlHttp = createRequest();
	  			request(xmlHttp,data,'saveChange');
	  			if(xmlHttp.responseText == 200){
	  				modalShow("alert_content","myModal",'修改密码成功，请重新登录');
	  			}
	  			else if(xmlHttp.responseText == 404){
	  				modalShow("alert_content","myModal",'原密码错误');
	  			}
	  			else if(xmlHttp.responseText == 409){
	  				$('#checkCode2').val('');
				    $('#checkCode2').attr('placeholder','验证码错误');
				    $('#checkCode2').focus();
	  			}
	  			else{

	  			}
  			}
  		}
  	}
