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
include ('tpl.class.php');
$API = new wxapi;
$API->callme();

class wxapi {
	function __construct(){
		if(!empty($_GET['tid']) && is_numeric($_GET['tid'])){
			$tid	=	abs($_GET['tid']);
			//因为带:冒号的链接 微信无法完整识别
			header("location: plugin.php?id=cloud_wx:view&tid=$tid");
		}
		if(!empty($_GET['redirect'])){
			$redirect_url	=	urldecode($_GET['redirect']);
			header("location:".$redirect_url);
		}
	}

	public function callme(){ 
		global $_G,$_set;
		$tpl = new tpl;
		if(!isset($_G['cache']['plugin'])){
				loadcache('plugin');
		}
		$help = "输入命令 （空格）参数进行相应操作：\n @帖子 关键词 进行搜索\n @绑定 账号 密码 进行账号绑定\n @解绑 即可解除账号绑定\n @广播 内容 进行发布广播操作* \n @消息 查看收到的未读消息*\n@搜图 关键词 进行图片搜索 \n @听听 歌曲名 收听喜欢的音乐\n 直接发送图片即可将图片分享到本站 \n 带*号的需绑定账号，输入@帮助 返回本说明。";
		$_set = $_G['cache']['plugin']['cloud_wx'];
		$_set['GROUPS'] = (array)unserialize($_set['GROUPS']);
		$_set['FORMS'] = (array)unserialize($_set['FORMS']);
		$_set['PLUS'] = (array)unserialize($_set['PLUS']);
		$_set['OTHER'] = (array)unserialize($_set['OTHER']);
		if($_G['charset'] !== 'utf8'){
			$site	=	iconv($_G['charset'],'utf-8',$_G['setting']['sitename']);
			$welcome	=	iconv($_G['charset'],'utf-8',$_set['WELCOME']);
		}else{
			$site	=	$_G['setting']['sitename'];
		}
		
		//检查总开关
		if($_set['ORC'] == 1){			
			//处理微信发送来的内容
			if(function_exists('file_get_contents')){
				$postStr = file_get_contents("php://input");
			}else{
				$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
			}
			//extract post data
			if (!empty($postStr)){
					$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
					$fu = $postObj->FromUserName."";
					$tu = $postObj->ToUserName."";
					$type	=	$postObj->MsgType;
					$time = $postObj->CreateTime;
					
					if($type == 'text'){
						//文本消息
						$msg = trim($postObj->Content);
						//截取字符判断是否为命令
						$keyword	=	trim(mb_substr($msg, 0, 3, 'utf-8'));
							if($keyword == '@帮助'){
								$tpl->txt_msg($fu,$tu,$help);
							}elseif(($keyword == '@帖子') || ($keyword == '@贴子')){
								$msg	.= "  ";
								$word	=	trim(mb_substr($msg, 3, -1, 'utf-8'));
								$data = C::t('#cloud_wx#forum_thread')->search($_set['FORMS'],$word);
								if(is_array($data)){
									$tpl->pic_msg($fu,$tu,$data);
								}else{
									$tpl->txt_msg($fu,$tu,$data);
								}
							}elseif($keyword == '@广播'){
								$this->check(1,$_set['PLUS'],$fu,$tu);
								$user = C::t('#cloud_wx#cloud_wx')->get_info($fu);
								if(($user == false) || (empty($user['uid']))){
									$tpl->txt_msg($fu,$tu,"您还没绑定会员账号，请发送指令 进行绑定，示例：@绑定 用户名 密码");
								}else{
									$a = C::t('#cloud_wx#home_doing')->pub_say($fu,$user['uid'],$msg);
									$tpl->txt_msg($fu,$tu,$a);
								}
						
							}elseif($keyword == '@消息'){
								$this->check(2,$_set['PLUS'],$fu,$tu);
								$user = C::t('#cloud_wx#cloud_wx')->get_info($fu);
								if(($user == false) || (empty($user['uid']))){
									$tpl->txt_msg($fu,$tu,"您还没绑定会员账号，请发送指令 进行绑定，示例：@绑定 用户名 密码");
								}else{
									$a = C::t('#cloud_wx#ucenter_pm_lists')->get_msg($user['uid']);
									$tpl->txt_msg($fu,$tu,$a);
								}
							}elseif($keyword == '@绑定'){
								$data	=	explode(" ",$msg);
								$a = C::t('#cloud_wx#cloud_wx')->bind($fu,$tu,$data[1],$data[2]);
								$tpl->txt_msg($fu,$tu,$a);
							}elseif($keyword == '@命令'){
								$tpl->txt_msg($fu,$tu,$help);
							}elseif($keyword == '@搜图'){
								$this->check(1,$_set['OTHER'],$fu,$tu);
								$msg	.= "  ";
								$findme	=	trim(mb_substr($msg, 3, -1, 'utf-8'));
								if(!empty($findme)){
									$url	=	"http://open.binguo.me/api.php?type=image&word=".$findme."&num=4";
									$a = file_get_contents($url);
									$a		=	str_replace('<strong>','',$a);
									$a		=	str_replace('<\/strong>','',$a);
									$arr	=	json_decode($a,true);									
									if(empty($arr)){
										$tpl->txt_msg($fu,$tu,"找不到相关结果哦，换个关键词试试。");
										exit;
									}
									$tpl->pic_msg($fu,$tu,$arr);						
								}else{
									$tpl->txt_msg($fu,$tu,"请输入关键词");
								}
								
							}elseif($keyword == '@听听'){
								$this->check(2,$_set['OTHER'],$fu,$tu);
								$msg	.= "  ";
								$findme	=	trim(mb_substr($msg, 3, -1, 'utf-8'));
								if(!empty($findme)){
									$url	=	"http://open.binguo.me/api.php?type=music&word=".$findme;
									$a = file_get_contents($url);
									$b	=	json_decode($a,true);
									$murl	=	array();
									$murl['title'] = $b['title'];
									$murl['url']	=	$b['url'];
									$murl['hqurl']	=	$b['url'];
									$murl['intro']	=	$b['intro'];
									if(empty($murl['title'])){
										$tpl->txt_msg($fu,$tu,"找不到相关结果哦，换个关键词试试。");
										exit;
									}
									$tpl->audio_msg($fu,$tu,$murl);							
								}else{
									$tpl->txt_msg($fu,$tu,"请输入你要听的歌曲名称或关键词");
								}
							}elseif($keyword == '@看看'){
								if($_set['SHAREIMG'] > 0){
									//查看分享的图片
									$data = C::t('#cloud_wx#cloud_wx_pic')->rand_(4); //返回四条
									foreach($data as $k=>$v){
										$data[$k]['title']	=	iconv('gbk','utf-8',$data[$k]['title']);
										$data[$k]['pic']	=	$_G['setting']['siteurl']."/".$data[$k]['cover'];
										$data[$k]['url']	=	$_G['setting']['siteurl']."/plugin.php?id=cloud_wx:look&p=".$data[$k]['id'];
									}
									$tpl->pic_msg($fu,$tu,$data);
								}
							}elseif($keyword == '@描述'){
								if($_set['SHAREIMG'] == 2){
									$data	=	C::t('#cloud_wx#cloud_wx_pic')->describe($fu,$msg);
									if(is_array($data)){
										$tpl->pic_msg($fu,$tu,$data);
									}else{
										$tpl->txt_msg($fu,$tu,$data);
									}
								}else{
									$tpl->txt_msg($fu,$tu,"图片分享功能已关闭。");
								}
							}elseif($keyword == '@解绑'){
								$user = C::t('#cloud_wx#cloud_wx')->get_info($fu);
								if(($user == false) || (empty($user['uid']))){
									$tpl->txt_msg($fu,$tu,"您还没绑定会员账号,无需解绑。");
									exit;
								}else{
									C::t('#cloud_wx#cloud_wx')->bind_qx($fu);
									$tpl->txt_msg($fu,$tu,"解除成功！");
									exit;
								}
							}else{
								//如果开启了智能聊天
								if($_set['TALK'] == 1){
									$user = $_set['ACCOUNT']; //自己的账号 open.binguo.me 输入则优先搜索个人教育的回复
									$json = $this->api($msg,$user); //获取json结果
									$arr = json_decode($json,true); //json 转换成数组
									if($arr['tpl'] == 'text'){
										$tpl->txt_msg($fu,$tu,$arr['msg']);
									}elseif($arr['tpl'] == 'audio'){
										$info['title']	=	$arr['title'];
										$info['url']	=	$arr['msg'];
										$info['intro']	=	$arr['intro'];
										$info['hqurl']	=	$arr['msg'];
										$tpl->audio_msg($fu,$tu,$info);
									}elseif($arr['tpl'] == 'news'){
										$tpl->pic_msg($fu,$tu,$arr);
									}else{
										echo $json;
									}
								// 如果没开启则执行
								}else{
									$data = C::t('#cloud_wx#forum_thread')->search($_set['FORMS'],$msg);
									if(is_array($data)){
										$tpl->pic_msg($fu,$tu,$data);
									}else{
										$tpl->txt_msg($fu,$tu,$data);
									}
								}
						}
						//text end
					}elseif($type == 'image'){
						//图片交换 增强互动
						if($_set['SHAREIMG'] == 2){
							$user = C::t('#cloud_wx#cloud_wx')->get_info($fu);
							if($_set['ONLY'] == 1){
								if(($user == false) || (empty($user['uid']))){
									$tpl->txt_msg($fu,$tu,"您还没绑定会员账号，请发送指令 进行绑定，示例：@绑定 用户名 密码");
									exit;
								}
							}
							if(empty($user['uid'])) $user['uid'] = 0;
							$pic	=	$postObj->PicUrl." ";
							$tpl->txt_msg($fu,$tu,"分享成功！为照片添加文字描述吧，请发送：@描述 图片文字介绍内容",1);
							C::t('#cloud_wx#cloud_wx_pic')->pub_pic($fu,$user['uid'],$pic);
						}else{
							$tpl->txt_msg($fu,$tu,'功能暂未开放！');
						}
					}elseif($type == 'event'){
						$event = $postObj->Event;
						if($event == 'subscribe'){
							if(empty($welcome)) $welcome = "欢迎关注\n ".$site."\n ".$help;
							$tpl->txt_msg($fu,$tu,$welcome);
						}else{
							$tpl->txt_msg($fu,$tu,"暂时无法提供支持。");
						}
					}

					
			}else{
				$this->valid();
			}
			//if (!empty($postStr)) end
		}else{
			//总开关 关闭
			exit;
		}
	}
	
	//聊天机器人调用方法
	function api($say,$user = '',$type = 'robot'){
	   $url = "http://open.binguo.me/api.php";  
	   $post_data = array (
		   "type" => $type,
		   "say" => $say,  
		  "user" => $user,   
	   );  
	   $ch = curl_init();  
	   curl_setopt($ch, CURLOPT_URL, $url);  
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
		 
	   curl_setopt($ch, CURLOPT_POST, 1);  
		
	   curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);  
	   $output = curl_exec($ch);  
	   curl_close($ch);  
	   return $output; 
	}

	private function check($a,$ary,$fu,$tu){
		$tpl = new tpl;
		if(!in_array($a,$ary)){
			$tpl->txt_msg($fu,$tu,"功能暂未开启!");
		}
	}
	
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        global $_set;
		$token = $_set['TOKEN'];
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}