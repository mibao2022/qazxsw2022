<?php /* EL PSY CONGROO */     	  	 	 
       				 
function ZBlogSEO_Url_Encryption($txt,$key='www.fengyan.cc')     		 	 	 
{    				 			
    $txt = $txt.$key;    	 					 
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";     	  		  
    $nh = rand(0,64);    	 				  
    $ch = $chars[$nh];    	   		 	
    $mdKey = md5($key.$ch);      			 	 
    $mdKey = substr($mdKey,$nh%8, $nh%8+7);    			   		
    $txt = base64_encode($txt);     						 
    $tmp = '';    		 	 		 
    $i=0;$j=0;$k = 0;     		     
    for ($i=0; $i<strlen($txt); $i++) {            
        $k = $k == strlen($mdKey) ? 0 : $k;        				
        $j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;    		   		 
        $tmp .= $chars[$j];    		 				 
    }    		 		 	 
    return urlencode(base64_encode($ch.$tmp));    	 	  	  
}    	  	  	 
      	 	  	
function ZBlogSEO_Url_Content(&$template){    		 		   
    global $zbp;     	  		  
	$comments = $template->GetTags('comments');    			 			 
	foreach ($comments as $key => $comment){     	  	  	
		require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'comment.php';     		 			 
	}       			  
	$template->SetTags('comments', $comments);    	 	 				
}    				 		 
     	 	  		
function ZBlogSEO_Url_Index_Begin() {    	  			 	
    global $zbp;    	  			  
    $_router = "go";    				    
    $_path = $_SERVER['REQUEST_URI'];     	 		  	
    $_path = explode('?',$_path);     		 	  	
    $_path = $_path[0];     	      
    if (!($zbp->option['ZC_STATIC_MODE'] == 'REWRITE')) {    	  		 		
        if ($_path == '/' || $_path == '/index.php') {         		 
            $_key = GetVars($_router,'GET');     			 	  
            if (!$_key) return;    		  		 	
        } else {     		 	   
            return;    	   				
        }    	 	 		 	
    } else {     	 	 			
        $_index = strripos($_path,'/'.$_router.'/');     	 	  		
        if ($_index === false) return;    				 			
        $_key = substr($_path,$_index+strlen($_router)+2);    			 	 	 
    }     		  		 
    ZBlogSEO_Url_query($_key);    	 	 	  	
}    					   
    	     		
function ZBlogSEO_Url_Decrypt($txt,$key='www.fengyan.cc')    		 		  	
{      	 	 		
    $txt = base64_decode(urldecode($txt));    	   	  	
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+";     	 		  	
    $ch = $txt[0];        				
    $nh = strpos($chars,$ch);    				 		 
    $mdKey = md5($key.$ch);      			 		
    $mdKey = substr($mdKey,$nh%8, $nh%8+7);    		   		 
    $txt = substr($txt,1);      		 	 	
    $tmp = '';      	 	   
    $i=0;$j=0; $k = 0;       	   	
    for ($i=0; $i<strlen($txt); $i++) {     	  		 	
        $k = $k == strlen($mdKey) ? 0 : $k;    	  				 
        $j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);    	 		 			
        while ($j<0) $j+=64;    				 		 
        $tmp .= $chars[$j];    	 	  			
    }    	 		 		 
    return trim(base64_decode($tmp),$key);     	 					
}        	   
     		 	 	 
function ZBlogSEO_Url_query($key) {     	 	 	  
	global $zbp;    		  	 	 
	$c_url = preg_replace('/^url=(.*)$/i','$1',$_SERVER["QUERY_STRING"]);      	  			 
	    		   		 
	    	 		 	  
	if ($zbp->Config('ZBlogSEO')->links_base64 == 1) {    	 	   		
        $c_url = base64_decode($c_url);    	 	 	  	
    }else{     	   	  
        $c_url = ZBlogSEO_Url_Decrypt($c_url,$zbp->Config('ZBlogSEO_Url')->commentlink_key);       	    
    }    	  	  	 
	    				 			
	if (!CheckRegExp($c_url, '[homepage]')) {      	  	 	
		$c_url=$zbp->host;     	  	  	 
	}else{     	   				
		    			   		
	}     	 	 		 
	include 'redirect.php';     	    		
	exit();    		   		 
}     		    	
    		 	    
    		 				 