/**
*	@name							mobilemenu
*	@descripton						This $jq plugin makes creating mobilemenus pain free
*	@version						1.3
*	@requires						$jq 1.2.6+
*
*	@author							Jan Jarfalk
*	@author-email					jan.jarfalk@unwrongest.com
*	@author-website					http://www.unwrongest.com
*
*	@licens							MIT License - http://www.opensource.org/licenses/mit-license.php
*/

(function($jq){
     $jq.fn.extend({  
         mobilemenu: function() {       
            return this.each(function() {
            	
            	var $jqul = $jq(this);
            	
				if($jqul.data('accordiated'))
					return false;
													
				$jq.each($jqul.find('ul, li>div'), function(){
					$jq(this).data('accordiated', true);
					$jq(this).hide();
				});
				
				$jq.each($jqul.find('span.head'), function(){
					$jq(this).click(function(e){
						activate(this);
						return void(0);
					});
				});
				
				var active = (location.hash)?$jq(this).find('a[href=' + location.hash + ']')[0]:'';

				if(active){
					activate(active, 'toggle');
					$jq(active).parents().show();
				}
				
				function activate(el,effect){
					$jq(el).parent('li').toggleClass('active').siblings().removeClass('active').children('ul, div').slideUp('fast');
					$jq(el).siblings('ul, div')[(effect || 'slideToggle')]((!effect)?'fast':null);
				}
				
            });
        } 
    }); 
})($jq);

$jq(document).ready(function () {
	
	$jq("ul.mobilemenu li.parent").each(function(){
        $jq(this).append('<span class="head"><a href="javascript:void(0)"></a></span>');
      });
	
	$jq('ul.mobilemenu').mobilemenu();
	
	$jq("ul.mobilemenu li.active").each(function(){
		$jq(this).children().next("ul").css('display', 'block');
	});
});