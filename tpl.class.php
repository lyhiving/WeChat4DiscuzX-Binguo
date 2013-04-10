<?php
/**
 *	[公众微信智能云平台(cloud_wx.{})] (C)2013-2099 Powered by YangLin.
 * Author QQ:28945763  问题解答技术交流QQ群：294440459
 *	Version: 5.5       http://i.binguo.me
 *	Date: 2013-4-4 00:00
 */

class tpl {
 	function txt_msg($fu,$tu,$content,$flag = 0){
		$tpl	=	"<xml> 
				<ToUserName><![CDATA[".$fu."]]></ToUserName> 
				<FromUserName><![CDATA[".$tu."]]></FromUserName> 
				<CreateTime>".$_SERVER['REQUEST_TIME']."</CreateTime> 
				<MsgType><![CDATA[text]]></MsgType> 
				<Content><![CDATA[".$content."]]></Content> 
				<FuncFlag>".$flag."</FuncFlag>
				</xml>";
		echo $tpl;
	}
	
	function pic_msg($fu,$tu,$data,$flg = 0){
		$num	=	count($data);
		if($num > 1){
			$add = $this->news_add($data);
			$tpl = " <xml>
					 <ToUserName><![CDATA[".$fu."]]></ToUserName>
					 <FromUserName><![CDATA[".$tu."]]></FromUserName>
					 <CreateTime>".$_SERVER['REQUEST_TIME']."</CreateTime> 
					 <MsgType><![CDATA[news]]></MsgType> 
					 <Content><![CDATA[%s]]></Content> 
					 <ArticleCount>".$num."</ArticleCount> 
					 <Articles> 
					 ".$add."
					 </Articles> 
					 <FuncFlag>".$flag."</FuncFlag> 
					 </xml> ";
			echo $tpl;
		}else{
			$tpl = " <xml>
					 <ToUserName><![CDATA[".$fu."]]></ToUserName>
					 <FromUserName><![CDATA[".$tu."]]></FromUserName>
					 <CreateTime>".$_SERVER['REQUEST_TIME']."</CreateTime> 
					 <MsgType><![CDATA[news]]></MsgType> 
					 <Content><![CDATA[%s]]></Content> 
					 <ArticleCount>1</ArticleCount> 
					 <Articles> 
					 <item> 
					 <Title><![CDATA[".$data[0]['title']."]]></Title> 
					 <Description><![CDATA[".$data[0]['intro']."]]></Description> 
					 <PicUrl><![CDATA[".$data[0]['pic']."]]></PicUrl> 
					 <Url><![CDATA[".$data[0]['url']."]]></Url> 
					 </item>
					 </Articles> 
					 <FuncFlag>".$flag."</FuncFlag> 
					 </xml> ";
			echo $tpl;
		}
	}
	
	function news_add($data){
		$add	=	"";
			foreach ($data as $k){
			$add	.= "<item> 
				 <Title><![CDATA[".trim($k['title'])."]]></Title> 
				 <Description><![CDATA[".trim($k['intro'])."]]></Description> 
				 <PicUrl><![CDATA[".trim($k['pic'])."]]></PicUrl> 
				 <Url><![CDATA[".trim($k['url'])."]]></Url> 
				 </item>  ";
			}
			return $add;
	}
	
	function audio_msg($fu,$tu,$data){
		$tpl	=	"<xml>
					 <ToUserName><![CDATA[".$fu."]]></ToUserName>
					 <FromUserName><![CDATA[".$tu."]]></FromUserName>
					 <CreateTime>".$_SERVER['REQUEST_TIME']."</CreateTime>
					 <MsgType><![CDATA[music]]></MsgType>
					 <Music>
					 <Title><![CDATA[".$data['title']."]]></Title>
					 <Description><![CDATA[".$data['intro']."]]></Description>
					 <MusicUrl><![CDATA[".$data['url']."]]></MusicUrl>
					 <HQMusicUrl><![CDATA[".$data['hqurl']."]]></HQMusicUrl>
					 </Music>
					 <FuncFlag>0</FuncFlag>
					 </xml>";
		echo $tpl;
	}
	
}
