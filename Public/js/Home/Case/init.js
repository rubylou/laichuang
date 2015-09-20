$("#target").Jcrop({
	setSelect:[60,0,420,360],
	aspectRatio: 1 / 1,
	onChange:   showCoords,
  	onSelect:   showCoords,
});

function showCoords(c)
{
	$('#x1').val(c.x);
    $('#y1').val(c.y);
    $('#w').val(c.w);
    $('#h').val(c.h);

}

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
	$('#conditions').append($('<p></p>').attr("id","row"+i));
	}

	for(var i in field){
	addLabel(i,field);
	}
}

function initializeProject(intro,member,fi,recruit,require){
	$("#details")[0].innerHTML = unescape(intro);
	
	var str = (member);
	str = str.replace(/&lt;br&gt;/g,"\r");
	str = str.replace(/&amp;nbsp;/g," ");
	$("#pro_member")[0].innerHTML = (str);

	var str1 = (member).replace(/&lt;br&gt;/g,"<br>");
	str1 = str1.replace(/&amp;/g,"&");
	$("#project_member")[0].innerHTML = (str1);

	str = (fi);
	str = str.replace(/&lt;br&gt;/g,"\r");
	str = str.replace(/&amp;nbsp;/g," ");
	$("#pro_fi")[0].innerHTML = (str);

	str1 = (fi).replace(/&lt;br&gt;/g,"<br>");
	str1 = str1.replace(/&amp;/g,"&");
	$("#project_fi")[0].innerHTML = (str1);

	str = (recruit);
	str = str.replace(/&lt;br&gt;/g,"\r");
	str = str.replace(/&amp;nbsp;/g," ");
	$("#pro_recruit")[0].innerHTML = (str);

	str1 = (recruit).replace(/&lt;br&gt;/g,"<br>");
	str1 = str1.replace(/&amp;/g,"&");
	$("#project_recruit")[0].innerHTML = (str1);

	str = (require);
	str = str.replace(/&lt;br&gt;/g,"\r");
	str = str.replace(/&amp;nbsp;/g," ");
	$("#pro_require")[0].innerHTML = (str);

	str1 = (require).replace(/&lt;br&gt;/g,"<br>");
	str1 = str1.replace(/&amp;/g,"&");
	$("#project_require")[0].innerHTML = (str1);

}

function initializeProject1(intro,member,fi,recruit,require){
	$("#details")[0].innerHTML = unescape(intro);

	var str1 = (member).replace(/&lt;br&gt;/g,"<br>");
	str1 = str1.replace(/&amp;/g,"&");
	$("#project_member")[0].innerHTML = (str1);

	str1 = (fi).replace(/&lt;br&gt;/g,"<br>");
	str1 = str1.replace(/&amp;/g,"&");
	$("#project_fi")[0].innerHTML = (str1);

	str1 = (recruit).replace(/&lt;br&gt;/g,"<br>");
	str1 = str1.replace(/&amp;/g,"&");
	$("#project_recruit")[0].innerHTML = (str1);

	str1 = (require).replace(/&lt;br&gt;/g,"<br>");
	str1 = str1.replace(/&amp;/g,"&");
	$("#project_require")[0].innerHTML = (str1);
	
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
		label.addClass('btn btn-default label theme-btn-inverse');
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
		label.addClass('btn btn-default label theme-btn');
		label.attr('value',id);
		label.attr("id","field"+id);
		label.click(function(){
		  if($("#choices").children().length==3){
		    alert("选择已达上限");
		  }
		  else{
		    addInterest(id,field);
		    $(this).remove();
		  }
		});

		var row = parseInt(id/6);
		if(id%6==0){
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