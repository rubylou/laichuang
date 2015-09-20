function modalShow(content,container,value){
  $('#'+content).html(value);
  $('#'+container).modal('show');
}

function hideCollapse(id){
  	$(id).collapse('hide');
  	$(id).find('input[type="text"]').each(function(){
  		$(this).val('');
  	});
  	$(id).find('input[type="checkbox"]').each(function(){
  		$(this).attr('checked',false);
  	});
  	$(id).find('select').each(function(){
  		$(this).attr('disabled',false);
  		$(this).children().first().attr('selected','selected');
  	});
  	$(id).find('textarea').each(function(){
  		$(this).val('');
  	})
}


function checkPwdfirm(pwd, pwdfirm){
	if(pwd==pwdfirm){
		return true;
	}
	else return false;
}

function checkID(value){
	return (/^[a-zA-Z]{1}[0-9a-zA-Z_]{1,}/).test(value);
}

function checkPwd(pwd,pwdfirm){
	if(checkPwdfirm($(pwd).val(),$(pwdfirm).val())){
		if(($(pwd).val()).length < 6){
			modalShow("alert_content","myModal","密码长度不能少于6位");
			$(pwd).focus();
			return false;
		}
		else return true;
	}
	else{
		modalShow("alert_content","myModal","两次密码不一致");
		$(pwd).focus();
		return false;
	}

}

function checkEmail(id){
	var reg = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/i;
	if (reg.test($(id).val())) {
		return true;
	}else{
		$(id).val('');
		$(id).attr('placeholder','不符合邮件格式');
		$(id).focus();
		return false;
	};
}

function checkPhone(id){
	var reg = /^0?1[3|4|5|8|9][0-9]\d{8}$/i;
	if (reg.test($(id).val())) {
		return true;
	}else{
		$(id).val('');
		$(id).attr('placeholder','不符合11位手机号码格式');
		$(id).focus();
		return false;
	};
}

function checkBirth(id){
	var reg = /\d{4}\-\d{2}\-\d{2}$/i;
	if (reg.test($(id).val())) {
		return true;
	}else{
		$(id).val('');
		$(id).attr('placeholder','不符合yyyy-mm-dd格式');
		$(id).focus();
		return false;
	};
}

function checkName(id){
	if($(id).val().length>0){
		return true;
	}
	else{
		$(id).val('');
		$(id).attr('placeholder','姓名不能为空');
		$(id).focus();
		return false;
	}
}

function checkValue(id,max,min,notice){
	if($(id).val().length<min){
		$(id).val('');
		$(id).attr('placeholder',notice);
		$(id).focus();
		return false;
	}
	else if($(id).val().length>max){
		$(id).val('');
		$(id).attr('placeholder','超出字符数要求:'+max);
		$(id).focus();
		return false;
	}
	else{
		return true;
	}
}

function checkNickname(id){
	if($(id).val().length>0){
		return true;
	}
	else{
		$(id).val('');
		$(id).attr('placeholder','昵称不能为空');
		$(id).focus();
		return false;
	}
}
