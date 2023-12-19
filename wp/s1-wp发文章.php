<?php
/* wordpress网站文章发布 
 * 
 * 需要在wp网站根目录上传Locoy.php接口文件!
 * 
 * 添加电脑外网ip到 服务器防火墙白名单
 * 

在本地电脑使用cmd终端 php命令运行文件



*/
//--------------------------------------------
//--------------------------------------------
//文章路径
$cof_dir = 'D:\2222\1111\222';
//接口文件Locoy.php验证密码
$cof_secret = 'sssdddffff';
//网站 每日 发布文章数量
$cof_num='5';
//自定义文章标题，一行一个（留空不改标题）
$cof_title='




';
//域名，一行一个，不带http://  （网站不设置强制跳转https）
$cof_str='


2222.com
1111.com





';
//--------------------------------------------
//--------------------------------------------
ini_set('date.timezone','Asia/Shanghai');
set_time_limit(0);
$cof_dir=trim($cof_dir);
$cof_secret=trim($cof_secret);
$cof_num=trim($cof_num);
if(!$cof_dir || !is_dir($cof_dir)){
	exit("设置正确的文章路径\n");
}
if(!$cof_secret){
	exit("设置接口文件密码\n");
}
$cof_num=intval($cof_num);
if($cof_num < 1){
	$cof_num = 1;
}

$site_arr = explode("\n", trim($cof_str));
$site_arr = array_values(array_filter(array_map('trim',$site_arr)));
if(empty($site_arr)){
    exit("设置域名\r\n");
}
$title_arr = explode("\n", trim($cof_title));
$title_arr = array_values(array_filter(array_map('trim',$title_arr)));


$tjnum=1;
$num = 1;

$err=array();

//执行
$glob = glob2foreach($cof_dir);
while($glob->valid()){
	// 当前文件 包括路径
	$filename = $glob->current();
	// $tname = basename($filename);
	$tag='';
	$tname = substr($filename,strrpos($filename, DIRECTORY_SEPARATOR) + 1);
	$title = rtrim($tname,'.txt');
	$site = current($site_arr);
	$content = file_get_contents($filename);
	
	if(!empty($title_arr)){
		$title=$rand_title=$title_arr[array_rand($title_arr,1)];
		$rand_title=str_replace(' ','',$rand_title);
		$tag = str_replace(array('-','_','|'),',',$rand_title);
		$content = $tag.','.$content.','.$tag;
	}
	
	$response = update_news($site,$title,$content,$cof_secret,$tag);
	if($response !='发布成功'){
		$err[]="{$site}----发布失败\r\n";
	}


	echo sprintf("%s：%s：%s ---- %s \n",$num,$site,$title,trim($response));
	unlink($filename);
	
	
	if($tjnum % $cof_num == 0){
		$tjnum=0;
		if( !next($site_arr) ){
			if(!empty($err)){
				echo "\r\n\r\n";
				$err=array_unique($err);
				echo implode('', $err);
				echo "\r\n\r\n";
			}
			$err=array();

			$sleep_time = strtotime('23:59:59') - time() +60;
			echo sprintf("%s 今天文章已发完，睡眠%s 秒后继续执行\n",date('Y-m-d H:i:s'),$sleep_time);
			sleep($sleep_time);
			reset($site_arr);
		}
	}
	
	$glob->next();
	$tjnum++;
	$num++;
}

echo "文章已发完\n";


//读取目录下的文件 生成器  $include_dirs 多级目录
function glob2foreach($path, $include_dirs=false){
	$path = rtrim($path, DIRECTORY_SEPARATOR.'*');
	$dh = opendir($path);
	while(($file = readdir($dh)) !== false){
		if(strpos($file,'.') === 0) continue;
		// yield $path.DIRECTORY_SEPARATOR.$file;

		$rfile = "{$path}".DIRECTORY_SEPARATOR."{$file}";
		if(is_dir($rfile)){
			$sub = glob2foreach($rfile, $include_dirs);
			while ($sub->valid()) {
				yield $sub->current();
				$sub->next();
			}
			if ($include_dirs) yield $rfile;
		}else{
			yield $rfile;
		}

	}
	closedir($dh);
}

//更新新闻
function update_news($site,$title,$content,$cof_secret,$tag){
	$title = trim($title);
	$url = sprintf('http://%s/Locoy.php?action=save&secret=%s',$site,$cof_secret);
	$data = [
		'post_title' => $title,
		'post_content' => $content,
		'post_category' => mt_rand(1,6),
		'post_type' => 'post',
		//'post_date' => date('Y-m-d'),#时间 可选
		//'post_excerpt' => '',#摘要 可选
		//'post_author' => '',#作者 可选
	];
	if($tag){
		$data['tag']=$tag;#标签 可选
	}
	return post_url($url,$data);
}


//request
function post_url($url,$post_data=[],$header=[],$timeout=10){
	if( $post_data && is_array($post_data) ) http_build_query($post_data);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	if( strpos($url,'https') ===0 ){
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	}
	if($header) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	$response = curl_exec($ch);
	curl_close($ch);
	return $response;
}







