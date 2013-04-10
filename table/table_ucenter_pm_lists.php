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
class table_ucenter_pm_lists {
	function get_msg($uid){
			require_once DISCUZ_ROOT.'./config/config_ucenter.php';
			global $_G;
			$newmsg = array();
			$msg = $this->fetch_all("select l.plid,l.lastmessage,t.lastdateline from ".UC_DBTABLEPRE.'pm_members'." t,".UC_DBTABLEPRE.'pm_lists'." l where t.plid = l.plid and t.isnew = '1' and t.uid = '".$uid."' order by t.lastdateline desc",$_G['setting']['version']);
			foreach ($msg as $value) {
				$value['lastmessage'] = unserialize($value['lastmessage']);
				$value['lastmessage']['lastsummary'] = cutstr(strip_tags($value['lastmessage']['lastsummary']), 120, '...');
				$newmsg[] = $value;
			}
			$msg_total = count($newmsg);
			if($msg_total == 0){
				return "您还没有收到新消息哦~欢迎随时关注";
			}else{
				$id = array_rand($newmsg);
				$out = $newmsg[$id]['lastmessage']['lastsummary'];
				if(UC_DBCHARSET != 'utf8')	$out	 = iconv(UC_DBCHARSET,'utf-8',$out);
				return "有".$msg_total."人向您发来新消息：\n 	".$out."\n\n请登陆本站查看详情。\n".$_G['siteurl'];
			}
	}

	function fetch_all($sql,$version='X2.5'){
		$result = array();
		if($version == 'X2'){
			$result = $this->fetch_all_for_x20($sql);
		}else{
			$result = $this->fetch_all_for_x25($sql);
		}
		return $result;
	}

	function fetch_all_for_x20($sql, $arg = array(), $keyfield = '', $silent=false) {
		$data = array();
		$query = DB::query($sql, $arg, $silent, false);
		while ($row = DB::fetch($query)) {
			if ($keyfield && isset($row[$keyfield])) {
				$data[$row[$keyfield]] = $row;
			} else {
				$data[] = $row;
			}
		}
		DB::free_result($query);
		return $data;
	}

	function fetch_all_for_x25($sql, $arg = array(), $keyfield = '', $silent=false) {
		return DB::fetch_all($sql, $arg, $keyfield, $silent);
	}

}

