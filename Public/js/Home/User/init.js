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

function upload(id){
	return $(id).click();
}

function finish_upload(id1,id2){
	var result =$(id1).val().match(/\.[^\.]+/g);
	if(RegExp.lastMatch == '.png' || RegExp.lastMatch == '.jpg' || RegExp.lastMatch == '.jpeg' || RegExp.lastMatch == '.gif'){
		$(id2).submit();
	}
	else{
		alert('请上传图片文件！');
	}
}

function disableEnd(value){
	if(value){
		$("#endyear").attr("disabled",true);
		$("#endmon").attr("disabled",true);
	}
	else{
		$("#endyear").removeAttr("disabled");
		$("#endmon").removeAttr("disabled");
	}
}

function initializeField(rows,field){
	for(var i=0;i<=rows;i++){
		$('#conditions').append($('<div class="row"></div>').attr("id","row"+i));
	}

	for(var i in field){
		addLabel(i,field);
	}
}

function addInterest(id,field){
	var exist = $("#choices").contents().filter("#field"+id);
	if(exist.text()==field[id]){
		alert("已添加");
	}
	else{
		var label = $('<span></span>').text(field[id]);
		label.addClass('btn btn-default label label-default theme-btn');
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
		label.addClass('btn btn-default label theme-bg theme-white');
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

		var row = parseInt(id/8);
		if(id%8==0){
		  --row;
		}
		$('#row'+row).append(label);
	}
}

function initailizeCity(city){
  for(var i in city){
    addCity(i,city);
  }
}

function addCity(id,field){
  var option = $('<option></option').text(field[id]);
  option.attr('value',id);
  $('#editCity').append(option);
}
  
  
