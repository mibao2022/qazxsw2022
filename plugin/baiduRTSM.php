<?php /* EL PSY CONGROO */     	  	 	 
require '../../../../zb_system/function/c_system_base.php';     		   		
require '../../../../zb_system/function/c_system_admin.php';    		 		 		
$zbp->Load();     	  			 
$action='root';     	 	 	 	
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}    	     		
if (!$zbp->CheckPlugin('ZBlogSEO')) {$zbp->ShowError(48);die();}     	  		 	
    	 			   
$blogtitle='ZBlogSEO';     	    	 
require $blogpath . 'zb_system/admin/admin_header.php';     		  	 	
require $blogpath . 'zb_system/admin/admin_top.php';        		  
?>    		  			 
<?php     	    		
if(isset($_POST['Forum'])){    	 			 	 
	foreach($_POST['Forum'] as $key=>$val){    	 	 	  	
	   $zbp->Config('ZBlogSEO')->$key = $val;    		  		 	
	}    	   			 
	$zbp->SaveConfig('ZBlogSEO');        				
	$zbp->ShowHint('good');       		 		
}     			   	
    		      
?>
<style>
.zzwsrk{width: 100%;font-size: 15px;height: 60px;min-height: 40px;margin: 0;padding: 8px 8px;color: #333;background-color: #fff;border: 1px solid #d7d7d7;box-sizing: border-box;vertical-align: middle;}
</style>
<div id="divMain">
  <div class="divHeader">ZBlogSEO工具</div>
  <div class="SubMenu">
  <?php ZBlogSEO_SubMenu(5);?>
  </div>
  <div id="divMain2">
	<form id="form2" name="form2" method="post">	
	<table width="100%" style='padding:0;margin-top:5px;' cellspacing='0' cellpadding='0' class="tableBorder">
		<tr>
			<th width="20%"><p align="center">项目名称</p></th>
			<th width="50%"><p align="center">功能代码</p></th>
			<th width="30%"><p align="center">备注说明</p></th>
		</tr>
		<tr>
			<td width="20%" height="50px" align="center">网站当前域名</td>
			<td width="40%" align="center"><?php echo $zbp->host; ?></td>
		</tr>
		<tr>
			<td><p align="center">是否开启百度快速收录</p></td>
			<td><p align="center"><input type="text" id="baidks" name="Forum[baidks]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->baidks;?>"/></p></td>
			<td><p>开启后将推送快速收录；</p></td>
		</tr>
		<tr>
			<td><label for="baidu_ksapi"><p align="center">快速收录接口</p></label></td>
			<td><p align="left"><textarea class="zzwsrk" name="Forum[baidu_ksapi]" type="text" id="baidu_ksapi"><?php echo $zbp->Config('ZBlogSEO')->baidu_ksapi;?></textarea></p></td>
			<td><p>
        
      </p></td>
		</tr>
		<tr>
		   <td><p align="center">说明：</p></td>
		   <td><p>1.百度接口地址如下：<img style="width: 700px;" src="<?php echo $bloghost . 'zb_users/plugin/ZBlogSEO/baiduRTSM/110655.jpg';?>" /></p></td>
		   <td></td>
		</tr>
		
		
		<tr>
			<td><p align="center">是否开启百度主动推送</p></td>
			<td><p align="center"><input type="text" id="baidurtsm" name="Forum[baidurtsm]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->baidurtsm;?>"/></p></td>
			<td><p>开启后将文章实时推送至百度；</p></td>
		</tr>
		<tr>
			<td><label for="baidu_data"><p align="center">设置百度接口地址</p></label></td>
			<td><p align="left"><textarea class="zzwsrk" name="Forum[baidu_data]" type="text" id="baidu_data"><?php echo $zbp->Config('ZBlogSEO')->baidu_data;?></textarea></p></td>
			<td><p>
        你需要访问
        <a href="http://zhanzhang.baidu.com/linksubmit/index" target="_blank">http://zhanzhang.baidu.com/linksubmit/index</a>
        获取地址，复制如下图所示区域到插件内。
      </p></td>
		</tr>
		<tr>
		   <td><p align="center">说明：</p></td>
		   <td><p>1.百度接口地址如下：<img src="<?php echo $bloghost . 'zb_users/plugin/ZBlogSEO/baiduRTSM/download.png';?>" /></p></td>
		   <td></td>
		</tr>
		<tr>
			<td><p align="center">是否开启JS版百度自动推送</p></td>
			<td><p align="center"><input type="text" id="baidurtsm_js" name="Forum[baidurtsm_js]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->baidurtsm_js;?>"/></p></td>
			<td><p></p></td>
		</tr>
		
	</table>
	
	<input name="" type="Submit" class="button" style="margin-top:10px;width:99%;padding:0 auto;" value="保存"/>
</form>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
		<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBlogSEO/logo.png';?>");</script>	
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';     	 		 		
RunTime();    					  	
?>