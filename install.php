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
require DISCUZ_ROOT.'./config/config_global.php';
$table_a = $_config['db']['1']['tablepre']."cloud_wx";
$table_b = $_config['db']['1']['tablepre']."cloud_wx_pic";
$sql = <<<EOF
CREATE TABLE `$table_a` (
  `wx` varchar(50) character set utf8 NOT NULL,
  `uid` int(11) default NULL,
  `act` varchar(20) character set utf8 default NULL,
  `step` tinyint(1) default NULL,
  `mark` varchar(255) character set utf8 default NULL,
  `much` tinyint(3) default NULL,
  `start` char(14) character set utf8 default NULL,
  `time` char(14) character set utf8 default NULL,
  PRIMARY KEY  (`wx`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `$table_b` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(8) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1',
  `user` varchar(50) NOT NULL,
  `add_time` char(14) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

EOF;

runquery($sql);
$finish = true;
?>