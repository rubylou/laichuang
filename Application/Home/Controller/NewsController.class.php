<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class NewsController extends Controller {
	public function index(){
		$Form = new Model();
		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_personal.admin_id = admin_articles.admin_id 
			where article_type = 1');
		foreach ($result as $key => $value) {
			$result[$key]['article_field'] = C('INTEREST_FIELD')[$value['article_field']];
			$result[$key]['article_type'] = C('MODULE_CODE')[$value['article_type']];
			$result[$key]['article_content'] = preg_replace("/\n/", "", $result[$key]['article_content']);
            $result[$key]['article_content'] = htmlspecialchars_decode($result[$key]['article_content']);
            
		}
		//dump($result);
		$this->assign("list1",$result);

		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_personal.admin_id = admin_articles.admin_id 
			where article_type = 2');		
		foreach ($result as $key => $value) {
			$result[$key]['article_field'] = C('INTEREST_FIELD')[$value['article_field']];
			$result[$key]['article_type'] = C('MODULE_CODE')[$value['article_type']];
			$result[$key]['article_content'] = preg_replace("/\n/", "", $result[$key]['article_content']);
            $result[$key]['article_content'] = htmlspecialchars_decode($result[$key]['article_content']);
            
		}
		//dump($result);
		$this->assign("list2",$result);

		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_personal.admin_id = admin_articles.admin_id 
			where article_type = 3');
		foreach ($result as $key => $value) {
			$result[$key]['article_field'] = C('INTEREST_FIELD')[$value['article_field']];
			$result[$key]['article_type'] = C('MODULE_CODE')[$value['article_type']];
			$result[$key]['article_content'] = preg_replace("/\n/", "", $result[$key]['article_content']);
            $result[$key]['article_content'] = htmlspecialchars_decode($result[$key]['article_content']);
            
		}
		//dump($result);
		$this->assign("list3",$result);

		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_personal.admin_id = admin_articles.admin_id 
			where article_type = 4');
		foreach ($result as $key => $value) {
			$result[$key]['article_field'] = C('INTEREST_FIELD')[$value['article_field']];
			$result[$key]['article_type'] = C('MODULE_CODE')[$value['article_type']];
			$result[$key]['article_content'] = preg_replace("/\n/", "", $result[$key]['article_content']);
            $result[$key]['article_content'] = htmlspecialchars_decode($result[$key]['article_content']);
            
		}
		//dump($result);
		$this->assign("list4",$result);

		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_personal.admin_id = admin_articles.admin_id 
			where article_type = 5');
		foreach ($result as $key => $value) {
			$result[$key]['article_field'] = C('INTEREST_FIELD')[$value['article_field']];
			$result[$key]['article_type'] = C('MODULE_CODE')[$value['article_type']];
			$result[$key]['article_content'] = preg_replace("/\n/", "", $result[$key]['article_content']);
            $result[$key]['article_content'] = htmlspecialchars_decode($result[$key]['article_content']);
            
		}
		//dump($result);
		$this->assign("list5",$result);
		$this->display();
	}

	public function detail(){
		$Form = new Model();
		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_articles.admin_id = admin_personal.admin_id
			where article_id="%s"',$_GET['p']);
		if($result){
			$result[0]['article_type'] = C('MODULE_CODE')[$result[0]['article_type']];
			//dump($result[0]);
			if($result[0]['article_about'] == C(PROJECT_CODE)){
				$object = $Form->query('select project_name as name, project_logo as portrait from project_info
					where project_id = "%s"',$result[0]['article_object']);
				//dump($object);
				$this->obj = $object[0];
			}
			else if($result[0]['article_about'] == C(INVESTOR_CODE)){
				$object = $Form->query('select name, portrait from investor_personal
					where user_id = "%s"',$result[0]['article_object']);
				//dump($object);
				$this->obj = $object[0];
			}
			else if($result[0]['article_about'] == C(INNOVATOR_CODE)){
				$object = $Form->query('select name, portrait from entrepreneur_personal
					where user_id = "%s"',$result[0]['article_object']);
				//dump($object);
				$this->obj = $object[0];
			}
			$this->p = $result[0];
		}

		$update = $Form->execute('update admin_articles set article_visits=%d where article_id="%s"',$result[0]['article_visits']+1,$_GET['p']);
		$this->display();
	}
}
?>