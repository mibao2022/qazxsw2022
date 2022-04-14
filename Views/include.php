<?php /* EL PSY CONGROO */    		 		 		
      	  			
function ZBlogSEO_Edit_Response() {    	   				
	global $zbp,$article;      				  
	echo '<div id=\'edtViewNums\' class="editmod"> <label for="edtViewNums" class="editinputname" > 浏览量 </label>';    	   	   
    echo '<input type="text" name="ViewNums" id="edtViewNums"  value="' . $article->ViewNums . '" style="width:171px;"/>';         		 
    echo '</div>';     		 		 	
}    	  	   	
       	 	 	
function ZBlogSEO_ViewNums(&$template) {     	   			
	global $zbp;      	  			
	if($zbp->Config('ZBlogSEO')->ViewNumsOnOff) {    				 	  
		$article = $template->GetTags('article');     	 	   	
		$article->ViewNums += mt_rand(intval($zbp->Config('ZBlogSEO')->ViewNumsStart),intval($zbp->Config('ZBlogSEO')->ViewNumsEnd));    	  			  
		$sql = $zbp->db->sql->Update($zbp->table['Post'], array('log_ViewNums' => $article->ViewNums), array(array('=', 'log_ID', $article->ID)));    		  		 	
		$zbp->db->Update($sql);     	     	
	}    	  		   
}     	  			 
    		 	  	 
function ZBlogSEO_PostArticle_Core2(&$article) {      	 			 
	global $zbp;    		   	  
	if($zbp->Config('ZBlogSEO')->SaveOnOff) {      	 	 	 
		if($article->ViewNums == 0 || $article->ViewNums == '') {      			 		
			$article->ViewNums = mt_rand(intval($zbp->Config('ZBlogSEO')->SaveStart),intval($zbp->Config('ZBlogSEO')->SaveEnd));     	   	  
		}      	 		 	
	}         	 	
}    				 	 	
    	  	  		
    	     		
     	      
     	 	   	