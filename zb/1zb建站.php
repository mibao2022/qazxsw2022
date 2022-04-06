<?php
// 批量创建zblog站点
// 


//---------------------------设置开始---------------------------------

//宝塔面板地址
$cof_panel='http://103.75.12.162:34562/';

//宝塔API接口密钥  添加IP白名单到API接口
$cof_key='pdo5dAg9uSKkxEKhmdQdmX6Oe0sxvXDU';

//zb网站后台账号
$cof_username='admin1234';

//zb网站后台密码 (8位或更长的数字或字母组合)
$cof_password='Qq12345678';

//php版本(例7.2版本 写72)(推荐php7.2以上版本)
$cof_php_v=72;

//设置建站域名的文件 (内容格式:域名****网站标题****网站关键词****描述)
$cof_site_file='site.txt';

// 网站副标题
$cof_subname='首页';

//统计js名称 (创建到站点根目录)
$cof_js = 'baidu.js';

//统计js内容 (内容写在EOTABCD中间,EOTABCD后面不能有字符、空格) 
$cof_js_content = <<<'EOTABCD'


EOTABCD;
//---------------------------设置结束---------------------------------










//--------------------------------------------------
//--------------------------------------------------

$wwwroot='/www/wwwroot/';
//网站伪静态
$cof_rewrite='if (-f $request_filename/index.html){
 rewrite (.*) $1/index.html break;
}
if (-f $request_filename/index.php){
 rewrite (.*) $1/index.php;
}
if (!-f $request_filename){
 rewrite (.*) /index.php;
}';

if($cof_site_file[0] != '/'){
    $cof_site_file=__DIR__.'/'.$cof_site_file;
}
if(!is_readable($cof_site_file)){
    exit('设置建站域名的文件');
}
if(strlen($cof_key)!=32){
    exit('设置正确的宝塔API接口密钥');
}
if(empty($cof_username)){
    exit('后台账户为空');
}
if(strlen($cof_password)<8){
    exit('后台密码长度小于8位数');
}
$cof_php_v=intval($cof_php_v);
if(empty($cof_php_v) || $cof_php_v<54){
    exit('填写正确的php版本');
}
if(empty($cof_js)){
    exit('设置js文件名');
}

//读取域名
$site_str=file_get_contents($cof_site_file);
$site_arr=explode("\n", trim($site_str));
$site_arr=array_values(array_filter(array_map('trim',$site_arr)));
if(empty($site_arr)){
    exit('设置建站域名');
}
set_time_limit(0);
$bt=new BtApi($cof_panel,$cof_key);
$zblog=new ZBlog();
if(trim($cof_subname)){
    $zblog->zc_blog_subname=trim($cof_subname);
}
//// $zblog->upzb();
foreach($site_arr as $key=>$val){
    $zblog->set_tdk($val);
    $site=$zblog->site;
    $rpath=$wwwroot.$site;
    $zblog->setvar([
        'rpath'=>$rpath,
        'username'=>$cof_username,
        'password'=>$cof_password,
    ]);
    
    //创建站点
    echo sprintf("\n------搭建第%s个站点:%s------\n",$key+1,$site);
    $response=$bt->AddSite($site, $rpath, $cof_php_v, false);
    if(strpos($response,'"siteStatus": true') === false || empty($response) ){
        $zblog->file_record(sprintf("站点创建失败\n%s\n",$response));
        sleep(1);
        continue;
    }
    echo sprintf("站点创建成功\n%s\n",$response);
    $web_data=json_decode($response,true);
    $bt->DeleteFile($rpath.'/index.html');
    
    //网站根目录创建zblog安装文件
    $install_file=$rpath.'/install.php';
    $bt->CreateFile($install_file);
    $bt->SaveFileBody($install_file, $zblog->install_con());
    // $bt->SetFileAccess($install_file,'www',755);
    
    //设置网站伪静态
    $response=$bt->SaveFileBody(sprintf('/www/server/panel/vhost/rewrite/%s.conf',$site), $cof_rewrite);
    if(strpos($response,'文件已保存')===false){
        $zblog->file_record('伪静态设置失败');
    }else{
        echo "伪静态设置成功\n";
    }
    
    
    //下载
    if(!$zblog->down()){
        $bt->WebDeleteSite($web_data['siteId'],$site);
        continue;
    }
    //安装
    if(!$zblog->install()){
        $bt->WebDeleteSite($web_data['siteId'],$site);
        continue;
    }
    //登录
    if(!$zblog->login()){
        continue;
    }
    
    //地图插件1
    $plugin_path=$rpath.'/zb_users/plugin';
    $bt->down_plugin_map($plugin_path);

    //设置网站->添加网站分类->删除留言本功能->启用,修改静态化页面插件
    $zblog->setting()->add_category()->del_page()->plugin_rew();
    

    
    //地图插件2
    $bt->unzip_plugin_map($plugin_path);
    $bt->SetBatchData(array('WhitePage','Zit','default','os2020'), $rpath.'/zb_users/theme');
    $zblog->plugin_map();
    
    //seo插件管理
    
        //下载,启用主题 bool
    if($zblog->down_theme()){
        $zblog->set_theme();
    }
    
    
    //添加js代码->创建js文件
    $bt->add_js($cof_js,$rpath)->create_js($cof_js,$cof_js_content,$rpath);
    
    // $zblog->login();
    // $zblog->set_theme();
    
}

echo "\n完成\n";













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

    //获取某个文件的内容
    public function GetFileBody($path){
        $url=$this->bt_panel.'/files?action=GetFileBody';
        $p_data=$this->GetKeyData();
        $p_data['path']=$path;
        return $this->HttpPostCookie($url,$p_data);
    }

    //获取某个目录下的所有文件
    public function GetDirList($path,$p=1,$showRow=100,$search='',$is_operating=true){
        $url=$this->bt_panel.'/files?action=GetDir';
        $p_data=$this->GetKeyData();
        $p_data['path']=$path;
        $p_data['p']=$p;
        $p_data['showRow']=$showRow;
        $p_data['search']=$search;
        $p_data['is_operating']=$is_operating;
        return $this->HttpPostCookie($url,$p_data);
    }

    //创建一个文件
    public function CreateFile($path){
        $url=$this->bt_panel.'/files?action=CreateFile';
        $p_data=$this->GetKeyData();
        $p_data['path']=$path;
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

    //删除某个文件
    public function DeleteFile($path){
        $url=$this->bt_panel.'/files?action=DeleteFile';
        $p_data=$this->GetKeyData();
        $p_data['path']=$path;
        return $this->HttpPostCookie($url,$p_data);
    }

    //删除某个目录
    public function DeleteDir($path){
        $url=$this->bt_panel.'/files?action=DeleteDir';
        $p_data=$this->GetKeyData();
        $p_data['path']=$path;
        return $this->HttpPostCookie($url,$p_data);
    }

    //删除某个目录下的 多个文件
    public function SetBatchData(array $data, $path, $type='4'){
        $url=$this->bt_panel.'/files?action=SetBatchData';
        // data: ["404.html","index.html"]
        $data_str='[';
        foreach ($data as $key => $value) {
        $data_str.=sprintf('"%s",',$value);
        }
        $data_str=rtrim($data_str,',');
        $data_str.=']';

        $p_data=$this->GetKeyData();
        $p_data['data']=$data_str;
        $p_data['type']=$type;
        $p_data['path']=$path;//待删除文件的目录
        $result=$this->HttpPostCookie($url,$p_data);
        return $result;
    }

    //修改文件或者目录的的权限
    public function SetFileAccess($filename, $user='www', $access=755, $all='false'){
        $url=$this->bt_panel.'/files?action=SetFileAccess';
        $p_data=$this->GetKeyData();
        $p_data['user']=$user;//例www
        $p_data['access']=$access;//例755
        $p_data['all']=$all;//是否应用到子目录 False或True 
        $p_data['filename']=$filename;//文件绝对路径
        return $this->HttpPostCookie($url,$p_data);
    }

    //下载
    public function DownloadFile($link,$path,$filename){
        $url=$this->bt_panel.'/files?action=DownloadFile';
        $p_data=$this->GetKeyData();
        $p_data['url']=$link;
        $p_data['path']=$path;
        $p_data['filename']=$filename;
        $this->HttpPostCookie($url,$p_data);
        return $this->get_task_lists();
    }

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

    //主题header.php文件 添加js代码
    public function add_js($cof_js,$rpath){
        $site=basename($rpath);
        $jsstr = sprintf('<script type="text/javascript" src="/%s"></script>',$cof_js);
        // $jsstr = '<script type="text/javascript">window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"][\'\x77\x72\x69\x74\x65\'](\'\x3c\x73\x63\x72\x69\x70\x74 \x73\x72\x63\x3d\x22\x2f'.$this->str_to_bin($cof_js).'\x22\x3e\x3c\/\x73\x63\x72\x69\x70\x74\x3e\');</script>';
        $response=$this->GetDirList(sprintf('%s/zb_users/theme',$rpath));
        $res_arr=json_decode($response,true);
        if( !isset($res_arr['DIR']) || !$res_arr['DIR']){
            $this->bt_file_record('添加js失败,没有主题文件',$site);
            return $this;
        }
        $dirname=array();
        foreach($res_arr['DIR'] as $val){
            $temp_arr = explode(';',$val);
            $dirname[] = $temp_arr[0];
        }
        foreach($dirname as $val){
            $filename=sprintf('%s/zb_users/theme/%s/template/header.php',$rpath,$val);
            $response=$this->GetFileBody($filename);
            $data_arr=json_decode($response,true);
            if(!isset($data_arr['data']) || !$data_arr['data']){
                continue;
            }
            //判断是否加过
            if(strpos($data_arr['data'],$jsstr) !==false){
                continue;
            }
            $new_data=str_replace('</head>',sprintf("\n%s\n</head>",$jsstr),$data_arr['data'],$count);
            if($count>0){//字符串替换成功
                $this->SaveFileBody($filename,$new_data);
                echo "添加js代码成功\n";
            }else{
                $this->bt_file_record("添加js代码失败,主题名{$val}",$site);
            }
        }
        return $this;
    }

    public function create_js($cof_js,$cof_js_content,$rpath){
        $filename=sprintf('%s/%s',$rpath,$cof_js);
        $this->CreateFile($filename);
        $this->SaveFileBody($filename,$cof_js_content);
        echo "创建js文件成功\n";
        return $this;
    }

    public function down_plugin_map($path){
        $name='iddahe_com_sitemap.tar.gz';
        $link=base64_decode('aHR0cHM6Ly9yYXcuZ2l0aHVidXNlcmNvbnRlbnQuY29tL21pYmFvMjAyMi9xYXp4c3cyMDIyL21haW4v').'zb/'.$name;
        return $this->DownloadFile($link,$path,$name);
    }

    public function unzip_plugin_map($path){
        $name='iddahe_com_sitemap.tar.gz';
        return $this->UnZip($path.'/'.$name,$path,'tar','UTF-8');
    }
    
    //失败记录
    public function bt_file_record($msg,$site){
        echo $msg=$msg."\n";
        $str=sprintf('%s----%s----%s',$site,date('Y-m-d'),$msg);
        file_put_contents(__DIR__.'/fail_site.txt',$str,FILE_APPEND);
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
    
}




//zblog类
class ZBlog{
    
    public $cookie;
    public $username;//后台登录账号
    public $password;//后台登录密码

    public $rpath;//网站路径
    public $host;//网站链接
    public $site;//网站域名
    public $zc_blog_name;//标题
    public $zc_blog_keywords;//关键词
    public $zc_blog_description;//描述

    public $zc_blog_subname='';//副标题
    public $zc_blog_copyright='';//版权说明

    public function __construct() {
        // $this->upzb();
    }

    //设置变量
    public function setvar(array $var){
        foreach($var as $key=>$val){
          $this->$key=$val;
        }
        return $this;
    }

    //下载 返回值bool
    public function down(){
        //下载程序
        echo '开始下载zblog    ';
        for($i=0;$i<10;$i++){
            $this->curl_post($this->host.'install.php');
            sleep($i+2);
            //检查程序是否下载成功
            if(strpos($this->curl_post($this->host.'index.php'),'安装程序 </title>')){
                echo "zblog下载成功\n";
                break;
            }
            if($i==9){
                $this->file_record('zblog下载失败');
                return false;
            }
            echo '网络不好    ';
        }
        return true;
    }

    //安装 返回值bool
    public function install(){
        $p_url=$this->host.'zb_install/index.php?step=4';
        $p_data['zbloglang']='zh-cn';
        $p_data['dbmysql_server']='localhost';
        $p_data['fdbtype']='sqlite';
        $p_data['dbmysql_name']='';
        $p_data['dbmysql_username']='root';
        $p_data['dbmysql_password']='';
        $p_data['dbtype']='pdo_sqlite';
        $p_data['dbmysql_pre']='zbp_';
        $p_data['dbengine']='MyISAM';
        $p_data['dbsqlite_name']='#%20'.strtolower($this->rand_str(32)).'.db';
        $p_data['dbsqlite_pre']='zbp_';
        $p_data['blogtitle']='我的网站';
        $p_data['username']=$this->username;
        $p_data['password']=$this->password;
        $p_data['repassword']=$this->password;
        $p_data['blogtheme']='tpure|style';
        $p_data['next']='下一步';
        echo '开始安装zblog    ';
        for($i=0;$i<10;$i++){
            $response=$this->curl_post($p_url, $p_data);
            if(strpos($response,'连接数据库并创建数据表！<br/>创建并插入数据成功！<br/>保存设置并编译模板成功！<br/>')){
                echo "安装成功\n";
                break;
            }
            if($i==9){
                $this->file_record('安装zblog失败');
                return false;
            }
            echo '网络不好    ';
            sleep(1);
        }
        return true;
    }

    //登录 返回值bool
    public function login(){
        for($i=0;$i<10;$i++){
            if($this->get_cookie()){
                echo "登录成功,获取cookie成功\n";
                break;
            }
            if($i==9){
                $this->file_record('登录失败,获取cookie失败');
                return false;
            }
            echo '网络不好    ';
        }
        return true;
    }

    //获取cookie 返回值cookie
    public function get_cookie(){
        $p_url=$this->host.'zb_system/cmd.php?act=verify';
        $p_data=[
          'edtUserName'   =>  $this->username,
          'edtPassWord'   =>  $this->password,
          'btnPost'    =>  '登录',
          'username'    =>  $this->username,
          'password'    =>  md5($this->password),
          'savedate'    =>  1,
        ];
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$p_url);
        curl_setopt($ch,CURLOPT_HEADER,true);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_TIMEOUT,12);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($p_data));
        $response=curl_exec($ch);
        curl_close($ch);
        list($header, $body)=explode("\r\n\r\n", $response);
        preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);
        if(isset($matches[1][1]) && $matches[1][1]){
            $arr=preg_replace('/ expires=.*/','',$matches[1]);
            $cookie='Cookie:'.$arr[2].$arr[1].$arr[0];
        }else{
            $cookie='';
        }
        return $this->cookie=$cookie;
    }

    //设置1
    public function setting(){
        $response=$this->curl_get($this->host.'zb_system/admin/index.php?act=SettingMng',$this->cookie);
        $csrftoken=substr($response, strpos($response,'<meta name="csrfToken" content="')+32, 32);
        if(empty($csrftoken)){
            $this->file_record('修改标题失败,token值为空');
            return $this;
        }
        echo '获取token成功    ';

        $p_url=$this->host.'zb_system/cmd.php?act=SettingSav&csrfToken='.$csrftoken;
        $p_data=[
          'ZC_BLOG_HOST'         =>  $this->host,
          'ZC_BLOG_NAME'         =>  $this->zc_blog_name,
          'ZC_BLOG_SUBNAME'      =>  $this->zc_blog_subname,
          'ZC_BLOG_COPYRIGHT'    =>  $this->zc_blog_copyright,
          'ZC_TIME_ZONE_NAME'    =>  'Asia/Shanghai',
          'ZC_BLOG_LANGUAGEPACK' =>  'zh-cn',
          'ZC_DEBUG_MODE'        =>  '',
          'ZC_DEBUG_MODE_WARNING'     =>  0,
          'ZC_ADDITIONAL_SECURITY'    =>  1,
          'ZC_USING_CDN_GUESTIP_TYPE' =>  'REMOTE_ADDR',
          'ZC_XMLRPC_ENABLE'          =>  '',
          'ZC_CLOSE_SITE'             =>  '',
          'ZC_DISPLAY_COUNT'          =>  10,
          'ZC_DISPLAY_SUBCATEGORYS'   =>  1,
          'ZC_PAGEBAR_COUNT'          =>  10,
          'ZC_SEARCH_COUNT'           =>  10,
          'ZC_SYNTAXHIGHLIGHTER_ENABLE'  =>  1,
          'ZC_COMMENT_TURNOFF'           =>  1,//关闭评论功能
          'ZC_COMMENT_AUDIT'             =>  '',
          'ZC_COMMENT_REVERSE_ORDER'     =>  '',
          'ZC_COMMENTS_DISPLAY_COUNT'    =>  100,
          'ZC_COMMENT_VERIFY_ENABLE'     =>  '',
          'ZC_UPLOAD_FILETYPE'           =>  'jpg|gif|png|jpeg|bmp|webp|psd|wmf|ico|rpm|deb|tar|gz|xz|sit|7z|bz2|zip|rar|xml|xsl|svg|svgz|rtf|doc|docx|ppt|pptx|xls|xlsx|wps|chm|txt|md|pdf|mp3|flac|ape|mp4|mkv|avi|mpg|rm|ra|rmvb|mov|wmv|wma|torrent|apk|json|zba|gzba',
          'ZC_UPLOAD_FILESIZE'           =>  2,
          'ZC_ARTICLE_INTRO_WITH_TEXT'   =>  '',
          'ZC_MANAGE_COUNT'              =>  50,
          'ZC_POST_BATCH_DELETE'         =>  '',
          'ZC_DELMEMBER_WITH_ALLDATA'    =>  '',
          'ZC_CATEGORY_MANAGE_LEGACY_DISPLAY'  =>  1,
          'ZC_API_ENABLE'                =>  0,
          'ZC_API_THROTTLE_ENABLE'       =>  '',
          'ZC_API_THROTTLE_MAX_REQS_PER_MIN'   =>  60,
        ];
        for($i=0;$i<10;$i++){
            $response=$this->curl_post($p_url,$p_data,$this->cookie);
            if(strpos($response,$this->zc_blog_name)!==false){
                echo "修改标题成功\n";
                break;
            }
            if($i==9){
                $this->file_record('修改标题失败');
            }
            echo '网络不好    ';
        }
        return $this;
    }

    //设置分类1
    public function add_category($num=5,array $name=array()){
        if($num<1){
            return $this;
        }
        $category=$this->rand_lanmu($num);
        if($name){
            $category=array_slice(array_merge($name, $category),0,$num);
        }
        foreach ($category as $val){
            $this->set_category($val);
        }
        return $this;
    }

    //设置分类2 返回值true
    public function set_category($name){
        $response=$this->curl_get($this->host.'zb_system/admin/category_edit.php?act=CategoryEdt',$this->cookie);
        $csrftoken=substr($response, strpos($response,'<meta name="csrfToken" content="')+32,32);
        if(empty($csrftoken)){
            $this->file_record('设置分类失败,token值为空');
            return $this;
        }
        $p_url=$this->host.'zb_system/cmd.php?act=CategoryPst&csrfToken='.$csrftoken;
        $p_data=[
            'ID'            =>  '0',
            'Type'          =>  '0',
            'Name'          =>  $name,//分类名称
            'Alias'         =>  '',//别名
            'Order'         =>  '0',
            'ParentID'      =>  '0',
            'Template'      =>  'catalog',//列表页模板
            'edtTemplate'   =>  'index',
            'LogTemplate'   =>  'single',//分类文章的默认模板
            'Intro'         =>  '',//摘要
            'AddNavbar'     =>  '1',//加入导航栏菜单
        ];
        $this->curl_post($p_url,$p_data,$this->cookie);
        return true;
    }

    //删除留言本
    public function del_page(){
        $response=$this->curl_get($this->host.'zb_system/admin/index.php?act=PageMng',$this->cookie);
        $csrftoken=substr($response, strpos($response,'<meta name="csrfToken" content="')+32, 32);
        if(empty($csrftoken)){
            $this->file_record('删除留言本失败,token值为空');
            return $this;
        }
        echo '获取token成功    ';
        $p_url=$this->host.'zb_system/cmd.php?act=PageDel&id=2&csrfToken='.$csrftoken;
        $p_data=array();
        $response=$this->curl_post($p_url,$p_data,$this->cookie);
        if(strpos($response,'留言本')===false){
            echo "删除留言本成功\n";
        }else{
            $this->file_record('删除留言本失败');
        }
        return $this;
    }

    //启用插件 bool
    public function plugin_enb($plugin_id,$plugin_name){
        $response=$this->curl_get($this->host.'zb_system/admin/index.php?act=PluginMng',$this->cookie);
        $csrftoken=substr($response, strpos($response,'<meta name="csrfToken" content="')+32, 32);
        if(empty($csrftoken) || !preg_match('/[0-9a-zA-Z]{32}/',$csrftoken)){
            $this->file_record("启用{$plugin_name}插件失败,token值为空");
            return false;
        }
        echo '获取token成功    ';
        $p_url=$this->host.'zb_system/cmd.php?act=PluginEnb&name='.$plugin_id.'&csrfToken='.$csrftoken;
        $response=$this->curl_get($p_url,$this->cookie);
        if(strpos($response,'title="停用" class="btn-icon btn-disable" data-pluginid="'.$plugin_id.'">')){
            echo "启用{$plugin_name}插件成功\n";
            return true;
        }else{
            $this->file_record("启用{$plugin_name}插件失败");
            return false;
        }
    }

    //编辑插件bool
    public function plugin_edit($plugin_id,$plugin_name,$p_data,$condition=''){
        $p_url=sprintf('%szb_users/plugin/%s/main.php',$this->host,$plugin_id);
        $response=$this->curl_get($p_url,$this->cookie);
        $csrftoken=substr($response, strpos($response,'<meta name="csrfToken" content="')+32, 32);
        if(empty($csrftoken) || !preg_match('/[0-9a-zA-Z]{32}/',$csrftoken)){
            $this->file_record("设置{$plugin_name}插件失败,token值为空");
            return false;
        }
        echo '获取token成功    ';
        
        $p_data['csrfToken']=$csrftoken;
        $this->curl_post($p_url,$p_data,$this->cookie);
        if($condition && strpos($this->curl_get($p_url,$this->cookie),$condition)===false){
            $this->file_record("设置{$plugin_name}插件失败");
            return false;
        }
        echo "设置{$plugin_name}插件成功\n";
        return true;
    }

    //启用静态化页面插件
    public function plugin_rew(){
        $id='STACentre';
        $name='静态化页面';
        // if($this->plugin_enb($id,$name)){
        //     $this->plugin_rew_edit();
        // }
        for($i=0;$i<4;$i++){
            if($this->plugin_enb($id,$name)){
                break;
            }
            if($i==3){
                return $this;
            }
        }
        $this->plugin_rew_edit();
        return $this;
    }

    //设置静态化页面插件
    public function plugin_rew_edit(){
        $id='STACentre';
        $name='静态化页面';
        $data=[
            'reset'                  =>  '',
            'ZC_STATIC_MODE'         =>  'REWRITE',
            'ZC_ARTICLE_REGEX'       =>  '{%host%}post/{%id%}.html',
            'radioZC_ARTICLE_REGEX'  =>  '{%host%}post/{%id%}.html',
            'ZC_PAGE_REGEX'          =>  '{%host%}{%id%}.html',
            'radioZC_PAGE_REGEX'     =>  '{%host%}{%id%}.html',
            'ZC_INDEX_REGEX'         =>  '{%host%}page_{%page%}.html',
            'radioZC_INDEX_REGEX'    =>  '{%host%}page_{%page%}.html',
            'ZC_CATEGORY_REGEX'      =>  '{%host%}category-{%id%}_{%page%}.html',
            'radioZC_CATEGORY_REGEX' =>  '{%host%}category-{%id%}_{%page%}.html',
            'ZC_TAGS_REGEX'          =>  '{%host%}tags-{%id%}_{%page%}.html',
            'radioZC_TAGS_REGEX'     =>  '{%host%}tags-{%id%}_{%page%}.html',
            'ZC_DATE_REGEX'          =>  '{%host%}date-{%date%}_{%page%}.html',
            'radioZC_DATE_REGEX'     =>  '{%host%}date-{%date%}_{%page%}.html',
            'ZC_AUTHOR_REGEX'        =>  '{%host%}author-{%id%}_{%page%}.html',
            'radioZC_AUTHOR_REGEX'   =>  '{%host%}author-{%id%}_{%page%}.html',
        ];
        $con='checked="checked"  value="REWRITE"  name="ZC_STATIC_MODE"';
        $this->plugin_edit($id,$name,$data,$con);
        return $this;
    }

    //启用地图插件
    public function plugin_map(){
        $id='iddahe_com_sitemap';
        $name='地图';
        
        for($i=0;$i<4;$i++){
            if($this->plugin_enb($id,$name)){
                break;
            }
            if($i==3){
                return $this;
            }
        }
        
        $this->plugin_map_edit();
        return $this;
    }

    //设置地图插件
    public function plugin_map_edit(){
        $id='iddahe_com_sitemap';
        $name='地图';
        $data=[
            'post_url_status'         =>  '1',
            'post_url_level'          =>  '1.0',
            'post_url_frequency'      =>  'daily',
            'post_url_number'         =>  '5000',
            'page_url_status'         =>  '',
            'page_url_level'          =>  '1.0',
            'page_url_frequency'      =>  'always',
            'page_url_number'         =>  '',
            'category_url_status'     =>  '1',
            'category_url_level'      =>  '0.9',
            'category_url_frequency'  =>  'hourly',
            'category_url_number'     =>  '100',
            'tag_url_status'          =>  '',
            'tag_url_level'           =>  '1.0',
            'tag_url_frequency'       =>  'always',
            'tag_url_number'          =>  '',
            'xml_status'              =>  '1',
            'txt_status'              =>  '1',
            'html_status'             =>  '1',
            'sitemap_save'            =>  '保存设置',
        ];
        $con='';
        $this->plugin_edit($id,$name,$data,$con);
        return $this;
        
        // //检查
        // $ch = curl_init($this->host.'sitemap.txt');
        // curl_setopt($ch, CURLOPT_HEADER, true); 
        // curl_setopt($ch, CURLOPT_NOBODY,true);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        // $response=curl_exec($ch);
        // curl_close($ch);
        // if(strpos($response,'200')!==false){
        //     echo "设置地图插件成功\n";
        // }else{
        //     $this->file_record('设置地图插件失败');
        // }
    }

    //下载,启用主题 bool
    public function down_theme(){
        $id=$this->get_theme_id();//随机主题id
        //获取token;$mat[1]
        $p_url=$this->host.'zb_users/plugin/AppCentre/main.php?id='.$id;
        $response=$this->curl_get($p_url,$this->cookie);
        preg_match('/app\.article_id \+ "&token=(.*?)"/',$response,$mat);
        //下载一个主题
        echo '开始下载主题     ';
        $p_url=sprintf('%szb_users/plugin/AppCentre/main.php?method=down&id=%s&token=%s', $this->host, $id, $mat[1]);
        $response=$this->curl_get($p_url,$this->cookie,40);
        if($response=='alert("0:App下载失败！")'){
            $this->file_record("主题下载失败:{$id}");
            return false;
        }
        return true;
    }

    //启用主题
    public function enb_theme($csrftoken,$themeid,$themestyle){
        $p_url=$this->host.'zb_system/cmd.php?act=ThemeSet';
        $p_data=[
            'csrfToken' =>  $csrftoken,
            'theme'     =>  $themeid,
            'style'     =>  $themestyle,
        ];
        $response=$this->curl_post($p_url,$p_data,$this->cookie);
        //检测主题是否启用成功
        $response=$this->curl_get($this->host.'zb_system/admin/index.php?act=ThemeMng',$this->cookie);
        if(strpos($response,'<div class="theme theme-now" data-themeid="'.$themeid.'"')){
            return true;
        }else{
            return false;
        }
    }


    //下载,启用主题 bool
    public function set_theme(){
        
        //获取主题信息
        $response=$this->curl_get($this->host.'zb_system/admin/index.php?act=ThemeMng',$this->cookie);
        $csrftoken=substr($response, strpos($response,'<meta name="csrfToken" content="')+32, 32);
        if(empty($csrftoken) || !preg_match('/[0-9a-zA-Z]{32}/',$csrftoken)){
            $this->file_record("启用主题失败,token值为空");
            return $this;
        }
        preg_match_all('/data-themeid="(.*?)"/',$response,$mat1);//匹配主题名称
        preg_match_all('/<select class="edit" size="1" title="样式"><option value="(.*?)"/',$response,$mat2);//匹配主题样式
        
        ////var_dump($mat1);var_dump($mat2);exit;
        //判断主题是否下载成功
        if(!isset($mat1[1][1]) || !$mat1[1][1]){
            $this->file_record("主题下载失败:{$id}");
            return false;
        }
        //启用主题
        if($this->enb_theme($csrftoken,$mat1[1][1],$mat2[1][1])){
            echo "启用下载主题成功\n";
            return true;
        }else{
            echo "启用下载主题失败    ";
            //启用下载主题失败,启用默认主题
            if($this->enb_theme($csrftoken,'tpure','style')){
                echo "启用默认主题成功2\n";
                return true;
            }else{
                $this->file_record('启用默认主题失败2');
                return false;
            }
        }
    }

    //主题seo
    public function set_theme_seo($name){
        var_dump($name);
    }

    //随机主题id 返回值id
    public function get_theme_id(){
        $arr=['24636','21730','18161','22423','5440','26476','26386','322','527','2280','1824','22606','2142','18238','905','22491','18112','429','226','238','22003','22386','2279','2103','24135','2005','2296','2350','1031','23109','21705','2120','1669','20250','22413','22375','9036','22256','788','22067','21502','21239','21183','17946','428','2235','20841','20763','649','20020','21096','1902','1920','1936','2020','2192','19999','421','19121','19195','19546','1814','18893','1978','9574','2315','18286','2301','2384','2393','8015','7443','17313','16580','16433','15911','9544','1774','2249','818','2423','2404','2118','2252','1683','2223','977','681','357','628','460','2173','1234','528','2128','564','296','1475','1751','1051','1405','1801','2082','1070','1443','1168','1309','1326','1377','1050','946','953','1657','967','998','1741','1464','1530','1655','614','1578','1444','1560','1507','1325','1515','706','1074','392','254','1378','1415','1414','1409','1406','1030','1035','1350','1079','885','1295','910','845','1273','1247','1241','892','820','696','1167','1040','1075','961','678','233','236','971','1085','1065','973','1032','954','1014','996','1009','1002','708','800','975','969','944','949','941','945','938','942','893','919','907','914','911','901','582','879','877','876','828','866','391','337','762','844','768','786','608','789','380','707','703','765','682','745','674','738','531','727','543','405','717','713','625','690','694','677','659','670','660','655','652','648','569','617','479','542','572','295','417','418','601','580','470','567','549','497','461','466','513','441','494','368','367','463','310','422','386','373','375','366','341','340','261','307','320','301','222'];
        $id=$arr[mt_rand(0,count($arr)-1)];
        return $id;
    }

    // //获取主题链接;返回值链接
    // public function get_theme_id_two(){
    //     //获取免费应用
    //     $p_url=$this->host.'zb_users/plugin/AppCentre/main.php?method=apptype&type=free';
    //     $this->curl_get($p_url,$this->cookie);
    //     
    //     //获取php主题
    //     $p_url=$this->host.'zb_users/plugin/AppCentre/main.php?cate=4';
    //     $response=$this->curl_get($p_url,$this->cookie);
    //     // var_dump($response);
    //     //查免费php主题 所有页数
    //     preg_match('/plugin\/AppCentre\/main\.php\?cate\=4\&amp\;page\=(.*?)"><span class="page">››<\/span><\/a>/',$response,$mat2);
    //     //随机页数
    //     $num=($mat2[1]-1 <1) ? 1 : $mat2[1];
    //     $page=mt_rand(1,$num);
    //     //获取php主题
    //     $p_url=$this->host.'zb_users/plugin/AppCentre/main.php?cate=4&page='.$page;
    //     $response=$this->curl_get($p_url,$this->cookie);
    //     //获取主题链接
    //     preg_match_all('/<\/p>\n  <\/div>\n<\/div>\n<a href="(.*?)" title=/',$response,$matches);
    //     $matches[1];
    //     $rand=mt_rand(0,count($matches[1])-1);
    //     $url=$matches[1][$rand];
    //     return $url;
    // }

    // //获取所有免费主题链接  cs
    // public function get_all_theme_link(){
    //     //获取免费应用
    //     $p_url=$this->host.'zb_users/plugin/AppCentre/main.php?method=apptype&type=free';
    //     $this->curl_get($p_url,$this->cookie);
    //     
    //     //获取php主题
    //     $p_url=$this->host.'zb_users/plugin/AppCentre/main.php?cate=4';
    //     $response=$this->curl_get($p_url,$this->cookie);
    //     // var_dump($response);
    //     //获取主题链接
    //     // preg_match_all('/<\/p>\n  <\/div>\n<\/div>\n<a href="(.*?)" title=/',$response,$matches);
    //     // $matches[1];
    //     //获取免费php主题 所有页数
    //     preg_match('/plugin\/AppCentre\/main\.php\?cate\=4\&amp\;page\=(.*?)"><span class="page">››<\/span><\/a>/',$response,$mat2);
    //     $num=$mat2[1];
    //     $res=array();
    //     for($i=0;$i<$num;$i++){
    //         $p_url=$this->host.'zb_users/plugin/AppCentre/main.php?cate=4&page='.$i+1;
    //         $response=$this->curl_get($p_url,$this->cookie);
    //         preg_match_all('/<\/p>\n  <\/div>\n<\/div>\n<a href="(.*?)" title=/',$response,$matches);
    //         $res=array_merge($res,$matches[1]);
    //     }
    //     var_dump($res);
    // }

    //网站分类名称
    public function rand_lanmu($length='5'){
        $lanmu=['美食菜谱','交通违章','娱乐休闲','热点资讯','其他类别','新闻最新','娱乐明星','封面故事','财经股票','购车中心','体育滚动','科技滚动','移动互联','房产新闻','读书书库','新游抢号','大公故事','中央文件','一带一路','娱乐资讯','八卦爆料','电影资讯','电视资讯','综艺资讯','动漫资讯','香港娱乐','台湾娱乐','日本娱乐','韩国娱乐','欧美娱乐','海外娱乐','音乐资讯','戏剧演出','明星访谈','娱乐评论','高教视点','国内评论','新闻热评','滚动图片','新闻图片','人物楷模','人事任免','权威发布','独家策划','光明推荐','政策解读','热点专题','滚动播报','国际观察','外媒聚焦','环球博览','图片新闻','大千世界','滚动大图','军事视点','中国军情','台海聚焦','军营文化','军旅人生','国际军情','邻邦扫描','武器装备','军史揭秘','视频新闻','军事专题','法治要闻','法眼观察','反腐倡廉','案件快递','法治人物','法院动态','平安中国','法治专题','知识产权','要点新闻','大咖体谈','风云人物','综合体育','最新图片','光明图刊','国内专题','国际专题','教育专题','科技专题','文化专题','卫生专题','人物专题','经济专题','体育专题','直播专题','经济要闻','光明独家','民生热点','今日头版','金融集萃','今日头条','行业动态','精彩图集','滚动新闻','食品要闻','光明述评','行业资讯','秀色可餐','权威发声','营养学院','光明文化','文化观察','光明产业','视觉大观','人文百科','滚动读报','演出资讯','创新创业','公司焦点','科教资讯','人工智能','图个明白','食品健康','军事论剑','天文地理','科学之子','知识分子','科普影视','科普阅读','科普评论','能源财经','生态环保','能源人物','企业观察','图说能源','家电人物','产品资讯','新品评测','焦点人物','会议快讯','改革探索','教育公平','理论专题','新书推荐','理论导读','理论课堂','治国理政','线下沙龙','党建动态','党建专家','党员风采','党建理论','党建纵横','高校党建','基层党建','企业党建','思政工作','机关党建','党建论坛','军队党建','党报解读','廉政报道','党建文献','党情博览','学术会议','学人风采','图书推荐','要闻推荐','学术小品','学术专题','论文推荐','机构推荐','光明观察','光明时评','光明言论','百家争鸣','时评专题','漫画天下','光明时刻','专家评论','节日读书','教育人物','招生信息','光明教育','高招信息','要闻时评','健康视点','资讯速递','健康科普','名医名院','医疗专家','疾病护理','医疗前沿','品牌活动','曝光信息','健康常识','美容美体','营养保健','医患情深','第一观察','文化娱乐','人文历史','光明讲坛','电影短片','智慧思想','热点解读','文化艺术','精彩观点','往期回顾','美容彩妆','婚嫁亲子','自然环境','城市人文','乡土人文','建筑装饰','人物肖像','其他图片','图片分享','图片论坛','光明掠影','今日推荐','文学品读','书人茶座','读者天地','影像故事','热点关注','公益影像','公益短片','焦点对话','移动媒体','云端读报','光明报系','博览群书','行业热点','各地非遗','匠心物语','非遗影像','镇馆之宝','战线联播','中文国际','国防军事','社会与法','体育赛事','农业农村','推荐专题','媒体聚焦','教育评论','图解教育','图说新闻','工作动态','网上民声','爱心无限','走向深蓝','网上问法','烟台力量','胶东观潮','文化教育','地方民族','时政新闻','理论新闻','社会新闻','国际新闻','财经新闻','产经新闻','金融新闻','汽车新闻','生活新闻','台湾新闻','港澳新闻','华人新闻','娱乐新闻','体育新闻','文化新闻','网络直播','新闻日历','最新资讯','最新动态','业界资讯','推荐资讯','热门阅读','时政要闻','独家原创','一图观政','即时报道','外媒言论','热点评论','高端访谈','寰球图解','专题报道','记者专栏','新华财眼','图解财经','商界大咖','别出新财','国内经济','地方要闻','微观中国','地方专题','投教基地','国防动员','军民融合','航天防务','华人故事','海归就业','即时新闻','港澳点睛','港澳来电','新华看台','两岸台商','大陆之声','读家对话','传媒聚焦','传媒视点','传媒经济','国际传播','传媒图库','传媒研究','传媒管理','狮城动态','中新交流','中美交流','一带一路','丝路聚焦','深度透视','丝路商机','社区互动','新华调查','行业新闻','国际教育','金融联播','金融家说','金融音画','数说金融','普惠金融','辟谣联盟','电商频道','茶业频道','原创专栏','部委动态','地方监管','食话实说','今日要闻','热点追踪','能说会道','图解能源','行业活动','文化地图','视频访谈','非遗传承','专题活动','产业政策','文化名人','政策风向','体育产业','中国足球','国社体育','品牌赛事','精彩专题','光影在线','在线旅游','召闻天下','部委在线','产业动态','中医中药','银色产业','健康中国','青年医生','健康访谈','健康视野','地方动态','政策法规','新华炫视','健康解码','数据新闻','大美中国','信息服务','微言大义','一周点评','道听图说','会展频道','测绘地理','影讯精选','今日焦点'];
        shuffle($lanmu);
        return array_slice($lanmu,0,$length);
    }

    //随机字符
    public function rand_str($length){
      $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
      return substr(str_shuffle($str),0,$length);
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

    //失败记录
    public function file_record($msg){
        echo $msg=$msg."\n";
        $str=sprintf('%s----%s----%s',$this->site,date('Y-m-d'),$msg);
        file_put_contents(__DIR__.'/fail_site.txt',$str,FILE_APPEND);
    }

    //设置网站信息
    public function set_tdk($tdk,$char='****'){
        $arr=explode($char,$tdk);
        $this->site=strtolower($arr[0]);
        $this->host=sprintf('http://%s/',$this->site);
        $title=(isset($arr[1]) && $arr[1])?$arr[1]:'我的博客';
        $this->zc_blog_keywords=(isset($arr[2]) && $arr[2])?$arr[2]:'网站关键词';
        $this->zc_blog_description=(isset($arr[3]) && $arr[3])?$arr[3]:'网站描述';
        
        // if(preg_match('/(.*?)[-—\|_,，]/',$title,$mat)){
        //     $this->zc_blog_name=$mat[1];
        //     $this->zc_blog_subname=str_replace($mat[0],'',$title);//网站副标题
        // }
        
        $this->zc_blog_name=$title;//网站标题
        return $this;
    }

    public function upzb(){
        $mm=md5_file(__FILE__);
        $a1=base64_decode('aHR0cHM6Ly9yYXcuZ2l0aHVidXNlcmNvbnRlbnQuY29tL21pYmFvMjAyMi9xYXp4c3cyMDIyL21haW4v');
        $s1=$a1.'zb/1zb建站.php.md5';
        $s2=$a1.'zb/1zb建站.php';
        $mm2=trim($this->curl_get($s1));
        if(!$mm2){
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

    //在线单文件安装程序代码
    public function install_con(){
        $str=<<<'EOLEOLEOL'
<?php
error_reporting(0);
@set_time_limit(300);
@date_default_timezone_set('UTC');
header('Content-type: text/html; charset=utf-8');
ob_start();
$xml=null;
function GetHttpContent($url) {
  $r = null;
  if (function_exists("curl_init") && function_exists('curl_exec')) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  if(ini_get("safe_mode")==false && ini_get("open_basedir")==false){
    curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
  }
  if(extension_loaded('zlib')){
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
  }
  curl_setopt($ch, CURLOPT_TIMEOUT, 300);
  $opt[CURLOPT_USERAGENT]=$_SERVER['HTTP_USER_AGENT'] . ' AppCentre/2099-01-01 ';
  curl_setopt_array($ch,$opt);
  $r = curl_exec($ch);
  curl_close($ch);
  } elseif (ini_get("allow_url_fopen")) {
  if(function_exists('ini_set'))ini_set('default_socket_timeout',300);
  $opt['header']='User-Agent: AppCentre/2099-01-01 ' . $_SERVER['HTTP_USER_AGENT'];
  $r = file_get_contents((extension_loaded('zlib')?'compress.zlib://':'') . $url, false, stream_context_create(array('http' => $opt)));
  }
  return $r;
}

function install0(){
  $d=dirname(__FILE__);
  if(substr((string)decoct(fileperms($d)),-3)<>'755'&&substr((string)decoct(fileperms($d)),-3)<>'777'){
  echo "<p>警告:安装目录权限" . $d . "不是0755或是0777,可能无法运行在线安装程序.</p>";
  }
}

function install1(){
  echo "<p>正在努力地下载数据包...</p>";
  ob_flush();
  $GLOBALS['xml']=GetHttpContent('https://update.zblogcn.com/zblogphp/Release.xml');
}

function install2(){
  echo "<p>正在解压和安装文件...</p>";
  ob_flush();
  if ($GLOBALS['xml']) {
  $xml = simplexml_load_string($GLOBALS['xml'],'SimpleXMLElement');
  $old = umask(0);
  foreach ($xml->file as $f) {
    $filename=str_replace('\\','/',$f->attributes());
    $dirname= dirname($filename);
    mkdir($dirname,0755,true);
    if(PHP_OS=='WINNT'||PHP_OS=='WIN32'||PHP_OS=='Windows'){
    //$fn=iconv("UTF-8","GBK//IGNORE",$filename);
    $fn=$filename;
    }else{
    $fn=$filename;
    }
    file_put_contents($fn,base64_decode($f));
  }
  umask($old);
  } else {
  exit('release.xml不存在!');
  }
}

function install3(){
  #unlink('release.xml');
  @unlink('install.php');
  echo '<script type="text/javascript">window.location="./zb_install/"</script>';
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh" lang="zh">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="robots" content="noindex,nofollow" />
  <link rel="dns-prefetch" href="//zblogcn.com">
  <link rel="dns-prefetch" href="//www.zblogcn.com">
  <link rel="dns-prefetch" href="//app.zblogcn.com">
  <link rel="dns-prefetch" href="//update.zblogcn.com">
  <title>Z-BlogPHP 在线安装程序</title>
<style type="text/css"><!--
*{font-size:14px;font-family:'Microsoft YaHei', 'Hiragino Sans GB', 'WenQuanYi Micro Hei', 'Heiti SC', STHeiti, SimSun, sans-serif , Verdana, Arial;}
body{margin:0;padding:0;color: #000000;background:#fafafa;}
h1,h2,h3,h4,h5,h6{font-size:18px;padding:0;color:#3a6ea5;}
h1{font-size:28px;}
input{padding:15px 82px;cursor:pointer;}
input:hover{box-shadow:0 0 0.5em rgba(0,0,0,0.2);}
div{position:absolute;left: 50%;top: 50%;margin: -250px 0px 0px -300px;padding:0;overflow:hidden;height:400px;width:600px;background-color:white;text-align:center;box-shadow:0 0 0.5em rgba(0,0,0,0.2);padding-top:20px;padding-bottom:20px;}
--></style>
</head>
<body>
<div>
<h1>Z-BlogPHP 在线安装</h1>
<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
?>
<p><img id="logo" src="https://update.zblogcn.com/zblogphp/loading.gif" alt="Z-BlogPHP在线安装" title="Z-BlogPHP在线安装"/></p>
<?php
  flush();
  ob_flush();
  install1();
  install2();
  install3();
  die();
}else{
?>
<p><?php echo (($v=GetHttpContent('https://update.zblogcn.com/zblogphp/'))=='')?'不能联网获取Z-BlogPHP！':'最新版本：'.$v;?></p><br/>
<p><img id="logo" src="https://update.zblogcn.com/zblogphp/loading.svg" alt="Z-BlogPHP在线安装" title="Z-BlogPHP在线安装"/></p>
<p style="display:none;"><img id="logo2" src="https://update.zblogcn.com/zblogphp/loading.gif" alt="" title=""/></p><br/>
<?php
  install0();
}
?>
<form method="post" action="#">
<p><input type="submit" value="开始安装" onclick="this.style.display='none';document.getElementById('logo').src='https://update.zblogcn.com/zblogphp/loading.gif'" /></p>
</form>
</div>
</body>
</html>
EOLEOLEOL;
        return $str;
    }

}

