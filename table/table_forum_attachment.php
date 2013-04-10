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

class table_forum_attachment {
	function get_att($tid){
		$tableid	=	$this->get_table($tid);
		if(!empty($tableid)){
			$table 	=	"forum_attachment_".$tableid;
			$datas = DB::fetch_all("SELECT * FROM ".DB::table($table)." WHERE tid = '$tid' AND price=0 AND remote=0 ");
			if($datas){
				$url = $this->get_one_url($datas);
				if(!empty($url)){
					global $_G;
					return $_G['setting']['siteurl'].$_G['setting']['attachurl']."forum/".$url;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
		
	}
	
	function get_table($tid){
		$datas = DB::fetch_first("SELECT aid,tid,tableid FROM ".DB::table('forum_attachment')." WHERE tid = '$tid' ");
		return $datas['tableid'];
	}
	
	function get_one_url($datas){
		foreach ($datas as $dv){
				if(strpos($dv['attachment'],'.jpg') !== false){
					return $dv['attachment'];
					exit;
				}
		}
	}
	
	
}