$('.hide-edit').hover(function(){
	$(this).find('a').show();
});

$('.hide-edit').mouseleave(function(){
	$(this).find('a').hide();
});

$('.dropdown-toggle').hover(function(){
	$('.dropdown-menu').show();
});
$('.dropdown-menu').mouseleave(function(){
	$(this).hide();
});

function hideCollapse(id){
  	$(id).collapse('hide');
  }
