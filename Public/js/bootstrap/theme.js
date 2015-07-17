$('.hide-edit').hover(function(){
	$(this).find('a').first().show();
});

$('.hide-edit').mouseleave(function(){
	$(this).find('a').first().hide();
});

$('.dropdown-toggle').hover(function(){
	$('.dropdown-menu').show();
});
$('.dropdown-menu').mouseleave(function(){
	$(this).hide();
});
