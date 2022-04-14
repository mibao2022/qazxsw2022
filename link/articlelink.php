<?php /* EL PSY CONGROO */      	  			
    		  	 	 
function ZBlogSEO_links_Encryption($txt,$key='www.fengyan.cc')    		 				 
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
     	 	 		 
function ZBlogSEO_links_Content(&$template){    		  	 	 
    global $zbp;      	  	 	
	$article = $template->GetTags('article');     		  		 
	$content = $article->Content;     	 			 	
	$host = $zbp->host;    		 					
	$flids = explode('|',$zbp->Config('ZBlogSEO')->discharge);     		     
	$guolv=array();        	 	 
	preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/',$content,$matchContent);    	  					
	    		 	 	 	
	foreach($flids as $flid){     		  	 	
		if($matchContent[2]){      	 		  
			foreach($matchContent[2] as $b){	    			 	   
				if(strpos($b,$flid)=== false){//不在     	      
				continue;    				  	 
				}else{    					  	
				$guolv[]=$b;     		    	
				     	  	   
				}    						  
			}     		 	   
			    		   		 
		}       					
		    		  				
	}       			  
	if($matchContent[2]){     		   	 
	$guolv=array_diff($matchContent[2],$guolv);     	      
	}    			 				
    if ($zbp->Config('ZBlogSEO')->links_base64 == 1) {    			     
    	foreach($guolv as $val){    	 		 	  
    		if(strpos($val,'://')!==false && strpos($val,$host)===false && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|tiff)/i',$val)){    		 	 		 
    			$content=str_replace("href=\"$val\"", "href=\"".$host."goto/?url=".base64_encode($val)."\" ",$content);    	 	 	 		
    		}    					  	
    	}    	  	  	 
    }else{        	  	
        foreach($guolv as $val){    	 		  	 
            if(strpos($val,'://')!==false && strpos($val,$host)===false && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|tiff)/i',$val)){    				  	 
                $content=str_replace("href=\"$val\"", "href=\"".$host."goto/?url=".ZBlogSEO_links_Encryption($val,$zbp->Config('ZBlogSEO_links')->articlelink_key)."\" ",$content);    		 	 		 
            }    			  	 	
        }     	  	 		
    }     		   	 
	$article->Content = $content;    		 		   
	$template->SetTags('article', $article);     	 			 	
}    		  	   
    			  	 	
function ZBlogSEO_links_Index_Begin() {    	 		  	 
    global $zbp;    	 	 	 	 
    $_router = 'goto';    	 		 	 	
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
    ZBlogSEO_links_query($_key);      	 			 
}    	  		 	 
    			 	   
function ZBlogSEO_links_Decrypt($txt,$key='www.fengyan.cc')     				   
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
    		  				
function ZBlogSEO_links_query($key) {    		 		 	 
	global $zbp;     	  	 	 
	$c_url = preg_replace('/^url=(.*)$/i','$1',$_SERVER["QUERY_STRING"]);     				 	 	
	     	 		 	 
    if ($zbp->Config('ZBlogSEO')->links_base64 == 1) {    							 
        $c_url = base64_decode($c_url);    		 	 			
    }else{    	  		 	 
        $c_url = ZBlogSEO_links_Decrypt($c_url,$zbp->Config('ZBlogSEO_links')->articlelink_key);     				 	 
    }    	  	   	
	include 'redirect.php';         	 	
	exit();     			  	 
}    		      
    	  	 	 	
    		      