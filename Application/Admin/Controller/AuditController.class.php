<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class AuditController extends Controller {
    public function index(){
    	//dump($_SESSION);
    	$this->display();   
    }



    public function fetchInnovator(){
        $Form = new Model();
        $user = $Form->query("select user_id,nickname,name,phone from entrepreneur_personal");
        echo json_encode($user);
    }
    public function fetchInnovatorUnderVerified(){
        $Form = new Model();
        $status=C(UNDERVIRIFIED);
        $user = $Form->query("select user_id,nickname,name,phone from entrepreneur_personal where reg_status = ".$status);
        echo json_encode($user);
    }
    public function auditInnovatorVerify(){
        $user_id=$_GET['key'];
        $this->display();
    }
    

    




    public function fetchInvestorPersonUnderVerified(){
        $Form = new Model();
        $status=C(UNDERVIRIFIED);
        $user = $Form->query("select user_id,name,company,mobile from investor_personal where reg_status = ".$status);
        echo json_encode($user);
    }
    public function fetchInvestor(){
        $Form = new Model();
        $user = $Form->query("select user_id,name,company,mobile from investor_personal");
        echo json_encode($user);
    }
    public function auditInvestorPsVerify(){
        $user_id=$_GET['key'];
        //get all need information 
        //dump($user_id);
        $Form = new Model();
        
        $pinfoRaw=$Form->query("select * from investor_personal where user_id=".$user_id);
        $this->userInfo=json_encode($pinfoRaw);
        //dump($this->userInfo);
        
        $jobRaw=$Form->query("select * from user_job where user_id=".$user_id);
        $this->jobInfo=json_encode($jobRaw);
        //dump($this->jobInfo);

        $this->display();
    }
    public function receiveInverstorPVerifyResult(){
        $Form = new Model();
        $user_id=$_GET['user_id'];
        $result=$_GET['result'];
        $note=$_GET['note'];
        $admin_id=$_SESSION[userid];
        //update the admin_audition
        $audition_type=C(INVESTOR_CODE);
        $time = date('Y-m-d');
        //$sqlString="replace into admin_audition (admin_id,audition_type,audition_object,audition_time,audition_note) values ()"
        //echo json_encode($sqlString);
        $Form->execute('insert into admin_audition (admin_id,audition_type,audition_object,audition_time,audition_note) 
            values ("%s","%d","%s","%s","%s")',
            $admin_id,$audition_type,$user_id,$time,$note);
        //echo "ok";
        //update the user reg_status
        $Form->execute('update investor_personal set reg_status=%d where user_id="%s"',$result,$user_id);


    }







    public function auditInvestorPsDel(){
        $user_id=$_GET['key'];
    }
    public function auditInnovatorDel(){
        $user_id=$_GET['key'];
    }
    
   



    public function projectList(){

    }

    public function investorList(){

    }

    public function entrepreneurList(){
    	
    }


}