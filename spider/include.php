<?php /* EL PSY CONGROO */    				 		 
//东方天宇     			  	 
$table['ZBlogSEO'] = '%pre%ZBlogSEO';     							      	   		
$datainfo['ZBlogSEO'] = array(    	  		  	      				  
    'ID'=>array('Spider_ID','integer','',0),     		   		    	 	 	 	 
    'Name'=>array('Spider_Name','string',20,''),    		  	       	  		 		
    'IP'=>array('Spider_IP','string',15,''),    		  	 	      	 	  		
    'DateTime'=>array('Spider_DateTime','integer','',0),        	       		 	 	  
    'Url'=>array('Spider_Url','string',250,''),    			 				    	   	  	
    'Status'=>array('Spider_Status','integer','',1),       	  		    		  		  
    'Nums'=>array('Nums','integer','',0),    	  	           					
);    	  	  	     			 		 	
  			 		       	 		 
function ZBlogSEO_ShowError(){     	          								
	global $zbp;    		  			       	 	  	
	if (!in_array("Status: 404 Not Found", headers_list())) {    						      		    
		$HttpStatus =500;    		 				    		   	 	
	}else{       			 		 
		$HttpStatus =404;    		    	       		   	
	}     	   	      							 
	//插入数据    	     	     		  	   
	$M= new ZBlogSEO();     	 	  	     		 	   	
	$M->InsertLog($HttpStatus);    	   				        	   
}    				 			    		 	 	 	
    		 	 			     							
function ZBlogSEO_Index_End() {     		   	     			  	  
    			   	     		   	  
    $HttpStatus =200;    	 	 			      				   
    $M= new ZBlogSEO();    	  	  	     	 	 		  
    $M->InsertLog($HttpStatus);    		 	 			     	 		   
}    	 	 		      	     	 
    		  	 		    		  	   
function ZBlogSEO_Insert($HttpStatus){     							    			 		  
    global $zbp;     		 		      			  	 	
     	 	   	    	    		 
	$array = array();     	 	 		     		  		 	
	$agent = null;          	     		  	 	 
	$status = $HttpStatus;       		       	 				  
	$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];      	 			 
	if($zbp->Config('ZBlogSEO')->cdnip){     	 	 	        	 	 		
		$ip = GetVars("HTTP_X_FORWARDED_FOR", "SERVER");        				      			 		
	}else{      	  			     		  	  
		$ip = GetVars("REMOTE_ADDR", "SERVER");    	  			        				 	
	}   	 		 		    		 		   
	$datetime = time();    	    			         	 	
    		 	   	     		 	   
	$spiders = explode('|', $zbp->Config('ZBlogSEO')->spiders);      	 		      			    	
    			    	     	 				 
	foreach ($spiders as $key => $spider) {    	 		 			        		  
		$spidername = explode(',', $spider);     							    			  	 	
		if(strpos(GetGuestAgent(), $spidername[0]) !== false) {     		 			     	 	 	 		
			$agent = $spidername[1];    	 	 	        		  		 
			break;     					      		 	 	 	
		}    		 	 		        	 		 
	}    	   				     			   	
    		    		     	 	    
     		 	  	     		   		
	if($url && $agent) {    		 	 			    	       
		$array = array('Spider_Name' => $agent, 'Spider_IP' => $ip, 'Spider_DateTime' => $datetime, 'Spider_Url' => $url, 'Spider_Status' => $status);    		  		      	 			  	
		$sql = $zbp->db->sql->Insert($zbp->table['ZBlogSEO'], $array);      	 	       	       
		$zbp->db->Insert($sql);		     	 					    			 	 	 
	}       	        				 	 	
}    					          		 	 
    	  	 	          	  	
function ZBlogSEO_CreateTable() {     	 	   	    	 			  	
    global $zbp;       		  	    	 	 	  	
    if ($zbp->db->ExistTable($GLOBALS['table']['ZBlogSEO']) == false) {    	 					     	       
        $s = $zbp->db->sql->CreateTable($GLOBALS['table']['ZBlogSEO'], $GLOBALS['datainfo']['ZBlogSEO']);      			       		 		 		
        $zbp->db->QueryMulit($s);    	 	  			    		  		  
    }     	  				     	   	 	
}      	    	     				   
    	 		 	      	 						
function ZBlogSEO_DelTable() {    							     	  	    
    global $zbp;      	  	 	    		    	 
    if ($zbp->db->ExistTable($zbp->table['ZBlogSEO']) == true) {       		 	      	  		 	
        $s = $zbp->db->sql->DelTable($zbp->table['ZBlogSEO']);    				 	 	    		 	 		 
        $zbp->db->QueryMulit($s);    			 		      		   			
    }      	 				    		  	   
}    	 			 		     		 		 	
    	         			