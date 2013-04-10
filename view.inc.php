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
header( 'Content-Type:text/html;charset=utf-8 '); 

new view;

class view{
	function __construct(){
		$id	=	$_GET['tid'];
		if(!empty($id) && is_numeric($id)){
			$this->read($id);
		}elseif(!empty($_POST['msg'])){
			//$this->post_msg();
			exit;
		}else{
			exit;
		}
	}

	function read($id){
		$data	=	C::t('#cloud_wx#forum_thread')->get_post($id);
		$title	=	$data['subject'];
		$content	=	$data['message'];
		$author	=	$data['author'];
		$from	=	$data['from'];
		$uid	=	$data['authorid'];
		$avatar	=	$data['avatar'];
		$comment	=	$data['comment'];
		$time	=	date("Y-m-d H:i:s",$data['dateline']);
		$rand	=	$data['rand'];

			include template('cloud_wx:thread_view');

	}
		
}