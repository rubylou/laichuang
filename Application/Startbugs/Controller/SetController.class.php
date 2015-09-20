<?php
namespace Startbugs\Controller;
use Think\Controller;
use Think\Model;
class SetController extends Controller {
    public function index(){
        if(session('?userid')&&session('?usertype')){
            $id=$_SESSION['userid'];
            $Form = new Model();
            $users=$Form->query('select admin_id,admin_nickname from admin_personal where admin_id="%s"',$id);
            $this->user=$users[0];
            //dump($this->user);
        	$this->display();
        }else{
            $this->redirect('Index/index');
        }
    }
    public function adminNKUpdate(){
        if(session('?userid')&&session('?usertype')){
            $id=$_SESSION['userid'];
            $nickname=$_POST['key1'];
            $Form = new Model();
            //$s=sprintf('update admin_personal set admin_nickname="%s" where admin_id="%s"',$nickname,$id);
            //echo $s;
            $Form->execute('update admin_personal set admin_nickname="%s" where admin_id="%s"',$nickname,$id);
        }
    }  
    public function adminPwdUpdate(){
        if(session('?userid')&&session('?usertype')){
            $id=$_SESSION['userid'];
            $pwd=$_POST['key1'];
            $Form = new Model();
             $Form->execute('update admin_personal set admin_pwd="%s" where admin_id="%s"',$pwd,$id);
         }
    }
    public function homeShowProject(){
        if(session('?userid')&&session('?usertype')){
            $Form = new Model();
            $content=$Form->query("select * from home_show where tag='%d'",C(PROJECT_CODE));

            $this->projects=json_encode( $content );

            //dump($this->projects);
            $this->display();
        }else{
            $this->redirect('Index/index');
        }

    }
    public function homeShowArticle(){
         if(session('?userid')&&session('?usertype')){
            $Form = new Model();
            $content=$Form->query("select * from home_show where tag='%d'",C(NEWS_CODE));

            $this->news=json_encode( $content );

            //dump($this->projects);
            $this->display();
        }else{
            $this->redirect('Index/index');
        }
    }

    public function saveNews()
    {
        if(session('?userid')&&session('?usertype')){
            $ps=$_POST;
            $Form = new Model();
            
            $Form->execute("delete from home_show where tag='%d'",C(NEWS_CODE));
            if($_POST)
            {
              for($i=1;$i<=12;$i++)
                {   
                   
                    $tmp=$ps['key'.$i];
                    
                    if($tmp){
                        //dump($tmp."||||".$i);
                        //$old[$i]=$tmp;
                        $Form->execute("replace into home_show (content_id,tag) values ('%s','%d')",$tmp,C(NEWS_CODE));

                    }
                        
                    
                }  
            }
            
            
            echo 200;
        }
    }
    public function saveProjects()
    {
        if(session('?userid')&&session('?usertype')){
            $ps=$_POST;
            $Form = new Model();
            
            $Form->execute("delete from home_show where tag='%d'",C(PROJECT_CODE));
            if($_POST)
            {
              for($i=1;$i<=12;$i++)
                {   
                   
                    $tmp=$ps['key'.$i];
                    
                    if($tmp){
                        //dump($tmp."||||".$i);
                        //$old[$i]=$tmp;
                        $Form->execute("replace into home_show (content_id,tag) values ('%s','%d')",$tmp,C(PROJECT_CODE));

                    }
                        
                    
                }  
            }
            
            
            echo 200;
        }
    }
}