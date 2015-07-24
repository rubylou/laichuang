<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class MediaController extends Controller {
    //articleList
    public function index(){
        $Form = new Model();

        $articlesRaw=$Form->query("select article_id,article_title,article_type,article_time,admin_id 
            from admin_articles order by article_time desc");
        if($articlesRaw){
                foreach ($articlesRaw as $key => $value) {
                    $articlesRaw[$key]['article_type'] = C('MODULE_CODE')[$articlesRaw[$key]['article_type']];
                }
                $this->x = $articlesRaw;
                $this->assign('articlelist',$articlesRaw);
            }

    	$this->display();
        
    }

    public function articleEdit(){
    	//dump($_SESSION);
    	$this->type = json_encode(C('MODULE_CODE'));
    	$this->field = json_encode(C('INTEREST_FIELD'));
    	$this->object = json_encode(C('OBJECT_CODE'));
        
        $article_id=$_GET['key'];
        //dump($_GET);
        if($article_id)// 1 for edit an old article 2for edit a new article and no need to fill
        {

            $this->fill_type=1;
            $Form = new Model();
            $articleRaw=$Form->query("select * from admin_articles where article_id='%s'",$article_id);
            $this->article=$articleRaw[0];
            //dump($this->article);
        }
        else
        {
            $this->fill_type=2;
        }





    	$this->display();
    }

    public function articleView(){

    }

    public function articleSave(){
    	//dump($_POST);
    	$Form = new Model();
    	$date = date("Y-m-d");
    	$id = date("Ymdhis");
    	$result = $Form->execute("replace into admin_articles (article_id,admin_id,article_title,article_type,
    		article_field,article_object,article_about,article_content,article_time) values 
    	('%s','%s','%s',%d,%d,'%s',%d,'%s','%s')",$id,$_SESSION['userid'],$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$date);
    	if($result){
    		echo 200;
    	}
    	else{
    		echo 400;
    	}

    }
    public function articleUpdate(){
        //dump($_POST);
        $Form = new Model();
        $date = date("Y-m-d");
        $id = date("Ymdhis");
        //'update project_info set status=%d where project_id="%s"'
        $sqlstr=sprintf("update admin_articles set admin_id='%s',article_title='%s',article_type=%d,
            article_field=%d,article_object=%d,article_about=%d,article_content='%s',article_time='%s' where article_id='%s'",  
            $_SESSION['userid'],$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$date,$_POST['key7']);
        $result = $Form->execute($sqlstr);
        if($result){
            echo $result;
        }
        else{
            echo $result;
        }

    }

    public function articleDel(){

    }

}