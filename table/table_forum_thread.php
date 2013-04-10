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

class table_forum_thread {
	function search($allow,$keyword){
		global $_G;
		if($_G['charset'] != 'utf8'){
			$keyword=	iconv('utf-8',$_G['charset'],$keyword);
		}
		$num = count($allow);
		$sql = "";
		for($i = 0; $i < $num; $i++){
			$sql .= ",$allow[$i]";
		}
		$sql	=	$sql."  ";
		$sql	=	mb_substr($sql, 1, -1, 'utf-8');
		$content = DB::fetch_all("SELECT * FROM ".DB::table('forum_thread')." WHERE subject LIKE '%$keyword%' AND displayorder=0 AND closed=0 AND fid IN ($sql)  ORDER BY lastpost DESC");
		$much	=	count($content);
		if($much == 0){
			return "找不到相关内容哦，你可以访问本站进行详细搜索。".$_G['setting']['siteurl'];
		}elseif($much == 1){
			$data	=	array();
			$data[0]['title']	=	$this->charset($content[0]['subject']);
			if(empty($content[0]['subject'])) $data[0]['title'] = "来自本站广播：\n";
			$data[0]['intro']	=	$this->get_intro($content[0]['tid']);
			$data[0]['url']	=	$this->get_url('forum_viewthread',$content[0]['tid']);
			$data[0]['pic']	=	$this->get_cover($content[0]['tid'],$content[0]['attchment']);
			return $data;
		}elseif($much == 2){
			$data	= $this->format($content,2);			
			return $data;
		}elseif($much == 3){
			$data	= $this->format($content,3);
			return $data;
		}elseif($much > 4){
			$data	= $this->format($content,4);
			return $data;
		}
	}
	
	function format($arr,$num){
		$ids = array_rand($arr,$num);
		$data	=	array();
			for($i=0;$i<$num;$i++){
				$data[$i]['title']	=	$this->charset($arr[$ids[$i]]['subject']);
				if(empty($arr[$ids[$i]]['subject'])) $data[$i]['title'] = "来自本站广播：\n";
				$data[$i]['intro']	=	$this->get_intro($arr[$ids[$i]]['tid']);
				$data[$i]['url']	=	$this->get_url('forum_viewthread',$arr[$ids[$i]]['tid']);
				$data[$i]['pic']	=	$this->get_cover($arr[$ids[$i]]['tid'],$arr[$ids[$i]]['attchment']);
			}
		return $data;
	}
	
	function get_cover($tid,$attnum){
			$cover = C::t('#cloud_wx#forum_threadimage')->get_cover($tid);
			if($cover == false){
				global $_G;
				if($attnum > 0){
					$atpic	=	C::t('#cloud_wx#forum_attachment')->get_att($tid);
					if($atpic == false){
						return	$_G['setting']['siteurl']."/source/plugin/cloud_wx/template/images/no_cover.png";
					 	//赋值默认封面
					}else{
						return	$atpic;
						//赋值附件图片
					}
				}else{
					return	$_G['setting']['siteurl']."/source/plugin/cloud_wx/template/images/no_cover.png";
					 //赋值默认封面
				}
			}else{
				return	$cover; 
				//赋值封面图片
			}
	}

	function get_intro($tid) {
		global $_G;
		$content = DB::fetch_first("SELECT * FROM ".DB::table('forum_post')." WHERE tid = '$tid' ");
		$a	=	$this->charset($content['message']);
		$intro = mb_substr($a, 0, 150, 'utf-8');
		$intro	=	preg_replace('/\[(.+?)\]/e','',$intro);
		$order   = array("\r\n", "\n", "\r", "http://");   
		$intro=str_replace($order, '', $intro);
		return trim($intro);
	}
	
	function get_post($tid) {
		require_once libfile('function/discuzcode');
		$content = DB::fetch_first("SELECT * FROM ".DB::table('forum_post')." WHERE tid = '$tid' AND first ='1' ");
		$content['subject']	=	$this->charset($content['subject']);
		$content['message']	=	$this->charset($content['message']);
		$content['author']	=	$this->charset($content['author']);
		$content['message']	=	discuzcode($content['message'], 0, 0, 0, 1, 1, 1, 1, 0, 0, 0,'1');
		$content['avatar']	=	$this->avatar($content['authorid'],'small');
		$content['aid'] = preg_match_all('/[attach](.*)[\/attach]/',$data,$str);
		$fid	=	$content['fid'];
		$content['from']	=	$this->forum_from($fid);
		if($content['invisible'] < 0){
			$content['subject']	=	"THIS CONTENT IS CLOSED!";
			$content['message']	=	"MAYBE IT'S HAS SOME BORING THING INSIDE. CLICK THE BUTTON AT BOTTOM CENTER AND TRY AGAIN. : )";
		}
		$rand	=	DB::fetch_all("SELECT * FROM ".DB::table('forum_thread')." WHERE fid = '$fid' AND closed = '0' AND price = '0' ");
		$id	=	array_rand($rand);
		$content['rand']	=	$rand[$id]['tid'];
		return $content;
		
	}
	
	function get_url($mod,$id){
		global $_G,$_set;
		$_set = $_G['cache']['plugin']['cloud_wx'];
		$in	=	in_array($mod,$_G['setting']['rewritestatus']);
		switch($mod){
			case 'forum_viewthread':
				if($_set['MVIEW'] == 1){
					return $_G['setting']['siteurl']."/plugin.php?id=cloud_wx&tid=".$id;
				}else{
					if($in){
						$rule	=	$_G['setting']['rewriterule'][$mod];
						$url	=	str_replace("{tid}",$id,$rule);
						$url	=	str_replace("{page}",'1',$url);
						$url	=	str_replace("{prevpage}",'1',$url);
						return $_G['setting']['siteurl']."/".$url;
					}else{
						return $_G['setting']['siteurl']."/forum.php?mod=viewthread&tid=".$id;
					}
				}

		}
		
	}
	
	
	function charset($a){
		global $_G;
		if($_G['charset'] != 'utf8'){
			$u_a	=	iconv($_G['charset'],'utf-8',$a);
			return $u_a;
		}else{
			return $a;
		}
	}
	function forum_from($fid){
		$info	=	DB::fetch_first("SELECT * FROM ".DB::table('forum_forum')." WHERE fid = $fid");
		if($info['type'] == 'group'){
			$from	=	"群组";
		}else{
			$from	=	"板块";
		}
			$name	=	$this->charset($info['name']);
		return $name	."  ".$from;
	}
	
	function avatar($uid, $size = 'middle', $returnsrc = FALSE, $real = FALSE, $static = FALSE, $ucenterurl = '') {
        global $_G;
        static $staticavatar;
        if($staticavatar === null) {
                $staticavatar = $_G['setting']['avatarmethod'];
        }

        $ucenterurl = empty($ucenterurl) ? $_G['setting']['ucenterurl'] : $ucenterurl;
        $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
        $uid = abs(intval($uid));
        if(!$staticavatar && !$static) {
                return $returnsrc ? $ucenterurl.'/avatar.php?uid='.$uid.'&size='.$size : '<img src="'.$ucenterurl.'/avatar.php?uid='.$uid.'&size='.$size.($real ? '&type=real' : '').'" />';
        } else {
                $uid = sprintf("%09d", $uid);
                $dir1 = substr($uid, 0, 3);
                $dir2 = substr($uid, 3, 2);
                $dir3 = substr($uid, 5, 2);
                $file = $ucenterurl.'/data/avatar/'.$dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).($real ? '_real' : '').'_avatar_'.$size.'.jpg';
                return $returnsrc ? $file : '<img src="'.$file.'" onerror="this.onerror=null;this.src=\''.$ucenterurl.'/images/noavatar_'.$size.'.gif\'" />';
        }
}

	function pub_post($fu,$uid,$msg){
		
		$info = C::t('#cloud_wx#common_member')->get_user($uid);

		if($info == false) {
			return "您在本站的账号状态异常！";
		}else{
			$username = $info['username'];
			$msg	.= "   ";
	
			$say	=	mb_substr($msg, 3, -1, 'utf-8');
	
			$len	=	mb_strlen(trim($say),'UTF8'); 
	
			if($len < 6){
				return "亲，多吐点儿槽吧，字数太短了哦~";
			}
	
			$check	=	C::t('#cloud_wx#cloud_wx')->rt_info($fu,"pub_post");
	
			if($check == false){
	
				return "亲，您的操作太快了。。。";
	
			}
	
			require_once DISCUZ_ROOT.'./config/config_ucenter.php';

			if(UC_DBCHARSET != 'utf8')	$say = iconv('utf-8',UC_DBCHARSET,$say);
			$say		=	daddslashes($say);

			$time	=	$_SERVER['REQUEST_TIME'];

			DB::insert('forum_thread', array(
								'fid' => 80,
								'posttableid'	=>	0,  
								'typeid' 	=>	0,
								'sortid'  	=>	0,
								'readperm'  	=>	0,
								'price' 	=>	0,	
								'author'	=>	$username,
								'authorid '	=>	$uid,
								'subject'		=>	$say,

								'dateline'	=>	$time,
								'lastpost'	=>	$time,
								'lastposter'	=>	$username,
								'views'			=>	0,
								'replies'		=>	0,
								'displayorder'	=>	0,
								'highlight'	=>	0,
								'digest'		=>	0,
								'rate'			=>	0,
								'special'		=>	0,
								'attachment'	=>	0,
								'moderated'	=>	0,
								'closed'		=>	0,
								'stickreply'	=>	0,
								'recommends'		=>	0,
								'recommend_add'	=>	0,
								'recommend_sub'	=>	0,
								'heats'		=>	0,
								'status'		=>	512,
								'isgroup'	=>	0,
								'favtimes'	=>	0,
								'sharetimes'	=>	0,
								'stamp'	=>	-1,
								'icon'		=>	20,
								'pushedaid'		=>	0,
								'cover'		=>	0,
								'replycredit'		=>	0,
								'relatebytag'	=>	0,
								'maxposition'		=>	0,
								));

			return "发布成功！";

		}

	}

}