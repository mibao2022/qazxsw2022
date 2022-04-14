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
  <?php ZBlogSEO_SubMenu(4);?>
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
			<td><p align="center">是否开启标签内链</p></td>
			<td><p align="center"><input type="text" id="taglink" name="Forum[taglink]" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->taglink;?>"/></p></td>
			<td><p>自动为文章添加已有的标签链接；</p></td>
		</tr>
		<tr>
			<td><p align="center">关键词重复</p></td>
			<td><p><input style="width: 100%;" type="text" id="tag_shu" name="Forum[tag_shu]" value="<?php echo $zbp->Config('ZBlogSEO')->tag_shu;?>"/> </p></td>
			<td>单篇文章重复关键词替换次数，默认一次</td>
		</tr>
		<tr>
			<td><p align="center">最大链接数</p></td>
			<td><p><input style="width: 100%;" type="text" id="tag_num_shu" name="Forum[tag_num_shu]" value="<?php echo $zbp->Config('ZBlogSEO')->tag_num_shu;?>"/> </p></td>
			<td>单篇文章内容允许最大链接数</td>
		</tr>
		<tr>
			<td><p align="center">标签颜色</p></td>
			<td><p><input style="width: 100%;" type="text" id="tag_color" name="Forum[tag_color]" value="<?php echo $zbp->Config('ZBlogSEO')->tag_color;?>"/> </p></td>
			<td>自定义标签颜色（例如：#E91E63）留空则显示主题自带的A标签颜色</td>
		</tr>
		<tr>
		   <td><p align="center">说明：</p></td>
		   <td><p></p></td>
		   <td></td>
		</tr>
	</table>
	
	<input name="" type="Submit" class="button" style="margin-top:10px;width:99%;padding:0 auto;" value="保存"/>
</form>
<?php
		$str = '<form action="save.php?type=add" method="post">
				<table width="100%" border="1" class="tableBorder">
				<tr>
					<th scope="col" width="5%" height="32" nowrap="nowrap">序号</th>
					<th scope="col" width="25%">关键字</th>
					<th scope="col" width="50%">链接</th>
					<th scope="col" width="10%">替换</th>
					<th scope="col" width="10%">操作</th>
				</tr>';
		$str .= '<tr>';    	  	 		 
		$str .= '<td align="center">0</td>';       	  	 
		$str .= '<td><input type="text" class="sedit" name="title" value=""></td>';    	 		 	 	
		$str .= '<td><input type="text" style="width:500px;"  class="sedit" name="url" value=""></td>';    					  	
		$str .= '<td><input type="text" class="checkbox" name="IsUsed" value="1" /></td>';
		$str .= '<td><input type="hidden" name="editid" value="">
						<input name="add" type="submit" class="button" value="增加"/></td>';
		$str .= '</tr>';        	   
		$str .= '</form>';    	      	
		$where = array(array('=','KeyWord_Type','0'));     	 				 
		$order = array('KeyWord_IsUsed'=>'DESC','KeyWord_Order'=>'ASC');    	  	 			
		$sql= $zbp->db->sql->Select($zbp->table['ZBlogSEOTag'],'*',$where,$order,null,null);    	  		 		
		$array=$zbp->GetListCustom($zbp->table['ZBlogSEOTag'],$zbp->datainfo['ZBlogSEOTag'],$sql);    			 	   
		$i =1;    	 	 		  
		foreach ($array as $key => $reg) {    	  					
			$str .= '<form action="save.php?type=add" method="post" name="keyword">';    	 		 	 	
			$str .= '<tr>';       	  	 
			$str .= '<td align="center">'.$i.'</td>';     	     	
			$str .= '<td><input type="text" class="sedit" name="title" value="'.$reg->Title.'" ></td>';     	 	 	  
			$str .= '<td><input type="text" style="width:500px;"  class="sedit" name="url" value="'.$reg->Url.'" ></td>';      	 			 
			$str .= '<td><input type="text" class="checkbox" name="IsUsed" value="'.$reg->IsUsed.'" /></td>';
			$str .= '<td nowrap="nowrap">
						<input type="hidden" name="editid" value="'.$reg->ID.'">
						<input name="edit" type="submit" class="button" value="修改"/>
						<input name="del" type="button" class="button" value="删除" onclick="if(confirm(\'您确定要进行删除操作吗？\')){location.href=\'save.php?type=del&id='.$reg->ID.'\'}"/>
					</td>';
			$str .= '</tr>';    	   	 	 
			$str .= '</form>';     			  	 
			$i++;     		     
		}    	 				  
		$str .='</table>';     		 			 
		echo $str;        	   
?>
	<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
		<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBlogSEO/logo.png';?>");</script>	
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';        		 	
RunTime();     	 	  		
?>