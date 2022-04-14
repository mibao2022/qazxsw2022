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
	   <?php ZBlogSEO_SubMenu(9);?>
    </div>
	<div id="divMain2">
		<form id="form2" name="form2" method="post">
		<table width="100%" style="padding:0;margin:0;" cellspacing="0" cellpadding="0" class="tableBorder">
			<tr>
				<th width="30%"><p align="center">项目名称</p></th>
				<th width="50%"><p align="center">文本/代码</p></th>
				<th width="20%"><p align="center">说明</p></th>
			</tr>
			<tr>
				<td><p align="center">是否开启文章浏览量自定义功能</p></td>
				<td><p><input type="text" id="zbp_views" name="Forum[zbp_views]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->zbp_views;?>"/> 开启后可以自定义文章浏览量，用户点击增加指定浏览量</p></td>
				<td></td>
			</tr>
		 </table>
		<table width="100%" style='padding:0;margin:0;' cellspacing='0' cellpadding='0' class="tableBorder">
		<tr>
			<th width="15%"><p align="center">配置名称</p></th>
			<th width="25%"><p align="center">配置内容</p></th>
			<th width="15%"><p align="center">配置名称</p></th>
			<th width="25%"><p align="center">配置内容</p></th>
			<th width="20%"><p align="center">配置开关</p></th>
		</tr>
		<tr>
			<td>
				<label for="ViewNumsStart"><p align="center">用户浏览开始</p></label>
			</td>
			<td>
				<p align="left"><textarea name="Forum[ViewNumsStart]" type="text" id="ViewNumsStart" style="width:98%;height:25px;"><?php echo $zbp->Config('ZBlogSEO')->ViewNumsStart;?></textarea></p>
			</td>
			<td>
				<label for="ViewNumsEnd"><p align="center">用户浏览结束</p></label>
			</td>
			<td>
				<p align="left"><textarea name="Forum[ViewNumsEnd]" type="text" id="ViewNumsEnd" style="width:98%;height:25px;"><?php echo $zbp->Config('ZBlogSEO')->ViewNumsEnd;?></textarea></p>
			</td>
			<td>
				<p align="center"><input type="text" id="ViewNumsOnOff" name="Forum[ViewNumsOnOff]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->ViewNumsOnOff;?>"/></p>
			</td>
		</tr>
		<tr>
			<td>
				<label for="SaveStart"><p align="center">文章保存浏览开始</p></label>
			</td>
			<td>
				<p align="left"><textarea name="Forum[SaveStart]" type="text" id="SaveStart" style="width:98%;height:25px;"><?php echo $zbp->Config('ZBlogSEO')->SaveStart;?></textarea></p>
			</td>
			<td>
				<label for="SaveEnd"><p align="center">文章保存浏览结束</p></label>
			</td>
			<td>
				<p align="left"><textarea name="Forum[SaveEnd]" type="text" id="SaveEnd" style="width:98%;height:25px;"><?php echo $zbp->Config('ZBlogSEO')->SaveEnd;?></textarea></p>
			</td>
			<td>
				<p align="center"><input type="text" id="SaveOnOff" name="Forum[SaveOnOff]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->SaveOnOff;?>"/></p>
			</td>
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