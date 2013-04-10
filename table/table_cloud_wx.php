<?php

/**
 * 	[公众微信智能云平台(cloud_wx.{})] (C)2013-2099 Powered by YangLin.
 * Author QQ:28945763  问题解答技术交流QQ群：294440459
 * 	Version: 5.5       http://i.binguo.me
 * 	Date: 2013-4-4 00:00
 */
if (!defined('IN_DISCUZ')) {

	exit('Access Denied');
}

class table_cloud_wx {

	function get_info($a) {

		$user = DB::fetch_first("SELECT * FROM " . DB::table('cloud_wx') . " WHERE wx = '$a' ");

		if ($user) {

			return $user;
		} else {

			return false;
		}
	}

	function rt_info($wx, $act = "", $step = "", $mark = "") {

		$result = $this->get_info($wx);

		$now = $_SERVER['REQUEST_TIME'];

		if ($result == false) {

			//写入

			DB::insert('cloud_wx', array(
				'wx' => $wx,
				'uid' => '',
				'act' => $act,
				'step' => $step,
				'mark' => $mark,
				'much' => 1,
				'start' => $now,
				'time' => $now
			));

			return true;
		} else {

			if (($act == $result['act']) && ($now - $result['time']) < 50) {

				return false;
			} else {
				//如果从上次操作记录到现在超过24小时则重置start记录
				if (($now - $result['start']) > 86400) {
					$much = 1;
					$start = $now;
				} else {
					//如果在24小时内，则验证操作次数
					if ($result['much'] > 100) {
						return false;
					} else {
						$much = $result['much'] + 1;
						$start = $result['start'];
					}
				}

				//更新

				DB::query("UPDATE " . DB::table('cloud_wx') . " SET act='$act',step = '$step',mark = '$mark',time = '$now',much = '$much',start = '$start' WHERE wx='$wx'");

				return true;
			}
		}
	}

	function check_bind($a) {

		$data = $this->get_info($a);

		if ($data != false) {

			if (($data['uid'] > 0)) {
				//uid有数据，已绑定过
				return 2;
			} else {
				//uid为0，已经发过图但未绑定
				return 1;
			}
		} else {
			//无数据，未绑定过
			return 0;
		}
	}

	function bind_qx($wx) {
		DB::query("UPDATE " . DB::table('cloud_wx') . " SET uid = '' WHERE wx='$wx'");
	}

	function bind($fu, $tu, $user, $pwd) {

		global $_G, $_set;

		$_set = $_G['cache']['plugin']['cloud_wx'];

		$_set['GROUPS'] = (array) unserialize($_set['GROUPS']);

		$_set['FORMS'] = (array) unserialize($_set['FORMS']);

		$_set['PLUS'] = (array) unserialize($_set['PLUS']);

		if (empty($user) || empty($pwd)) {

			return "请检查输入格式，用户名和密码使用 空格 分割。";
		} else {

			$u = $this->check_bind($fu);

			if ($u == 2) {

				return "你已经绑定过了！";
			} else {

				$uid = C::t('#cloud_wx#ucenter_members')->check_user($user, $pwd);

				if (!is_numeric($uid)) {

					return $uid;
				} else {

					$info = C::t('#cloud_wx#common_member')->get_user($uid);

					$groupid = $info['groupid'];

					if (in_array($groupid, $_set['GROUPS'])) {

						if ($u == 1) {

							$result = DB::query("UPDATE " . DB::table('cloud_wx') . " SET uid='$uid' WHERE wx='$fu'");

							return "绑定成功！";
						} else {

							DB::insert('cloud_wx', array(
								'wx' => $fu,
								'uid' => $uid,
								'act' => 'bind',
								'step' => 0,
								'mark' => '',
								'much' => 1,
								'start' => time(),
								'time' => time(),
							));

							return "绑定成功！";
						}
					} else {

						return "你所在的用户组暂无法绑定！";
					}
				}
			}
		}
	}

}