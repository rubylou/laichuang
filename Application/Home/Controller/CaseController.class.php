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
		$result = $Form->query('select project_info.*, name from project_info inner join entrepreneur_personal on project_admin = user_id where project_id="%s"',$id);
		foreach ($result as $key => $value) {
			$result[$key]['project_type'] = C('INTEREST_FIELD')[$value['project_type']];
			$result[$key]['project_intro'] = htmlspecialchars_decode($value['project_intro']);
		}
		dump($result);
		$this->info = $result[0];
		$this->display();
	}
}
?>