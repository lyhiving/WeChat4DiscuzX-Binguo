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
class table_cloud_wx_pic {
	
	function pub_pic($wx,$uid,$url){
		$url	=	trim($url);
		//以下为异步处理，防止服务器处理超时而无法回复消息。
		$this->savedata($url,$wx,$uid);
	}

	
	function savedata($url,$wx,$uid){
		global $_G;
		$urls = $this->savepic($url,$uid);
		$big_pic	=	$urls['big'];
		$small_pic	=	$urls['small'];
		require_once DISCUZ_ROOT.'./config/config_ucenter.php';
		$title	=	"来自微信平台用户分享";
		if(UC_DBCHARSET != 'utf8')	$title = iconv('utf-8',UC_DBCHARSET,$title);
		//存入pic表
		DB::insert('cloud_wx_pic', array(
								'uid'	=> $uid,
								'title' => $title,
								'cover' =>	$small_pic,
								'pic'	=> "<img src=\"".$big_pic."\"  />",
								'status'	=>	1,
								'user'	=>	$wx,
								'add_time'	=> $_SERVER['REQUEST_TIME'],
								));
		$newID = mysql_insert_id();
		//将ID写入mark
		C::t('#cloud_wx#cloud_wx')->rt_info($wx,'pub_pic',1,$newID);
		
	}
	function savepic($url,$uid){
		$urls = $this->get_image_byurl($url,$uid);
		return $urls;
	}
	
	function savethumb($filename,$thumbpic){
		require_once DISCUZ_ROOT.'./source/plugin/cloud_wx/thumb.class.php';
		$thumb = new Thumbnail(1, 200, 600);
		$result = $thumb->createThumbnail(DISCUZ_ROOT.'./'.$filename,DISCUZ_ROOT.'./'.$thumbpic);
		if($result === false){
			return $filename;
		}else{
			return  $thumbpic;
		}
	}
	
	function get_image_byurl($url,$uid,$filename="") {
		$dir	=	"source/plugin/cloud_wx/uploads/".date("Ymd")."/";
		if(!is_dir($dir)) mkdir($dir,0777,true);
		if ($url == "") { 
			exit; 	
		}
		$ext = strrchr($url, ".");  //得到图片的扩展名
		if($ext != ".gif" && $ext != ".jpg" && $ext != ".png") { $ext = ".jpg"; }
		if($filename == "") { $filename = $dir.$uid."-".date("YmdHis"). $ext; }  
		$thumb_path		=	$dir.$uid."-".date("YmdHis")."_s". $ext;
		//以时间另起名，在此可指定相对目录 ，未指定则表示同php脚本执行的当前目录
		//以流的形式保存图片
		ob_start();   
		readfile($url);   
		$img = ob_get_contents();   
		ob_end_clean();   
		$size = strlen($img);  
		$fp2=@fopen($filename, "a");   
		fwrite($fp2,$img);   
		fclose($fp2); 
		sleep(2);
		$thumburl	=	$this->savethumb($filename,$thumb_path);
		$data	=	array();
		$data['small']	=	$thumburl;
		$data['big']	=	$filename;
		return $data;  //返回文件数组
	
	}
	
	function describe($wx,$msg){
		$msg	.= "   ";
		$msg=	mb_substr($msg, 3, -1, 'utf-8');
		$len	=	mb_strlen(trim($msg),'UTF8');
		if($len < 6) return "字数太少了亲~~";
		$info	=	C::t('#cloud_wx#cloud_wx')->get_info($wx);
		//if(empty($info['uid']) || ($info == false)) {
		//	return "请先绑定帐号，输入指令：@绑定 用户名 密码";
		//}else{
			if(empty($info['mark']) || (!is_numeric($info['mark'])) || ($info['act'] != 'pub_pic')){
				return "请先发送您要分享的图片！";
			}else{
				if((time() - $info['time']) > 600){
					C::t('#cloud_wx#cloud_wx')->rt_info($wx);
					return "操作超时，请重新分享图片！";
				}else{
					$id	=	$info['mark'];
					require_once DISCUZ_ROOT.'./config/config_ucenter.php';
					if(UC_DBCHARSET != 'utf8')	$msg = iconv('utf-8',UC_DBCHARSET,$msg);
					DB::query("UPDATE ".DB::table('cloud_wx_pic')." SET title = '$msg' WHERE id = '$id'");
					C::t('#cloud_wx#cloud_wx')->rt_info($wx);
					$data = $this->view($id);
					global $_G;
					$arr[0]['title']	=	@iconv(UC_DBCHARSET,'utf-8',$data['title']);
					$arr[0]['pic']	=	$_G['setting']['siteurl']."/".$data['cover'];
					$arr[0]['url']	=	$_G['setting']['siteurl']."/plugin.php?id=cloud_wx:look&p=".$id;
					$arr[0]['intro']	=	"描述成功！，点击预览。";
					return $arr;
				}
			}
		//}
	} 

	
	
	function view($id){
		$data = DB::fetch_first("SELECT * FROM ".DB::table('cloud_wx_pic')." WHERE id='$id' AND status=1 ");
		return $data;
	}
	function rand_($num = 4){
		$data = DB::fetch_all("SELECT * FROM ".DB::table('cloud_wx_pic')." WHERE status=1 LIMIT $num ");
		return $data;
	}
	function pnid($id){
		$data = array();
		$data[0]	=	DB::fetch_first("SELECT id FROM ".DB::table('cloud_wx_pic')." WHERE  status=1 AND id < $id ORDER BY id DESC LIMIT 1");
		$data[1]	=	DB::fetch_first("SELECT id FROM ".DB::table('cloud_wx_pic')." WHERE  status=1 AND id > $id ORDER BY id ASC LIMIT 1");
		return $data;
	}
	


}