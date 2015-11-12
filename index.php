<?php

require_once("get_review.php");
 
//define your token
define("TOKEN", "dadalovejj");


$wechatObj = new wechatCallbackapiTest();

if(isset($_GET["echostr"])){
	$wechatObj->valid();
}else{
    
	$wechatObj->responseMsg();
}

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
				$event = $postObj->Event;
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[text]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>"; 
				//一条图文消息的模版
          		$newsTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
							<Articles>
							<item>
							<Title><![CDATA[%s]]></Title>
							<Description><![CDATA[%s]]></Description>
							<PicUrl><![CDATA[%s]]></PicUrl>
							<Url><![CDATA[%s]]></Url>
							</item>
							</Articles>
							<FuncFlag>0</FuncFlag>
							</xml>";
				//帮助信息
				//【QQ】查看最热QQ新闻 \n【新浪】查看最热新浪新闻 \n【sports】查看最热体育新闻 \n \n【网易】或【163】查看网易最新热帖 \n----------\n请发送括号中的汉字即可，忘记指令请发送【帮助】查看可用指令
				$help = "发送：\n【1】查看网易最热十帖 ";
				
				
				if(!empty($event))
                {
                    $contentStr = "欢迎关注热门新闻评论排行榜！\n\n你可以".$help;
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $contentStr);
                	echo $resultStr;
                }

				if(!empty( $keyword ))
                {
                    switch($keyword)
                    {
                        case "wangyi":
                        	/*$contentPicUrl ="http://hotnews-hotnewsstorage.stor.sinaapp.com/wangyi.png";
                       	    $contentUrl="http://news.163.com/rank/";
                    		$resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, "网易热门新闻","",$contentPicUrl,$contentUrl);
                    		echo $resultStr;*/
							$msgType = "text";
							$hot_post_result = array();
							get_review_news($hot_post_result);
							
							$result = "【网易最热评论新闻】\n  ".$hot_post_result["hit_count"]."℃  主题:".$hot_post_result["news_title"]."\n\n";
							
							//$hot_post_result["hot_post_count"]
							for($i=0; $i < $hot_post_result["hot_post_count"] ; $i++)
							{
								for($j=0;$j < count($hot_post_result["data".$i]);$j++)
								{
                                    //评论作者是$hot_post[$i][$j]["n"]或["f"]
									if(array_key_exists("author",$hot_post_result["data".$i][$j]))
									{
										//评论作者
                                        $result = $result.($i+1)."楼".($j+1)."房 ".$hot_post_result["data".$i][$j]["author"];
									}
									if(array_key_exists("vote",$hot_post_result["data".$i][$j]))
									{
										$result = $result." | 赞[".$hot_post_result["data".$i][$j]["vote"]."]";
									}
									//精彩评论
									$result = $result."\n =>".$hot_post_result["data".$i][$j]["review"]."\n";

								}
                                
                                $result = $result."\n";
							}
							
							//$result = $result."\n有任何建议请您拍砖，小贱力求做到有问必答!";
	
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $result);
                    		echo $resultStr;
                         	break;
						case "网易":
						case "163":
							$contentPicUrl ="http://mmsns.qpic.cn/mmsns/Ht5GG5iaSBKty5ROibut8RZ5nibB26SjjWiclrwwiaHIdQIUNTT5aCAnEnw/0";
                       	    $contentUrl="http://hotnews.sinaapp.com/163_htm.php";
                    		$resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, "网易评论盛大首发：无评论，不新闻！","无评论，不新闻！在《热门新闻评论排行榜》，看更“好看”的网易评论，在这里体验和初恋在一起的感觉！有任何建议可以轻拍小贱，小贱力求有问必答！",$contentPicUrl,$contentUrl);
                    		echo $resultStr;
							break;
						case "1":
						case "2":
						case "3":
						case "4":
						case "5":
						case "6":
						case "7":
						case "8":
						case "9":
						case "10":
							$contentPicUrl ="http://mmsns.qpic.cn/mmsns/Ht5GG5iaSBKty5ROibut8RZ5nibB26SjjWiclrwwiaHIdQIUNTT5aCAnEnw/0";
                       	    $contentUrl="http://hotnews.sinaapp.com/163_htm.php?hot_id=".$keyword;
                    		$resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, "网易评论盛大首发：无评论，不新闻！","无评论，不新闻！在《热门新闻评论排行榜》，看更“好看”的网易评论，在这里体验和初恋在一起的感觉！有任何建议可以轻拍小贱，小贱力求有问必答！",$contentPicUrl,$contentUrl);
                    		echo $resultStr;
							break;
                        case "qq":
						case "QQ":
                        	$contentPicUrl ="http://hotnews-hotnewsstorage.stor.sinaapp.com/qqnews.jpg";
                       	    $contentUrl="http://news.qq.com/paihang.htm";
                    		$resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, "QQ热门新闻【开发中】","",$contentPicUrl,$contentUrl);
                    		echo $resultStr;
                         	break;
                        case "新浪":
                        	$contentPicUrl ="http://hotnews-hotnewsstorage.stor.sinaapp.com/sina.jpg";
                       	    $contentUrl="http://news.sina.com.cn/hotnews/";
                    		$resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, "新浪热门新闻【开发中】","",$contentPicUrl,$contentUrl);
                    		echo $resultStr;
                         	break;
						case "体育":
                        	$contentStr = "体育新闻正在开发中";
                    		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time,  $contentStr);
                    		echo $resultStr;
                         	break;
                        case "m":
                        case "M":
                        case "汇率":
                        	
                        	$msgType = "text";
							
							// 获取新浪财经招行页面汇率
                            $pcode = file_get_contents("http://stock.finance.sina.com.cn/forex/api/openapi.php/ForexService.getAllBankForex");
                            if($pcode == false)
                            {
                                die("无法实时获取最热新浪人民币外汇牌价！");
                            }
                            
                            $bank=json_decode($pcode,true);
                            if($bank == false)
                            {
                                die("json格式编码错误！");
                            }
                            //icbc 工行   boc 中行  abchina 农行   bankcomm 交行 ccb 建行
                            $raw_bank_data = $bank["result"]["data"];
                        
							$result = "【最新汇率信息】\n";
							
							$result = $result."招行  ".$raw_bank_data["cmbchina"]["0"]["xh_buy"]."\n";
                            $result = $result."工行  ".$raw_bank_data["icbc"]["0"]["xh_buy"]."\n";
                            $result = $result."中行  ".$raw_bank_data["boc"]["0"]["xh_buy"]."\n";
                            $result = $result."农行  ".$raw_bank_data["abchina"]["0"]["xh_buy"]."\n";
                            $result = $result."交行  ".$raw_bank_data["bankcomm"]["0"]["xh_buy"]."\n";
                            $result = $result."建行  ".$raw_bank_data["ccb"]["0"]["xh_buy"]."\n";
	
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $result);
                    		echo $resultStr;
                         	break;
						case "帮助":
							$msgType = "text";
							$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $help);
                    		echo $resultStr;
                         	break;
                        default:
                    		$contentStr = "指令【".$keyword."】未定义，请输入【帮助】查看如何使用.";
                    		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $contentStr);
                    		echo $resultStr;
    						break;
                    }
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
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

?>
