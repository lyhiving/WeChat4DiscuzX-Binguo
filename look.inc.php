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

$look = new look;
$id	=	$_GET['p'];
if(!empty($id) && is_numeric($id)){
	$look->view($id);
}
class look{
	function view($id){
		global $_G;
		$set = $_G['cache']['plugin']['cloud_wx'];
		$data	=	C::t('#cloud_wx#cloud_wx_pic')->view($id);
		if(empty($data)) exit;
		$pid	=	$data['id'];
		$title = $data['title'];
		$content = $data['pic'];
		$checks	=	$data['checks'];
		$add_time	=	$data['add_time'];
		$code = $set['CODEIMG'];
		$pn = C::t('#cloud_wx#cloud_wx_pic')->pnid($id);
		if(!empty($pn[0]['id'])) $pre = "plugin.php?id=cloud_wx:look&p=".$pn[0]['id'];
		if(!empty($pn[1]['id'])) $nex = "plugin.php?id=cloud_wx:look&p=".$pn[1]['id'];
		require_once DISCUZ_ROOT.'./config/config_ucenter.php';
		$is_mo	=	$this->is_mobile();
		if($is_mo == true){
			header( 'Content-Type:text/html;charset=utf-8 '); 
			if(UC_DBCHARSET != 'utf8'){
			$title = iconv(UC_DBCHARSET,'utf-8',$title);
			$content = iconv(UC_DBCHARSET,'utf-8',$content);
			}
			include template('cloud_wx:mb_view');
		}else{
			$navtitle	=	$title;
			if($_G['charset'] == 'gbk'){
				include template('cloud_wx:pc_view_gbk');
			}else{
				include template('cloud_wx:pc_view_utf');
			}
		}
	}
	
	function is_mobile() {
		global $_SERVER;
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
		$is_mobile = false;
		foreach ($mobile_agents as $device) {
			if (stristr($user_agent, $device)) {
				$is_mobile = true;
				break;
			}
		}
		return $is_mobile;
	}

}