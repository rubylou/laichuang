<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class AuditController extends Controller {
    public function index(){
    	//dump($_SESSION);
        if(session('?userid')&&session('?usertype')&&($_SESSION['usertype']==1||$_SESSION['usertype']==3)){

        $Form = new Model();
        $status= C(VERIFIED);
        $pro = $Form->query("select project_id,project_admin,project_brief,project_logo,project_name from project_info where status=".$status);
        $this->project=$pro;

        $status=C(UNDERVIRIFIED);
        $pro = $Form->query("select project_id,project_admin,project_brief,project_logo,project_name from project_info where status=".$status);
        $this->unProject=$pro;


        $status= C(VERIFIED);
        $user = $Form->query("select user_id,nickname,name,phone from entrepreneur_personal where reg_status=".$status);
        $this->innovator=$user;

        $status=C(UNDERVIRIFIED);
        $user = $Form->query("select user_id,nickname,name,phone from entrepreneur_personal where reg_status = ".$status);
        $this->unInnovator=$user;

        $status=C(UNDERVIRIFIED);
        $user = $Form->query("select user_id,name,company,mobile from investor_personal where reg_status = ".$status);
        $this->unInvestor=$user;

        $status= C(VERIFIED);
        $user = $Form->query("select user_id,name,company,mobile from investor_personal where reg_status=".$status);
        $this->investor=$user;


        $this->unreadcount=$Form->query("select COUNT(*) from messagebox where adminread='0' and msg_type <= 3")[0]['count(*)'];
        //dump($this->unreadcount);
    	$this->display();   
    }
        else{
            $this->redirect('Index/index');
        }
    }

    public function fetchProject(){
        $Form = new Model();
        $status= C(VERIFIED);
        $pro = $Form->query("select project_id,project_admin,project_brief,project_logo,project_name from project_info where status=".$status);
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
        $_SESSION['type']=2;
        $result = $Form->query('select project_info.*, name, portrait from project_info inner join entrepreneur_personal on project_admin = user_id where project_id="%s"',$id);
        foreach ($result as $key => $value) {
            $result[$key]['project_type'] = C('INTEREST_FIELD')[$value['project_type']];
            $result[$key]['project_intro'] = htmlspecialchars_decode($value['project_intro']);
        }
        
        //关注量
        $follow = $Form->query('select count(user_id) from relation_follow where object_type=%d and object_id="%s"',C(PROJECT_CODE),$id);
        $result[0]['project_watches'] = $follow[0]['count(user_id)'];
        $this->info = $result[0];
        $_SESSION['id']=$this->info['project_admin'];

        dump($this->info);
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
        $this->rounds = json_encode(C('INVEST_ROUND'));
        $this->currency = json_encode(C('CURRENCY_CODE'));
        
        //感兴趣领域
        $fields = C('INTEREST_FIELD');
        $fieldlen = count($fields);
        $this->field = json_encode($fields);
        $this->fieldlen = $fieldlen;
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
        $res=$Form->execute('update project_info set status=%d where project_id="%s"',$result,$project_id);

        if($res)
        {
            echo '200';
        }else
        {
            echo '400';
        }

    }






    public function fetchInnovator(){
        $Form = new Model();
        $status= C(VERIFIED);
        $user = $Form->query("select user_id,nickname,name,phone from entrepreneur_personal where reg_status=".$status);
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

         //所在行业
        $result = $Form->query('select interest_field from interest_entrepreneur where id="%s"', $_SESSION['id']);
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
        $fields = C('INTEREST_FIELD');
        $fieldlen = count($fields);
        $this->field = json_encode($fields);
        $this->fieldlen = $fieldlen;

        $city = C('PROVINCE_CODE');
        $this->city = json_encode($city);


         //set session to maintain current verify object
        $_SESSION['type']=2;
        $_SESSION['id']=$id;

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
        $res=$Form->execute('update entrepreneur_personal set reg_status=%d where user_id="%s"',$result,$innovator_id);
        if($res)
        {
            echo '200';
        }else
        {
            echo '400';
        }

 
    }


    




    public function fetchInvestorPersonUnderVerified(){
        $Form = new Model();
        $status=C(UNDERVIRIFIED);
        $user = $Form->query("select user_id,name,company,mobile from investor_personal where reg_status = ".$status);
        echo json_encode($user);
    }
    public function fetchInvestor(){
        $Form = new Model();
        $status= C(VERIFIED);
        $user = $Form->query("select user_id,name,company,mobile from investor_personal where reg_status=".$status);
        echo json_encode($user);
    }
    public function auditInvestorPsVerify(){
        $id=$_GET['key'];
        $_SESSION['id']=$id;
        $_SESSION['type']=1;
        //get all need information 
        //dump($user_id);
        $Form = new Model();
        $result = $Form->query('select interest_field from interest_investor where id="%s"', $id);
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

        $cases = $Form->query('select * from investor_case where user_id="%s" order by invest_time desc',$id);
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
            $this->cases = $cases;
            $this->assign('caselist',$cases);
        }

        //投资项目
        $pros = $Form->query('select project_investor.project_id, project_name, project_logo from project_investor 
            inner join project_info on project_investor.project_id = project_info.project_id
            where user_id="%s"', $id);
        if($pros){
            $this->pros = $pros;
            $this->assign('prolist',$pros);
        }

        //关注项目
        $watch = $Form->query('select object_id, project_name, project_logo from relation_follow 
            inner join project_info on project_id = object_id
            where user_id="%s" and object_type="%s" and follow_status=1',$id,C(PROJECT_CODE));
        if($watch){
            $this->watch = $watch;
            $this->assign('watchlist',$watch);
        }

        $jobs = $Form->query('select * from user_job where user_id="%s" order by job_start',$id);
        if($jobs){
            foreach ($jobs as $key => $value) {
                $jobs[$key]['job_start'] = substr($value['job_start'], 0,7);
                $jobs[$key]['job_end'] = substr($value['job_end'], 0,7);
            }
            $this->jobs = $jobs;
            $this->assign('joblist',$jobs);
        }
        $user = $Form->query("select * from investor_personal where user_id='%s'",$id);
        $this->user = $user[0];



        //认证资料
        if($user[0]['user_type']==1){
                $auth = $Form->query('select * from investor_company where user_id = "%s"', $id);
                $this->auth = $auth[0];
                //dump($this->auth);
            }
            else if($user[0]['user_type']==2){
                $auth = $Form->query('select * from investor_fi where user_id = "%s"',$id);
                $this->auth = $auth[0];
        }

        $this->rounds = json_encode(C('INVEST_ROUND'));
        $this->currency = json_encode(C('CURRENCY_CODE'));
        $fields = C('INTEREST_FIELD');
        $fieldlen = count($fields);
        $this->field = json_encode($fields);
        $this->fieldlen = $fieldlen;
        $this->userid=$id;
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
        $res=$Form->execute('update investor_personal set reg_status=%d where user_id="%s"',$result,$user_id);
        if($res)
        {
            echo '200';
        }else
        {
            echo '400';
        }

    }
     

     public function delJob(){
        if(session('?usertype') && session('?userid')){
            $Form = new Model();
            $result = $Form->execute('delete from user_job where job_id = "%s"',$_POST['c']);
            if($result){
                echo 200;
            }
            else{
                echo 400;
            }
        }
        else{
            echo 401;
        }
    }

    public function editJob(){
        $Form = new Model();
        $result = $Form->query('select * from user_job where job_id = "%s"',$_POST['c']);
        $result[0]['startyear'] = substr($result[0]['job_start'], 0,4);
        $result[0]['startmon'] = intval(substr($result[0]['job_strart'], 5,2));
        $result[0]['endyear'] = substr($result[0]['job_end'], 0,4);
        $result[0]['endmon'] = intval(substr($result[0]['job_end'], 5,2));
        echo json_encode($result[0]);
    }
     public function jobSave(){
        //dump($_POST);
        if(session('?usertype') && session('?userid')){
            $Form = new Model();
            $seed = rand(C(RANDOM_CASE_MIN),C(RANDOM_CASE_MAX));
            $user_id= $_SESSION['id'];
            if(count($_POST['c'])>0){
                $job = $_POST['c'];
                if($_POST['key8']==='true'){
                    $result = $Form->execute('update user_job set job_title="%s",job_company="%s",job_start="%s",job_end="%s",
                        job_info="%s" where job_id="%s"',$_POST['key1'],$_POST['key2'],$_POST['key3'].'-'.$_POST['key4'].'-00',date('Y-m-d'),$_POST['key7'],$job);
                }
                else{
                    $result = $Form->execute('update user_job set job_title="%s",job_company="%s",job_start="%s",job_end="%s",
                        job_info="%s" where job_id="%s"',$_POST['key1'],$_POST['key2'],$_POST['key3'].'-'.$_POST['key4'].'-00',$_POST['key5'].'-'.$_POST['key6'].'-00',$_POST['key7'],$job);
                }
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }
            else{
                $job = $user_id.$seed;
                $exist = $Form->query('select job_id from user_job where job_id = "%s"',$job);
                while($exist){
                    $seed = rand(C(RANDOM_CASE_MIN),C(RANDOM_CASE_MAX));
                    $job = $user_id.$seed;
                    $exist = $Form->query('select job_id from user_job where job_id = "%s"',$job);
                }
                if($_POST['key8']==='true'){
                    $result = $Form->execute('insert into user_job (job_id,user_id,job_title,job_company,job_start,job_end,job_info) 
                values ("%s","%s","%s","%s","%s","%s","%s")',$job,$user_id,$_POST['key1'],$_POST['key2'],$_POST['key3'].'-'.$_POST['key4'].'-00',date('Y-m-d'),$_POST['key7']);
                }
                else{
                    $result = $Form->execute('insert into user_job (job_id,user_id,job_title,job_company,job_start,job_end,job_info) 
                values ("%s","%s","%s","%s","%s","%s","%s")',$job,$user_id,$_POST['key1'],$_POST['key2'],$_POST['key3'].'-'.$_POST['key4'].'-00',$_POST['key5'].'-'.$_POST['key6'].'-00',$_POST['key7']);
                }
                
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }
        }
        else{
            echo 401;
        }
        
    }

    public function editInfo(){
        dump($_POST);
        dump($_SESSION);
        if(session('?usertype') && session('?userid')){
            $Form = new Model();
            if(count($_POST['name'])>0 && $_SESSION['type']==1){
                $result = $Form->execute('update investor_personal set name="%s" where user_id="%s"',$_POST['name'],$_SESSION['id']);
                if($result){
                    echo 200;
                    $_SESSION['user'] = $_POST['name'];
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['brief'])>0 && $_SESSION['type']==1){
                $result = $Form->execute('update investor_personal set brief="%s" where user_id="%s"',$_POST['brief'],$_SESSION['id']);
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['sns'])>0 && $_SESSION['type']==1){
                $result = $Form->execute('update investor_personal set sns_id="%s" where user_id="%s"',$_POST['sns'],$_SESSION['id']);
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['field'])>0 && $_SESSION['type']==1){
                $interests = $_POST['field'];
                $interests = explode(',', $interests);

                $result = $Form->execute('delete from interest_investor where id="%s"',$_SESSION['id']);

                for($i=0;$i<count($interests)-1;$i++){
                    $temp = $Form->execute('replace into interest_investor (id, interest_field) values ("%s",%d)',$_SESSION['id'],$interests[$i]);
                }
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['company'])>0 && $_SESSION['type']==1){
                $result = $Form->execute('update investor_personal set company="%s" where user_id="%s"',$_POST['company'],$_SESSION['id']);
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['title'])>0 && $_SESSION['type']==1){
                $result = $Form->execute('update investor_personal set title="%s" where user_id="%s"',$_POST['title'],$_SESSION['id']);
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['name'])>0 && $_SESSION['type']==2){
                $result = $Form->execute('update entrepreneur_personal set name="%s" where user_id="%s"',$_POST['name'],$_SESSION['id']);
                if($result){
                    echo 200;
                    $_SESSION['user'] = $_POST['name'];
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['brief'])>0 && $_SESSION['type']==2){
                $result = $Form->execute('update entrepreneur_personal set brief="%s" where user_id="%s"',$_POST['brief'],$_SESSION['id']);
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['field'])>0 && $_SESSION['type']==2){
                $interests = $_POST['field'];
                $interests = explode(',', $interests);

                $result = $Form->execute('delete from interest_entrepreneur where id="%s"',$_SESSION['id']);

                for($i=0;$i<count($interests)-1;$i++){
                    $temp = $Form->execute('replace into interest_entrepreneur (id, interest_field) values ("%s",%d)',$_SESSION['id'],$interests[$i]);
                }
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['sns'])>0 && $_SESSION['type']==2){
                $result = $Form->execute('update entrepreneur_personal set sns_id="%s" where user_id="%s"',$_POST['sns'],$_SESSION['id']);
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['gender'])>0 && $_SESSION['type']==2){
                $result = $Form->execute('update entrepreneur_personal set gender="%s" where user_id="%s"',$_POST['gender'],$_SESSION['id']);
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['birth'])>0 && $_SESSION['type']==2){
                $result = $Form->execute('update entrepreneur_personal set birthday="%s" where user_id="%s"',$_POST['birth'],$_SESSION['id']);
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }

            if(count($_POST['city'])>0 && $_SESSION['type']==2){
                $result = $Form->execute('update entrepreneur_personal set city="%s" where user_id="%s"',$_POST['city'],$_SESSION['id']);
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }



            //insert admin record
            $audition_type;
            if(_SESSION['type']==1)
                $audition_type=C[INVESTOR_CODE];
            else if(_SESSION['type']==2)
                $audition_type=C[INNOVATOR_CODE];
            else
                $audition_type=C[PROJECT_CODE];
            //$res=insertAdminRecord($_SESSION['userid'],$audition_type,$_SESSION['id'],"编辑用户个人信息");
           // echo "jiguo".$res;
        }

        else{
            echo 401;
        }
    }
    

    public function editCaseInfo(){
        $Form = new Model();
        if(count($_POST['name'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_name="%s" where project_admin="%s" and project_id="%s"',$_POST['name'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['brief'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_brief="%s" where project_admin="%s" and project_id="%s"',$_POST['brief'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['member'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_member="%s" where project_admin="%s" and project_id="%s"',$_POST['member'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['fi'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_fi="%s" where project_admin="%s" and project_id="%s"',$_POST['fi'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['recruit'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_recruit="%s" where project_admin="%s" and project_id="%s"',$_POST['recruit'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['require'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_require="%s" where project_admin="%s" and project_id="%s"',$_POST['require'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['field'])>0 && $_SESSION['type']==2){
            $interests = $_POST['field'];
            $interests = explode(',', $interests);

            $result = $Form->execute('delete from interest_project where id="%s"',$_GET['p']);

            for($i=0;$i<count($interests)-1;$i++){
                $temp = $Form->execute('replace into interest_project (id, interest_field) values ("%s",%d)',$_GET['p'],$interests[$i]);
            }
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }
    }

    public function editEdu(){
        //dump($_POST);
        $Form = new Model();
        $result = $Form->execute('replace into user_edu (user_id, school, degree, start) 
        values ("%s","%s",%d,"%s")',$_SESSION['id'],$_POST['key1'],$_POST['key2'],$_POST['key3'].'-'.$_POST['key4'].'-00');
        if($result){
            echo 200;
        }
        else{
            echo 400;
        }
    }
    public function editCase(){
        $Form = new Model();
        $result = $Form->query('select * from investor_case where case_id = "%s"',$_POST['c']);
        $result[0]['year'] = substr($result[0]['invest_time'], 0,4);
        $result[0]['mon'] = intval(substr($result[0]['invest_time'], 5,2));
        echo json_encode($result[0]);
    }
    public function caseSave(){
        if(session('?usertype') && session('?userid')){
            $Form = new Model();
            $seed = rand(C(RANDOM_CASE_MIN),C(RANDOM_CASE_MAX));
            $user_id= $_SESSION['id'];
            if(count($_POST['c'])>0){
                $caseid = $_POST['c'];
                $result = $Form->execute('update investor_case set company = "%s",round = %d,
                    invest_cur=%d,invest_amount=%d,assess_cur=%d,assess_amount=%d,investor_name="%s",invest_time="%s" 
                    where case_id = "%s"',$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$_POST['key7'],$_POST['key8'].'-'.$_POST['key9'].'-00',$caseid);
                if($result){
                    echo 200;
                }
                else{
                    echo 400;
                }
            }
            else{
                $caseid = $user_id.$seed;
                $exist = $Form->query('select case_id from investor_case where case_id = "%s"',$caseid);
                while($exist){
                    $seed = rand(C(RANDOM_CASE_MIN),C(RANDOM_CASE_MAX));
                    $caseid = $user_id.$seed;
                    $exist = $Form->query('select case_id from investor_case where case_id = "%s"',$caseid);
                }
                $result = $Form->execute('insert into investor_case (case_id,user_id,company,round,
                    invest_cur,invest_amount,assess_cur,assess_amount,investor_name,invest_time) 
                values ("%s","%s","%s",%d,%d,%d,%d,%d,"%s","%s")',$caseid,$user_id,$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$_POST['key7'],$_POST['key8'].'-'.$_POST['key9'].'-00');
                if($result){
                    echo 200;
                }
                else {
                    echo 400;
                }
            }
        }
        else{
            echo 401;
        }
        
        
    }
    public function cardSave(){
        if(session('?usertype') && session('?userid')){
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/upload/pic/card/'; // 设置附件上传根目录
            $upload->savePath = $_SESSION['id'].'/'; // 设置附件上传（子）目录
            $upload->saveName = $_SESSION['id']."card";
            $upload->replace = true;
            $upload->subName = '';
            // 上传文件 
            $info = $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                $src = $upload->rootPath.$upload->savePath.$info['mycard']['savename'];
                $image = new \Think\Image(); 
                $image->open($src);
                $filename = explode('.', $info['mycard']['savename']);
                $filename = $filename[0].'.png';
                $thumbName = $upload->rootPath.$upload->savePath.'thumb_'.$filename;
                $result = $image->thumb(288, 162,\Think\Image::IMAGE_THUMB_CENTER)->save($thumbName);
                if($result){
                    $Form = new Model();
                    if($_SESSION['type']=="1"){
                        $success = $Form->execute('update investor_personal set mycard="%s" 
                            where user_id="%s"',C(UPLOAD).'pic/card/'.$upload->savePath.'thumb_'.$filename,$_SESSION['id']);
                        header("Location: auditInvestorPsVerify?key=".$_SESSION['id']);
                        
                    }
                    else{
                        $success = $Form->execute('update entrepreneur_personal set mycard="%s" 
                            where user_id="%s"',C(UPLOAD).'pic/card/'.$upload->savePath.'thumb_'.$filename,$_SESSION['id']);
                        header("Location: auditInnovatorVerify?key=".$_SESSION['id']);
                    }
                }
            }
        }
        else{
            echo 401;
        }
    }
    public function authSave(){
        $Form = new Model();

        $fi_type = $_POST['fiType'];
        $fi_info = $_POST['pro_require'];
        $company_type = $_POST['companyType'];

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->savePath = $_SESSION['id'].'/'; // 设置附件上传（子）目录
        $upload->rootPath = './Public/upload/authorization/'; // 设置附件上传根目录
        $upload->replace = true;
        $upload->subName = '';

        $Form->execute('update investor_company set company_type = %d where user_id="%s"',$company_type,$_SESSION['id']);

        if(strlen($_FILES['license']['name'])>0){
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg','pdf');// 设置附件上传类型
            $upload->saveName = $_SESSION['id']."_license";
            // 上传文件 
            $info = $upload->uploadOne($_FILES['license']);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                //dump($info);
                $result = $Form->execute('update investor_company set license = "%s" 
                    where user_id = "%s"',C(UPLOAD).'authorization/'.$upload->savePath.$info['savename'],$_SESSION['id']);
            }
        }

        if(strlen($_FILES['companyCode']['name'])>0){
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg','pdf');// 设置附件上传类型
            $upload->saveName = $_SESSION['id']."_companyCode";
            // 上传文件 
            $info = $upload->uploadOne($_FILES['companyCode']);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                $result = $Form->execute('update investor_company set company_code = "%s" 
                    where user_id = "%s"',C(UPLOAD).'authorization/'.$upload->savePath.$info['savename'],$_SESSION['id']);
            }
        }

        if(strlen($_FILES['statement']['name'])>0){
            $upload->exts = array('pdf');// 设置附件上传类型
            $upload->saveName = $_SESSION['id']."_statement";
            // 上传文件 
            $info = $upload->uploadOne($_FILES['statement']);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                $result = $Form->execute('update investor_company set fi_statement = "%s" 
                    where user_id = "%s"',C(UPLOAD).'authorization/'.$upload->savePath.$info['savename'],$_SESSION['id']);
            }
        }
        
        if(strlen($_FILES['finance']['name'])>0){
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg','pdf');// 设置附件上传类型
            $upload->saveName = $_SESSION['id']."_finance";
            // 上传文件 
            $info = $upload->uploadOne($_FILES['finance']);
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                $result = $Form->execute('update investor_fi set financial_doc = "%s", financial_type=%d, financial_info = "%s" 
                    where user_id = "%s"',C(UPLOAD).'authorization/'.$upload->savePath.$info['savename'],$fi_type,$fi_info,$_SESSION['id']);
            }
        } 

        header("Location: auditInvestorPsVerify?key=".$_SESSION['id']);

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