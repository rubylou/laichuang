function submitJob(){
  var id = $('#editJob_input').val();
  var xmlHttp = createRequest();
  if(checkValue("#title",20,0,'职位不能为空')&&checkValue("#company2",20,0,'公司名称不能为空')&&checkValue("#job_info",80,0,"职位描述不能为空")){
    var title = "key1="+$("#title").val()+"&";
    var company = "key2="+$("#company2").val()+"&";
    var startyear = "key3="+$("#startyear").val()+"&";
    var startmon = "key4="+$("#startmon").val()+"&";
    var endyear = "key5="+$("#endyear").val()+"&";
    var endmon = "key6="+$("#endmon").val()+"&";
    var info = "key7="+$("#job_info").val()+"&";
    var now = "key8="+document.getElementById('untilnow').checked+'&';
    var data = title+company+startyear+startmon+endyear+endmon+info+now;
    if(id.length>0){
      request(xmlHttp,data+"c="+id,'jobSave');
    }
    else{
      request(xmlHttp,data,"jobSave");
    }
    if(xmlHttp.responseText==200){
      document.location.reload();
    }
  }
}

function case_submit(){
  var id = $('#editCase_input').val();
  var xmlHttp = createRequest();
  if(checkValue("#company",30,0,'投资公司不能为空') && checkValue("#investor",50,0,'投资代表主体不能为空')){
    var company = 'key1='+$('#company').val()+'&';
    var round = 'key2='+$('#round').val()+'&';
    var currency1 = 'key3='+$('#currency1').val()+'&';
    var amount = 'key4='+$('#amount').val()+'&';
    var currency2 = 'key5='+$('#currency2').val()+'&';
    var assess = 'key6='+$('#assess').val()+'&';
    var investor = 'key7='+$('#investor').val()+'&';
    var investyear = 'key8='+$('#investyear').val()+'&';
    var investmon = 'key9='+$('#investmon').val()+'&';
    var data = company+round+currency1+amount+currency2+assess+investor+investyear+investmon;
    if(id.length>0){
      request(xmlHttp,data+"c="+id,'caseSave');
    }
    else{
      request(xmlHttp,data,"caseSave");
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

function editName(){
  $('#user_name').attr('type','text');
  $('#saveName').show();
}

function saveName(){
  var xmlHttp = createRequest();
  request(xmlHttp,'name='+$('#user_name').val(),"editInfo");
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
  else{
    $('#user_name').val('');
    $('#user_name').attr('placeholder','姓名带有特殊字符');
    $('#user_name').focus();
  }
}

function editBrief(){
  $('#user_brief').attr('type','text');
  $('#saveBrief').show();
}

function saveBrief(){
  var xmlHttp = createRequest();
  request(xmlHttp,'brief='+$('#user_brief').val(),"editInfo");
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
  else{
    $('#user_brief').val('');
    $('#user_brief').attr('placeholder','简介带有特殊字符');
    $('#user_brief').focus();
  }
}

function editField(){
  $('#editField').show();
  $('.modal-footer').find('div').hide();
  $('.modal-footer').find('div').filter('#editFieldBtn').show();
  modalShow('alert_content','myModal','请重新选择感兴趣领域(最多选5个)');
}

$('#editField_confirm').click(function(){
  var field = 'field=';
  $('#choices').children().each(function(){
    field = field + $(this).attr('value') + ',';
  });
  field = field + "&";
  var xmlHttp = createRequest();
  request(xmlHttp,field,"editInfo");
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
});

function delCase(id){
  $('.modal-footer').find('div').hide();
  $('#editField').hide();
  $('.modal-footer').find('div').filter('#delCaseBtn').show();
  $('#delCase_input').val(id);
  modalShow('alert_content','myModal','确认删除该投资案例?');
}

$('#delCase_confirm').click(function(){
  var xmlHttp = createRequest();
  request(xmlHttp,'c='+$("#delCase_input").val(),"delCase");
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
  else{
    $('#collapseExample1').collapse('hide');
  }
});

$('#delCase_cancel').click(function(){
  $('#delCase_input').val('');
});

function editCase(id){
  var xmlHttp = createRequest();
  request(xmlHttp,'c='+id,"editCase");
  var info = JSON.parse(xmlHttp.responseText);
  $('#collapseExample1').collapse('show');
  $('#company').val(info.company);
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
  $('#editCase_input').val(id);
  
}

function delJob(id){
  $('.modal-footer').find('div').hide();
  $('#editField').hide();
  $('.modal-footer').find('div').filter('#delJobBtn').show();
  $('#delJob_input').val(id);
  modalShow('alert_content','myModal','确认删除该工作经历?');
}

$('#delJob_confirm').click(function(){
  var xmlHttp = createRequest();
  request(xmlHttp,'c='+$("#delJob_input").val(),"delJob");
  if(xmlHttp.responseText==200){
    document.location.reload();
  }
  else{
    $('#collapseExample2').collapse('hide');
  }
});

$('#delJob_cancel').click(function(){
  $('#delJob_input').val('');
});


function editJob(id){
  var xmlHttp = createRequest();
  request(xmlHttp,'c='+id,"editJob");
  var info = JSON.parse(xmlHttp.responseText);
  $('#collapseExample2').collapse('show');
  $("#title").val(info.job_title);
  $('#company2').val(info.job_company);
  $('#startyear').children('option').each(function(){
    if($(this).val()==info.startyear){
      $(this).attr('selected','selected');
    }
  });
  $('#startmon').children('option').each(function(){
    if($(this).val()==info.startmon){
      $(this).attr('selected','selected');
    }
  });
  $('#endyear').children('option').each(function(){
    if($(this).val()==info.endyear){
      $(this).attr('selected','selected');
    }
  });
  $('#endmon').children('option').each(function(){
    if($(this).val()==info.endmon){
      $(this).attr('selected','selected');
    }
  });
  $('#job_info').val(info.job_info);
  $('#editJob_input').val(id);
}

function editBasics(){
  var xmlHttp = createRequest();
  if(checkValue("#editCompany",30,0,'公司不能为空') && checkValue("#editTitle",30,0,'职位不能为空')){
    var basics = "company="+$('#editCompany').val()+"&";
    basics += "title="+$('#editTitle').val()+"&";
    request(xmlHttp,basics,"editInfo");
    if(xmlHttp.responseText.match('200')){
      document.location.reload();
    }
    else{
      $('#collapseExample3').collapse('hide');
      $('#editCompany').val('');
      $('#editTitle').val('');
    }
  }
  
}

function editInnovatorBasics(){
  var gender = "gender="+$('#editGender').val()+'&';
  var birth = "birth="+$('#editBirth').val()+'&';
  var city = "city="+$('#editCity').val()+'&';
  var data = gender+birth+city;
  if(checkBirth('#editBirth')){
    var xmlHttp = createRequest();
    request(xmlHttp,data,"editInfo");
    if(xmlHttp.responseText.match('200')){
      document.location.reload();
    }
    else{
      $('#collapseExample3').collapse('hide');
    }
  }
}

function editEducation(){
  var school = 'key1='+$('#editSchool').val()+'&';
  var degree = 'key2='+$('#editDegree').val()+'&';
  var year = 'key3='+$('#eduStartyear').val()+'&';
  var mon = 'key4='+$('#eduStartmon').val()+'&';
  var data = school+degree+year+mon;
  if(checkValue("#editSchool",128,0,'学校不能为空')){
    var xmlHttp = createRequest();
    request(xmlHttp,data,"editEdu");
    if(xmlHttp.responseText==200){
      document.location.reload();
    }
    else{
      $('#collapseExample1').collapse('hide');
    }
  }
}

function editSNS(){
  var xmlHttp = createRequest();
  if($("#editSNS").val().match('weibo.com')){
    var sns = "sns="+$("#editSNS").val()+"&";
    request(xmlHttp,sns,"editInfo");
    if(xmlHttp.responseText==200){
      document.location.reload();
    }
    else{
      $('#collapseExample4').collapse('hide');
      $('#editSNS').val('');
    }
  }
  else{
    $('#editSNS').val('');
    $('#editSNS').attr('placeholder','URL无效');
    $('#editSNS').focus();
  }
}

