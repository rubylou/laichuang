<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class NewsController extends Controller {
	public function index(){
		$Form = new Model();
		$result = $Form->query('select * from admin_articles');
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
		$this->vo = $result;
		$this->assign("list",$result);
		$this->display();
	}

	public function detail(){
		$Form = new Model();
		$result = $Form->query('select * from admin_articles where article_id="%s"',$_GET['p']);
		if($result){
			$this->p = $result[0];
		}
		$this->display();
	}
}
?>