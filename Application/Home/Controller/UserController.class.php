<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;

class UserController extends Controller {
    public function index(){
    	//dump($_SESSION);
    	
        if(!session('?type') || !session('?id')){
            $this->redirect('Index/login');
        }

        if($_SESSION['type']==='2'){
            $Form = new Model();
            $projects = $Form->query("select project_id, project_name, project_logo, project_admin, project_brief, project_type 
                from project_info where project_admin='%s'", $_SESSION['id']);
            if($projects){
                $fields = C('INTEREST_FIELD');
                foreach ($projects as $key => $value) {
                    $projects[$key]['project_type'] = $fields[$value['project_type']];
                }
            }
            $user = $Form->query("select * from entrepreneur_personal where user_id='%s'",$_SESSION['id']);
            $user[0]['business'] = $fields[$user[0]['business']];
            $this->user = $user[0];
            //dump($this->user);
            $this->projects = $projects;
            $this->assign('prolist',$projects);
            $jobs = $Form->query('select * from user_job where user_id="%s" order by job_start',$_SESSION['id']);
            if($jobs){
                foreach ($jobs as $key => $value) {
                    $jobs[$key]['job_start'] = substr($value['job_start'], 0,7);
                    $jobs[$key]['job_end'] = substr($value['job_end'], 0,7);
                }
                $this->jobs = $jobs;
                $this->assign('joblist',$jobs);
            }
        }
        else{
            header("Location: investorEdit");
        }
    
    	$this->display();
    }

    public function innovator(){
        if(!session('?type') || !session('?id')){
            $this->redirect('Index/login');
        }
        
        $id = I('get.val',0);
        if(!$id){
            $this->redirect('Index/index');
        }

        $Form = new Model();
        $projects = $Form->query("select project_id, project_name, project_logo, project_admin, project_brief, project_type 
                from project_info where project_admin='%s'", $_SESSION['id']);
        if($projects){
            $fields = C('INTEREST_FIELD');
            foreach ($projects as $key => $value) {
                $projects[$key]['project_type'] = $fields[$value['project_type']];
            }
        }
        $user = $Form->query("select * from entrepreneur_personal where user_id='%s'",$_SESSION['id']);
        $user[0]['business'] = $fields[$user[0]['business']];
        $this->user = $user[0];
        //dump($this->user);
        $this->projects = $projects;
        $this->assign('prolist',$projects);
        $jobs = $Form->query('select * from user_job where user_id="%s" order by job_start',$_SESSION['id']);
        if($jobs){
            foreach ($jobs as $key => $value) {
                $jobs[$key]['job_start'] = substr($value['job_start'], 0,7);
                $jobs[$key]['job_end'] = substr($value['job_end'], 0,7);
            }
            $this->jobs = $jobs;
            $this->assign('joblist',$jobs);
        }
        $this->display();

    }

    public function investorEdit(){
        //dump($_SESSION);

        if(!session('?type') || !session('?id')){
            $this->redirect('Index/login');
        }

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

            $cases = $Form->query('select * from investor_case where user_id="%s" order by invest_time desc',$_SESSION['id']);
            if($cases){
                $round = C('INVEST_ROUND');
                $cur = C('CURRENCY_CODE');
                foreach ($cases as $key => $value) {
                    $cases[$key]['round'] = $round[$value['round']];
                    $cases[$key]['invest_cur'] = $cur[$value['invest_cur']];
                    $cases[$key]['assess_cur'] = $cur[$value['assess_cur']];
                    $cases[$key]['invest_time'] = substr($value['invest_time'], 0,7);
                }
                $this->cases = $cases;
                $this->assign('caselist',$cases);
            }

            $jobs = $Form->query('select * from user_job where user_id="%s" order by job_start',$_SESSION['id']);
            if($jobs){
                foreach ($jobs as $key => $value) {
                    $jobs[$key]['job_start'] = substr($value['job_start'], 0,7);
                    $jobs[$key]['job_end'] = substr($value['job_end'], 0,7);
                }
                $this->jobs = $jobs;
                $this->assign('joblist',$jobs);
            }
            $user = $Form->query("select * from investor_personal where user_id='%s'",$_SESSION['id']);
            $this->user = $user[0];
            $this->rounds = json_encode(C('INVEST_ROUND'));
            $this->currency = json_encode(C('CURRENCY_CODE'));
        }
        $this->display();
    }

    public function investor(){
        if(!session('?type') || !session('?id')){
            $this->redirect('Index/login');
        }
        
        $id = I('get.val',0);
        if(!$id){
            $this->redirect('Index/index');
        }

        $Form = new Model();
        $result = $Form->query('select interests from investor_personal where user_id="%s"',$id);
        if($result){
            $interests = explode(',',$result[0]['interests']);
            $fields = C('INTEREST_FIELD');
            foreach ($interests as $key => $value) {
                $interests[$key] = $fields[$value];
            }
            $this->interests = json_encode($interests);
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
            }
            $this->cases = $cases;
            $this->assign('caselist',$cases);
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
        $this->display();

    }

    public function profileSave(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728 ;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Public/upload/pic/profile/'; // 设置附件上传根目录
        $upload->savePath = $_SESSION['id'].'/'; // 设置附件上传（子）目录
         $upload->saveName = $_SESSION['id']."profile";
        $upload->replace = true;
        $upload->subName = '';
        // 上传文件 
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{// 上传成功
            $src = $upload->rootPath.$upload->savePath.$info['profile']['savename'];
            $image = new \Think\Image(); 
            $image->open($src);
            $thumbName = $upload->rootPath.$upload->savePath.'thumb_'.$info['profile']['savename'];
            $result = $image->thumb(100, 100,\Think\Image::IMAGE_THUMB_CENTER)->save($thumbName);
            if($result){
                $Form = new Model();
                if($_SESSION['type']=="1"){
                    $success = $Form->execute('update investor_personal set portrait="%s" 
                        where user_id="%s"','/lcb/Public/upload/pic/profile/'.$upload->savePath.'thumb_'.$info['profile']['savename'],$_SESSION['id']);
                    header("Location: investor");
                }
                else{
                    $success = $Form->execute('update entrepreneur_personal set portrait="%s" 
                        where user_id="%s"','/lcb/Public/upload/pic/profile/'.$upload->savePath.'thumb_'.$info['profile']['savename'],$_SESSION['id']);
                    header("Location: index");
                }
            }
        }
    }

    public function cardSave(){
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
            $thumbName = $upload->rootPath.$upload->savePath.'thumb_'.$info['mycard']['savename'];
            $result = $image->thumb(288, 162,\Think\Image::IMAGE_THUMB_CENTER)->save($thumbName);
            if($result){
                $Form = new Model();
                if($_SESSION['type']=="1"){
                    $success = $Form->execute('update investor_personal set mycard="%s" 
                        where user_id="%s"','/lcb/Public/upload/pic/card/'.$upload->savePath.'thumb_'.$info['mycard']['savename'],$_SESSION['id']);
                    header("Location: investor");
                    
                }
                else{
                    $success = $Form->execute('update entrepreneur_personal set mycard="%s" 
                        where user_id="%s"','/lcb/Public/upload/pic/card/'.$upload->savePath.'thumb_'.$info['mycard']['savename'],$_SESSION['id']);
                    header("Location: index");
                }
            }
        }
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

    public function jobSave(){
        //dump($_POST);
        $Form = new Model();
        $seed = rand(C(RANDOM_CASE_MIN),C(RANDOM_CASE_MAX));
        $user_id= $_SESSION['id'];
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