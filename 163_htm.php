<?php
   require_once("get_review.php");	
   $hot_id = 1;
   //获取是第几热门新闻
   if(isset($_GET["hot_id"]))
	{
		$hot_id = $_GET["hot_id"];
		if($hot_id < 1 || $hot_id > 10)
		{
			sae_debug("有人恶意攻击！".$hot_id);
			die("有人恶意攻击！".$hot_id);
		}
	}
	
   //在debug模式下需要清除json缓存数据
   //先是以163为例
   if(isset($_GET["debug"]))
	{
		//清除json缓存数据
		del_json_file($hot_id);
	}

   $hot_post_result = array();
   get_review_news($hot_post_result,$hot_id);
?>

<html>
   <head>
      <title>网易热门新闻评论排行榜</title>
      <meta http-equiv=Content-Type content="text/html;charset=utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
      <meta name="apple-mobile-web-app-capable" content="yes">
      <meta name="apple-mobile-web-app-status-bar-style" content="black">
      <meta name="format-detection" content="telephone=no">
      <style type="text/css">
         <!--
			h2{
				color: #a65993;
				font-size: 20px;
            	font-weight: bold;
            	word-break: normal;
            	word-wrap: break-word;
			}
            .author {
            	color: #657b83;/*660099 紫色*/
            	font-weight: bold;
            }
            .triangle_title {
            	position:relative;
            	padding:10px;
				padding-bottom:22px;
            	margin:1em 0 1em;
            	color:#000;
            	background:#cccccc; /* default background for browsers without gradient support */
            	background:-webkit-gradient(linear, 0 0, 0 100%, from(#d8d8d8), to(#cccccc));
            	background:-moz-linear-gradient(#d8d8d8, #cccccc);
            	background:-o-linear-gradient(#d8d8d8, #cccccc);
            	background:linear-gradient(#d8d8d8, #cccccc);
            	-webkit-border-radius:10px;
            	-moz-border-radius:10px;
            	border-radius:10px;
            }
            .triangle_title h1{
            	font-size: 20px;
            	font-weight: bold;
            	word-break: normal;
            	word-wrap: break-word;
            }
			
			.triangle_title .a_left{
				width:50%;
				float:left;
            	font-size: 15px;
            	font-weight: bold;
            	word-break: normal;
            	word-wrap: break-word;
            }
			.triangle_title .a_right{
				width:50%;
				float:right;
				text-align:right;
            	font-size: 15px;
            	font-weight: bold;
            	word-break: normal;
            	word-wrap: break-word;
            }
            .triangle_green {
            	position:relative;
            	padding:10px;
            	margin:0.5em 0 0.7em;
            	color:#000;
				/*b8db29*/
				/*80c124 d5f870*/
            	background:#d8efd7; 
            }
            .triangle_green:after {
            	content:"";
            	position:absolute;
            	bottom:-15px;
            	left:50px;
            	border-width:15px 15px 0; 
            	border-style:solid;
            	border-color:#d8efd7 transparent;
                display:block; 
                width:0;
            }
            .triangle_blue {
            	position:relative;
            	padding:10px;
            	margin:0.5em 0 0.7em;
            	color:#000;
            	background:#b5e5f5; /*52c3dd 00b0f0  b8e1e1 */
            }
            .triangle_blue:after {
            	content:"";
            	position:absolute;
            	bottom:-15px;
            	left:50px;
            	border-width:15px 15px 0; 
            	border-style:solid;
            	border-color:#b5e5f5 transparent;
                display:block; 
                width:0;
            }
			 .triangle_yellow {
            	position:relative;
            	padding:10px;
            	margin:0.5em 0 0.7em;
            	color:#000;
            	background:#faf0cd; /*fff0e0 f7e9cc f0dea7 FFEF61*/
            }
            .triangle_yellow:after {
            	content:"";
            	position:absolute;
            	bottom:-15px;
            	left:50px;
            	border-width:15px 15px 0; 
            	border-style:solid;
            	border-color:#faf0cd transparent;
                display:block; 
                width:0;
            }
			 .triangle_purple {
            	position:relative;
            	padding:10px;
            	margin:0.5em 0 0.7em;
            	color:#000;
            	background:#bdaee8; 
            }
            .triangle_purple:after {
            	content:"";
            	position:absolute;
            	bottom:-15px;
            	left:50px;
            	border-width:15px 15px 0; 
            	border-style:solid;
            	border-color:#bdaee8 transparent;
                display:block; 
                width:0;
            }
            -->
      </style>
   </head>
   <body>
      <div class="triangle_title">
         <h1 >
            <?php
               echo $hot_post_result["news_title"]."【".$hot_post_result["hit_count"]."评】";
            ?>
         </h1>
         <p>
            <?php
               echo $hot_post_result["news_content"];
               echo "<p> <div class=\"a_left\" ><a href=\"".$hot_post_result["news_url"]." \">查看新闻原文</a></div>";
			   if($hot_id<10)
			   {
					echo "<div class=\"a_right\" ><a href=\"163_htm.php?hot_id=".($hot_id+1)."\" >下一热门新闻</a></div>";
			   }
			   echo "</p>";
			   
            ?>
         </p>
      </div>
      <div>
         <?php	
			if($hot_post_result["hot_post_count"])
			{
				echo "<h2>精彩评论：</h2>";
				echo "<hr width=100% size=3 color=#f188a0 style=\"border:2 double \"> ";
			}
			
            for($i=0; $i < $hot_post_result["hot_post_count"] ; $i++)
            {
            	for($j=0;$j < count($hot_post_result["data".$i]);$j++)
            	{
            		if($j%2==0)
            		{
            			//精彩评论：蓝色方块
            			echo "<p class=\"triangle_blue\">".$hot_post_result["data".$i][$j]["review"]."</p>";
            		}else
            		{
            			//精彩评论：黄色方块
            			echo "<p class=\"triangle_yellow\">".$hot_post_result["data".$i][$j]["review"]."</p>";
            		}
            			
            		//评论作者是$hot_post[$i][$j]["n"]或["f"]
            		if(array_key_exists("author",$hot_post_result["data".$i][$j]))
            		{
            			//评论作者
            			//echo "<p class=\"author\"> @【".($i+1)."楼".($j+1)."房】 ".$hot_post_result["data".$i][$j]["author"];
            			echo "<p class=\"author\"> 来自：".$hot_post_result["data".$i][$j]["author"];
            			if(array_key_exists("vote",$hot_post_result["data".$i][$j]))
            			{
            				echo " | 爱过【".$hot_post_result["data".$i][$j]["vote"]."】";
            			}
            			echo "</p>";
            		}
            	}
            	echo "<hr width=100% size=2 color=#e022a0 style=\"border:1.2 double \"> ";
            	
            }

			if($hot_post_result["build_post_count"])
			{
				echo "<h2>精彩盖楼：</h2>";
				echo "<hr width=100% size=3 color=#f188a0 style=\"border:2 double \"> ";
			}
			
			//精彩盖楼
			for($i=0; $i < $hot_post_result["build_post_count"] ; $i++)
            {
            	for($j=0;$j < count($hot_post_result["build_data".$i]);$j++)
            	{
            		if($j%2==0)
            		{
            			//精彩评论：绿色方块
            			echo "<p class=\"triangle_green\">".$hot_post_result["build_data".$i][$j]["review"]."</p>";
            		}else
            		{
            			//精彩评论：粉色方块
            			echo "<p class=\"triangle_purple\">".$hot_post_result["build_data".$i][$j]["review"]."</p>";
            		}
            			
            		//评论作者是$hot_post[$i][$j]["n"]或["f"]
            		if(array_key_exists("author",$hot_post_result["build_data".$i][$j]))
            		{
            			//评论作者
            			echo "<p class=\"author\"> 来自：".$hot_post_result["build_data".$i][$j]["author"];
            			if(array_key_exists("vote",$hot_post_result["build_data".$i][$j]))
            			{
            				echo " | 爱过【".$hot_post_result["build_data".$i][$j]["vote"]."】";
            			}
            			echo "</p>";
            		}
            	}
            	echo "<hr width=100% size=2 color=#e022a0 style=\"border:1.2 double \"> ";
            }
			
			if($hot_id<10)
			{
				echo "<div class=\"a_right\" ><a href=\"163_htm.php?hot_id=".($hot_id+1)."\" >下一热门新闻</a></div>";
			}
            ?>
      </div>
   </body>
</html>
