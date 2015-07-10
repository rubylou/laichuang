<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class MediaController extends Controller {
    //articleList
    public function index(){
    	$this->display();
        
    }

    public function articleEdit(){
    	//dump($_SESSION);
    	$this->type = json_encode(C('MODULE_CODE'));
    	$this->field = json_encode(C('INTEREST_FIELD'));
    	$this->object = json_encode(C('OBJECT_CODE'));
    	$this->display();
    }

    public function articleView(){

    }

    public function articleSave(){
    	//dump($_POST);
    	$Form = new Model();
    	$date = date("Y-m-d");
    	$id = date("Ymdhis");
    	$result = $Form->execute("insert into admin_articles (article_id,admin_id,article_title,article_type,
    		article_field,article_object,article_about,article_content,article_time) values 
    	('%s','%s','%s',%d,%d,'%s',%d,'%s','%s')",$id,$_SESSION['userid'],$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$date);
    	if($result){
    		echo 200;
    	}
    	else{
    		echo 400;
    	}

    }

    public function articleDel(){

    }

}