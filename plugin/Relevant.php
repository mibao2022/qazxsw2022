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
?>
<div id="divMain">
	<div class="divHeader"><?php echo $blogtitle;?></div>
	<div class="SubMenu">
	   <?php ZBlogSEO_SubMenu(10);?>
    </div>
	<div id="divMain2">
		<form id="form2" name="form2" method="post">
			<table width="100%" style="padding:0;margin:0;" cellspacing="0" cellpadding="0" class="tableBorder">
		        <tr>
			        <th><p>插件说明</p></th>
			    </tr>
		        <tr>
		            <td><p>1.标题SEO：支持自定义首页标题、关键词、描述；文章、单页、分类、标签等支持自定义/自动获取标题、描述、关键词等；</p></td>
		         </tr>
				<tr>
		            <td><p>2.Sitemap：支持自动生成XML、Html、TXT网站地图；可设置XML首页、分类、文章、标签等更新频率，建议生成数量不超过5000</p></td>
		         </tr>
				 <tr>
		            <td><p>3.外链转内链：【文章外链转内链】可将文章中的外链自动转换成内链，可设置排除域名；【评论外链转内链】可将评论外链自动转换成内链跳转；</p><p>外链转内链功能可设置秘钥加密，有效的防止其他别有用心的人通过你的网站内链跳转做坏事。</p><p>插件会自动提取外链并进行转换，不需要进行额外操作。插件不会修改任何ZBlog数据，这很好地保护了你的数据安全。任何情况下删除该插件均不会留下痕迹。</p></td>
		         </tr>
				 <tr>
		            <td><p>4.图片ALT：自动给文章内的图片添加ALT，ALT属性为文章标题；因为UE编辑器上传的图片自动显示文件名为Title，所以插件自动给图片添加了文章标题为新的Title标题。</p></td>
		         </tr>
				 <tr>
		            <td><p>5.标签内链：文章自动添加标签链接变为内链，可设置文章关键词重复次数，单篇文章内容允许最大链接数。</p></td>
		         </tr>
				 <tr>
		            <td><p>6.百度推送：文章实时推送至百度；JS版百度自动推送；</p></td>
		         </tr>
				 <tr>
		            <td><p>7.蜘蛛统计：无错支持ZBlog最新版，具有蜘蛛爬行榜单、来访排行榜、错误链接记录、最近来访蜘蛛等功能；</p></td>
		         </tr>
				 <tr>
		            <td><p>8.辅助工具：【可设置全站新窗口打开】【文章标题重复检测】【文章自动别名】【评论管理增强--评论管理显示邮箱；评论管理显示IP；评论可以编辑；】</p></td>
		         </tr>
				 <tr>
		            <td><p>9.Robots：在线查看并设置Robots.txt</p></td>
		         </tr>
				 <tr>
			        <th><p>售后说明</p></th>
			    </tr>
				<tr>
		            <td>
						<p>如果你在使用的过程中有什么问题</p>
						<p>添加作者QQ：744355891 （注：如遇QQ不在线，请耐心等待，上线后烽烟会第一时间回复给您的）</p>
					</td>
		        </tr>
				<tr>
			        <th><p>烽烟工作室</p></th>
			    </tr>
				<tr>
		            <td>
						<p>承接ZBlog模板定制、ZBlog仿站、ZBlog模板修改、ZBlog插件定制等业务。</p>
						<p>建站技术交流群：99464245</p>
					</td>
		        </tr>
		     </table>
	        <br />
	       
        </form>
		<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
		<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBlogSEO/logo.png';?>");</script>	
	</div>
</div>
<?php
     	 	   	
require $blogpath . 'zb_system/admin/admin_footer.php';       	    
RunTime();     			 		 
?>