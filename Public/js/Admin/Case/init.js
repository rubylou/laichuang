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

function initializeField(rows,field){
	for(var i=0;i<=rows;i++){
	$('#conditions').append($('<div class="row"></div>').attr("id","row"+i));
	}

	for(var i in field){
	addLabel(i,field);
	}
}

function initializeProject(intro,member,fi,recruit,require){
	$("#details")[0].innerHTML = unescape(intro);
	var str = unescape(member);
	str = str.replace(/<br>/g,"\r");
	$("#pro_member")[0].innerHTML = (str);
	$("#project_member")[0].innerHTML = (unescape(member));

	str = unescape(fi);
	str = str.replace(/<br>/g,"\r");
	$("#pro_fi")[0].innerHTML = (str);
	$("#project_fi")[0].innerHTML = (unescape(fi));

	str = unescape(recruit);
	str = str.replace(/<br>/g,"\r");
	$("#pro_recruit")[0].innerHTML = (str);
	$("#project_recruit")[0].innerHTML = (unescape(recruit));

	str = unescape(require);
	str = str.replace(/<br>/g,"\r");
	$("#pro_require")[0].innerHTML = (str);
	$("#project_require")[0].innerHTML = (unescape(require));
}


function optionInitialize(value){
  var field = value;
  for(var i in field){
    addOption(i,field);
  }
}

function addOption(id,field){
  var option = $('<option></option').text(field[id]);
  option.attr('value',id);
  $('#proField').append(option);
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

function showEditPanel(id){
	if($(id).is(":visible")){
		$(id).fadeOut();
	}
	else{
		$(id).fadeIn();
	}
	
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

function upload(id){
  return $(id).click();
}