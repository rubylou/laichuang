function editorInitialize(id){
	//实例化编辑器
	var um = UE.getEditor(id);
	UE.getEditor(id).ready(function() {
	  //this是当前创建的编辑器实例
	  this.setContent('项目图文介绍');
	});
	return um;
}

function getContent(um) {    
  var content = um.getContent();
  content = content.replace(/·/g,"&middot;");
  /*content = content.replace(/&#38;/g,"%26");
  content = content.replace(/&#39;/g,"%27" );
  content = content.replace(/&quot;/g,"%22")*/
  content = escape(content);
  return (content);  
}