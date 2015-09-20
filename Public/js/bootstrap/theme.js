$('.hide-edit').hover(function(){
	$(this).find('a.show-edit').show();
});

$('.hide-edit').mouseleave(function(){
	$(this).find('a.show-edit').hide();
	$(this).find('input:text').attr('type','hidden');
	$(this).find('a').hide();
});

$('.dropdown-toggle').hover(function(){
	$('.dropdown-menu').show();
});
$('.dropdown-menu').mouseleave(function(){
	$(this).hide();
});

$('.theme-label-btn').mousedown(function(){
	$(this).parent().parent().find('.theme-label-btn.active').removeClass('active');
	$(this).addClass('active');
})

$('.unescape').each(function(){
	var str1 = ($(this)[0].innerHTML).replace(/&lt;br&gt;/g,"<br>");
	str1 = str1.replace(/&amp;/g,"&");
	$(this)[0].innerHTML = (str1);
});