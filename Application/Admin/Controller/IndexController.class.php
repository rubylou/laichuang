<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class IndexController extends Controller {
    
    public function index(){
        //$a=send_active_mail("1154066164",2,C(TESTMAIL));
        //dump(sha1('supersuper'));
        $this->display();
    }
    
    public function login(){
        
    	$Form = new Model();
    	$user = $Form->query("select * from admin_personal where admin_id = '%s'", $_POST['loginuser']);
        //echo  json_encode($user[0]);
    	if($user[0]['admin_pwd']===$_POST['loginpwd']){
    		$_SESSION['userid'] = $user[0]['admin_id'];
    		$_SESSION['usertype'] = $user[0]['admin_type'];

            if($_SESSION['usertype']==1){
                echo 201;
            }
            else if($_SESSION['usertype']==2){
                echo 202;
            }
            else if($_SESSION['usertype']==3){
                echo 203;
            }
            else{
                echo 404;
            }
    	}else
        {
            echo 409;
        }
        
    	
    }

    public function logout(){
    	$_SESSION = array();
    	header("Location: index"); 
    }
   
}