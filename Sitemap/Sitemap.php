<?php /* EL PSY CONGROO */ 	     				  	 
function ZBlogSEO_sitemap_html($article){     					 	     		 	 		
	global $zbp;      	 	  	     	  	 	 
	if ($zbp->Config('ZBlogSEO')->Enabled_HTML_Sitemap){      						    	 			 		
		$name = $zbp->Config('ZBlogSEO')->XML_FileName;    	           	 	 		  
		$number = $zbp->Config('ZBlogSEO')->sitemap_number;      	         				    
		$s = '';      	   		    		   			
		$s .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head profile="http://gmpg.org/xfn/11"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><title>'.$name.' - '.$zbp->name.'</title><meta name="copyright" content="FengYan" /><style type="text/css">body {font-family: Verdana;FONT-SIZE: 12px;MARGIN: 0;color: #000000;background: #ffffff;}img {border:0;}li {margin-top: 8px;}.page {padding: 4px; border-top: 1px #EEEEEE solid}.author {background-color:#EEEEFF; padding: 6px; border-top: 1px #ddddee solid}#nav, #content, #footer {padding: 8px; border: 1px solid #EEEEEE; clear: both; width: 95%; margin: auto; margin-top: 10px;}</style></head><body vlink="#333333" link="#333333"><h2 style="text-align: center; margin-top: 20px">'.$zbp->name.' SiteMap </h2><center></center><div id="nav"><a href="'.$zbp->host.'"><strong>'.$zbp->name.'</strong></a>  &raquo; '.$name.'</div>';    	 	   		     	   	 	
		if($zbp->Config('ZBlogSEO')->post_select){    	   	       	   		 	
			$s .= '<div id="content"><h3>最新文章</h3><ul>';    			    	    			  	 	
			$array=GetList($count = $number, $cate = null, $auth = null, $date = null, $tags = null, $search = null, $option = null);      	 		 	     						 
			foreach ($array as $key => $value) {     	 			      			  	  
				$s .= '<li><a href="'.$value->Url.'" title="'.$value->Title.'" target="_blank">'.$value->Title.'</a></li>';    		 	 		     				 	 	
			}     	 			 	    		 			  
			$s .= '</ul></div>';      	 	        		 	 		
		}     	   			     		 		 	
		$s .= '<div id="content">';     			 			     			 	  
		if($zbp->Config('ZBlogSEO')->category_select){    	  		 	        	 			
			$catalogs=$zbp->GetCategoryList(    	 	 		 	           	
			array('*'),    		 	  	     	  		  	
			null);        	  	    		  	 		
			$s.='<li class="categories">分类目录<ul>';     					           		 
			foreach ($catalogs as $category) {    		 					    	 			 	 
			  $s.='<li class=""><a href="'.$category->Url.'">'.$category->Name.'</a></li>';     		 	 		    	   	 		
			}    	   	  	     		 	 		
			$s .= '</ul></li>';     		  			       	    
		}       		         		 	  
		if($zbp->Config('ZBlogSEO')->page_select){    	 	  	        	   		
			$articles=$zbp->GetPageList(array('*'),array(array('=','log_Type',1),array('=','log_Status',0)),null,20,null );    	  		 		    		  				
			$s.='<li class="pagenav">页面<ul>';    	   	       	    	 	
			foreach ($articles as $article) {    		          			    	
			  $s.='<li class=""><a href="'.$article->Url.'">'.$article->Title.'</a></li>';    	  	 	 	    			 	   
			}    	            			 	 	
			$s .= '</ul></li>';    				  		     	    		
		}	        		 	    	  	  		
		$s .= '</div><div id="footer">查看网站首页: <strong><a href="'.$zbp->host.'">'.$zbp->name.'</a></strong></div><br /></body></html>';     		  	      	  	  		
		file_put_contents($zbp->path . ''.$name.'.html', $s);      						     		  			
	}      	 	       			  	 	
	if ($zbp->Config('ZBlogSEO')->Enabled_XML_Sitemap){    	 		 	      	 		 	  
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><urlset />');
		$xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$url = $xml->addChild('url');
		$url->addChild('loc', $zbp->host);
		$url->addChild('lastmod',date('c'));	
		$url->addChild('changefreq', $zbp->Config('ZBlogSEO')->home_frequency);
		$url->addChild('priority', $zbp->Config('ZBlogSEO')->home_priority);
		if($zbp->Config('ZBlogSEO')->category_select){
			foreach ($zbp->categorys as $c) {
				$url = $xml->addChild('url');
				$url->addChild('loc', $c->Url);
				$url->addChild('lastmod',date('c'));	
				$url->addChild('changefreq', $zbp->Config('ZBlogSEO')->category_frequency);
				$url->addChild('priority', $zbp->Config('ZBlogSEO')->category_priority); 
			}
		}
		if($zbp->Config('ZBlogSEO')->post_select){
			$sitemap_number=$zbp->Config('ZBlogSEO')->sitemap_number;
			$array=GetList($count = $sitemap_number, $cate = null, $auth = null, $date = null, $tags = null, $search = null, $option = null);
			foreach ($array as $key => $value) {
				$url = $xml->addChild('url');
				$url->addChild('loc', $value->Url);
				$url->addChild('lastmod',date('c',$value->PostTime));	
				$url->addChild('changefreq', $zbp->Config('ZBlogSEO')->post_frequency);
				$url->addChild('priority', $zbp->Config('ZBlogSEO')->post_priority);
			}
		}
		if($zbp->Config('ZBlogSEO')->page_select){
			$array=$zbp->GetPageList(
				null,
				array(array('=','log_Status',0)),
				null,
				null,
				null
				);

			foreach ($array as $key => $value) {
				$url = $xml->addChild('url');
				$url->addChild('loc', $value->Url);
				$url->addChild('lastmod',date('c',$value->PostTime));	
				$url->addChild('changefreq', $zbp->Config('ZBlogSEO')->page_frequency);
				$url->addChild('priority', $zbp->Config('ZBlogSEO')->page_priority);
			}
			
		}
		if($zbp->Config('ZBlogSEO')->tag_select){
			$array=$zbp->GetTagList();

			foreach ($array as $key => $value) {
				$url = $xml->addChild('url');
				$url->addChild('loc', $value->Url);
				$url->addChild('lastmod',date('c'));	
				$url->addChild('changefreq', $zbp->Config('ZBlogSEO')->tag_frequency);
				$url->addChild('priority', $zbp->Config('ZBlogSEO')->tag_priority);
			}
		}
		if($zbp->Config('ZBlogSEO')->XML_FileName == 'sitemap_baidu'){
			file_put_contents($zbp->path . 'sitemap_baidu.xml',$xml->asXML());
			if(is_file($zbp->path . 'sitemap.xml')){
			unlink($zbp->path . 'sitemap.xml');
			}
		}else{
			file_put_contents($zbp->path . 'sitemap.xml',$xml->asXML());
			if(is_file($zbp->path . 'sitemap_baidu.xml')){
				unlink($zbp->path . 'sitemap_baidu.xml');
			}
		}
	}
	if ($zbp->Config('ZBlogSEO')->Enabled_TXT_Sitemap){
		$name = $zbp->Config('ZBlogSEO')->XML_FileName;
		$number = $zbp->Config('ZBlogSEO')->sitemap_number;
		$s = '';
		$s .= ''.$zbp->host.''. "\r\n";
		if($zbp->Config('ZBlogSEO')->post_select){
			$array=GetList($count = $number, $cate = null, $auth = null, $date = null, $tags = null, $search = null, $option = null);
			foreach ($array as $key => $value) {
				$s .= ''.$value->Url.''. "\r\n";
			}
		}
		if($zbp->Config('ZBlogSEO')->category_select){
			$catalogs=$zbp->GetCategoryList(
			array('*'),
			null);
			foreach ($catalogs as $category) {
			  $s.=''.$category->Url.''. "\r\n";
			}
			
		}
		if($zbp->Config('ZBlogSEO')->page_select){
			$articles=$zbp->GetPageList(array('*'),array(array('=','log_Type',1),array('=','log_Status',0)),null,20,null );
			foreach ($articles as $article) {
			  $s.=''.$article->Url.''. "\r\n";
			}
			
		}	
		$text="\xEF\xBB\xBF".$s;
    	file_put_contents($zbp->path . ''.$name.'.txt', $text);
	}
 }


?>