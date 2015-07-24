<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class IndexController extends Controller {
    public function index(){
        $Form = new Model();
        $result = $Form->query('select * from admin_articles limit 3');
        foreach ($result as $key => $value) {
            $result[$key]['article_field'] = C('INTEREST_FIELD')[$value['article_field']];
            $result[$key]['article_type'] = C('MODULE_CODE')[$value['article_type']];
            $result[$key]['article_content'] = preg_replace("/\n/", "", $result[$key]['article_content']);
            $result[$key]['article_content'] = htmlspecialchars_decode($result[$key]['article_content']);
            $result[$key]['info'] = getPic($result[$key]['article_content']);
            $origin = $result[$key]['info'];
            if($origin!=null){
                $thumb=substr($origin,0,strlen($origin)-4).'thumb.jpg';
                //ThinkImage类方法
                $image = new \Think\Image(); 
                $image->open($origin);
                $unlink = $image->thumb(240,135,\Think\Image::IMAGE_THUMB_CENTER)->save($thumb);

                if($unlink !== false){
                    $result[$key]['thumb'] = '/lcb'.substr($thumb,1);
                }
                else{
                    $result[$key]['thumb'] = '';
                }

            }else{
                $result[$key]['thumb'] = '';
            }
        }
        //dump($result);
        $this->vo = $result;
        $this->assign("list",$result);
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
        if(mysql_real_escape_string($_POST['value'])==='1'){
            $name = mysql_real_escape_string($_POST['key1']);
            $pwd = mysql_real_escape_string($_POST['key2']);
            $result = $Form->query('select user_id, name, portrait from investor_personal where mobile="%s" or email="%s"',$name,$name);
            if($result){
                $id = $result[0]['user_id'];
                $safety = $Form->query('select user_pwd from investor_security where user_id="%s"',$id);
                if($safety[0]['user_pwd']===$pwd){
                    $_SESSION['user'] = $result[0]['name'];
                    $_SESSION['id'] = $result[0]['user_id'];
                    $_SESSION['type'] = '1';
                    $_SESSION['portrait'] = $result[0]['portrait'];
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
            $result = $Form->query('select user_id,name, portrait from entrepreneur_personal where phone="%s" or email="%s"',$name,$name);
            if($result){
                $id = $result[0]['user_id'];
                $safety = $Form->query('select user_pwd from entrepreneur_security where user_id="%s"',$id);
                if($safety[0]['user_pwd']===$pwd){
                    $_SESSION['user'] = $result[0]['name'];
                    $_SESSION['id'] = $result[0]['user_id'];
                    $_SESSION['type'] = '2';
                    $_SESSION['portrait'] = $result[0]['portrait'];
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
                (user_id,name,mobile,email,company,title,user_type,reg_time,reg_status)
                values ("%s","%s","%s","%s","%s","%s",%d,
                "%s",%d)',$id,$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$regTime,0);
            
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
                (user_id,name,email,phone,nickname,gender,birthday,city,reg_time,reg_status)
                values ("%s","%s","%s","%s","%s",%d,"%s",%d,
                "%s",%d)',$id,$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$_POST['key7'],$regTime,0);
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