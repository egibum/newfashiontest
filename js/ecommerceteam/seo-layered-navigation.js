/**
 * "Seo Layered Navigation" extension for "Magento Commerce" by "EcommerceTeam (www.ecommerce-team.com)"
 *
 * @category     Extension
 * @copyright    Copyright (c) 2014 EcommerceTeam (http://www.ecommerce-team.com)
 * @author       EcommerceTeam
 * @version      4.0.0
 */

var layered = {
    overlay:{},
    inProcess:false,
    navigationBlocks:[],
    baseUrl: null,
    init:function(layeredBaseUrl){
        layered.baseUrl = layeredBaseUrl;
        $$('div.block-layered-nav, div.block-layered-mobile, div.toolbar').each(function(e){
            e.select('a').each(function(e){
                // Fix for stupid IE9
                e.onclick = function(){
                    if (this.hasClassName('checked')) {
                        this.removeClassName('checked');
                    } else {
                        this.addClassName('checked');
                    }
                    layered.setUrl(this.href, 'get', {});
                    return false;
                };
            });
            e.select('select').each(function(e){
                e.onchange = 'return false';
                Event.observe(e, 'change', function(e){
                    layered.setUrl(this.value, 'get', {});
                    Event.stop(e);
                });
            });
        });
        $$('div.block-layered-nav').each(function(e){
            e.select('dt').each(function(e) {
                Event.observe(e, 'click', function(e){
                    $(this).next('dd').toggle();
                });
            })
            e.select('li.more').each(function(li) {
                Event.observe(li, 'click', function(event){
                    $(li).up('ol').select('.additional').each(function(e) {
                        if (e.hasClassName('hidden')) {
                            e.removeClassName('hidden');
                        } else {
                            e.addClassName('hidden');
                        }
                    })
                    $(li).select('.more-label').each(function(e) {
                        e.toggle();
                    })
                });
            })
        })
        if (typeof TPG != 'undefined'
            && typeof TPG.Control != 'undefined'
            && typeof TPG.Control.Slider != 'undefined') {
            TPG.Control.Slider.manager.createAll();
        }
    },
    setUrl:function(url, method, params) {
        if(!layered.inProcess){
            layered.inProcess = true;
            layered.showOverlay();
            var forceLayered = ((url.replace(/\?.+$/, "") != layered.baseUrl.replace(/\?.+$/, "")));
            var parameters = {};
            if (params) {
                Object.extend(parameters, params);
            }
            new Ajax.Request(url,{
                    method: method || 'get',
                    requestHeaders: {forceLayered: (forceLayered ? 1 : 0)},
                    parameters:parameters,
                    onSuccess: function(response){
                        layered.processResponse(response.responseText.evalJSON(), url);
                        layered.inProcess = false;
                        layered.hideOverlay();
                    },
                    onFailure: function(){
                        setLocation(url);
                        layered.inProcess = false;
                        layered.hideOverlay();
                    }
                }
            );
        }
    },
    processResponse:function(data, url){

        var bufer = document.createElement('div');

        if (data.navigation_block_html) {
            for (blockId in data.navigation_block_html) {
                var html = data.navigation_block_html[blockId]['html']
                if (html) {
                    bufer.innerHTML = html;
                    if(script = data.navigation_block_html[blockId]['script']){
                        try {
                            eval(script);
                        } catch (e) {
                            if (console) {
                                console.log(e);
                            }
                        }
                    }
                }
                var navigationBlock = $(blockId);
                if(navigationBlock && html){
                    navigationBlock.parentNode.replaceChild(bufer.firstChild, navigationBlock);
                } else if(navigationBlock){
                    var emptyNode = document.createTextNode('');
                    this.navigationBlocks[blockId] = emptyNode;
                    navigationBlock.parentNode.replaceChild(emptyNode, navigationBlock);
                } else if(html){
                    if (navigationBlock = this.navigationBlocks[blockId]) {
                        navigationBlock.parentNode.replaceChild(bufer.firstChild, navigationBlock);
                        this.navigationBlocks[blockId] = null;
                    }
                }


            }
        }

        bufer.innerHTML = data.product_list_block_html;
        var product_list_block = $$('.category-products')[0];
        if (!product_list_block) {
            product_list_block = $$('.col-main .note-msg')[0];
        }
        var category_products = '';
        if (bufer.select('.category-products').length) {
            category_products = bufer.select('.category-products')[0];
        } else {
            category_products = bufer.select('.note-msg')[0];
        }
        product_list_block.parentNode.replaceChild(category_products, product_list_block);

        var title = data.page_title;

        layered.init(data['layered_base_url']);

        if (window.History.enabled ) {
            window.History.replaceState('', title, url);
        }
		jQuery('.more-items-item').css('display','none');
    },
    showOverlay:function() {
        this.showIndicator();
        var product_list = $$('div.category-products');
        if(product_list.length > 0){
            product_list = product_list[0];
        }else if(product_list = $$('div.col-main p.note-msg')){
            product_list = product_list[0];
        }else{
            return false;
        }
        this.createOverlay('products-list', product_list, false);
        var navigationBlocks = $$('div.block-layered-nav .block-content');
        for(var i = 0; i < navigationBlocks.length;i++){
            this.createOverlay('layered-navigation-'+i, navigationBlocks[i], true);
        }

    },
    hideOverlay:function(){
        for(i in this.overlay){
            this.overlay[i].style.display = 'none';
        }
        this.hideIndicator();
    },
    showIndicator:function() {
        var indicator = $('sln-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.id = 'sln-indicator';
            indicator.innerHTML = '<span>Please wait...</span>'
            indicator.style.display = 'none';
            document.body.appendChild(indicator);
        }
        indicator.style.display = 'block';
    },
    hideIndicator:function() {
        var indicator = $('sln-indicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    },
    createOverlay:function(id, container, showIndicator){
        if(this.overlay['sln-overlay-'+id]){
            var overlay = this.overlay['sln-overlay-'+id];
        }else{
            var overlay = document.createElement('div');
            overlay.id = 'sln-overlay-'+id;
            document.body.appendChild(overlay);
            this.overlay['sln-overlay-'+id] = overlay;
        }

        if(typeof SLN_IS_IE == 'boolean'){
            container.style.position = 'relative';
        }else{
            SLN_IS_IE = false;
        }

        var obj = container;
        var offsetLeft = obj.offsetLeft;
        var offsetTop = obj.offsetTop;
        while(obj.offsetParent){
            if(obj == $$('body')[0]){
                break
            } else{
                offsetLeft = offsetLeft+obj.offsetParent.offsetLeft;
                offsetTop = offsetTop+obj.offsetParent.offsetTop;
                obj = obj.offsetParent;
            }
        }

        overlay.style.top           = offsetTop + 'px';
        overlay.style.left          = offsetLeft - (SLN_IS_IE ? 1 : 0) + 'px';
        overlay.style.width         = container.offsetWidth + (SLN_IS_IE ? 1 : 0) + 'px';
        overlay.style.height        = container.offsetHeight + 'px';
        overlay.style.display       = 'block';
        overlay.style.background    = '#ffffff';
        overlay.style.position      = 'absolute';
        overlay.style.opacity       = '0.4';
        overlay.style.filter        = 'alpha(opacity: 40)';
    }
}

document.observe("dom:loaded", function() { layered.init(window.layeredBaseUrl); });
