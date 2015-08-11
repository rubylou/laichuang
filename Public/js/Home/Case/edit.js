function submitProject(){
  var xmlHttp = createRequest();
  if(checkValue("#proName",20,0,'名称不能为空')&&checkValue("#proBrief",30,0,"简要介绍不能为空")){
    var name = "key1="+$("#proName").val()+"&";
    var brief = "key3="+$("#proBrief").val()+"&";
    var member = "key4="+$("#proMember").val()+"&";
    var fi = "key5="+$("#proFi").val()+"&";
    var type = "key6="+$("#proField").val()+"&";
    var recruit = "key7="+$("#proRecruit").val()+"&";
    var require = "key8="+$('#proRequire').val()+"&";
    var content="key9="+getContent(um)+"&";
    var data = name+brief+member+fi+type+recruit+require+content;
    request(xmlHttp,data,"proSave");
    if(xmlHttp.responseText.length>4){
      //window.location.href='../User/index';
      request_message(xmlHttp.responseText,'PROJECTS','PROJECT');
    }
  }
}

function fi_submit(id,url){
  var index = $('#editFi_input').val();
  var xmlHttp = createRequest();
  if(checkValue("#investor",50,1,'投资主体不能为空')){
    var round = 'key1='+$('#round').val()+'&';
    var currency1 = 'key2='+$('#currency1').val()+'&';
    var amount = 'key3='+$('#amount').val()+'&';
    var currency2 = 'key4='+$('#currency2').val()+'&';
    var assess = 'key5='+$('#assess').val()+'&';
    var investor = 'key6='+$('#investor').val()+'&';
    var investyear = 'key7='+$('#investyear').val()+'&';
    var investmon = 'key8='+$('#investmon').val()+'&';
    var data = round+currency1+amount+currency2+assess+investor+investyear+investmon;
    if(index.length>0){
      request(xmlHttp,"p="+id+"&"+data+"c="+index,url);
    }
    else{
      request(xmlHttp,"p="+id+"&"+data,url);
    }

    if(xmlHttp.responseText==200){
      //document.location.reload();
      request_message(id,'FINANCIAL','PROJECT',$('#round').val());
    }
    else{
      
    }
  }
  else{
  }
  
}

function followPro(id,value,url){
  var xmlHttp = createRequest();
  request(xmlHttp,"key="+value,url+"?val="+id);
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
}

function submitAuth(id,logo,url){
    if(logo.length>0){
      var xmlHttp = createRequest();
      request(xmlHttp,'p='+id,url);
      if(xmlHttp.responseText == '200'){
        alert('提交成功');
        document.location.reload();
      }
    }
    else{
      $('#alert_window')[0].innerHTML = "<strong>请先添加创业项目Logo</strong>"; 
      $('.alert').fadeIn();
    }
    
}

function addMember(p,url){
    var xmlHttp = createRequest();
    var member = "key1="+$("#member").val()+"&";
    var title = "key2="+$("#title").val()+"&";
    var pro = "p="+p+"&";
    data = member+title+pro;
    request(xmlHttp,data,url);
    if(xmlHttp.responseText==200){
      //document.location.reload();
      request_message(p,'MEMBERS','PROJECT',$("#member").val());
    }
    else if(xmlHttp.responseText==404){
      $('#member').val('');
      $('#member').attr('placeholder','用户不存在');
      $("#member").focus();
    }
    else if(xmlHttp.responseText==409){
      $('#member').val('');
      $('#member').attr('placeholder','该用户为创始人');
      $("#member").focus();
    }
  }

function addInvestor(p,url){
  var xmlHttp = createRequest();
  var investor = "key1="+$("#pro_investor").val()+"&";
  var pro = "p="+p+"&";
  data = investor+pro;
  request(xmlHttp,data,url);
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
  else if(xmlHttp.responseText==404){
    $('#investor').val('');
    $('#investor').attr('placeholder','用户不存在');
    $("#investor").focus();
  }
}

function editProFi(id,url){
  var xmlHttp = createRequest();
  request(xmlHttp,'c='+id,url);
  var info = JSON.parse(xmlHttp.responseText);
  $('#collapseExample1').collapse('show');
  $('#round').children('option').each(function(){
    if($(this).val()==info.round){
      $(this).attr('selected','selected');
    }
  });
  $('#amount').val(info.invest_amount);
  $('#currency1').children('option').each(function(){
    if($(this).val()==info.invest_cur){
      $(this).attr('selected','selected');
    }
  });
  $('#assess').val(info.assess_amount);
  $('#currency2').children('option').each(function(){
    if($(this).val()==info.assess_cur){
      $(this).attr('selected','selected');
    }
  });
  $('#investor').val(info.investor_name);
  $('#investyear').children('option').each(function(){
    if($(this).val()==info.year){
      $(this).attr('selected','selected');
    }
  });
  $('#investmon').children('option').each(function(){
    if($(this).val()==info.mon){
      $(this).attr('selected','selected');
    }
  });
  $('#editFi_input').val(id);
  
}

function saveMember(url){
  var str = $('#pro_member').val();
  str = (str.replace(/(\n|\r|(\r\n))/g,"<br>"));
  var xmlHttp = createRequest();
  var data = 'member='+escape(str)+'&';
  request(xmlHttp,data,url);
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
  else{
    showEditPanel('#editMembers');
  }

}

function saveFi(url){
  var str = $('#pro_fi').val();
  str = (str.replace(/(\n|\r|(\r\n))/g,"<br>"));
  var xmlHttp = createRequest();
  var data = 'fi='+escape(str)+'&';
  request(xmlHttp,data,url);
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
  else{
    showEditPanel('#editFi');
  }

}

function saveRecruit(url){
  var str = $('#pro_recruit').val();
  str = (str.replace(/(\n|\r|(\r\n))/g,"<br>"));
  var xmlHttp = createRequest();
  var data = 'recruit='+escape(str)+'&';
  request(xmlHttp,data,url);
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
  else{
    showEditPanel('#editRecruit');
  }
}

function saveRequire(url){
  var str = $('#pro_require').val();
  str = (str.replace(/(\n|\r|(\r\n))/g,"<br>"));
  var xmlHttp = createRequest();
  var data = 'require='+escape(str)+'&';
  request(xmlHttp,data,url);
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
  else{
    showEditPanel('#editRequire');
  }
}

function saveInfo(url){
  var xmlHttp = createRequest();
  if(checkValue('#edit_name',16,1,'姓名不能为空') && checkValue('#edit_brief',80,1,'简介不能为空')){
    if($("#choices").children().length==0){
      alert("请至少选择一个所属领域");
    }
    else{
      var name = 'name='+$('#edit_name').val()+'&';
      var brief = "brief="+$('#edit_brief').val()+'&';
      var field = 'field=';
      $('#choices').children().each(function(){
        field = field + $(this).attr('value') + ',';
      });
      field = field + "&";
      var data = name+brief+field;
      request(xmlHttp,data,url);
      if(xmlHttp.responseText.match('200')){
        document.location.reload();
      }
    }
  }
}


function delMember(id){
  $('.modal-footer').find('div').hide();
  $('.modal-footer').find('div').filter('#delMemberBtn').show();
  $('#delMember_input').val(id);
  modalShow('alert_content','myModal','确认删除该项目成员?');
}

function delInvestor(id){
  $('.modal-footer').find('div').hide();
  $('.modal-footer').find('div').filter('#delInvestorBtn').show();
  $('#delInvestor_input').val(id);
  modalShow('alert_content','myModal','确认删除该投资人?');
}

function delFi(id){
  $('.modal-footer').find('div').hide();
  $('.modal-footer').find('div').filter('#delFiBtn').show();
  $('#delFi_input').val(id);
  modalShow('alert_content','myModal','确认删除该融资信息?');
}

function crop(url,send){
  var x1 = "x1="+$('#x1').val()+"&";
  var y1 = "y1="+$('#y1').val()+"&";
  var w = "w="+$('#w').val()+"&";
  var h = "h="+$('#h').val()+"&";
  var data = x1+y1+w+h;
  var xmlHttp = createRequest();
  request(xmlHttp,data,send);
  if(xmlHttp.responseText==200){
      window.location.href=url;
  }
}


