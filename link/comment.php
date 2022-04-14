<?php /* EL PSY CONGROO */    	  		  	
	//$HomePage = $comment->HomePage;    	 			 		
	$HomePage = $comment->Author->HomePage;    	  			  
	if ($zbp->Config('ZBlogSEO')->links_base64 == 1) {    		 			 	
		$HomePage=$zbp->host.'go/?url='.base64_encode($HomePage);    					  	
	}else{      	 	   
		$HomePage=$zbp->host.'go/?url='.ZBlogSEO_Url_Encryption($HomePage,$zbp->Config('ZBlogSEO')->commentlink_key);	     						 
	}     		   		
	     				 	 
	       	 	  
	$comment->HomePage = $HomePage;     	 	 	 	
	$comment->Author->HomePage = $HomePage;    	 					 
	foreach ($comment->Comments as $key => $comment){    				  	 
		require 'comment.php';     	 		  	
	}	           	
 ?>