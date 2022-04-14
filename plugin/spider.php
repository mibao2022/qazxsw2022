<?php /* EL PSY CONGROO */    	   	 		
require '../../../../zb_system/function/c_system_base.php';      	  			    				 			
require '../../../../zb_system/function/c_system_admin.php';      			  	    		  			 
$zbp->Load(); $action='root';    		  		 	     	 		   
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}    		 	 		     	 	   	 
if (!$zbp->CheckPlugin('ZBlogSEO')) {$zbp->ShowError(48);die();}    	    	       	 			 	
		 	    			   		
$blogtitle='蜘蛛统计';      		   	      						
$act = '';       	 	 	    	  			  
$act = GetVars('act','GET') == "" ? 'index' : GetVars('act','GET');    			 		 	    		  	   
require $blogpath . 'zb_system/admin/admin_header.php';    	   		      	    			
require $blogpath . 'zb_system/admin/admin_top.php';      			       		   	  
switch ($act){     	     	    		  	   
    case 'ListByURL':    			  	 	     							
    case 'ListByName':    	 				 	       					
    case 'ListByERR':    		  		      	 	 			 
    case 'ListLast':     	  				        		 	
        ZBlogSEO_box($act);          		     	 	   	
        break;      	   	     	   		 	
    case 'errlist':         	 	    				 			
        ZBlogSEO_errlist();    			  			       	 	 	
        break;     	 		       	 	   		
    case 'list':    		  		      	  	    
        ZBlogSEO_list();     	          		   	  
        break;      	  		     				 	  
    case 'config':     		 	         	 			 
        ZBlogSEO_config();    	     	      							
        break;     			  	     		 	  	 
    case 'today':    	 	 		      		 		  	
        ZBlogSEO_today();    			   	           		
        break;    	 	  	       	  	   
    case 'index':    	 	  		     	   				
        default:    		    	     		 		  	
    ZBlogSEO_index();    			   		    	     		
    		 				       		 		 
    				 	       	  	  	
}    	  		  	    						  
function ZBlogSEO_config()      			  	          		
{     		         					 		
    global $zbp;    		   	 	    	 	    	
if(isset($_POST['viewconut'])) {    	 	  	      	    	  
    $zbp->Config('ZBlogSEO')->viewconut = $_POST['viewconut'];    	  			 	            
    $zbp->Config('ZBlogSEO')->spiders = $_POST['spiders'];     				 		     	  				
    $zbp->Config('ZBlogSEO')->logdate=$_POST['logdate'];          	  	
	$zbp->Config('ZBlogSEO')->cdnip=$_POST['cdnip']; 	       			  
    $zbp->SaveConfig('ZBlogSEO');    	  					    	 		 			
    $zbp->SetHint('good'); }       		   	
    		   	  
	    	 						
	?>
	

	
<div id="divMain">
    <div class="divHeader">蜘蛛统计</div>
	<div class="SubMenu"><?php ZBlogSEO_SubMenu(6);?></div>
	<div class="SubMenu"> 
		<a href="?act=config" ><span class="m-left m-now">基本设置</span></a>
		<a href="?act=index" ><span class="m-left">爬行榜单</span></a>
		<a href="?act=today" ><span class="m-left ">今日到访</span></a>
		<a href="?act=list" ><span class="m-left ">所有记录</span></a>
	</div>
      <div id="divMain2">
<form method="post">
    <table class="tb-set" width="100%">
        <tr>
            <td align="right" class="td20"><b>显示篇数：</b></td>
            <td><input type="text" class="txt" name="viewconut" value="<?php echo $zbp->Config('ZBlogSEO')->viewconut; ?>" /></td>
        </tr>
        <tr>
            <td align="right"><b>记录保留天数：</b></td>
            <td><input type="text" class="txt" name="logdate" value="<?php echo $zbp->Config('ZBlogSEO')->logdate; ?>" /></td>
        </tr>
        <tr>
            <td align="right"><b>蜘蛛列表：</b><br />格式：名称,特征。<br />多个用“|”分隔,如：Baiduspider,Baidu|Googlebot,Google。</td>
            <td><textarea class="txt txt-lar" name="spiders" style="width: 98%"><?php echo $zbp->Config('ZBlogSEO')->spiders; ?></textarea></td>
        </tr>
		<tr>
			<td><p align="center">切换IP记录</p></td>
			<td><p align="center"><input type="text" id="cdnip" name="cdnip" class="checkbox" value="<?php echo $zbp->Config('ZBlogSEO')->cdnip;?>"/></p></td>
		</tr>
		<tr>
			<td></td>
			<td>如果您的服务器使用CDN加速，造成用户IP统计不准，打开该按钮即可解决。</td>
		 </tr>
        <tr>
            <td>&nbsp;</td>
            <td><p>注意：开启伪静态后才能获取到完整的404记录</p></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" name="submit" value="保存" /></td>
        </tr>
	</table>
</form>
	<form enctype="multipart/form-data" method="post" action="save.php?type=spider"> 
	  <p>
		<input type="submit" class="button" value="一键清空蜘蛛记录" />
	  </p>
	</form>
	
</div>
</div>
<script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>

<?php
}    		 	 		        					
     	  		 	      	 		 	
function ZBlogSEO_index()    		 	  		    		 				 
{       	  	     					 		
    $M = new ZBlogSEO('Load');     	  	 		    		   			
    $M->url_format ='{%host%}zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list{&page=%page%}';       				      	   	  
    $M->template->SetTags('ListByURL', $M->GetListByURL(10));                							 
    $M->template->SetTags('ListByName', $M->GetListByName(10));      			  	    	  	 	  
    $M->template->SetTags('ListByERR', $M->GetListByERR(10));    	 		  		     	 	 		 
    $M->template->SetTags('ListLast', $M->GetListLast(10));    			 	 		      		 		 
    $M->template->SetTags('ListData', $M->GetData(10));     	    	         	  	
    $M->template->SetTemplate('index');    	 	  		        	 			
    $M->template->Display();    		 	   	          		
}    		 		       	 		 			
     	  			     	   	 		
function ZBlogSEO_list()       		         			 	 
{      			 	     	  	   	
    $_name =GetVars('name','GET');     		 	        				  	
    $_ip =GetVars('ip','GET');      		  		    		  			 
    $_url =GetVars('url','GET');    			 				       		 		
    $_type='list';    	  	          			  	
    $M = new ZBlogSEO('Load');     	   	        		 			
    $M->url_format ='{%host%}zb_users/plugin/ZBlogSEO/plugin/spider.php?act='.$_type.'&page={%page%}&name='.$_name.'&ip='.$_ip.'&url='.$_url.'';    	  	 			    	   	 		
    if($_name<>null ){$M->where[]=array('=','Spider_Name',$_name);}    	     		    		 		 		
    if($_ip<>null ){$M->where[]=array('=','Spider_IP',$_ip);}       		 		    	 	 			 
    if($_url<>null ){$M->where[]=array('=','Spider_URL',$_url);}     			 	         	 	 	
    $M->template->SetTags('_name', $_name);     	 	 	 	       		 	 
    $M->template->SetTags('_ip', $_ip);    	   		        		  		
    $M->template->SetTags('ListData', $M->GetData());     	  	       	  	 	 	
    $M->template->SetTemplate('list');    						      	  	  		
    $M->template->Display();    		 	 	         	    
}     	  		 	        			 
      			        	  	 	 
function ZBlogSEO_errlist()      	 				           	
{     			            		  
    $_name =GetVars('name','GET');     	  	       		 	   	
    $_ip =GetVars('ip','GET');    	 		   	    	  	 		 
    $_url =GetVars('url','GET');    		 	 	 	    	  			  
    $_type='errlist';    	 	   		    	    	  
    $M = new ZBlogSEO('Load');     	     	    	 				  
    $M->url_format ='{%host%}zb_users/plugin/ZBlogSEO/plugin/spider.php?act='.$_type.'&page={%page%}&name='.$_name.'&ip='.$_ip.'&url='.$_url.'';      	  		      	  		  
    if($_name<>null ){$M->where[]=array('=','Spider_Name',$_name);}     		 		       				 		
    if($_ip<>null ){$M->where[]=array('=','Spider_IP',$_ip);}    			  			     			 			
    if($_url<>null ){$M->where[]=array('=','Spider_URL',$_url);}    	   	       		 				 
    $M->where[]=array('<>','Spider_Status',200);      		  		     		  			
    $M->template->SetTags('ListData', $M->GetData());    	 				      			  	  
    $M->template->SetTemplate('list');    		    	           		
    $M->template->Display();    	 	 	 		     	 	   	
}     				       	  		  	
     				 	       	   	 
function ZBlogSEO_box($act)    							     	  	 	 	
{     			  	     	 		    
    $M = new ZBlogSEO('Load');    			  			     	  	 	 
    $M->url_format ='{%host%}zb_users/plugin/ZBlogSEO/plugin/spider.php?act=list&page={%page%}';     		          		 		  
    switch ($act){    	 			  	     	  	   
        case 'ListByURL':      			 		    				 	  
            $Listdata = $M->GetListByURL(500);      		  		      		  	 
            $box_title ='爬行榜单';    	  	 		     	  	    
            break;    		   			    				 	 	
        case 'ListByName':    	  			      	  			  
            $Listdata = $M->GetListByName(500);    				 			     			 			
            $box_title ='来访排行';     				       			   		
            break;    	 					      			   	
        case 'ListByERR':      	 		 	    	    			
            $Listdata = $M->GetListByERR(500);      		  	     				 	  
            $box_title ='错误记录';     		   	      		 	  	
            break;    	    			    		 			 	
        case 'ListLast':    		 	  	      	 					
            $Listdata = $M->GetListLast(500);     	  	 	     						  
            $box_title ='最近来访';    						       	  	 		
            break;     		  		     	    			
            default:    	   		        		  	 
            return;    	   	 	     	 			 	 
    }       		 	      		 				
    $M->template->SetTags('List', $Listdata);       	        	  			 	
    $M->template->SetTags('box_title', $box_title);      	          		
    $M->template->SetTemplate('box');    	 			 	     		 	    
    $M->template->Display();    	    		      		   		
}    		     	    		 	   	
     		 		      		 		   
function ZBlogSEO_today()     		 		      	  		   
{                 	 		 		
    global $zbp;     		 	 		    	    		 
    $box_title = '今日到访';    			   	       	 	   
    $_name =GetVars('name','GET');    	 		 		        		 	 
    $_ip =GetVars('ip','GET');    	  			      	  	 	 	
    $date =GetVars('date','GET');    	 				        	 	 		
    $date = $date==null?date('Y-m-d',time()):$date;    	 		 			      	 	 	 
    	  				         		 	
    $M = new ZBlogSEO('Load');       		 		    	  	  		
    $Listdata = $M->GetListToday($date);    	  	         	  	  	
    $M->template->SetTags('List', $Listdata);    	 		 			      	 		  
    $M->template->SetTags('box_title', $box_title."({$date})");    			    	    	  		   
    $M->template->SetTags('box_title_button', '<a href="?act=today&date='.date("Y-m-d",strtotime("-1 day",strtotime($date))).'" class="button" >前一日&gt;</a>');    		 			 	      			 	 
    $M->template->SetTags('date', $date);      		         		 				
    $M->template->SetTags('_name', $_name);    		 	 		       	 	 		
    $M->template->SetTags('_ip', $_ip);     	    	     			  	 	
    $M->template->SetTemplate('today');    	   			      	 		 	 
    $M->template->Display();          	     		 		  	
}     					        			   
      	 	   
    					 	 
require $blogpath . 'zb_system/admin/admin_footer.php';     	  	   
RunTime();    							 
?>