<?php
/**
 * 
2023 速简版

要求：
1 关闭宝塔的数据库回收站
2 此文件放到服务器上 打开xshell或者宝塔终端 使用php命令运行本文件

如果提示(数据库创建失败)：
  数据库回收站，点击数据库，点击从服务器获取，删除此域名对应的数据库

如果提示(数据库连接失败)，可能原因：
  关闭nginx防火墙软件
  此域名有使用cdn
  此域名未解析


推荐使用固态硬盘的服务器
推荐屏蔽国外搜索引擎蜘蛛（网站根目录设置robots.txt）



运行本文件命令：
php /www/1111/1wp批量建站.php


*/
//-----------------------------------------------
//--------------------设置开始--------------------
//宝塔面板地址(带端口号)* 
$cof_panel='http://111.111.111.111:33754/';
//宝塔API接口密钥*
$cof_key='K3Mt83at5AOjwwAIdr13xIaYkwTaaaaa';
//网站使用的php版本(推荐php7.0以上版本)
$cof_php_v='7.4';


//wp网站后台账号*
$cof_admin_name='admin';
//wp网站后台密码*
$cof_admin_password='admin123';


//wp网站是否下载应用随机模板 1启用0禁用
$cof_is_muban='1';
//wp网站是否添加随机分类 1启用0禁用
$cof_is_fenlei='0';
//wp网站是否启用和设置菜单(设置菜单需要先启用添加分类，有的主题不支持菜单) 1启用0禁用
$cof_is_caidan='0';
//wp网站是否启用seo插件 1启用0禁用，建议开启！
$cof_is_seoplugin='1';


//统计js名字,创建到站点根目录和网站模板中,(留空不添加js)--值可为空
$cof_js_name='baidu.js';
//统计js内容(没有<script>标签)--值可为空
$cof_js_cont=<<<'EOLJSCONT'


(function(){
    var bp = document.createElement("script");
    var curProtocol = window.location.protocol.split(":")[0];
    if (curProtocol === "https") {
        bp.src = "https://zz.bdstatic.com/linksubmit/push.js";
    }
    else {
        bp.src = "http://push.zhanzhang.baidu.com/push.js";
    }
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(bp, s);
})();

EOLJSCONT;
//设置建站域名文件路径* (内容格式:域名****网站名称****网站首页标题****网站首页关键词****网站首页描述)
$cof_site_file='site.txt';
//------------------设置结束----------------------
//------------------------------------------------
//------------------------------------------------
//------------------------------------------------
//下载模板主题类型popular(有5000个主题) 或 new(有10000个主题)
$cof_browse = 'popular';
//wp管理员邮箱,留空随机生成--值可为空
$cof_email='';
//wp压缩包在服务器上的完整路径，或者下载链接
$cof_wplink='https://raw.githubusercontent.com/mibao2022/qazxsw2022/main/wp/wordpress_laster.tar.gz';
//wp-seo插件在服务器上的完整路径，或者下载链接
$cof_pluginlink='https://raw.githubusercontent.com/mibao2022/qazxsw2022/main/wp/all-in-one-seo-pack.tar.gz';
//wp网站伪静态
$cof_rewrite='if (!-e $request_filename) {
    rewrite  ^(.*)$  /index.php/$1  last;
    break;
 }';
//------------------------------------------------
//------------------------------------------------
if(!is_dir('/www/server')){
    exit("文件放到有宝塔的服务器上运行\r\n");
}
$cof_panel=trim(rtrim($cof_panel,'/'));
$cof_key=trim($cof_key);
if(!preg_match('/[0-9a-zA-Z]{32}/',$cof_key)){
    exit("设置正确的宝塔API接口密钥\n");
}
$cof_php_v=intval(str_replace('.','',$cof_php_v));
if(!$cof_php_v || $cof_php_v<70){
    exit("设置php版本大于7.0\r\n");
}
$cof_admin_name=trim($cof_admin_name);
if(!$cof_admin_name){
    exit("设置wp网站后台账号\r\n");
}
$cof_admin_password=trim($cof_admin_password);
if(!$cof_admin_password || strlen($cof_admin_password)<8){
    exit("设置wp网站后台密码，密码长度大于8位数\r\n");
}
$cof_js_name=trim($cof_js_name);
$cof_js_cont=trim($cof_js_cont);
$cof_site_file=trim($cof_site_file);
if($cof_site_file[0] !='/'){
    $cof_site_file= __DIR__ .'/'.$cof_site_file;
}
if(!is_file($cof_site_file)){
    exit("设置正确的建站域名文件路径\r\n");
}
$site_file_str=file_get_contents($cof_site_file);
$site_file_tmp=explode("\n", trim($site_file_str));
$site_file_arr=array_values(array_filter(array_map('trim',$site_file_tmp)));
unset($site_file_str,$site_file_tmp);
if(empty($site_file_arr)){
    exit("设置建站域名文件内容\r\n");
}
$cof_browse=trim($cof_browse);
$cof_email=trim($cof_email);
$cof_wplink=trim($cof_wplink);
$cof_pluginlink=trim($cof_pluginlink);
$cof_rewrite=trim($cof_rewrite);


set_time_limit(0);
ini_set('memory_limit',-1);

$bt=new BtApi($cof_panel,$cof_key);//先实例
$wp=new WordPress();

$wp->admin_name=$cof_admin_name;
$wp->admin_password=$cof_admin_password;
$wp->browse=($cof_browse=='popular')?'popular':'new';
$wp->email=$cof_email?$cof_email:'wp'.mt_rand(10000000,99999999).'@gmail.com';

$path_wp=$wp->check_file($cof_wplink);
$path_plugin=$wp->check_file($cof_pluginlink);
$bt->SetFileAccess($path_wp);
$bt->SetFileAccess($path_plugin);
$path_wp_type=(substr($path_wp,-7)=='.tar.gz')?'tar':'zip';
$path_plugin_type=(substr($path_plugin,-7)=='.tar.gz')?'tar':'zip';



foreach ($site_file_arr as $key=>$val){
    $wp->set_tdk($val);
    $domain=$wp->domain;
    $domain_path=$wp->domain_path='/www/wwwroot/'.$wp->domain;
    $db_name=substr(str_replace(['.','-'], '_', $domain),0,16);
    $db_pwd=$wp->rand_str(16);
    
    
    //创建站点
    echo sprintf("\r\n------搭建第%s个站点: %s ------\r\n",$key +1,$domain);
    $response=$bt->AddSite($domain,$domain_path,$cof_php_v,'MySQL',$db_name,$db_pwd);
    if(empty($response) || strpos($response,'"siteStatus": true') === false){
        $tmp1=json_decode($response,true);
        if(isset($tmp1['msg'])){
            echo $wp->err .= $domain.">>>>站点创建失败,".$tmp1['msg']."\r\n";
        }else{
            echo $wp->err .= $domain.">>>>站点创建失败\r\n";
        }
        
        $wp->save_text();
        continue;
    }
    $web_data=json_decode($response,true);
	if( $web_data['databaseStatus'] === false ){
		echo $wp->err .= $domain.">>>>数据库创建失败！\r\n";
		$bt->WebDeleteSite($web_data['siteId'],$domain,true);
		$wp->save_text();
		continue;
	}
    echo "站点创建成功\r\n";
    $bt->UnZip($path_wp,$domain_path,$path_wp_type);
    @unlink($domain_path.'/index.html');
    //设置网站伪静态
    $response=$bt->SaveFileBody(sprintf('/www/server/panel/vhost/rewrite/%s.conf',$domain), $cof_rewrite);
    if(strpos($response,'status": true')!==false){
        echo "伪静态设置成功\r\n";
    }else{
        echo $wp->err .= $domain.">>>>伪静态设置失败\r\n";
    }
    //解压seo插件 (改btapi解压)
    $bt->UnZip($path_plugin,$domain_path.'/wp-content/plugins',$path_plugin_type);
    //安装wp
    if(!$wp->install_wp($db_name,$db_pwd)){
        $bt->WebDeleteSite($web_data['siteId'],$domain);
        $wp->save_text();
        continue;
    }


    //登录wp
    if(!$wp->login_wp()){
        $bt->WebDeleteSite($web_data['siteId'],$domain);
        $wp->save_text();
        continue;
    }

    //网站设置
    $wp->setting();
    
    
    //加跳转/统计js
    if($cof_js_name){$wp->addtjjs($cof_js_name,$cof_js_cont);}

    
    $wp->save_text();
    
}




//wp类
class WordPress{

    public $admin_name;//wp网站后台账号
    public $admin_password;//wp网站后台密码
    
    public $browse;//网站主题类型
    public $email;//网站邮箱

    public $domain;//网站域名
    public $domain_path;//网站根目录路径，结尾不带/
    public $home_page;//网站首页网址，结尾不带/

    public $blog_name;//网站名称
    public $blog_title;//网站首页标题
    public $blog_keyw;//网站首页关键词
    public $blog_desc;//网站首页描述

    public $err;
    private $cookie;//网站cookie
    
    
    public function __construct(){
        
    }
    
    public function __destruct(){
        
    }
    
    public function save_text(){
        if($this->err){
            file_put_contents(__DIR__ .'/fail_site.txt',$this->err,FILE_APPEND);
            $this->err='';
        }
    }
    

    //网站设置
    public function setting(){
        global $cof_is_muban,$cof_is_fenlei,$cof_is_caidan,$cof_is_seoplugin;
        
        if($cof_is_muban == '1'){
            $this->theme_down();//下载随机主题
        }
        
        if($cof_is_fenlei == '1'){
            $this->category_add();//添加分类
        }
        
        if($cof_is_caidan == '1'){
            $this->menu_add();//创建和设置菜单
        }
        
        if($cof_is_seoplugin == '1'){
            $this->plugin_seo_enb();//启用seo插件
            $this->plugin_seo_edit();//设置插件信息
        }
        
        return true;
    }
    
    //安装
    public function install_wp($db_name,$db_pwd){
        echo "开始安装wordpress\r\n";
        $p_url=$this->home_page.'/wp-admin/setup-config.php?step=0';
        $p_data=['language'=>'zh_CN'];
        $this->curl_post($p_url,$p_data);
        
        $p_url=$this->home_page.'/wp-admin/setup-config.php?step=2';
        $p_data=[
            'dbname'    =>  $db_name,
            'uname'     =>  $db_name,
            'pwd'       =>  $db_pwd,
            'dbhost'    =>  'localhost',
            'prefix'    =>  'wp_',
            'language'  =>  'zh_CN',
            'submit'    =>  '提交'
        ];
        $response=$this->curl_post($p_url,$p_data);
        if(strpos($response,'数据库连接成功')!==false || strpos($response,'Successful database connection')!==false){
            echo "数据库连接成功\r\n";
        }else{
            echo $this->err .= $this->domain.">>>>数据库连接失败！\r\n";
            return false;
        }
        
        $p_url=$this->home_page.'/wp-admin/install.php?step=2';
        $p_data=[
            'weblog_title'      =>  $this->blog_name,
            'user_name'         =>  $this->admin_name,
            'admin_password'    =>  $this->admin_password,
            'admin_password2'   =>  $this->admin_password,
            'pw_weak'           =>  'on',
            'admin_email'       =>  $this->email,
            'Submit'            =>  '安装WordPress',
            'language'          =>  'zh_CN'
        ];
        $response=$this->curl_post($p_url,$p_data,array(),40);
        if(strpos($response,'成功！')!==false){
            echo "wordpress安装成功\r\n";
        }else{
            echo $this->err .= $this->domain.">>>>wordpress安装失败！\r\n";
            return false;
        }
        return true;
    }
    
    //登录
    public function login_wp(){
        $this->cookie='';
    	$p_url=$this->home_page.'/wp-login.php';
    	$p_data = [
    		'log' => $this->admin_name,
    		'pwd' => $this->admin_password,
    		'wp-submit' => '登录',
    		'redirect_to' => $this->home_page.'/wp-admin/',
    		'testcookie' => 1,
    	];
    	$ch = curl_init(); 
    	curl_setopt($ch, CURLOPT_URL, $p_url);
    	curl_setopt($ch, CURLOPT_HEADER, true);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($p_data,'','&'));
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    	$response = curl_exec($ch);
    	curl_close($ch);

    	list($header, $body) = explode("\r\n\r\n", $response);
    	preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);
    	if(isset($matches[1][2]) && $matches[1][2]){
            $re=str_replace(' path=/wp-admin; HttpOnly','',$matches[1][2]);
            $this->cookie = 'Cookie:'.$re;
    	}else{
    		$this->cookie = '';
    		echo $this->err.=$this->domain.">>>>登录失败1，获取cookie！\r\n";
    		return false;
    	}
        
        $p_url2 = $this->home_page.'/wp-admin/index.php';
    	$response = $this->curl_get($p_url2);
        if(strpos($response,'<title>仪表盘')!==false){
            echo "登录成功\r\n";
            return true;
        }else{
            echo $this->err.=$this->domain.">>>>登录失败2！\r\n";
            return false;
        }
        
    }
    

    //下载一个随机主题 new最新主题,popular热门主题
    public function theme_down(){
        if($this->browse=='popular'){
            $theme_num=5000;//主题数量
        }else{
            $theme_num=10000;//主题数量
        }
        $per_page=10;//每页展示主题数量
        $all_page=$theme_num/$per_page - 2;//总页数
        if($all_page<1){$all_page=1;}
        $page=mt_rand(1,$all_page);//随机页
        
        $p_url=$this->home_page.'/wp-admin/admin-ajax.php';
        $p_data=[
            'request[per_page]'    =>  $per_page,
            'request[browse]'      =>  $this->browse,
            'request[page]'        =>  $page,
            'action'               =>  'query-themes',
        ];
        $response=$this->curl_post($p_url,$p_data);
        $themes_arr=json_decode($response,true);
        if(isset($themes_arr['success']) && $themes_arr['success']==true && $themes_arr['data']['themes']){
            // echo "新主题链接获取成功\r\n";
        }else{
            echo "新主题链接获取失败\r\n";
            return false;
        }
        
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
        $response=$this->curl_get($rand_themes['install_url'],array(),50);
        if(strpos($response,'</strong>成功。')!==false || strpos($response,'<p>目标目录已存在')!==false){
            echo "新主题下载成功:".$rand_themes['slug']."\r\n";
        }else{
            echo "新主题下载失败\r\n";
            return false;
        }
        if(isset($rand_themes['parent'])){
            echo "下载父主题:".$rand_themes['parent']['slug']."\r\n";
        }
        
        //启用主题
        // $pp = $this->domain_path.'/wp-content/themes/'.strtolower($rand_themes['slug']);
        // if(isset($rand_themes['parent']['slug'])){
        //     $pp2 = $this->domain_path.'/wp-content/themes/'.strtolower($rand_themes['parent']['slug']);
        // }
        $response=$this->curl_get($rand_themes['activate_url']);
        if(strpos($response,'<p>新主题已启用')!==false){
            echo "新主题启用成功\r\n";
        }else{
            echo "新主题启用失败\r\n";
            return false;
        }
        return true;
    }
    
    //添加分类
    public function category_add(){
        $p_url=$this->home_page.'/wp-admin/edit-tags.php?taxonomy=category';
        $reg='/<input type="hidden" id="_wpnonce_add-tag" name="_wpnonce_add-tag" value="(.*?)"/';
        $response=$this->curl_get($p_url);
        preg_match('/name="_wpnonce_add-tag" value="(.*?)"/',$response,$matches);
        if(isset($matches[1]) && $matches[1]){
            $wpnonce=$matches[1];
        }else{
            echo $this->err.="添加分类失败\r\n";
            return false;
        }
        
        $p_url = $this->home_page.'/wp-admin/admin-ajax.php';
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
        $arr=$this->rand_lanmu();
        foreach($arr as $key=>$val){
            $p_data['tag-name']=$val;//分类名称
            $p_data['slug']= is_numeric($key) ? 'category'.$key : $key;//分类别名
            $this->curl_post($p_url,$p_data);
        }
        echo "添加分类成功\r\n";
        return true;
    }
    
    //网站分类名称 默认添加5个分类
    public function rand_lanmu($len='5'){
    $arr=['recipe'=>'美食菜谱','traffic'=>'交通违章','entertm'=>'娱乐休闲','zgrdnews'=>'热点资讯','other'=>'其他类别','today'=>'新闻最新','star'=>'娱乐明星','story'=>'封面故事','stock'=>'财经股票','shop'=>'购车中心','sports'=>'体育滚动','technology'=>'科技滚动','mobileinternet'=>'移动互联','house'=>'房产新闻','library'=>'读书书库','code'=>'新游抢号','road'=>'一带一路','ent'=>'娱乐资讯','gossip'=>'八卦爆料','film'=>'电影资讯','tv'=>'电视资讯','variety'=>'综艺资讯','animation'=>'动漫资讯','hongkong'=>'香港娱乐','japan'=>'日本娱乐','european'=>'欧美娱乐','overseas'=>'海外娱乐','music'=>'音乐资讯','theatrical'=>'戏剧演出','interview'=>'明星访谈','review'=>'娱乐评论','perspective'=>'高教视点','comments'=>'国内评论','xwrp'=>'新闻热评','xwtp'=>'新闻图片','hero'=>'人物楷模','staff'=>'员工信息','planning'=>'独家策划','recommendation'=>'光明推荐','policy'=>'政策解读','topics'=>'热点专题','broadcast'=>'滚动播报','international'=>'国际观察','foreign media'=>'外媒聚焦','global'=>'环球博览','tabloid'=>'图片新闻','world'=>'大千世界','picture'=>'滚动大图','viewpoint'=>'军事视点','military'=>'军旅人生','situation'=>'国际军情','army'=>'军史揭秘','video'=>'视频新闻','law'=>'法治要闻','observation'=>'法眼观察','corruption'=>'反腐倡廉','delivery'=>'案件快递','governed'=>'法治人物','court'=>'法院动态','intellectual'=>'知识产权','ydxw'=>'要点新闻','influential'=>'风云人物','motion'=>'综合体育','publication'=>'光明图刊','tech'=>'科技专题','culture'=>'文化专题','hygiene'=>'卫生专题','characters'=>'人物专题','money'=>'经济专题','livelihood'=>'民生热点','headlines'=>'今日头条','finance'=>'金融集萃','industry'=>'行业动态','food'=>'食品要闻','information'=>'行业资讯','encyclopedias'=>'人文百科','business'=>'创新创业','company'=>'公司焦点','ai'=>'人工智能','astrogeography'=>'天文地理','science'=>'科普影视','energy'=>'能源财经','sustainable'=>'生态环保','product'=>'产品资讯','edu'=>'教育公平','special'=>'理论专题','dangjian'=>'党建动态','jcdj'=>'基层党建','qydj'=>'企业党建','szgz'=>'思政工作','jgdj'=>'机关党建','djlt'=>'党建论坛','dbjd'=>'党报解读','recommend'=>'要闻推荐','sketch'=>'学术小品','cartoon'=>'漫画天下','gmsk'=>'光明时刻','zjpl'=>'专家评论','jrds'=>'节日读书','jyrw'=>'教育人物','zsxx'=>'招生信息','gmjy'=>'光明教育','gzxx'=>'高招信息','ywsp'=>'要闻时评','jksd'=>'健康视点','zxsd'=>'资讯速递','jkkp'=>'健康科普','ylzj'=>'医疗专家','bgxx'=>'曝光信息','jkcs'=>'健康常识','cosmetology'=>'美容美体','healthcare'=>'营养保健','microfilm'=>'电影短片','natural'=>'自然环境','csrw'=>'城市人文','xtrw'=>'乡土人文','jzzs'=>'建筑装饰','dztd'=>'读者天地','yxgs'=>'影像故事','rdgz'=>'热点关注','gdfy'=>'各地非遗','jxwy'=>'匠心物语','fyyx'=>'非遗影像','zgzb'=>'镇馆之宝','zxlb'=>'战线联播','gfjs'=>'国防军事','shyf'=>'社会与法','tyss'=>'体育赛事','nync'=>'农业农村','tjzt'=>'推荐专题','mtjj'=>'媒体聚焦','jypl'=>'教育评论','tjjy'=>'图解教育','tsxw'=>'图说新闻','gzdt'=>'工作动态','wsms'=>'网上民声','axwx'=>'爱心无限','wswf'=>'网上问法','szxw'=>'时政新闻','llxw'=>'理论新闻','gjxw'=>'国际新闻','cjxw'=>'财经新闻','jrxw'=>'金融新闻','qcxw'=>'汽车新闻','shxw'=>'生活新闻','hrxw'=>'华人新闻','ylxw'=>'娱乐新闻','tyxw'=>'体育新闻','whxw'=>'文化新闻','wlzb'=>'网络直播','xwrl'=>'新闻日历','zxzx'=>'最新资讯','zxdt'=>'最新动态','yjzx'=>'业界资讯','tjzx'=>'推荐资讯','rnyd'=>'热门阅读','szyw'=>'时政要闻','djyc'=>'独家原创','ytgz'=>'一图观政','jsbd'=>'即时报道','wmyl'=>'外媒言论','rdpl'=>'热点评论','gdft'=>'高端访谈','tjcj'=>'图解财经','sjdk'=>'商界大咖','gnjj'=>'国内经济','dfjj'=>'地方要闻','wgzg'=>'微观中国','hgjy'=>'海归就业','jsxw'=>'即时新闻','cmjj'=>'传媒聚焦','cmsd'=>'传媒视点','gjcb'=>'国际传播','ydyl'=>'一带一路','sljj'=>'丝路聚焦','sdts'=>'深度透视','slsj'=>'丝路商机','sqhd'=>'社区互动','shdc'=>'新华调查','hyxw'=>'行业新闻','gjjy'=>'国际教育','jrlb'=>'金融联播','jrjs'=>'金融家说','jrym'=>'金融音画','ssjr'=>'数说金融','pylm'=>'辟谣联盟','dspd'=>'电商频道','tea'=>'茶业频道','hyhd'=>'行业活动','spft'=>'视频访谈','fycc'=>'非遗传承','zthd'=>'专题活动','cyzc'=>'产业政策','whmr'=>'文化名人','zcfx'=>'政策风向','tycy'=>'体育产业','zgzq'=>'中国足球','gsty'=>'国社体育','ppss'=>'品牌赛事','jczt'=>'精彩专题','gyzx'=>'光影在线','zxly'=>'在线旅游','zwtx'=>'召闻天下','zcdt'=>'产业动态','zyzy'=>'中医中药','yscy'=>'银色产业','jkzg'=>'健康中国','qnys'=>'青年医生','jkft'=>'健康访谈','jksy'=>'健康视野','xwzx'=>'新闻中心','xw'=>'新闻资讯','ywsk'=>'要闻速看','yxhd'=>'游戏活动','esports'=>'电竞新闻','djyx'=>'单机游戏','sjyx'=>'手机游戏','yxrj'=>'游戏软件','wlyx'=>'网络游戏','rdyx'=>'热点游戏','yxpm'=>'游戏排行','dmys'=>'动漫影视','steam'=>'steam游戏','vr'=>'vr游戏','qzyx'=>'枪战游戏','yxgl'=>'游戏攻略','yjsb'=>'硬件设备','yxlb'=>'游戏礼包','rmjx'=>'热门精选'];
        $arr_ran=array_rand($arr,$len);
        $res=array();
        foreach($arr_ran as $v){
            $res[$v]=$arr[$v];
        }
        return $res;
    }
    
    //创建和设置菜单
    public function menu_add(){
        global $cof_is_fenlei;
        $menu_name = 'menu123'; //菜单名称
        
        //1获取页面内容
        $p_url=$this->home_page.'/wp-admin/nav-menus.php';
        $response=$this->curl_get($p_url);
        if(strpos($response,'您的主题不支持导航菜单或小工具')!==false){
            echo "创建菜单失败1，主题不支持菜单\r\n";
            return false;
        }
        if(strpos($response,'<title>菜单')===false){
            echo "创建菜单失败1，获取内容失败\r\n";
            return false;
        }
        
        //2创建菜单
    	preg_match('/id="closedpostboxesnonce" name="closedpostboxesnonce" value="(.*?)"/',$response,$mat_closedpostboxesnonce);
    	preg_match('/id="meta-box-order-nonce" name="meta-box-order-nonce" value="(.*?)"/',$response,$mat_meta_box_order_nonce);
    	preg_match('/id="update-nav-menu-nonce" name="update-nav-menu-nonce" value="(.*?)"/',$response,$mat_update_nav_menu_nonce);
        if(!isset($mat_closedpostboxesnonce[1]) || !isset($mat_meta_box_order_nonce[1]) || !isset($mat_update_nav_menu_nonce[1])){
            echo "创建菜单失败1，获取key失败\r\n";
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
        $a_url=$this->home_page.'/wp-admin/nav-menus.php?action=edit&menu=0';
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
        $response=$this->curl_post($a_url,$a_data);
        if(strpos($response,'和另一菜单名称冲突') !== false){
            echo "创建菜单失败2，菜单名称({$menu_name})已存在\r\n";
            return true;
        }
        if(strpos($response,'class="menu-name regular-text menu-item-textbox form-required" required="required" value="'.$menu_name.'"')===false){
            echo "创建菜单失败2，创建菜单失败\r\n";
            return false;
        }
        echo "创建菜单成功\r\n";
        
        //未设置添加分类
        if(!$cof_is_fenlei){
            return false;
        }
        
        //3分类添加至菜单
        preg_match('/<input type="hidden" name="menu" id="menu" value="(.*?)"/',$response,$mat_menu);
        preg_match('/<input type="hidden" id="menu-settings-column-nonce" name="menu-settings-column-nonce" value="(.*?)"/',$response,$mat_column_nonce);
        preg_match('/<ul id="categorychecklist" data-wp-lists="list:category" class="categorychecklist form-no-clear">([\s\S]*?)<\/ul>/',$response,$mat_html);
        if(!isset($mat_menu[1]) || !isset($mat_column_nonce[1]) || !isset($mat_html[1])){
            echo "创建菜单失败3,获取分类失败\r\n";
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
        $p_url=$this->home_page.'/wp-admin/admin-ajax.php';
        $p_data['action']='add-menu-item';
        $p_data['menu']=$menu;
        $p_data['menu-settings-column-nonce']=$menu_settings_column_nonce;
        $response=$this->curl_post($p_url,$p_data,array(),30);
        if(strpos($response,'<div class="menu-item-bar">')===false){
            echo "设置菜单失败3,分类添加至菜单\r\n";
            return false;
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
        $r_url=$this->home_page.'/wp-admin/nav-menus.php?menu='.$menu;
        $response=$this->curl_post($r_url,$r_data);
         if(strpos($response,'<div id="message" class="updated notice is-dismissible"><p><strong>'.$menu_name.'</strong>已被更新。</p></div>') === false){
            echo "设置菜单失败4,保存菜单\r\n";
            return false;
         }
        echo "设置菜单成功\r\n";
        return true;
    }

    //启用seo插件
    public function plugin_seo_enb(){
        //获取seo插件启用链接
        $p_url=$this->home_page.'/wp-admin/plugins.php';
        $response=$this->curl_get($p_url);
        // $reg_en='/<strong>All In One SEO Pack<\/strong><div class="row-actions visible"><span class=\'activate\'><a href="(.*?)" id="activate-all-in-one-seo-pack"/';
        $reg_zh='/<strong>多合一SEO包<\/strong><div class="row-actions visible"><span class=\'activate\'><a href="(.*?)" id="activate-all-in-one-seo-pack"/';
        preg_match($reg_zh,$response,$mat_zh);
        if(isset($mat_zh[1]) && $mat_zh[1]){
            // echo "获取seo插件链接成功\r\n";
        }else{
            echo $this->err.=$this->domain.">>>>启用seo插件失败1，获取链接\r\n";
            return false;
        }
        
        //启用seo插件
        $p_url=$this->home_page.'/wp-admin/'.str_replace('&amp;','&',$mat_zh[1]);
        $response=$this->curl_get($p_url);
        if(strpos($response,'插件已启用')!==false || strpos($response,'您点击的链接已过期')!==false){
            echo "启用seo插件成功\r\n";
        }else{
            echo $this->err.=$this->domain.">>>>启用seo插件失败2\r\n";
            return false;
        }
        $this->curl_get($this->home_page.'/wp-admin/index.php?page=aioseop-welcome');//初始化
        return true;
    }
    
    //设置seo插件
    public function plugin_seo_edit(){
        $p_url=$this->home_page.'/wp-admin/admin.php?page=all-in-one-seo-pack/aioseop_class.php';
        $response=$this->curl_get($p_url);
        
        preg_match("/<input name='nonce-aioseop' type='hidden'  value='(.*?)'/",$response,$mat);
        if(isset($mat[1]) && $mat[1]){
            $wpnonce=$mat[1];
        }else{
            echo $this->err.=$this->domain.">>>>设置seo插件失败1，获取wpnonce\r\n";
            return false;
        }

        if(function_exists('mb_strlen')){
            $blog_title_strlen=mb_strlen($this->blog_title);
            $blog_desc_strlen=mb_strlen($this->blog_desc);
        }else{
            preg_match_all("/./us", $this->blog_title, $mat_strlen1);
            $blog_title_strlen=count(current($mat_strlen1));
            preg_match_all("/./us", $this->blog_desc, $mat_strlen2);
            $blog_desc_strlen=count(current($mat_strlen2));
        }
        $p_url=$this->home_page.'/wp-admin/admin.php?page=all-in-one-seo-pack/aioseop_class.php';
        $p_data=[
            'action'                                        =>  'aiosp_update_module',
            'module'                                        =>  'All_in_One_SEO_Pack',
            'location'                                      =>  '',
            'nonce-aioseop'                                 =>  $wpnonce,
            'page_options'                                  =>  'aiosp_home_description',
            'aiosp_can'                                     =>  'on',
            'aiosp_use_original_title'                      =>  '0',
            'aiosp_home_title'                              =>  $this->blog_title,//网站首页标题
            'aiosp_length1'                                 =>  $blog_title_strlen,
            'aiosp_home_description'                        =>  $this->blog_desc,
            'aiosp_length2'                                 =>  $blog_desc_strlen,
            'aiosp_home_keywords'                           =>  $this->blog_keyw,
            'aiosp_use_static_home_info'                    =>  '0',
            'aiosp_force_rewrites'                          =>  '1',
            'aiosp_home_page_title_format'                  =>  '%page_title%',
            'aiosp_page_title_format'                       =>  '%page_title% | %site_title%',
            'aiosp_post_title_format'                       =>  '%post_title% | %site_title%',//文章页格式化
            'aiosp_category_title_format'                   =>  '%category_title% | %site_title%',
            'aiosp_archive_title_format'                    =>  '%archive_title% | %site_title%',
            'aiosp_date_title_format'                       =>  '%date% | %site_title%',
            'aiosp_author_title_format'                     =>  '%author% | %site_title%',
            'aiosp_tag_title_format'                        =>  '%tag% | %site_title%',//标签页格式化
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
        $response = $this->curl_post($p_url,$p_data);
        if(strpos($response,"value='".$this->blog_title."'")!==false){
            echo "设置seo插件成功\r\n";
        }else{
            echo $this->err.=$this->domain.">>>>设置seo插件失败2\r\n";
            return false;
        }
        return true;
    }
    
    
    //添加统计js
    public function addtjjs($js_name,$js_cont){
        $add_str='<script type="text/javascript" src="/'.$js_name.'"></script>';
        $t_path = $this->domain_path.'/wp-content/themes';
        
        $t_arr=$this->get_dirlist($t_path,'dir');
        foreach ($t_arr as $val) {
            $f1 = $t_path . '/' . $val .'/header.php';
            if(!is_file($f1)){
                $f1 = $t_path . '/' . $val .'/parts/header.html';
                if(!is_file($f1)){
                    continue;
                }
            }
            $tmp = file_get_contents($f1);
            if(strpos($tmp,sprintf('"/%s"',$js_name))!==false){
                continue;//已经添加了js
            }
            if(strpos($tmp,'</head>')!==false){
                $newtmp = str_replace('</head>',$add_str."\r\n</head>",$tmp);
            }else{
                $newtmp = $tmp . "\r\n".$add_str;
            }
            file_put_contents($f1,$newtmp);
        }
        file_put_contents($this->domain_path.'/'.$js_name,$js_cont);
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
            }else{
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
    
    //检测wp和插件文件路径是否正确/下载文件
    public function check_file($pathname){
        if(substr($pathname,0,4)=='http'){
            //链接形式
            $fname=basename($pathname);
            $sfile = __DIR__ .'/'.$fname;
            if(!is_file($sfile)){
                echo "下载文件{$fname}\r\n";
                $this->down_file($pathname,$sfile);
            }
            return $sfile;
        }else{
            //文件路径形式
            if(!is_file($pathname)){
                exit("设置正确的压缩文件路径\r\n");
            }
        }
        return $pathname;
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

    //设置网站信息
    public function set_tdk($tdk){
        $arr=explode('****',$tdk);
        $this->domain=strtolower($arr[0]);
        $this->home_page='http://'.$arr[0];
        $this->blog_name=(isset($arr[1]) && $arr[1])?$arr[1]:'我的网站';
        $this->blog_title=(isset($arr[2]) && $arr[2])?$arr[2]:'我的网站';
        $this->blog_keyw=(isset($arr[3]) && $arr[3])?$arr[3]:'';
        $this->blog_desc=(isset($arr[4]) && $arr[4])?$arr[4]:'';
        return true;
    }
    
    //随机字符
    public function rand_str($length){
      $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
      return substr(str_shuffle($str),0,$length);
    }
    
    //get
    public function curl_get($url,$header=array(),$time=15){
        if($this->cookie){
            $header[]=$this->cookie;
        }
        $header=array_merge(array('Content-Type: text/html; charset=utf-8','Accept: text/html,application/xhtml+xml,application/xml;'), $header);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $response=curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    //post
    public function curl_post($url,$post_data=array(),$header=array(),$time=15){
        if($this->cookie){
            $header[]=$this->cookie;
        }
        $header=array_merge(array('Content-Type: application/x-www-form-urlencoded','Accept: text/html,application/xhtml+xml,application/xml;'), $header);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data,'','&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
        curl_setopt($ch, CURLOPT_TIMEOUT, $time);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
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
        if(!$response){exit("宝塔api连接失败\r\n");}
        if(strpos($response,'status": false')){
            $res=json_decode($response,true);
            echo $res['msg'],"\r\n";
            exit;
        }
    }
    public function __destruct() {
        
    }
    //获取面板日志测试
    public function getLogs(){
        $url=$this->bt_panel.'/data?action=getData';
        $p_data=$this->GetKeyData();
        $p_data['tojs']='test';
        $p_data['table']='logs';
        $p_data['limit']='10';
        return $this->HttpPostCookie($url,$p_data);
    }

    //添加网站
    public function AddSite($domain,$path,$version,$sql=false,$datauser='',$datapassword=''){
        $url=$this->bt_panel.'/site?action=AddSite';
        $p_data=$this->GetKeyData();
        $p_data['webname']=sprintf('{"domain":"%s\r","domainlist":["www.%s"],"count":1}',$domain,$domain);
        $p_data['type']='php';
        $p_data['port']=80;//端口号
        $p_data['ps']=$domain;
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
    public function WebDeleteSite($id,$webname,$path=true,$database=true){
        $url=$this->bt_panel.'/site?action=DeleteSite';
        $p_data=$this->GetKeyData();
        $p_data['id']=$id;//网站ID
        $p_data['webname']=$webname;//网站名称
        // $p_data['ftp']=$ftp;//关联FTP
        //关联网站根目录
        if($path){ $p_data['path']=$path;}
        //关联数据库
        if($database){ $p_data['database']=$database;}
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
        'request_token' => md5($now_time.''.md5($this->bt_key)),
        'request_time'  => $now_time
        );
        return $p_data;
    }

    //请求面板
    private function HttpPostCookie($url, $data){
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data,'','&'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $response=curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}







