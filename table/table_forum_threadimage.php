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

class table_forum_threadimage {
	function get_cover($tid){
		global $_G;
		$cover =  DB::fetch_first("SELECT * FROM ".DB::table('forum_threadimage')." WHERE tid = '$tid' ");
		if($cover){
			if($cover['remote'] == 0){
				return $_G['setting']['siteurl']."/".$_G['setting']['attachurl']."forum/".$cover['attachment'];
			}else{
				return false;
			}
				
		}else{
			return false;
		}
		
	}
	
	
}