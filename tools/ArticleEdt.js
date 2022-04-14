function ZBlogSEO_ajax(element,url){
	$.ajax({
		type: "POST",
		url: ajaxurl + url,
		data: {title:$(element).val(), id:$("#edtID").val()},
		dataType: 'json',
		success: function(data) {
			if (data.msg) {
				$(element).after("<p class='ZBlogSEO_hide' style='color:red;'>"+data.msg+"</p>"); 
			}else{
				$(element).next(".ZBlogSEO_hide").remove();
			}
		}
	});
}
$(function(){
	var ZBlogSEO_get = $.ZBlogSEO_url_get();
	var ZBlogSEO_act = ZBlogSEO_get['act'];
	$(".post_edit #edtTitle").blur(function(){
		ZBlogSEO_ajax(this,"ZBlogSEO_verify_post_title");
	});
}); 
(function($){
    $.extend({
        ZBlogSEO_url_get:function(){
            var aQuery = window.location.href.split("?");
            var aGET = new Array();
            if(aQuery.length > 1){
                var aBuf = aQuery[1].split("&");
                for(var i=0, iLoop = aBuf.length; i<iLoop; i++){
                    var aTmp = aBuf[i].split("=");
                    aGET[aTmp[0]] = aTmp[1];
                }
            }
            return aGET;
        },
    });
})(jQuery);