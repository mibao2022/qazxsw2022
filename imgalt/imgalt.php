<?php /* EL PSY CONGROO */    			  			
function ZBlogSEO_ViewPost_ImgAlt(&$template){       		 		
	global $zbp;     			 			
	$article = $template->GetTags('article');    	   				
	$num = preg_match_all( '/<img.*?>/i', $article->Content, $matches );
	if( 1 == $num ) {
		preg_match( '/<img.*?title=[\"\'](.*?)[\"\'].*?>/', $article->Content, $match_title );
		$title = isset( $match_title[1] ) ? $match_title[1] : '';				
		preg_match( '/<img.*?alt=[\"\'](.*?)[\"\'].*?>/', $article->Content, $match_alt );
		$alt = isset( $match_alt[1] ) ? $match_alt[1] : '';
		$article->Content = preg_replace( '/(<img.*?) title=["\'].*?["\']/i', '${1}', $article->Content );
		$article->Content = preg_replace( '/(<img.*?) alt=["\'].*?["\']/i','${1}', $article->Content );$article->Content = preg_replace( '/<img/i', '<img' . ' alt="' .  $article->Title.'"', $article->Content, 1 );
	}
	if( 1 < $num ) {
		$temp = '*@@##@@*';
		for( $i = 1; $i <= $num; $i++ ) {
			preg_match( '/<img.*?>/', $article->Content, $match_img );
			$img = isset( $match_img[0] ) ? $match_img[0] : '';
			preg_match( '/<img.*?title=[\"\'](.*?)[\"\'].*?>/', $img, $match_title );
			$title = isset( $match_title[1] ) ? $match_title[1] : '';				
			preg_match( '/<img.*?alt=[\"\'](.*?)[\"\'].*?>/', $img, $match_alt );
			$alt = isset( $match_alt[1] ) ? $match_alt[1] : '';			
			$ZBlogSEO_IMGNumber =  ZBlogSEO_IMGNumber( $i ) ;
			$alt_suffix =  ZBlogSEO_IMGNumber( $i ) ;
			if( $title )
			$article->Content = preg_replace( '/(<img.*?) title=["\'].*?["\']/i', '${1}', $article->Content, 1 );
			if( $alt )
			$article->Content = preg_replace( '/(<img.*?) alt=["\'].*?["\']/i','${1}', $article->Content, 1 );				
			$replace = '<' . $temp . ' alt="'.$article->Title .' '.$alt_suffix.'"';					
			$article->Content = preg_replace( '/<img/i', $replace, $article->Content, 1 );
		}
		$article->Content = str_replace( $temp, 'img', $article->Content );	
	}
}

function ZBlogSEO_IMGNumber( $i ) {
	global $zbp;
	if( $zbp->Config('ZBlogSEO')->Number ) {
		$Number = str_replace('%IMG%', $i, $zbp->Config('ZBlogSEO')->Number );
		return ' ' . trim( $Number );
	}
	return '';
}


?>