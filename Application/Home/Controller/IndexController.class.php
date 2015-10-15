<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class IndexController extends Controller {
    public function index(){
        //dump($_SESSION);
        $Form = new Model();
        $projects = $Form->query('select content_id as project_id, project_name, project_logo, project_brief from home_show 
            inner join project_info on content_id = project_id 
            where tag = 1');
        //dump($projects);
        $this->assign('proslist',$projects);
        $this->display();
    }

    public function news(){
        $Form = new Model();
        $articles = $Form->query('select content_id, article_title, article_content from home_show 
            inner join admin_articles on content_id = article_id 
            where tag = 4');
        foreach ($articles as $key => $value) {
            $pic = getPic($value['article_content'],C(EXP_PREFIX));
            $pic = C(PREFIX).substr($pic,1);
            $articles[$key]['article_content'] = $pic;
        }
        $this->assign('newslist',$articles);
        //dump($articles);

        $this->display();
    }

    public function login(){
        $this->display();
    }

    public function logout(){
        unset($_SESSION['user']);
        unset($_SESSION['id']);
        unset($_SESSION['type']);
        unset($_SESSION['portrait']);
        unset($_SESSION['msg']);
        header("Location: index"); 
    }

    public function register(){
        $fields = C('INTEREST_FIELD');
        $fieldlen = count($fields);
        $this->field = json_encode($fields);
        $this->fieldlen = $fieldlen;
        $city = C("PROVINCE_CODE");
        $this->city = json_encode($city);
        $this->display();
    }

    public function test(){
        session_start();  
        getCode(4,60,20);  
        $this->display();
    }

    public function loginCheck(){
        $Form = new Model();
        if(intval($_POST['value'])===1){
            //echo 'aaa';
            $name = encode($_POST['key1']);
            $pwd = ($_POST['key2']);
            $result = $Form->query('select user_id, name, portrait from investor_personal where mobile="%s" or email="%s"',$name,$name);
            if($result){
                $id = $result[0]['user_id'];
                $safety = $Form->query('select user_pwd from investor_security where user_id="%s"',$id);
                if($safety[0]['user_pwd']===$pwd){
                    $_SESSION['user'] = $result[0]['name'];
                    $_SESSION['id'] = $result[0]['user_id'];
                    $_SESSION['type'] = '1';
                    $_SESSION['portrait'] = strlen($result[0]['portrait'])>0?$result[0]['portrait']:C(INVESTOR_PORTRAIT);

                    $msg = $Form->query('select count(*) as sum from messagebox where ifread = 0 and to_id = "%s"',$result[0]['user_id']);
                    if($msg[0]){
                        $_SESSION['msg'] = $msg[0]['sum'];
                    }

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
        else if(intval($_POST['value'])===2){
            //echo 'bbb';
            $name = encode($_POST['key1']);
            $pwd = ($_POST['key2']);
            $result = $Form->query('select user_id,name, portrait from entrepreneur_personal where phone="%s" or email="%s"',$name,$name);
            if($result){
                $id = $result[0]['user_id'];
                $safety = $Form->query('select user_pwd from entrepreneur_security where user_id="%s"',$id);
                if($safety[0]['user_pwd']===$pwd){
                    $_SESSION['user'] = $result[0]['name'];
                    $_SESSION['id'] = $result[0]['user_id'];
                    $_SESSION['type'] = '2';
                    $_SESSION['portrait'] = strlen($result[0]['portrait'])>0?$result[0]['portrait']:C(INNOVATOR_PORTRAIT);

                    $msg = $Form->query('select count(*) as sum from messagebox where ifread = 0 and to_id = "%s"',$result[0]['user_id']);
                    if($msg[0]){
                        $_SESSION['msg'] = $msg[0]['sum'];
                    }

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
        else{
            echo 400;
        }
    	
    }

    public function userSave(){
        $Form = new Model();
        if(I('post.value')==='investor'){
            $seed = rand(C(RANDOM_USER_MIN),C(RANDOM_USER_MAX));
            $id = '1'.substr(date('Y'),2).$seed;
            $regTime = date('Y-m-d');
            $exist = $Form->query('select user_id from investor_personal where user_id = "%s"',$id);
            while($exist){
                $seed = rand(C(RANDOM_USER_MIN),C(RANDOM_USER_MAX));
                $id = '1'.substr(date('Y'),2).$seed;
                $exist = $Form->query('select user_id from investor_personal where user_id = "%s"',$id);
            }
            $result = $Form->execute('insert into investor_personal 
                (user_id,name,mobile,email,company,title,user_type,reg_time,reg_status)
                values ("%s","%s","%s","%s","%s","%s",%d,
                "%s",%d)',$id,$_POST['key1'],encode($_POST['key2']),encode($_POST['key3']),$_POST['key4'],$_POST['key5'],$_POST['key6'],$regTime,0);
            
            //感兴趣领域
            $interests = $_POST['key9'];
            $interests = explode(',', $interests);
            for($i=0;$i<count($interests)-1;$i++){
                $temp = $Form->execute('replace into interest_investor (id, interest_field) values ("%s",%d)',$id,$interests[$i]);
            }

            //认证资料
            if($_POST['key6']==1){
                $result1 = $Form->execute('insert into investor_company (user_id, company_name) values ("%s","%s")',$id,$_POST['key4']);
            }
            else if($_POST['key6']==2){
                $result1 = $Form->execute('insert into investor_fi (user_id) values ("%s")',$id);
            }
            
            if($result){
                $safety = $Form->execute('insert into investor_security (user_id,user_pwd) 
                    values ("%s","%s")',$id,$_POST['key7']);
                if($safety){
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
        else if(I('post.value')==='innovator'){
            $seed = rand(C(RANDOM_USER_MIN),C(RANDOM_USER_MAX));
            $id = '2'.substr(date('Y'),2).$seed;
            $regTime = date('Y-m-d');
            $exist = $Form->query('select user_id from entrepreneur_personal where user_id = "%s"',$id);
            while($exist){
                $seed = rand(C(RANDOM_USER_MIN),C(RANDOM_USER_MAX));
                $id = '1'.substr(date('Y'),2).$seed;
                $exist = $Form->query('select user_id from entrepreneur_personal where user_id = "%s"',$id);
            }
            $result = $Form->execute('insert into entrepreneur_personal 
                (user_id,name,email,phone,nickname,gender,birthday,city,reg_time,reg_status)
                values ("%s","%s","%s","%s","%s",%d,"%s",%d,
                "%s",%d)',$id,$_POST['key1'],encode($_POST['key2']),encode($_POST['key3']),$_POST['key4'],$_POST['key5'],$_POST['key6'],$_POST['key7'],$regTime,0);
            
            //感兴趣领域
            $interests = $_POST['key9'];
            $temp = $Form->execute('replace into interest_entrepreneur (id, interest_field) values ("%s",%d)',$id,$interests);

            if($result){
                $safety = $Form->execute('insert into entrepreneur_security (user_id,user_pwd) 
                    values ("%s","%s")',$id,$_POST['key11']);
                if($safety){
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
        else{
            echo 400;
        }
        
    }

    public function validCheck(){
        $Form = new Model();
        $result1 = $Form->query('select mobile from investor_personal where mobile = "%s"',encode(I('post.key2')));
        $result2 = $Form->query('select email from investor_personal where email = "%s"',encode(I('post.key3')));
        if(!$result1&&!$result2){
            if(check_mobile(I('post.key2'),I('post.c'))==200){
                echo 200;
                exit(); 
            }
            else{
                echo 409;
                exit();
            }
            
        }
        if($result1){
            echo 2002;
            exit();
        }
        if($result2){
            echo 2003;
            exit();
        }
    }

    public function validCheck1(){
        $Form = new Model();
        $result1 = $Form->query('select email from entrepreneur_personal where email = "%s"',encode($_POST['key2']));
        $result2 = $Form->query('select phone from entrepreneur_personal where phone = "%s"',encode($_POST['key3']));
        $result3 = $Form->query('select nickname from entrepreneur_personal where nickname = "%s"',$_POST['key4']);
        if(!$result1&&!$result2&&!$result3){
            if(check_mobile(I('post.key3'),I('post.c'))==200){
                echo 200;
                exit();
            }
            else{
                echo 409;
                exit();
            }
           
        }
        if($result1){
            echo 2002;
            exit();
        }
        if($result2){
            echo 2003;
            exit();
        }
        if($result3){
            echo 2004;
            exit();
        }
    }

    public function queryCheckCode(){
        //dump($_POST);
        if(I('post.mobile')){
            $result = send_msg(I('post.mobile'));
            echo $result;
        }
    }

    
}