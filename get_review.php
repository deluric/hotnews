<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<?php
	require_once("163.php");
	
	define("FILE_QQ","saemc://review_qq.json");
	define("FILE_163","saemc://review_163.json");
	define("FILE_SINA","saemc://review_sina.json");
	
	//删除掉json数据
	function del_json_file($id = 1,$file = FILE_163 )
	{
		$json_file = $file.$id;
		echo $json_file;
		if( file_exists($json_file) && is_readable($json_file) )
		{
			unlink($json_file);
		}
	}
	
	function get_review_news( &$hot_post_result,$hot_id = 1, $file = FILE_163 )
	{
        $json_file = $file.$hot_id;
		
		if(!file_exists($json_file) || !is_readable($json_file) )
		{
			unlink($json_file);
			//调用最热评论获取程序，生成今天最热评论
			sae_debug( "文件不存在，需重新生成最热评论！\r\n<br/>");
			get_163_review_to_file($hot_post_result,$json_file,$hot_id);
			return;
		}
		//查看文件是否是今天创建的最新日志文件
		if(date("Y-m-d-h",time()) != date("Y-m-d-h",filemtime($json_file)))
		{
			sae_debug( "这个文件不是今天同一小时创建的！\r\n<br/>");
			unlink($json_file);
			//调用最热评论获取程序，生成今天最热评论
			get_163_review_to_file($hot_post_result,$json_file,$hot_id);
			return;
		}
        sae_debug( "已有今天同一小时创建的热门新闻评论！\r\n<br/>");
        
		$pcode=file_get_contents($json_file); 
		$hot_post_result = json_decode($pcode,true);
	}
?>
