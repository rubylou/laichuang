function regsubmit(){
  var pwd = "#regPwd";
  var pwdfirm = "#regPwdfirm";

  var idValid = checkID($('#regUser').val());
  if(!idValid){
    modalShow('alert_content','myModal','用户名不符合命名规则');
  }
  
  else{
    if(checkPwd(pwd,pwdfirm)){
      pwd = CryptoJS.SHA1($(pwd).val());
      pwdfirm = CryptoJS.SHA1($(pwdfirm).val());
      var xmlHttp = createRequest();
      var data = 'id='+$('#regUser').val()+'&key1='+pwd+'&key2='+pwdfirm+'&key3='+$('#regType').val();
      request(xmlHttp,data,"adminSave");
      if(xmlHttp.responseText==2){
        window.location.href = 'index';
      }
      else{
        modalShow('alert_content','myModal','用户名已存在');
      }
    }
  }

  
}