<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class SetController extends Controller {
    public function index(){
        $id=$_SESSION['userid'];
        $Form = new Model();
        $users=$Form->query('select admin_id,admin_nickname from admin_personal where admin_id="%s"',$id);
        $this->user=$users[0];
        //dump($this->user);
    	$this->display();
    }
    public function adminNKUpdate(){
        $id=$_SESSION['userid'];
        $nickname=$_POST['key1'];
        $Form = new Model();
        //$s=sprintf('update admin_personal set admin_nickname="%s" where admin_id="%s"',$nickname,$id);
        //echo $s;
        $Form->execute('update admin_personal set admin_nickname="%s" where admin_id="%s"',$nickname,$id);
    }  
    public function adminPwdUpdate(){
        $id=$_SESSION['userid'];
        $pwd=$_POST['key1'];
        $Form = new Model();
         $Form->execute('update admin_personal set admin_pwd="%s" where admin_id="%s"',$pwd,$id);
    }
}