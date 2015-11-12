<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<?php 
	/*
	指定163新闻页面，通过此函数获取此新闻的最热跟帖
	hot_post_result
	{
		["hit_count"] =>点击次数
		["news_title"] =>新闻标题
		["news_url"] =>新闻url
		["news_content"] =>新闻概要
		["hot_post_count =>热门跟帖数目
		["data0"]=>
			array(1) {
				[0]=>
				array(3) {
				  ["review"]=> 评论内容
				  ["author"]=> 评论作者
				  ["vote"]=> 得票数
				}
			}
		["build_post_count =>精彩盖楼数目
		["build_data0"]=>
			array(1) {
				[0]=>
				array(3) {
				  ["review"]=> 评论内容
				  ["author"]=> 评论作者
				  ["vote"]=> 得票数
				}
			}
	}
	*/
	/*
	$json_file = "review_163.json";
	$hot_post_result = array();
	get_163_review_to_file($hot_post_result,$json_file,1);*/
	
	
	function get_163_review_to_file(&$hot_post_result,$json_file,$hot_id)
	{
		// 163排行榜地址 
		$url = "http://news.163.com/rank/"; 
		// 获取页面代码 
		$pcode = file_get_contents($url); 
		//$pcode = '<h2>跟贴榜</h2><ul class="tabNav"><li class="active">今日跟贴排行</li><li>本周跟贴排行</li><li>';
		//sae_debug( $pcode);
		
		$pcode = iconv("gb2312", "utf-8//IGNORE",$pcode);//把gb2312转utf
		// 设置匹配正则，第一个匹配的就是网易新闻“全站”最热评论
		$preg = '#跟贴数</th></tr>(.*)</table>#isU'; 
		// 进行正则搜索，匹配到第一个全站的评论排行榜即可
		if(preg_match($preg, $pcode, $view))
		{
            /*echo "已找到163热门排行榜的最热新闻位置！匹配到的模板代码是： \r\n<br/>";
			print_r($view[1]);
            echo "\r\n<br/>";*/
		}else
		{
			//echo "can't find rank page @163.com";
			die("在163.com无法找到全站最热评论的位置，可能是模板变化引起的！\r\n<br/>");
		}
		
		/*<td class="red"><span>1</span><a href="http://travel.163.com/13/0531/12/9070KEVU00064L1L.html">优秀保留剧目大奖作品全国巡演好评如潮</a></td>
						<td class="cBlue">39807</td>*/
		//换行符不知道如何表达，若以使用了(.*)来替代，所以第三个参数是没有意义的
		$preg2 = '#<a href="(.*)">(.*)</a></td>(.*)<td class="cBlue">(.*)</td>#isU';
		
		//将163的热点新闻写入到配置文件中
		if(file_exists($json_file)&&is_readable($json_file))
		{
			unlink($json_file);
            //echo "成功删除掉旧的热门新闻评论文件！\r\n<br/>";
		}
		 
		if(preg_match_all($preg2,$view[1],$title))
		{
			// 计算标题数量 
			$count = count($title[1]);
			if($hot_id>$count)
			{
				die("帖子id超出热门新闻数量！可能是有人恶意攻击！");
			}
			/*
            echo " 发现热门新闻条数: ".$count;
			echo "\r\n<br/>";
            echo " 成功获取的热门新闻列表数组如下: ";
			print_r($title);
			echo "\r\n<br/>";*/

			//title[1][x] 是url,title[2][x]是具体标题 ，title[4][x]是具体点击数
            
			/*
			for($x=0;$x < $count; $x++)
			{
                $news_ini = "[".($x+1)."]\r\n	hitcount = ".$title[4][$x]."\r\n	title = ".$title[2][$x]."\r\n	url = ".$title[1][$x]."\r\n";
                //$news_ini = "[".($x+1)."]\r\n	hitcount = ".$title[4][$x]."\r\n	url = ".$title[1][$x]."\r\n";
				//新浪sae不支持加锁FILE_APPEND|LOCK_EX
				if(file_put_contents($json_file,$news_ini,FILE_APPEND) == false)
				{
					die( "读取@163.com新闻评论排行榜到文件时发生错误！\r\n<br/>");
				}
                
                //读出写入到配置文件的项
				//echo $news_ini;
				//echo "</br>\r\n";
			}*/
			
			//获取第$hot_id个热门的那条评论新闻
			$hot_post_result["hit_count"] = $title[4][$hot_id-1];
			$hot_post_result["news_title"] = $title[2][$hot_id-1];
			$hot_post_result["news_url"] = $title[1][$hot_id-1];
			get_163_hot_post($hot_post_result["news_url"],$hot_post_result);
			
			//sae_debug("<br>打印一下获取到的hot_post_result数组：<br>");
			//sae_debug($hot_post_result);
			
			//将获取到的热门新闻评论数据写到json文件中
			if(file_put_contents($json_file,json_encode($hot_post_result,true),FILE_APPEND) == false)
			{
				die( "将163.com新闻评论排行榜写到文件时发生错误！\r\n<br/>");
			}
			
			/*$pcode=file_get_contents($json_file); 
			echo "<br>打印一下从文件中获取到的json格式的hot_post_result数组：<br>";
			var_dump(json_decode($pcode,true));
			*/
            /*
			//读取新闻文件中的取值
			$cfg = parse_ini_file($json_file,true);
			print_r($cfg);*/
		}
		else
		{
			die( "可能是163.com的排行榜模板发生变化，导致无法取出热门排行！");
		}
	}
	
	/*$news_url = 'http://ent.163.com/photoview/00AJ0003/502368.html#p=924KQ10800AJ0003'; 
	$hot_post_result = array();
	get_163_hot_post($news_url,$hot_post_result);
	
	echo "打印一下获取到的hot_post_result数组：<br>";
	var_dump($hot_post_result);*/
	
	
	function get_163_hot_post($news_url,&$hot_post_result)
	{
		// 163新闻地址
		//$news_url = 'http://news.163.com/13/0616/09/91FU0AT00001121M.html'; 
		$arr_url = parse_url($news_url);
		if($arr_url == false)
		{
			die("新闻url地址不合规范！：".$news_url);
		}
		$host = $arr_url['host'];
		//echo $arr_url['host'];
		
		// 获取热门新闻的页面代码，然后从中寻找构造热门跟帖数据的地址 
		$pcode = file_get_contents($news_url); 
		if($pcode == false)
		{
			die("无法实时获取最热新闻页面！");
		}
		//把gb2312转utf
		$pcode = iconv("gb2312", "utf-8//IGNORE",$pcode);
		/*
		<meta name="description" content="帕克得到26分和5次助攻，吉诺比利首发贡献24分和10次助攻，格林投中6个三分球创造了总决赛新纪录，他们带领球队在第四节顶住了热火队的反扑，马刺队主场拿下热火队3-2领先……" />
		*/
		//先获取新闻概要
		$preg_desc = '#meta name="description" content="(.*)"#isU';
		// 进行正则搜索，匹配到第一个就是新闻简要
		if(preg_match($preg_desc, $pcode, $desc_arr) == false)
		{
			die("没有找到新闻概要数据@163，可能是模板变化引起的！\r\n<br/>");
		}
		$hot_post_result["news_content"] = $desc_arr[1];
		//echo "已找到163热门新闻概要： \r\n<br/>";
		//print_r($desc_arr[1]);
       // echo "\r\n<br/>";
		
		//热门跟帖的正则表达式
		$preg = '#threadId = "(.*)",(.*)boardId = "(.*)",#isU'; 
		// 进行正则搜索，匹配到第一个就是threadid，第三个是boardId
		if(preg_match($preg, $pcode, $hot_addr))
		{
			//sae_debug("已找到163的评论replyData页面构造格式！ \r\n<br/>");
			//echo "匹配到的模板代码是：";
			//print_r($hot_addr);
			//echo "\r\n<br/>";
			$threadId = $hot_addr[1];
			$boardId = $hot_addr[3];
			//构造出的热门跟帖数据
			$reply_data_url="http://comment.".$host."/data/".$boardId."/df/".$threadId.".html";
			//echo "热门新闻评论数据(replydata)的地址是：  ".$reply_data_url."\r\n<br/>";
		}else
		{
			//娱乐新闻的热门跟帖数据和其他版式不同
			if($host == "ent.163.com")
			{
				//热门跟帖的正则表达式
				//<a href="http://comment.ent.163.com/ent2_bbs/PHOTFAJ0000300AJ.html" hidefocus="true" class="comment">评论 <span>
				$preg = '# <div class="parting-line"></div>

					<a href="(.*)" hidefocus="true" class="comment">评论 <span>#isU'; 
				// 进行正则搜索，匹配到第一个就是replydata
				//更好的改法是先匹配host，然后钩子不同的新闻评论地址
				if(preg_match($preg, $pcode, $hot_addr))
				{
					//http://comment.ent.163.com/ent2_bbs/PHOTFAJ0000300AJ.html
					//http://comment.ent.163.com/data/ent2_bbs/df/PHOTFAJ0000300AJ_1.html
					//构造出的热门跟帖数据
					$review_url=$hot_addr[1];
					//echo "该娱乐新闻评论页面的地址是：  ";
					//print_r($hot_addr);
					$review_url = trim($review_url,"http://");
					$review_url = explode("/",$review_url);
					$boardId = $review_url[1];
					$threadId = $review_url[2];
					$threadID = str_replace(".html","_1.html",$threadID);
					//构造出的热门跟帖数据
					$reply_data_url="http://comment.".$host."/data/".$boardId."/df/".$threadId;
					//echo $reply_data_url;
				}else
				{
					die("没有从娱乐新闻页面获取到评论页面的地址！可能是非标准新闻页面或新闻改版格式变化引起的！\r\n<br/>");
				}
			}else
			{
				die("没有从新闻页面数据中获取到评论replyData页面的地址！可能是非标准新闻页面或新闻改版格式变化引起的！\r\n<br/>");
			}
		}

		get_163_hot_post_reply_data($reply_data_url,$hot_post_result);
	}
	
	/*$reply_data_url = 'http://comment.news.163.com/data/news_guonei8_bbs/df/90FOLERB0001124J_1.html'; 
	$hot_post_result = array();
	get_163_hot_post_reply_data($reply_data_url,$hot_post_result);
	
	echo "打印一下获取到的hot_post_result数组：<br>";
	var_dump($hot_post_result);*/
	
	function get_163_hot_post_reply_data($reply_data_url,&$hot_post_result)
	{
		// 获取热门新闻评论数据 
		$pcode = file_get_contents($reply_data_url); 
		if($pcode == false)
		{
			die("无法实时获取最热新闻评论数据页面！");
		}
		//先获取json格式
		$preg_json = '#var replyData={(.*)};#isU';
		// 进行正则搜索，匹配到json数据
		if(preg_match($preg_json, $pcode, $json_data_arr)==false)
		{
			die("没有找到热门新闻评论数据的json串，可能是模板变化引起的！\r\n<br/>");
		}
		$json_data = $json_data_arr[1];	
		//echo "已找到163热门新闻的json数据： \r\n<br/>";
		//print_r($json_data_arr);
		//echo "\r\n<br/>";
		
		//对json数据进行解码，变为数组
		$reply_data = json_decode(("{".$json_data."}"),true);
		if($reply_data == false)
		{
			die("json格式编码错误！");
		}
		//获取热门跟帖的数组，后面取“精彩盖楼”
		$hot_post = $reply_data["hotPosts"];
		
		//打印出热门跟帖的json数组
		//echo "replyData的hotpost数组打印如下 \r\n<br/>";
		//var_dump($hot_post);
		//echo "该新闻的最热评论数共有：".count($hot_post)."个！\r\n<br/>";
		
		$hot_post_result["hot_post_count"] = count($hot_post);
		
		for($i=0; $i< count($hot_post); $i++)
		{
			//echo "内层第".$i."个值的count为".count($hot_post[i])."\r\n<br/>";
			/*echo "其值为";
			var_dump($hot_post[$i]);
			echo "\r\n<br/>";*/
			//注意：内层楼数是从1开始的
			for($j=1;$j<=count($hot_post[$i]);$j++)
			{
				//精彩评论
				//echo "<br/>".$hot_post[$i][$j]["b"]."\r\n<br/>";
				$hot_post_result["data".$i][$j-1]["review"] = $hot_post[$i][$j]["b"];
				
				//评论作者是$hot_post[$i][$j]["n"]或["f"]
				if(array_key_exists("n",$hot_post[$i][$j]))
				{
					//评论作者
					//echo "------".trim($hot_post[$i][$j]["n"],"：");
					$hot_post_result["data".$i][$j-1]["author"] = trim($hot_post[$i][$j]["n"],"：");
				}elseif(array_key_exists("f",$hot_post[$i][$j]))
				{
					//评论作者
					//echo "------".trim($hot_post[$i][$j]["f"],"：");
					$hot_post_result["data".$i][$j-1]["author"] = trim($hot_post[$i][$j]["f"],"：");
				}
				if(array_key_exists("v",$hot_post[$i][$j]))
				{
					//echo " | 赞[".$hot_post[$i][$j]["v"]."]<br/>";
					$hot_post_result["data".$i][$j-1]["vote"] = $hot_post[$i][$j]["v"];
				}
			}
		}
		
		//获取“精彩盖楼”数组
		$build_post = $reply_data["buildPosts"];
		
		//打印出精彩盖楼的json数组
		//echo "replyData的buildPosts数组打印如下 \r\n<br/>";
		//var_dump($build_post);
		//echo "该新闻的最佳盖楼数共有：".count($build_post)."个！\r\n<br/>";
		
		$hot_post_result["build_post_count"] = count($build_post);
		
		for($i=0; $i< count($build_post); $i++)
		{
			//echo "内层第".$i."个值的count为".count($build_post[i])."\r\n<br/>";
			/*echo "其值为";
			var_dump($build_post[$i]);
			echo "\r\n<br/>";*/
			//注意：内层楼数是从1开始的
			for($j=1;$j<=count($build_post[$i]);$j++)
			{
				//精彩盖楼
				//echo "<br/>".$build_post[$i][$j]["b"]."\r\n<br/>";
				$hot_post_result["build_data".$i][$j-1]["review"] = $build_post[$i][$j]["b"];
				
				//评论作者是$build_post[$i][$j]["n"]或["f"]
				if(array_key_exists("n",$build_post[$i][$j]))
				{
					//评论作者
					//echo "------".trim($build_post[$i][$j]["n"],"：");
					$hot_post_result["build_data".$i][$j-1]["author"] = trim($build_post[$i][$j]["n"],"：");
				}elseif(array_key_exists("f",$build_post[$i][$j]))
				{
					//评论作者
					//echo "------".trim($build_post[$i][$j]["f"],"：");
					$hot_post_result["build_data".$i][$j-1]["author"] = trim($build_post[$i][$j]["f"],"：");
				}
				if(array_key_exists("v",$build_post[$i][$j]))
				{
					//echo " | 赞[".$build_post[$i][$j]["v"]."]<br/>";
					$hot_post_result["build_data".$i][$j-1]["vote"] = $build_post[$i][$j]["v"];
				}
			}
		}
	}
?>
