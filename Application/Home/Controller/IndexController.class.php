<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class IndexController extends Controller {
    public function index(){
    	$this->display();
    }

    public function login(){
        $this->display();
    }

    public function logout(){
        $_SESSION = array();
        header("Location: index"); 
    }

    public function register(){
        $fields = C('INTEREST_FIELD');
        $this->field = json_encode($fields);
        $this->display();
    }

    public function test(){
        session_start();  
        getCode(4,60,20);  
        $this->display();
    }

    public function loginCheck(){
        $Form = new Model();
        if(mysql_real_escape_string($_POST['value'])==='1'){
            $name = mysql_real_escape_string($_POST['key1']);
            $pwd = mysql_real_escape_string($_POST['key2']);
            $result = $Form->query('select user_id, name from investor_personal where mobile="%s" or email="%s"',$name,$name);
            if($result){
                $id = $result[0]['user_id'];
                $safety = $Form->query('select user_pwd from investor_security where user_id="%s"',$id);
                if($safety[0]['user_pwd']===$pwd){
                    $_SESSION['user'] = $result[0]['name'];
                    $_SESSION['id'] = $result[0]['user_id'];
                    $_SESSION['type'] = '1';
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
        else if(mysql_real_escape_string($_POST['value'])==='2'){
            $name = mysql_real_escape_string($_POST['key1']);
            $pwd = mysql_real_escape_string($_POST['key2']);
            $result = $Form->query('select user_id,name from entrepreneur_personal where phone="%s" or email="%s"',$name,$name);
            if($result){
                $id = $result[0]['user_id'];
                $safety = $Form->query('select user_pwd from entrepreneur_security where user_id="%s"',$id);
                if($safety[0]['user_pwd']===$pwd){
                    $_SESSION['user'] = $result[0]['name'];
                    $_SESSION['id'] = $result[0]['user_id'];
                    $_SESSION['type'] = '2';
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
        if(mysql_real_escape_string($_POST['value'])==='investor'){
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
                (user_id,name,mobile,email,company,title,user_type,reg_time,reg_status,interests)
                values ("%s","%s","%s","%s","%s","%s",%d,
                "%s",%d,"%s")',$id,$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$regTime,0,$_POST['key9']);
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
        else if(mysql_real_escape_string($_POST['value'])==='innovator'){
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
                (user_id,name,email,phone,nickname,gender,birthday,city,education,business,experience,reg_time,reg_status)
                values ("%s","%s","%s","%s","%s",%d,"%s","%s","%s",%d,"%s",
                "%s",%d)',$id,$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$_POST['key7'],$_POST['key8'],$_POST['key9'],$_POST['key10'],$regTime,0);
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
        $result1 = $Form->query('select mobile from investor_personal where mobile = "%s"',$_POST['key2']);
        $result2 = $Form->query('select email from investor_personal where email = "%s"',$_POST['key3']);
        if(!$result1&&!$result2){
            echo 200;
            exit();
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
        $result1 = $Form->query('select email from entrepreneur_personal where email = "%s"',$_POST['key2']);
        $result2 = $Form->query('select phone from entrepreneur_personal where phone = "%s"',$_POST['key3']);
        $result3 = $Form->query('select nickname from entrepreneur_personal where nickname = "%s"',$_POST['key4']);
        if(!$result1&&!$result2&&!$result3){
            echo 200;
            exit();
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

    
}