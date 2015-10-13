<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class MessageController extends Controller {
	public function index(){
		//dump($_SESSION);
		$Form = M('messagebox');
		$message = $Form->where('to_id = "%s"',$_SESSION['id'])->order('sent_time desc')->select();
		//dump($message);
		if($message){
			$this->assign('msg_list',$message);
		}

		$data['ifread'] = true;
		$read = $Form->where('to_id = "%s"',$_SESSION['id'])->save($data);
		if($read){
			$_SESSION['msg'] = 0;
		}
		$this->display();

	}

	public function receiver(){
		$from = $_SESSION['id'];
		$to = I('post.to',0);
		$msg_type = I('post.type',0).'_CODE';
		$object = I('post.obj',0);
		$attach = I('post.attach');
        //echo json_encode($_POST);
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
					$email = decode($investor[0]['email']);
					$phone = decode($investor[0]['mobile']);
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

			//send sms and email to project_admin
			$innovator = $model->query('select nickname, phone, email from entrepreneur_personal where user_id = "%s"',$project_admin);
			if($innovator[0]['phone']){
				$sms = sprintf('您的项目%s收到了投资人%s的投资意愿，请登录查看消息。',$project_name,$name);
	            send_forward_msg(decode($innovator[0]['phone']),$sms);
	        }
	        if($innovator[0]['email']){
	        	$body = $body=sprintf("尊敬的用户 %s：<br>  您的项目“%s”收到了投资人“%s”的投资意愿，
	        	请登录来创吧并进入消息盒查看。<br><a href='%s'>点击查看</a>",$innovator[0]['nickname'],$project_name,$name,C(MAIN_PAGE));
	        	think_send_mail(decode($innovator[0]['email']), 'User', $subject = '您收到了新的投资意愿', $body);
	        }

	        $innovator[0]['phone'] = decode($innovator[0]['phone']);
	        $innovator[0]['email'] = decode($innovator[0]['email']);
			echo json_encode($innovator[0]);
			//echo 400;
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
					$email = decode($innovator[0]['email']);
					$phone = decode($innovator[0]['phone']);
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

			//send sms and email to project_admin
			$admin = $model->query('select nickname, phone, email from entrepreneur_personal where user_id = "%s"',$project_admin);
			if($admin[0]['phone']){
				$sms = sprintf('您的项目%s收到了创业者%s的合伙意愿，请登录查看消息。',$project_name,$name);
	            send_forward_msg(decode($admin[0]['phone']),$sms);
	        }
	        if($admin[0]['email']){
	        	$body = $body=sprintf("尊敬的用户 %s：<br>  您的项目“%s”收到了创业者“%s”的合伙意愿，
	        	请登录来创吧并进入消息盒查看。<br><a href='%s'>点击查看</a>",$admin[0]['nickname'],$project_name,$name,C(MAIN_PAGE));
	        	think_send_mail(decode($admin[0]['email']), 'User', $subject = '您收到了新的合伙意愿', $body);
	        }
			//dump($result);

		}

		if($msg_type==='REQUIRE_CODE'){
			if($object === 'INVESTOR'){
				$innovator = $model -> query('select nickname, email, phone from entrepreneur_personal where user_id = "%s"',$from);
				if($innovator){
					$name = $innovator[0]['nickname'];
					$email = decode($innovator[0]['email']);
					$phone = decode($innovator[0]['phone']);
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

			//send sms and email to project_admin
			$investor = $model->query('select name, mobile, email from investor_personal where user_id = "%s"',$to);
			if($investor[0]['mobile']){
				$sms = sprintf('尊敬的投资人: 您收到了创业者%s的投资申请，请登录查看详情。',$name);
	            send_forward_msg(decode($investor[0]['mobile']),$sms);
	        }
	        if($investor[0]['email']){
	        	$body = $body=sprintf("尊敬的用户 %s：<br>  您收到了创业者“%s”的投资申请，
	        	请登录来创吧并进入消息盒查看。<br><a href='%s'>点击查看</a>",$investor[0]['name'],$name,C(MAIN_PAGE));
	        	think_send_mail(decode($investor[0]['email']), 'User', $subject = '您收到了新的投资申请', $body);
	        }
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
				$users = arrayUnion($followers,$investors,'user_id');
				
				$data['from_id'] = $to;
				$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
				$data['msg_content'] = $content;
				$data['sent_time'] = date('Y-m-d H:i:s');
				foreach ($users as $key => $value) {
					$data['to_id'] = $value;
					$result = $Form->add($data);
				}
				echo 200;
			}
		}

		if($msg_type === 'FINANCIAL_CODE'){
			if($object === 'PROJECT'){
				$round = C('INVEST_ROUND')[intval($_POST['attach'])];

				$project = $model -> query('select project_id, project_name from project_info where project_id = "%s"',$to);
				if($project){
					$project_name = $project[0]['project_name'];
					$content = '<p>您关注/投资的项目<a onclick="openProject(\''.$to.'\')">'.$project_name.'</a>进行了'.$round.'融资。</p>';
				}

				$followers = $model -> query('select user_id from relation_follow where object_id="%s" and object_type = 1 and follow_status = 1',$to);
				//dump($followers);
				$investors = $model -> query('select user_id from project_investor where project_id="%s"',$to);
				//dump($investors);
				$users = arrayUnion($followers,$investors,'user_id');
				//dump($users);

				$data['from_id'] = $to;
				$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
				$data['msg_content'] = $content;
				$data['sent_time'] = date('Y-m-d H:i:s');
				foreach ($users as $key => $value) {
					$data['to_id'] = $value;
					$result = $Form->add($data);
				}
				echo 200;
			}
		}

		if($msg_type === 'ARTICLES_CODE'){
			//dump($_POST);
			$article = $model->query('select * from admin_articles where article_id = "%s"',$to);
			if($article){
				$field = $article[0]['article_field'];
				$users = $model->query('select id from interest_investor where interest_field = %d',$field);
				$content = "您感兴趣的“".C('INTEREST_FIELD')[$field]."”领域发布了一条新的资讯,<a onclick=\"openNews('".$to."')\">点击查看</a>";

				$data['from_id'] = $to;
				$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
				$data['msg_content'] = $content;
				$data['sent_time'] = date('Y-m-d H:i:s');
				foreach ($users as $key => $value) {
					$data['to_id'] = $value['id'];
					$result = $Form->add($data);
					//dump($result);
				}
				echo 200;
			}
		}

		if($msg_type === 'PROJECTS_CODE'){
			if($object === "PROJECT"){
				$project = $model->query('select * from project_info where project_id = "%s"',$to);
				if($project){
					$project_name = $project[0]['project_name'];
				}
				$users = $model->query('select interest_investor.id, interest_investor.interest_field from interest_investor 
					inner join interest_project on interest_project.interest_field = interest_investor.interest_field
					where interest_project.id = "%s"',$to);
				$data['from_id'] = $to;
				$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
				$data['sent_time'] = date('Y-m-d H:i:s');
				foreach ($users as $key => $value) {
					$data['to_id'] = $value['id'];
					$data['msg_content'] = "您感兴趣的“".C('INTEREST_FIELD')[$value['interest_field']]."”领域新增了一个创业项目“".$project_name."”, 
					<a onclick=\"openProject('".$to."')\">点击查看</a>";
					$result = $Form->add($data);
					//dump($result);
				}
				echo 200;
			}
		}

		if($msg_type === 'AUTHORIZATION_CODE'){
			if($object === "PROJECT"){
				$data['from_id'] = $_SESSION['userid'];
				$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
				$verified = $model->query('select status, project_admin, project_name from project_info where project_id = "%s"',$to);
				if($verified[0]['status']==2){
					$data['msg_content'] = '您的项目<a onclick="openProject(\''.$to.'\')">'.$verified[0]['project_name'].'</a>已经通过审核, 成为来创认证的创业项目!';
					$data['to_id'] = $verified[0]['project_admin'];
				}
				else if($verified[0]['status']==0){
					$data['msg_content'] = '您的项目<a onclick="openProject(\''.$to.'\')">'.$verified[0]['project_name'].'</a>未通过管理员审核, 请修改信息并重新提交审核!';
					$data['to_id'] = $verified[0]['project_admin'];
					$data['msg_attachment'] = $attach;
				}
				
				$data['sent_time'] = date('Y-m-d H:i:s');
				//dump($data);
				$result = $Form->add($data);
			}
			if($object === "INVESTOR"){
				$data['from_id'] = $_SESSION['userid'];
				$data['to_id'] = $to;
				$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
				$verified = $model->query('select reg_status from investor_personal where user_id = "%s"',$to);
				if($verified[0]['reg_status']==2){
					$data['msg_content'] = '您已经通过审核, 成为来创认证的投资人!';
				}
				else if($verified[0]['reg_status']==0){
					$data['msg_content'] = '您未通过管理员审核, 请修改资料并重新提交审核!';
					$data['msg_attachment'] = $attach;
				}
				
				$data['sent_time'] = date('Y-m-d H:i:s');
				//dump($data);
				$result = $Form->add($data);
			}
			if($object === "INNOVATOR"){
				$data['from_id'] = $_SESSION['userid'];
				$data['to_id'] = $to;
				$data['msg_type'] = C(MESSAGE_CODE)[$msg_type];
				$verified = $model->query('select reg_status from entrepreneur_personal where user_id = "%s"',$to);
				if($verified[0]['reg_status']==2){
					$data['msg_content'] = '您已经通过审核, 成为来创认证的创业者!';
				}
				else if($verified[0]['reg_status']==0){
					$data['msg_content'] = '您未通过管理员审核, 请修改资料并重新提交审核!';
					$data['msg_attachment'] = $attach;
				}
				
				$data['sent_time'] = date('Y-m-d H:i:s');
				//dump($data);
				$result = $Form->add($data);
			}
		}
	}
}
?>