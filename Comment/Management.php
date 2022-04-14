<?php /* EL PSY CONGROO */    		 		  	
	function ZBlogSEO_Comment_SubMenu(){    	 			 	 
		global $zbp;    	 	   		
		$url = $_SERVER['PHP_SELF'];    	 	 			 
		$filename1 = explode('/',$url);    	   	 	 
		$filename = end($filename1);     		 				
		echo '<a href="'. $zbp->host .'zb_users/plugin/ZBlogSEO/Comment/main.php"><span class="m-left ' . ($filename=='main.php'||'comment_edit.php'?'m-now':'') . '"">评论管理</span></a>';     				 	 
		echo '<a href="'. $zbp->host .'zb_users/plugin/ZBlogSEO/Comment/main.php?act=CommentMng&amp;ischecking=1"><span class="m-left '.(GetVars('ischecking') ? 'm-now' : '').'">审核评论</span></a>';     	 	  		
		echo '<a href="http://www.fengyan.cc/" title="烽烟工作室" target="_blank"><span class="m-right" style="color:#F00">帮助</span></a>';    		 		 	 
	}     		 				
      		 			
################################################################################################################    			 		 	
/**     	      
 * 后台评论管理    	 		  	 
 */    	  	  		
     	  	   
	function ZBlogSEO_CommentMng() {    			 				
     	   	  
    global $zbp;      		    
        	 	 
    echo '<div class="divHeader">' . $zbp->lang['msg']['comment_manage'] . '</div>';          	 
    echo '<div class="SubMenu">';    		  	  	
    ZBlogSEO_Comment_SubMenu();    		   			
    echo '</div>';     				 		
    echo '<div id="divMain2">';    			 	   
    	 	 	  	
    echo '<form class="search" id="search" method="post" action="#">';    		  	 	 
    echo '<p>' . $zbp->lang['msg']['search'] . '&nbsp;&nbsp;&nbsp;&nbsp;<input name="search" style="width:450px;" type="text" value="" /> &nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" value="' . $zbp->lang['msg']['submit'] . '"/></p>';    	  	  	 
    echo '</form>';    		    		
   echo '<form method="post" action="' . $zbp->host . 'zb_system/cmd.php?act=CommentBat">';        			 
    echo '<input type="hidden" name="csrfToken" value="' . $zbp->GetCSRFToken() . '">';       			 	
    		 	 		 
    $p = new Pagebar('{%host%}zb_users/plugin/ZBlogSEO/Comment/main.php?act=CommentMng{&page=%page%}{&ischecking=%ischecking%}{&search=%search%}', false);    	    	  
    $p->PageCount = $zbp->managecount;    					   
    $p->PageNow = (int) GetVars('page', 'GET') == 0 ? 1 : (int) GetVars('page', 'GET');    		 		   
    $p->PageBarCount = $zbp->pagebarcount;    		 			  
    			 	   
    $p->UrlRule->Rules['{%search%}'] = rawurlencode(GetVars('search'));    	  	 			
    $p->UrlRule->Rules['{%ischecking%}'] = (boolean) GetVars('ischecking');       					
     	 	  		
    $w = array();    		  		  
    if (!$zbp->CheckRights('CommentAll')) {      			 	 
        $w[] = array('=', 'comm_AuthorID', $zbp->user->ID);    	  	    
    }      	     
    if (GetVars('search')) {    	 	   	 
        $w[] = array('search', 'comm_Content', 'comm_Name', GetVars('search'));    				 		 
    }    				 	  
    if (GetVars('id')) {      		 	 	
        $w[] = array('=', 'comm_ID', GetVars('id'));         		 
    }     		 	  	
    		 	  	 
    $w[] = array('=', 'comm_Ischecking', (int) GetVars('ischecking'));    	 			  	
    			   	 
    $s = '';     			    
    $or = array('comm_ID' => 'DESC');    	 	 	  	
    $l = array(($p->PageNow - 1) * $p->PageCount, $p->PageCount);      		   	
    $op = array('pagebar' => $p);    	 		 		 
     	 		   
    foreach ($GLOBALS['hooks']['Filter_Plugin_LargeData_Comment'] as $fpname => &$fpsignal) {      			 		
        $fpreturn = $fpname($s, $w, $or, $l, $op);    	 	    	
    }    	 		    
     	 	   	
    $array = $zbp->GetCommentList(      		   	
        $s,    	  				 
        $w,    	    	  
        $or,    	 	 	  	
        $l,      		  	 
        $op     		  	  
    );    	 			 		
     	   	  
    echo '<table border="1" class="tableFull tableBorder tableBorder-thcenter table_hover table_striped">';     	  	 	 
    $tables = '';       	 			
    $tableths = array();    		   		 
    $tableths[] = '<tr>';       			 	
    $tableths[] = '<th>' . $zbp->lang['msg']['id'] . '</th>';    	     	 
    $tableths[] = '<th>' . $zbp->lang['msg']['parend_id'] . '</th>';    			  			
    $tableths[] = '<th>' . $zbp->lang['msg']['name'] . '</th>';    	  	   	
	$tableths[] = '<th>' . $zbp->lang['msg']['email'] . '</th>';     			 	  
	$tableths[] = '<th>IP</th>';    			 	  	
    $tableths[] = '<th>' . $zbp->lang['msg']['content'] . '</th>';    		 		 		
    $tableths[] = '<th>' . $zbp->lang['msg']['article'] . '</th>';    	 				  
    $tableths[] = '<th>' . $zbp->lang['msg']['date'] . '</th>';    			     
    $tableths[] = '<th>操作</th>';     		  	  
    $tableths[] = '<th><a href="" onclick="BatchSelectAll();return false;">' . $zbp->lang['msg']['select_all'] . '</a></th>';    	 			 		
    $tableths[] = '</tr>';        	 	 
     	 	 		 
    foreach ($array as $cmt) {       	 		 
      						
        $article = $zbp->GetPostByID($cmt->LogID);      			  	
        if ($article->ID == 0) {    	    	  
            $article = null;      	 	 	 
        }    		 	  	 
    			 		 	
        $tabletds = array();//table string    	 		   	
        $tabletds[] = '<tr>';     	   		 
        $tabletds[] = '<td class="td5"><a href="main.php?act=CommentMng&id=' . $cmt->ID . '" title="' . $zbp->lang['msg']['jump_comment'] . $cmt->ID . '">' . $cmt->ID . '</a></td>';    	  				 
        if ($cmt->ParentID > 0) {    		     	
            $tabletds[] = '<td class="td5"><a href="main.php?act=CommentMng&id=' . $cmt->ParentID . '" title="' . $zbp->lang['msg']['jump_comment'] . $cmt->ParentID . '">' . $cmt->ParentID . '</a></td>';         			
        } else {     		  	  
            $tabletds[] = '<td class="td5"></td>';      	 	 	 
        }     					 	
    		  	 		
        $tabletds[] = '<td class="td10"><span class="cmt-note" title="' . $zbp->lang['msg']['email'] .':' . htmlspecialchars($cmt->Email) . '"><a href="mailto:' . htmlspecialchars($cmt->Email) . '">' . $cmt->Author->Name . '</a></span></td>';    	     		
		$tabletds[] = '<td class="td5">' . $cmt->Email . '</td>';     		 				
		$tabletds[] = '<td class="td5">' . $cmt->IP . '</td>';    	  	    
        $tabletds[] = '<td><div style="overflow:hidden;max-width:500px;">' .     	 	  	 	
        (($article)?      	 	  	
            '<a href="' . $article->Url . '" target="_blank"><img src="../../../../zb_system/image/admin/link.png" alt="" title="" width="16" /></a> '      			  	
        :      	 				
            '<a href="javascript:;"><img src="../../../zb_system/image/admin/delete.png" alt="no exists" title="no exists" width="16" /></a>'            
        ) .     	 		 		
		$cmt->Content . '<div></td>';    	 	 	   
		    	  	  	 
        $tabletds[] = '<td class="td5">' . $cmt->LogID . '</td>';         			
        $tabletds[] = '<td class="td15">' . $cmt->Time() . '</td>';    			  		 
        $tabletds[] = '<td class="td10 tdCenter">' .     		  		  
            '<a onclick="return window.confirm(\'' . $zbp->lang['msg']['confirm_operating'] . '\');" href="' . BuildSafeCmdURL('act=CommentDel&amp;id=' . $cmt->ID) . '"><img src="../../../../zb_system/image/admin/delete.png" alt="' . $zbp->lang['msg']['del'] . '" title="' . $zbp->lang['msg']['del'] . '" width="16" /></a>' .     	  			 	
        '&nbsp;&nbsp;&nbsp;&nbsp;' .     		  		 	
        (!GetVars('ischecking', 'GET') ?     	 			 	
            '<a href="' . BuildSafeCmdURL('act=CommentChk&amp;id=' . $cmt->ID . '&amp;ischecking=' . (int) !GetVars('ischecking', 'GET')) . '"><img src="../../../../zb_system/image/admin/minus-shield.png" alt="' . $zbp->lang['msg']['audit'] . '" title="' . $zbp->lang['msg']['audit'] . '" width="16" /></a>'     		   		
        :    	  	    
            '<a href="' . BuildSafeCmdURL('act=CommentChk&amp;id=' . $cmt->ID . '&amp;ischecking=' . (int) !GetVars('ischecking', 'GET')) . '"><img src="../../../../zb_system/image/admin/ok.png" alt="' . $zbp->lang['msg']['pass'] . '" title="' . $zbp->lang['msg']['pass'] . '" width="16" /></a>'      	 		 	
        ) .     	       
           '&nbsp;&nbsp;&nbsp;&nbsp;' .       	  			
		   '<a href="../../../../zb_users/plugin/ZBlogSEO/Comment/comment_edit.php?act=CmtEdt&amp;id='. $cmt->ID .'"><img src="../../../../zb_system/image/admin/comment_edit.png" alt="'.$zbp->lang['msg']['edit'] .'" title="'.$zbp->lang['msg']['edit'] .'" width="16" /></a>'.     				   
			'</td>';    	  	   	
        $tabletds[] = '<td class="td5 tdCenter">' . '<input type="checkbox" id="id' . $cmt->ID . '" name="id[]" value="' . $cmt->ID . '"/>' . '</td>';    	  					
       		 		
        $tabletds[] = '</tr>';        				
        foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_CommentMng_Table'] as $fpname => &$fpsignal) {          	 
            //传入 当前$cmt，当前行，表头        				
           $fpreturn = $fpname($cmt,$tabletds,$tableths,$article);    	  		 	 
        }        		  
        		  
        $tables .= implode($tabletds);    	 			 	 
    }    			 			 
     	   	 	
    echo implode($tableths) . $tables;    	 		    
    echo '</table>';     			    
    echo '<hr/>';    	 	   	 
    				    
    echo '<p style="float:right;">';    							 
    	     		
    if ((boolean) GetVars('ischecking')) {     	   			
        echo '<input type="submit" name="all_del"  value="' . $zbp->lang['msg']['all_del'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';    		 		   
        echo '<input type="submit" name="all_pass"  value="' . $zbp->lang['msg']['all_pass'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';     	     	
    } else {    	 	 		 	
        echo '<input type="submit" name="all_del"  value="' . $zbp->lang['msg']['all_del'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';     		  	  
        echo '<input type="submit" name="all_audit"  value="' . $zbp->lang['msg']['all_audit'] . '"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';      		  	 
    }     			 			
    				 		 
    echo '</p>';     			 	 	
     	  		 	
    echo '<p class="pagebar">';    			 	  	
    		    	 
    foreach ($p->Buttons as $key => $value) {    	 		 	  
        if($p->PageNow == $key)    	 		   	
            echo '<span class="now-page">' . $key . '</span>&nbsp;&nbsp;';    						  
        else    	  			  
            echo '<a href="' . $value . '">' . $key . '</a>&nbsp;&nbsp;';    	 	     
    }     	 	 	 	
    		 				 
    echo '</p>';    	   	  	
    		  			 
    echo '<hr/></form>';     			    
    			 	 		
    echo '</div>';     			  	 
    echo '<script type="text/javascript">ActiveLeftMenu("aCommentMng");</script>';     					  
    echo '<script type="text/javascript">AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/comments_32.png' . '");$(".cmt-note").tooltip();</script>';     	      
}    	    	  
        	 	 
           	
    	   	 	 
      	 	 	 
    		 	    
?>