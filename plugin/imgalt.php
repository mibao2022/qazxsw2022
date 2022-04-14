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
<div id="divMain">
  <div class="divHeader">ZBlogSEO工具</div>
  <div class="SubMenu">
  <?php ZBlogSEO_SubMenu(3);?>
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
			<td><p align="center">图片ALT</p></td>
			<td><p align="center"><input type="text" id="imgalt" name="Forum[imgalt]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->imgalt;?>"/></p></td>
			<td><p></p></td>
		</tr>
		<tr>
			<td>多张图时显示图片数量</td>
			<td>
				<input id='Number' name='Forum[Number]' type='text' value='<?php echo $zbp->Config('ZBlogSEO')->Number;?>'>
			</td>
			<td><p>留空不显示图片数量</p></td>
		</tr>
		<tr>
		   <td><p align="center">说明：</p></td>
		   <td><p>开启后自动给文章内的图片添加ALT，ALT属性为文章标题；</p><p>新的alt格式为：文章名 第X张</p><p>多张图末尾增加内容，%IMG%为自增加参数，设置为“第%IMG%张”，就会在末尾增加第1张、第2张、第3张</p></td>
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