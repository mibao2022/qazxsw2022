<?php /* EL PSY CONGROO */    		 	   	
function ZBlogSEO_Title(&$templates){      				 	    				  		
	global $zbp,$article;      	 	 	 		
	if($zbp->Config("ZBlogSEO")->zbp_seo){	
	$templates['header'] = preg_replace("/<title>.+<\/title>/is",'{php}
		if($zbp->Config("ZBlogSEO")->separator){
		    $separator = $zbp->Config("ZBlogSEO")->separator;
	    }else{
		    $separator = " | ";
	    }
		$separatornav = " - ";  
		if($type =="index"){
		    if($page == "1"){
			    if($zbp->Config("ZBlogSEO")->homepage_title){
				    $dftitle = $zbp->Config("ZBlogSEO")->homepage_title;
			    }else{
				    $dftitle = $zbp->name.$separator.$zbp->subname;
			    }
		    }else{
			    if($zbp->Config("ZBlogSEO")->homepage_title){
				    $dftitle = $zbp->Config("ZBlogSEO")->homepage_title.$separatornav."第" .$page. "页";
			    }else{
				    $dftitle = $zbp->name.$separator.$zbp->subname.$separatornav."第".$page."页";
			    }
		    }
		    $keywords = $zbp->Config("ZBlogSEO")->homepage_keywords;
		    $description = $zbp->Config("ZBlogSEO")->homepage_description;
	    }
		elseif($type == "category"){
		    if ($page=="1") {
				if($category->Metas->cat_title){
                    $dftitle = $category->Metas->cat_title.$separator.$zbp->name;
				}else{
				    $dftitle = $category->Name.$separator.$zbp->name;
			    }
			} else {
				if($category->Metas->cat_title){
                    $dftitle = $category->Metas->cat_title.$separatornav."第".$page."页".$separator.$zbp->name;
				}else{
				    $dftitle = $category->Name.$separatornav."第".$page."页".$separator.$zbp->name;
			    }
            }
			if ($category->Metas->cat_keywords) {
		        $keywords = $category->Metas->cat_keywords;
			} else {
				$keywords = $category->Name;
			}
			$description = $category->Intro;
		}
		elseif($type == "article"){
			if ($article->Metas->cmp_seo_title) {
		        $dftitle = $article->Metas->cmp_seo_title.$separator.$zbp->name;
			} else {
				$dftitle = $article->Title.$separator.$zbp->name;
			}
			$aryTags = array();
			foreach($article->Tags as $key){
                $aryTags[] = $key->Name;
            }
			if ($article->Metas->cmp_seo_keywords) {
                $keywords = $article->Metas->cmp_seo_keywords;
            } else {
                $keywords = implode(",",$aryTags);
            }
			if ($article->Metas->cmp_seo_description) {
                $description = $article->Metas->cmp_seo_description;
            } else {
                $description = preg_replace("/[\r\n\s]+/", " ", trim(SubStrUTF8(TransferHTML($article->Content,"[nohtml]"),135))."...");
            }
		}
		elseif($type == "page"){
			if ($article->Metas->cmp_seo_title) {
		        $dftitle = $article->Metas->cmp_seo_title.$separator.$zbp->name;
			} else {
				$dftitle = $article->Title.$separator.$zbp->name;
			}
			if ($article->Metas->cmp_seo_keywords) {
                $keywords = $article->Metas->cmp_seo_keywords;
            } else {
                $keywords = $article->Title;
            }
            if ($article->Metas->cmp_seo_description) {
                $description = $article->Metas->cmp_seo_description;
            } else {
                $description = preg_replace("/[\r\n\s]+/", " ", trim(SubStrUTF8(TransferHTML($article->Content,"[nohtml]"),135))."...");
            }
		}
		elseif ($type == "tag"){
			if ($tag->Metas->tag_title) {
		        $dftitle = $tag->Metas->tag_title.$separator.$zbp->name;
			} else {
				$dftitle = $zbp->title.$separator.$zbp->name;
			}
			if ($tag->Metas->tag_keywords) {
		        $keywords = $tag->Metas->tag_keywords;
			} else {
				$keywords = $zbp->Config("ZBlogSEO")->homepage_keywords;
			}
			if ($tag->Intro) {
		        $description = $tag->Intro;
			} else {
				$description = $zbp->Config("ZBlogSEO")->homepage_description;
			} 
		}elseif($type == "search"){
			$dftitle = $title.$separator.$zbp->name;
			$keywords = $title;
			$description = preg_replace("/[\r\n\s]+/", " ", trim(SubStrUTF8(TransferHTML($article->Content,"[nohtml]"),135))."...");
        }	
		else {
            $dftitle = $zbp->title.$separator.$zbp->name;
            $keywords = $zbp->Config("ZBlogSEO")->homepage_keywords;
            $description = $zbp->Config("ZBlogSEO")->homepage_description;
        }
		echo "<title>".$dftitle."</title><meta name=\"keywords\" content=\"".$keywords."\" /><meta name=\"description\" content=\"".$description."\" />";{/php}',$templates['header']);
	}	         			 		 	
}      				  		
    	 		    