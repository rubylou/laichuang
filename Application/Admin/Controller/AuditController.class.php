<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class AuditController extends Controller {
    public function index(){
    	//dump($_SESSION);
    	$this->display();   
    }
    public function auditInnovatorVerify(){
        $user_id=$_GET['key'];
        $this->display();
    }
    public function auditInvestorPsVerify(){
        $user_id=$_GET['key'];
        //get all need information 
        dump($user_id);
        $Form = new Model();
        $pinfoRaw=$Form->query("select * from investor_personal ");
        $pInfo=json_encode($pinfoRaw);

        $jobRaw=$Form->query("select * from user_job where user_id=".$user_id);
        $jobInfo=json_encode($jobRaw);

        $this->display();
    }
    public function auditInvestorPsDel(){
        $user_id=$_GET['key'];
    }
    public function auditInnovatorDel(){
        $user_id=$_GET['key'];
    }
    public function fetchInnovatorUnderVerified(){
        $Form = new Model();
        $status=C(UNDERVIRIFIED);
        $user = $Form->query("select user_id,nickname,name,phone from entrepreneur_personal where reg_status = ".$status);
        echo json_encode($user);
    }
    public function fetchInvestorPersonUnderVerified(){
        $Form = new Model();
        $status=C(UNDERVIRIFIED);
        $user = $Form->query("select user_id,name,company,mobile from investor_personal where reg_status = ".$status);
        echo json_encode($user);
    }



    public function projectList(){

    }

    public function investorList(){

    }

    public function entrepreneurList(){
    	
    }


}