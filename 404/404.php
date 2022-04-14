<?php /* EL PSY CONGROO */     		  				
     	 	  		
function ZBlogSEO_ShowError404() {      	 	       				  	 
	global $zbp;     	  			       	  	 	
	if (!in_array("Status: 404 Not Found", headers_list())) {    			 	 	         			 
		return;           	    	 			  	
	}       	       
    if($zbp->Config('ZBlogSEO')->ym404yx == 'one'){   			 		      		 	 		
		include '404one.php';     	 			 	
	}elseif($zbp->Config('ZBlogSEO')->ym404yx == 'two'){     		  			
		include '404two.php';     		  			
	}else{     	   	  
		include '404three.php';    	   	   
	}        			  
	die; 		  	       		  	  
}       				  	 
     	 			 	
     	    	 