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
  <?php ZBlogSEO_SubMenu(2);?>
  </div>
  <div id="divMain2">
	<form id="form2" name="form2" method="post">	
	<table width="100%" style='padding:0;margin-top:5px;' cellspacing='0' cellpadding='0' class="tableBorder">
		<tr>
			<th><p align="center">项目名称</p></th>
			<th><p align="center">功能代码</p></th>
			<th><p align="center">备注说明</p></th>
		</tr>
		<tr>
			<td><p align="center">是否开启文章外链转内链</p></td>
			<td><p align="center"><input type="text" id="article_url" name="Forum[article_url]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->article_url;?>"/></p></td>
			<td></td>
		</tr>
		<tr>
			<td><p align="center">文章内链密钥</p></td>
			<td><p align="center"> <input name="Forum[articlelink_key]" style="width:98%;" type="text" value="<?php echo $zbp->Config('ZBlogSEO')->articlelink_key; ?>" /></p></td>
			<td>密钥可以用来加密链接（密钥填自定义的字母），可以防止其他别有用心的人通过你的网站内链跳转做坏事。</td>
		</tr>
		<tr>
			<td><p align="center">连接加密方式</p></td>
			<td><p align="center"><input type="text" id="links_base64" name="Forum[links_base64]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->links_base64;?>"/></p></td>
			<td>OFF为秘钥加密，ON为base64加密。</td>
		</tr>
		<tr>
			<td><label for="discharge"><p align="center">不转内链的链接</p></label></td>
			<td><p align="left"><textarea class="zzwsrk" name="Forum[discharge]" type="text" id="discharge"><?php echo $zbp->Config('ZBlogSEO')->discharge;?></textarea></p></td>
			<td><p>输入文章中不需要转内链的链接地址，多个链接请用|分隔。</p></td>
		</tr>
		<tr>
			<td><p align="center">是否开启评论外链转内链</p></td>
			<td><p align="center"><input type="text" id="commentlink_url" name="Forum[commentlink_url]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->commentlink_url;?>"/></p></td>
			<td></td>
		</tr>
		<tr>
			<td><p align="center">评论内链密钥</p></td>
			<td><p align="center"> <input name="Forum[commentlink_key]" style="width:98%;" type="text" value="<?php echo $zbp->Config('ZBlogSEO')->commentlink_key; ?>" /></p></td>
			<td>密钥可以用来加密链接（密钥填自定义的字母），可以防止其他别有用心的人通过你的网站内链跳转做坏事。</td>
		</tr>
		<tr>
		   <td><p align="center">说明：</p></td>
		   <td><p>1.使用外链转内链功能，网站必须是伪静态；</p><p>2.IIS环境需要支持index.php；</p><p></p></td>
		   <td></td>
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