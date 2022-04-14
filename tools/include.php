<?php /* EL PSY CONGROO */     	  	 	 
    				 	 	
function ZBlogSEO_MakeTemplatetags_blank(){      				 	
	global $zbp;    				   	
	if($zbp->Config('ZBlogSEO')->zbp_blank){      				  
		$zbp->header .= '<base target="_blank">' . "\r\n";    		 	 	  
	}    				  		
	if($zbp->Config('ZBlogSEO')->zbp_ico){    	    	 	
		$zbp->header .= '<link rel="shortcut icon" href="'.$zbp->Config('ZBlogSEO')->zbp_ico_img.'" type="image/x-icon" />' . "\r\n";    					  	
	}      		 		 
	if($zbp->Config('ZBlogSEO')->zbp_copyright_hui){     	 		   
		$zbp_copyright_hui = '*{filter: grayscale(100%);-webkit-filter: grayscale(100%);-moz-filter: grayscale(100%);-ms-filter: grayscale(100%);-o-filter: grayscale(100%);}';        			 
	}else{    		 				 
		$zbp_copyright_hui = '';    		   		 
	}        	 		
	if($zbp->Config('ZBlogSEO')->zbp_copyright_tu){    	   			 
		$zbp_copyright_tu_css = 'img{pointer-events: none; -webkit-user-select: none;-moz-user-select: none;-webkit-user-select:none;  -o-user-select:none;user-select:none;}';    	 			 		
		$zbp_copyright_tu_js = 'function imgdragstart(){return false;}for(i in document.images)document.images[i].ondragstart=imgdragstart;';	      				  
	}else{     			 	 	
		$zbp_copyright_tu_css = '';     			  	 
		$zbp_copyright_tu_js = '';    	 			  	
	}     	      
	if($zbp->Config('ZBlogSEO')->zbp_copyright_f12){    				  		
		$zbp_copyright_f12 = '$(document).keydown(function(){return key(arguments[0])});function key(e){var keynum;if(window.event){keynum=e.keyCode;}else if(e.which){keynum=e.which;} if(keynum==123){window.close();return false;}}' . "\r\n";     	  				
	}else{     	 	  		
		$zbp_copyright_f12 = '';      	  	  
	}           	
	if($zbp->Config('ZBlogSEO')->zbp_copyright_fuzhi){    			 				
		$zbp_copyright_fuzhi= '*{-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;-khtml-user-select: none;user-select: none; }' . "\r\n";     		 		  
	}else{      		 	  
		$zbp_copyright_fuzhi = '';    	 			 	 
	}    	 	 	   
	if($zbp->Config('ZBlogSEO')->zbp_copyright_you){     		 	 		
		$zbp_copyright_you = 'function stop(){return false;}document.oncontextmenu=stop;' . "\r\n";     	 		  	
	}else{    		 	 		 
		$zbp_copyright_you = '';    	 		   	
	}         	  
	if($zbp->Config('ZBlogSEO')->zbp_copyright_f5){     	    	 
		$zbp_copyright_f5 = 'document.onkeydown = ppPressF5; function ppPressF5(){if(event.keyCode==82&&event.ctrlKey){event.keyCode=0;event.returnValue=false;return false}if(event.keyCode==62&&event.ctrlKey){event.keyCode=0;event.returnValue=false;return false}if(event.keyCode==68&&event.ctrlKey){event.keyCode=0;event.returnValue=false;return false}if(event.keyCode==77&&event.ctrlKey){event.keyCode=0;event.returnValue=false;return false}if(event.keyCode==82&&event.ctrlKey){event.keyCode=0;event.returnValue=false;return false}if(event.keyCode==85&&event.ctrlKey){event.keyCode=0;event.returnValue=false;return false}if(event.keyCode==116){event.keyCode=0;event.returnValue=false;return false}}' . "\r\n";        	 	 
	}else{     	   	  
		$zbp_copyright_f5 = '';    			 			 
	}      	 		 	
	if($zbp->Config('ZBlogSEO')->zbp_copyright_hui || $zbp->Config('ZBlogSEO')->zbp_copyright_tu_css || $zbp->Config('ZBlogSEO')->zbp_copyright_fuzhi){    	 						
		$zbp->header .= '<style>'.$zbp_copyright_hui.''.$zbp_copyright_tu_css.''.$zbp_copyright_fuzhi.'</style>' . "\r\n";     	 			   
	}    	  	 	  
	if($zbp->Config('ZBlogSEO')->zbp_copyright_tu_js || $zbp->Config('ZBlogSEO')->zbp_copyright_f12 || $zbp->Config('ZBlogSEO')->zbp_copyright_f5){     		 			 
		$zbp->footer .= '<script language="javascript">'.$zbp_copyright_tu_js.''.$zbp_copyright_f12.''.$zbp_copyright_you.''.$zbp_copyright_f5.'</script>' . "\r\n";    					 		
	}    	 	  	 	
	if($zbp->Config('ZBlogSEO')->zbp_copyright_kuangjia){    							 
		$zbp->header .= ''.header ("X-FRAME-OPTIONS:DENY").'' . "\r\n";    		 	 		 
	}      		   	
}    	 	  			
    				 	 	
//标题重复检测 流年      		    
function ZBlogSEO_Filter_Plugin_Cmd_Ajax(){       		 		
	global $zbp;     		 				
	if($zbp->Config('ZBlogSEO')->zbp_post_article_title){     	 	 	  
		$action 	=	GetVars('src', 'GET');     	  	 		
		$array  	=	array();     		     
		$ZBlogSEO	=	array();    			 		  
		$c			=	array();    		  	   
		$title=trim(GetVars('title', 'POST'));    		 	 	 	
		$id=trim(GetVars('id', 'POST'));     			    
		if($title!=''){    	 	  	  
			$ZBlogSEO[] = 'ZBlogSEO_verify_post_title';     	 	 	 	
			$c=$zbp->GetPostList('',array(array('=','log_Title',$title),array('<>','log_ID',$id)));     	   	 	
			if(count($c)>0){     	    	 
				$array['msg'] = '文章列表已存在相同标题的文章';    	  		 		
			}    					 		
			if (in_array($action,$ZBlogSEO)) {    		  		  
				if (count($array) > 0) {    	   				
					echo json_encode($array);    	  		  	
				}      	     
			}    	     	 
		}    	   			 
	}     	   	  
}    	  	 	 	
function ZBlogSEO_Filter_Plugin_Admin_Footer(){        		  
	global $zbp;    	 			   
	if($zbp->Config('ZBlogSEO')->zbp_post_article_title){          	 
		$act=GetVars('act', 'GET');     			   	
		$act_array=array('ArticleEdt');    						 	
		if(in_array($act,$act_array)){    		  	  	
			echo '<script src="' . $zbp->host . 'zb_users/plugin/ZBlogSEO/tools/ArticleEdt.js" type="text/javascript"></script>' . "\r\n";    	 		    
		}     	    	 
	}    								
}     		 	   
    		  		  
//文章自动别名    	   		  
function ZBlogSEO_PostArticle_Core(&$article) {    		 	  		
	global $zbp;      	 		  
	if($zbp->Config('ZBlogSEO')->zbp_post_bieming){    		 	 		 
		if ($article->Alias == null){    	  	    
			$article->Alias = md5($article->Time("YmdHis"));	    	 	 			 
		}    								
	}    		  	   
}    	      	
     						 
      	 	  	
    	 	 	 	 
    	   	 	 
?>