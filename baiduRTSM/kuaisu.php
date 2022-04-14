<?php /* EL PSY CONGROO */      	 	  	
     		   		
function ZBlogSEO_kuaisu_Post_Add($article){    		    	 
    global $zbp;    		  		 	
	if (GetVars('post_kuaisu', 'POST')== '1') {           	
		if ($article->Status == 0){    	 			   
			$urls=$article->Url;     		  	  
			$api = $zbp->Config('ZBlogSEO')->baidu_ksapi;        	  	
			$ajax = Network::Create();    	 		  		
			if (!$ajax) {    			 		  
				throw new Exception('主机没有开启网络功能');    		   		 
			}    				 	 	
			$ajax->open('POST', $api);    		 	 			
			$ajax->setRequestHeader('Content-Type', 'text/plain');    	  				 
			$ajax->send($urls);     			 	 	
			$result =json_decode($ajax->responseText);           	
			if (isset($result->remain_daily)) {        	  	
				if (isset($result->remain_daily)){          		
					$zbp->SetHint('good','快速收录已成功推送，今天剩余可推送到快速收录数据的url条数还有：'.$result->remain_daily);    				 	  
					$zbp->SetHint('good','快速收录已成功推送，新增内容的url条数：'.$result->success_daily);    	   	  	
				} else {       	 		 
					$zbp->SetHint('bad','快速收录已成功推送');    		 			  
				}    			 		  
			} elseif(isset($result->message)) {    		      
				$zbp->SetHint('bad','快速收录推送失败，错误描述：'.$result->message);      			  	
			} elseif(isset($result->not_same_site)) {    			  		 
				$zbp->SetHint('bad','快速收录推送失败，错误描述：由于不是本站url而未处理');    	  	  	 
			}       						
			else {        	   
				$zbp->SetHint('bad','快速收录推送失败');     	 	    
			}     	  	 	 
		}      	 		  
	}      		 			
}      	 	   
    		 	 	  
function ZBlogSEO_kuaisu_Post_Response3(){        	  	
    global $zbp,$article;     	  	 	 
	echo '<div id="alias" class="editmod"><label for="post_kuaisu" class="editinputname">快速收录</label><input type="text" name="post_kuaisu" class="checkbox" value="1"/></div>';        			 
}    	 			 	 
      	   	 
    		  			 