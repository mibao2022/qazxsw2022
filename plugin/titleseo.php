<?php /* EL PSY CONGROO */    	  		 	 
require '../../../../zb_system/function/c_system_base.php';      		 		 
require '../../../../zb_system/function/c_system_admin.php';     		    	
$zbp->Load();     				  	
$action='root';    	    	  
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}      		 	  
if (!$zbp->CheckPlugin('ZBlogSEO')) {$zbp->ShowError(48);die();}     		    	
$blogtitle='ZBlogSEO工具';      	  	  
require $blogpath . 'zb_system/admin/admin_header.php';      	 		 	
require $blogpath . 'zb_system/admin/admin_top.php';       		 		
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
.zwsrk{width: 100%;font-size: 15px;height: 150px;min-height: 40px;margin: 0;margin-top: 10px;padding: 8px 8px;color: #333;background-color: #fff;border: 1px solid #d7d7d7;box-sizing: border-box;vertical-align: middle;}
input[type=radio]{border:1px solid #b4b9be;background:#fff;color:#555;clear:none;cursor:pointer;display:inline-block;border-radius:50%;margin-right:4px;line-height:10px;height:16px;margin:-4px 4px 0 0;outline:0;padding:0!important;text-align:center;vertical-align:middle;width:16px;min-width:16px;-webkit-border-radius:50%;-webkit-appearance:none;-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1);-webkit-transition:.05s border-color ease-in-out;transition:.05s border-color ease-in-out}
input[type=radio]:checked:before{float:left;content:'\2022';text-indent:-9999px;-webkit-border-radius:50px;border-radius:50px;font-size:24px;width:6px;height:6px;margin:4px;line-height:16px;background-color:#1e8cbe}
</style>
<div id="divMain">
	<div class="divHeader"><?php echo $blogtitle;?></div>
	<div class="SubMenu">
	   <?php ZBlogSEO_SubMenu(0);?>
    </div>
	<div id="divMain2">
		<form id="form2" name="form2" method="post">
			<table width="100%" style="padding:0;margin:0;" cellspacing="0" cellpadding="0" class="tableBorder">
		        <tr>
			        <th width="15%"><p align="center">项目名称</p></th>
			        <th width="50%"><p align="center">文本/代码</p></th>
			        <th width="25%"><p align="center">说明</p></th>
		        </tr>
		        <tr>
		            <td><p align="center">是否开启SEO设置</p></td>
		            <td><p><input type="text" id="zbp_seo" name="Forum[zbp_seo]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_seo;?>"/> 如果不使用该功能，可在这里关闭插件的SEO设置</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">标题和博客名称之间的分隔符</p></td>
		            <td><p><input style="width: 100%;" type="text" id="separator" name="Forum[separator]" value="<?php echo $zbp->Config('ZBlogSEO')->separator;?>"/> </p></td>
		            <td>请设置标题与博客名称之前的分隔符，你可以使用  | , _ , - , > 等，默认是 |</td>
		        </tr>
		        <tr>
		            <td><p align="center">首页标题</p></td>
		            <td><p><input style="width: 100%;" type="text" id="homepage_title" name="Forum[homepage_title]" value="<?php echo $zbp->Config('ZBlogSEO')->homepage_title;?>"/> </p></td>
		            <td><p align="center">自定义首页标题</p></td>
		        </tr>
		        <tr>
			        <td><label for="homepage_keywords"><p align="center">首页关键词</p></label></td>
			        <td><p align="left"><textarea class="zzwsrk" name="Forum[homepage_keywords]" type="text" id="homepage_keywords"><?php echo $zbp->Config('ZBlogSEO')->homepage_keywords;?></textarea></p></td>
			        <td><p align="center">填写首页关键词，多个关键词请用英文逗号,隔开。</p></td>
		        </tr>
		        <tr>
			        <td><label for="homepage_description"><p align="center">首页描述</p></label></td>
			        <td><p align="left"><textarea class="zzwsrk" name="Forum[homepage_description]" type="text" id="homepage_description"><?php echo $zbp->Config('ZBlogSEO')->homepage_description;?></textarea></p></td>
			        <td><p align="center">填写首页描述</p></td>
		        </tr>
				
				
		        <tr>
		           <td><p align="center">说明：</p></td>
				   <td><p>设置完成请点击下后台首页的[清空缓存并重新编译模板]按钮；</p><p>关闭或者开启SEO标题都需要点击下后台首页的[清空缓存并重新编译模板]按钮；</p><p>烽烟工作室付费主题用户可关闭此功能，主题自带SEO增强设置。</p></td>
				   <td></td>
		        </tr>
		     </table>
	        <br />
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