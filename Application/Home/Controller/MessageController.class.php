<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class MessageController extends Controller {
	public function index(){
		//dump($_SESSION);
		$Form = M('messagebox');
		$message = $Form->where('to_id = "%s"',$_SESSION['id'])->select();
		//dump($message);
		if($message){
			$this->assign('msg_list',$message);
		}
		$this->display();

	}

	public function receiver(){
		$from = $_SESSION['id'];
		$to = I('post.to',0);
		$msg_type = I('post.type',0).'_CODE';
		$object = I('post.obj',0);
		$attach = I('post.attach');

		//dump($from);
		//dump($to);
		//dump($msg_type);
		//dump($object);

		$Form = M('messagebox');
		$model = new Model();
		if($msg_type==='INVEST_CODE'){
			if($object === 'PROJECT'){
				$project = $model -> query('select project_id, project_name, project_admin from project_info where project_id = "%s"',$to);
				if($project){
					$project_name = $project[0]['project_name'];
					$project_admin = $project[0]['project_admin'];
					$content = '<p>您的项目<a onclick="openProject(\''.$to.'\')">'.$project_name.'</a>收到了投资人的投资意愿。</p>';
				}
				$investor = $model -> query('select name, email, mobile from investor_personal where user_id = "%s"',$from);
				if($investor){
					$name = $investor[0]['name'];
					$email = $investor[0]['email'];
					$phone = $investor[0]['mobile'];
					$content = $content.'<p>姓名: '.$name.' 邮箱: '.$email.' 电话: '.$phone.'</p>';
				}
			}
			$data['from_id'] = $from;
			$data['to_id'] = $project_admin;
			$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
			$data['msg_content'] = $content;
			$data['sent_time'] = date('Y-m-d H:i:s');
			//dump($data);
			$result = $Form->add($data);
			//dump($result);

		}

		if($msg_type==='JOIN_CODE'){
			if($object === 'PROJECT'){
				$project = $model -> query('select project_id, project_name, project_admin from project_info where project_id = "%s"',$to);
				if($project){
					$project_name = $project[0]['project_name'];
					$project_admin = $project[0]['project_admin'];
					$content = '<p>您的项目<a onclick="openProject(\''.$to.'\')">'.$project_name.'</a>收到了创业者的合伙意愿。</p>';
				}
				$innovator = $model -> query('select nickname, email, phone from entrepreneur_personal where user_id = "%s"',$from);
				if($innovator){
					$name = $innovator[0]['nickname'];
					$email = $innovator[0]['email'];
					$phone = $innovator[0]['phone'];
					$content = $content.'<p>昵称: '.$name.' 邮箱: '.$email.' 电话: '.$phone.'</p>';
				}
			}
			$data['from_id'] = $from;
			$data['to_id'] = $project_admin;
			$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
			$data['msg_content'] = $content;
			$data['msg_attachment'] = $attach;
			$data['sent_time'] = date('Y-m-d H:i:s');
			//dump($data);
			$result = $Form->add($data);
			//dump($result);

		}

		if($msg_type==='REQUIRE_CODE'){
			if($object === 'INVESTOR'){
				$innovator = $model -> query('select nickname, email, phone from entrepreneur_personal where user_id = "%s"',$from);
				if($innovator){
					$name = $innovator[0]['nickname'];
					$email = $innovator[0]['email'];
					$phone = $innovator[0]['phone'];
					$content = '<p>您收到了创业者<a onclick="openUser(\''.$from.'\')">'.$name.'</a>的投资申请, 联系方式如下: </p>';
					$content = $content.'<p>邮箱: '.$email.' 电话: '.$phone.'</p>';
				}
				
			}
			$data['from_id'] = $from;
			$data['to_id'] = $to;
			$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
			$data['msg_content'] = $content;
			$data['msg_attachment'] = $attach;
			$data['sent_time'] = date('Y-m-d H:i:s');
			//dump($data);
			$result = $Form->add($data);
			//dump($result);
		}

		if($msg_type==='MEMBERS_CODE'){
			if($object === "PROJECT"){
				$member = $model -> query('select user_id, nickname from entrepreneur_personal where email="%s" or phone="%s"',$attach,$attach);
				if($member){
					$member_info = "<a onclick='openUser(\"".$member[0]['user_id']."\")'>".$member[0]['nickname']."</a>";
				}
				$project = $model -> query('select project_id, project_name, project_admin from project_info where project_id = "%s"',$to);
				if($project){
					$project_name = $project[0]['project_name'];
					$content = '<p>您关注/投资的项目<a onclick="openProject(\''.$to.'\')">'.$project_name.'</a>增加了新成员'.$member_info.'。</p>';
				}

				$followers = $model -> query('select user_id from relation_follow where object_id="%s" and object_type = 1 and follow_status = 1',$to);
				$investors = $model -> query('select user_id from project_investor where project_id="%s"',$to);
				$users = array_merge($followers,$investors);
				$users = array_unique($users);

				$data['from_id'] = $to;
				$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
				$data['msg_content'] = $content;
				$data['sent_time'] = date('Y-m-d H:i:s');
				foreach ($users as $key => $value) {
					$data['to_id'] = $value['user_id'];
					$result = $Form->add($data);
				}
			}
		}
	}
}
?>