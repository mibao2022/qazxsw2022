<?php
/**
 * 删除网站站点和对应数据库
 * 可以在本机或者服务器上运行
php /www/1111/sc删除站点和数据库.php

*/

//-----------------------------------------------
//-----------------------------------------------
//宝塔面板地址*
$cof_panel='http://1111.222.333.4444:35626/';
//宝塔API接口密钥*
$cof_key='4ILTWdpCNQbUKlNGmNbg7gCEZlG11111';


//待删除站点的域名，注意域名大小写
$cof_str='







';
//-----------------------------------------------
//-----------------------------------------------





$cof_panel=rtrim($cof_panel,'/');
$cof_key=trim($cof_key);
if(!preg_match('/[0-9a-zA-Z]{32}/',$cof_key)){
    exit("设置正确的宝塔API接口密钥\n");
}
$cof_str = trim($cof_str);
if(!$cof_str){
    exit("设置域名\n");
}

set_time_limit(0);

$bt=new BtApi($cof_panel,$cof_key);


$tmp_as = explode("\n", trim($cof_str));
$tmp_as = array_values(array_filter(array_map('trim',$tmp_as)));




//网站查询
$response = $bt->WebGetData('sites','',5000);
$result=json_decode($response,true);
$arr_site = array();
foreach ($result['data'] as $value) {
    $arr_site[$value['name']] = $value['id'];//取网站名称和网站id
}

//数据库查询，名称查询
$response = $bt->WebGetData('databases','',5000);
$result=json_decode($response,true);
$arr_db = array();
foreach ($result['data'] as $value) {
    $arr_db[$value['name']] = $value['id'];//取数据库名称和数据库id
}

$msg = '';
foreach ($tmp_as as $key=>$site) {
    //删除网站
    if( isset($arr_site[$site]) && $arr_site[$site]){
        $response = $bt->WebDeleteSite($arr_site[$site], $site, 0, 1);
        if(strpos($response,'status": true')){
            echo "{$site}----站点删除成功\n";
        }else{
            echo $tmp = "{$site}----站点删除失败！\n";
            $msg .= $tmp;
        }
    }
    
    //删除数据库 数据库名
    $n1 = str_replace(['.','-'],'_',$site);
    if($bt->del_mysql($n1)) continue;
    $n2 = substr(str_replace(['.','-'],'_',$site),0,16);
    if($bt->del_mysql($n2)) continue;
    
    $n3 = str_replace('.','_',$site);
    if($bt->del_mysql($n3)) continue;
    $n4 = substr(str_replace('.','_',$site),0,16);
    if($bt->del_mysql($n4)) continue;
    
    $n5 = $site;
    if($bt->del_mysql($n5)) continue;
    $n6 = substr($site,0,16);
    if($bt->del_mysql($n6)) continue;
    
    $n7 = strstr($site,'.',true);
    if($bt->del_mysql($n7)) continue;
    $n8 = substr(strstr($site,'.',true),0,16);
    if($bt->del_mysql($n8)) continue;
    
    
}

$bt->save_text($msg);


echo '完成';









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

    //获取网站、数据库信息
    function WebGetData($table='sites',$search='',$limit=50,$p=1,$type=-1){
        $url = $this->bt_panel.'/data?action=getData';
        $p_data = $this->GetKeyData();
        $p_data['table'] = $table;
        $p_data['limit'] = $limit;
        $p_data['p'] = $p;
        $p_data['search'] = $search;
        $p_data['type'] = $type;
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
        return $this->HttpPostCookie($url,$p_data);
    }

    //删除数据库
    public function WebDeleteDatabase($id,$name){
        $url = $this->bt_panel.'/database?action=DeleteDatabase';
        $p_data = $this->GetKeyData();
        $p_data['id'] = $id;//数据库ID
        $p_data['name'] = $name;//网站名称
        return $this->HttpPostCookie($url,$p_data);
    }


    //失败日志，自定义
    public function save_text($msg){
        return file_put_contents(__DIR__ . '/fail_site.txt', $msg, FILE_APPEND);
    }
    //删除数据库，$name 数据库名称，自定义
    public function del_mysql($name){
        global $arr_db;
        if( isset($arr_db[$name]) && $arr_db[$name]){
            $response = $this->WebDeleteDatabase($arr_db[$name], $name);
            if(strpos($response,'status": true')){
                echo "{$name}----数据库删除成功\n";
                return true;
            }
        }
        return false;
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










