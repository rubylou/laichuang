<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class UserController extends Controller {
    public function index(){
    	//dump($_SESSION);
    	$Form = new Model();
    	if($_SESSION['type']==='2'){
            $projects = $Form->query("select project_id, project_name, project_logo, project_admin, project_brief, project_type 
                from project_info where project_admin='%s'", $_SESSION['id']);
            if($projects){
                $fields = C('INTEREST_FIELD');
                foreach ($projects as $key => $value) {
                    $projects[$key]['project_type'] = $fields[$value['project_type']];
                }
            }
            $this->projects = $projects;
            $this->assign('prolist',$projects);
    	}
        else{
            header("Location: investor");
        }

    	$this->display();
    }

    public function investor(){
        //dump($_SESSION);
        $Form = new Model();
        if($_SESSION['type']==='1'){
            $result = $Form->query('select interests from investor_personal where user_id="%s"', $_SESSION['id']);
            if($result){
                $interests = explode(',',$result[0]['interests']);
                $fields = C('INTEREST_FIELD');
                foreach ($interests as $key => $value) {
                    $interests[$key] = $fields[$value];
                }
                $this->interests = json_encode($interests);
            }

            $cases = $Form->query('select * from investor_case where user_id="%s"',$_SESSION['id']);
            if($cases){
                $round = C('INVEST_ROUND');
                $cur = C('CURRENCY_CODE');
                foreach ($cases as $key => $value) {
                    $cases[$key]['round'] = $round[$value['round']];
                    $cases[$key]['invest_cur'] = $cur[$value['invest_cur']];
                    $cases[$key]['assess_cur'] = $cur[$value['assess_cur']];
                }
                $this->cases = $cases;
                $this->assign('caselist',$cases);
            }
            $this->rounds = json_encode(C('INVEST_ROUND'));
            $this->currency = json_encode(C('CURRENCY_CODE'));
        }
        $this->display();
    }

    public function proEdit(){
        $fields = C('INTEREST_FIELD');
        $this->field = json_encode($fields);
        
    	$this->display();
    }

    public function proSave(){
        //dump($_POST);
        $Form = new Model();
        $seed = rand(C(RANDOM_CASE_MIN),C(RANDOM_CASE_MAX));
        $user_id= $_SESSION['id'];
        $project = $user_id.$seed;
        $exist = $Form->query('select project_id from project_info where project_id = "%s"',$project);
        while($exist){
            $seed = rand(C(RANDOM_CASE_MIN),C(RANDOM_CASE_MAX));
            $project = $user_id.$seed;
            $exist = $Form->query('select project_id from project_info where project_id = "%s"',$project);
        }
        $result = $Form->execute('insert into project_info (project_id,project_admin,project_name,project_logo,project_brief,
            project_member,project_fi,project_type,project_other,project_intro) 
        values ("%s","%s","%s","%s","%s","%s","%s",%d,"%s","%s")',$project,$user_id,$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$_POST['key7'],$_POST['key8']);
        if($result){
            echo 200;
        }
        else {
            echo 400;
        }

    }

    public function caseAdd(){
        $Form = new Model();
        $seed = rand(C(RANDOM_CASE_MIN),C(RANDOM_CASE_MAX));
        $user_id= $_SESSION['id'];
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
?>