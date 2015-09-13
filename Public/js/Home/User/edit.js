//add a job
function submitJob(){
  var id = $('#editJob_input').val();
  var xmlHttp = createRequest();
  if(checkValue("#title",20,1,'职位不能为空')&&checkValue("#company2",20,1,'公司名称不能为空')&&checkValue("#job_info",80,1,"职位描述不能为空")){
    var title = "key1="+$("#title").val()+"&";
    var company = "key2="+$("#company2").val()+"&";
    var startyear = "key3="+$("#startyear").val()+"&";
    var startmon = "key4="+$("#startmon").val()+"&";
    var endyear = "key5="+$("#endyear").val()+"&";
    var endmon = "key6="+$("#endmon").val()+"&";

    var info = $("#job_info").val();
    info = (info.replace(/(\n|\r|(\r\n))/g,"<br>"));
    info = "key7="+ escape(info) +"&";
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

//add a case
function case_submit(){
  var id = $('#editCase_input').val();
  var xmlHttp = createRequest();
  if(checkValue("#company",30,1,'投资公司不能为空') && checkValue("#investor",50,1,'投资代表主体不能为空')){
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

function saveInfo(){
  var xmlHttp = createRequest();
  if(checkValue('#edit_name',16,1,'姓名不能为空') && checkValue('#edit_brief',80,0,'')){
    if($("#choices").children().length==0){
      alert("请至少选择一个感兴趣领域");
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
      request(xmlHttp,data,"editInfo");
      if(xmlHttp.responseText.match('200')){
        document.location.reload();
      }
    }
  }
}

//delete a case?
function delCase(id){
  $('.modal-footer').find('div').hide();
  $('.modal-footer').find('div').filter('#delCaseBtn').show();
  $('#delCase_input').val(id);
  modalShow('alert_content','myModal','确认删除该投资案例?');
}

//confirm deleting a case
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

//cancel deleting a case
$('#delCase_cancel').click(function(){
  $('#delCase_input').val('');
});

//edit a case
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

//delete a project?
function delPro(id){
  $('.modal-footer').find('div').hide();
  $('.modal-footer').find('div').filter('#delProBtn').show();
  $('#delPro_input').val(id);
  modalShow('alert_content','myModal','确认删除该创业项目?');
}

$('#delPro_confirm').click(function(){
  var xmlHttp = createRequest();
  request(xmlHttp,'c='+$("#delPro_input").val(),"delPro");
  if(xmlHttp.responseText==200){
    alert('删除成功');
    document.location.reload();
  }
});

$('#delPro_cancel').click(function(){
  $('#delPro_input').val('');
});

//delete a job?
function delJob(id){
  $('.modal-footer').find('div').hide();
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


//edit a job
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

  str = unescape(info.job_info);
  str = str.replace(/<br>/g,"\r");
  $('#job_info').val(str);
  $('#editJob_input').val(id);
}

//edit and save company and title
function editBasics(){
  var xmlHttp = createRequest();
  if(checkValue("#editCompany",30,1,'公司不能为空') && checkValue("#editTitle",30,1,'职位不能为空')){
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

//edit and save gender/birthday/city
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

//edit education
function editEducation(){
  var school = 'key1='+$('#editSchool').val()+'&';
  var degree = 'key2='+$('#editDegree').val()+'&';
  var year = 'key3='+$('#eduStartyear').val()+'&';
  var mon = 'key4='+$('#eduStartmon').val()+'&';
  var data = school+degree+year+mon;
  if(checkValue("#editSchool",128,1,'学校不能为空')){
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

//edit sns account
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

function submitAuth(id){
  var xmlHttp = createRequest();
  request(xmlHttp,'val='+id,"requestAuth");
  if(xmlHttp.responseText == '200'){
    alert('提交成功');
    document.location.reload();
  }
  else if(xmlHttp.responseText == '4041'){
    //alert('请填写姓名');
    $('#alert_window')[0].innerHTML = "<strong>请填写姓名</strong>"; 
    $('.alert').fadeIn();
  }
  else if(xmlHttp.responseText == '4042'){
    //alert('请填写公司、职位信息');
    $('#alert_window')[0].innerHTML = "<strong>请填写公司、职位信息</strong>"; 
    $('.alert').fadeIn();
  }
  else if(xmlHttp.responseText == '4043'){
    //alert('请上传名片');
    $('#alert_window')[0].innerHTML = "<strong>请上传名片</strong>"; 
    $('.alert').fadeIn();
  }
  else if(xmlHttp.responseText == '4044'){
    //alert('请上传公司营业执照');
    $('#alert_window')[0].innerHTML = "<strong>请上传公司营业执照</strong>"; 
    $('.alert').fadeIn();
  }
  else if(xmlHttp.responseText == '4045'){
    //alert('请上传组织结构代码证件');
    $('#alert_window')[0].innerHTML = "<strong>请上传组织结构代码证件</strong>"; 
    $('.alert').fadeIn();
  }
  else if(xmlHttp.responseText == '4046'){
    //alert('请上传公司近三年财务报表');
    $('#alert_window')[0].innerHTML = "<strong>请上传公司近三年财务报表</strong>"; 
    $('.alert').fadeIn();
  }
  else if(xmlHttp.responseText == '4047'){
    //alert('请上传个人财产证明');
    $('#alert_window')[0].innerHTML = "<strong>请上传个人财产证明</strong>"; 
    $('.alert').fadeIn();
  }
  else if(xmlHttp.responseText == '4048'){
    //alert('请填写至少三个投资案例');
    $('#alert_window')[0].innerHTML = "<strong>请填写至少三个投资案例</strong>"; 
    $('.alert').fadeIn();
  }
  else{

  }

}

function crop(url){
  var x1 = "x1="+$('#x1').val()+"&";
  var y1 = "y1="+$('#y1').val()+"&";
  var w = "w="+$('#w').val()+"&";
  var h = "h="+$('#h').val()+"&";
  var data = x1+y1+w+h;
  var xmlHttp = createRequest();
  request(xmlHttp,data,"profileCrop");
    if(xmlHttp.responseText==200){
        window.location.href=url;
    }
}


