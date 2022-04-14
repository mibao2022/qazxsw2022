<?php /* EL PSY CONGROO */    	 	  		 
require '../../../../zb_system/function/c_system_base.php';    	   	  	
require '../../../../zb_system/function/c_system_admin.php';      	    	
       	 	  
$zbp->Load();      		   	
     		 		  
$action='root';      	 			 
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}    		 		   
      						
if (!$zbp->CheckPlugin('ZBlogSEO')) {$zbp->ShowError(48);die();}     			    
     							
$blogtitle='留言管理';     			  	 
    			 	 	 
require $blogpath . 'zb_system/admin/admin_header.php';    		     	
require $blogpath . 'zb_system/admin/admin_top.php';      	  	  
         			
?>
<div id="divMain"> 
<?php ZBlogSEO_CommentMng();?>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';      				  
    	     	 
RunTime();     	  				
?>