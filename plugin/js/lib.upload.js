var container = document.createElement('script');
$(container).attr('type','text/plain').attr('id','img_editor');
$("body").append(container);
_editor = UE.getEditor('img_editor');
_editor.ready(function () {       
	_editor.hide();
	$(".upload_button").click(function(){		
		object = $(this).parent().find('.uplod_img');
		object2 = $(this).parent().find('#upload123');
		_editor.getDialog("insertimage").open();
		_editor.addListener('beforeInsertImage', function (t, arg) {
			object.attr("value", arg[0].src);
			object2.attr("src", arg[0].src);
		});
	});
});

 var container = document.createElement('script');
$(container).attr('type','text/plain').attr('id','img_editor');
$("body").append(container);
_editor = UE.getEditor('img_editor');
_editor.ready(function () {
	_editor.hide();
	$(".uploadimg strong").click(function(){		
		object = $(this).parent().find('.uplod_img');
		_editor.getDialog("insertimage").open();
		_editor.addListener('beforeInsertImage', function (t, arg) {
			object.attr("value", arg[0].src);
		});
	});
});
			