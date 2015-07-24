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

            //创业项目
            $projects = $Form->query("select project_id, project_name, project_logo, project_admin, project_brief 
                from project_info where project_admin='%s'", $_SESSION['id']);
            $this->projects = $projects;
            $this->assign('prolist',$projects);


            $user = $Form->query("select * from entrepreneur_personal where user_id='%s'",$_SESSION['id']);
            $user[0]['gender'] = C('GENDER_CODE')[$user[0]['gender']];
            $user[0]['city'] = C('PROVINCE_CODE')[$user[0]['city']];

            $this->user = $user[0];
            //dump($this->user);
           
            //工作经历
            $jobs = $Form->query('select * from user_job where user_id="%s" order by job_start',$_SESSION['id']);
            if($jobs){
                foreach ($jobs as $key => $value) {
                    $jobs[$key]['job_start'] = substr($value['job_start'], 0,7);
                    $jobs[$key]['job_end'] = substr($value['job_end'], 0,7);
                }
                $this->jobs = $jobs;
                $this->assign('joblist',$jobs);
            }
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

        //创业项目
        $Form = new Model();
        $projects = $Form->query("select project_id, project_name, project_logo, project_admin, project_brief 
                from project_info where project_admin='%s'", $id);
        $this->projects = $projects;
        $this->assign('prolist',$projects);

        $user = $Form->query("select * from entrepreneur_personal where user_id='%s'",$id);
        $user[0]['gender'] = C('GENDER_CODE')[$user[0]['gender']];
        $user[0]['city'] = C('PROVINCE_CODE')[$user[0]['city']];
        $this->user = $user[0];

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

        //教育背景
        $edu = $Form->query('select * from user_edu where user_id="%s"',$_SESSION['id']);
        if($edu){
            $edu[0]['degree'] = C('DEGREE_CODE')[$edu[0]['degree']];
            $edu[0]['year'] = intval(substr($edu[0]['start'],0,4));
            $edu[0]['mon'] = intval(substr($edu[0]['start'],5,2));
            $this->edu = $edu[0];
        }

        //工作经历
        $jobs = $Form->query('select * from user_job where user_id="%s" order by job_start',$id);
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
            //感兴趣领域
            $result = $Form->query('select interest_field from interest_investor where id="%s"', $_SESSION['id']);
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

            //投资案例
            $cases = $Form->query('select * from investor_case where user_id="%s" order by invest_time desc',$_SESSION['id']);
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
                where user_id="%s"', $_SESSION['id']);
            if($pros){
                $this->pros = $pros;
                $this->assign('prolist',$pros);
            }

            //关注项目
            $watch = $Form->query('select object_id, project_name, project_logo from relation_follow 
                inner join project_info on project_id = object_id
                where user_id="%s" and object_type="%s" and follow_status=1',$_SESSION['id'],C(PROJECT_CODE));
            if($watch){
                $this->watch = $watch;
                $this->assign('watchlist',$watch);
            }

            //工作经历
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

            //认证资料
            if($user[0]['user_type']==1){
                $auth = $Form->query('select * from investor_company where user_id = "%s"', $_SESSION['id']);
                $this->auth = $auth[0];
                //dump($this->auth);
            }
            else if($user[0]['user_type']==2){
                $auth = $Form->query('select * from investor_fi where user_id = "%s"',$_SESSION['id']);
                $this->auth = $auth[0];
            }

            $this->rounds = json_encode(C('INVEST_ROUND'));
            $this->currency = json_encode(C('CURRENCY_CODE'));
            $fields = C('INTEREST_FIELD');
            $fieldlen = count($fields);
            $this->field = json_encode($fields);
            $this->fieldlen = $fieldlen;
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
        $result = $Form->query('select interest_field from interest_investor where id="%s"', $_SESSION['id']);
        if($result){
            $fields = C('INTEREST_FIELD');
            foreach ($result as $key => $value) {
                $result[$key]['interest_field'] = $fields[$value['interest_field']];
            }
            $this->interests = json_encode($result);
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
        $this->display();

    }

    public function profileSave(){
        if(session('?type') && session('?id')){
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
                $filename = explode('.', $info['profile']['savename']);
                $filename = $filename[0].'.png';

                $thumbName = $upload->rootPath.$upload->savePath.'thumb_'.$filename;
                //dump($thumbName);

                $result = $image->thumb(100, 100,\Think\Image::IMAGE_THUMB_CENTER)->save($thumbName);
                //dump($result);
                if($result){
                    $Form = new Model();
                    if($_SESSION['type']=="1"){
                        $success = $Form->execute('update investor_personal set portrait="%s" 
                            where user_id="%s"','/lcb/Public/upload/pic/profile/'.$upload->savePath.'thumb_'.$filename,$_SESSION['id']);
                        header("Location: investorEdit");
                    }
                    else{
                        $success = $Form->execute('update entrepreneur_personal set portrait="%s" 
                            where user_id="%s"','/lcb/Public/upload/pic/profile/'.$upload->savePath.'thumb_'.$filename,$_SESSION['id']);
                        header("Location: index");
                    }
                }
            }
        }
        else{
            echo 401;
        }
        
    }

    public function cardSave(){
        if(session('?type') && session('?id')){
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
                            where user_id="%s"','/lcb/Public/upload/pic/card/'.$upload->savePath.'thumb_'.$filename,$_SESSION['id']);
                        header("Location: investorEdit");
                        
                    }
                    else{
                        $success = $Form->execute('update entrepreneur_personal set mycard="%s" 
                            where user_id="%s"','/lcb/Public/upload/pic/card/'.$upload->savePath.'thumb_'.$filename,$_SESSION['id']);
                        header("Location: index");
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

        header("Location: investorEdit");

    }

    public function proEdit(){
        $fields = C('INTEREST_FIELD');
        $this->field = json_encode($fields);
    	$this->display();
    }

    public function proSave(){
        //dump($_POST);
        if(session('?type') && session('?id')){
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
                project_member,project_fi,project_require,project_intro) 
            values ("%s","%s","%s","%s","%s","%s","%s","%s","%s")',$project,$user_id,$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key7'],$_POST['key8']);
            
            $temp = $Form->execute('replace into interest_project (id, interest_field) values ("%s",%d)',$project,$_POST['key6']);

            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }
        else{
            echo 401;
        }

    }

    public function jobSave(){
        //dump($_POST);
        if(session('?type') && session('?id')){
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

    public function caseSave(){
        if(session('?type') && session('?id')){
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

    public function delCase(){
        if(session('?type') && session('?id')){
            $Form = new Model();
            $result = $Form->execute('delete from investor_case where case_id = "%s"',$_POST['c']);
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

    public function delJob(){
        if(session('?type') && session('?id')){
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

    public function editCase(){
        $Form = new Model();
        $result = $Form->query('select * from investor_case where case_id = "%s"',$_POST['c']);
        $result[0]['year'] = substr($result[0]['invest_time'], 0,4);
        $result[0]['mon'] = intval(substr($result[0]['invest_time'], 5,2));
        echo json_encode($result[0]);
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

    public function editInfo(){
        if(session('?type') && session('?id')){
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
        }

        else{
            echo 401;
        }
    }

    public function requestAuth(){
        //dump($_POST);
        $Form = new Model();
        //验证投资人审核资格
        if(session('type')==1){
            $id = I('post.val',0);
            //验证基本信息
            $basics = $Form->query('select * from investor_personal where user_id = "%s"',$id);
            $basics = $basics[0];
            if(strlen($basics['name'])==0){
                echo 4041;
                exit();
            }
            if(strlen($basics['company'])==0 || strlen($basics['title'])==0){
                echo 4042;
                exit();
            }
            if(strlen($basics['mycard'])==0){
                echo 4043;
                exit();
            }
            //验证公司信息
            if($basics['user_type']==1){
                $company = $Form->query('select * from investor_company where user_id = "%s"',$id);
                $company = $company[0];
                if(strlen($company['license'])==0){
                    echo 4044;
                    exit();
                }
                if(strlen($company['company_code'])==0){
                    echo 4045;
                    exit();
                }
                if(strlen($company['fi_statement'])==0){
                    echo 4046;
                    exit();
                }
                
            }
            //验证财产信息
            else if($basics['user_type']==2){
                $fi = $Form->query('select * from investor_fi where user_id = "%s"',$id);
                $fi = $fi[0];
                if(strlen($fi['financial_doc'])==0){
                    echo 4047;
                    exit();
                }
            }

            //验证投资案例
            $cases = $Form->query('select count(*) from investor_case where user_id ="%s"',$id);
            $cases = $cases[0];
            if(intval($cases['count(*)'])<3){
                echo 4048;
                exit();
            }

            $result = $Form->execute('update investor_personal set reg_status = 1 where user_id = "%s"',$id);
            if($result){
                echo 200;
            }
            else{
                echo 400;
            }

        }
        //验证创业者审核资格
        else if(session('type')==2){
            $id = I('post.val',0);
            //验证基本信息
            $basics = $Form->query('select * from entrepreneur_personal where user_id="%s"',$id);
            $basics = $basics[0];
            if(strlen($basics['name'])==0){
                echo 4041;
                exit();
            }
            if(strlen($basics['birthday'])==0){
                echo 4042;
                exit();
            }
            //验证教育背景
            $edu = $Form->query('select * from user_edu where user_id="%s"',$id);
            $edu = $edu[0];
            if(strlen($edu['school'])==0 || strlen($edu['degree'])==0 ||strlen($edu['start'])==0){
                echo 4043;
                exit();
            }

            $result = $Form->execute('update entrepreneur_personal set reg_status = 1 where user_id = "%s"',$id);
            if($result){
                echo 200;
            }
            else{
                echo 400;
            }
        }
        else{
            echo 400;
        }
    }
}
?>