<?php /* EL PSY CONGROO */    		 					
function ZBlogSEO_Category_field(){     				 		
    global $zbp,$cate;    		   	 	
	if($zbp->Config("ZBlogSEO")->zbp_seo){	
   	echo '<p><span class="title">SEO 关键词:</span>
	<br />
	<textarea cols="3" rows="6" name="meta_cat_keywords" style="width:600px;">'.htmlspecialchars($cate->Metas->cat_keywords).'</textarea>
	</p>';
	echo '<p><span class="title">SEO 标题:</span><br />
	<textarea cols="3" rows="6" name="meta_cat_title" style="width:600px;">'.htmlspecialchars($cate->Metas->cat_title).'</textarea>
	</p>';
	}       	 	  
}       			  
       	 			
function ZBlogSEO_SEO_field(){    	 	 		  
    global $zbp,$article;    	 				 	
	if($zbp->Config("ZBlogSEO")->zbp_seo){	
    echo '<style>
#SEO{width:100%;float:left;padding:5px 10px!important;min-width:255px;border:1px solid #e5e5e5;border-top: 0;box-shadow:0 1px 1px rgba(0,0,0,.04);background:#fff}
#img{width:78%;float:right;padding:5px 10px!important;border:1px solid #e5e5e5;border-top:0;box-shadow:0 1px 1px rgba(0,0,0,.04);background:#fff}
.hndle{font-size:14px;padding:8px 5px 12px;margin:0;line-height:1.4;border:1px solid #e5e5e5;box-shadow:0 1px 1px rgba(0,0,0,.04);cursor:pointer}
.hndleimg{float:right;width:78%;text-align:left;font-size:14px;padding:6px 5px 6px;margin:0;line-height:1.4;border:1px solid #e5e5e5;box-shadow:0 1px 1px rgba(0,0,0,.04);cursor:pointer}
#localImag img{width:180px;height:118px}
.seoname{vertical-align:top;text-align:left;padding:20px 10px 20px 0;width:200px;line-height:1.3;font-weight:600}
.woth{width:20%;float:left}
.setext{width:70%;float:left}
.inseo{width:50%;border:1px solid #ddd;margin:1px;padding:6px 8px}
</style>
<script>
$(document).ready(function(){
    $(".hndle").click(function(){
	    $("#SEO").slideToggle(500);
	});
	$(".hndleimg").click(function(){
	    $("#img").slideToggle(500);
	});
});
</script>';
    echo '<div class="hndle"><label class="editinputname">ZBlogSEO 设置</div>';     		 				
    echo '<div id="SEO">';       		   
    echo '<div id="alias" class="editmod"><div class="woth"><label for="meta_cmp_seo_title" class="seoname">SEO 标题</label></div><div class="setext"><input type="text" name="meta_cmp_seo_title" size="30" tabindex="30" class="inseo" value="'.htmlspecialchars($article->Metas->cmp_seo_title).'"><p class="description">你可以设置一个不一样的标题，用于该页面的&lt;title&gt;标签中。它的优先权高于本文默认的标题。</p></div></div>';          		
	echo '<div id="alias" class="editmod"><div class="woth"><label for="meta_cmp_seo_keywords" class="seoname">SEO 关键词</label></div><div class="setext"><textarea name="meta_cmp_seo_keywords" cols="60" rows="4" tabindex="30" class="inseo">'.htmlspecialchars($article->Metas->cmp_seo_keywords).'</textarea><p class="description">这里设置的内容将作为该页面 keywords meta 的值，多个关键词请用英文半角逗号,隔开。它的优先权高于本文设置的标签。</p></div></div>';    	       
	echo '<div id="alias" class="editmod"><div class="woth"><label for="meta_cmp_seo_description" class="seoname">SEO 描述</label></div><div class="setext"><textarea name="meta_cmp_seo_description" cols="60" rows="4" tabindex="30" class="inseo">'.htmlspecialchars($article->Metas->cmp_seo_description).'</textarea><p class="description">这里设置的内容将作为该页面 description meta 的值。它的优先权高于本文的摘要。</p></div></div></div>';    						  
	}        		 	
}      		  	 
    			 	   
    	    		 
function ZBlogSEO_Tag_Response(){    	    	 	
	global $zbp,$tag;     	  	   
	if($zbp->Config("ZBlogSEO")->zbp_seo){	
	echo '<p><span class="title">SEO 关键词:</span>
	<br />
	<textarea cols="3" rows="6" name="meta_tag_keywords" style="width:600px;">'.htmlspecialchars($tag->Metas->tag_keywords).'</textarea>
	</p>';
	echo '<p><span class="title">SEO 标题:</span><br />
	<textarea cols="3" rows="6" name="meta_tag_title" style="width:600px;">'.htmlspecialchars($tag->Metas->tag_title).'</textarea>
	</p>';	
	}    	  	 	 	
}       		 		
     			   	
?>