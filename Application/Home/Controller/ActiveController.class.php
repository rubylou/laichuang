<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class ActiveController extends Controller {
    
    public function index(){
        //dump($_GET);
    	$key1=$_GET['key1'];
        $key2=$_GET['key2'];
        $key3=$_GET['key3'];
        $Form = new Model();
        $r = $Form->query('select * from email_active where user_id = "%s"',$key1);
       
        //dump($r);
        if($r)
        {
            $mesg;
            $record=$r[0];
            //dump($record);
            //dump($record[mail_address]);
            if($record[mail_address]!=$key2)
            {
                $mesg= "请使用注册邮箱激活！";
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
                        $res=$Form->execute('update email_active set active_status="1" where user_id="%s"',$key1);
                        if($res)
                        {
                            //dump($res);
                            $mesg= " 激活成功！";
                            //dump($this->mesg);
                        }
                    }
                }
            }
        }else
        {
            $mesg= "激活失败！";
        }
        $this->tip=$mesg;
        //dump($this->tip);
        $this->display();
    }

   
}