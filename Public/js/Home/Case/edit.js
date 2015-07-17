function fi_submit(id){
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
    request(xmlHttp,data,"profiAdd/p/"+id);
    if(xmlHttp.responseText==200){
      document.location.reload();
    }
    else{
      
    }
  }
  else{
  }
  
}