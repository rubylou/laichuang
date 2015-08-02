<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class CaseController extends Controller {
	public function index(){
		$Form = new Model();
		$result = $Form->query('select project_id, project_name, project_logo, project_brief, name, portrait from project_info inner join entrepreneur_personal on project_admin = user_id');
		
		$this->vo = $result;
		$this->assign("list",$result);
		$this->display();
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
            $result = $Form->execute('insert into project_info (project_id,project_admin,project_name,project_brief,
                project_member,project_fi,project_recruit,project_require,project_intro) 
            values ("%s","%s","%s","%s","%s","%s","%s","%s","%s")',$project,$user_id,$_POST['key1'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key7'],$_POST['key8'],$_POST['key9']);
            
            $temp = $Form->execute('replace into interest_project (id, interest_field) values ("%s",%d)',$project,$_POST['key6']);

            if($result){
                echo $project;
            }
            else {
                echo 400;
            }
        }
        else{
            echo 401;
        }

    }

	public function infoEdit(){
		$id = $_GET['key'];
		$Form = new Model();
		$result = $Form->query('select project_info.*, name, portrait from project_info 
            inner join entrepreneur_personal on project_admin = user_id where project_id="%s"',$id);
		foreach ($result as $key => $value) {
			$result[$key]['project_type'] = C('INTEREST_FIELD')[$value['project_type']];
			$result[$key]['project_intro'] = htmlspecialchars_decode($value['project_intro']);
		}

        //关注信息
        $follow = $Form->query('select count(user_id) from relation_follow where object_type=%d and object_id="%s"',C(PROJECT_CODE),$id);
		$result[0]['project_watches'] = $follow[0]['count(user_id)'];
        $result[0]['status'] = C('AUTH_STATUS')[$result[0]['status']];
        if($result[0]['project_admin']!== $_SESSION['id']){
            $this->redirect('Home/Case/info/key/'.$id);
        }

        $this->info = $result[0];

		//所属领域
		$result = $Form->query('select interest_field from interest_project where id="%s"',$id);
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
		
		//成员信息
		$member = $Form->query('select project_member.*, portrait, nickname from project_member inner join entrepreneur_personal on project_member.user_id = entrepreneur_personal.user_id where project_id="%s"',$id);
		$this->$vo = $member;
		$this->assign("memberlist",$member);

		//投资人信息
		$investor = $Form->query('select project_investor.*, portrait, name from project_investor inner join investor_personal on project_investor.user_id = investor_personal.user_id where project_id="%s"',$id);
		$this->$it = $investor;
		$this->assign("investorlist",$investor);

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
                if($value['invest_amount']==0){
                    $profi[$key]['invest_amount'] = '-未知-';
                }
            }
            $this->profi = $profi;
            $this->assign('filist',$profi);
        }

		$this->rounds = json_encode(C('INVEST_ROUND'));
        $this->currency = json_encode(C('CURRENCY_CODE'));
		
        //感兴趣领域
		$fields = C('INTEREST_FIELD');
        $fieldlen = count($fields);
        $this->field = json_encode($fields);
        $this->fieldlen = $fieldlen;

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

        //关注量
        $follow = $Form->query('select count(user_id) from relation_follow where object_type=%d and object_id="%s"',C(PROJECT_CODE),$id);
        $result[0]['project_watches'] = $follow[0]['count(user_id)'];
        $this->info = $result[0];

        //所属领域
        $result = $Form->query('select interest_field from interest_project where id="%s"',$id);
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
        
        //成员信息
        $member = $Form->query('select project_member.*, portrait, nickname from project_member inner join entrepreneur_personal on project_member.user_id = entrepreneur_personal.user_id where project_id="%s"',$id);
        $this->$vo = $member;
        $this->assign("memberlist",$member);

        //投资人信息
        $investor = $Form->query('select project_investor.*, portrait, name from project_investor inner join investor_personal on project_investor.user_id = investor_personal.user_id where project_id="%s"',$id);
        $this->$it = $investor;
        $this->assign("investorlist",$investor);

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
                if($value['invest_amount']==0){
                    $profi[$key]['invest_amount'] = '-未知-';
                }
            }
            $this->profi = $profi;
            $this->assign('filist',$profi);
        }

        //类似项目
        $familiar = $Form->query('select distinct familiar.id as project_id,project_name, project_logo from interest_project as original 
            inner join interest_project as familiar on original.interest_field = familiar.interest_field 
            inner join project_info on familiar.id = project_id
            where original.id = "%s" and familiar.id <> "%s" limit 5',$id, $id);
        $this->familiar = $familiar;
        $this->assign('prolist',$familiar);   

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
		if(I('post.p')){
			$Form = new Model();
			if(count($_POST['c'])>0){
	            $id = $_POST['c'];
	            $result = $Form->execute('update project_fi set round = %d,
	                invest_cur=%d,invest_amount=%d,assess_cur=%d,assess_amount=%d,investor_name="%s",invest_time="%s" 
	                where project_id = "%s" and id=%d',$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$_POST['key7'].'-'.$_POST['key8'].'-'.'-00',I('post.p'),$_POST['c']);
	            if($result){
	                echo 200;
	            }
	            else{
	                echo 400;
	            }
	        }
	        else{
	        	$result = $Form->execute('insert into project_fi (project_id,round,
		            invest_cur,invest_amount,assess_cur,assess_amount,investor_name,invest_time) 
		        values ("%s",%d,%d,%d,%d,%d,"%s","%s")',I('post.p'),$_POST['key1'],$_POST['key2'],$_POST['key3'],$_POST['key4'],$_POST['key5'],$_POST['key6'],$_POST['key7'].'-'.$_POST['key8'].'-00');
		        if($result){
		            echo 200;
		        }
		        else {
		            echo 400;
		        }
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
                $filename = explode('.', $info['profile']['savename']);
                $filename = $filename[0].'.png';
	            $thumbName = $upload->rootPath.$upload->savePath.'thumb_'.$filename;
	            $result = $image->thumb(100, 100,\Think\Image::IMAGE_THUMB_CENTER)->save($thumbName);
	            if($result){
	                $Form = new Model();
                    $success = $Form->execute('update project_info set project_logo="%s" 
                        where project_id="%s"',C(UPLOAD).'pic/logo/'.$upload->savePath.'thumb_'.$filename,I('get.p'));
                    header("Location: infoEdit/key/".I('get.p'));
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

    public function queryInvestor(){
    	$Form = new Model();
    	$exist = $Form->query('select user_id, email, mobile from investor_personal where email = "%s" or mobile="%s"',$_POST['key1'],$_POST['key1']);
        if(!$exist){
        	echo 404; 
        }
        else{
    		$result = $Form->execute('replace into project_investor (project_id, user_id) 
    		values ("%s","%s")',$_POST['p'],$exist[0]['user_id']);
			if($result){
				echo 200;
			}     
        	   	
        }

    }

    public function introEdit(){
    	$id = $_GET['p'];
    	$Form = new Model();
    	$result = $Form->query('select project_intro from project_info where project_id = "%s"',$id);
    	$result[0]['project_intro'] = htmlspecialchars_decode($result[0]['project_intro']);
    	$this->content = $result[0]['project_intro'];
    	$this->display();
    }

    public function saveIntro(){
    	$Form = new Model();
    	if(count($_POST['c'])>0 && count($_POST['p'])>0){
    		$result = $Form->execute('update project_info set project_intro="%s" where project_id="%s"',$_POST['c'],$_POST['p']);
    		if($result){
    			echo 200;
    		}
    		else{
    			echo 400;
    		}
    	}
    }

    public function editInfo(){
    	$Form = new Model();
    	if(count($_POST['name'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_name="%s" where project_admin="%s" and project_id="%s"',$_POST['name'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['brief'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_brief="%s" where project_admin="%s" and project_id="%s"',$_POST['brief'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['member'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_member="%s" where project_admin="%s" and project_id="%s"',$_POST['member'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['fi'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_fi="%s" where project_admin="%s" and project_id="%s"',$_POST['fi'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['recruit'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_recruit="%s" where project_admin="%s" and project_id="%s"',$_POST['recruit'],$_SESSION['id'],$_GET['p']);
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }

        if(count($_POST['require'])>0 && $_SESSION['type']==2){
            $result = $Form->execute('update project_info set project_require="%s" where project_admin="%s" and project_id="%s"',$_POST['require'],$_SESSION['id'],$_GET['p']);
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

            $result = $Form->execute('delete from interest_project where id="%s"',$_GET['p']);

            for($i=0;$i<count($interests)-1;$i++){
                $temp = $Form->execute('replace into interest_project (id, interest_field) values ("%s",%d)',$_GET['p'],$interests[$i]);
            }
            if($result){
                echo 200;
            }
            else {
                echo 400;
            }
        }
    }

    public function delMember(){
    	//dump($_POST);
    	$Form = new Model();
    	$result = $Form->execute('delete from project_member where project_id="%s" and user_id="%s"',$_POST['p'],$_POST['m']);
    	if($result){
    		echo 200;
    	}
    	else{
    		echo 400;
    	}
    }

    public function delInvestor(){
    	//dump($_POST);
    	$Form = new Model();
    	$result = $Form->execute('delete from project_investor where project_id="%s" and user_id="%s"',$_POST['p'],$_POST['key']);
    	if($result){
    		echo 200;
    	}
    	else{
    		echo 400;
    	}
    }

    public function editProFi(){
    	$Form = new Model();
        $result = $Form->query('select * from project_fi where id = "%s" and project_id="%s"',$_POST['c'],$_GET['p']);
        $result[0]['year'] = substr($result[0]['invest_time'], 0,4);
        $result[0]['mon'] = intval(substr($result[0]['invest_time'], 5,2));
        echo json_encode($result[0]);
    }

    public function delFi(){
    	$Form = new Model();
    	$result = $Form->execute('delete from project_fi where project_id="%s" and id=%d',$_GET['p'],$_POST['c']);
    	if($result){
    		echo 200;
    	}
    	else{
    		echo 400;
    	}
    }

    public function requestAuth(){
        //dump($_POST);
        $Form = new Model();
        $id = I('post.p',0);
        $result = $Form->execute('update project_info set status = 1 where project_id = "%s"',$id);
        if($result){
            echo 200;
        }
        else{
            echo 400;
        }
    }
}
?>