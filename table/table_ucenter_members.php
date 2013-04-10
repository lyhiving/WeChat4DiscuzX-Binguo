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
class table_ucenter_members {
	function check_user($user,$pwd){
		require_once DISCUZ_ROOT.'./config/config_ucenter.php';
		if(UC_DBCHARSET != 'utf8') $user = iconv('utf-8',UC_DBCHARSET,$user);
		$data = DB::fetch_first("SELECT  uid,username,password,salt  FROM ".DB::table('ucenter_members')." WHERE username = '$user' ");
		if($data){
			if(md5(md5($pwd).$data['salt']) == $data['password']){
				return $data['uid'];
			}else{
				return "密码错误！";
			}
		}else{
			return "此用户不存在！";
		}

	}
	
}