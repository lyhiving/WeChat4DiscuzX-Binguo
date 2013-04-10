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
global $_G;
$set = $_G['cache']['plugin']['cloud_wx'];
if($set['ORC'] == 1){
	if($set['SHAREIMG'] == 0){
		echo "Already closed!";
		exit;
	}
	$show = new show;
	$num	=	30;
	$list = $show->echolist($num);
	$pid = count($list);
	$navtitle = "微信图墙";
	if($pid == $num){
		if($_GET['p'] > 1){
			$pid = $_GET['p'] + 1;
		}else{
			$pid = 2;
		}
		$page = "<a href=\"plugin.php?id=cloud_wx:show&p=".$pid."\">Next</a>";
	}else{
		$page = "";
	}
	if($_G['charset'] == 'gbk'){
		$navtitle = iconv('utf-8','gbk',$navtitle);
		include template ( 'cloud_wx:share_gbk' );
	}else{
		include template ( 'cloud_wx:share_utf' );
	}
}



class show{

		function echolist($num){
			if(empty($num)) $num	=	30;	//每页显示数量
			if(!empty($_GET['p']) && ($_GET['p'] > 1) && is_numeric($_GET['p'])){
				$page	=	intval($_GET['p']);
				$start	=	($page * $num) - $num;
				$result = DB::fetch_all("SELECT * FROM pre_cloud_wx_pic WHERE status=1  ORDER BY id DESC LIMIT $start,$num");
				//$count	=	count($result); //总记录数量
				return $result;
			}else{
				$result = DB::fetch_all("SELECT * FROM pre_cloud_wx_pic WHERE status=1  ORDER BY id DESC LIMIT 0,$num");
				return $result;
			}
			
			
		}
		
		
}