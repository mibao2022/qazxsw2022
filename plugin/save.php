<?php /* EL PSY CONGROO */    		   	 	
require '../../../../zb_system/function/c_system_base.php';      	  	 	
require '../../../../zb_system/function/c_system_admin.php';     	  				
     	     	
$zbp->Load();    			  	 	
$action='root';     		   		
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}    	       
if (!$zbp->CheckPlugin('ZBlogSEO')) {$zbp->ShowError(48);die();}      	    	
    		 	 	 	
if(GetVars('type','GET') == 'spider' ){        	 	   		
global $zbp;     			 			     	  	 		
    ZBlogSEO_DelTable();    				  		
	ZBlogSEO_CreateTable();				    	 	 				
    $zbp->SetHint('good');    		    	 
	Redirect('spider.php?act=config');     		    		
}	    	   	   
     					  
if($_GET['type'] == 'add' ){     	  			 
	global $zbp;            
	      	    	
	if(!$_POST["title"] or !$_POST["url"]){      		 		 
		$zbp->SetHint('bad','关键字或链接不能为空');     			 	  
		Redirect('./taglink.php');    		 		 		
		exit();       				 
	}     						 
	       			  
	$DataArr = array(     	 			  
		'KeyWord_Title'=>$_POST["title"],    	 			  	
		'KeyWord_Url'=>$_POST["url"],    	 	   	 
		'KeyWord_IsUsed'=>$_POST["IsUsed"]    		  	   
	);     	 	    
          	 
	if($_POST["editid"]){    		     	
		$where = array(array('=','KeyWord_ID',$_POST["editid"]));    		 	 			
		$sql= $zbp->db->sql->Update($zbp->table['ZBlogSEOTag'],$DataArr,$where);    	  	  		
		$zbp->db->Update($sql);    	 	   		
	}else{        			 
		$sql= $zbp->db->sql->Insert($zbp->table['ZBlogSEOTag'],$DataArr);      	   	 
		$zbp->db->Insert($sql);     			 	  
	}    	  	 			
	$zbp->SetHint('good','关键词保存成功');    	 	 			 
	Redirect('./taglink.php');        	  	
}    		 				 
       	   	
if($_GET['type'] == 'del' ){     	 		   
	global $zbp;       	   	
	$where = array(array('=','KeyWord_ID',$_GET['id']));    	     		
	$sql= $zbp->db->sql->Delete($zbp->table['ZBlogSEOTag'],$where);    	 	 		 	
	$zbp->db->Delete($sql);    		 	 	 	
	$zbp->SetHint('good','删除成功');     				   
	Redirect('./taglink.php');     		 	 		
}     		   	 
      	  			
?>