  function addInterests(value,id){
    var interests = value;
    for(var i in interests){
      if(interests[i]!=null){
        var label = $('<span></span>').text(' '+interests[i]+' ');
        $(id).find(".glyphicon-tag").after(label);
      }
    }
  }
  
  function addRound(value,id){
    var rounds = value;
    for(var i in rounds){
      addOption(rounds[i],i,id);
    }
  }

  function addCur(value,id){
    var currency = value;
    for(var i in currency){
      addOption(currency[i],i,id);
    }
  }
  
  function addYM(year,month,start){
    for(var i=1;i<13;i++){
      addOption(i,i,month);
    }

    var date = new Date();
    for(var i=date.getFullYear(); i>=start;i--){
      addOption(i,i,year);
    }

  }
  
  function addOption(value,index,id){
  	var option = $('<option></option').text(value);
    option.attr('value',index);
    $(id).append(option);
  }
