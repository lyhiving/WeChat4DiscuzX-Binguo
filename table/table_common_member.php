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
class table_common_member {
	function get_user($uid){
		$data = DB::fetch_first("SELECT  uid,username,groupid   FROM ".DB::table('common_member')." WHERE uid = '$uid' ");
		if(!empty($data)){
			return $data;
		}else{
			return false;
		}

	}
	
}