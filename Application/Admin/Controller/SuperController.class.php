<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class SuperController extends Controller {

    //adminList
    public function index(){
        $Form = new Model();
        $Users = $Form->query('select admin_id, admin_type from admin_personal');
        $Admins = C('ADMIN_TYPE');
        if($Users){
        	foreach ($Users as $key => $value) {
        		$Users[$key]['admin_type'] = $Admins[$value['admin_type']];
        	}
        	$this->vo = $Users;
        	$this->assign('list',$Users);
        }
        $this->display();
    }

    public function adminAdd(){
    	$this->display();

    }

    public function adminSave(){
    	$Form = new Model();
    	$exist = $Form->query('select admin_id from admin_personal where admin_id = "%s"',$_POST['id']);
    	if($exist){
    		echo 3;
    	}
    	else{
    		$result = $Form->execute('insert into admin_personal (admin_id,admin_pwd,admin_type) 
    			values ("%s","%s",%d)',$_POST['id'],$_POST['key1'],$_POST['key3']);
    		if($result){
    			echo 2;
    		}
    		else {
    			echo -1;
    		}
    	}
    	
    }

    public function adminEdit(){
    	$Form = M('admin_personal');
    	$result = $Form->where('admin_id="%s"',$_GET['key'])->field(array('admin_id','admin_type'))->select();
    	$this->data = $result[0];
    	$this->display();
    }

    public function adminDel(){
        $Form = M('admin_personal');
        if($_SESSION['usertype']==1){
        	$result = $Form->where('admin_id = "%s"',$_GET['key'])->delete();
	        if($result){
	        	header("Location: index"); 
	        }
	        else{
	        	header("Location: index"); 
	        }
        }
        else{
        	header("Location: index"); 
        }
        
    }

}