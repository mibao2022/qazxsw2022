<?php /* EL PSY CONGROO */   		   			     		   	 
require '../../../../zb_system/function/c_system_base.php';     	  				       	 		 
require '../../../../zb_system/function/c_system_admin.php';     	    	          	  
$zbp->Load();    		 	  		      	    	
$action='root';      				 	    	 			 	 
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}     	  	 	     		 		   
if (!$zbp->CheckPlugin('ZBlogSEO')) {$zbp->ShowError(48);die();}    	  		 		    	 				 	
$blogtitle='ZBlogSEO工具'; 			  		         		  
if(count($_POST)>0){    	 	  	 	    		   		 
	$zbp->Config('ZBlogSEO')->XML_FileName = $_POST['XML_FileName'];    			  			
	$zbp->Config('ZBlogSEO')->XML_ON = $_POST['XML_ON'];	     	 		   
	$zbp->Config('ZBlogSEO')->home_priority = $_POST['home_priority'];	    			   		    	 		 			
	$zbp->Config('ZBlogSEO')->home_frequency = $_POST['home_frequency'];          	       	   		
	$zbp->Config('ZBlogSEO')->category_priority = $_POST['category_priority'];	     		  		      					  
	$zbp->Config('ZBlogSEO')->category_frequency = $_POST['category_frequency'];		    	 					      	     	
	$zbp->Config('ZBlogSEO')->post_priority = $_POST['post_priority'];	          	       	    	
	$zbp->Config('ZBlogSEO')->post_frequency = $_POST['post_frequency'];	     		 		 	     	 			 	
	$zbp->Config('ZBlogSEO')->page_priority = $_POST['page_priority'];	        				      	 			 
	$zbp->Config('ZBlogSEO')->page_frequency = $_POST['page_frequency'];	    		  		 	    	    	  
	$zbp->Config('ZBlogSEO')->tag_priority = $_POST['tag_priority'];	    	  		  	    	     		
	$zbp->Config('ZBlogSEO')->tag_frequency = $_POST['tag_frequency'];	     	 			 	      					 
	$zbp->Config('ZBlogSEO')->sitemap_number = $_POST['sitemap_number'];     				       		 		   
	if(GetVars('Enabled_XML_Sitemap')){    				 	 	    	 		 	  
		$zbp->Config('ZBlogSEO')->Enabled_XML_Sitemap = $_POST['Enabled_XML_Sitemap'];     	  	         	  	 	
	}else{      	   		    					 		
		$zbp->Config('ZBlogSEO')->Enabled_XML_Sitemap = '';     		 			       	 	 		
	}      				       			  	 
	if(GetVars('Enabled_HTML_Sitemap')){    	     	       						
		$zbp->Config('ZBlogSEO')->Enabled_HTML_Sitemap = $_POST['Enabled_HTML_Sitemap'];     			 	 	     		  	  
	}else{    	  		  	      	  	  
		$zbp->Config('ZBlogSEO')->Enabled_HTML_Sitemap = '';    		 		         		 			
	}    		  				    				  		
	if(GetVars('Enabled_TXT_Sitemap')){    	 			 	     				  	 
		$zbp->Config('ZBlogSEO')->Enabled_TXT_Sitemap = $_POST['Enabled_TXT_Sitemap'];    								       		 	 
	}else{    			 	        					 	
		$zbp->Config('ZBlogSEO')->Enabled_TXT_Sitemap = '';    		 				      		 		 	
	}      	  	 	    	 	 	   
	      	  			     	 	 			
	if(GetVars('page_select')){    	 						      		   	
		$zbp->Config('ZBlogSEO')->page_select = $_POST['page_select'];    		 	 			    		  	 	 
	}else{    	    	 	      		 	  
		$zbp->Config('ZBlogSEO')->page_select = '';    	 	  	      			 				
	}     		 	  	    	  	  		
	if(GetVars('category_select')){     	 			 	     		 		  
		$zbp->Config('ZBlogSEO')->category_select = $_POST['category_select'];     		  		      	   	  
	}else{     	  	 		     	  				
		$zbp->Config('ZBlogSEO')->category_select = '';      		 	 	      	   	 
	}    	 	 	 		    	 		   	
	if(GetVars('post_select')){       	 	 	      			   
		$zbp->Config('ZBlogSEO')->post_select = $_POST['post_select'];     	   			    		  	  	
	}else{     	  		       	  		  
		$zbp->Config('ZBlogSEO')->post_select = '';     		  		      		     
	}    	  			 	    	       
	if(GetVars('tag_select')){    		 	  	     	  	 			
		$zbp->Config('ZBlogSEO')->tag_select = $_POST['tag_select'];      	 		       	 	 			
	}else{         			       	  	 
		$zbp->Config('ZBlogSEO')->tag_select = '';    		 			      	 			  	
	}    	  	 		       		 	  
	$zbp->SetHint('good');     			   	    	 	  		 
	$zbp->SaveConfig('ZBlogSEO');    		     	    	 	     
}    	 	 		      					 		
    			    	       	 			
require $blogpath . 'zb_system/admin/admin_header.php';    		 	 		     	   	  	
require $blogpath . 'zb_system/admin/admin_top.php';    		 			 	     		 		  
     	 		 		      	  	 	
    	  					    		 	  		
?>
<style>
p{overflow:hidden}
p span{width:20%;display:inline-block;float:left;font-size:11pt}
p select{margin-left:50px}
input[type=checkbox]{border:1px solid #b4b9be;background:#fff;color:#555;clear:none;cursor:pointer;display:inline-block;line-height:0;height:16px;outline:0;padding:6px 0 0 0;text-align:center;vertical-align:middle;width:16px;min-width:16px}
input[type=radio]{border:1px solid #b4b9be;background:#fff;color:#555;clear:none;cursor:pointer;display:inline-block;border-radius:50%;margin-right:4px;line-height:10px;height:16px;margin:-4px 4px 0 0;outline:0;padding:0!important;text-align:center;vertical-align:middle;width:16px;min-width:16px;-webkit-border-radius:50%;-webkit-appearance:none;-webkit-box-shadow:inset 0 1px 2px rgba(0,0,0,.1);box-shadow:inset 0 1px 2px rgba(0,0,0,.1);-webkit-transition:.05s border-color ease-in-out;transition:.05s border-color ease-in-out}
input[type=radio]:checked:before{float:left;content:'\2022';text-indent:-9999px;-webkit-border-radius:50px;border-radius:50px;font-size:24px;width:6px;height:6px;margin:4px;line-height:16px;background-color:#1e8cbe}
</style>	
<div id="divMain">
    <div class="divHeader"><?php echo $blogtitle;?></div>
    <div class="SubMenu">
	<?php ZBlogSEO_SubMenu(1);?>
    </div>
    <div id="divMain2">
	    <form id="form3" name="form3" method="post">
	        <input id="reset" name="reset" type="hidden" value="" />
            <table border="1" class="tableFull tableBorder tableBorder-thcenter">
				<tr>
					<th class="td20"></th>
					<th>SitemapXML内容组成</th>
				</tr>
                <tr>
					<td>选项</td>
					<td>
						<p><span>地图开关</span>
						<label style="display:block; float: left;padding-top:4px;">
							<input type="radio" id="XML_ON" name="XML_ON" value="ON" <?php if($zbp->Config('ZBlogSEO')->XML_ON == 'ON') echo 'checked'?> />	开启地图
						</label>
						<label style="display:block;padding-top:4px;float: left;margin-left: 15px;">
							<input type="radio" id="XML_ON" name="XML_ON" value="OFF" <?php if($zbp->Config('ZBlogSEO')->XML_ON == 'OFF') echo 'checked'?> />关闭地图
						</label></p>
						<p><span>XML文件名</span>
						<label style="display:block; float: left;padding-top:4px;">
							<input type="radio" id="XML_FileName" name="XML_FileName" value="sitemap_baidu" <?php if($zbp->Config('ZBlogSEO')->XML_FileName == 'sitemap_baidu') echo 'checked'?> />	sitemap_baidu
						</label>
						<label style="display:block;padding-top:4px;float: left;margin-left: 15px;">
							<input type="radio" id="XML_FileName" name="XML_FileName" value="sitemap" <?php if($zbp->Config('ZBlogSEO')->XML_FileName == 'sitemap') echo 'checked'?> />sitemap
						</label></p>
                        <p><span>生成XML地图</span><input type="checkbox" name="Enabled_XML_Sitemap" id="Enabled_XML_Sitemap" value="true" <?php if($zbp->Config('ZBlogSEO')->Enabled_XML_Sitemap) echo 'checked="checked"'?> /></p>
                        <p><span>生成Html地图</span><input type="checkbox" name="Enabled_HTML_Sitemap" id="Enabled_HTML_Sitemap" value="true" <?php if($zbp->Config('ZBlogSEO')->Enabled_HTML_Sitemap) echo 'checked="checked"'?> /></p>
                         <p><span>生成TXT地图</span><input type="checkbox" name="Enabled_TXT_Sitemap" id="Enabled_TXT_Sitemap" value="true" <?php if($zbp->Config('ZBlogSEO')->Enabled_TXT_Sitemap) echo 'checked="checked"'?> /></p>
						<p><span>首页</span>
						<input style="width:50px;margin-left: 70px;" id="home_priority" name="home_priority" type="text" value="<?php echo $zbp->Config('ZBlogSEO')->home_priority;?>" />
						<select name="home_frequency" id="home_frequency">
							<option value="Always" <?php if($zbp->Config('ZBlogSEO')->home_frequency == 'Always') echo 'selected'?>>Always</option>
							<option value="Hourly" <?php if($zbp->Config('ZBlogSEO')->home_frequency == 'Hourly') echo 'selected'?>>Hourly</option>
							<option value="Daily" <?php if($zbp->Config('ZBlogSEO')->home_frequency == 'Daily') echo 'selected'?>>Daily</option>
							<option value="Weekly" <?php if($zbp->Config('ZBlogSEO')->home_frequency == 'Weekly') echo 'selected'?>>Weekly</option>
							<option value="Monthly" <?php if($zbp->Config('ZBlogSEO')->home_frequency == 'Monthly') echo 'selected'?>>Monthly</option>
							<option value="Yearly" <?php if($zbp->Config('ZBlogSEO')->home_frequency == 'Yearly') echo 'selected'?>>Yearly</option>
						</select>
						</p>
						<p><span>分类</span><input type="checkbox" name="category_select" id="category_select" value="true" <?php if($zbp->Config('ZBlogSEO')->category_select) echo 'checked="checked"'?> />
						<input style="width:50px;margin-left: 50px;" id="category_priority" name="category_priority" type="text" value="<?php echo $zbp->Config('ZBlogSEO')->category_priority;?>" />
						<select name="category_frequency" id="category_frequency">
							<option value="Always" <?php if($zbp->Config('ZBlogSEO')->category_frequency == 'Always') echo 'selected'?>>Always</option>
							<option value="Hourly" <?php if($zbp->Config('ZBlogSEO')->category_frequency == 'Hourly') echo 'selected'?>>Hourly</option>
							<option value="Daily" <?php if($zbp->Config('ZBlogSEO')->category_frequency == 'Daily') echo 'selected'?>>Daily</option>
							<option value="Weekly" <?php if($zbp->Config('ZBlogSEO')->category_frequency == 'Weekly') echo 'selected'?>>Weekly</option>
							<option value="Monthly" <?php if($zbp->Config('ZBlogSEO')->category_frequency == 'Monthly') echo 'selected'?>>Monthly</option>
							<option value="Yearly" <?php if($zbp->Config('ZBlogSEO')->category_frequency == 'Yearly') echo 'selected'?>>Yearly</option>
						</select>
						</p>
						<p><span>文章</span><input type="checkbox" name="post_select" id="post_select" value="true" <?php if($zbp->Config('ZBlogSEO')->post_select) echo 'checked="checked"'?> />
						<input style="width:50px;margin-left: 50px;" id="post_priority" name="post_priority" type="text" value="<?php echo $zbp->Config('ZBlogSEO')->post_priority;?>" />
						<select name="post_frequency" id="post_frequency">
							<option value="Always" <?php if($zbp->Config('ZBlogSEO')->post_frequency == 'Always') echo 'selected'?>>Always</option>
							<option value="Hourly" <?php if($zbp->Config('ZBlogSEO')->post_frequency == 'Hourly') echo 'selected'?>>Hourly</option>
							<option value="Daily" <?php if($zbp->Config('ZBlogSEO')->post_frequency == 'Daily') echo 'selected'?>>Daily</option>
							<option value="Weekly" <?php if($zbp->Config('ZBlogSEO')->post_frequency == 'Weekly') echo 'selected'?>>Weekly</option>
							<option value="Monthly" <?php if($zbp->Config('ZBlogSEO')->post_frequency == 'Monthly') echo 'selected'?>>Monthly</option>
							<option value="Yearly" <?php if($zbp->Config('ZBlogSEO')->post_frequency == 'Yearly') echo 'selected'?>>Yearly</option>
						</select>
						</p>	
						<p><span>页面</span><input type="checkbox" name="page_select" id="page_select" value="true" <?php if($zbp->Config('ZBlogSEO')->page_select) echo 'checked="checked"'?> />
						<input style="width:50px;margin-left: 50px;" id="page_priority" name="page_priority" type="text" value="<?php echo $zbp->Config('ZBlogSEO')->page_priority;?>" />
						<select name="page_frequency" id="page_frequency">
							<option value="Always" <?php if($zbp->Config('ZBlogSEO')->page_frequency == 'Always') echo 'selected'?>>Always</option>
							<option value="Hourly" <?php if($zbp->Config('ZBlogSEO')->page_frequency == 'Hourly') echo 'selected'?>>Hourly</option>
							<option value="Daily" <?php if($zbp->Config('ZBlogSEO')->page_frequency == 'Daily') echo 'selected'?>>Daily</option>
							<option value="Weekly" <?php if($zbp->Config('ZBlogSEO')->page_frequency == 'Weekly') echo 'selected'?>>Weekly</option>
							<option value="Monthly" <?php if($zbp->Config('ZBlogSEO')->page_frequency == 'Monthly') echo 'selected'?>>Monthly</option>
							<option value="Yearly" <?php if($zbp->Config('ZBlogSEO')->page_frequency == 'Yearly') echo 'selected'?>>Yearly</option>
						</select>
						</p>	
						<p><span>标签</span><input type="checkbox" name="tag_select" id="tag_select" value="true" <?php if($zbp->Config('ZBlogSEO')->tag_select) echo 'checked="checked"'?> />
						<input style="width:50px;margin-left: 50px;" id="tag_priority" name="tag_priority" type="text" value="<?php echo $zbp->Config('ZBlogSEO')->tag_priority;?>" />
						<select name="tag_frequency" id="tag_frequency">
							<option value="Always" <?php if($zbp->Config('ZBlogSEO')->tag_frequency == 'Always') echo 'selected'?>>Always</option>
							<option value="Hourly" <?php if($zbp->Config('ZBlogSEO')->tag_frequency == 'Hourly') echo 'selected'?>>Hourly</option>
							<option value="Daily" <?php if($zbp->Config('ZBlogSEO')->tag_frequency == 'Daily') echo 'selected'?>>Daily</option>
							<option value="Weekly" <?php if($zbp->Config('ZBlogSEO')->tag_frequency == 'Weekly') echo 'selected'?>>Weekly</option>
							<option value="Monthly" <?php if($zbp->Config('ZBlogSEO')->tag_frequency == 'Monthly') echo 'selected'?>>Monthly</option>
							<option value="Yearly" <?php if($zbp->Config('ZBlogSEO')->tag_frequency == 'Yearly') echo 'selected'?>>Yearly</option>
						</select>
						</p>	
						<p><span>生成文章数量</span>
						<input style="width:50px;margin-left: 70px;" id="sitemap_number" name="sitemap_number" type="text" value="<?php echo $zbp->Config('ZBlogSEO')->sitemap_number;?>" />
						
						</p>
	                </td>
                 </tr>
            </table>
	        <hr/>
	        <p><input type="submit" class="button" value="保存" /></p>
	        <hr/>
			<table width="100%" style='padding:0;margin:0;' cellspacing='0' cellpadding='0' class="tableBorder">
				<tr height="32"><td style="color: #E91E63;font-weight: bold;">保存之后【新建一篇文章】自动生成地图哦!!!</td></tr>
				
			</table>
            <table border="1" class="tableFull tableBorder">
                <tr>
					<?php if($zbp->Config('ZBlogSEO')->XML_FileName == 'sitemap_baidu'){ ?>
						<th class="td20">sitemap_baidu.xml地址：</td>
					<th><p><a href="<?php echo $zbp->host;?>sitemap_baidu.xml" target="_blank"><?php echo $zbp->host;?>sitemap_baidu.xml</a></p></td>
					
					<?php }else{ ?>
						<th class="td20">sitemap.xml地址：</td>
					<th><p><a href="<?php echo $zbp->host;?>sitemap.xml" target="_blank"><?php echo $zbp->host;?>sitemap.xml</a></p></td>
					<?php } ?>
                </tr>
				<tr>
					<?php if($zbp->Config('ZBlogSEO')->XML_FileName == 'sitemap_baidu'){ ?>
						<th class="td20">sitemap_baidu.html地址：</td>
					<th><p><a href="<?php echo $zbp->host;?>sitemap_baidu.html" target="_blank"><?php echo $zbp->host;?>sitemap_baidu.html</a></p></td>
					
					<?php }else{ ?>
						<th class="td20">sitemap.html地址：</td>
					<th><p><a href="<?php echo $zbp->host;?>sitemap.html" target="_blank"><?php echo $zbp->host;?>sitemap.html</a></p></td>
					<?php } ?>
                </tr>
                <tr>
					<?php if($zbp->Config('ZBlogSEO')->XML_FileName == 'sitemap_baidu'){ ?>
						<th class="td20">sitemap_baidu.txt地址：</td>
					<th><p><a href="<?php echo $zbp->host;?>sitemap_baidu.txt" target="_blank"><?php echo $zbp->host;?>sitemap_baidu.txt</a></p></td>
					
					<?php }else{ ?>
						<th class="td20">sitemap.txt地址：</td>
					<th><p><a href="<?php echo $zbp->host;?>sitemap.txt" target="_blank"><?php echo $zbp->host;?>sitemap.txt</a></p></td>
					<?php } ?>
                </tr>
				<tr>
					<td class="td20">向Google提交：</td>
					<td><p><a href="http://www.google.com/webmasters" target="_blank">http://www.google.com/webmasters</a></p></td>
				</tr>
				<tr>
					<td class="td20">向百度站长平台提交：</td>
					<td><p><a href="http://zhanzhang.baidu.com/sitemap" target="_blank">http://zhanzhang.baidu.com/sitemap</a></p></td>
				</tr>
				<tr>
				<td class="td20">更新频率：</td>
				<td><p>Always（总是）、Hourly（每小时）、Daily（每天）、Weekly（每个星期）、Monthly（每个月）、Yearly（每年）</p></td>
				</tr>

            </table>

	  	</form>
		<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
		<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBlogSEO/logo.png';?>");</script>	
    </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';        	  	    		 	 			
    	 	 		      				 	 	
RunTime();    			  	 	    								
?>