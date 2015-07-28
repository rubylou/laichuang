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
		$this->assign("list1",$result);

		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_personal.admin_id = admin_articles.admin_id 
			where article_type = 2');		
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
		$this->assign("list2",$result);

		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_personal.admin_id = admin_articles.admin_id 
			where article_type = 3');
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
		$this->assign("list3",$result);

		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_personal.admin_id = admin_articles.admin_id 
			where article_type = 4');
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
		$this->assign("list4",$result);

		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_personal.admin_id = admin_articles.admin_id 
			where article_type = 5');
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
		$this->assign("list5",$result);
		$this->display();
	}

	public function detail(){
		$Form = new Model();
		$result = $Form->query('select admin_articles.*, admin_nickname from admin_articles 
			inner join admin_personal on admin_articles.admin_id = admin_personal.admin_id
			where article_id="%s"',$_GET['p']);
		if($result){
			$this->p = $result[0];
		}

		$update = $Form->execute('update admin_articles set article_visits=%d where article_id="%s"',$result[0]['article_visits']+1,$_GET['p']);
		$this->display();
	}
}
?>