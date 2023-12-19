<?php
/**
 * 
 * 
 * 修改网站名称,修改All in One SEO插件TDK信息




*/



//------------------------------------------------
//--------------------设置开始--------------------
//wp网站后台账号*
$cof_admin_name='admin111';
//wp网站后台密码*
$cof_admin_password='admin222.!';

//网站邮箱 留空使用随机数
$cof_blog_email='';

//设置建站域名文件* (内容格式:域名****网站名称****网站标题****网站关键词****描述)
$cof_site_file='site.txt';
//------------------设置结束----------------------
//------------------------------------------------
//------------------------------------------------
//------------------------------------------------



if($cof_site_file[0] != '/'){
    $cof_site_file = __DIR__ .'/' . $cof_site_file;
}
if(!is_file($cof_site_file)){
    exit("域名文件的路径不存在\n");
}
$site_str = file_get_contents($cof_site_file);
$site_arr = explode("\n", trim($site_str));
$site_arr = array_values(array_filter(array_map('trim',$site_arr)));
if(empty($site_arr)){
    exit("设置域名\n");
}

$wp=new WordPress();
$wp->admin_name = $cof_admin_name;
$wp->admin_password = $cof_admin_password;
$wp->email = $cof_blog_email;
foreach($site_arr as $key => $val){
    //tdk设置
    $wp->set_tdk($val);
    echo sprintf("----修改站点 %s ----\n",$wp->site);
    
    if(!$wp->blog_name){
        $wp->save_text();
        continue;
    }
    
    //登陆
    if(!$wp->login()){
        $wp->save_text();
        continue;
    }
    
    //网站设置
    $wp->setting();
    $wp->save_text();
    
}

echo "\n完成\n";



//wp类
class WordPress{
    

    public $admin_name;//wp网站后台账号
    public $admin_password;//wp网站后台密码
    
    public $email;//网站邮箱

    public $domain;//网站域名
    public $home_page;//网站首页网址，结尾不带/

    public $blog_name;//网站名称
    public $blog_title;//网站首页标题
    public $blog_keyw;//网站首页关键词
    public $blog_desc;//网站首页描述

    public $err;
    private $cookie;//网站cookie
    
    //网站设置
    public function setting(){
        //常规设置
        $this->options_general();
        
        //判断设置seo插件
        $this->plugin_seo_set();
        
        return $this;
    }

    
    public function save_text(){
        if($this->err){
            file_put_contents(__DIR__ .'/fail_site.txt',$this->err,FILE_APPEND);
            $this->err='';
        }
    }
    
    
    //登录
    public function login(){
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
        $response = curl_exec($ch);
        curl_close($ch);
        if(!$response){
            echo $this->err.=$this->domain.">>>>登录失败，访问失败\r\n";
            return false;
        }
        
        
        if(strpos($response,'正在执行例行维护，请一分钟后回来。')!==false){
            echo "正在执行例行维护,睡眠60秒\r\n";
            sleep(60);
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $p_url);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($p_data,'','&'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            $response = curl_exec($ch);
            curl_close($ch);
            if(!$response){
                echo $this->err.=$this->domain.">>>>登录失败，访问失败\r\n";
                return false;
            }
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
    

    //判断设置seo插件
    public function plugin_seo_set(){
        $p_url=$this->home_page .'/wp-admin/plugins.php';
        for ($i = 0; $i < 3; $i++) {
            $response = $this->curl_get($p_url);
            if($response){ break; }
            if($i==2){
                echo $this->err .= $this->domain.">>>>设置aioseo插件失败,访问页面\r\n";
                return false;
            }
        }
        
        //按新版本aioseo插件(new)规则去匹配
        if(strpos($response,'多合一SEO集">禁用</a>')!==false){
            $this->plugin_aioseo_edit_new();//编辑
            return true;
        }
        
        //按老版本aioseo插件(old)规则去匹配
        if(strpos($response,'多合一SEO包">禁用</a>')!==false){
            $this->plugin_aioseo_edit_old();//编辑
            return true;
        }
        
        return true;
    }

    //常规设置
    public function options_general(){
        $p_url=$this->home_page.'/wp-admin/options-general.php';
        $response=$this->curl_get($p_url);
        preg_match('/id="_wpnonce" name="_wpnonce" value="(.*?)"/',$response,$mat);//匹配数据
        if(!isset($mat[1]) || !$mat[1]){
            echo $this->err.=$this->domain."网站名称设置失败1，匹配参数失败\r\n";
            return false;
        }
        
        
        $email = $this->email ? $this->email : strtolower($this->rand_str(mt_rand(6,9))) .'@gmail.com';
        $p_url = $this->home_page.'/wp-admin/options.php';
        $p_data = [
            'option_page'        => 'general',
            'action'             => 'update',
            '_wpnonce'           => $mat[1],
            '_wp_http_referer'   => '/wp-admin/options-general.php',
            'blogname'           => $this->blog_name,//标题
            'blogdescription'    => '',//副标题
            'siteurl'            => 'http://'.$this->domain,
            'home'               => 'http://'.$this->domain,
            'new_admin_email'    => $email,
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
        $response=$this->curl_post($p_url,$p_data);
        if(strpos($response,'<p><strong>设置已保存。')===false){
            echo $this->err .= $this->domain."网站名称设置失败\r\n";
            return false;
        }
        echo "网站名称设置成功\n";
        return true;
    }


    // 设置aioseo插件(new)4.2.9版本
    public function plugin_aioseo_edit_new(){
        $p_url=$this->home_page.'/wp-admin/admin.php?page=aioseo-search-appearance#/global-settings';
        $response=$this->curl_get($p_url);
        if(!$response){
            echo $this->err.=$this->domain.">>>>设置aioseo插件(new)失败，返回值为空\r\n";
            // var_dump($response);
            return false;
        }

        // file_put_contents(__DIR__ .'/tmp1.txt',$response);
        preg_match('/var aioseo = ([\s\S]*?)<\/script>/',$response,$mat);//匹配数据
        if(!isset($mat[1]) || !$mat[1]){
            echo $this->err.=$this->domain.">>>>设置aioseo插件(new)失败1，匹配参数失败\r\n";
            return false;
        }
        
        $json_str=rtrim(trim($mat[1]),';');
        $arr=json_decode($json_str,true);
        if(!is_array($arr) || empty($arr)){
            echo $this->err.=$this->domain.">>>>设置aioseo插件(new)失败2，转换数据格式\r\n";
            return false;
        }
        
        //设置
        $arr['options']['searchAppearance']['global']['schema']['websiteName']=$this->blog_name;//设置网站名称
        $arr['options']['searchAppearance']['global']['siteTitle']=$this->blog_title;//设置网站 首页标题
        $arr['options']['searchAppearance']['global']['metaDescription']=$this->blog_desc;//设置网站 首页描述
        
        $arr['options']['searchAppearance']['advanced']['useKeywords']=true;//开启关键词功能
        $arr['options']['searchAppearance']['advanced']['useCategoriesForMetaKeywords']=true;//类别页面使用关键词
        $arr['options']['searchAppearance']['advanced']['useTagsForMetaKeywords']=true;//标签页面使用关键词
        $arr['options']['searchAppearance']['advanced']['dynamicallyGenerateKeywords']=true;//动态生成文章页关键词
        if($this->blog_keyw){
            $arr_gjc=explode(',',$this->blog_keyw);
            foreach ($arr_gjc as $val){
                $gjc[]=array("label"=>$val,"value"=>$val);
            }
        }else{
            $gjc=array();
        }
        $arr['options']['searchAppearance']['global']['keywords']=json_encode($gjc);//设置网站 首页关键词
        $arr['options']['sitemap']['general']['linksPerIndex']='10000';//设置post-sitemap.xml页面最大文章数
        
        $arr['options']['social']['facebook']['general']['enable']=false;//禁用社交網絡facebook的Open Graph標記
        $arr['options']['social']['twitter']['general']['enable']=false;//禁用社交網絡Twitter的Open Graph標記
        
        $p_url=$this->home_page.'/wp-json/aioseo/v1/options';
        $p_data=[
            'options' => $arr['options'],
            'dynamicOptions' => $arr['dynamicOptions'],
            'network' => false,
            'networkOptions' => array(),
        ];
        $p_data=json_encode($p_data);
        $header=array(
            'X-WP-Nonce' => 'X-WP-Nonce: '.$arr['nonce'],
            'Accept' => 'Accept: */*',
            'Content-Type' => 'Content-Type: application/json',
            
        );
        $response = $this->curl_post($p_url,$p_data,$header);
        if(strpos($response,'"success":true')!==false){
            echo "设置aioseo插件(new)成功\r\n";
        }else{
            echo $this->err.=$this->domain.">>>>设置aioseo插件(new)失败3，提交数据\r\n";
            return false;
        }
        return true;
    }


    //设置aioseo插件(old)3.7.1版本
    public function plugin_aioseo_edit_old(){
        $p_url=$this->home_page.'/wp-admin/admin.php?page=all-in-one-seo-pack/aioseop_class.php';
        $response=$this->curl_get($p_url);
        
        preg_match("/<input name='nonce-aioseop' type='hidden'  value='(.*?)'/",$response,$mat);
        if(isset($mat[1]) && $mat[1]){
            $wpnonce=$mat[1];
        }else{
            echo $this->err.=$this->domain.">>>>设置aioseo插件(old)失败1，获取wpnonce\r\n";
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
            'aiosp_home_description'                        =>  $this->blog_desc,//网站首页描述
            'aiosp_length2'                                 =>  $blog_desc_strlen,
            'aiosp_home_keywords'                           =>  $this->blog_keyw,//网站首页关键词
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
            echo "设置aioseo插件(old)成功\r\n";
        }else{
            echo $this->err.=$this->domain.">>>>设置aioseo插件(old)失败2\r\n";
            return false;
        }
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
        if(!$header){
            $header=array(
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: text/html,application/xhtml+xml,application/xml;'
            );
        }
        if($this->cookie){
            $header[]=$this->cookie;
        }
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
        if(!$header){
            $header=array(
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: text/html,application/xhtml+xml,application/xml;'
            );
        }
        if($this->cookie){
            $header[]=$this->cookie;
        }
        if(is_array($post_data)){
            $post_data=http_build_query($post_data,'','&');
        }
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
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






