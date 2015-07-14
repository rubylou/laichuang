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
		$follow = $Form->query("select follow_status from follow_relation where user_id='%s' 
			and object_id='%s' and object_type=%d",$_SESSION['id'],$_GET['key'],C(PROJECT_CODE));
		$this->info = $result[0];
		dump($this->info);
		if($follow[0]['follow_status']=='1'){
			$this->follow = C('FOLLOWING');
		}
		else{
			$this->follow = C('UNFOLLOW');
		}
		
		$this->display();
	}

	public function proFollow(){
		$Form = new Model();
		$result = $Form->execute('replace into follow_relation (user_id, object_id, object_type, follow_status) 
			values ("%s","%s",%d,%d)',$_SESSION['id'],$_GET['val'],C(PROJECT_CODE),C($_POST['key']));
		if($result){
			echo 200;
		}
		else{
			echo 400;
		}
	}
}
?>