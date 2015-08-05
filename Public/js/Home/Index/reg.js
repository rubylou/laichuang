function selectCollpase(id1,id2){
  $(id1).collapse('toggle');
  $(id2).attr('class','collapse');
}

function initializeField(rows,field){
  for(var i=0;i<=rows;i++){
    $('#conditions').append($('<p></p>').attr("id","row"+i));
  }

  for(var i in field){
    addLabel(i,field);
    addOption(i,field);
  }
}

function initailizeCity(city){
  for(var i in city){
    addCity(i,city);
  }
}

function addInterest(id,field){
  var exist = $("#choices").contents().filter("#field"+id);
  if(exist.text()==field[id]){
    alert("已添加");
  }
  else{
    var label = $('<span></span>').text(field[id]);
    label.addClass('btn btn-default label label-default theme-btn-inverse');
    label.attr('value',id);
    label.attr("id","field"+id);
    label.click(function(){
      addLabel(id,field);
      $(this).remove();
    });
    $('#choices').append(label);
  }
}

function addLabel(id,field){
  var exist = $("#conditions").contents().filter("#field"+id);
  if(exist.text()==field[id]){
    alert("已添加");
  }
  else{
    var label = $('<span></span>').text(field[id]);
    label.addClass('btn btn-default label theme-btn');
    label.attr('value',id);
    label.attr("id","field"+id);
    label.click(function(){
      if($("#choices").children().length==5){
        alert("选择已达上限");
      }
      else{
        addInterest(id,field);
        $(this).remove();
      }
    });

    var row = parseInt(id/6);
    if(id%6==0){
      --row;
    }
    $('#row'+row).append(label);
  }
}

function addOption(id,field){
  var option = $('<option></option').text(field[id]);
  option.attr('value',id);
  $('#regField2').append(option);
}

function addCity(id,field){
  var option = $('<option></option').text(field[id]);
  option.attr('value',id);
  $('#regCity2').append(option);
}

function submitInvestor(){
  if(checkValue("#regTitle1",100,0,"请填写职位")&&checkValue("#regCompany1",100,0,"请填写公司名称")&&checkPwd('#regPwd1','#regPwdfirm1')&&checkEmail('#regEmail1')&&checkPhone('#regMobile1')&&checkName('#regName1')){
    var xmlHttp = createRequest();

    var status = 'value=investor&';
    var name = 'key1='+$('#regName1').val()+'&';

    var mobile = 'key2='+$('#regMobile1').val()+'&';
    var email = 'key3='+$('#regEmail1').val()+'&';
    request(xmlHttp,mobile+email,"validCheck");
    if(xmlHttp.responseText==200){
      var company = 'key4='+$('#regCompany1').val()+'&';
      var title = 'key5='+$('#regTitle1').val()+'&';
      var type = 'key6='+$('#regType1').val()+'&';
      var pwd = 'key7='+CryptoJS.SHA1($('#regPwd1').val())+'&';
      var pwdfirm = 'key8='+CryptoJS.SHA1($('#regPwdfirm1').val())+'&';
      var field = 'key9=';
      $('#choices').children().each(function(){
        field = field + $(this).attr('value') + ',';
      });
      field = field + "&";

      var data = status+name+mobile+email+company+title+type+pwd+pwdfirm+field;
      request(xmlHttp,data,"userSave");
      if(xmlHttp.responseText==200){
        modalShow('alert_content','myModal','注册成功,2秒内即将跳转');
        setTimeout("window.location.href = 'index';",2000);
      }
      else{
        modalShow('alert_content','myModal','注册出现异常');
      }
    }
    else if(xmlHttp.responseText==2002){
      $('#regMobile1').val('');
      $('#regMobile1').attr('placeholder','该手机号已注册');
      $('#regMobile1').focus();
    }
    else if(xmlHttp.responseText==2003){
      $('#regEmail1').val('');
      $('#regEmail1').attr('placeholder','该邮箱已注册');
      $('#regEmail1').focus();
    }
    else{

    }
    
  }
  else{

  }
  
  
}

function submitInnovator(){
  if(checkPwd('#regPwd2','#regPwdfirm2')&&checkBirth('#regBirth2')&&checkNickname('#regNickname2')&&checkEmail('#regEmail2')&&checkPhone('#regMobile2')&&checkName('#regName2')){
    var xmlHttp = createRequest();
    var status = 'value=innovator&';
    var name = 'key1='+$('#regName2').val()+'&';
    var email = 'key2='+$('#regEmail2').val()+'&';
    var mobile = 'key3='+$('#regMobile2').val()+'&';
    var nickname = 'key4='+$('#regNickname2').val()+'&';
    request(xmlHttp,email+mobile+nickname,"validCheck1");
    if(xmlHttp.responseText==200){
      var gender = 'key5='+$('#regGender2').val()+'&';
      var birth = 'key6='+$('#regBirth2').val()+'&';
      var city = 'key7='+$('#regCity2').val()+'&';
      var field = 'key9='+$('#regField2').val()+'&';
      var pwd = 'key11='+CryptoJS.SHA1($('#regPwd2').val())+'&';
      var pwdfirm = 'key12='+CryptoJS.SHA1($('#regPwdfirm2').val())+'&';
      var data = status+name+email+mobile+nickname+gender+birth+city+field+pwd+pwdfirm;
      var xmlHttp = createRequest();
      request(xmlHttp,data,"userSave");
      if(xmlHttp.responseText==200){
        modalShow('alert_content','myModal','注册成功,2秒内即将跳转');
        setTimeout("window.location.href = 'index';",2000);
      }
      else{
        modalShow('alert_content','myModal','注册出现异常');
      }
    }
    else if(xmlHttp.responseText==2002){
      $('#regEmail2').val('');
      $('#regEmail2').attr('placeholder','该邮箱已注册');
      $('#regEmail2').focus();
    }
    else if(xmlHttp.responseText==2003){
      $('#regMobile2').val('');
      $('#regMobile2').attr('placeholder','该手机号已注册');
      $('#regMobile2').focus();
    }
    else if(xmlHttp.responseText==2004){
      $('#regNickname2').val('');
      $('#regNickname2').attr('placeholder','该昵称已被人使用');
      $('#regNickname2').focus();
    }
    else{

    }

  }
  else{

  }
}

function queryCheckCode(val){
    var mobile = $(val).val();
    var xmlHttp = createRequest();
    request(xmlHttp,'mobile='+mobile,'queryCheckCode');
}
