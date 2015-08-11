<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class AccountController extends Controller {
    
    public function index(){
        //dump($_GET);
    	//dump($res);
                           
        $this->display();
    }
    public function findByEmail(){
        $email=$_POST['key1'];
        $usertype=$_POST['key2'];//1 innovator 2 investor
        $Form = new Model();
        $user='';
        $objectid=$usertype;//1 innovator 2 investor;
        
        //dump($_POST);
        if($usertype==1)
        {
            $exist = $Form->query('select user_id from entrepreneur_personal where email = "%s" ',$_POST['key1']);
            $user=$exist[0];
            if(!$user)
            {
                echo 404;
                return;
            }
        }else
        {
            $exist=$Form->query('select user_id from investor_personal where email = "%s" ',$_POST['key1']);
            $user=$exist[0];
            if(!$user)
            {
                echo 404;
                return ;
            }
        }

        //dump($user);
        $res=send_find_mail($user['user_id'],$objectid,$email);
        if($res==200)
            echo 200;
        else
            echo 410;
        
    } 


    public function setting (){

        $key1=$_GET['key1'];
        $key2=$_GET['key2'];
        $key3=$_GET['key3'];
        $Form = new Model();
        $r = $Form->query('select * from email_find_pwd where user_id = "%s"',$key1);
        
        //dump($r);
        if($r)
        {
            $mesg;
            $record=$r[0];
            //dump($record);
            //dump($record[mail_address]);
            if($record[mail_address]!=$key2)
            {
                $mesg= "请使用注册邮箱重置密码！";
                //dump($this->mesg);

            }else
            {
                if($record[active_code]!=$key3)
                {
                    $mesg= "激活码错误！";
                    //dump($this->mesg);
                }else
                {
                    if(intval($record[over_time])<time())
                    {
                        $mesg= "激活码过期！";
                        //dump($this->mesg);
                    }else
                    {
                        
                        $this->user_id=$key1;
                    }
                }
            }



        }else
        {
            $mesg= "重置失败！请重试重置过程。";
        }


        $this->tip=$mesg;
        //dump($this->tip);
        //$this->display();

        $this->display();
    }

    public function saveChange(){
        //dump($_POST);
        $Form = new Model();
        $user_id=$_POST['key1'];
        $newpwd=$_POST['key2'];
        
        $pwds=$Form->query('select user_pwd from investor_security where user_id="%s"',$user_id);
        $pwd=$pwds[0];
        if($pwd)
        {
            $result = $Form->execute('update investor_security set user_pwd = "%s" where user_id = "%s"',I('post.key2'),$user_id);
            if($result)
                echo 200;
            else
                echo 404;
        }else
        {
            $result = $Form->execute('update entrepreneur_security set user_pwd = "%s" where user_id = "%s"',I('post.key2'),$user_id);
            if($result)
                echo 200;
            else
                echo 404;
        }
    }
    public function sendMobileCheck(){
        $phone = $_POST["mobile"];
        $usertype=$_POST["usertype"];
        send_msg($phone);
    }
    public function checkMobile(){
        //dump($_POST);
        $mobile=$_POST['key1'];
        $code=$_POST['key2'];
        $usertype=$_POST['key3'];
        $Form=new Model();
        //first check mobile is allowed
        if($usertype==1)
        {
            $exist = $Form->query('select user_id from entrepreneur_personal where phone = "%s" ',$_POST['key1']);
            $user=$exist[0];
            if(!$user)
            {
                echo 401;
                return;
            }
        }else
        {
            $exist=$Form->query('select user_id from investor_personal where mobile = "%s" ',$_POST['key1']);
            $user=$exist[0];
            if(!$user)
            {
                echo 401;
                return ;
            }
        }


        $res=check_mobile($mobile,$code);
        if($res==200)
        {
            echo 200;
            $_SESSION['changeType']=$usertype;
            $_SESSION['mobile']=$mobile;
            $_SESSION['JCALLOW']=1;;
            $_SESSION['userId']=$user['user_id'];
        }else
        {
            echo 404;
        }
    }
    public function mobileSetting(){
        //dump($_SESSION);
        if(session('?changeType')&&session('?JCALLOW')&&session('?mobile')&&$_SESSION['JCALLOW']==1){

            //get user_id
            $this->user_id=$_SESSION['userId'];

            $this->display();
        }else
        {
             //$this->redirect('Account/index');
        }
        
    }
   
}