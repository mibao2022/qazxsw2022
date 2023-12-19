<?php
/**
 * 修改wp网站 文章固定链接设置
 * 
 * 添加电脑外网IP 到防火墙白名单
 * 
 * 

搜索 permalink_structure 自定义固定链接


php /www/1111/xg2-修改wp文章固定链接设置.php


*/
//---------------------------------
//---------------------------------
//wp网站后台账号*
$cof_admin_name='admin111';
//wp网站后台密码*
$cof_admin_password='admin222';
//wp后台登录地址
$cof_admin_addr='wp-login.php';

//固链接格式，复制wp网站后台 改好的固定连接
// $cof_permalink_structure='/%year%%monthnum%%day%/%post_id%.html';//示例1
// $cof_permalink_structure='/news/%post_id%.html';//示例2
$cof_permalink_structure='/archives/%post_id%.html';//示例3

//域名
$cof_site='

111.com
222.com






';
//---------------------------------
//---------------------------------

$cof_admin_name=trim($cof_admin_name);
$cof_admin_password=trim($cof_admin_password);
$cof_admin_addr=trim($cof_admin_addr);
$cof_permalink_structure=trim($cof_permalink_structure);

if(!$cof_admin_name || !$cof_admin_password){
    exit("设置账号密码\r\n");
}
if(!$cof_admin_addr){
    exit("设置后台地址\r\n");
}

$site_arr = explode("\n", trim($cof_site));
$site_arr = array_values(array_filter(array_map('trim',$site_arr)));
if(empty($site_arr)){
    exit("设置域名\r\n");
}

$wp=new WordPress();
$wp->admin_name = $cof_admin_name;
$wp->admin_password = $cof_admin_password;

foreach($site_arr as $key=>$val){
    echo sprintf("----修改第%s站点 %s ----\n",$key,$val);
    $wp->domain = $val;
    $wp->home_page = "http://{$val}";

	//登陆
    if(!$wp->login_wp()){
        //echo $wp->err.=$wp->domain.">>>>登陆失败\r\n";
        $wp->save_text();
        continue;
    }
    //网站设置
    $wp->setting();
    
    $wp->save_text();
}

echo "\r\n完成\r\n";



//wp类
class WordPress{

    public $cookie;
    public $domain;
    public $home_page;//最后面不带/

    public $admin_name;
    public $admin_password;

    public $err;

    //网站设置
    public function setting(){
        //固定链接设置
        $this->options_permalink();
        return $this;
    }

    public function save_text(){
        if($this->err){
            file_put_contents(__DIR__ .'/fail_site.txt',$this->err,FILE_APPEND);
            $this->err='';
        }
    }

    //登录
    public function login_wp(){
        global $cof_admin_addr;
        $this->cookie='';
        $p_url=$this->home_page.'/'.$cof_admin_addr;
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

        if(!$response){
            echo $this->err.=$this->domain.">>>>登录失败，访问失败\r\n";
            return false;
        }

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

    //固定链接设置
    public function options_permalink(){
        global $cof_permalink_structure;
        $p_url=$this->home_page.'/wp-admin/options-permalink.php';
        $response=$this->curl_get($p_url);
         preg_match('/<input type="hidden" id="_wpnonce" name="_wpnonce" value="(.*?)"/',$response,$mat);
        if(isset($mat[1]) && $mat[1]) {
            $wpnonce=$mat[1];
        }else{
            echo $this->err.=$this->domain.">>>>固定链接设置失败,获取wpnonce\r\n";
            return false;
        }

        $p_url=$this->home_page.'/wp-admin/options-permalink.php';
        $p_data=[
            '_wpnonce'              =>  $wpnonce,
            '_wp_http_referer'      =>  '/wp-admin/options-permalink.php?settings-updated=true',
            'selection'             =>  'custom',//自定义
            'permalink_structure'   =>  $cof_permalink_structure,//设置自定义固定链接
            'category_base'         =>  '',
            'tag_base'              =>  '',
            'submit'                =>  '保存更改'
        ];
        $response=$this->curl_post($p_url,$p_data);
        if(strpos($response,'<p><strong>固定链接结构已更新。</strong></p>')){
            echo "固定链接设置成功\n";
            return true;
        }else{
            echo $this->err.=$this->domain.">>>>固定链接设置失败\r\n";
            return false;
        }
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






