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
