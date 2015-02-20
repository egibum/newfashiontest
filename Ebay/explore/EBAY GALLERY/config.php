<?php 
/* **************************************************************************************
	Dynamic Related Items Widget
	Version:	1.0
	By: 		Matthew Ogborne - Lastdropofink.co.uk
	eBay ID:	-
 ************************************************************************************** */

/*
	eBay ID & Store Name
	-----------------------------------
	Enter your eBay ID between the quotes and also your eBay store name.
	Note: For the store name, put the name, not the full path, for example if your store is 
	'http://stores.ebay.co.uk/your-store-name' Set $StoreName as 'your-store-name'.
*/ 
$eBayID 	= 'new_fashion_official';
$StoreName	= 'New-Fashion-Official';
/*
	eBay Token
	-----------------------------------
	eBay uses tokens, it means as a developer I do not need to know your login details.
	This is a really long set of numbers, letters & characters and you can get yours securely here:
	http://apps.lastdropofink.co.uk/GetToken/Login.php
*/
$userToken = 'AgAAAA**AQAAAA**aAAAAA**EgxRVA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AFmIuhDJKDog+dj6x9nY+seQ**q3EBAA**AAMAAA**4W4MNcrqz8bOZKdWCWnweMbi5N75LxAnqBX7H8nXFtHCtLcm53bozto1s/jre5NFhV53I3oe3hGChymwsq+Xa9BhJ4xF0Qfqwjth8NUrH3WTckuMAN1svlkwB81sTAE6RnmC9rq7RlIO9vQO+T7cQ43t8zdsVIe+XWRgIJxa5WWQW85sBEIC3e4w+UeLrY4dRLdcvFMrrgdBZRFVt8+15S8wEj+DP4eUywZ1B+5vTI1B2FnjADGnAbTAbs00BzAMeyf9AzYtE9M3KSoS0/O7ToyMYCsBATt9TLKOhHd0Sbjn3EosbdtkugZddHk9BUz9SKhPrdOhAwF3G2poIJDaCzlcZtBwBVStrfDosKgVZkYC7rU7R9ah5Tk9FDW4/WMO6UZVFESbKQzJWfgTciMpj1ghcy4ynLoM3hre2O2CdA2aU31q1XfHfryUpzmT1dfhzRW0lhqvgn9f4V/kbCLwyD2vIoOkV6ViTOpC+BxWTmjYzbMQTw2OJlteGpaXMXKnxynPKKihdVhtv9HfVpOoyUFTsEwqGUhAstAeoPcjUci27SssBtiEpUbWxyLrPrBVGnVx5o5A6bugaNdUDVzlJvozowwgxr0q7MrUen7MQ0FrdWUrDsgHiuqLSIKnAqMXzCTB7eQ3OussVAVHLA50PAdmeXhcQV4tuuVxWBIR0qu1rPUeUJbsCkZ2cHMbC03MEn4aaAk7vvEW8evH5+eIn9yE+MSvVvzW6nAQb/9RNE47nbrkaKqgiJPCutlK4Ipu';   
/*
	Base URL
	-----------------------------------
	This is the public path to the folder where you have uploaded these files to.
	Example: http://apps.lastdropofink.co.uk/Gallery/
*/
$baseurl	= 'http://new-fashion.co.uk/Ebay/slider/';
/*
	eBay Site TLD (Domain Extension)
	-----------------------------------
	This is the domain that your mainly use on eBay. For example if you main eBay site is "eBay.co.uk", then leave
	it set to ".co.uk", however if you are US based and your core site is "eBay.com", then enter ".com"
	Default: '.co.uk'
*/
$eBaySiteTLD	= ".co.uk";
/*
	Title Character Limit
	-----------------------------------
	This restricts the number of letters in the title of the products that appear in the product gallery.
	35 is suggested, although the maximum is 80 (the max length of an eBay title).
	Default: 35
*/
$Title_Limit	= 35;
/*
	Price Decimal Places
	-----------------------------------
	This is really handy if you sell at whole number prices. Set to 0 if you deal with whole price selling prices 
	eg "£375.00" becomes "£375" however if you sell normally say '39.99' prices, then leave at 2 to get the '.99' on the end
	Default: 2
*/
$Price_Decimal_Places	= 2;		// 
/*
	Default Sorting Method
	-----------------------------------
	This changes the order in which the items are displayed. This is the default and can be overridden for each product if you wish.
	
	A list of valid options are below. If you would like it set to "best match", then set this value as 12.
	Time: ending soonest			= 1
	Time: newly listed				= 10
	Price: lowest first				= 2
	Price: highest first			= 3
	Price + P&P: lowest first		= 15
	Price + P&P: highest first		= 16
	Best Match						= 12
*/
$Default_Sorting		= 10;
/*
	Default eBay Store Category ID
	-----------------------------------
	If you wish for the products returned to be from one category by default, set this value to the desired eBay store category ID.
	You can view a list of your eBay category ID's on the right hand side of this page:
	http://cgi6.ebay.co.uk/ws/eBayISAPI.dll?StoreAllCategories
	An example is "2997236013" and this can be overriden at listing level
	Default: ""
*/
$Default_CategoryID		= "";
/*
	Larger Gallery Images
	-----------------------------------
	This is a new setting that allows you to use larger gallery images.
	However this does depend on you having larger gallery images on eBay and sometimes maynot work, that is why it is set to false
	by default
	Set to 'true' to enable
	Default: false
*/
$LargerGalleryImageEnabled 	= true;
/*
	Larger Gallery Size
	-----------------------------------
	Leave this at 255 as this is normally large enough for normal use. You can go as far as 1200 if your gallery images are that size in width.
	Default: 255 or 96
*/
$LargerGalleryImageSize		= 96;
/*
	CSS File Directory
	-----------------------------------
	This is the directory where you have the CSS files to.
	Default: "css/"
*/
$CSS_File_DIR			= "css/";
/*
	Image File Directory
	-----------------------------------
	This is the directory where you have the IMAGE files to.
	Note: Check the CSS file(s)	for realtive paths is changing this.
	Default: "images/"
*/
$Images_File_DIR			= "images/";
/*
	Cache Directory
	-----------------------------------
	If in ANY doubt, do not change this value.
	If you wish for your cached eBay categories to be held in a different directory, such as  "cache", set the path here.
	Some hosts won't work with relataive paths, instead you'll need to put the full path here such as /host/public_html/etc/etc/
	This directory must be writable, 775 is suggested
*/
$cacheDir 		= "cache/";
/*
	Cache Time
	-----------------------------------
	If in ANY doubt, do not change this value.
	This is the amount of time to cache the categories for. 
	86400 is a day, 43200 is half a day.
	Tip: Set this to 1 when testing design/CSS and swapping levels so that all the levels are loaded straight away.
*/
$CacheTime		= 86400;
/*
	CSS File
	-----------------------------------
	The name of your CSS file.
	Default: "product-slider.css";
*/
$CSS_File 	= "product-slider.css";
/*
	IE Fix CSS File
	-----------------------------------
	Internet Explorer needs some overides to make it work effectively.  Specify the file name below, leave alone if unsure.
	Default: "ie-fix.css"
*/
$CSS_IE_Fix	= "ie-fix.css";
/*
	jQuery Tools Included
	-----------------------------------
	This setting is handy if you are using this widget with other widgets from the store. Instead of browser downlaing the tools
	more than once, you can set this to false and the jquery.tools.min.js will not be added to this widget.
	Default: true
*/
$jQueryTools_Included	= true;
/*
	jQuery Tools Path
	-----------------------------------
	While the jQuery Tools is in a CDN (content delivery network), I could see the desire arising for someone to want to self host this file also. 
	If you would like to self-host this file change the path below, otherwise leave as it is.
	Default: "http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js"
*/
$jQueryTools_URL		= "http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js";
/*
	Scrollable ID
	-----------------------------------
	This allows you to change the ID tag on the related items if desired.
	IMPORTANT: If you change this value make sure you change the ID name at the bottom of this config file AND in the js/js.js file too.
	Default: "scrollable"
*/
$ScrollableID_Name	=  "scrollable";
/*
	Referral Tracking
	-----------------------------------
	This is a path that appends the link of each product that is shown in the gallkery, so you are able to track the click rate of the items.
	Note: Make sure you leave the ? at the begining so that the url stays valid.
	Default: "?referrer=ldoi-widget"
*/
$Referal_Tracking		= "?referrer=ldoi-widget";
/*
	Gallery  Title Text 
	-----------------------------------
	This is the text that is shown above the gallery
	Default: = "<h2>Related Items</h2>"
*/
$GalleryTitleText		= "<h2>You May Also Like...</h2>";
/*
	View Item Label
	-----------------------------------
	This is the text label that is shown below each item. 
	If you would like this text area to be blank, it's suggested you use CSS instead on the class .gal_button
	Default: = "View Item"
*/
$ViewItemLabel			= "View Item";
/*
	Use jQuery Loading Image
	-----------------------------------
	This displays the jQuery loading image while the gallery is being loaded.
	Leave as true to show or set to false to hide. The loading image is called 'ajax-loader.gif' in the images directory
	Default: = true
*/
$UseLoaderImage			= true;
/*
	Currency Symbol
	-----------------------------------
	This is the currency symbol that appears next to each product in the gallery.
	£ 		= "&#163;"
	$		= "&#36;"
	€		= "&#128;"
	For a full list see http://webdesign.about.com/od/localization/l/blhtmlcodes-cur.htm
	Default: "&#163;"
*/
$CurrencySymbol			= "&#163;";
/*
	Footer Text
	-----------------------------------
	Originally used for credits, this is to show any additional text after the gallery.
	An example from the free version is '<div style="font-size: 7px; text-align: right;">Widget by <a href="http://lastdropofink.co.uk/?apps" target="_blank" style="font-size: 7px;">MO</a></div>'
	Default: ""
*/
$FooterText 	= '';
/*
	jQuery Loader Options
	-----------------------------------
	I'm not going to cover each option for the gallery here, however it's extremely important that you DO NOT alter the doDelay() function. As I found this needs to be 3000 (3 seconds)
	to allow the page to load and then show the gallery, otherwise it does not work :(  
	
	NOTE: Also see the contents of the js/js.js file as this file is also loaded and contains the loader file below.
	
	You can change the .slider options, the defaults are for it to autoplay and be circular, as these are both set to true. If you'd like autoplay off, set the value of "true" to "false"
	A full run down of the options/attributes are here http://jquerytools.org/documentation/
*/
$jQueryLoader	= '
				$(document).ready(function() {
					setTimeout("doDelay()",1500);
					//$("#slider-center").hide();
				});
				function doDelay(){
					$(".slider").scrollable({next: ".next2", prev: ".prev2", circular: "true"}).autoscroll({ autoplay: true });
					$("#slider-center").show();
				}
				';
				
/*
	The scroller class
	-----------------------------------
	Helpful if you need to set more than one scroller on the same page.
	Default: "slider"
*/
$ScrollableCLASS_Name = "slider";