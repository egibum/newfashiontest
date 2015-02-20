/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_ColorSwatchPro
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

/**************************** EXTEND CONFIGURABLE PRODUCT **************************/
//Class.create(Product.Config, {});
Product.Config = Class.create(Product.Config, {
    initialize: function(config, block_id, optionsPrice, setdata, image_element, loader_element){
        this.block_id   = block_id;
        this.optionsPrice = optionsPrice;
        this.config     = config;
        this.taxConfig  = this.config.taxConfig;
		this.state      = new Hash();
        this.priceTemplate = new Template(this.config.template);
        this.prices     = config.prices;
        this.setdata    = setdata;
		this.image      = image_element;
		this.loader     = loader_element;
		this.common_src = this.image.src;
		this.already_more = false;
		
		if (this.block_id) {
			this.settings = $$('div#colorswatch-options-block-' + this.block_id + ' .super-attribute-select');
		}
		else {
			this.settings = $$('.super-attribute-select');
		}

        this.settings.each(function(element){
			Event.observe(element, 'change', this.configure.bind(this))
			Event.observe(element, 'on:change', this.configure.bind(this))
		}.bind(this));

		// fill state
        this.settings.each(function(element){
            var attributeId = element.id.replace('-'+this.block_id,'').replace(/[a-z]*/, '');
            if(attributeId && this.config.attributes[attributeId]) {
                element.config = this.config.attributes[attributeId];
                element.attributeId = attributeId;
                this.state[attributeId] = false;
            }
        }.bind(this))

        // Init settings dropdown
        var childSettings = [];
        for (var i=this.settings.length-1;i>=0;i--){
            var prevSetting = this.settings[i-1] ? this.settings[i-1] : false;
            var nextSetting = this.settings[i+1] ? this.settings[i+1] : false;
            
			disabled = (i!=0) ? true : false;
			this.fillSelect(this.settings[i], disabled)
            
            $(this.settings[i]).childSettings = childSettings.clone();
            $(this.settings[i]).prevSetting   = prevSetting;
            $(this.settings[i]).nextSetting   = nextSetting;
            childSettings.push(this.settings[i]);
        }

        // try retireve options from url
        var separatorIndex = window.location.href.indexOf('#');
        if (separatorIndex!=-1) {
            var paramsStr = window.location.href.substr(separatorIndex+1);
            this.values = paramsStr.toQueryParams();
            this.settings.each(function(element){
                var attributeId = element.attributeId;
                element.value = (typeof(this.values[attributeId]) == 'undefined')? '' : this.values[attributeId];
                this.configureElement(element);
            }.bind(this));
        }
    },
	
	resetChildren : function(element){
        if(element.childSettings) {
            for(var i=0;i<element.childSettings.length;i++){
				element.childSettings[i].selectedIndex = 0;
                element.childSettings[i].disabled = true;
                if(element.config){
                    this.state[element.config.id] = false;
                }
				
				attributeId = element.childSettings[i].id.replace('-'+this.block_id,'').replace(/[a-z]*/, '');
				if (parseInt(this.setdata.use_icons) && this.setdata.attributes.include(attributeId)){
					this.displayIcons(element.childSettings[i], attributeId, this.getAttributeOptions(attributeId), this.getAttributeOptions(attributeId), true);
				}
            }
        }
    },
	
    fillSelect: function(element, disabled){
		var attributeId = element.id.replace('-'+this.block_id,'').replace(/[a-z]*/, '');
        var options = this.getAttributeOptions(attributeId);
        this.clearSelect(element);
        element.options[0] = new Option(this.config.chooseText, '');

		var prevConfig = false;
        if (element.prevSetting) {
            prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
        }
		
		element.disabled = disabled;

        if (options) {
            var index = 1, options_allowed = [], all_options = [];
            for (var i=0;i<options.length;i++){
                var allowedProducts = [];
                if (prevConfig) {
                    for (var j=0;j<options[i].products.length;j++){
                        if (prevConfig.config.allowedProducts
                            && prevConfig.config.allowedProducts.indexOf(options[i].products[j])>-1){
                            allowedProducts.push(options[i].products[j]);
                        }
                    }
                } else {
                    allowedProducts = options[i].products.clone();
                }
				
				if (allowedProducts.size()>0){
                    options[i].allowedProducts = allowedProducts;
                    element.options[index] = new Option(this.getOptionLabel(options[i], options[i].price), options[i].id);
                    element.options[index].config = options[i];
					
					options_allowed[index-1] = options[i];
					
					index++;
                }
			}
			
			if (parseInt(this.setdata.use_icons) && this.setdata.attributes.include(attributeId)){
				this.displayIcons(element, attributeId, options_allowed, options, disabled);
			}
			else {
				element.show();
			}
        }
    },
	
	configureElement : function(element) {
        this.reloadOptionLabels(element);
		if (element.value){
            this.state[element.config.id] = element.value;
            if (element.nextSetting){
                element.nextSetting.disabled = false;
                this.fillSelect(element.nextSetting);
                this.resetChildren(element.nextSetting);
				
				if (parseInt(this.setdata.auto_selection)) {
					element.nextSetting.options[1].selected = true;
					this.configureElement(element.nextSetting);
									
					var attributeId = element.nextSetting.id.replace('-'+this.block_id,'').replace(/[a-z]*/, '');
					if (parseInt(this.setdata.use_icons) && this.setdata.attributes.include(attributeId)) {
						$$('ul#colorswatch-icon-set-' + this.block_id + '-' + attributeId+' > li[rel='+element.nextSetting.value+']').first().addClassName('active');
					}
				}
			}
			else {
				if (parseInt(this.setdata.switch_image)) {
					this.loadImages(element);
				}
			}
        }
        else {
			this.resetChildren(element);
			this.image.src = this.common_src;
        }
        
        this.reloadPrice();
	},

	reloadPrice: function(){
        var price    = 0;
        var oldPrice = 0;
        for (var i=this.settings.length-1;i>=0;i--){
            var selected = this.settings[i].options[this.settings[i].selectedIndex];
			if (selected.config){
                price    += parseFloat(selected.config.price);
                oldPrice += parseFloat(selected.config.price);
            }
        }

        this.optionsPrice.changePrice('config', price);
        this.optionsPrice.reload();

        return price;

        if ($('product-price-'+this.config.productId)){
            $('product-price-'+this.config.productId).innerHTML = price;
        }
        this.reloadOldPrice();
    },
	
	displayIcons: function(element, attributeId, options_allowed, options, disabled) {
		
		if ($('colorswatch-icon-set-'+this.block_id+'-'+attributeId)) {
			$('colorswatch-icon-set-'+this.block_id+'-'+attributeId).remove();	
		}
		
		if ($('colorswatch-show-all'+attributeId+this.block_id)) {
			$('colorswatch-show-all'+attributeId+this.block_id).remove();	
		}
		
		if (!parseInt(this.setdata.show_not_allowed)) {
			options = options_allowed;
		}
		
		if (options) {
			ul_el = new Element('ul', {
				'id': 'colorswatch-icon-set-'+this.block_id+'-'+attributeId
			});
			ul_el.addClassName('colorswatch-icon-set');
			
			allowed_ids = [];
			for (var i=0;i<options_allowed.length;i++) {
				allowed_ids[i] = options_allowed[i].id
			}
			
			for (var i=0;i<options.length;i++) {
				allowed = allowed_ids.intersect([options[i].id]).length;
				icon_src = this.setdata.icon_folder + options[i].label.replace(/[^A-Za-z0-9]*/, '').replace(/\s/g, '_').toLowerCase() + this.setdata.icon_ext,
				
				img_el = new Element('img', {
					alt: options[i].label,
					title: options[i].label,
					src: icon_src
				});
				
				disabledClass = disabled ? 'disabled' : allowed ? '' : 'disabled';
				hidden = (parseInt(this.setdata.display_link)&&i>=this.setdata.icons_qty&&!this.already_more) ? 'hidden' : '';
				
				li_el = new Element('li', {
					'rel': options[i].id
				}).insert({bottom: img_el});
				li_el.addClassName('colorswatch-icon-item').addClassName(disabledClass).addClassName(hidden);
				
				ul_el.insert({bottom: li_el});
				
				if (allowed_ids.intersect([options[i].id]).length) {				
					li_el.observe('click', function(event) {
						if (!this.hasClassName('disabled') && !this.hasClassName('active')) {
							element.value = parseInt(this.getAttribute('rel').replace(/[a-z]*/, ''));
							element.fire('on:change');
							
							this.addClassName('active').siblings().each(function(item) {
								item.removeClassName('active');
							});
						}
						return false;
					});
				}
			}
			element.insert({before : ul_el});
			
			if (parseInt(this.setdata.display_link) && options.length > this.setdata.icons_qty && !this.already_more) {
				var self = this;
				show_all_el = new Element('a', {
					id:   'colorswatch-show-all'+attributeId+this.block_id,
					href: '#all_icons',
					rel:  '#colorswatch-icon-set-'+this.block_id+'-'+attributeId,
				});
				
				more_qty = options.length - this.setdata.icons_qty;
				
				show_all_el.update(this.sprintf(this.setdata.link_text, parseInt(more_qty))).observe('click', function(event) {
					Event.stop(event);
					self.already_more = true;
					Selector.findChildElements($$(this.rel)[0], ['li.hidden']).each(function(el) { el.removeClassName('hidden'); });
					this.remove();
					return false;
				});
				
				element.insert({before : show_all_el});
			}
		}
	},
	
	sprintf : function(format, etc) {
		var arg = arguments, i = 1;
		return format.replace(/%((%)|s)/g, function (m) { return m[2] || arg[i++] })
	},
	
	loadImages: function(element){
		if (!element.selectedIndex) return false;
		var product_selected = element.options[element.selectedIndex].config.allowedProducts[0], self = this;
		if (!self.simple_images) {
			new Ajax.Request(this.setdata.swatch_url, {
				method : 'POST',
				parameters : {
					product_id     : self.setdata.product_id, 
					reload_gallery : self.setdata.reload_gallery, 
					original_size  : self.setdata.original_size,
					page_type      : self.setdata.page_type
				},
				onLoading : function() {
					if (self.loader) { self.loader.show(); }
				},
				onLoaded: function() {
					if (self.loader) { self.loader.hide(); }
				},
				onSuccess: function(response) {
					self.simple_images = response.responseJSON;
					self.switchImage(product_selected);
					$H(self.simple_images).each(function(pair) {
						item = pair.value, imageObj = new Image();
						imageObj.src = item.image.src;
						if (item.image.gallery.length) {
							item.image.gallery.each(function(item) {
								imageObj.src = item.image.src;
								imageObj.src = item.image.original;
							});
						}
					});
				}
			});
		}
		else {
			self.switchImage(product_selected);
		}
	},
	
	switchImage : function(product_selected) {
		
		if (this.setdata.page_type == 1) { // product list
			this.image.src = this.simple_images[product_selected].image.src;
		}
		else {  // product view
			switchImage(this.image, this.simple_images, product_selected);
		}
		
	}
});

/**************************** EXTEND PRICE RELOADER **************************/
Product.OptionsPrice = Class.create(Product.OptionsPrice, {
	changePrice: function(key, price) {
		this.optionPrices[key] = parseFloat(price);
	}
});

