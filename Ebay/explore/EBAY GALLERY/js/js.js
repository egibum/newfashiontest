$(document).ready(function() {
	setTimeout("doDelay()",1500);
	//$("#slider-center").hide();
});
function doDelay(){
	$(".slider").scrollable({next: ".next2", prev: ".prev2", circular: "true"}).autoscroll({ autoplay: true });
	$("#slider-center").show();
}