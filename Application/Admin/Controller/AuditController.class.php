<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class AuditController extends Controller {
    public function index(){
    	//dump($_SESSION);
    	$this->display();   
    }
    public function fetchInnovatorUnderVerified(){
        $Form = new Model();
        $user = $Form->query("select * from entrepreneur_personal where reg_status = 0");
        echo json_encode($user);
    }
    public function projectList(){

    }

    public function investorList(){

    }

    public function entrepreneurList(){
    	
    }


}