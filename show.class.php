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

class plugin_cloud_wx {
	
	function global_footer() {	
		global $_G;
		$set = $_G['cache']['plugin']['cloud_wx'];
		if(($set['ORC'] == 1) && ($set['SHOW'] == 1)){
		if($_G['charset'] == 'gbk'){
			include template ( 'cloud_wx:show_gbk' );
		}else{
			include template ( 'cloud_wx:show_utf' );
		}
		return $show;
		}
	}
	

}
