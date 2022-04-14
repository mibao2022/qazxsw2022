<?php /* EL PSY CONGROO */    				 		 
require '../../../../zb_system/function/c_system_base.php';    		 			 	
require '../../../../zb_system/function/c_system_admin.php';    					 		
$zbp->CheckGzip();    		 			  
$zbp->Load();          	 
$action='root';    	 	    	
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6,__FILE__,__LINE__);die();}    						 	
if (!$zbp->CheckPlugin('ZBlogSEO')) {$zbp->ShowError(48);die();}     		    	
            
$blogtitle='评论编辑 - 评论管理';     	 					
         	  
require $blogpath . 'zb_system/admin/admin_header.php';    			 				
require $blogpath . 'zb_system/admin/admin_top.php';    	     		
     		 	   
?>    	 		  		
<?php    		 	 			
          	 
$cmtid=null;    		 		  	
if(isset($_GET['id'])){$cmtid = (integer)GetVars('id','GET');}else{$cmtid = 0;}      				  
$cmt=$zbp->GetCommentByID($cmtid);    	   		  
if (isset($_POST['name']) && $_POST['name'] != '') {      				  
	$cmt = $zbp->GetCommentByID($_POST['ID']);     	      
	$cmt->Name = $_POST['name'];    	 	 	 	 
	$cmt->Email = $_POST['email'];     		 		  
	$cmt->IP = $_POST['IP'];      			  	
	$cmt->HomePage = $_POST['HomePage'];    		    	 
	$cmt->Content = $_POST['content'];    			  		 
	$cmt->Save();    	   		  
	$zbp->SetHint('good');    	  	   	
	Redirect('./main.php');      	 	  	
}    			 			 
     				 	 
?>

<div id="divMain">
  <div class="divHeader2"><?php echo $blogtitle?></div>
  <div class="SubMenu"><?php echo ZBlogSEO_Comment_SubMenu();?></div>
  <div id="divMain2" class="edit cmt_edit">
	<form id="edit" name="edit" method="post" action="#">
		<input id="edtID" name="ID" type="hidden" value="<?php echo $cmt->ID;?>" />
		  <p>
			<span class="title">名称:</span><span class="star">(*)</span><br />
			<input id="name" class="edit" size="40" name="name" maxlength="50" type="text" value="<?php echo $cmt->Name;?>" />
		  </p>
		  <p>
			<span class="title">邮箱:</span><br />
			<input id="email" class="edit" size="40" name="email" maxlength="50" type="text" value="<?php echo $cmt->Email;?>" />
		  </p>
		  <p>
			<span class="title">IP:</span><br />
			<input id="IP" class="edit" size="40" name="IP" maxlength="50" type="text" value="<?php echo $cmt->IP;?>" />
		  </p>
		  <p>
			<span class="title">网址:</span><br />
			<input id="HomePage" class="edit" size="40" name="HomePage" maxlength="50" type="text" value="<?php echo $cmt->HomePage;?>" />
		</p>
	  	<p>
			<span class="title">评论内容:</span><br>
			<textarea style="width: 293px;" id="content" type="text" cols="50" rows="4" tabindex="5" name="content"><?php echo $cmt->Content;?></textarea>
		</p>
		  <p>
			<input type="submit" class="button" value="<?php echo $lang['msg']['submit']?>" id="btnPost" onclick="return checkInfo();" />
		  </p>
	</form>
	<script type="text/javascript">
		function checkInfo(){
		  if(!$("#name").val()){
			alert("<?php echo $lang['error']['72']?>");
			return false
		  }
		}
	</script>
	<script type="text/javascript">ActiveLeftMenu("acmtMng");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $zbp->host . 'zb_system/image/common/comments_32.png';?>");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';    			   	 
      	  			
RunTime();    	  		 		
?>