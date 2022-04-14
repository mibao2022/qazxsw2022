<?php
/*创建wordpress站点
文件放在服务器上运行
php /www/1111/1wp批量建站.php


*/




//---------------------------设置开始---------------------------------

//宝塔面板地址*
$cof_panel='http://202.165.121.194:8888/';

//宝塔API接口密钥*
$cof_key='VIAZyCfERh1ii1RkAe3zmZmjmWL4A4Qc';

//网站使用的php版本* (例7.2版本 写72)(推荐php7.0以上版本)
$cof_php_v=72;


//wp网站后台账号*
$cof_admin_name='admin1234';

//wp网站后台密码*
$cof_admin_password='Qq12345678';

//wp管理邮箱
$cof_email='123456@qq.com';


//设置建站域名的文件* (内容格式:域名****网站标题****网站关键词****描述)
$cof_site_file='site.txt';


//统计js名称* (创建到网站根目录)
$cof_js='baidu.js';

//统计js内容 (内容写在EOTABCD中间,EOTABCD后面不能有字符、空格) 
$cof_js_content=<<<'EOTABCD'


EOTABCD;
//---------------------------设置结束---------------------------------








//--------------------------------------------------
//--------------------------------------------------
//wp压缩包路径，或者下载链接
$cof_wplink='https://raw.githubusercontent.com/mibao2022/qazxsw2022/main/wp/wordpress.5.9.3.tar.gz';

//下载模板主题类型* popular(有4000个主题) 或 new(有8000个主题)
$cof_browse = 'popular';
//路径
$wwwroot='/www/wwwroot';
//网站伪静态
$cof_rewrite='if (!-e $request_filename) {
    rewrite  ^(.*)$  /index.php/$1  last;
    break;
 }';
//更新本文件;1开启
$cof_update='0';
//--------------------------------------------
//--------------------------------------------
//---------------代码开始---------------------
//--------------------------------------------
//--------------------------------------------
$cof_panel=rtrim($cof_panel,'/');
$cof_key=trim($cof_key);
if(!preg_match('/[0-9a-zA-Z]{32}/',$cof_key)){
    exit('设置正确的宝塔API接口密钥');
}

set_time_limit(0);
$bt=new BtApi($cof_panel,$cof_key);
$wp=new WordPress();
if($cof_update){$wp->upwp();}

$cof_php_v=intval($cof_php_v);
$cof_admin_name=trim($cof_admin_name);
$cof_admin_password=trim($cof_admin_password);
$cof_email=trim($cof_email);
$cof_site_file=trim($cof_site_file);
$cof_js=trim($cof_js);
$cof_js_content=trim($cof_js_content);

if($cof_site_file[0] != '/'){
    $cof_site_file=__DIR__.'/'.$cof_site_file;
}
if(!is_readable($cof_site_file)){
    exit('设置建站域名的文件');
}

if(!$cof_php_v || $cof_php_v<54){
    exit('填写正确的php版本');
}
if(!$cof_admin_name){
    exit('网站后台账户为空');
}
if(!$cof_admin_password || strlen($cof_admin_password)<8){
    exit('网站后台密码长度小于8位数');
}
if(empty($cof_email)){
    $cof_email='250888888@qq.com';
}
if(!$cof_js){
    exit('设置js文件名');
}
if(!is_dir('/www/server')){
    exit('文件需要放到服务器上运行');
}
if(!$cof_wplink){
    exit('设置wp压缩包路径');
}
if(!$cof_browse){
    exit('设置下载模板主题类型');
}




//读取域名
$site_str=file_get_contents($cof_site_file);
$site_arr=explode("\n", trim($site_str));
$site_arr=array_values(array_filter(array_map('trim',$site_arr)));
if(empty($site_arr)){
	exit('设置建站域名');
}
if($cof_wplink[0] == '/' && is_file($cof_wplink)){
    $wp_zipfile=$cof_wplink;
}else{
    $wp_zipfile=$wp->down_wp($cof_wplink);
}
$seo_zipfile=$wp->down_seozip();



foreach($site_arr as $key=>$val){
    $wp->set_tdk($val);
    $site=$wp->site;
    $rpath=$wwwroot.'/'.$site;
    $db_name=substr(str_replace(['.','-'], '_', $site),0,16);
    $db_pwd=$wp->rand_str(16);
    $wp->setvar([
        'rpath'=>$rpath,
        'admin_name'=>$cof_admin_name,
        'admin_password'=>$cof_admin_password,
        'db_name'=>$db_name,
        'db_pwd'=>$db_pwd,
        'blog_email'=>$cof_email,
    ]);
    
    
    
    
    //创建站点
    echo sprintf("\n------搭建第%s个站点:%s------\n",$key+1,$site);
    $response=$bt->AddSite($site,$rpath,$cof_php_v,'MySQL',$db_name,$db_pwd);
    if(strpos($response,'"siteStatus": true') === false || empty($response) ){
        $wp->file_record(sprintf("站点创建失败\n%s\n",$response));
        sleep(2);
        continue;
    }
    echo sprintf("站点创建成功\n%s\n",$response);
    $web_data=json_decode($response,true);
	if( $webData['databaseStatus'] === false ){
		$wp->file_record('数据库创建失败');
		$bt->WebDeleteSite($web_data['siteId'],$site);
		continue;
	}
    @unlink($rpath.'/index.html');
    
    //设置网站伪静态
    $response=$bt->SaveFileBody(sprintf('/www/server/panel/vhost/rewrite/%s.conf',$site), $cof_rewrite);
    if(strpos($response,'文件已保存')===false){
        $wp->file_record('伪静态设置失败');
    }else{
        echo "伪静态设置成功\n";
    }
    
    //解压
    $wp->unzip_wp($wp_zipfile);
    
    //安装wp
    if(!$wp->install()){
        $bt->WebDeleteSite($web_data['siteId'],$site);
        continue;
    }
    //登录wp
    if(!$wp->login()){
        $bt->WebDeleteSite($web_data['siteId'],$site);
        continue;
    }
    
    
    //网站设置
    $wp->setting();
    
    
    
    //添加js代码
    $wp->add_js($cof_js);
    $wp->create_js($cof_js,$cof_js_content);
    
    
    
}


echo "\n完成\n";














//wp类
class WordPress{

    public $cookie;
    public $site;
    public $host;
    public $rpath;
    
    public $db_name;
    public $db_pwd;
    
    public $admin_name;
    public $admin_password;

    public $blog_name;
    public $blog_keywords;
    public $blog_desc;
    public $blog_email;
    
    
    public function __construct(){
        // $this->upwp();
    }

    public function setvar(array $var){
        foreach($var as $key=>$val){
          $this->$key=$val;
        }
        return $this;
    }

    //网站设置
    public function setting(){
        global $seo_zipfile;
        
        //常规设置
        $this->options_general();
        
        //评论设置
        $this->options_discussion();
        
        //固定连接设置
        $this->options_permalink();
        
        //添加分类
        $this->category_add();
        
        
        //解压seo插件
        $this->plugin_seo_unzip($seo_zipfile);
        
        //下载随机主题
        $theme=$this->theme_down();
        
        //启用新主题
        $this->theme_enb($theme);
        
        //创建菜单
        $this->menu_add();
        
        //启用seo插件
        if($this->plugin_seo_enb()){
            //设置seo插件的seo信息
            $this->plugin_seo_edit();
            //设置seo插件的sitemap信息
            $this->plugin_seo_sitemap(5000);
            //开启设交meta功能
            if($this->plugin_seo_opengraph()){
                $this->plugin_seo_editgraph();
            }
        }
        
        
        return $this;
    }


    //安装
    public function install(){
        echo "开始安装wordpress\n";
        $p_url=$this->host.'wp-admin/setup-config.php?step=0';
        $p_data=['language'=>'zh_CN'];
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($p_url,$p_data);
            if(strpos($response,'<h1 class="screen-reader-text">开始之前</h1>')!==false || strpos($response,'<h1 class="screen-reader-text">Before getting started</h1>')!==false){
                break;
            }elseif(strpos($response,'There has been a critical error on this website')!==false || strpos($response,'Permission denied')!==false){
                exit('修改wp文件权限为755');
            }
            if($i==9){
                $this->file_record('安装失败,网络连接失败');
                return false;
            }
            echo "网络不好  ";
        }
        echo "网络连接成功\n";
        
        
        $p_url=$this->host.'wp-admin/setup-config.php?step=2';
        $p_data=[
            'dbname'    =>  $this->db_name,
            'uname'     =>  $this->db_name,
            'pwd'       =>  $this->db_pwd,
            'dbhost'    =>  'localhost',
            'prefix'    =>  'wp_',
            'language'  =>  'zh_CN',
            'submit'    =>  'Submit'
        ];
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($p_url,$p_data);
            if(strpos($response,'<h1 class="screen-reader-text">数据库连接成功</h1>')!==false || strpos($response,'<h1 class="screen-reader-text">Successful database connection</h1>')!==false){
                break;
            }
            if($i==9){
                $this->file_record('安装失败,数据库连接失败');
                return false;
            }
            echo "网络不好  ";
        }
        echo "数据库连接成功\n";
        
        
        $p_url=$this->host.'wp-admin/install.php?step=2';
        $p_data=[
            'weblog_title'      =>  $this->blog_name,
            'user_name'         =>  $this->admin_name,
            'admin_password'    =>  $this->admin_password,
            'admin_password2'   =>  $this->admin_password,
            'pw_weak'           =>  'on',
            'admin_email'       =>  $this->blog_email,
            'Submit'            =>  '安装WordPress',
            'language'          =>  'zh_CN'
        ];
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($p_url,$p_data);
            if(strpos($response,'<p>WordPress安装完成。谢谢！</p>')!==false || strpos($response,'<p>WordPress has been installed. Thank you, and enjoy!</p>')!==false){
                break;
            }elseif (strpos($response,'<h1>已安装过</h1>')!==false || strpos($response,'<h1>Already Installed</h1>')!==false) {
                break;
            }
            if($i==9){
                $this->file_record('WordPress安装完成');
                return false;
            }
            echo "网络不好  ";
        }
        echo "WordPress安装完成\n";
        return true;
    }

    //登录
    public function login(){
        for($i=0;$i<10;$i++){
            if($this->get_cookie()){
                break;
            }
            if($i==9){
                $this->file_record('登录失败,获取cookie失败');
                return false;
            }
            echo '网络不好  ';
        }
        echo "登录成功\n";
        return true;
    }

    public function get_cookie(){
    	$p_url=$this->host.'wp-login.php';
    	$p_data = [
    		'log' => $this->admin_name,
    		'pwd' => $this->admin_password,
    		'wp-submit' => '登录',
    		'redirect_to' => $this->host.'wp-admin/',
    		'testcookie' => 1,
    	];
    	$ch = curl_init(); 
    	curl_setopt($ch, CURLOPT_URL, $p_url);
    	curl_setopt($ch, CURLOPT_HEADER, true);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($p_data,'','&'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	$response = curl_exec($ch);
    	curl_close($ch);
    	list($header, $body) = explode("\r\n\r\n", $response);
    	preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);
    	if(isset($matches[1][2]) && $matches[1][2]){
            $re=str_replace(' path=/wp-admin; HttpOnly','',$matches[1][2]);
            $cookie='Cookie:'.$re;
    	}else{
    		$cookie='';
    	}
    	return $this->cookie=$cookie;
    }

    /*
    * 获取权限wpnonce函数
    * $url 页面链接
    * $reg 正则,匹配wpnonce
    * return  string
    */
    public function get_wpnonce_func($url,$reg){
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_get($url,$this->cookie);
            if(strpos($response,'<form name="loginform" id="loginform"')!==false){
                $this->login();//未登录
            }
             preg_match($reg,$response,$mat);
            if(isset($mat[1]) && preg_match('/[0-9a-zA-Z]{10}/',$mat[1])){
                break;
            }
            if($i==9){
                $this->file_record('获取权限wpnonce失败');
                return '';
            }
            echo '网络不好  ';
        }
        return $mat[1];
    }

    //常规设置
    public function options_general(){
        $p_url=$this->host.'wp-admin/options-general.php';
        $reg='/id="_wpnonce" name="_wpnonce" value="(.*?)"/';
        if(!$wpnonce=$this->get_wpnonce_func($p_url,$reg)){
            $this->file_record('网站设置失败,获取权限wpnonce失败');
            return false;
        }
        
        $p_url = $this->host.'wp-admin/options.php';
        $p_data = [
    		'option_page'        => 'general',
    		'action'             => 'update',
    		'_wpnonce'           => $wpnonce,
    		'_wp_http_referer'   => '/wp-admin/options-general.php',
    		'blogname'           => $this->blog_name,//标题
    		'blogdescription'    => $this->blog_desc,//副标题
    		'siteurl'            => 'http://'.$this->site,
    		'home'               => 'http://'.$this->site,
    		'new_admin_email'    => $this->blog_email,
    		'default_role'       => 'subscriber',
    		'WPLANG'             => 'zh_CN',
    		'timezone_string'    => 'Asia/Shanghai',
    		'date_format'        => 'Y年n月j日',
    		'date_format_custom' => 'Y年n月j日',
    		'time_format'        => 'ag:i',
    		'time_format_custom' => 'ag:i',
    		'start_of_week'      => '1',
    		'submit'             => '保存更改',
    	];
    	for ($i = 0; $i < 10; $i++) {
            if(strpos($this->curl_post($p_url,$p_data,$this->cookie),'<p><strong>设置已保存。</strong></p>')){
                break;
            }
            if($i==9){
                $this->file_record('网站设置失败');
                return false;
            }
    	    echo "网络不好  ";
    	}
    	echo "网站设置成功\n";
    	return true;
    }

    //评论设置
    public function options_discussion(){
        $p_url=$this->host.'wp-admin/options-discussion.php';
        $reg='/<input type="hidden" id="_wpnonce" name="_wpnonce" value="(.*?)"/';
        if(!$wpnonce=$this->get_wpnonce_func($p_url,$reg)){
            $this->file_record('评论设置失败,获取权限wpnonce失败');
            return false;
        }
        
        $p_url=$this->host.'wp-admin/options.php';
        $p_data=[
            'option_page'                   =>   'discussion',
            'action'                        =>   'update',
            '_wpnonce'                      =>   $wpnonce,
            '_wp_http_referer'              =>   '/wp-admin/options-discussion.php',
            'default_pingback_flag'         =>   '1',
            'default_ping_status'           =>   'open',
            'default_comment_status'        =>   'open',
            'require_name_email'            =>   '1',
            'close_comments_for_old_posts'  =>   '1',
            'close_comments_days_old'       =>   '1',
            'show_comments_cookies_opt_in'  =>   '1',
            'thread_comments'               =>   '1',
            'thread_comments_depth'         =>   '5',
            'comments_per_page'             =>   '50',
            'default_comments_page'         =>   'newest',
            'comment_order'                 =>   'asc',
            'comment_previously_approved'   =>   '1',
            'comment_max_links'             =>   '2',
            'moderation_keys'               =>   '',
            'disallowed_keys'               =>   '',
            'show_avatars'                  =>   '1',
            'avatar_rating'                 =>   'G',
            'avatar_default'                =>   'mystery',
            'submit'                        =>   '保存更改'
        ];
        for ($i = 0; $i < 10; $i++) {
            if(strpos($this->curl_post($p_url,$p_data,$this->cookie),'<p><strong>设置已保存。</strong></p>')){
                break;
            }
            if($i==9){
                $this->file_record('评论设置失败');
                return false;
            }
        }
        echo "评论设置成功\n";
    	return true;
    	
    }

    //固定链接设置
    public function options_permalink(){
        $permalink_arr=array(
            '/news/%post_id%.html',
            '/article/%post_id%.html',
            '/xinwen/%post_id%.html',
            '/post/%post_id%.html',
            '/%year%%monthnum%%day%/%post_id%/',
            '/%year%%monthnum%%day%/%postname%/',
            '/archives/%post_id%/',
        );
        $rand_key=array_rand($permalink_arr,1);
        
        
        $p_url=$this->host.'wp-admin/options-permalink.php';
        $reg='/<input type="hidden" id="_wpnonce" name="_wpnonce" value="(.*?)"/';
        if(!$wpnonce=$this->get_wpnonce_func($p_url,$reg)){
            $this->file_record('固定链接设置失败,获取权限wpnonce失败');
            return false;
        }
        
        $p_url=$this->host.'wp-admin/options-permalink.php';
        $p_data=[
            '_wpnonce'              =>  $wpnonce,
            '_wp_http_referer'      =>  '/wp-admin/options-permalink.php',
            'selection'             =>  'custom',
            'permalink_structure'   =>  $permalink_arr[$rand_key],
            'category_base'         =>  '',
            'tag_base'              =>  '',
            'submit'                =>  '保存更改'
        ];
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($p_url,$p_data,$this->cookie);
            if(strpos($response,'<p><strong>固定链接结构已更新。</strong></p>')){
                break;
            }
            if($i==9){
                $this->file_record('固定链接设置失败');
                return false;
            }
        }
        echo "固定链接设置成功\n";
    	return true;
    }

    //分类设置,添加
    public function category_add(){
        $p_url=$this->host.'wp-admin/edit-tags.php?taxonomy=category';
        $reg='/<input type="hidden" id="_wpnonce_add-tag" name="_wpnonce_add-tag" value="(.*?)"/';
        if(!$wpnonce=$this->get_wpnonce_func($p_url,$reg)){
            $this->file_record('固定链接设置失败,获取权限wpnonce失败');
            return false;
        }
        
        $num=5;
        $arr=$this->rand_lanmu($num);
        $p_url = $this->host.'wp-admin/admin-ajax.php';
        $p_data = [
            'action' => 'add-tag',
            'screen' => 'edit-category',
            'taxonomy' => 'category',
            'post_type' => 'post',
            '_wpnonce_add-tag' => $wpnonce,
            '_wp_http_referer' => '/wp-admin/edit-tags.php?taxonomy=category',
            'parent' => '-1',
            'description' => '',
        ];
        for ($i = 0; $i < $num; $i++) {
             $p_data['tag-name']=$arr[$i];//分类名称
             $p_data['slug']='categorized'. strval($i + 1);//别名
             $this->curl_post($p_url,$p_data,$this->cookie);
            //  if(strpos($response,$arr[$i])!==false){
            //      echo "添加分类成功\n";
            //  }
        }
        echo "添加分类成功\n";
        return true;
    }

    //解压seo插件
    public function plugin_seo_unzip($fname){
        $this->unzip_file($fname,$this->rpath.'/wp-content/plugins');
        return true;
    }

    //启用seo插件
    public function plugin_seo_enb(){
        $reg_en='/<strong>All In One SEO Pack<\/strong><div class="row-actions visible"><span class=\'activate\'><a href="(.*?)" id="activate-all-in-one-seo-pack"/';
        $reg_zh='/<strong>多合一SEO包<\/strong><div class="row-actions visible"><span class=\'activate\'><a href="(.*?)" id="activate-all-in-one-seo-pack"/';
        return $this->plugin_enb_func($reg_en,$reg_zh,'seo插件');
    }

    /*
    * 启用插件函数
    * $reg 正则,匹配插件的启用链接
    * $name 插件名
    * return bool
    */
    public function plugin_enb_func($reg_en,$reg_zh,$name){
        //获取插件启用链接
        $p_url=$this->host.'wp-admin/plugins.php';
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_get($p_url,$this->cookie);
            if(strpos($response,'<form name="loginform" id="loginform"')!==false){
                $this->login();//未登录
            }
            preg_match($reg_en,$response,$mat_en);
            if(isset($mat_en[1]) && $mat_en[1]){
                $link=$mat_en[1];
                break;
            }
            preg_match($reg_zh,$response,$mat_zh);
            if(isset($mat_zh[1]) && $mat_zh[1]){
                $link=$mat_zh[1];
                break;
            }
            if($i==9){
                $this->file_record('获取权限wpnonce失败');
                return false;
            }
            echo '网络不好  ';
        }
        echo "获取插件启用链接成功\n";
        
        
        //启用seo插件
        $p_url=$this->host . 'wp-admin/' . str_replace('&amp;','&',$link);
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_get($p_url,$this->cookie);
            if(strpos($response,'<div id="message" class="updated notice is-dismissible"><p>插件已启用。</p>')!==false || strpos($response,'<div class="wp-die-message">您点击的链接已过期。</div>')!==false){
                break;
            }
            if($i==9){
                $this->file_record("启用{$name}失败");
                return false;
            }
            echo '网络不好  ';
        }
        echo "启用{$name}成功\n";
        return true;
    }

    //设置seo插件的seo信息
    public function plugin_seo_edit(){
        $p_url=$this->host.'wp-admin/admin.php?page=all-in-one-seo-pack/aioseop_class.php';
        $reg="/<input name='nonce-aioseop' type='hidden'  value='(.*?)'/";
        if(!$wpnonce=$this->get_wpnonce_func($p_url,$reg)){
            $this->file_record('设置seo插件失败,获取权限wpnonce失败');
            return false;
        }
        
        if(function_exists('mb_strlen')){
            $blog_name_strlen=mb_strlen($this->blog_name);
            $blog_desc_strlen=mb_strlen($this->blog_desc);
        }else{
            preg_match_all("/./us", $this->blog_name, $mat_strlen1);
            $blog_name_strlen=count(current($mat_strlen1));
            preg_match_all("/./us", $this->blog_desc, $mat_strlen2);
            $blog_desc_strlen=count(current($mat_strlen2));
        }
        $p_url=$this->host.'wp-admin/admin.php?page=all-in-one-seo-pack/aioseop_class.php';
        $p_data=[
            'action'                                        =>  'aiosp_update_module',
            'module'                                        =>  'All_in_One_SEO_Pack',
            'location'                                      =>  '',
            'nonce-aioseop'                                 =>  $wpnonce,
            'page_options'                                  =>  'aiosp_home_description',
            'aiosp_can'                                     =>  'on',
            'aiosp_use_original_title'                      =>  '0',
            'aiosp_home_title'                              =>  $this->blog_name,
            'aiosp_length1'                                 =>  $blog_name_strlen,
            'aiosp_home_description'                        =>  $this->blog_desc,
            'aiosp_length2'                                 =>  $blog_desc_strlen,
            'aiosp_home_keywords'                           =>  $this->blog_keywords,
            'aiosp_use_static_home_info'                    =>  '0',
            'aiosp_force_rewrites'                          =>  '1',
            'aiosp_home_page_title_format'                  =>  '%page_title%',
            'aiosp_page_title_format'                       =>  '%page_title% | %site_title%',
            'aiosp_post_title_format'                       =>  '%post_title% | %site_title%',
            'aiosp_category_title_format'                   =>  '%category_title% | %site_title%',
            'aiosp_archive_title_format'                    =>  '%archive_title% | %site_title%',
            'aiosp_date_title_format'                       =>  '%date% | %site_title%',
            'aiosp_author_title_format'                     =>  '%author% | %site_title%',
            'aiosp_tag_title_format'                        =>  '%tag% | %site_title%',
            'aiosp_search_title_format'                     =>  '%search% | %site_title%',
            'aiosp_description_format'                      =>  '%description%',
            'aiosp_404_title_format'                        =>  '无法找到 %request_words%',
            'aiosp_paged_format'                            =>  ' - Part %page%',
            'aiosp_cpostactive'                             =>  ['post','page'],
            'aiosp_attachment_title_format'                 =>  '%post_title% | %site_title%',
            'aiosp_oembed_cache_title_format'               =>  '%post_title% | %site_title%',
            'aiosp_user_request_title_format'               =>  '%post_title% | %site_title%',
            'aiosp_wp_block_title_format'                   =>  '%post_title% | %site_title%',
            'aiosp_wp_template_title_format'                =>  '%post_title% | %site_title%',
            'aiosp_wp_template_part_title_format'           =>  '%post_title% | %site_title%',
            'aiosp_wp_global_styles_title_format'           =>  '%post_title% | %site_title%',
            'aiosp_wp_navigation_title_format'              =>  '%post_title% | %site_title%',
            'aiosp_posttypecolumns'                         =>  ['post','page'],
            'aiosp_google_verify'                           =>  '',
            'aiosp_bing_verify'                             =>  '',
            'aiosp_pinterest_verify'                        =>  '',
            'aiosp_yandex_verify'                           =>  '',
            'aiosp_baidu_verify'                            =>  '',
            'aiosp_google_analytics_id'                     =>  '',
            'aiosp_ga_advanced_options'                     =>  'on',
            'aiosp_ga_domain'                               =>  '',
            'aiosp_ga_addl_domains'                         =>  '',
            'aiosp_schema_markup'                           =>  '0',
            'aiosp_schema_search_results_page'              =>  'on',
            'aiosp_schema_social_profile_links'             =>  '',
            'aiosp_schema_site_represents'                  =>  'person',
            'aiosp_schema_organization_name'                =>  '',
            'aiosp_schema_organization_logo_checker'        =>  '0',
            'aiosp_schema_organization_logo'                =>  '',
            'aiosp_schema_person_user'                      =>  '1',
            'aiosp_schema_person_manual_name'               =>  '',
            'aiosp_schema_person_manual_image_checker'      =>  '0',
            'aiosp_schema_person_manual_image'              =>  '',
            'aiosp_schema_phone_number'                     =>  '',
            'aiosp_schema_contact_type'                     =>  'none',
            'aiosp_category_noindex'                        =>  'on',
            'aiosp_archive_date_noindex'                    =>  'on',
            'aiosp_archive_author_noindex'                  =>  'on',
            'aiosp_rss_content_before'                      =>  '',
            'aiosp_rss_content_after'                       =>  'The post %post_link% first appeared on %site_link%.',
            'aiosp_generate_descriptions'                   =>  'on',
            'aiosp_ex_pages'                                =>  '',
            'aiosp_togglekeywords'                          =>  '0',
            'aiosp_use_categories'                          =>  'on',
            'aiosp_use_tags_as_keywords'                    =>  'on',
            'aiosp_dynamic_postspage_keywords'              =>  'on',
            'action'                                        =>  'aiosp_update_module',
            'module'                                        =>  'All_in_One_SEO_Pack',
            'location'                                      =>  '',
            'nonce-aioseop'                                 =>  $wpnonce,
            'page_options'                                  =>  'aiosp_home_description',
            'Submit'                                        =>  'Update Options »'
        ];
        for ($i = 0; $i < 10; $i++) {
            $response = $this->curl_post($p_url,$p_data,$this->cookie);
            if(strpos($response,sprintf("value='%s'",$this->blog_name))!==false){
                break;
            }
            if($i==9){
                $this->file_record('设置seo插件失败');
                return false;
            }
            echo '网络不好  ';
        }
        echo "设置seo插件成功\n";
        return true;
    }
    
    //设置seo插件的sitemap信息
    public function plugin_seo_sitemap($max_posts=5000){
        $p_url=$this->host.'wp-admin/admin.php?page=all-in-one-seo-pack/modules/aioseop_sitemap.php';
        $reg="/<input name='nonce-aioseop' type='hidden'  value='(.*?)'/";
        if(!$wpnonce=$this->get_wpnonce_func($p_url,$reg)){
            // $this->file_record('设置seo插件sitemap失败,获取权限wpnonce失败');
            return false;
        }
        
        $p_url=$this->host.'wp-admin/admin.php?page=all-in-one-seo-pack/modules/aioseop_sitemap.php';
        $p_data=[
            'action'                                    =>  'aiosp_update_module',
            'module'                                    =>  'All_in_One_SEO_Pack_Sitemap',
            'location'                                  =>  '',
            'nonce-aioseop'                             =>  $wpnonce,
            'page_options'                              =>  'aiosp_home_description',
            'Submit'                                    =>  'Update Sitemap »',
            'aiosp_sitemap_daily_cron'                  =>  '0',
            'aiosp_sitemap_indexes'                     =>  'on',
            'aiosp_sitemap_max_posts'                   =>  $max_posts,//每个站点地图页面的最大帖子数
            'aiosp_sitemap_posttypes'                   =>  ['post','page'],
            'aiosp_sitemap_taxonomies'                  =>  ['all','category','post_tag','post_format'],
            'aiosp_sitemap_rewrite'                     =>  'on',
            'aiosp_sitemap_rss_sitemap'                 =>  'on',
            'aiosp_sitemap_addl_url'                    =>  '',
            'aiosp_sitemap_addl_prio'                   =>  '0.0',
            'aiosp_sitemap_addl_freq'                   =>  'always',
            'aiosp_sitemap_addl_mod'                    =>  '',
            'aiosp_sitemap_excl_pages'                  =>  '',
            'aiosp_sitemap_prio_homepage'               =>  'no',
            'aiosp_sitemap_prio_post'                   =>  'no',
            'aiosp_sitemap_prio_post_attachment'        =>  'no',
            'aiosp_sitemap_prio_post_page'              =>  'no',
            'aiosp_sitemap_prio_post_post'              =>  'no',
            'aiosp_sitemap_prio_taxonomies'             =>  'no',
            'aiosp_sitemap_prio_taxonomies_post_format' =>  'no',
            'aiosp_sitemap_prio_taxonomies_post_tag'    =>  'no',
            'aiosp_sitemap_prio_taxonomies_category'    =>  'no',
            'aiosp_sitemap_prio_archive'                =>  'no',
            'aiosp_sitemap_prio_author'                 =>  'no',
            'aiosp_sitemap_freq_homepage'               =>  'no',
            'aiosp_sitemap_freq_post'                   =>  'no',
            'aiosp_sitemap_freq_post_attachment'        =>  'no',
            'aiosp_sitemap_freq_post_page'              =>  'no',
            'aiosp_sitemap_freq_post_post'              =>  'no',
            'aiosp_sitemap_freq_taxonomies'             =>  'no',
            'aiosp_sitemap_freq_taxonomies_post_format' =>  'no',
            'aiosp_sitemap_freq_taxonomies_post_tag'    =>  'no',
            'aiosp_sitemap_freq_taxonomies_category'    =>  'no',
            'aiosp_sitemap_freq_archive'                =>  'no',
            'aiosp_sitemap_freq_author'                 =>  'no',
            'action'                                    =>  'aiosp_update_module',
            'module'                                    =>  'All_in_One_SEO_Pack_Sitemap',
            'location'                                  =>  '',
            'nonce-aioseop'                             =>  $wpnonce,
            'page_options'                              =>  'aiosp_home_description',
        ];
        $this->curl_post($p_url,$p_data,$this->cookie);
        // echo "设置seo插件sitemap成功\n";
        return true;
    }

    //开启社交meta
    public function plugin_seo_opengraph(){
        //开启社交meta
        $p_url=$this->host.'wp-admin/admin.php?page=all-in-one-seo-pack/modules/aioseop_feature_manager.php';
        $reg="/<input name='nonce-aioseop' type='hidden'  value='(.*?)'/";
        if(!$wpnonce=$this->get_wpnonce_func($p_url,$reg)){
            $this->file_record('开启社交meta失败,获取权限wpnonce失败');
            return false;
        }
        
        $p_url=$this->host.'wp-admin/admin-ajax.php';
        $options=[
            'action'                =>  'aiosp_update_module',
            'module'                =>  'All_in_One_SEO_Pack_Feature_Manager',
            'location'              =>  '',
            'nonce-aioseop'         =>  $wpnonce,
            'page_options'          =>  'aiosp_home_description',
            'aiosp_feature_manager_enable_sitemap'      =>  'on',//XML网站地图
            'aiosp_feature_manager_enable_opengraph'    =>  'on',//社交Meta
            // 'aiosp_feature_manager_enable_robots'       =>  'on',//Robots.txt
            'aiosp_feature_manager_enable_performance'  =>  'on',//性能
            'action'                =>  'aiosp_update_module',
            'module'                =>  'All_in_One_SEO_Pack_Feature_Manager',
            'location'              =>  '',
            'nonce-aioseop'         =>  $wpnonce,
            'page_options'          =>  'aiosp_home_description',
        ];
        $options=http_build_query($options,'','&');
        $p_data=[
            'action'                =>  'aioseop_ajax_save_settings',
            'settings'              =>  'ajax_settings_message',
            'options'               =>  $options,
            'nonce-aioseop'         =>  $wpnonce,
            'nonce-aioseop-edit'    =>  'undefined',
            'rndval'                =>  $this->msectime(),
        ];
        for ($i = 0; $i < 10; $i++) {
             $response=$this->curl_post($p_url,$p_data,$this->cookie);
             if(strpos($response,'All In One SEO Pack Options Updated')!==false){
                 break;
             }
             if($i==9){
                 $this->file_record('社交meta开启失败');
                 return false;
             }
        }
        // $p_data=[
        //     'action'              =>  'aioseop_ajax_get_menu_links',
        //     'settings'            =>  'ajax_settings_message',
        //     'options'             =>  'target=.wp-has-current-submenu%20%3E%20ul',
        //     'nonce-aioseop'       =>  $wpnonce,
        //     'nonce-aioseop-edit'  =>  'undefined',
        //     'rndval'              =>  $this->msectime(),
        // ];
        // $this->curl_post($p_url,$p_data,$this->cookie);
        echo "社交meta开启成功\n";
        return true;
    }
    
    //时间戳 毫秒
    function msectime(){
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }

    //编辑社交meta
    public function plugin_seo_editgraph(){
        //设置社交meta
        $p_url=$this->host.'wp-admin/admin.php?page=aiosp_opengraph';
        $reg="/<input name='nonce-aioseop' type='hidden'  value='(.*?)'/";
        if(!$wpnonce=$this->get_wpnonce_func($p_url,$reg)){
            $this->file_record('设置社交meta失败,获取权限wpnonce失败');
            return false;
        }
        
        $p_url=$this->host.'wp-admin/admin.php?page=aiosp_opengraph';
        $p_data=[
            'action'                =>  'aiosp_update_module',
            'module'                =>  'All_in_One_SEO_Pack_Opengraph',
            'location'              =>  'opengraph',
            'nonce-aioseop'         =>  $wpnonce,
            'page_options'          =>  'aiosp_home_description',
            'Submit'                =>  '更新选项 »',
            'aiosp_opengraph_setmeta'           =>  'on',
            'aiosp_opengraph_sitename'          =>  $this->blog_name,
            'aiosp_opengraph_hometitle'         =>  '',
            'aiosp_length1'                     =>  '0',
            'aiosp_opengraph_description'       =>  '',
            'aiosp_length2'                     =>  '0',
            'aiosp_opengraph_homeimage_checker' =>  '0',
            'aiosp_opengraph_homeimage'         =>  '',
            'aiosp_opengraph_defimg'            =>  '',
            'aiosp_opengraph_dimg_checker'      =>  '0',
            'aiosp_opengraph_dimg'      =>  'http://wireformsindia.com/wp-content/plugins/all-in-one-seo-pack/images/default-user-image.png',
            'aiosp_opengraph_dimgwidth'         =>  '0',
            'aiosp_opengraph_dimgheight'        =>  '0',
            'aiosp_opengraph_meta_key'          =>  '',
            'aiosp_opengraph_key'               =>  '',
            'aiosp_opengraph_appid'             =>  '',
            'aiosp_opengraph_gen_tags'          =>  'on',
            'aiosp_opengraph_gen_keywords'      =>  'on',
            'aiosp_opengraph_gen_categories'    =>  'on',
            'aiosp_opengraph_gen_post_tags'     =>  'on',
            'aiosp_opengraph_types'             =>  ['post','page'],
            'aiosp_opengraph_facebook_publisher'                =>  '',
            'aiosp_opengraph_post_fb_object_type'               =>  'article',
            'aiosp_opengraph_page_fb_object_type'               =>  'article',
            'aiosp_opengraph_attachment_fb_object_type'         =>  'article',
            'aiosp_opengraph_oembed_cache_fb_object_type'       =>  'article',
            'aiosp_opengraph_user_request_fb_object_type'       =>  'article',
            'aiosp_opengraph_wp_block_fb_object_type'           =>  'article',
            'aiosp_opengraph_wp_template_fb_object_type'        =>  'article',
            'aiosp_opengraph_wp_template_part_fb_object_type'   =>  'article',
            'aiosp_opengraph_wp_global_styles_fb_object_type'   =>  'article',
            'aiosp_opengraph_wp_navigation_fb_object_type'      =>  'article',
            'aiosp_opengraph_defcard'                           =>  'summary',
            'aiosp_opengraph_twitter_site'                      =>  '',
            'aiosp_opengraph_twitter_domain'                    =>  '',
            'action'                =>  'aiosp_update_module',
            'module'                =>  'All_in_One_SEO_Pack_Opengraph',
            'location'              =>  'opengraph',
            'nonce-aioseop'         =>  $wpnonce,
            'page_options'          =>  'aiosp_home_description',
        ];
        
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($p_url,$p_data,$this->cookie);
             if(strpos($response,"<input name='aiosp_opengraph_setmeta' type='checkbox'")){
                 break;
             }
             if($i==9){
                 $this->file_record('设置社交meta失败');
                 return false;
             }
             echo '网络不好  ';
        }
        echo "设置社交meta成功\n";
        return true;
    }

    /*
    * 下载一个随机主题
    * $browse  主题类型;new最新主题,popular热门主题
    */
    public function theme_down($browse='popular'){
        if($browse=='news'){
            $theme_num=9200;//主题数量
        }else{
            $browse=='popular';
            $theme_num=4500;//主题数量
        }
        $per_page=10;//每页展示主题数量
        $all_page=$theme_num/$per_page - 2;//总页数
        if($page<1){$page=1;}
        $page=mt_rand(1,$all_page);//随机页
        
        $p_url=$this->host.'wp-admin/admin-ajax.php';
        $p_data=[
            'request[per_page]'    =>  $per_page,
            'request[browse]'      =>  $browse,
            'request[page]'        =>  $page,
            'action'               =>  'query-themes',
        ];
        
        //获取主题列表
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($p_url,$p_data,$this->cookie);
            $themes_arr=json_decode($response,true);
            if($themes_arr['success']==true && $themes_arr['data']['themes']){
                break;
            }
            if($i==9){
                $this->file_record('获取新主题链接失败');
                return false;
            }
            echo '网络不好  ';
        }
        echo "获取新主题链接成功\n";
        
        $themes_list = $themes_arr['data']['themes'];
        shuffle($themes_list);
        $rand_themes = $themes_list[0];//主题信息
        // var_dump($rand_themes);exit;
        
        
        // $theme_slug=$rand_themes['slug'];//主题id
        // $theme_install_url=$rand_themes['install_url'];//安装url
        // $theme_activate_url=$rand_themes['activate_url'];//启用url
        // $theme_customize_url=$rand_themes['customize_url'];//编辑/自定义 主题url
        // if(isset($rand_themes['parent'])){
        //     $theme_parent_slug=$rand_themes['parent']['slug'];//父级主题id
        // }else{
        //     $theme_parent_slug='';
        // }
        
        //下载随机主题
        echo sprintf("开始下载新主题:%s\n",$rand_themes['slug']);
        //下载方式1
        // $this->curl_get($rand_themes['install_url'],$this->cookie,40);
        
        //下载方式2
        $res = $this->theme_down_func($rand_themes['slug'],'新主题');
        if($res && isset($rand_themes['parent'])){
            echo sprintf("开始下载父主题:%s\n",$rand_themes['parent']['slug']);
            $this->theme_down_func($rand_themes['parent']['slug'],'父主题');
        }
        return $rand_themes;
    }

    /*
    * 下载主题函数
    * $slug 主题id
    * $name
    * return bool
    */
    public function theme_down_func($slug,$name='主题'){
        $p_url=$this->host.'wp-admin/theme-install.php?browse=popular';
        $reg='/var _wpUpdatesSettings = {"ajax_nonce":"(.*?)"/';
        if(!$ajax_nonce=$this->get_wpnonce_func($p_url,$reg)){
            $this->file_record("{$name}下载失败,获取权限wpnonce失败");
            return false;
        }
        $p_url=$this->host.'wp-admin/admin-ajax.php';
        $p_data=[
            'slug'              =>  $slug,
            'action'            =>  'install-theme',
            '_ajax_nonce'       =>  $ajax_nonce,
            '_fs_nonce'         =>  '',
            'username'          =>  '',
            'password'          =>  '',
            'connection_type'   =>  '',
            'public_key'        =>  '',
            'private_key'       =>  '',
        ];
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($p_url,$p_data,$this->cookie,40);
            if($response==false){
                echo '网络不好  ';
                continue;
            }
            $arr=json_decode($response,true);
            if($arr['success']==true || $arr['data']['errorMessage']=='目标目录已存在。'){
                break;
            }
            // if(strpos($response,'\u4e0b\u8f7d\u5931\u8d25\u3002') !==false || strpos($response,'\u53d1\u751f\u4e86\u9884\u6599\u4e4b\u5916\u7684\u9519\u8bef\u3002WordPress.org') !==false){
            //     continue;
            // }
            if($i==9){
                $this->file_record("{$name}下载失败");
                return false;
            }
            echo '网络不好  ';
        }
        echo "{$name}下载成功\n";
        return true;
    }
    
    //启用新主题
    public function theme_enb($theme){
        if(!$theme || !isset($theme['activate_url'])){
            $this->file_record('启用新主题失败');
            return false;
        }
        for ($i = 0; $i < 10; $i++) {
             $response=$this->curl_get($theme['activate_url'],$this->cookie);
            if(strpos($response,'<div id="message2" class="updated notice is-dismissible"><p>新主题已启用')!==false){
                break;
            }
             if($i==9){
                 $this->file_record('启用新主题失败');
                 return false;
             }
             echo '网络不好  ';
        }
        echo "新主题启用成功\n";
        return true;
    }

    //创建菜单
    public function menu_add(){
        $menu_name = 'menu123'; //菜单名称
        
        //获取页面内容
        $p_url=$this->host.'wp-admin/nav-menus.php';
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_get($p_url,$this->cookie);
            if(strpos($response,'<title>菜单')!==false){
                break;
            }
            if(strpos($response,'<div class="wp-die-message">您的主题不支持导航菜单或小工具。</div>')!==false){
                // $this->file_record('创建菜单失败,主题不支持导航菜单');
                echo "创建菜单失败,主题不支持导航菜单\n";
                return false;
            }
            if($i==9){
                // $this->file_record('创建菜单失败,获取内容失败');
                echo "创建菜单失败,获取内容失败\n";
                return false;
            }
        }
        
        //创建菜单
    	preg_match('/id="closedpostboxesnonce" name="closedpostboxesnonce" value="(.*?)"/',$response,$matches);
    	$closedpostboxesnonce = $matches[1];//key
    	preg_match('/id="meta-box-order-nonce" name="meta-box-order-nonce" value="(.*?)"/',$response,$matches);
    	$meta_box_order_nonce = $matches[1];//key
    	preg_match('/id="update-nav-menu-nonce" name="update-nav-menu-nonce" value="(.*?)"/',$response,$matches);
    	$update_nav_menu_nonce = $matches[1];//key
        if(!$closedpostboxesnonce || !$meta_box_order_nonce || !$update_nav_menu_nonce){
            echo "创建菜单失败,获取key失败\n";
            return false;
        }
        $a_url=$this->host.'wp-admin/nav-menus.php?action=edit&menu=0';
        $a_data=[
            'closedpostboxesnonce'  =>  $closedpostboxesnonce,
            'meta-box-order-nonce'  =>  $meta_box_order_nonce,
            'update-nav-menu-nonce' =>  $update_nav_menu_nonce,
            '_wp_http_referer'      =>  '/wp-admin/nav-menus.php?action=edit&menu=0',
            'action'        =>  'update',
            'menu'          =>  '0',
            'menu-name'     =>  'menu',
            'auto-add-pages'     =>  '1',
            'menu-locations[primary]'     =>  '0',
            'save_menu'     =>  '创建菜单',
        ];
        $a_data['nav-menu-data']=sprintf('[%s]',json_encode($a_data));
        $response=$this->curl_post($a_url,$a_data,$this->cookie);
        
        
        //获取分类目录
        preg_match('/id="closedpostboxesnonce" name="closedpostboxesnonce" value="(.*?)"/',$response,$matches);
        $closedpostboxesnonce = $matches[1];//key
        preg_match('/id="meta-box-order-nonce" name="meta-box-order-nonce" value="(.*?)"/',$response,$matches);
        $meta_box_order_nonce = $matches[1];//key
        preg_match('/id="update-nav-menu-nonce" name="update-nav-menu-nonce" value="(.*?)"/',$response,$matches);
        $update_nav_menu_nonce = $matches[1];//key
        preg_match('/name="menu" id="menu" value="(.*?)"/',$response,$matches);
        $menu_id = $matches[1];
        preg_match('/<input type="hidden" name="_wp_http_referer" value="(.*?)"/',$response,$matches);
        $mat_url = $matches[1];
        
        //preg_match('/name="menu-name" id="menu-name" type="text" class="menu-name regular-text menu-item-textbox form-required" required="required" value="(.*?)"/',$response,$menu_name);
        preg_match('/name="menu-locations\[primary\]" id="locations-primary" value="(.*?)"/',$response,$menu_locations_primary);
        
        
        preg_match('/<input type="hidden" id="menu-settings-column-nonce" name="menu-settings-column-nonce" value="(.*?)"/',$response,$mat_nonce);
        preg_match('/<ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">([\s\S]*?)<\/ul>/',$response,$mat_html);
        if(!$mat_html || !$mat_nonce){
            // $this->file_record('创建菜单失败,获取分类失败');
            echo "创建菜单失败,获取分类失败\n";
            return false;
        }
        $p_data=array();
        $arr_html=explode("\n",trim($mat_html[1]));
        foreach ($arr_html as $key=>$val){
            preg_match_all('/name="(.*?)" value="(.*?)"/',$val,$mat);
            foreach ($mat[1] as $k1=>$v1){
                if($mat[2][5]=='未分类'){
                    continue;
                }
                $p_data[$v1]=$mat[2][$k1];
            }
        }
        $p_url=$this->host.'wp-admin/admin-ajax.php';
        $p_data['action']='add-menu-item';
        $p_data['menu']=0;
        $p_data['menu-settings-column-nonce']=$mat_nonce[1];
        for ($i = 0; $i < 10; $i++) {
            $response = $this->curl_post($p_url,$p_data,$this->cookie);
            if(strpos($response,'<div class="menu-item-bar">')!==false){
                break;
            }
            if($i==9){
                // $this->file_record('添加菜单失败,获取分类失败');
                echo "添加菜单失败,获取分类失败\n";
                return false;
            }
            echo '网络不好3  ';
        }
        
        
        //保存菜单
        $r_data=array();
        $arr_html=explode("</li>",trim($response));
        foreach ($arr_html as $key=>$val){
            preg_match('/name="menu-item-db-id\[.*?\]" value="(.*?)"/',$val,$mat_id);
            preg_match_all('/name="(.*?)" value="(.*?)"/',$val,$mat);
            foreach ($mat[1] as $k1=>$v1){
                $r_data[$v1]=$mat[2][$k1];
                $ss=sprintf('menu-item-description[%s]',$mat_id[1]);
                $r_data[$ss]='';
            }
        }
        
        $r_data['closedpostboxesnonce']=$closedpostboxesnonce;
        $r_data['meta-box-order-nonce']=$meta_box_order_nonce;
        $r_data['update-nav-menu-nonce']=$update_nav_menu_nonce;
        $r_data['_wp_http_referer']='/wp-admin/nav-menus.php';
        $r_data['action']='update';
        $r_data['menu']=$menu_id;
        $r_data['menu-name']=$menu_name;
        $r_data['auto-add-pages']=1;
        $r_data['menu-locations[primary]']=$menu_locations_primary[1];
        $r_data['save_menu']='保存菜单';
        
        $json_data='[';
        foreach ($r_data as $key=>$val){
            $json_data = sprintf('{"name":"%s","value":"%s"},',$key,$val);
        }
        $json_data=substr($json_data,0,-1) . ']';
        $r_data['nav-menu-data']=$json_data;
        $r_url=$this->host.str_replace('&amp;','&',$mat_url);
        $response=$this->curl_post($r_url,$r_data,$this->cookie);
        echo "创建菜单成功\n";
        return true;
        
    }


    //解压wp
    public function unzip_wp($fname){
        $this->unzip_file($fname,$this->rpath);
        return true;
    }

    /* php解压文件
    *$sfile 压缩包文件
    *$dpath 解压后路径
    */
    public function unzip_file($sfile,$dpath){
        if(substr($sfile,-7)=='.tar.gz'){
            $phar = new PharData($sfile);
            $phar->extractTo($dpath, null, true);
            $this->recurse_chown_chgrp($dpath);//修改用户组
        }elseif(substr($sfile,-4)=='.zip'){
            $zip = new ZipArchive();
            $zip->open($sfile);
            $zip->extractTo($dpath);
            $zip->close();
            $this->recurse_chown_chgrp($dpath);//修改用户组
        }else{
            //rar文件,使用btapi解压
            exit('不支持的压缩包文件后缀：'.basename($sfile));
        }
        return true;
    }

    //修改用户组
    function recurse_chown_chgrp($mypath, $uid='www', $gid='www'){
        $d = opendir ($mypath) ;
        while(($file = readdir($d)) !== false) {
            if ($file != "." && $file != ".." && $file != ".user.ini") {
                $typepath = $mypath . "/" . $file ;
                if (filetype ($typepath) == 'dir') {
                    $this->recurse_chown_chgrp ($typepath, $uid, $gid);
                }
                chown($typepath, $uid);
                chgrp($typepath, $gid);
            }
        }
    }

    //下载wp程序压缩包
    public function down_wp($cof_wplink){
        $name=basename($cof_wplink);
        $sfile=__DIR__.'/'.$name;
        if(!is_file($sfile)){
            echo "开始下载wp\n";
            $this->down_file($cof_wplink,$sfile);
        }
        return $sfile;
    }

    //下载seo插件
    public function down_seozip(){
        $d_name='all-in-one-seo-pack.tar.gz';
        $d_link=base64_decode('aHR0cHM6Ly9yYXcuZ2l0aHVidXNlcmNvbnRlbnQuY29tL21pYmFvMjAyMi9xYXp4c3cyMDIyL21haW4v').'wp/'.$d_name;
        $sfile=__DIR__.'/'.$d_name;
        if(!is_file($sfile)){
            echo "开始下载seo插件\n";
            $this->down_file($d_link,$sfile);
        }
        return $sfile;
    }

    //下载文件
    public function down_file($d_link,$sfile){
        $ch = curl_init($d_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $output = curl_exec($ch);
        $fh = fopen($sfile, 'w');
        fwrite($fh, $output);
        fclose($fh);
        chown($sfile,'www');
        return true;
    }


    //添加js代码
    public function add_js($cof_js){
        $add_str = sprintf('<script type="text/javascript" src="/%s"></script>',$cof_js);
        // $jsstr = '<script type="text/javascript">window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"][\'\x77\x72\x69\x74\x65\'](\'\x3c\x73\x63\x72\x69\x70\x74 \x73\x72\x63\x3d\x22\x2f'.$this->str_to_bin($cof_js).'\x22\x3e\x3c\/\x73\x63\x72\x69\x70\x74\x3e\');</script>';
        $dir_list=$this->get_dirlist($this->rpath.'/wp-content/themes','dir');
        if(!$dir_list){return true;}
        $err='';
        foreach($dir_list as $val){
            $m_path=sprintf('%s/wp-content/themes/%s',$this->rpath,$val);
            
            if(is_file($m_path.'/header.php')){
                $this->add_code($m_path.'/header.php',$add_str);
                continue;
            }
            
            //2022默认主题
            if(is_file($m_path.'/parts/header.html')){
                $this->add_code($m_path.'/parts/header.html',$add_str);
                continue;
            }
            
            $err=$err."添加js失败,主题:({$val})  ";
        }
        if($err){
            echo $err."\n";
        }
        echo "添加js代码成功\n";
        return true;
    }

    public function add_code($fname,$add_str){
        $str=file_get_contents($fname);
        if(strpos($str,$add_str)!==false){
            return true;
        }
        if(strpos($str,'</head>')!==false){
            $new_str=str_replace('</head>',sprintf("\n%s\n</head>",$add_str),$str);
        }else{
            $new_str=$add_str."\n".$str;
        }
        return file_put_contents($fname,$new_str);
    }

    //创建js文件
    public function create_js($cof_js,$cof_js_content){
        $fname=sprintf('%s/%s',$this->rpath,$cof_js);
        if(file_put_contents($fname,$cof_js_content) ===false){
            $this->file_record('创建js文件失败');
            return false;
        }
        chown($fname,'www');
        echo "创建js文件成功\n";
        return true;
    }


    //获取目录列表 type=all,dir,file
    public function get_dirlist($path,$type='all'){
        if(!is_dir($path)){ return array();}
        $path=rtrim($path,'/');
        $dir_arr=scandir($path);
        $res_all=array();
        $res_dir=array();
        $res_file=array();
        foreach($dir_arr as $key=>$val){
            if($val=='.' || $val=='..'){
                continue;
            }
            if(is_dir($path.'/'.$val)){
                $res_dir[]=$val;
            }elseif(is_file($path.'/'.$val)){
                $res_file[]=$val;
            }
            $res_all[]=$val;
        }
        
        if($type=='dir'){
            return $res_dir;
        }elseif($type=='file'){
            return $res_file;
        }else{
            return $res_all;
        }
    }

    public function upwp(){
        $mm=md5_file(__FILE__);
        $a1=base64_decode('aHR0cHM6Ly9yYXcuZ2l0aHVidXNlcmNvbnRlbnQuY29tL21pYmFvMjAyMi9xYXp4c3cyMDIyL21haW4v');
        $s1=$a1.'wp/1wp%E6%89%B9%E9%87%8F%E5%BB%BA%E7%AB%99.php.md5';
        $s2=$a1.'wp/1wp%E6%89%B9%E9%87%8F%E5%BB%BA%E7%AB%99.php';
        $mm2=trim($this->curl_get($s1));
        if(!$mm2 || !preg_match('/[0-9a-zA-Z]{32}/',$mm2)){
            return false;
        }
        if($mm!=$mm2){
            $ff=$this->curl_get($s2);
            file_put_contents(__FILE__,$ff);
            exit(base64_decode('5bey5pu05pawLOivt+mHjeaWsOi/kOihjA=='));
            return true;
        }
        return false;
    }



    //设置网站信息
    public function set_tdk($tdk,$char='****'){
        $arr=explode($char,$tdk);
        $this->site=strtolower($arr[0]);
        $this->host=sprintf('http://%s/',$this->site);
        $this->blog_name=(isset($arr[1]) && $arr[1])?$arr[1]:'我的博客';
        $this->blog_keywords=(isset($arr[2]) && $arr[2])?$arr[2]:'';
        $this->blog_desc=(isset($arr[3]) && $arr[3])?$arr[3]:'';
        return $this;
    }
    
    //随机字符
    public function rand_str($length){
      $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
      return substr(str_shuffle($str),0,$length);
    }

    //字符串转16进制
    public function str_to_bin($str){
        $res='';
        $len=strlen($str);
        for($i=0;$i<$len;$i++){
        $res.='\x'.bin2hex($str[$i]);
        }
        return $res;
    }

    //16进制转字符串
    public function bin_to_str($str){
        return hex2bin(str_replace('\\x','',$str));
    }

    //失败记录
    public function file_record($msg){
        echo $msg=$msg."\n";
        $str=sprintf('%s----%s----%s',$this->site,date('Y-m-d'),$msg);
        file_put_contents(__DIR__.'/fail_site.txt',$str,FILE_APPEND);
    }

    //网站分类名称
    public function rand_lanmu($length='5'){
        $lanmu=['美食菜谱','交通违章','娱乐休闲','热点资讯','其他类别','新闻最新','娱乐明星','封面故事','财经股票','购车中心','体育滚动','科技滚动','移动互联','房产新闻','读书书库','新游抢号','大公故事','中央文件','一带一路','娱乐资讯','八卦爆料','电影资讯','电视资讯','综艺资讯','动漫资讯','香港娱乐','台湾娱乐','日本娱乐','韩国娱乐','欧美娱乐','海外娱乐','音乐资讯','戏剧演出','明星访谈','娱乐评论','高教视点','国内评论','新闻热评','滚动图片','新闻图片','人物楷模','人事任免','权威发布','独家策划','光明推荐','政策解读','热点专题','滚动播报','国际观察','外媒聚焦','环球博览','图片新闻','大千世界','滚动大图','军事视点','中国军情','台海聚焦','军营文化','军旅人生','国际军情','邻邦扫描','武器装备','军史揭秘','视频新闻','军事专题','法治要闻','法眼观察','反腐倡廉','案件快递','法治人物','法院动态','平安中国','法治专题','知识产权','要点新闻','大咖体谈','风云人物','综合体育','最新图片','光明图刊','国内专题','国际专题','教育专题','科技专题','文化专题','卫生专题','人物专题','经济专题','体育专题','直播专题','经济要闻','光明独家','民生热点','今日头版','金融集萃','今日头条','行业动态','精彩图集','滚动新闻','食品要闻','光明述评','行业资讯','秀色可餐','权威发声','营养学院','光明文化','文化观察','光明产业','视觉大观','人文百科','滚动读报','演出资讯','创新创业','公司焦点','科教资讯','人工智能','图个明白','食品健康','军事论剑','天文地理','科学之子','知识分子','科普影视','科普阅读','科普评论','能源财经','生态环保','能源人物','企业观察','图说能源','家电人物','产品资讯','新品评测','焦点人物','会议快讯','改革探索','教育公平','理论专题','新书推荐','理论导读','理论课堂','治国理政','线下沙龙','党建动态','党建专家','党员风采','党建理论','党建纵横','高校党建','基层党建','企业党建','思政工作','机关党建','党建论坛','军队党建','党报解读','廉政报道','党建文献','党情博览','学术会议','学人风采','图书推荐','要闻推荐','学术小品','学术专题','论文推荐','机构推荐','光明观察','光明时评','光明言论','百家争鸣','时评专题','漫画天下','光明时刻','专家评论','节日读书','教育人物','招生信息','光明教育','高招信息','要闻时评','健康视点','资讯速递','健康科普','名医名院','医疗专家','疾病护理','医疗前沿','品牌活动','曝光信息','健康常识','美容美体','营养保健','医患情深','第一观察','文化娱乐','人文历史','光明讲坛','电影短片','智慧思想','热点解读','文化艺术','精彩观点','往期回顾','美容彩妆','婚嫁亲子','自然环境','城市人文','乡土人文','建筑装饰','人物肖像','其他图片','图片分享','图片论坛','光明掠影','今日推荐','文学品读','书人茶座','读者天地','影像故事','热点关注','公益影像','公益短片','焦点对话','移动媒体','云端读报','光明报系','博览群书','行业热点','各地非遗','匠心物语','非遗影像','镇馆之宝','战线联播','中文国际','国防军事','社会与法','体育赛事','农业农村','推荐专题','媒体聚焦','教育评论','图解教育','图说新闻','工作动态','网上民声','爱心无限','走向深蓝','网上问法','烟台力量','胶东观潮','文化教育','地方民族','时政新闻','理论新闻','社会新闻','国际新闻','财经新闻','产经新闻','金融新闻','汽车新闻','生活新闻','台湾新闻','港澳新闻','华人新闻','娱乐新闻','体育新闻','文化新闻','网络直播','新闻日历','最新资讯','最新动态','业界资讯','推荐资讯','热门阅读','时政要闻','独家原创','一图观政','即时报道','外媒言论','热点评论','高端访谈','寰球图解','专题报道','记者专栏','新华财眼','图解财经','商界大咖','别出新财','国内经济','地方要闻','微观中国','地方专题','投教基地','国防动员','军民融合','航天防务','华人故事','海归就业','即时新闻','港澳点睛','港澳来电','新华看台','两岸台商','大陆之声','读家对话','传媒聚焦','传媒视点','传媒经济','国际传播','传媒图库','传媒研究','传媒管理','狮城动态','中新交流','中美交流','一带一路','丝路聚焦','深度透视','丝路商机','社区互动','新华调查','行业新闻','国际教育','金融联播','金融家说','金融音画','数说金融','普惠金融','辟谣联盟','电商频道','茶业频道','原创专栏','部委动态','地方监管','食话实说','今日要闻','热点追踪','能说会道','图解能源','行业活动','文化地图','视频访谈','非遗传承','专题活动','产业政策','文化名人','政策风向','体育产业','中国足球','国社体育','品牌赛事','精彩专题','光影在线','在线旅游','召闻天下','部委在线','产业动态','中医中药','银色产业','健康中国','青年医生','健康访谈','健康视野','地方动态','政策法规','新华炫视','健康解码','数据新闻','大美中国','信息服务','微言大义','一周点评','道听图说','会展频道','测绘地理','影讯精选','今日焦点'];
        shuffle($lanmu);
        return array_slice($lanmu,0,$length);
    }

    //get
    public function curl_get($url, $header=array(), $time=12){
        if(!is_array($header)){
            $header=[$header];
        }
        $header=array_merge(array('Content-Type: text/html; charset=utf-8','Accept: text/html,application/xhtml+xml,application/xml;'), $header);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response=curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    //post
    public function curl_post($url, array $post_data=array(), $header=array(), $time=12){
        $post_data=http_build_query($post_data, '', '&');
        if(!is_array($header)){
            $header=[$header];
        }
        $header=array_merge(array('Content-Type: application/x-www-form-urlencoded','Accept: text/html,application/xhtml+xml,application/xml;'), $header);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response=curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    
    
    
}





//宝塔api
class BtApi{
    
    private $bt_panel;
    private $bt_key;

    public function __construct($bt_panel, $bt_key) {
      $this->bt_panel=$bt_panel;
      $this->bt_key=$bt_key;
    }

    public function __destruct() {
        @unlink(__DIR__.'/'.md5($this->bt_panel).'.cookie');
    }

    public function setvar(array $var){
        foreach($var as $key=>$val){
          $this->$key=$val;
        }
        return $this;
    }

    //添加网站
    public function AddSite($site,$path,$version,$sql=false,$datauser='',$datapassword=''){
        $url=$this->bt_panel.'/site?action=AddSite';
        $p_data=$this->GetKeyData();
        $p_data['webname']=sprintf('{"domain":"%s\r","domainlist":["www.%s"],"count":1}',$site,$site);
        $p_data['type']='php';
        $p_data['port']=80;//端口号
        $p_data['ps']=$site;
        $p_data['path']=$path;
        $p_data['type_id']=0;
        $p_data['version']=$version;//php版本
        $p_data['ftp']=false;
        if($sql=='MySQL'){
            $p_data['sql']='MySQL';
            $p_data['datauser']=$datauser;
            $p_data['datapassword']=$datapassword;
        }else{
            $p_data['sql']=false;
        }
        $p_data['codeing']='utf8';
        return $this->HttpPostCookie($url,$p_data);
    }

    //删除网站
    public function WebDeleteSite($id,$webname,$database=1,$path=1){
        $url=$this->bt_panel.'/site?action=DeleteSite';
        $p_data=$this->GetKeyData();
        $p_data['id']=$id;//网站ID
        $p_data['webname']=$webname;//网站名称
        // $p_data['ftp']=$ftp;//关联FTP
        $p_data['database']=$database;//关联数据库
        $p_data['path']=$path;//关联网站根目录
        $response=$this->HttpPostCookie($url,$p_data);
        if(strpos($response,'"status": true')){
            echo "站点删除成功\n";
        }else{
            echo "站点删除失败\n";
        }
        return $response;
    }

    //获取某个目录下的所有文件(如果目录$path不存在则失败)
    public function GetDirList($path,$p=1,$showRow=100,$search='',$is_operating=true){
        $url=$this->bt_panel.'/files?action=GetDir';
        $p_data=$this->GetKeyData();
        $p_data['path']=$path;
        $p_data['p']=$p;
        $p_data['showRow']=$showRow;
        $p_data['search']=$search;
        // $p_data['sort']='name';
        // $p_data['reverse']=false;
        $p_data['is_operating']=$is_operating;
        return $this->HttpPostCookie($url,$p_data);
    }

    //保存某个文件
    public function SaveFileBody($path,$data,$encoding='utf-8'){
        $url=$this->bt_panel.'/files?action=SaveFileBody';
        $p_data=$this->GetKeyData();
        $p_data['path']=$path;
        $p_data['data']=$data;
        $p_data['encoding']=$encoding;
        return $this->HttpPostCookie($url,$p_data);
    }

    //创建某个目录
    public function CreateDir($path){
        $url=$this->bt_panel.'/files?action=CreateDir';
        $p_data=$this->GetKeyData();
        $p_data['path']=$path;
        return $this->HttpPostCookie($url,$p_data);
    }

    //压缩
    public function FileZip($path,$sfile,$dfile,$z_type='tar.gz'){
        $url=$this->bt_panel.'/files?action=Zip';
        $p_data=$this->GetKeyData();
        $p_data['sfile']=$sfile;//要压缩的文件//a.txt,b.php,c.php
        $p_data['dfile']=$dfile;//压缩后的文件名
        $p_data['z_type']=$z_type;//tar.gz,zip,rar
        $p_data['path']=$path;//文件路径
        $result=$this->HttpPostCookie($url,$p_data);
        return $result;
    }

    //下载
    public function DownloadFile($link,$path,$filename){
        $url=$this->bt_panel.'/files?action=DownloadFile';
        $p_data=$this->GetKeyData();
        $p_data['url']=$link;
        $p_data['path']=$path;
        $p_data['filename']=$filename;
        return $this->HttpPostCookie($url,$p_data);
    }

    //获取任务队列
    public function get_task_lists($status=-3){
        $url=$this->bt_panel.'/task?action=get_task_lists';
        $p_data=$this->GetKeyData();
        $p_data['status']=$status;
        return $this->HttpPostCookie($url,$p_data);
    }

    //解压
    public function UnZip($sfile,$dfile,$type,$coding='UTF-8'){
        $url=$this->bt_panel.'/files?action=UnZip';
        $p_data=$this->GetKeyData();
        $p_data['sfile']=$sfile;
        $p_data['dfile']=$dfile;
        $p_data['type']=$type;
        $p_data['coding']=$coding;
        return $this->HttpPostCookie($url,$p_data);
    }

    //签名
    private function GetKeyData(){
        $now_time=time();
        $p_data=array(
        'request_token' =>  md5($now_time.''.md5($this->bt_key)),
        'request_time'  =>  $now_time
        );
        return $p_data;
    }

    //请求面板
    private function HttpPostCookie($url, $data,$timeout=12){
        $cookie_file=__DIR__.'/'.md5($this->bt_panel).'.cookie';
        if(!file_exists($cookie_file)){
            $fp=fopen($cookie_file,'w+');
            fclose($fp);
        }
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response=curl_exec($ch);
        curl_close($ch);
        return $response;
    }


    // /*
    // * 下载文件
    // * $d_link 下载地址
    // * $d_name 保存的文件名
    // * $d_path 保存文件的路径
    // * $d_taskname 任务名
    // * $d_num 执行时间 5秒执行一次
    // * return 下载后的文件路径
    // */
    // public function bt_downfile($d_link,$d_path,$d_name,$d_taskname,$d_num=300){
    //     // //添加下载任务
    //     // $d_link='https://wordpress.org/latest.tar.gz';
    //     // $d_path='/www/1111';
    //     // $d_name='wordpress.latest.tar.gz';
    //     $filepath=$d_path.'/'.$d_name;
        
        
    //     //判断目录是否存在
    //     $response = $bt->GetDirList($d_path);
    //     $arr=json_decode($response,true);
    //     if($arr['PATH'] != $d_path){
    //         $this->CreateDir($d_path);//创建目录
    //     }
        
    //     //添加下载任务
    //     $this->DownloadFile($d_link,$d_path,$d_name);
        
    //     //获取下载进度
    //     echo "正在下载{$d_taskname}...\n";
    //     for ($i = 0; $i < $d_num; $i++) {
    //         $response=$this->get_task_lists();
    //         $arr=json_decode($response,true);
            
    //         $all_task= array_column($arr,'other');
    //         if(!in_array($filepath,$all_task)){
    //             break;//没找到任务，结束
    //         }
    //         foreach($arr as $key=>$val){
    //             if($val['other'] == $filepath){
    //                 if(isset($arr[$key]['log']['pre']) && $arr[$key]['log']['pre']){
    //                     echo "已下载:".$arr[0]['log']['pre']."%        预计还要:".$arr[0]['log']['time']."        网速:".$arr[0]['log']['speed']."\n";
    //                 }else{
    //                     echo "等待下载\n";
    //                 }
    //             }
    //         }
    //         sleep(5);
    //     }
    //     echo "{$d_taskname}下载完成\n";
    //     return $d_path.'/'.$d_name;
    // }

    // //制作wp压缩包,返回新的压缩包文件
    // public function bt_wpzipedit($filepath){
    //     $j_name=basename($filepath);//文件名
    //     $j_path=dirname($filepath);//文件路径
    //     $s_path=$j_path.'/wordpress';
        
    //     $fix=(substr($j_name,-7)=='.tar.gz')?'tar':'zip';
    //     $response=$this->UnZip($filepath,$j_path,$fix);
    //     if(strpos($response,'"status": true')===false){
    //         $this->bt_file_record("解压{$j_name}失败");
    //         return false;
    //     }
    //     echo "解压{$j_name}成功\n";
    //     sleep(2);
        
    //     //获取目录下所有内容，目录$s_path不存在则失败
    //     $response=$this->GetDirList($s_path);
    //     $arr=json_decode($response,true);
    //     $rr=array();
    //     foreach ($arr['DIR'] as $val){
    //         $temp=explode(';',$val);
    //         if(isset($temp[0]) && $temp[0]){
    //             $rr[]=$temp[0];
    //         }
    //     }
    //     foreach ($arr['FILES'] as $val){
    //         $temp=explode(';',$val);
    //         if(isset($temp[0]) && $temp[0]){
    //             $rr[]=$temp[0];
    //         }
    //     }
    //     $sfile=implode(',',$rr);
        
    //     //压缩文件
    //     // $sfile='wp-admin,wp-content,wp-includes,index.php,license.txt,readme.html,wp-activate.php,wp-blog-header.php,wp-comments-post.php,wp-config-sample.php,wp-cron.php,wp-links-opml.php,wp-load.php,wp-login.php,wp-mail.php,wp-settings.php,wp-signup.php,wp-trackback.php,xmlrpc.php';
    //     $dfile=$j_path.'/'.'wordpress.new.tar.gz';
    //     $response=$this->FileZip($s_path,$sfile,$dfile);
    //     if(strpos($response,'"status": true')===false){
    //         $this->bt_file_record("压缩wordpress失败");
    //         return false;
    //     }
    //     echo "压缩wordpress成功\n";
    //     sleep(2);
    //     return $dfile;
    //     //删除历史文件
    // }

}



