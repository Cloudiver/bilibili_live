<html>
<head>
    <meta charset="utf-8">
	<meta name="keywords" content="bilibili,直播,live,哔哩哔哩live直链,B站,哔哩哔哩">
    <meta name="description" content="哔哩哔哩live直链获取">
	<meta itemprop="name" content="bilibili live 直链获取">
	<meta itemprop="description" content="bilibili live 直链获取">
	<meta itemprop="image" content="https://q.qlogo.cn/g?b=qq&nk=800059038&s=100">
	<meta name="referrer" content="no-referrer">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>bilibili live 直链获取</title>
    <style>
        html,
        body {
            height: 100%;
        }
        
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
        }
        
        .container {
            text-align: center;
            display: block;
            position: relative;
            top: 100px;
            vertical-align: middle;
			position: relative;
			min-height: 100%;
			padding-bottom: 50px;
        }
		@media screen and (max-width: 375px){
			.container {
				top: 20px;
			}
		}
        
        .content {
            text-align: center;
            display: inline-block;
        }
        
        .title {
            font-size: 66px;
        }
		
		hr{
			border: 0.5px dashed silver;
			width: 50%;
		}
		
		a {
			text-decoration: none;
			color: black;
		}
		
		a:hover{ 
			color: #00A1D6;
		}
		
	</style>
</head>
<body>
<div>
    <div class="container">
        <div class="content">
            <div class="title">bilibili live 直链获取</div>
			<span>（1.若无法播放，换用其它地址</span>
			<span>2.只能获取直播间直链，不能用于投稿视频</span>
			<br>
			<span>3.b站视频解析可用 https://www.parsevideo.net </span>
			<span>4.<a href="https://github.com/Cloudiver/bilibili_live" target="_blank">源码</a>）</span>
			
        </div>
        <h3>请在下方输入房间号</h3>
		<div>
			<form action="/index.php">
				<input type="text" id='room_id' name="room_id" pattern="\d*" title="只能输入数字" placeholder="请输入房间号" maxlength="25" value="" required>
				<label>清晰度：</label>
				<input type="radio" name="quality" value="10000" checked>原画
				<input type="radio" name="quality" value="150">高清	
				<br>
				<br>
				<button id="submit">获取</button>
			</form>
		</div>
        <?php
		if (isset($_GET['room_id'], $_GET['quality'])){
			$room_id = trim($_GET['room_id']);  //清理空格
			$room_id = strip_tags($room_id);   //过滤html标签  
			$room_id = htmlspecialchars($room_id);   //将字符内容转化为html实体  
			$room_id = addslashes($room_id);  //防止SQL注入
			
			$quality = trim($_GET['quality']);
			$quality = strip_tags($quality);   //过滤html标签  
			$quality = htmlspecialchars($quality);   //将字符内容转化为html实体  
			$quality = addslashes($quality);  //防止SQL注入
			
			if (isset($_GET['sina'])){
				$sina = trim($_GET['sina']);  //清理空格
				$sina = strip_tags($sina);   //过滤html标签  
				$sina = htmlspecialchars($sina);   //将字符内容转化为html实体  
				$sina = addslashes($sina);  //防止SQL注入
			}

			/* echo $room_id; */
			echo "<script>document.getElementById('room_id').value = $room_id </script>";
			
			/* 获取的地址有效期一个小时 */
			if($room_id != "" && $quality == "10000" || $quality == "150"){
				preg_match('/\d+/', $room_id, $matches);
				if (count($matches)){
					$live_data = live_status($matches[0]);
					if (array_key_exists("live_status", $live_data) && $live_data['live_status'] == 1){
						$live_room_id = $live_data['room_id'];   # 房间号
						$mid = $live_data['uid'];   # mid
						$up_name = user_info($mid);
						echo '<h3>房间号：' . $live_room_id . '</h3>';
						echo '<h3>up：' . $up_name . '</h3>';
						echo '<br>';
						$live_info = geturl('https://api.live.bilibili.com/xlive/web-room/v1/playUrl/playUrl?cid='.$matches[0].'&qn='.$quality.'&platform=web');
						$live_urls = $live_info['data']['durl'];
						$durl_length = count($live_urls);
						for($i=0; $i<$durl_length; $i++){
							echo $live_urls[$i]['url'];
							echo "<hr/>";
						}
					}else{
						echo 'up主还未开播';
					}
				}
			}
		}
		?>
	</div>
</div>
</body>
<?php
# 当前开播状态
function live_status($room_id){
	error_reporting(E_ALL ^ E_NOTICE);   # 关闭Notice提示
	$live_room = geturl('https://api.live.bilibili.com/xlive/web-room/v1/index/getRoomPlayInfo?room_id='.$room_id);
	return $live_room['data']; # 轮播的状态码为2，也没法看
}

# 直播间基本信息
# 需要携带cookie，不弄了
function live_info($mid){
	error_reporting(E_ALL ^ E_NOTICE);   # 关闭Notice提示
	$live_info = geturl('https://api.live.bilibili.com/room/v1/Room/getRoomInfoOld?mid='.$mid);
	return $live_info['data'];
}

# up个人信息
function user_info($mid){
	error_reporting(E_ALL ^ E_NOTICE);   # 关闭Notice提示
	$user_info = geturl('https://api.bilibili.com/x/space/acc/info?mid='.$mid);
	return $user_info['data']['name'];
}

function geturl($url){
	$headerArray =array("Content-type:application/json;","Accept:application/json");
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArray);
	$output = curl_exec($ch);
	curl_close($ch);
	$output = json_decode($output,true);
	return $output;
}

// get_turl('https://www.runoob.com/js/js-tutorial.html')

?>
