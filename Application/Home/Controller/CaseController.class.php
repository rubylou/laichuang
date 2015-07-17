<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class CaseController extends Controller {
	public function index(){
		$Form = new Model();
		$result = $Form->query('select project_id, project_name, project_logo, project_brief, project_type, name from project_info inner join entrepreneur_personal on project_admin = user_id');
		foreach ($result as $key => $value) {
			$result[$key]['project_type'] = C('INTEREST_FIELD')[$value['project_type']];
		}

		$this->vo = $result;
		$this->assign("list",$result);
		$this->display();
	}

	public function info(){
		$id = $_GET['key'];
		$Form = new Model();
		$result = $Form->query('select project_info.*, name, portrait from project_info inner join entrepreneur_personal on project_admin = user_id where project_id="%s"',$id);
		foreach ($result as $key => $value) {
			$result[$key]['project_type'] = C('INTEREST_FIELD')[$value['project_type']];
			$result[$key]['project_intro'] = htmlspecialchars_decode($value['project_intro']);
		}
		$this->info = $result[0];
		
		//成员信息
		$member = $Form->query('select project_member.*, portrait, nickname from project_member inner join entrepreneur_personal on project_member.user_id = entrepreneur_personal.user_id where project_id="%s"',$id);
		$this->$vo = $member;
		$this->assign("memberlist",$member);

		//关注信息
		$follow = $Form->query("select follow_status from relation_follow where user_id='%s' 
			and object_id='%s' and object_type=%d",$_SESSION['id'],$_GET['key'],C(PROJECT_CODE));
		if($follow[0]['follow_status']=='1'){
			$this->follow = C('FOLLOWING');
		}
		else{
			$this->follow = C('UNFOLLOW');
		}

		//融资情况
		$profi = $Form->query('select * from project_fi where project_id="%s" order by invest_time desc',$id);
        if($profi){
            $round = C('INVEST_ROUND');
            $cur = C('CURRENCY_CODE');
            foreach ($profi as $key => $value) {
                $profi[$key]['round'] = $round[$value['round']];
                $profi[$key]['invest_cur'] = $cur[$value['invest_cur']];
                $profi[$key]['assess_cur'] = $cur[$value['assess_cur']];
                $profi[$key]['invest_time'] = substr($value['invest_time'], 0,7);
            }
            $this->profi = $profi;
            $this->assign('filist',$profi);
        }

		$this->rounds = json_encode(C('INVEST_ROUND'));
        $this->currency = json_encode(C('CURRENCY_CODE'));
		
		$this->display();
	}

	public function proFollow(){
		$Form = new Model();
		$result = $Form->execute('replace into relation_follow (user_id, object_id, object_type, follow_status) 
			values ("%s","%s",%d,%d)',$_SESSION['id'],$_GET['val'],C(PROJECT_CODE),C($_POST['key']));
		if($result){
			echo 200;
		}
		else{
			echo 400;
		}
	}

	public function profiAdd(){
		if(I('get.p')){
			$Form = new Model();
	        $result = $Form->execute('insert into project_fi (project_id,round,
	            invest_cur,invest_amount,assess_cur,assess_amount,investor_name,invest_time) 
	        values ("%s",%d,%d,%d,%d,%d,"%s","%s")',I('get.p'),$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$_POST['key7'].'-'.$_POST['key8'].'-00');
	        if($result){
	            echo 200;
	        }
	        else {
	            echo 400;
	        }
		}
		else{
			echo 400;
		}
		
	}

	public function logoSave(){
		if(I('get.p')){
			$upload = new \Think\Upload();// 实例化上传类
	        $upload->maxSize = 3145728 ;// 设置附件上传大小
	        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	        $upload->rootPath = './Public/upload/pic/logo/'; // 设置附件上传根目录
	        $upload->savePath = I('get.p').'/'; // 设置附件上传（子）目录
	        $upload->saveName = I('get.p')."logo";
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
                    $success = $Form->execute('update project_info set project_logo="%s" 
                        where project_id="%s"','/lcb/Public/upload/pic/logo/'.$upload->savePath.'thumb_'.$info['profile']['savename'],I('get.p'));
                    header("Location: info/key/".I('get.p'));
	            }
	        }
		}  
    }

    public function queryUser(){
    	$Form = new Model();
    	$exist = $Form->query('select user_id, email, phone from entrepreneur_personal where email = "%s" or phone="%s"',$_POST['key1'],$_POST['key1']);
        if(!$exist){
        	echo 404; 
        }
        else{
        	$admin = $Form->query('select project_admin from project_info where project_id="%s"',$_POST['p']);
        	if($admin[0]['project_admin']===$exist[0]['user_id']){
        		echo 409;
        	}
        	else{
        		$result = $Form->execute('replace into project_member (project_id, user_id, title) 
        		values ("%s","%s","%s")',$_POST['p'],$exist[0]['user_id'],$_POST['key2']);
				if($result){
					echo 200;
				}     
        	}
        	   	
        }

    }
}
?>