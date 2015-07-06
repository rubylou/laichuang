function selectCollpase(id1,id2){
    $(id1).collapse('toggle');
    $(id2).attr('class','collapse');
  }
  
  function loginInvestor(){
    var status = "value=1&";
    var name = "key1="+$('#logName1').val()+"&";
    var pwd = "key2="+CryptoJS.SHA1($('#logPwd1').val())+"&";
    var data = status+name+pwd;
    var xmlHttp = createRequest();
    request(xmlHttp,data,"loginCheck");
    if(xmlHttp.responseText==200){
      window.location.href = 'index';
    }
    else{
      modalShow('alert_content','myModal','用户名或密码错误');
    }
  }

  function loginInnovator(){
    var status = "value=2&";
    var name = "key1="+$('#logName2').val()+"&";
    var pwd = "key2="+CryptoJS.SHA1($('#logPwd2').val())+"&";
    var data = status+name+pwd;
    var xmlHttp = createRequest();
    request(xmlHttp,data,"loginCheck");
    if(xmlHttp.responseText==200){
      window.location.href = 'index';
    }
    else{
      modalShow('alert_content','myModal','用户名或密码错误');
    }

  }