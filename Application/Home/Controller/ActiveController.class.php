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
            $record=$r[0];
            //dump($record);
            //dump($record[mail_address]);
            if($record[mail_address]!=$key2)
            {
                echo "请使用注册邮箱激活！";

            }else
            {
                if($record[active_code]!=$key3)
                {
                    echo "激活码错误！";
                }else
                {
                    if(intval($record[over_time])<time())
                    {
                        echo "激活码过期！";
                    }else
                    {
                        $res=$Form->execute('update email_active set active_status="1" where user_id="%s"',$key1);
                        if($res)
                        {
                            echo " 激活成功！";
                        }
                    }
                }
            }
        }else
        {
            echo "激活失败！";
        }
    }
   
}