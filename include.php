<?php /* EL PSY CONGROO */        				
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/InstallPlugin/active.php';      			 		
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/TitleSeo/titleseo.php';      					 
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/TitleSeo/custom.php';     	 	 	  
if($zbp->Config('ZBlogSEO')->XML_ON !=='OFF'){    		 		   
	require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/Sitemap/Sitemap.php';    			  	  
}     		  			
if($zbp->Config('ZBlogSEO')->article_url){    		 	 			
	require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/link/articlelink.php';     		   		 
}     				   
if($zbp->Config('ZBlogSEO')->commentlink_url){    			 				
	require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/link/commentlink.php';      	      
}    		 	 	  
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/imgalt/imgalt.php';    	  	 	  
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/tag/include.php';       		 		  
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/tag/tagclass.php';        	   	
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/baiduRTSM/baiduRTSM.php';     		    		
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/tools/include.php';       			  	
if($zbp->Config('ZBlogSEO')->zbp_Comment_Management){    				   	
	require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/Comment/Management.php';     		  		 
}    		   	  
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/spider/include.php';    	 			 	 
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/spider/class.spider.php';      	     
if($zbp->Config('ZBlogSEO')->zbp_views){    	 		 		 
	require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/Views/include.php';    		   			
}    	 		   	
require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/InstallPlugin/InstallPlugin.php';     	 	 	 	 
    				 	  
if($zbp->Config('ZBlogSEO')->ym404){      		  		
	require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/404/404.php';     		 		   
}     		   	 
if($zbp->Config('ZBlogSEO')->baidks){    	  	 	  
	require dirname(__FILE__) . DIRECTORY_SEPARATOR . '/baiduRTSM/kuaisu.php';     		   			
}    	   		  
#注册插件     						 
RegisterPlugin("ZBlogSEO","ActivePlugin_ZBlogSEO");      			 	 
       	   	
function ZBlogSEO_Main(){    		   	 	
	global $zbp;      					 
	if($zbp->Config('ZBlogSEO')->zbp_Comment_Management){    	  		 		
		if ($zbp->CheckRights('root')) {       			  
			Redirect(''.$zbp->host.'zb_users/plugin/ZBlogSEO/Comment/main.php');    		 	 	  
		}    		 	 	 	
	}    		  		 	
}    	  			  
      				 	
function ZBlogSEO_AddMenu(&$m){      	  	  
	global $zbp;      	   	 
	array_unshift($m, MakeTopMenu("root",'SEO设置',$zbp->host . "zb_users/plugin/ZBlogSEO/plugin/titleseo.php","","topmenu_ZBlogSEO"));    	  		   
}    	   	 	 
      	  		 
    	 	 				
$table['ZBlogSEOTag'] = '%pre%ZBlogSEOTag';     		  	  
$datainfo['ZBlogSEOTag'] = array(    		 					
	'ID'=>array('KeyWord_ID','integer','',0),    		    		
	'Type'=>array('KeyWord_Type','integer','',0),      			 	 
	'Title'=>array('KeyWord_Title','string',255,''),    		 	 			
	'Url'=>array('KeyWord_Url','string',255,''),    						 	
	'Des'=>array('KeyWord_Des','string',255,''),      				  
	'Order'=>array('KeyWord_Order','integer','',99),    		     	
	'Code'=>array('KeyWord_Code','string',255,''),      			  	
	'IsUsed'=>array('KeyWord_IsUsed','boolean','',true),    			 		  
);    		 	 	  
    		   			
function UninstallPlugin_ZBlogSEO() {     	  	 		
	global $zbp;    	  		   
	if($zbp->Config('ZBlogSEO')->zbp_zblog_seo){    	   	 	 
		$zbp->DelConfig('ZBlogSEO');     	  	  	
		ZBlogSEO_DelTable();    	     		
		ZBlogSEO_Tag_DelTable();    				 		 
	}    	   	   
}    			  			
    	 			 	 