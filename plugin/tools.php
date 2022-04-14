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
</style>
<div id="divMain">
	<div class="divHeader"><?php echo $blogtitle;?></div>
	<div class="SubMenu">
	   <?php ZBlogSEO_SubMenu(7);?>
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
		            <td><p align="center">是否开启全站新窗口打开</p></td>
		            <td><p><input type="text" id="zbp_blank" name="Forum[zbp_blank]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_blank;?>"/> 开启后网页全部在新窗口打开</p></td>
		            <td></td>
		        </tr>
		        <tr>
		            <td><p align="center">是否开启文章标题重复检测</p></td>
		            <td><p><input type="text" id="zbp_post_article_title" name="Forum[zbp_post_article_title]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_post_article_title;?>"/> 开启后可检测当前发布的文章标题是否已存在</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">是否开启文章自动别名</p></td>
		            <td><p><input type="text" id="zbp_post_bieming" name="Forum[zbp_post_bieming]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_post_bieming;?>"/> 开启后文章发布自动生成别名，别名为MD5</p></td>
		            <td></td>
		        </tr>
		        <tr>
		            <td><p align="center">是否开启评论管理增强</p></td>
		            <td><p><input type="text" id="zbp_Comment_Management" name="Forum[zbp_Comment_Management]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_Comment_Management;?>"/> 开启后评论管理显示邮箱；评论管理显示IP；评论可以编辑；</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">是否开启禁用右键查看</p></td>
		            <td><p><input type="text" id="zbp_copyright_you" name="Forum[zbp_copyright_you]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_copyright_you;?>"/> 开启后前台页面将禁止右键查看；</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">是否开启禁用F12查看</p></td>
		            <td><p><input type="text" id="zbp_copyright_f12" name="Forum[zbp_copyright_f12]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_copyright_f12;?>"/> 开启后前台页面将禁止F12查看,按F12自动关闭网页；</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">是否开启禁用F5刷新</p></td>
		            <td><p><input type="text" id="zbp_copyright_f5" name="Forum[zbp_copyright_f5]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_copyright_f5;?>"/> 开启后网站将禁用F5刷新，防止访客恶意按F5刷新页面；</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">是否开启禁用内容复制</p></td>
		            <td><p><input type="text" id="zbp_copyright_fuzhi" name="Forum[zbp_copyright_fuzhi]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_copyright_fuzhi;?>"/> 开启后网站页面文字不能被选中也不能被Ctrl+C复制；</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">是否开启禁用图片另存</p></td>
		            <td><p><input type="text" id="zbp_copyright_tu" name="Forum[zbp_copyright_tu]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_copyright_tu;?>"/> 开启后网站内的图片不能另存，也不能拖拽保存；</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">是否开启禁止网站被框架引用</p></td>
		            <td><p><input type="text" id="zbp_copyright_kuangjia" name="Forum[zbp_copyright_kuangjia]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_copyright_kuangjia;?>"/> 开启后网站不能被框架引用，防止网站被恶意引用；</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">是否开启全站变灰</p></td>
		            <td><p><input type="text" id="zbp_copyright_hui" name="Forum[zbp_copyright_hui]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_copyright_hui;?>"/> 开启后网站全站变灰；</p></td>
		            <td></td>
		        </tr>
				<tr>
		            <td><p align="center">是否开启开启ICO图标</p></td>
		            <td><p><input type="text" id="zbp_ico" name="Forum[zbp_ico]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_ico;?>"/> 开启后浏览器标题栏显示ICO图片；</p></td>
		            <td></td>
		        </tr>
				<tr>
                    <td><p align="center">ICO图标（48X48）</p></td>
	                <td><p>
						<input id="uplod_img5" class="uplod_img" type="text" size="56" name="Forum[zbp_ico_img]" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_ico_img;?>">
							<input type="button" class="upload_button" value="上传">
					</p></td>
					<td>
	                    <p align="center"><a href="<?php echo $zbp->Config('ZBlogSEO')->zbp_ico_img;?>" target="_blank"><img src="<?php echo $zbp->Config('ZBlogSEO')->zbp_ico_img;?>" height="48px"></a></p>
	                </td>
	            </tr>
				<tr>
		            <td><p align="center" style="color: #f90b0b;">停用删表</p></td>
		            <td><p style="color: #f90b0b;"><input  type="text" id="zbp_zblog_seo" name="Forum[zbp_zblog_seo]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_zblog_seo;?>"/> 请慎重：选中后停用插件将删除本插件的设置数据！</p></td>
		            <td></td>
		        </tr>
		        <tr>
		           <td><p align="center">说明：</p></td>
				   <td><p style="color: #f90b0b;">还需要什么功能是插件里没有的，可以反馈给作者哦！</p></td>
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
if ($zbp->CheckPlugin('UEditor')) {	      	     
	echo '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/UEditor/ueditor.config.php"></script>';    	 			  	
	echo '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/UEditor/ueditor.all.min.js"></script>';    			 	 	 
	echo '<script type="text/javascript" src="'.$zbp->host.'zb_users/plugin/ZBlogSEO/plugin/js/lib.upload.js"></script>';    	 	 		 	
}    				  		
require $blogpath . 'zb_system/admin/admin_footer.php';    	 	 		 	
RunTime();     		  	  
?>