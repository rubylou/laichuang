function loginsubmit(url,url1,url2,url3){
  var userlen = $('#loginuser').val().length;
  var pwdlen = $('#loginpwd').val().length;
  if(userlen==0 || pwdlen==0){
      modalShow('alert_content','myModal','请填写用户名及密码');
  }
  else{
      var hash = CryptoJS.SHA1($("#loginpwd").val());
      var xmlHttp = createRequest();
      var data = 'loginuser='+$('#loginuser').val()+'&loginpwd='+hash;
      request(xmlHttp,data,url);
      if(xmlHttp.responseText==201){
        window.location.href = url1;
      }
      else if(xmlHttp.responseText==202){
        window.location.href = url2;
      }
      else if(xmlHttp.responseText==203){
        window.location.href = url3;
      }
      else if(xmlHttp.responseText==404){
        modalShow('alert_content','myModal','用户名或密码错误');
      }
  }
}  