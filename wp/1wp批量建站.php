<?php
/**
 * 创建wordpress站点
 * 文件放在服务器上运行
 * 使用时关闭禁止海外访问！
 * 22/07/01

php /www/1111/1wp批量建站.php


*/



//-----------------------------------------------
//--------------------设置开始--------------------
//宝塔面板地址*
$cof_panel='http://111.111.111.111:8888/';
//宝塔API接口密钥*
$cof_key='11111111xpGkV8CCRlJtwOCG4uQ0fxDp';
//网站使用的php版本(推荐php7.0以上版本)
$cof_php_v='7.2';


//wp网站后台账号*
$cof_admin_name='admin1234';
//wp网站后台密码*
$cof_admin_password='Qq12345678';


//统计js名字
$cof_js_name = 'tj.js';
//统计js内容 （没有<script>标签）
$cof_js_cont=<<<'EOLJSCONT'

var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "https://hm.baidu.com/hm.js?111111111111111111111111111111";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();

EOLJSCONT;
//设置建站域名的文件* (内容格式:域名****网站标题****网站关键词****描述)
$cof_site_file='site.txt';
//------------------设置结束----------------------
//------------------------------------------------
//------------------------------------------------
//------------------------------------------------
//wp压缩包文件路径，或者下载链接
$cof_wplink='https://raw.githubusercontent.com/mibao2022/qazxsw2022/main/wp/wordpress_laster.tar.gz';
//wp插件文件路径，或者下载链接
$cof_pluginlink='https://raw.githubusercontent.com/mibao2022/qazxsw2022/main/wp/all-in-one-seo-pack.tar.gz';
//下载模板主题类型popular(有4000个主题) 或 new(有8000个主题)
$cof_browse = 'popular';
//wp管理邮箱，不写使用随机字符串
$cof_email='';
//wp用户昵称，即文章作者，不写使用随机字符串
$cof_nickname='';
//宝塔网站路径
$wwwroot='/www/wwwroot';
//网站伪静态
$cof_rewrite='if (!-e $request_filename) {
    rewrite  ^(.*)$  /index.php/$1  last;
    break;
 }';
//--------------------------------------------
//--------------------------------------------
//---------------代码开始---------------------
//--------------------------------------------
//--------------------------------------------

if(!is_dir('/www/server')){
    exit('文件需要放到服务器上运行');
}
$cof_panel=rtrim($cof_panel,'/');
$cof_key=trim($cof_key);
if(!preg_match('/[0-9a-zA-Z]{32}/',$cof_key)){
    exit("设置正确的宝塔API接口密钥\n");
}

$cof_php_v=intval(str_replace('.','',$cof_php_v));
if(!$cof_php_v || $cof_php_v<70) exit("php版本大于7.0\n");

$cof_admin_name=trim($cof_admin_name);
if(!$cof_admin_name) exit("网站后台账户为空\n");

$cof_admin_password=trim($cof_admin_password);
if(!$cof_admin_password || strlen($cof_admin_password)<8) exit("网站后台密码长度小于8位数\n");

$cof_js_name = trim($cof_js_name);
$cof_js_cont = trim($cof_js_cont);
if(!$cof_js_name){
    exit("设置统计js\n");
}

$cof_site_file=trim($cof_site_file);
if($cof_site_file[0] != '/'){
    $cof_site_file= __DIR__ .'/'.$cof_site_file;
}
if(!is_readable($cof_site_file)) exit("设置建站域名的文件\n");

if(!$cof_wplink) exit("设置wp压缩包路径\n");
if(!$cof_browse) exit("设置下载模板主题类型\n");

//读取域名
$site_str=file_get_contents($cof_site_file);
$site_arr=explode("\n", trim($site_str));
$site_arr=array_values(array_filter(array_map('trim',$site_arr)));
if(empty($site_arr)) exit('设置建站域名');


set_time_limit(0);
$wp=new WordPress();
$bt=new BtApi($cof_panel,$cof_key);
if($cof_wplink[0] == '/' && is_file($cof_wplink)){
    $wp_zipfile=$cof_wplink;
}else{
    $wp_zipfile=$wp->down_wp($cof_wplink,'开始下载wp');
}
if($cof_pluginlink[0] == '/' && is_file($cof_pluginlink)){
    $seo_zipfile=$cof_pluginlink;
}else{
    $seo_zipfile=$wp->down_wp($cof_pluginlink,'开始下载插件');
}

$bt->SetFileAccess($wp_zipfile);
$bt->SetFileAccess($seo_zipfile);
$wp_zipfile_fix= (substr($wp_zipfile,-7)=='.tar.gz')?'tar':'zip';
$seo_zipfile_fix= (substr($seo_zipfile,-7)=='.tar.gz')?'tar':'zip';

$wp->browse = $cof_browse;
$wp->js_name=$cof_js_name;
$wp->js_cont=$cof_js_cont;
$wp->admin_name=$cof_admin_name;
$wp->admin_password=$cof_admin_password;

foreach($site_arr as $key=>$val){
    $wp->set_tdk($val);
    $site=$wp->site;
    $rpath=$wwwroot.'/'.$site;
    $db_name=substr(str_replace(['.','-'], '_', $site),0,16);
    $db_pwd=$wp->rand_str(16);
    $rand_str=strtolower($wp->rand_str(mt_rand(6,9)));
    if(empty($cof_email)){
        $tmp_email=$rand_str.'@gmail.com';
    }
    if(empty($cof_nickname)){
        $tmp_nickname=$rand_str;
    }
    $wp->setvar([
        'rpath'=>$rpath,
        'db_name'=>$db_name,
        'db_pwd'=>$db_pwd,
        'blog_email'=>$tmp_email,
        'blog_nickname'=>$tmp_nickname,
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
	if( $web_data['databaseStatus'] === false ){
		$wp->file_record('数据库创建失败');
		$bt->WebDeleteSite($web_data['siteId'],$site);
		continue;
	}
    @unlink($rpath.'/index.html');
    
    //解压程序 (改btapi解压)
    $bt->UnZip($wp_zipfile,$rpath,$wp_zipfile_fix);
    sleep(1);
    
    //设置网站伪静态
    $response=$bt->SaveFileBody(sprintf('/www/server/panel/vhost/rewrite/%s.conf',$site), $cof_rewrite);
    if(strpos($response,'文件已保存')===false){
        $wp->file_record('伪静态设置失败');
    }else{
        echo "伪静态设置成功\n";
    }
    
    //安装wp
    if(!$wp->install()){
        $wp->file_record('安装wp失败');
        $bt->WebDeleteSite($web_data['siteId'],$site);
        continue;
    }
    
    //登录wp
    if(!$wp->login()){
        $wp->file_record('登录wp失败');
        $bt->WebDeleteSite($web_data['siteId'],$site);
        continue;
    }
    
    //解压seo插件 (改btapi解压)
    $bt->UnZip($seo_zipfile,$rpath.'/wp-content/plugins',$seo_zipfile_fix);
    
    
    //网站设置
    $wp->setting();
    
    
    $wp->addtjjs();
    
    
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
    public $blog_nickname;
    
    public $browse;
    public $js_name;
    public $js_cont;
    
    public function __construct(){
    
    }

    public function setvar(array $var){
        foreach($var as $key=>$val){
          $this->$key=$val;
        }
        return $this;
    }

    //网站设置
    public function setting(){

        //常规设置
        $this->options_general();
        
        //评论设置
        $this->options_discussion();
        
        //固定链接设置
        $this->options_permalink();
        
        //编辑个人资料
        $this->options_profile();
        
        //添加分类
        $this->category_add();
        
        //下载随机主题
        $this->theme_down();
        
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
                exit("修改wp文件权限为755\n");
            }
            if($i==9){
                $this->file_record('安装失败,网络连接失败');
                return false;
            }
            echo "网络不好\n";
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
            echo "网络不好\n";
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
            echo "网络不好\n";
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
            echo "网络不好\n";
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
            echo "网络不好\n";
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
    	    echo "网络不好\n";
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
        //文章路由
        $permalink_arr=array(
            '/news/%post_id%.html',
            '/%year%%monthnum%%day%/%post_id%.html',
            '/archives/%post_id%.html',
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

    //编辑个人资料
    public function options_profile(){
        $p_url=$this->host.'wp-admin/profile.php';
        $reg='/<input type="hidden" id="_wpnonce" name="_wpnonce" value="(.*?)"/';
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_get($p_url,$this->cookie);
            if(strpos($response,'<form name="loginform" id="loginform"')!==false){
                $this->login();//未登录
            }
            preg_match($reg,$response,$mat);
            if(isset($mat[1]) && preg_match('/[0-9a-zA-Z]{10}/',$mat[1])){
                break;
            }
            if($i==9){
                echo "编辑个人资料失败\n";
                // $this->file_record('编辑个人资料失败');
                return false;
            }
            echo "网络不好\n";
        }
        
        //更新
        $wpnonce = $mat[1];
        preg_match('/<input type="hidden" name="checkuser_id" value="(.*?)"/',$response,$mat_checkuser_id);
        preg_match('/<input type="hidden" name="user_id" id="user_id" value="(.*?)"/',$response,$mat_user_id);
        preg_match('/<input type="hidden" name="_wp_http_referer" value="(.*?)"/',$response,$mat_referer);
        preg_match('/<input type="hidden" name="from" value="(.*?)"/',$response,$mat_profile);
        preg_match('/<input type="hidden" id="color-nonce" name="color-nonce" value="(.*?)"/',$response,$mat_color_nonce);
        $p_data=[
            '_wpnonce'              =>  $wpnonce,
            '_wp_http_referer'      =>  $mat_referer[1],
            'from'                  =>  $mat_profile[1],
            'checkuser_id'          =>  $mat_checkuser_id[1],
            'color-nonce'           =>  $mat_color_nonce[1],
            'admin_color'           =>  'fresh',
            'admin_bar_front'       =>  '0',//在浏览站点时显示工具栏;1是
            'locale'                =>  'zh_CN',
            'first_name'            =>  '',
            'last_name'             =>  '',
            'nickname'              =>  $this->blog_nickname,//昵称
            'display_name'          =>  $this->blog_nickname,//公开显示为
            'email'                 =>  $this->blog_email,//邮箱
            'url'                   =>  sprintf('http://%s',$this->site),//网站地址
            'description'           =>  '',
            'pass1'                 =>  '',
            'pass2'                 =>  '',
            'action'                =>  'update',
            'user_id'               =>  $mat_user_id[1],
            'submit'                =>  '更新个人资料',
        ];
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($p_url,$p_data,$this->cookie);
            if(strpos($response,'<p><strong>个人资料已更新。</strong></p>')){
                break;
            }
            if($i==9){
                echo "编辑个人资料失败\n";
                // $this->file_record('编辑个人资料失败');
                return false;
            }
            echo '网络不好';
        }
        echo "编辑个人资料成功\n";
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
        
        $num=5;//栏目数量
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
        
        foreach($arr as $key=>$val){
            $p_data['tag-name']=$val;//分类名称
            $p_data['slug']= is_numeric($key) ? sprintf('column%s',$key) : $key;//别名,url规则
            $this->curl_post($p_url,$p_data,$this->cookie);
        }
        
        
        echo "添加分类成功\n";
        return true;
    }


    //启用seo插件
    public function plugin_seo_enb(){
        $reg_en='/<strong>All In One SEO Pack<\/strong><div class="row-actions visible"><span class=\'activate\'><a href="(.*?)" id="activate-all-in-one-seo-pack"/';
        $reg_zh='/<strong>多合一SEO包<\/strong><div class="row-actions visible"><span class=\'activate\'><a href="(.*?)" id="activate-all-in-one-seo-pack"/';
        $res = $this->plugin_enb_func($reg_en,$reg_zh,'seo插件');
        $this->curl_get($this->host.'wp-admin/index.php?page=aioseop-welcome',$this->cookie);//初始化
        return $res;
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
            echo "网络不好\n";
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
            echo "网络不好\n";
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
            echo "网络不好\n";
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
             echo "网络不好\n";
        }
        echo "设置社交meta成功\n";
        return true;
    }

    /*
    * 下载一个随机主题
    * $browse  主题类型;new最新主题,popular热门主题
    */
    public function theme_down(){
        $browse = $this->browse;
        if($browse=='new'){
            $theme_num=9200;//主题数量
        }else{
            $browse=='popular';
            $theme_num=4500;//主题数量
        }
        $per_page=10;//每页展示主题数量
        $all_page=$theme_num/$per_page - 2;//总页数
        if($all_page<1){$all_page=1;}
        $page=mt_rand(1,$all_page);//随机页
        
        $p_url=$this->host.'wp-admin/admin-ajax.php';
        $p_data=[
            'request[per_page]'    =>  $per_page,
            'request[browse]'      =>  $browse,
            'request[page]'        =>  $page,
            'action'               =>  'query-themes',
        ];
        
        //获取随机主题列表
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($p_url,$p_data,$this->cookie);
            if(strpos($response,'Briefly unavailable for scheduled maintenance')!==false){
                echo "{等待5秒}：Briefly unavailable for scheduled maintenance\n";
                sleep(5);
            }
            
            $themes_arr=json_decode($response,true);
            if($themes_arr['success']==true && $themes_arr['data']['themes']){
                break;
            }
            if($i==9){
                $this->file_record('新主题链接获取失败');
                return false;
            }
            echo "网络不好\n";
        }
        echo "新主题链接获取成功\n";
        
        $themes_list = $themes_arr['data']['themes'];
        shuffle($themes_list);
        $rand_themes = $themes_list[0];//主题信息
        
        
        //$rand_themes['slug'];//主题id
        //$rand_themes['install_url'];//安装url
        //$rand_themes['activate_url'];//启用url
        //$rand_themes['customize_url'];//编辑/自定义 主题url
        // if(isset($rand_themes['parent'])){
        //     $rand_themes['parent']['slug'];//父级主题id
        // }
        
        //下载随机主题
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_get($rand_themes['install_url'],$this->cookie,55);
            if(strpos($response,'Briefly unavailable for scheduled maintenance')!==false){
                echo "{等待5秒}：Briefly unavailable for scheduled maintenance\n";
                sleep(5);
            }
            if(strpos($response,'</strong>成功。</p>')!==false || strpos($response,'<p>目标目录已存在')!==false){
                break;
            }
            if($i==9){
                // $this->file_record('新主题下载失败');
                echo "新主题下载失败\n";
                return false;
            }
            echo "网络不好\n";
            
        }
        echo sprintf("新主题下载成功:%s\n",$rand_themes['slug']);
        
        
        if(isset($rand_themes['parent'])){
            echo sprintf("开始下载父主题:%s\n",$rand_themes['parent']['slug']);
        }
        
        //启用主题
        $pp = sprintf('%s/wp-content/themes/%s',$this->rpath,strtolower($rand_themes['slug']));
        if(isset($rand_themes['parent']['slug'])){
            $pp2 = sprintf('%s/wp-content/themes/%s',$this->rpath,strtolower($rand_themes['parent']['slug']));
        }
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_get($rand_themes['activate_url'],$this->cookie);
            if(strpos($response,'<p>新主题已启用')!==false){
                break;
            }elseif(strpos($response,'<p>当前启用的主题已受损')!==false){
                //主题受损，重新下载
                echo "新主题受损,重新下载\n";
                $this->deldir($pp);
                if(isset($rand_themes['parent']['slug'])){
                    $this->deldir($pp2);
                }
                $this->theme_down();
            }elseif(strpos($response,'<p>此站点遇到了致命错误')!==false || strpos($response,'<p>请求的主题不存在')!==false){
                //主题报错，重新下载
                echo "新主题报错,重新下载\n";
                $this->deldir($pp);
                if(isset($rand_themes['parent']['slug'])){
                    $this->deldir($pp2);
                }
                $this->theme_down();
            }
            
            if($i==5){
                // $this->file_record('启用新主题失败');
                echo "新主题启用失败\n";
                return false;
            }
            echo "网络不好\n";
        }
        echo "新主题启用成功\n";
        return true;
    }


    //创建菜单
    public function menu_add(){
        $menu_name = 'menu123'; //菜单名称
        
        //1获取页面内容
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
                echo "创建菜单失败,1获取页面内容\n";
                return false;
            }
            echo '网络不好1  ';
        }
        
        
        //2创建菜单
    	preg_match('/id="closedpostboxesnonce" name="closedpostboxesnonce" value="(.*?)"/',$response,$mat_closedpostboxesnonce);
    	preg_match('/id="meta-box-order-nonce" name="meta-box-order-nonce" value="(.*?)"/',$response,$mat_meta_box_order_nonce);
    	preg_match('/id="update-nav-menu-nonce" name="update-nav-menu-nonce" value="(.*?)"/',$response,$mat_update_nav_menu_nonce);
        if(!isset($mat_closedpostboxesnonce[1]) || !isset($mat_meta_box_order_nonce[1]) || !isset($mat_update_nav_menu_nonce[1])){
            echo "创建菜单失败,获取key失败\n";
            return false;
        }
        $closedpostboxesnonce = $mat_closedpostboxesnonce[1];//下面复用
        $meta_box_order_nonce = $mat_meta_box_order_nonce[1];//下面复用
        $update_nav_menu_nonce = $mat_update_nav_menu_nonce[1];//下面复用
        //获取菜单显示位置 $res_loca
        preg_match('/<fieldset class="menu-settings-group menu-theme-locations">([\s\S]*?)<\/fieldset>/',$response,$mat_loca);
        if(isset($mat_loca[1]) && $mat_loca[1]){
            preg_match_all('/name="(.*?)"/',$mat_loca[1],$mat_name);
            preg_match_all('/value="(.*?)"/',$mat_loca[1],$mat_value);
            $cnum=count($mat_name[1]);
            $res_loca=array();
            for ($i = 0; $i < $cnum; $i++) {
                $key=$mat_name[1][$i];
                 $res_loca[$key]=$mat_value[1][$i];
                //  $res_loca[$key]='0';
            }
        }
        $a_url=$this->host.'wp-admin/nav-menus.php?action=edit&menu=0';
        $a_data=[
            'closedpostboxesnonce'  =>  $closedpostboxesnonce,
            'meta-box-order-nonce'  =>  $meta_box_order_nonce,
            'update-nav-menu-nonce' =>  $update_nav_menu_nonce,
            '_wp_http_referer'      =>  '/wp-admin/nav-menus.php?action=edit&menu=0',
            'action'        =>  'update',
            'menu'          =>  '0',
            'menu-name'     =>  $menu_name,
            // 'auto-add-pages'     =>  '1',//自动将新的顶级页面添加至此菜单
        ];
        if(isset($res_loca) && is_array($res_loca)){
            $a_data=array_merge($a_data,$res_loca);
        }
        $a_data['nav-menu-data']=sprintf('[%s]',json_encode($a_data));
        $a_data['save_menu']='创建菜单';
        for ($i = 0; $i < 10; $i++) {
            $response=$this->curl_post($a_url,$a_data,$this->cookie);
            if(strpos($response,'class="menu-name regular-text menu-item-textbox form-required" required="required" value="'.$menu_name.'"')!==false){
                break;
            }elseif(strpos($response,'和另一菜单名称冲突，请另选一个名称。') !== false){
                echo "菜单:{$menu_name}已存在\n";
                ////$this->menu_edit($menu_name);//编辑菜单
                return true;
            }
            if($i==9){
                echo "创建菜单失败,2创建菜单\n";
                return false;
            }
            echo '网络不好2  ';
        }
        
        
        //3分类添加至菜单
        preg_match('/<input type="hidden" name="menu" id="menu" value="(.*?)"/',$response,$mat_menu);
        preg_match('/<input type="hidden" id="menu-settings-column-nonce" name="menu-settings-column-nonce" value="(.*?)"/',$response,$mat_column_nonce);
        preg_match('/<ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">([\s\S]*?)<\/ul>/',$response,$mat_html);
        if(!isset($mat_menu[1]) || !isset($mat_column_nonce[1]) || !isset($mat_html[1])){
            echo "创建菜单失败,获取分类失败\n";
            return false;
        }
        $menu = $mat_menu[1];//下面复用 菜单id
        $menu_settings_column_nonce=$mat_column_nonce[1];
        $html=$mat_html[1];
        
        //获取菜单显示位置 $res_loca_new value值变化
        preg_match('/<fieldset class="menu-settings-group menu-theme-locations">([\s\S]*?)<\/fieldset>/',$response,$mat_loca);
        if(isset($mat_loca[1]) && $mat_loca[1]){
            preg_match_all('/name="(.*?)"/',$mat_loca[1],$mat_name);
            preg_match_all('/value="(.*?)"/',$mat_loca[1],$mat_value);
            $cnum=count($mat_name[1]);
            $res_loca_new=array();
            for ($i = 0; $i < $cnum; $i++) {
                $key=$mat_name[1][$i];
                 $res_loca_new[$key]=$mat_value[1][$i];
            }
        }
        //获取分类信息
        $arr_html=explode("\n",trim($html));
        $p_data=array();
        foreach ($arr_html as $val){
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
        $p_data['menu']=$menu;
        $p_data['menu-settings-column-nonce']=$menu_settings_column_nonce;
        for ($i = 0; $i < 10; $i++) {
            $response = $this->curl_post($p_url,$p_data,$this->cookie);
            if(strpos($response,'<div class="menu-item-bar">')!==false){
                break;
            }
            if($i==9){
                echo "添加菜单失败,3分类添加至菜单\n";
                return false;
            }
            echo '网络不好3  ';
        }
        
        
        //4保存菜单
        $arr_html=explode("</li>",trim($response));
        $r_data=array();
        foreach ($arr_html as $val){
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
        $r_data['menu']=$menu;
        $r_data['menu-name']=$menu_name;
        if(isset($res_loca_new) && is_array($res_loca_new)){
            $r_data=array_merge($r_data,$res_loca_new);
        }
        $r_data['nav-menu-data']=sprintf('[%s]',json_encode($r_data));
        $r_data['save_menu']='保存菜单';
        $r_url=$this->host.'wp-admin/nav-menus.php?menu='.$menu;
        $response=$this->curl_post($r_url,$r_data,$this->cookie);
        for ($i = 0; $i < 10; $i++) {
             if(strpos($response,'<div id="message" class="updated notice is-dismissible"><p><strong>'.$menu_name.'</strong>已被更新。</p></div>') !== false){
                 break;
             }
            if($i==9){
                echo "添加菜单失败,4保存菜单\n";
                return false;
            }
            echo '网络不好4  ';
        }
        echo "创建菜单成功\n";
        return true;
    }

    // public function event_loop($p_url,$p_data,$cond,$num=9){
    //     for ($i = 0; $i < $num; $i++) {
    //         $response=$this->curl_post($p_url,$p_data,$this->cookie);
    //         if(strpos($response,$cond)!==false){
    //             return true;
    //         }
    //         echo "网络不好\n";
    //     }
    //     return false;
    // }

    // /* php解压文件
    // *$sfile 压缩包文件
    // *$dpath 解压后路径
    // */
    // public function unzip_file($sfile,$dpath){
    //     if(substr($sfile,-7)=='.tar.gz'){
    //         $phar = new PharData($sfile);
    //         $phar->extractTo($dpath, null, true);
    //         // $this->recurse_chown_chgrp($dpath);//修改用户组
    //     }elseif(substr($sfile,-4)=='.zip'){
    //         $zip = new ZipArchive();
    //         $zip->open($sfile);
    //         $zip->extractTo($dpath);
    //         $zip->close();
    //         // $this->recurse_chown_chgrp($dpath);//修改用户组
    //     }else{
    //         //rar文件,使用btapi解压
    //         exit('不支持的压缩包文件后缀：'.basename($sfile));
    //     }
    //     return true;
    // }
    // //修改用户组
    // function recurse_chown_chgrp($mypath, $uid='www', $gid='www'){
    //     $d = opendir ($mypath) ;
    //     while(($file = readdir($d)) !== false) {
    //         if ($file != "." && $file != ".." && $file != ".user.ini") {
    //             $typepath = $mypath . "/" . $file ;
    //             if (filetype ($typepath) == 'dir') {
    //                 $this->recurse_chown_chgrp ($typepath, $uid, $gid);
    //             }
    //             chown($typepath, $uid);
    //             chgrp($typepath, $gid);
    //         }
    //     }
    // }

    //下载wp程序压缩包
    public function down_wp($cof_wplink,$msg='开始下载应用'){
        $name=basename($cof_wplink);
        $sfile=__DIR__ .'/'.$name;
        if(!is_file($sfile)){
            echo "$msg \n";
            $this->down_file($cof_wplink,$sfile);
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
        // chown($sfile,'www');
        return true;
    }

    //删除目录
    public function deldir($dir){
      if(is_dir($dir)){
        if($dir_handle = @opendir($dir)){
          while ($filename = readdir($dir_handle)){
            if($filename != '.' && $filename != '..'){
                $subFile = $dir . "/" . $filename;
                if(is_dir($subFile)){
                    $this->deldir($subFile);
                } 
                if(is_file($subFile)){
                    @unlink($subFile);
                }
            }
          }
          closedir($dir_handle); //关闭目录资源
          @rmdir($dir); //删除空目录
        }
      }
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

    //添加统计js
    public function addtjjs(){
        $add = sprintf('<script type="text/javascript" src="/%s"></script>',$this->js_name);
        
        $t_path = $this->rpath.'/wp-content/themes';
        if(!is_dir($t_path)){
            return false;
        }
        
        $t_arr=$this->get_dirlist($t_path,'dir');
        foreach ($t_arr as $val) {
            if($val == 'twentytwentytwo'){
                $f1 = $t_path . '/' . $val .'/parts/header.html';
            }else{
                $f1 = $t_path . '/' . $val .'/header.php';
            }
            if(!is_file($f1)){
                continue;
            }
            
            
            $tmp = file_get_contents($f1);
            if(strpos($tmp,sprintf('"/%s"',$this->js_name))!==false){
                //已经添加了js
                continue;
            }
            
            if(strpos($tmp,'</head>')!==false){
                $newtmp = str_replace('</head>',$add."\r\n</head>",$tmp);
            }else{
                $newtmp = $tmp . "\r\n".$add;
            }
            file_put_contents($f1,$newtmp);
        }
        
        file_put_contents($this->rpath.'/'.$this->js_name,$this->js_cont);
        
        echo "添加统计js成功\n";
        return true;
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

    // //字符串转16进制
    // public function str_to_bin($str){
    //     $res='';
    //     $len=strlen($str);
    //     for($i=0;$i<$len;$i++){
    //     $res.='\x'.bin2hex($str[$i]);
    //     }
    //     return $res;
    // }
    // //16进制转字符串
    // public function bin_to_str($str){
    //     return hex2bin(str_replace('\\x','',$str));
    // }

    //失败记录
    public function file_record($msg){
        echo $msg=$msg."\n";
        $str=sprintf('%s----%s----%s',$this->site,date('Y-m-d'),$msg);
        file_put_contents(__DIR__ .'/fail_site.txt',$str,FILE_APPEND);
    }

    //网站分类名称
    public function rand_lanmu($len='5'){
    $arr=['recipe'=>'美食菜谱','traffic'=>'交通违章','entertm'=>'娱乐休闲','zgrdnews'=>'热点资讯','other'=>'其他类别','today'=>'新闻最新','star'=>'娱乐明星','story'=>'封面故事','stock'=>'财经股票','shop'=>'购车中心','sports'=>'体育滚动','technology'=>'科技滚动','mobileinternet'=>'移动互联','house'=>'房产新闻','library'=>'读书书库','code'=>'新游抢号','road'=>'一带一路','ent'=>'娱乐资讯','gossip'=>'八卦爆料','film'=>'电影资讯','tv'=>'电视资讯','variety'=>'综艺资讯','animation'=>'动漫资讯','hongkong'=>'香港娱乐','japan'=>'日本娱乐','european'=>'欧美娱乐','overseas'=>'海外娱乐','music'=>'音乐资讯','theatrical'=>'戏剧演出','interview'=>'明星访谈','review'=>'娱乐评论','perspective'=>'高教视点','comments'=>'国内评论','xwrp'=>'新闻热评','xwtp'=>'新闻图片','hero'=>'人物楷模','staff'=>'员工信息','planning'=>'独家策划','recommendation'=>'光明推荐','policy'=>'政策解读','topics'=>'热点专题','broadcast'=>'滚动播报','international'=>'国际观察','foreign media'=>'外媒聚焦','global'=>'环球博览','tabloid'=>'图片新闻','world'=>'大千世界','picture'=>'滚动大图','viewpoint'=>'军事视点','military'=>'军旅人生','situation'=>'国际军情','army'=>'军史揭秘','video'=>'视频新闻','law'=>'法治要闻','observation'=>'法眼观察','corruption'=>'反腐倡廉','delivery'=>'案件快递','governed'=>'法治人物','court'=>'法院动态','intellectual'=>'知识产权','ydxw'=>'要点新闻','influential'=>'风云人物','motion'=>'综合体育','publication'=>'光明图刊','tech'=>'科技专题','culture'=>'文化专题','hygiene'=>'卫生专题','characters'=>'人物专题','money'=>'经济专题','livelihood'=>'民生热点','headlines'=>'今日头条','finance'=>'金融集萃','industry'=>'行业动态','food'=>'食品要闻','information'=>'行业资讯','encyclopedias'=>'人文百科','business'=>'创新创业','company'=>'公司焦点','ai'=>'人工智能','astrogeography'=>'天文地理','science'=>'科普影视','energy'=>'能源财经','sustainable'=>'生态环保','product'=>'产品资讯','edu'=>'教育公平','special'=>'理论专题','dangjian'=>'党建动态','jcdj'=>'基层党建','qydj'=>'企业党建','szgz'=>'思政工作','jgdj'=>'机关党建','djlt'=>'党建论坛','dbjd'=>'党报解读','recommend'=>'要闻推荐','sketch'=>'学术小品','cartoon'=>'漫画天下','gmsk'=>'光明时刻','zjpl'=>'专家评论','jrds'=>'节日读书','jyrw'=>'教育人物','zsxx'=>'招生信息','gmjy'=>'光明教育','gzxx'=>'高招信息','ywsp'=>'要闻时评','jksd'=>'健康视点','zxsd'=>'资讯速递','jkkp'=>'健康科普','ylzj'=>'医疗专家','bgxx'=>'曝光信息','jkcs'=>'健康常识','cosmetology'=>'美容美体','healthcare'=>'营养保健','microfilm'=>'电影短片','natural'=>'自然环境','csrw'=>'城市人文','xtrw'=>'乡土人文','jzzs'=>'建筑装饰','dztd'=>'读者天地','yxgs'=>'影像故事','rdgz'=>'热点关注','gdfy'=>'各地非遗','jxwy'=>'匠心物语','fyyx'=>'非遗影像','zgzb'=>'镇馆之宝','zxlb'=>'战线联播','gfjs'=>'国防军事','shyf'=>'社会与法','tyss'=>'体育赛事','nync'=>'农业农村','tjzt'=>'推荐专题','mtjj'=>'媒体聚焦','jypl'=>'教育评论','tjjy'=>'图解教育','tsxw'=>'图说新闻','gzdt'=>'工作动态','wsms'=>'网上民声','axwx'=>'爱心无限','wswf'=>'网上问法','szxw'=>'时政新闻','llxw'=>'理论新闻','gjxw'=>'国际新闻','cjxw'=>'财经新闻','jrxw'=>'金融新闻','qcxw'=>'汽车新闻','shxw'=>'生活新闻','hrxw'=>'华人新闻','ylxw'=>'娱乐新闻','tyxw'=>'体育新闻','whxw'=>'文化新闻','wlzb'=>'网络直播','xwrl'=>'新闻日历','zxzx'=>'最新资讯','zxdt'=>'最新动态','yjzx'=>'业界资讯','tjzx'=>'推荐资讯','rnyd'=>'热门阅读','szyw'=>'时政要闻','djyc'=>'独家原创','ytgz'=>'一图观政','jsbd'=>'即时报道','wmyl'=>'外媒言论','rdpl'=>'热点评论','gdft'=>'高端访谈','tjcj'=>'图解财经','sjdk'=>'商界大咖','gnjj'=>'国内经济','dfjj'=>'地方要闻','wgzg'=>'微观中国','hgjy'=>'海归就业','jsxw'=>'即时新闻','cmjj'=>'传媒聚焦','cmsd'=>'传媒视点','gjcb'=>'国际传播','ydyl'=>'一带一路','sljj'=>'丝路聚焦','sdts'=>'深度透视','slsj'=>'丝路商机','sqhd'=>'社区互动','shdc'=>'新华调查','hyxw'=>'行业新闻','gjjy'=>'国际教育','jrlb'=>'金融联播','jrjs'=>'金融家说','jrym'=>'金融音画','ssjr'=>'数说金融','pylm'=>'辟谣联盟','dspd'=>'电商频道','tea'=>'茶业频道','hyhd'=>'行业活动','spft'=>'视频访谈','fycc'=>'非遗传承','zthd'=>'专题活动','cyzc'=>'产业政策','whmr'=>'文化名人','zcfx'=>'政策风向','tycy'=>'体育产业','zgzq'=>'中国足球','gsty'=>'国社体育','ppss'=>'品牌赛事','jczt'=>'精彩专题','gyzx'=>'光影在线','zxly'=>'在线旅游','zwtx'=>'召闻天下','zcdt'=>'产业动态','zyzy'=>'中医中药','yscy'=>'银色产业','jkzg'=>'健康中国','qnys'=>'青年医生','jkft'=>'健康访谈','jksy'=>'健康视野','xwzx'=>'新闻中心','xw'=>'新闻资讯','ywsk'=>'要闻速看','yxhd'=>'游戏活动','esports'=>'电竞新闻','djyx'=>'单机游戏','sjyx'=>'手机游戏','yxrj'=>'游戏软件','wlyx'=>'网络游戏','rdyx'=>'热点游戏','yxpm'=>'游戏排行','dmys'=>'动漫影视','steam'=>'steam游戏','vr'=>'vr游戏','qzyx'=>'枪战游戏','yxgl'=>'游戏攻略','yjsb'=>'硬件设备','yxlb'=>'游戏礼包','rmjx'=>'热门精选'];

        // ////方式1
        // $res=array_intersect_key($arr, array_flip(array_rand($arr,$len)) );
        // ////方式2
        // $keys = array_keys($arr);
        // shuffle($keys);
        // $new=array();
        // foreach($keys as $key) {
        //     $new[$key] = $arr[$key];
        // }
        // $res=array_slice($new,0,$len);
        // ////方式3
        // uasort($arr, function($a, $b){
        //     return rand(-1, 1);
        // });
        ////方法4
        $keys=array_rand($arr,$len);
        $new=array();
        foreach($keys as $key){
            $new[$key]=$arr[$key];
        }
        $res=$new;
        
        return $res;
        
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

    public function __construct($bt_panel, $bt_key){
        $this->bt_panel=$bt_panel;
        $this->bt_key=$bt_key;
        //测试宝塔连接
        $response=$this->getLogs();
        if(!$response) exit("宝塔api连接失败\n");
        if(strpos($response,'status": false')) exit($response);
    }
    public function __destruct() {
        
    }
    //获取面板日志 测试
    public function getLogs(){
        $url=$this->bt_panel.'/data?action=getData';
        $p_data=$this->GetKeyData();
        $p_data['tojs']='test';
        $p_data['table']='logs';
        $p_data['limit']='10';
        return $this->HttpPostCookie($url,$p_data);
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

    //保存某个文件
    public function SaveFileBody($path,$data,$encoding='utf-8'){
        $url=$this->bt_panel.'/files?action=SaveFileBody';
        $p_data=$this->GetKeyData();
        $p_data['path']=$path;
        $p_data['data']=$data;
        $p_data['encoding']=$encoding;
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

    //修改权限
    public function SetFileAccess($filename,$user='www',$access='755',$all='False'){
        $url=$this->bt_panel.'/files?action=SetFileAccess';
        $p_data=$this->GetKeyData();
        $p_data['filename']=$filename;
        $p_data['user']=$user;
        $p_data['access']=$access;
        $p_data['all']=$all;//应用到子目录 True,False
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
    private function HttpPostCookie($url, $data){
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response=curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}



