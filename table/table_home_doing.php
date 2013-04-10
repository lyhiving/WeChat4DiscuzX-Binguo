<?php
/**
 *	[公众微信智能云平台(cloud_wx.{})] (C)2013-2099 Powered by YangLin.
 * Author QQ:28945763  问题解答技术交流QQ群：294440459
 *	Version: 5.5       http://i.binguo.me
 *	Date: 2013-4-4 00:00
 */



if(!defined('IN_DISCUZ')) {

	exit('Access Denied');

}

class table_home_doing {

	function pub_say($fu,$uid,$msg){
		
		$info = C::t('#cloud_wx#common_member')->get_user($uid);

		if($info == false) {
			return "您在本站的账号状态异常！";
		}else{
			$username = $info['username'];
			$msg	.= "   ";
	
			$say	=	mb_substr($msg, 3, -1, 'utf-8');
	
			$len	=	mb_strlen(trim($say),'UTF8'); 
	
			if($len < 6){
				return "亲，多吐点儿槽吧，字数太短了哦~";
			}
	
			$check	=	C::t('#cloud_wx#cloud_wx')->rt_info($fu,"pub_say");
	
			if($check == false){
	
				return "亲，您的操作太快了。。。";
	
			}else{
	
				require_once DISCUZ_ROOT.'./config/config_ucenter.php';

				if(UC_DBCHARSET != 'utf8')	$say = iconv('utf-8',UC_DBCHARSET,$say);
				$say		=	daddslashes($say);
	
				$time	=	$_SERVER['REQUEST_TIME'];
	
				DB::insert('home_doing', array(
	
									'uid' => $uid,
	
									'username' => $username,
	
									'from'	=>	"weixin",
	
									'dateline'	=>	$time,
	
									'message'	=>	$say."<img src=\"source\\plugin\\cloud_wx\\template\\images\\icon.png\" alt=\"From WeiXin\" style=\"float:right;\" />",
	
									'ip'	=>	"1.1.1.1",
	
									'replynum'	=>	0,
	
									'status'	=>	0
	
									));
	
				
	
				return "发布成功！";
	
			}
		}

	}

	

	

}