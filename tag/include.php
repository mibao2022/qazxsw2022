<?php /* EL PSY CONGROO */    	  	 	  
function ZBlogSEO_ViewPost_Tag_Main(&$template) {     			    
	global $zbp;    		 	    
	if ($zbp->Config('ZBlogSEO')->taglink){    	  		  	
		$article = $template->GetTags('article');      			 		
		$arrkey = ZBlogSEO_GetKeyWord() + ZBlogSEO_GetTags();          	 
	    $str = $article->Content;      	 	 	 
	    $key = new ZBlogSEO_KeyReplace($str, $arrkey);     		 			  
		$article->Content = $key->getResultText();    	 	  	 	
	}      	 	   
}       	 	  
      			   
function ZBlogSEO_GetTags() {    		  		  
	global $zbp;      			 	 
	$result = array();      		  	 
	$Namestr = $Urlstr = '';     	   	  
	$array = $zbp->GetTagList('','',array('tag_Count'=>'DESC'),1000,'');    	 	 		 	
	shuffle($array);      			  		
	foreach ($array as $key => $tag) {    	    	  
		$result[$tag->Name] = $tag->Url;     	 		  	
	}     	    	 
	return $result;     		 		 	
}     	 	   	
      			 	 
function ZBlogSEO_GetKeyWord() {    	   	   
    global $zbp;    	 			 		
    $result = array();          		
	$where = array(array('=','KeyWord_Type','0'),array('=','KeyWord_IsUsed','1'));    		   			
	$order = array('KeyWord_IsUsed'=>'DESC','KeyWord_ID'=>'ASC');    			  		 
      	    	
    $sql = $zbp->db->sql->Select($zbp->table['ZBlogSEOTag'], '*', $where, $order, null, null);    	 		 	 	
    $array = $zbp->GetListCustom($zbp->table['ZBlogSEOTag'], $zbp->datainfo['ZBlogSEOTag'], $sql);     	 	 	 	
    foreach ($array as $key => $reg) {    	     	 
     	  	  	
        $result[$reg->Title] = $reg->Url;      				  
    }    		 				 
     	   		 
    return $result;    	 	 	  	
}    			 	  	
      	   		
function ZBlogSEO_Tag_CreateTable() {     	 	 		 
    global $zbp;    		 		  	
    if ($zbp->db->ExistTable($GLOBALS['table']['ZBlogSEOTag']) == false) {      		 	  
        $s = $zbp->db->sql->CreateTable($GLOBALS['table']['ZBlogSEOTag'], $GLOBALS['datainfo']['ZBlogSEOTag']);     			 			
        $zbp->db->QueryMulit($s);    		 			  
    }     		 			 
}     	 		   
     		 	 	 
function ZBlogSEO_Tag_DelTable() {     		  	 	
    global $zbp;    					 		
    if ($zbp->db->ExistTable($zbp->table['ZBlogSEOTag']) == true) {    	  			  
        $s = $zbp->db->sql->DelTable($zbp->table['ZBlogSEOTag']);      			   
        $zbp->db->QueryMulit($s);     		 			 
    }    	     		
}      	 	 	 
     	    	 
      	  		 
?>