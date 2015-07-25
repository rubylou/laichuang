<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class AuditController extends Controller {
    public function index(){
    	//dump($_SESSION);
    	$this->display();   
    }

    public function fetchProject(){
        $Form = new Model();
        $pro = $Form->query("select project_id,project_admin,project_brief,project_logo,project_name from project_info");
        echo json_encode($pro);
    }
    public function fetchProjectUnderVerified(){
        $Form = new Model();
        $status=C(UNDERVIRIFIED);
        $pro = $Form->query("select project_id,project_admin,project_brief,project_logo,project_name from project_info where status=".$status);
        echo json_encode($pro);
    }
    public function auditProjectVerify(){
        $id=$_GET['key'];
        $Form = new Model();
        
        $result = $Form->query('select project_info.*, name, portrait from project_info inner join entrepreneur_personal on project_admin = user_id where project_id="%s"',$id);
        foreach ($result as $key => $value) {
            $result[$key]['project_type'] = C('INTEREST_FIELD')[$value['project_type']];
            $result[$key]['project_intro'] = htmlspecialchars_decode($value['project_intro']);
        }

        //关注量
        $follow = $Form->query('select count(user_id) from relation_follow where object_type=%d and object_id="%s"',C(PROJECT_CODE),$id);
        $result[0]['project_watches'] = $follow[0]['count(user_id)'];
        $this->info = $result[0];

        //所属领域
        $result = $Form->query('select interest_field from interest_project where id="%s"',$id);
        if($result){
            $fields = C('INTEREST_FIELD');
            foreach ($result as $key => $value) {
                $result[$key]['interest_field'] = $fields[$value['interest_field']];
            }
            $this->interests = json_encode($result);
        }
        else{
            $this->interests = json_encode(null);
        }
        
        //成员信息
        $member = $Form->query('select project_member.*, portrait, nickname from project_member inner join entrepreneur_personal on project_member.user_id = entrepreneur_personal.user_id where project_id="%s"',$id);
        $this->$vo = $member;
        $this->assign("memberlist",$member);

        //投资人信息
        $investor = $Form->query('select project_investor.*, portrait, name from project_investor inner join investor_personal on project_investor.user_id = investor_personal.user_id where project_id="%s"',$id);
        $this->$it = $investor;
        $this->assign("investorlist",$investor);

        //关注信息
        $follow = $Form->query("select follow_status from relation_follow where user_id='%s' 
            and object_id='%s' and object_type=%d",$_SESSION['id'],$_GET['key'],C(PROJECT_CODE));
        if($follow[0]['follow_status']=='1'){
            $this->follow = C('FOLLOWING');
        }
        else{
            $this->follow = C('UNFOLLOW');
        }

        //融资情况
        $profi = $Form->query('select * from project_fi where project_id="%s" order by invest_time desc',$id);
        if($profi){
            $round = C('INVEST_ROUND');
            $cur = C('CURRENCY_CODE');
            foreach ($profi as $key => $value) {
                $profi[$key]['round'] = $round[$value['round']];
                $profi[$key]['invest_cur'] = $cur[$value['invest_cur']];
                $profi[$key]['assess_cur'] = $cur[$value['assess_cur']];
                $profi[$key]['invest_time'] = substr($value['invest_time'], 0,7);
                if($value['invest_amount']==0){
                    $profi[$key]['invest_amount'] = '-未知-';
                }
            }
            $this->profi = $profi;
            $this->assign('filist',$profi);
        }

        //类似项目
        $familiar = $Form->query('select distinct familiar.id,project_name, project_logo from interest_project as original 
            inner join interest_project as familiar on original.interest_field = familiar.interest_field 
            inner join project_info on familiar.id = project_id
            where original.id = "%s" and familiar.id <> "%s" limit 5',$id, $id);
        $this->familiar = $familiar;
        $this->assign('prolist',$familiar);
        $this->project_id=$id;
        $this->display();
    }
    public function receiveProjectVerifyResult(){
        $Form = new Model();
        $project_id=$_GET['project_id'];
        $result=$_GET['result'];
        $note=$_GET['note'];
        $admin_id=$_SESSION[userid];
        //update the admin_audition
        $audition_type=C(PROJECT_CODE);
        $time = date('Y-m-d');
        //$sqlString="replace into admin_audition (admin_id,audition_type,audition_object,audition_time,audition_note) values ()"
        //echo json_encode($sqlString);
        $Form->execute('insert into admin_audition (admin_id,audition_type,audition_object,audition_time,audition_note) 
            values ("%s","%d","%s","%s","%s")',
            $admin_id,$audition_type,$project_id,$time,$note);
        //echo "ok";
        //update the user reg_status
        $Form->execute('update project_info set status=%d where project_id="%s"',$result,$project_id);


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
        $id=$_GET['key'];
        $fields = C('INTEREST_FIELD');
        $Form = new Model();
        



        $user = $Form->query("select * from entrepreneur_personal where user_id='%s'",$id);
        $user[0]['business'] = $fields[$user[0]['business']];
        $user[0]['gender'] = C('GENDER_CODE')[$user[0]['gender']];
        $user[0]['city'] = C('PROVINCE_CODE')[$user[0]['city']];
        $this->user = $user[0];

        $jobs = $Form->query('select * from user_job where user_id="%s" order by job_start',$id);
        if($jobs){
            foreach ($jobs as $key => $value) {
                $jobs[$key]['job_start'] = substr($value['job_start'], 0,7);
                $jobs[$key]['job_end'] = substr($value['job_end'], 0,7);
            }
            $this->jobs = $jobs;
            $this->assign('joblist',$jobs);
        }
        $this->user_id=$id;


        //教育背景
        $edu = $Form->query('select * from user_edu where user_id="%s"',$_SESSION['id']);
        if($edu){
            $edu[0]['degree'] = C('DEGREE_CODE')[$edu[0]['degree']];
            $edu[0]['year'] = intval(substr($edu[0]['start'],0,4));
            $edu[0]['mon'] = intval(substr($edu[0]['start'],5,2));
            $this->edu = $edu[0];
        }






        $this->display();
    }
    public function receiveInnovatorVerifyResult(){
        $Form = new Model();
        $innovator_id=$_GET['innovator_id'];
        $result=$_GET['result'];
        $note=$_GET['note'];
        $admin_id=$_SESSION[userid];
        //update the admin_audition
        $audition_type=C(INNOVATOR_CODE);
        $time = date('Y-m-d');
        //$sqlString="replace into admin_audition (admin_id,audition_type,audition_object,audition_time,audition_note) values ()"
        //echo json_encode($sqlString);
        $Form->execute('insert into admin_audition (admin_id,audition_type,audition_object,audition_time,audition_note) 
            values ("%s","%d","%s","%s","%s")',
            $admin_id,$audition_type,$innovator_id,$time,$note);
        //echo "ok";
        //update the user reg_status
        $Form->execute('update entrepreneur_personal set reg_status=%d where user_id="%s"',$result,$innovator_id);


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

        $cases = $Form->query('select * from investor_case where user_id="%s" order by invest_time desc',$user_id);
        if($cases){
            $round = C('INVEST_ROUND');
            $cur = C('CURRENCY_CODE');
            foreach ($cases as $key => $value) {
                $cases[$key]['round'] = $round[$value['round']];
                $cases[$key]['invest_cur'] = $cur[$value['invest_cur']];
                $cases[$key]['assess_cur'] = $cur[$value['assess_cur']];
                $cases[$key]['invest_time'] = substr($value['invest_time'], 0,7);
                if($value['invest_amount']==0){
                    $cases[$key]['invest_amount'] = '-未知-';
                }
            }
            $this->cases = json_encode($cases);
            //dump($cases);
            //$this->assign('caselist',$cases);
        }

        //投资项目
        $pros = $Form->query('select project_investor.project_id, project_name, project_logo from project_investor 
                inner join project_info on project_investor.project_id = project_info.project_id
                where user_id="%s"', $user_id);
        if($pros){
            $this->pros = $pros;
            $this->assign('prolist',$pros);
        }
        //关注项目
        $watch = $Form->query('select object_id, project_name, project_logo from relation_follow 
            inner join project_info on project_id = object_id
            where user_id="%s" and object_type="%s" and follow_status=1',$user_id,C(PROJECT_CODE));
        if($watch){
            $this->watch = $watch;
            $this->assign('watchlist',$watch);
        }

        //工作经历
            $jobs = $Form->query('select * from user_job where user_id="%s" order by job_start',$user_id);
            if($jobs){
                foreach ($jobs as $key => $value) {
                    $jobs[$key]['job_start'] = substr($value['job_start'], 0,7);
                    $jobs[$key]['job_end'] = substr($value['job_end'], 0,7);
                }
                $this->jobs = $jobs;
                $this->assign('joblist',$jobs);
            }
            $user = $Form->query("select * from investor_personal where user_id='%s'",$user_id);
            $this->user = $user[0];

            //认证资料
            if($user[0]['user_type']==1){
                $auth = $Form->query('select * from investor_company where user_id = "%s"', $user_id);
                $this->auth = $auth[0];
                //dump($this->auth);
            }
            else if($user[0]['user_type']==2){
                $auth = $Form->query('select * from investor_fi where user_id = "%s"',$user_id);
                $this->auth = $auth[0];
            }

            $this->rounds = json_encode(C('INVEST_ROUND'));
            $this->currency = json_encode(C('CURRENCY_CODE'));
            $fields = C('INTEREST_FIELD');
            $fieldlen = count($fields);
            $this->field = json_encode($fields);
            $this->fieldlen = $fieldlen;

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