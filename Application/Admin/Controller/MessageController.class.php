<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Model;
class MessageController extends Controller {
    
    public function index(){
        //$a=send_active_mail("1154066164",2,C(TESTMAIL));
        //dump(sha1('supersuper'));
        $Form = new Model();
        $this->iwantyou=$Form->query("select * from messagebox where msg_type='2' order by sent_time desc");
        $this->ineedyou=$Form->query("select * from messagebox where msg_type='1' order by sent_time desc");
        $this->ijoinyou=$Form->query("select * from messagebox where msg_type='3' order by sent_time desc");


        $this->display();
    }
    public function messageDetail(){
        $id=$_GET['key'];
        $Form = new Model();
        $Form->execute("update messagebox set adminread=1 where id='%s'",$id);
        $msg= $Form->query("select * from messagebox where id = '%s'",$id);
        if($msg)
        {
            $this->msg=$msg[0];
            $type=$msg[0]['msg_type'];
            if($type=='1')
            {
                $innovator=$Form->query('select user_id,name,email,phone from entrepreneur_personal where user_id="%s"',
                    $msg[0]['from_id']);
                $this->from=$innovator[0];
                $this->from_page=U('Home/User/innovator/val/'.$msg[0]['from_id']);

                $investor=$Form->query('select user_id,name,email,mobile as phone from investor_personal where user_id="%s"',$msg[0]['to_id']);
                $this->to_page=U('Home/User/investor/val/'.$msg[0]['to_id']);
                $this->to=$investor[0];

            }else if($type=='2')
            {
                $innovator=$Form->query('select user_id,name,email,phone from entrepreneur_personal where user_id="%s"',
                    $msg[0]['to_id']);
                $this->to=$innovator[0];
                $this->to_page=U('Home/User/innovator/val/'.$msg[0]['to_id']);

                $investor=$Form->query('select user_id,name,email,mobile as phone from investor_personal where user_id="%s"',$msg[0]['from_id']);
                $this->from_page=U('Home/User/investor/val/'.$msg[0]['from_id']);
                $this->from=$investor[0];
            }else
            {
                $innovator=$Form->query('select user_id,name,email,phone from entrepreneur_personal where user_id="%s"',
                    $msg[0]['from_id']);
                $this->from=$innovator[0];
                $this->from_page=U('Home/User/innovator/val/'.$msg[0]['from_id']);

                $innovator=$Form->query('select user_id,name,email,phone from entrepreneur_personal where user_id="%s"',
                    $msg[0]['to_id']);
                $this->to=$innovator[0];
                $this->to_page=U('Home/User/innovator/val/'.$msg[0]['to_id']);
            }
        }
        $this->display();
    }
}