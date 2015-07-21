function addInterests(value,id){
  var interests = value;
  for(var i in interests){
    if(interests[i]!=null){
      var label = $('<span></span>').text(' '+interests[i]['interest_field']+' ');
      $(id).find(".glyphicon-tag").after(label);
      $('#conditions').find('span:contains("'+interests[i]['interest_field']+'")').click();
    }
  }
}

function fi_submit(id){
  var index = $('#editFi_input').val();
  var xmlHttp = createRequest();
  if(checkValue("#investor",50,0,'投资主体不能为空')){
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
      request(xmlHttp,"p="+id+"&"+data+"c="+index,"profiAdd");
    }
    else{
      request(xmlHttp,"p="+id+"&"+data,"profiAdd");
    }

    if(xmlHttp.responseText==200){
      document.location.reload();
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

function editName(){
  $('#pro_name').attr('type','text');
  $('#saveName').show();
}

function editBrief(){
  $('#pro_brief').attr('type','text');
  $('#saveBrief').show();
}

function delMember(id){
  $('.modal-footer').find('div').hide();
  $('#editField').hide();
  $('.modal-footer').find('div').filter('#delMemberBtn').show();
  $('#delMember_input').val(id);
  modalShow('alert_content','myModal','确认删除该项目成员?');
}

function delInvestor(id){
  $('.modal-footer').find('div').hide();
  $('#editField').hide();
  $('.modal-footer').find('div').filter('#delInvestorBtn').show();
  $('#delInvestor_input').val(id);
  modalShow('alert_content','myModal','确认删除该投资人?');
}

function delFi(id){
  $('.modal-footer').find('div').hide();
  $('#editField').hide();
  $('.modal-footer').find('div').filter('#delFiBtn').show();
  $('#delFi_input').val(id);
  modalShow('alert_content','myModal','确认删除该融资信息?');
}
