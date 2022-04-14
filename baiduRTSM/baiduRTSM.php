<?php /* EL PSY CONGROO */     		  	 	
if($zbp->Config('ZBlogSEO')->baidurtsm){     		    	
	function ZBlogSEO_edit_response3() {    	  			 	
		?>
	<div id="ZBlogSEO_baiduRTSM" class="editmod">
	  <label for="edtZBlogSEO_baiduRTSM" class="editinputname">百度推送:</label>
	  <input id="edtZBlogSEO_baiduRTSM" name="ZBlogSEO_baiduRTSM" type="text" value="1" class="checkbox"/>
	</div>
	<?php
	}    	 	 	 	 
	     			  	 
	function ZBlogSEO_post_article_succeed(&$article) {    	  			  
		global $zbp;     	  		 	
		if (GetVars('ZBlogSEO_baiduRTSM', 'POST')== '1') {     		  	  
			if ($article->Status == 0){    	 				  
				$urls=$article->Url;      	 	 		
				$api = $zbp->Config('ZBlogSEO')->baidu_data;    					  	
				$ajax = Network::Create();    				    
				if (!$ajax) {     			    
					throw new Exception('主机没有开启网络功能');    			 				
				}     		 	  	
				$ajax->open('POST', $api);    		   		 
				$ajax->setRequestHeader('Content-Type', 'text/plain');    	    	 	
				$ajax->send($urls);    					  	
				$result =json_decode($ajax->responseText);     	   		 
				if (isset($result->remain)) {    					  	
					if (isset($result->remain)){     					  
						$zbp->SetHint('good','百度主动推送成功，剩余'.$result->remain.'条数据可提交');     		 		 	
					} else {    	 	 	  	
						$zbp->SetHint('bad','百度主动推送成功');     			 		 
					}        				
				} elseif(isset($result->not_same_site)) {    				 	 	
					$zbp->SetHint('bad','推送失败，由于不是本站Url而未处理，Url:'.$result->not_same_site[0].'');     	    		
				} elseif(isset($result->not_valid)) {       	 	 	
					$zbp->SetHint('bad','推送失败，不合法的Url，Url:'.$result->not_valid[0].'');      						
				} elseif(isset($result->error)) {    	  			  
					$zbp->SetHint('bad','推送失败，错误代码：'.$result->error.'，错误描述：'.$result->message.'');     			 		 
				} else {    	 	   		
					$zbp->SetHint('bad','推送失败，未知错误!请检查推送接口是否正确、主机是否开启网络功能');      		 	 	
				}     		 	  	
			}    	 	 	   
		}    		 	  	 
	}     	 			 	
}    	  					
    	   	  	
function ZBlogSEO_MakeTemplatetags_bdstatic(){     	 		 	 
	global $zbp;       				 
	if($zbp->Config('ZBlogSEO')->baidurtsm_js){
		$zbp->footer .= '<script>
		(function(){
			var bp = document.createElement(\'script\');
			var curProtocol = window.location.protocol.split(\':\')[0];
			if (curProtocol === \'https\'){
		   bp.src = \'https://zz.bdstatic.com/linksubmit/push.js\';
		  }
		  else{
		  bp.src = \'http://push.zhanzhang.baidu.com/push.js\';
		  }
			var s = document.getElementsByTagName("script")[0];
			s.parentNode.insertBefore(bp, s);
		})();
		</script>' . "\r\n";
	}      		 		 
}    		  		 	
     	 	  	 
     					 	