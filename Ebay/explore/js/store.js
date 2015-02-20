jQuery(document).ready(function(){
	//Right Click Protection
	//$(document).bind("contextmenu",function(e){ return false;});
	
	//Content Area Mods
	if(pageName == "PageAboutMeViewStore") {
		$(".pagecontainer > table:eq(1) tr:first td:first").addClass("os-background");
	}else {
		$(".pagecontainer > table:eq(1) tr:first td:first").addClass("os-background");
		$(".os-background table:eq(1)").addClass("os-content");
		$(".os-content").find("br[clear = none]").remove();
	}	

	//Footer
	var footer = '<div id="footer" class="clear"><div id="footer_container"><div class="designby"> Designed &amp; Developed by <a target="_blank" href="http://www.explorewebstore.com/">Explore WebStore</a></div></div></div>';
	if(pageName != "PageAboutMeViewStore") {
		if($(".os-content").length > 0) {
			$(".os-content").after(footer);			
		}
	}
});
