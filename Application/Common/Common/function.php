<?php
use Think\Model;
require 'PHPMailerAutoload.php';
    function insertAdminRecord($admin_id,$audition_type,$audition_object,$audition_note)
    {
        $Form = new Model();
        //update the admin_audition
        $time = date('Y-m-d');
        $id=time();
        $splstr=sprintf('insert into admin_audition (admin_id,audition_type,audition_object,audition_time,audition_note) 
            values ("%d","%s","%d","%s","%s","%s")',
        $admin_id,$audition_type,$audition_object,$time,$audition_note);
        $res=$Form->execute($sqlstr);
        if($res)
            echo $sqlstr."200";
        else
            echo $sqlstr."400";
    }
    function getCode($num,$w,$h) {  
        $code = "";  
        for ($i = 0; $i < $num; $i++) {  
            $code .= rand(0, 9);  
        }  
        //4位验证码也可以用rand(1000,9999)直接生成  
        //将生成的验证码写入session，备验证时用  
        $_SESSION["helloweba_num"] = $code;  
        //创建图片，定义颜色值  
        header("Content-type: image/PNG");  
        $im = imagecreate($w, $h);  
        $black = imagecolorallocate($im, 0, 0, 0);  
        $gray = imagecolorallocate($im, 200, 200, 200);  
        $bgcolor = imagecolorallocate($im, 255, 255, 255);  
        //填充背景  
        imagefill($im, 0, 0, $gray);  

        //画边框  
        imagerectangle($im, 0, 0, $w-1, $h-1, $black);  

        //随机绘制两条虚线，起干扰作用  
        $style = array ($black,$black,$black,$black,$black,  
            $gray,$gray,$gray,$gray,$gray  
        );  
        imagesetstyle($im, $style);  
        $y1 = rand(0, $h);  
        $y2 = rand(0, $h);  
        $y3 = rand(0, $h);  
        $y4 = rand(0, $h);  
        imageline($im, 0, $y1, $w, $y3, IMG_COLOR_STYLED);  
        imageline($im, 0, $y2, $w, $y4, IMG_COLOR_STYLED);  

        //在画布上随机生成大量黑点，起干扰作用;  
        for ($i = 0; $i < 80; $i++) {  
            imagesetpixel($im, rand(0, $w), rand(0, $h), $black);  
        }  
        //将数字随机显示在画布上,字符的水平间距和位置都按一定波动范围随机生成  
        $strx = rand(3, 8);  
        for ($i = 0; $i < $num; $i++) {  
            $strpos = rand(1, 6);  
            imagestring($im, 5, $strx, $strpos, substr($code, $i, 1), $black);  
            $strx += rand(8, 12);  
        }  
        imagepng($im);//输出图片  
        imagedestroy($im);//释放图片所占内存  
    }

     /**
     * [getPic description]
     * 获取文本中首张图片地址
     * @param  [type] $content [description]
     * @return [type]          [description]
     */
    function getPic($content,$prefix=''){
      //dump($content);
      if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
        //dump($matches);
        $str=$matches[3][0];
        //dump($str);
        if (preg_match('/'.$prefix.'\/Public\/upload\/pic/', $str)){
	  if(strlen($prefix)>0){
              $str1 = ".".substr($str, strlen($prefix)-1);
	  }
          else{
	      $str1 = ".".$str;
          }
          //dump($str1);
          return $str1;
        }
      }
    }


    
    /**********************************************************************************************************************/
    //下面的都是用来发短信用的
    function generate_code()
    {
        $length=6;
        $code=rand(pow(10,($length-1)), pow(10,$length)-1);;
        return $code;
    }
    function send_forward_msg($mobile,$content)
    {
        $uid=C(MSGUID);
        $pwd=C(MSGPWD);
        $res = sendSMS($uid,$pwd,$mobile,$content);
        if($res=="OK")
        {
            
           return 200;
        }else
        {
            return 400;
        }
    }
    function send_msg($mobile)
    {
        $uid=C(MSGUID);
        $pwd=C(MSGPWD);
        $code=generate_code();
        $content=sprintf("欢迎加入来创，您的验证码为：%s ，30分钟内有效，过期失效。",$code);
        $res = sendSMS($uid,$pwd,$mobile,$content);
        if($res=="OK")
        {
            $Form = new Model();
            $overtime=time()+1800;//设定超时
            $insert = $Form->execute('replace into mobile_check (mobile,check_code,over_time) 
            values ("%s","%s","%d")',
            $mobile,$code,$overtime);
            if($insert)
                return 200;
            else
                return 400;
        }else
        {
            return 400;
        }
    }
    function check_mobile($mobile,$code){
        $Form = new Model();
        $now=time();
        $record=$Form->query("select * from mobile_check where mobile='%s' ",$mobile);
        if($record)
        {
            $r=$record[0];
            $stamp=intval($r[over_time]);
            if($stamp<$now)
                return 408;
            if($r[check_code]!=$code)
                return 409;
            return 200;
        }else
        {
            return 400;
        }
    }
    function sendSMS($uid,$pwd,$mobile,$content,$time='',$mid='')
    {
        $http = C(MSGAPI);
        $data = array
            (
            'ac'=>'send',
            'uid'=>$uid,                    //用户账号
            'pwd'=>strtolower(md5($pwd)),   //MD5位32密码
            'mobile'=>$mobile,              //号码
            'content'=>$content,         //内容
            'encode'=> 'utf8'
            //'time'=>'2010-05-27 12:11',     //定时发送
            );
        $re= postSMS($http,$data);          //POST方式提交
        if( trim($re) == '100' )
        {
            return "OK";
         }
        else 
        {
            return trim($re);
        }
    }

    function postSMS($url,$data='')
    {
        $row = parse_url($url);
        $host = $row['host'];
        $port = $row['port'] ? $row['port']:80;
        $file = $row['path'];
        while (list($k,$v) = each($data)) 
        {
            $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
        }
        $post = substr( $post , 0 , -1 );
        $len = strlen($post);
        $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
        if (!$fp) {
            return "$errstr ($errno)\n";
        } else {
            $receive = '';
            $out = "POST $file HTTP/1.0\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n\r\n";
            $out .= $post;      
            fwrite($fp, $out);
            while (!feof($fp)) {
                $receive .= fgets($fp, 128);
            }
            fclose($fp);
            $receive = explode("\r\n\r\n",$receive);
            unset($receive[0]);
            return implode("",$receive);
        }
    }
    /**********************************************************************************************************************/
    //下面是发邮件用的    
  function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){
        
        $mail = new PHPMailer;
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = C(SMTP);  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = C(MAILUSER);                 // SMTP username
        $mail->Password = C(MAILPWD);                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
        $mail->From = C(MAILFROM);
        $mail->FromName = C(MAILNAME);
        $mail->addAddress($to); // Add a recipient 


        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strval($body);

        if(!$mail->send()) {
            return 400;//'Message could not be sent.';
        //return 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            //echo 'Message has been sent';
            return 200;
        }
    }
    //发送激活邮件给userid objectid
    function send_active_mail($user_id,$objectid,$to_address)
    {
        $Form = new Model();
        $name='';
        $pwd='';
        $active_code='';
        if($objectid==2)//investor
        {
            $names=$Form->query('select name from investor_personal where user_id="%s"',$user_id);
            $pwds=$Form->query('select user_pwd from investor_security where user_id="%s"',$user_id);
            $name=$names[0][name];
            $pwd=$pwds[0][user_pwd];
        }else
        {
            $names=$Form->query('select name from entrepreneur_personal where user_id="%s"',$user_id);
            $pwds=$Form->query('select user_pwd from entrepreneur_security where user_id="%s"',$user_id);
            $name=$names[0][name];
            $pwd=$pwds[0][user_pwd];
        }

        $active_time=time();
        $over_time=$active_time+24*60*60;
        $active_code=md5($user_id.$name.$pwd.$active_time);
        $sqlstr=sprintf("replace into email_active (user_id,active_code,mail_address,over_time,active_status)
             values ('%s','%s','%s','%d','%d')",$user_id,$active_code,$to_address,$over_time,0);
        $res=$Form->execute($sqlstr);
        if($res)
        {
            $url=sprintf("http://localhost:8888/lcb/index.php/Home/Active/index?key1=%s&key2=%s&key3=%s",
                $user_id,$to_address,$active_code);
            $body=sprintf("尊敬的用户 %s：请点击以下链接激活邮箱，如不能点击请将地址拷贝至浏览器栏激活邮箱。<br><a>%s</a>",$name,$url);
            return think_send_mail($to_address, $name, $subject = '来创科技邮箱激活', $body);
        }
        return 400;

    }
    //找回密码的邮件
    function send_find_mail($user_id,$objectid,$to_address)
    {
        $Form = new Model();
        $name='';
        $pwd='';
        $active_code='';
        if($objectid==2)//investor
        {
            $names=$Form->query('select name from investor_personal where user_id="%s"',$user_id);
            $pwds=$Form->query('select user_pwd from investor_security where user_id="%s"',$user_id);
            $name=$names[0][name];
            $pwd=$pwds[0][user_pwd];
        }else
        {
            $names=$Form->query('select name from entrepreneur_personal where user_id="%s"',$user_id);
            $pwds=$Form->query('select user_pwd from entrepreneur_security where user_id="%s"',$user_id);
            $name=$names[0][name];
            $pwd=$pwds[0][user_pwd];
        }

        $active_time=time();
        $over_time=$active_time+24*60*60;
        $active_code=md5($user_id.$name.$pwd.$active_time);
        $sqlstr=sprintf("replace into email_find_pwd (user_id,active_code,mail_address,over_time,active_status)
             values ('%s','%s','%s','%d','%d')",$user_id,$active_code,$to_address,$over_time,0);
        $res=$Form->execute($sqlstr);
        if($res)
        {
            $url=sprintf("http://localhost:8888/lcb/index.php/Home/Account/setting?key1=%s&key2=%s&key3=%s",
                $user_id,$to_address,$active_code);
            $body=sprintf("尊敬的用户 %s：请点击以下链接重置密码，如不能点击请将地址拷贝至浏览器栏。<br><a>%s</a>",$name,$url);
            return think_send_mail($to_address, $name, $subject = '来创科技重置密码', $body);
        }
        return 400;

    }
    //---------------------------php post--------------------------------//
    function send_post($url, $post_data) {
        $postdata = http_build_query($post_data);
        $options = array(
                'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
                )
            );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
/*    
$post_data = array(
    'username' => 'stclair2201',
    'password' => 'handan'
);
send_post('http://blog.snsgou.com', $post_data);
*/
    //

    function arrayUnion($arr1,$arr2,$token){
        foreach ($arr1 as $key => $value) {
            $arr1[$key] = $value[$token];
        }
        foreach ($arr2 as $key => $value) {
            $arr2[$key] = $value[$token];
        }
        $arr3 = array_merge($arr1,$arr2);
        return array_unique($arr3);
    }

    function encode($data){
        $des = \Think\Crypt\Driver\Des::encrypt($data, C(KEY_SEEDS));
        return base64_encode($des);
    }

    function decode($data){
        $des = base64_decode($data);
        $des = \Think\Crypt\Driver\Des::decrypt($des, C(KEY_SEEDS));
        return trim($des);
    }

 ?>
