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
p{overflow:hidden}
p span{width:20%;display:inline-block;float:left;font-size:11pt}
p select{margin-left:50px}
input[type=checkbox]{border:1px solid #b4b9be;background:#fff;color:#555;clear:none;cursor:pointer;display:inline-block;line-height:0;height:16px;outline:0;padding:6px 0 0 0;text-align:center;vertical-align:middle;width:16px;min-width:16px}
input[type=radio]{border:1px solid #b4b9be;background:#fff;color:#555;clear:none;cursor:pointer;display:inline-block;border-radius:50%;margin-right:4px;line-height:10px;height:16px;margin:-4px 4px 0 0;outline:0;padding:0!important;text-align:center;vertical-align:middle;width:16px;min-width:16px;-webkit-border-radius:50%;-webkit-appearance:none;-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1);-webkit-transition:.05s border-color ease-in-out;transition:.05s border-color ease-in-out}
input[type=radio]:checked:before{float:left;content:'\2022';text-indent:-9999px;-webkit-border-radius:50px;border-radius:50px;font-size:24px;width:6px;height:6px;margin:4px;line-height:16px;background-color:#1e8cbe}
label{width: 100%;float: left;margin: 5px 0;}
</style>
<div id="divMain">
  <div class="divHeader">ZBlogSEO工具</div>
  <div class="SubMenu">
  <?php ZBlogSEO_SubMenu(11);?>
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
			<td><p align="center">是否开启404页面样式</p></td>
			<td><p align="center"><input type="text" id="ym404" name="Forum[ym404]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->ym404;?>"/></p></td>
			<td><p>开启后网站404页面则显示插件带的样式。</p></td>
		</tr>
		<tr>
		   <td><p align="center">页面样式</p></td>
		   <td>
				<p>
					<label>
						<input type="radio" id="ym404yx" name="Forum[ym404yx]" value="one" <?php if($zbp->Config('ZBlogSEO')->ym404yx == 'one') echo 'checked'?> />	404样式一
					</label>
					<label>
						<input type="radio" id="ym404yx" name="Forum[ym404yx]" value="two" <?php if($zbp->Config('ZBlogSEO')->ym404yx == 'two') echo 'checked'?> /> 404样式二
					</label>
					<label>
						<input type="radio" id="ym404yx" name="Forum[ym404yx]" value="three" <?php if($zbp->Config('ZBlogSEO')->ym404yx == 'three') echo 'checked'?> /> 404样式三
					</label>
				</p>
		   </td>
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