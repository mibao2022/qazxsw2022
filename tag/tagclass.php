<?php /* EL PSY CONGROO */     		 		  
       	 		 
class ZBlogSEO_KeyReplace {     						 
	private $keys = array();    	 		  	 
	private $text = "";       	   	
	private $runtime = 0;    	 		    
	private $url = true;         		 
	private $stopkeys = array();          	 
	private $all = false;      	 		  
       	 		 
	public function __construct($text='',$keys=array(),$url=true,$stopkeys=array(),$all=false) {    		 	 			
		$this->keys = $keys;      				 	
		$this->text = $text;    	  			  
		$this->url = $url;    		      
		$this->stopkeys = $stopkeys;     	   	 	
		$this->all = $all;      	  		 
	}    		   	  
    						 	
	public function getResultText() {    	 				 	
		global $zbp;    					 		
		$start = microtime(true);    			 	   
		$keys = $this->hits_keys();      	 		  
		$keys_tmp = array_keys($keys);     		 		  
		function cmp($a, $b){      					 
			if (mb_strlen($a) == mb_strlen($b)) {       	 		 
				return 0;            
			}    		 	 			
			return (mb_strlen($a) < mb_strlen($b)) ? 1 : -1;     		  			
		}    	   	 		
       		 	 
	usort($keys_tmp,"cmp");    	  	 		 
         	  
	foreach($keys_tmp as $i=>$key) {    	  	    
		/**/    		 				 
		$ki = $i + 2;     	    		
		if($ki>$zbp->Config('ZBlogSEO')->tag_num_shu){     				 		
			break;    	    			
		}    			 	  	
		/**/     	   		 
		if(is_array($keys[$key])) {     	  	 		
			$url = $keys[$key][rand(0,count($keys[$key])-1)];    		 	  		
		}      	    	
		else    		 	 	  
			$url = $keys[$key];      			   
			$this->text = $this->r_s($this->text,$key,$url);     			 		 
		}    			 		 	
		$this->runtime = microtime(true)-$start;     	 			 	
		return $this->text;    	 	 	   
	}    	 					 
     		 		 	
	public function getRuntime() {       		 		
		return $this->runtime;    		 				 
	}    	 	  		 
    		  			 
	public function setKeys($keys) {     					  
		$this->keys = $keys;     					 	
	}    		   			
    	 		 	 	
	public function setStopKeys($keys) {     	  	  	
		$this->stopkeys = $keys;    	  		 	 
	}     	 	  		
      	  		 
	public function setText($text) {    		  		  
		$this->text = $text;    	  	  	 
	}        				
    	 		 			
	public function hits_keys(){     		 		  
		$ar = $this->keys;        	  	
		$ar = $ar?$ar:array();    	  		  	
		$result=array();    	 		 	 	
		$str = $this->text;        	 	 
        	   	   
	foreach($ar as $k=>$url){    	  		 		
		$k = trim($k);     	   		 
		if(!$k)    		    		
		continue;    		 	 	 	
			if(strpos($str,$k)!==false && !in_array($k,$this->stopkeys)){    	 				  
				$result[$k] = $url;         	 	
			}    	  			 	
		}    				    
		return $result?$result:array();       	 	  
	}    	 	 	 	 
         	 	
	public function hits_stop_keys(){     		 			 
		$ar = $this->stopkeys;    						  
		$ar = $ar?$ar:array();       	  	 
		$result=array();    	 			  	
		$str = $this->text;     	  			 
			foreach($ar as $k){      	  		 
				$k = trim($k);     	 	 			
				if(!$k)     	 		   
				continue;    	 			  	
				if(strpos($str,$k)!==false && in_array($k,$this->stopkeys)){    			 			 
					$result[] = $k;     				   
				}    	 		 	  
			}        				
		return $result?$result:array();    			  		 
	}    	   	  	
     		    	
	//处理替换过程     		    	 
	private function r_s($text,$key,$url){    			  			
		global $zbp;      	   	 
		$tmp = $text;     	  	 	 
		$stop_keys = $this->hits_stop_keys();     	 		 		
		$stopkeys = $tags = $pre = $img = $a = array();       				 
		if(preg_match_all("#<a[^>]+>[^<]*</a[^>]*>#su",$tmp,$m)){    	 	 	 		
			$a=$m[0];    	  	    
			foreach($m[0] as $k=>$z){      		    
				$z = preg_replace("#\##s","\#",$z);     		  	  
				$tmp = preg_replace("#<a[^>]+>[^<]*</a[^>]*>#su","[_a".$k."_]",$tmp,1);    	  	  		
			}     		 			 
		};      			   
		if(preg_match_all("#<pre[^>]+>[^<]*</pre[^>]*>#su",$tmp,$m)){    		  	 	 
			$pre=$m[0];      	   	 
			foreach($m[0] as $k=>$z){     	  				
				$z = preg_replace("#\##s","\#",$z);    	 	    	
				$tmp = preg_replace("#<pre[^>]+>[^<]*</pre[^>]*>#su","[_pre".$k."_]",$tmp,1);     	   	 	
			}    	  	  		
		};     		  			
		/*    			   		
		if(preg_match_all("#<img\s+.*?[\/]?>#su",$tmp,$m)){
			$img=$m[0];
			foreach($m[0] as $k=>$z){
				$z = preg_replace("#\##s","\#",$z);
				$tmp = preg_replace("#<img\s+.*?[\/]?>#su","[_img".$k."_]",$tmp,1);
			}
		}; 
		
		if(preg_match_all("#<[^>]+>#s",$tmp,$m)){
			$tags = $m[0];
			foreach($m[0] as $k=>$z){
				 $z = preg_replace("#\##s","\#",$z);
				$tmp = preg_replace('#'.$z.'#s',"[_tag".$k."_]",$tmp,1);
			}
		}
		*/
		if(!empty($stop_keys)){
			if(preg_match_all("#".implode("|",$stop_keys)."#s",$tmp,$m)){
				$stopkeys = $m[0];
				foreach($m[0] as $k=>$z){
					$z = preg_replace("#\##s","\#",$z);
					$tmp = preg_replace('#'.$z.'#s',"[_s".$k."_]",$tmp,1);
				}
			}
		}
		$key1 = preg_replace("#([\#\(\)\[\]\*])#s","\\\\$1",$key);
		if($this->url){
			if ($zbp->Config('ZBlogSEO')->tag_color){
				$tag_color = 'style="color: '.$zbp->Config('ZBlogSEO')->tag_color.';"'; 
			}else{
				$tag_color = '';
			}
			//$tmp = preg_replace("#(?!\[_s|\[_a|\[_|\[_t|\[_ta|\[_tag)".$key1."(?!ag\d+_\]|g\d+_\]|\d+_\]|s\d+_\]|_\])#us",'<a href="'.$url.'" class="tooltip-trigger tin" '.$tag_color.' title="查看更多关于 '.$key.' 的文章" target="_blank">'.$key.'</a>',$tmp,$zbp->Config('ZBlogSEO')->tag_shu); //替换
			$tmp = preg_replace("#(?!((<.*?)|(<a.*?)))(".$key1.")(?!(([^<>]*?)>)|([^>]*?<\/a>))#us",'<a href="'.$url.'" class="tooltip-trigger tin" '.$tag_color.' title="查看更多关于 '.$key.' 的文章" target="_blank">'.$key.'</a>',$tmp,$zbp->Config('ZBlogSEO')->tag_shu); //替换
		}else{
			 $tmp = preg_replace("#(?!\[_s|\[_a|\[_|\[_t|\[_ta|\[_tag)".$key1."(?!ag\d+_\]|g\d+_\]|\d+_\]|s\d+_\]|_\])#us",$url,$tmp,$this->all?-1:1); //无连接替换
		}
		if(!empty($a)){
			foreach($a as $n=>$at){
				$tmp = str_replace("[_a".$n."_]",$at,$tmp);
			}    
		}   
		if(!empty($pre)){
			foreach($pre as $n=>$at){
				$tmp = str_replace("[_pre".$n."_]",$at,$tmp);
			}    
		}   
		if(!empty($tags)){
			foreach($tags as $n=>$at){
				$tmp = str_replace("[_tag".$n."_]",$at,$tmp);
			}    
		}  
		/*
		if(!empty($img)){
			foreach($img as $n=>$at){
				$tmp = str_replace("[_img".$n."_]",$at,$tmp);
			}    
		} 
		*/
		if(!empty($stopkeys)){
			foreach($stopkeys as $n=>$at){
				$tmp = str_replace("[_s".$n."_]",$at,$tmp);
			}    
		}   
		return $tmp;
	}
}

?>