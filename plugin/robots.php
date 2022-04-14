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
$file = $zbp->path . 'robots.txt';    	 	 	  	
if(GetVars('zbp_robots','POST')){       		         	 	 		
	@file_put_contents($zbp->path . 'robots.txt',$_POST["zbp_robots"]);     	  	        	 	 	 	
	if (@file_get_contents(ZBP_PATH.'robots.txt')){    		 	           			  
		$zbp->SaveConfig('ZBlogSEO');    	  	 	 	
		$zbp->ShowHint('good');  				  		    	    	 	
	}       	 			     	 	  		
}      				        	    	
if(GetVars('del_robots','GET')){      	 	  	      	  			
	@unlink($zbp->path . 'robots.txt');     	  	 		     			  		
	$zbp->SaveConfig('ZBlogSEO');    								
	$zbp->ShowHint('good');   	    	     		 		   
}           	     	
    	  					
?>
<style>
.zzwsrk{width: 100%;font-size: 15px;height: 200px;min-height: 40px;margin: 0;padding: 8px 8px;color: #333;background-color: #fff;border: 1px solid #d7d7d7;box-sizing: border-box;vertical-align: middle;}
</style>
<div id="divMain">
  <div class="divHeader">ZBlogSEO工具</div>
  <div class="SubMenu">
  <?php ZBlogSEO_SubMenu(8);?>
  </div>
  <div id="divMain2">
	<form id="form2" name="form2" method="post" enctype="multipart/form-data" action="robots.php">	
	<table width="100%" style='padding:0;margin-top:5px;' cellspacing='0' cellpadding='0' class="tableBorder">
		<tr>
			<th><p align="center">项目名称</p></th>
			<th><p align="center">当前内容</p></th>
			<th><p align="center">推荐写法</p></th>
		</tr>
		<tr>
			<td><label for="zbp_robots"><p align="center">当前Robots.txt</p></label></td>
			<td><p align="left"><textarea class="zzwsrk" name="zbp_robots" type="text" id="zbp_robots"><?php if(is_file($file)){$paths = readfile($file);} else {$paths = '';}; ?></textarea></p></td>
			<td style="width: 30%;">	
				<p>User-agent: *</p>
				<p>Disallow: /zb_users/</p>
				<p>Disallow: /zb_system/</p>
				<p>Sitemap: <?php echo $bloghost;?>sitemap.xml</p>
			</td>
		</tr>
		<tr>
		   <td><p align="center">说明：</p></td>
		   <td>
				<p>1.Robots.txt 是存放在站点根目录下的一个纯文本文件。虽然它的设置很简单，但是作用却很强大。<br>它可以指定搜索引擎蜘蛛只抓取指定的内容，或者是禁止搜索引擎蜘蛛抓取网站的部分或全部内容。</p>
				<p>2.[User-agent:]用于描述搜索引擎蜘蛛的名字，在" Robots.txt "文件中，如果有多条User-agent记录<br>说明有多个搜索引擎蜘蛛会受到该协议的限制，对该文件来说，至少要有一条User-agent记录。如果该项<br>的值设为*，则该协议对任何搜索引擎蜘蛛均有效，在" Robots.txt "文件中，"User-agent:*"这样的记录只能有一条。</p>
				<p>3.[Disallow:]用于描述不希望被访问到的一个URL，这个URL可以是一条完整的路径，也可以是部分的，任何<br>以Disallow开头的URL均不会被Robot访问到。</p>
				<p></p>
				<p></p>
				<p></p>
			</td>
		   <td></td>
		</tr>
	</table>
	<input type="submit" value="更新Robots" />
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="button" onclick="window.location.href='?del_robots=true'" value="移除Robots" />
</form>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
		<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBlogSEO/logo.png';?>");</script>	
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';    				 			
RunTime();    	    	  
?>