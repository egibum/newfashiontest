var J2t_One_Checkout = Class.create();
J2t_One_Checkout.prototype = {
    initialize: function(urls){
        this.saveMethodUrl = urls.saveMethod;
        this.failureUrl = urls.failure;
        this.method = '';
        
        this.showOverlay = urls.showOverlay;

        this.shippingUrl = urls.shippingUrl;
        this.saveBilling = urls.saveBilling;
        this.shippingMethod = urls.shippingMethod;
        this.paymentMethod = urls.paymentMethod;
        this.reviewUrl = urls.reviewUrl;
        this.couponUrl = urls.couponUrl;
        this.rewardUrl = null;
        
        this.j2tbillingUrl = urls.j2tbillingUrl;
        
        this.doSubmitPayment = false;
        this.doRefreshReview = false;
        this.doSaveShipping = false;
        this.doSaveShippingMethod = false;
        this.doSavePaymentMethod = false;
        this.doSubmitOrder = false;
        this.submittingOrder = false;
        
        this.shippingQuoteMethodUrl = urls.shippingQuoteMethodUrl;
        
        this.stepProcess=0;
        this.firstLoad = true;
        
        this.userLoggedAddress = false;
        
        this.stepShippingComplete = false;
        this.stepPaymentComplete = false;
        this.stepShippingChecked = false;
        this.needPaymentRefresh = true;
        this.checkRedirect = false;
        this.reloadShippingOnBilling = false;
        this.saveShippingOnBilling = true;
        this.reloadPaymentMethodOnShipping = false;
        
        this.reloadReviewOnShippingSave = false;
        
        this.saveRealShipping = false;
        this.stepSoColissimo = false;
        this.stepShippingMethodOk = false;
        this.stepPaymentMethodOk = false;
        this.stepOrderReviewOk = false;
        this.stepOrderCouponOk = false;
        this.stepOrderRewardOk = false;
        this.stepReloadShippingAtEnd = false;
        this.stepReloadShippingAtEndAction = false;

        this.billingCity = '';
        this.billingPostCode = '';
        this.billingRegion = '';
        this.billingCountry = '';
        this.billingSelect = '';

        this.shippingCity = '';
        this.shippingPostCode = '';
        this.shippingRegion = '';
        this.shippingCountry = '';
        this.shippingSelect = '';
        
        this.shippingQuotePostCode = '';
        this.shippingQuoteRegion = '';
        this.shippingQuoteCountry = '';

        this.processBillingOperation = true;
        this.processShippingOperation = true;
        
        this.canHideFinishAddressTip = false;
        
        this.savePayment = false;
        this.goReview = false;
        
        this.paymentFormSerialize = null;
        this.paymentFormSerializeText = null;
        
        //this.selectedPaymentId = 'none';
    },
    saveAndSubmitAll: function () {
        //1. save billing
        //2. save shipping
        //3. save shipping method
        //4. save payment method
        //5. submit order
        
        j2t_one_checkout.unrestrictForms();
        j2t_one_checkout.doSaveShipping = true;
        j2t_one_checkout.doSaveShippingMethod = true;
        j2t_one_checkout.doSavePaymentMethod = true;
        j2t_one_checkout.doSubmitOrder = true;
        j2t_one_checkout.goReview = true;
        
        //j2t_one_checkout.processOrderSubmission();
        billing.save();
    },
    checkOverlay: function (){
        if ($('j2t-onecheckout-loading').getStyle('display') == 'block'){
            $('j2t-onecheckout-address').hide();
            $('j2t-onecheckout-address-arraw').hide();
        } else if ($('j2t-onecheckout-address').getStyle('display') == 'block'){
            $('j2t-onecheckout-loading').hide();
        }
    },
    showAddressModifying: function() {
        
        if (j2t_one_checkout.showOverlay == 1){
            $('j2t-onecheckout-address-complete').hide();
            
            var x = $('j2t-onecheckout-left-1').getWidth();
            var y = $('j2t-onecheckout-title').getHeight();

            var div_width = $('j2t-onecheckout-main').getWidth() - $('j2t-onecheckout-left-1').getWidth();
            var div_height = $('j2t-onecheckout-main').getHeight();

            // set the style of the element so it is centered
            var styles = {
                position: 'absolute',
                top: y + 'px',
                left : x + 'px',
                width : div_width + 'px',
                height : div_height + 'px'
            };
            var styles2 = {
                paddingTop : Math.round(div_height/2) + 'px'
            };

            $('j2t-onecheckout-address').setStyle(styles);
            $('j2t-onecheckout-address-txt').setStyle(styles2);

            if ($('j2t-onecheckout-address').getStyle('display') != 'block'){
                var styles3 = {
                    position: 'absolute',
                    top: Math.round(div_height/2) + 'px',
                    left : (x-10) + 'px'
                };
                $('j2t-onecheckout-address-arraw').setStyle(styles3);
                $('j2t-onecheckout-address-arraw').show();

                new Effect.Appear($('j2t-onecheckout-address'), {duration: 0.5,  to: 0.9});
            }
        }
    },
    hideAddressModifying: function() {
        var continue_process = true;
        if (j2t_one_checkout.reloadShippingOnBilling && j2t_one_checkout.saveShippingOnBilling){
            var validator_shipping = new Validation(shipping.form, {stopOnFirst: true, focusOnError:false});
            if (!validator_shipping.validate()) {
                validator_shipping.reset();
                continue_process = false;
            }
        }
        if(continue_process) {
            var validator = new Validation(billing.form, {stopOnFirst: true, focusOnError:false});
            if (!validator.validate()) {
                validator.reset();
            } else {
                $('j2t-onecheckout-address').hide();
                $('j2t-onecheckout-address-arraw').hide();
            }
        } else {
            $('j2t-onecheckout-address-complete').show();
        }
    },
    restrictForms: function () {
        
        var elm_div_added = ($$('body')[0]).insert({bottom: new Element('div', {id: 'j2t-overlay-unclick'})});
        
        var x = 0;
        var y = 0;
        var div_width = ($$('body')[0]).getWidth();
        var div_height = ($$('body')[0]).getHeight();

        // set the style of the element so it is centered
        var styles = {
            position: 'absolute',
            top: y + 'px',
            left : x + 'px',
            width : div_width + 'px',
            height : div_height + 'px',
            background : 'transparent',
            zIndex : 10000
        };
        
        $('j2t-overlay-unclick').setStyle(styles);
        
        
        
        /*$$('#co-shipping-method-form input').invoke('disable');
        $$('#co-shipping-method-form radio').invoke('disable');
        $$('#co-shipping-method-form select').invoke('disable');
        
        $$('#co-payment-form input').invoke('disable');
        $$('#co-payment-form radio').invoke('disable');
        $$('#co-payment-form select').invoke('disable');*/
        
        
        /*$$('#discount-coupon-form input').invoke('disable');*/
        //$$('#discount-coupon-form radio').invoke('disable');
        /*$$('#discount-coupon-form select').invoke('disable');*/
        
        /*$$('#j2t-onecheckout-left-1 select').invoke('disable');
        $$('#j2t-onecheckout-left-1 input:radio').invoke('disable');*/
        
        $$('#discount-coupon-form .buttons-set').invoke('hide');
    },
    unrestrictForms: function () {
        if ($('j2t-overlay-unclick')){
            $('j2t-overlay-unclick').remove();
        }
        $$('#co-shipping-method-form input').invoke('enable');
        $$('#co-shipping-method-form radio').invoke('enable');
        $$('#co-shipping-method-form select').invoke('enable');
        
        $$('#co-payment-form input').invoke('enable');
        $$('#co-payment-form radio').invoke('enable');
        $$('#co-payment-form select').invoke('enable');
        
        $$('#discount-coupon-form input').invoke('enable');
        //$$('#discount-coupon-form radio').invoke('enable');
        $$('#discount-coupon-form select').invoke('enable');
        $$('#j2t-onecheckout-left-1 select').invoke('enable');
        $$('#j2t-onecheckout-left-1 input:radio').invoke('enable');
        
        $$('#discount-coupon-form .buttons-set').invoke('show');
    },
    showLoading: function(){
        
        $('j2t-shipping-please-wait').show();
        $('j2t-payment-please-wait').show();
        if (!j2t_one_checkout.submittingOrder)
            $('j2t-review-please-wait').show();
        
        if (j2t_one_checkout.showOverlay != 1){
            $('j2t-onecheckout-address').hide();
            $('j2t-onecheckout-address-arraw').hide();
            this.restrictForms();
        } else {
            $('j2t-onecheckout-address').hide();
            $('j2t-onecheckout-address-arraw').hide();

            var x = 0;
            var y = $('j2t-onecheckout-title').getHeight();

            var div_width = $('j2t-onecheckout-main').getWidth();
            var div_height = $('j2t-onecheckout-main').getHeight();

            // set the style of the element so it is centered
            var styles = {
                position: 'absolute',
                top: y + 'px',
                left : x + 'px',
                width : div_width + 'px',
                height : div_height + 'px'
            };
            var styles2 = {
                paddingTop : Math.round(div_height/2) + 'px'
            };

            $('j2t-onecheckout-loading').setStyle(styles);
            $('j2t-onecheckout-loading-img').setStyle(styles2);
            new Effect.Appear($('j2t-onecheckout-loading'), {duration: 0.5,  to: 0.9});
        }
        j2t_one_checkout.completeOff();
    },
    hideLoading: function(){
        if (j2t_one_checkout.stepShippingMethodOk && j2t_one_checkout.stepPaymentMethodOk && j2t_one_checkout.stepOrderReviewOk){
            $('j2t-shipping-please-wait').hide();
            $('j2t-payment-please-wait').hide();
            if (!j2t_one_checkout.submittingOrder)
                $('j2t-review-please-wait').hide();
            if (j2t_one_checkout.showOverlay != 1){
                this.unrestrictForms();
                $('j2t-onecheckout-address').hide();
                $('j2t-onecheckout-address-arraw').hide();
            } else {
                if (this.canHideFinishAddressTip){
                    $('j2t-onecheckout-address').hide();
                    $('j2t-onecheckout-address-arraw').hide();
                }            
                $('j2t-onecheckout-loading').hide();
            }
            j2t_one_checkout.stepShippingMethodOk = false;
            j2t_one_checkout.stepPaymentMethodOk = false;
            j2t_one_checkout.stepOrderReviewOk = false;
            
            j2t_one_checkout.completeOn();
        }
    },
    resetBillingShipping: function(){
        this.processBillingOperation = true;
        this.processShippingOperation = true;

        this.billingCity = '';
        this.billingPostCode = '';
        this.billingRegion = '';
        this.billingCountry = '';
        this.billingSelect = '';

        this.shippingCity = '';
        this.shippingPostCode = '';
        this.shippingRegion = '';
        this.shippingCountry = '';
        this.shippingSelect = '';
    },
    checkBilling: function (){
        var returnValue = true;
        var processFields = true;

        if ($('billing-address-select')){
            if (this.billingSelect != $F('billing-address-select')){
                returnValue = false;
                processFields = false;
                this.billingSelect = $F('billing-address-select');
            }
        }

        if ($('billing:city') && processFields){
            if (this.billingCity != $F('billing:city')){
                returnValue = false;
                this.billingCity = $F('billing:city');
            }
        }
        if ($('billing:postcode') && processFields){
            if (this.billingPostCode != $F('billing:postcode')){
                returnValue = false;
                this.billingPostCode = $F('billing:postcode');
            }
        }
        if ($('billing:region_id') && processFields){
            if (this.billingRegion != $F('billing:region_id')){
                returnValue = false;
                this.billingRegion = $F('billing:region_id');
            }
        }
        if ($('billing:country_id') && processFields){
            if (this.billingCountry != $F('billing:country_id')){
                returnValue = false;
                this.billingCountry = $F('billing:country_id');
            }
        }

        if (!returnValue){
            this.processBillingOperation = true;
        } else {
            this.processBillingOperation = false;
        }
    },
    
    submitShippingQuote : function () {
        
        if ($('billing:use_for_shipping_yes').checked){
            shippingQuoteCountryTxt = $F('billing:country_id');
            shippingQuotePostCodeTxt = $F('billing:postcode');
            if ($('billing:region_id'))
                shippingQuoteRegionTxt = $F('billing:region_id');
        } else {
            shippingQuoteCountryTxt = $F('shipping:country_id');
            shippingQuotePostCodeTxt = $F('shipping:postcode');
            if ($('shipping:region_id'))
                shippingQuoteRegionTxt = $F('shipping:region_id');
        }
        
        if (this.shippingQuoteCountry != shippingQuoteCountryTxt /*&& this.shippingQuotePostCode != shippingQuotePostCodeTxt && this.shippingQuoteRegion != shippingQuoteRegionTxt*/){

            j2t_one_checkout.showLoading();

            var request = new Ajax.Request(
                this.shippingQuoteMethodUrl,
                {
                    asynchronous: true,
                    method:'post',
                    onFailure: function (xhr, e)
                    {
                        alert('Exception : ' + e);
                    },
                    onComplete: function (xhr)
                    {
                        //xhr.responseText;
                        j2t_one_checkout.refreshShippingMethodRadio();
                        j2t_one_checkout.refreshPaymentMethodInputs();
                        j2t_one_checkout.reloadReview();
                    },
                    parameters: "country_id="+shippingQuoteCountryTxt+"&region_id="+shippingQuoteRegionTxt+"&estimate_postcode="+shippingQuotePostCodeTxt
                }
            );
        }
    },
    checkShipping: function (){
        
        var returnValue = true;
        var processFields = true;

        if ($('shipping-address-select')){
            if (this.shippingSelect != $F('shipping-address-select')){
                returnValue = false;
                processFields = false;
                this.shippingSelect = $F('shipping-address-select');
            }
        }

        if ($('shipping:city') && processFields){
            if (this.shippingCity != $F('shipping:city')){
                returnValue = false;
                this.shippingCity = $F('shipping:city');
            }
        }
        if ($('shipping:postcode') && processFields){
            if (this.shippingPostCode != $F('shipping:postcode')){
                returnValue = false;
                this.shippingPostCode = $F('shipping:postcode');
            }
        }
        if ($('shipping:region_id') && processFields){
            if (this.shippingRegion != $F('shipping:region_id')){
                returnValue = false;
                this.shippingRegion = $F('shipping:region_id');
            }
        }
        if ($('shipping:country_id') && processFields){
            if (this.shippingCountry != $F('shipping:country_id')){
                returnValue = false;
                this.shippingCountry = $F('shipping:country_id');
            }
        }
        
        
        if ($('billing:use_for_shipping_yes').checked){
            shippingQuoteCountryTxt = $F('billing:country_id');
            shippingQuotePostCodeTxt = $F('billing:postcode');
            if ($('billing:region_id'))
                shippingQuoteRegionTxt = $F('billing:region_id');
        } else {
            shippingQuoteCountryTxt = $F('shipping:country_id');
            shippingQuotePostCodeTxt = $F('shipping:postcode');
            if ($('shipping:region_id'))
                shippingQuoteRegionTxt = $F('shipping:region_id');
        }
        
        if (this.shippingQuoteCountry == shippingQuoteCountryTxt){
            returnValue = false;
        }
        
        
        if (!returnValue){
            this.processShippingOperation = true;
        } else {
            this.processShippingOperation = false;
        }
    },
    ajaxFailure: function(){
        location.href = this.failureUrl;
    },
    completeOn: function() {
        if ($('review-buttons-container') && !j2t_one_checkout.submittingOrder){
            $('review-buttons-container').show();
            $('order-info-complete').hide();
            //this.unrestrictForms();
        }        
    },
    completeOff: function() {
        if ($('review-buttons-container') && !j2t_one_checkout.submittingOrder){
            $('review-buttons-container').hide();
            $('order-info-complete').show();
            
        }        
    },
    setMethodCheckbox: function(){
        if ($('login:guest_register') && $('login:guest_register').checked) {
            if ($('login:guest'))
                $('login:guest').checked = false;
            if($('login:register'))
                $('login:register').checked = true;
        } else {
            if($('login:register'))
                $('login:register').checked = false;
            if ($('login:guest'))
                $('login:guest').checked = true;
        }
        this.setMethod();
    },
    setMethod: function(){
        if ($('login:guest') && $('login:guest').checked) {
            this.method = 'guest';
            var request = new Ajax.Request(
                this.saveMethodUrl,
                {
                    asynchronous: true,
                    method: 'post',
                    onFailure: this.ajaxFailure.bind(this),
                    parameters: {method:'guest'}
                }
            );
            Element.hide('register-customer-password');
        }
        else if($('login:register') && ($('login:register').checked || $('login:register').type == 'hidden')) {
            this.method = 'register';
            var request = new Ajax.Request(
                this.saveMethodUrl,
                {
                    asynchronous: true,
                    method: 'post',
                    onFailure: this.ajaxFailure.bind(this),
                    parameters: {method:'register'}
                }
            );
            Element.show('register-customer-password');
        }
        else{
            /*alert(Translator.translate('Please choose to register or to checkout as a guest'));
            return false;*/
        }
        //CAPTCHA MODIFICATION
        document.body.fire('login:setMethod', {method : this.method});
    },
    setLoadWaiting: function(step, keepDisabled) {
        if (step) {
            Element.show(step+'-please-wait');
        } else {
            
        }
        
    },
    processShippingDetails: function() {
        //CHECK IF SHIP TO DIFFERENT ADDRESS
        if ($('billing:use_for_shipping_yes').checked){
            j2t_one_checkout.showLoading();
            var billing_id = $F('billing-address-select');
            if (billing_id != ""){
                j2t_one_checkout.userLoggedAddress = true;
                billing.save();
            } else {
                j2t_one_checkout.userLoggedAddress = true;
                billing.save();
            }
        } else {
            j2t_one_checkout.showLoading();
            var shipping_id = $F('shipping-address-select');
            if (shipping_id != ""){
                //submit shipping address
                j2t_one_checkout.userLoggedAddress = true;
                shipping.save();
            } else {
                j2t_one_checkout.userLoggedAddress = true;
                shipping.save();
            }
        }
    },
    
    processShippingSelection: function () {
        var shippingQuoteCountryTxt = '';
        var shippingQuotePostCodeTxt = '';
        var shippingQuoteRegionTxt = '';
        
        var params_txt = "";
        
        var extra_params = "";
        
        if ($('billing:use_for_shipping_yes')){
            if (!$('billing:use_for_shipping_yes').checked){
                var billingQuoteCountryTxt = $F('billing:country_id');
                var billingQuotePostCodeTxt = $F('billing:postcode');
                var billingQuoteRegionTxt = '';
                if ($('billing:region_id'))
                    billingQuoteRegionTxt = $F('billing:region_id');
                extra_params = '&billing_country_id='+billingQuoteCountryTxt+'&billing_region_id='+billingQuoteRegionTxt+'&billing_postcode='+billingQuotePostCodeTxt;
            }
        }
        
        if (!$('billing:use_for_shipping_no') && !$('billing:use_for_shipping_yes')){
            shippingQuoteCountryTxt = $F('billing:country_id');
            shippingQuotePostCodeTxt = $F('billing:postcode');
            if ($('billing:region_id'))
                shippingQuoteRegionTxt = $F('billing:region_id');
            if ($('billing-address-select'))
                if ($F('billing-address-select') != ''){
                    params_txt = "address_id="+$F('billing-address-select');
                }
        } else if ($('billing:use_for_shipping_yes').checked){
            shippingQuoteCountryTxt = $F('billing:country_id');
            shippingQuotePostCodeTxt = $F('billing:postcode');
            if ($('billing:region_id'))
                shippingQuoteRegionTxt = $F('billing:region_id');
            if ($('billing-address-select'))
                if ($F('billing-address-select') != ''){
                    params_txt = "address_id="+$F('billing-address-select');
                }
        } else {
            shippingQuoteCountryTxt = $F('shipping:country_id');
            shippingQuotePostCodeTxt = $F('shipping:postcode');
            if ($('shipping:region_id'))
                shippingQuoteRegionTxt = $F('shipping:region_id');
            
            if ($('billing-address-select'))
                if ($F('shipping-address-select') != ''){
                    params_txt = "address_id="+$F('shipping-address-select');
                }
        }
        

        if (params_txt == ''){
            params_txt = 'country_id='+shippingQuoteCountryTxt+'&region_id='+shippingQuoteRegionTxt+'&postcode='+shippingQuotePostCodeTxt;
        }
        if ($('billing:use_for_shipping_yes')){
            if ($('billing:use_for_shipping_yes').checked){
                used_billing = '&use_for_shipping=1';
            } else {
                used_billing = '&use_for_shipping=0';
            }
            params_txt = params_txt + used_billing;
        }
        if (extra_params != ""){
            params_txt = params_txt + extra_params;
        }
        
        if (shippingQuoteCountryTxt != ''){
            j2t_one_checkout.showLoading();
            var request = new Ajax.Request(
                this.j2tbillingUrl,
                {
                    asynchronous: true,
                    method: 'post',                    
                    onSuccess: function (){
                        /*if ($('login:register')){
                            j2t_one_checkout.stepReloadShippingAtEnd = true;
                            j2t_one_checkout.reloadShippingSaveReloadReview();
                        } else {
                            j2t_one_checkout.stepReloadShippingAtEnd = false;
                            j2t_one_checkout.reloadShippingSaveReloadReview();
                        }*/
                        //TODO: allow to reload shipping at end
                        j2t_one_checkout.stepReloadShippingAtEnd = false;
                        j2t_one_checkout.reloadShippingSaveReloadReview();
                        
                    },
                    parameters: params_txt
                }
            );
        }
    },
    processOrderSubmission: function (){
        
        j2t_one_checkout.showLoading();
        var request = new Ajax.Request(
            this.j2tbillingUrl,
            {
                asynchronous: true,
                method: 'post',                    
                onSuccess: function (){
                    billing.save();
                },
                parameters: 'reset=1'
            }
        );
    },
    processRegisterGuest: function() {
        //CHECK IF SHIP TO DIFFERENT ADDRESS
        
        //if (!j2t_one_checkout.userLoggedAddress){
        if ($('billing-address-select') || $('shipping-address-select')){
            if ($('billing:use_for_shipping_yes').checked){
                //SHIP TO SAME ADDRESS
                //CAN SUBMIT SHIPPING PROCESS
                //CHECK IF country & region is set
                if ($F('billing:country_id') != ''){
                    //submit billing & reload shipping & submit shipping
                    this.reloadShippingOnBilling = false;
                    this.saveShippingOnBilling = false;
                    billing.checkSave();
                }
                billing.checkSave();
            } else {            
                //check if can submit shipping
                if ($F('shipping:country_id') != ''){
                    //submit billing & reload shipping & submit shipping
                    this.reloadShippingOnBilling = true;
                    this.saveShippingOnBilling = true;
                    this.needPaymentRefresh = true;
                    billing.checkSave();
                }
            }
        } else {
            
            var shippingQuoteCountryTxt = '';
            var shippingQuotePostCodeTxt = '';
            var shippingQuoteRegionTxt = '';
            
            if ($('billing:use_for_shipping_yes').checked){
                shippingQuoteCountryTxt = $F('billing:country_id');
                shippingQuotePostCodeTxt = $F('billing:postcode');
                if ($('billing:region_id'))
                    shippingQuoteRegionTxt = $F('billing:region_id');
            } else {
                shippingQuoteCountryTxt = $F('shipping:country_id');
                shippingQuotePostCodeTxt = $F('shipping:postcode');
                if ($('shipping:region_id'))
                    shippingQuoteRegionTxt = $F('shipping:region_id');
            }
            
            if (shippingQuoteCountryTxt != ''){
                j2t_one_checkout.showLoading();
                var request = new Ajax.Request(
                    this.j2tbillingUrl,
                    {
                        asynchronous: true,
                        method: 'post',                    
                        onSuccess: function (){
            
                            j2t_one_checkout.refreshShippingMethodRadio();
                            j2t_one_checkout.refreshPaymentMethodInputs();
            
                            j2t_one_checkout.reloadReview();
                        },
                        parameters: 'country_id='+shippingQuoteCountryTxt+'&region_id='+shippingQuoteRegionTxt+'&postcode='+shippingQuotePostCodeTxt
                    }
                );
            }
        }
    },
    
    restrictRewardButtons: function () {
        $$("#rewardFormArea button").each(function (el, index){
            //$('foo').observe('click', myHandler);
            $(el).stopObserving();
            $(el).writeAttribute('onclick', null);
            $(el).observe('click', function (elmt) {
                if (!$('j2t-review-please-wait').visible()){
                    $('j2t-review-please-wait').show();
                    j2t_one_checkout.processRewardEntry();
                }
            });
        });
    },    
    reloadRewardHtml: function() {
        //rewardUrl
        if ($("rewardFormArea")){
            j2t_one_checkout.stepOrderRewardOk = false;
            var rewardUrl = this.rewardUrl;
            if ($('j2t-review-please-wait')){
                $('j2t-review-please-wait').show();
                new Ajax.Request(
                    rewardUrl,
                    {
                        asynchronous: true,
                        method: 'get',
                        on403: function(t) {
                            alert('Your session appears to have expired.  You will asked to log in again and returned here.');
                            return true
                        },
                        onComplete: function(transport) {
                            var rewardContent = transport.responseText;
                            rewardContent = rewardContent.replace("function verifyJ2tCartPointsValue", "window.verifyJ2tCartPointsValue = function");
                            $('rewardFormArea').replace(rewardContent);
                            j2t_one_checkout.restrictRewardButtons();
                            $('j2t-review-please-wait').hide();
                            j2t_one_checkout.stepOrderRewardOk = true;
                            j2t_one_checkout.hideLoading();
                            //reload shippingMethods
                            j2t_one_checkout.refreshShippingMethodRadio();
                        }
                    }
                );
            }
        }
    },
    
    processRewardEntry: function() {
        var j2t_reward_form = $('discountFormPoints2');
        //var url = (j2t_reward_form.action).replace('/rewardpoints/index', '/j2tonecheckout/cart/rewardPost');
        var url = j2t_reward_form.action;
        
        var current_url = document.location.href;
        if (url.indexOf('http:')  != -1 && current_url.indexOf('https:')  != -1) {
            url = url.replace('http:', 'https:')
        } else if (url.indexOf('https:')  != -1 && current_url.indexOf('http:')  != -1){
            url = url.replace('https:', 'http:')
        }
        
        var request = new Ajax.Request(
            url,
            {
                asynchronous: true,
                method:'post',
                onFailure: function (xhr, e)
                {
                    alert('Exception : ' + e);
                },
                onSuccess: function (xhr)
                {
                    //$('j2t-coupon-ajax-reply').innerHTML = xhr.responseText;
                    j2t_one_checkout.needPaymentRefresh = false;
                    j2t_one_checkout.reloadReviewReward();
                    //$('j2t-coupon-ajax-reply').innerHTML = "";
                },
                parameters: Form.serialize("discountFormPoints2")
            }
        );
    },
    
    reloadReviewReward: function() {
        //needPaymentRefresh        
        var reviewUrl = this.reviewUrl;
        var parent = this;
        if ($('j2t-review-please-wait')){
            $('j2t-review-please-wait').show();
            new Ajax.Request(
                reviewUrl,
                {
                    asynchronous: true,
                    method: 'get',
                    on403: function(t) {
                        alert('Your session appears to have expired.  You will asked to log in again and returned here.');
                        return true
                    },
                    onComplete: function(transport) {
                        if (transport.responseText == 'NO-ITEMS'){
                            window.location.reload();
                        } else {
                            $$('#checkout-review-load .messages').each(Element.remove);
                            $('checkout-review-table-wrapper').replace(transport.responseText);
                            $('j2t-review-please-wait').hide();
                            j2t_one_checkout.hideLoading();
                            parent.reloadRewardHtml();
                        }
                    }
                }
            );
        }  
    },
    
    reloadCouponHtml: function() {
        //couponUrl
        j2t_one_checkout.stepOrderCouponOk = false;
        var couponUrl = this.couponUrl;
        if ($('j2t-review-please-wait')){
            $('j2t-review-please-wait').show();
            new Ajax.Request(
                couponUrl,
                {
                    asynchronous: true,
                    method: 'get',
                    on403: function(t) {
                        alert('Your session appears to have expired.  You will asked to log in again and returned here.');
                        return true
                    },
                    onComplete: function(transport) {
            
                        $('checkout-review-coupon').innerHTML = transport.responseText;
                        $('j2t-review-please-wait').hide();
                        j2t_one_checkout.stepOrderCouponOk = true;
                        j2t_one_checkout.hideLoading();
                        //reload shippingMethods
                        j2t_one_checkout.refreshShippingMethodRadio();
                        j2t_one_checkout.restrictRewardButtons();
                    }
                }
            );
        }
    },
    
    processCouponEntry: function() {
        var j2t_coupon_form = $('discount-coupon-form');
        var url = (j2t_coupon_form.action).replace('/checkout/cart/couponPost', '/j2tonecheckout/cart/couponPost');
        
        var current_url = document.location.href;
        if (url.indexOf('http:')  != -1 && current_url.indexOf('https:')  != -1) {
            url = url.replace('http:', 'https:')
        } else if (url.indexOf('https:')  != -1 && current_url.indexOf('http:')  != -1){
            url = url.replace('https:', 'http:')
        }
        
        var request = new Ajax.Request(
            url,
            {
                asynchronous: true,
                method:'post',
                onFailure: function (xhr, e)
                {
                    alert('Exception : ' + e);
                },
                onSuccess: function (xhr)
                {
                    $('j2t-coupon-ajax-reply').innerHTML = xhr.responseText;
                    j2t_one_checkout.needPaymentRefresh = false;
                    j2t_one_checkout.reloadReviewCoupon();
                    $('j2t-coupon-ajax-reply').innerHTML = "";
                },
                parameters: Form.serialize("discount-coupon-form")
            }
        );
    },
    processCouponForm: function() {
        var j2t_coupon_form = $('discount-coupon-form');
        var j2t_one_checkout_page = $$('.checkout-onepage-index');
        if(j2t_coupon_form && j2t_one_checkout_page.length > 0) {
            if (discountForm) {
                discountForm.submit = function (isRemove) {
                    if (isRemove) {
                        $('coupon_code').removeClassName('required-entry');
                        $('remove-coupone').value = "1";
                    } else {
                        $('coupon_code').addClassName('required-entry');
                        $('remove-coupone').value = "0";
                    }
                    $('j2t-review-please-wait').show();
                    j2t_one_checkout.processCouponEntry();
                    return false;
                }
            }
        }
    },
    
    reloadReviewCoupon: function() {
        //needPaymentRefresh        
        var reviewUrl = this.reviewUrl;
        var parent = this;
        if ($('j2t-review-please-wait')){
            $('j2t-review-please-wait').show();
            new Ajax.Request(
                reviewUrl,
                {
                    asynchronous: true,
                    method: 'get',
                    on403: function(t) {
                        alert('Your session appears to have expired.  You will asked to log in again and returned here.');
                        return true
                    },
                    onComplete: function(transport) {
                        if (transport.responseText == 'NO-ITEMS'){
                            window.location.reload();
                        } else {
                            $$('#checkout-review-load .messages').each(Element.remove);
                            $('checkout-review-table-wrapper').replace(transport.responseText);
                            $('j2t-review-please-wait').hide();
                            j2t_one_checkout.hideLoading();
                            parent.reloadCouponHtml();
                            j2t_one_checkout.restrictRewardButtons();
                        }
                    }
                }
            );
        }  
    },
    
    reloadReview: function() {
            j2t_one_checkout.stepOrderReviewOk = false;
            //J2T >> TABLE RATE double check shipping method
            if (j2t_one_checkout.stepReloadShippingAtEnd == true){
                j2t_one_checkout.stepReloadShippingAtEndAction = true;
                j2t_one_checkout.refreshShippingMethodRadio();
                //j2t_one_checkout.stepReloadShippingAtEnd = false;
                //1.save shipping
                //1.2.reload review
                //j2t_one_checkout.reloadReviewFromSubmitOnlyShipping();
            } else {
                var reviewUrl = this.reviewUrl;
                new Ajax.Request(
                    reviewUrl,
                    {
                        asynchronous: true,
                        method: 'get',
                        on403: function(t) {
                            alert('Your session appears to have expired.  You will asked to log in again and returned here.');
                            return true
                        },
                        onComplete: function(transport) {
                            if (transport.responseText == 'NO-ITEMS'){
                                window.location.reload();
                            } else {
                                $('checkout-review-table-wrapper').replace(transport.responseText);
                                $('j2t-review-please-wait').hide();
                                j2t_one_checkout.completeOn();

                                j2t_one_checkout.stepOrderReviewOk = true;

                                j2t_one_checkout.hideLoading();
                                if (j2t_one_checkout.goReview){
                                    j2t_one_checkout.goReview = false;
                                    review.saveGoReview();
                                }
                            }
                            /*$$("#checkout-review-load .item-options").each(function (el, index){
                                el.hide();
                            });*/
                        }
                    }
                );
            }
        
    },
    
    cartUpdateQty: function () {
        this.showLoading();
        var parent = this;
        var j2t_onechekout_cart_form = $('co-update-cart-qty');
        var form_url = j2t_onechekout_cart_form.action;
        new Ajax.Request(
                form_url,
                {
                    asynchronous: true,
                    method: 'post',
                    postBody: j2t_onechekout_cart_form.serialize(),
                    parameters : Form.serialize(j2t_onechekout_cart_form.name),
                    onException: function (xhr, e)
                    {
                        alert('Exception : ' + e);
                    },
                    onComplete: function (xhr)
                    {
                        parent.stepShippingMethodOk = true;
                        parent.stepPaymentMethodOk = true;
                        parent.stepOrderReviewOk = true;
                        parent.reloadRewardHtml();
                        parent.reloadReviewCoupon();
                    }
                });
    },
    deleteCartItem: function (delete_url) {
        this.showLoading();
        var parent = this;
        new Ajax.Request(
        delete_url,
                { 
                    asynchronous: true,
                    method: 'get',
                    onException: function (xhr, e)
                    {
                        alert('Exception : ' + e);
                    },
                    onComplete: function(transport) {
                        if (transport.responseText == 'NO-ITEMS'){
                            window.location.reload();
                        } else {
                            $$('#checkout-review-load .messages').each(Element.remove);
                            $('checkout-review-table-wrapper').replace(transport.responseText);
                            //$('j2t-review-please-wait').hide();
                            //parent.hideLoading();

                            parent.stepShippingMethodOk = true;
                            parent.stepPaymentMethodOk = true;
                            parent.stepOrderReviewOk = true;

                            parent.reloadRewardHtml();
                            parent.reloadCouponHtml();
                            //parent.reloadReviewCoupon();
                        }
                    }
                });
    },
    reloadPayment: function() {
        var paymentUrl = this.paymentMethod;
        this.stepProcess = 0;

        if ($('j2t-payment-please-wait')){
            new Ajax.Request(
                paymentUrl,
                {
                    asynchronous: true,
                    method: 'get',
                    onComplete: function(transport) {
                        j2t_one_checkout.stepPaymentComplete = false;
                        $('checkout-payment-method-load').replace(transport.responseText);
                        var j2t_onechekout_payment_checked = false;
                        var j2t_onechekout_payment_form = $('co-payment-form');
                        var j2t_onechekout_payment_radios = j2t_onechekout_payment_form.getInputs('radio');
                        j2t_onechekout_payment_radios.each(function (el, index){
                            if(el.checked){
                                j2t_onechekout_payment_checked = true;
                            }
                        });
                        
                        j2t_one_checkout.stepProcess++;
                        payment.checkSave();
                    }
                }
            );
        }
    },
    backupPaymentMethodInputs: function () {
        j2t_one_checkout.paymentFormSerializeText = $('co-payment-form').serialize(); //not json format
        j2t_one_checkout.paymentFormSerialize = $('co-payment-form').serialize(true); //in json format
    },
    restorePaymentMethodInputs: function () {
        var j2t_onechekout_payment_radios = $('co-payment-form').select('input[type="radio"]');
        var j2t_onechekout_payment_inputs = $('co-payment-form').select('input[type="text"]');
        var j2t_onechekout_payment_selects = $('co-payment-form').select('select');
        
        //Form.deserialize($('co-payment-form'), j2t_one_checkout.paymentFormSerializeText);
        //console.log(j2t_one_checkout.paymentFormSerialize);
        
        j2t_onechekout_payment_radios.each(function (el, index){
            if (j2t_one_checkout.paymentFormSerialize != undefined){
                if (j2t_one_checkout.paymentFormSerialize != null){
                    if (j2t_one_checkout.paymentFormSerialize[el.name] != undefined){
                        
                        selected_payment_means = j2t_one_checkout.paymentFormSerialize[el.name];
                        
                        if (j2t_one_checkout.paymentFormSerialize[el.name] instanceof Array) {
                            if (j2t_one_checkout.paymentFormSerialize[el.name][1] != undefined){
                                if (j2t_one_checkout.paymentFormSerialize[el.name][1] == el.value){
                                    el.selected = true;
                                }
                            }

                        } else if (j2t_one_checkout.paymentFormSerialize[el.name] == el.value){
                            el.checked = true;
                        }
                    }
                }
            }
        });
        
        j2t_onechekout_payment_selects.each(function (el, index){
            if (j2t_one_checkout.paymentFormSerialize != undefined){
                if (j2t_one_checkout.paymentFormSerialize != null){
                    if (j2t_one_checkout.paymentFormSerialize[el.name] != undefined){
                        var select_options = el.select('option');
                        select_options.each(function (elmt, index){
                            //alert(el.name);
                            if (j2t_one_checkout.paymentFormSerialize[el.name] instanceof Array) {
                                
                                if (j2t_one_checkout.paymentFormSerialize[el.name][1] != undefined){
                                    if (j2t_one_checkout.paymentFormSerialize[el.name][1] == elmt.value){
                                        elmt.selected = true;
                                    }
                                }
                                
                            } else if (j2t_one_checkout.paymentFormSerialize[el.name] == elmt.value){
                                elmt.selected = true;
                            }
                        });
                    }
                }
            }
        });
        
        j2t_onechekout_payment_inputs.each(function (el, index){
            if (j2t_one_checkout.paymentFormSerialize != undefined){
                if (j2t_one_checkout.paymentFormSerialize != null){
                    if (j2t_one_checkout.paymentFormSerialize[el.name] != undefined){
                        if (j2t_one_checkout.paymentFormSerialize[el.name] instanceof Array) {
                            if (j2t_one_checkout.paymentFormSerialize[el.name][1] != undefined){
                                el.value = j2t_one_checkout.paymentFormSerialize[el.name][1];
                            }
                        } else {
                            el.value = j2t_one_checkout.paymentFormSerialize[el.name];
                        }
                        //el.value = (j2t_one_checkout.paymentFormSerialize[el.name] != ',') ? j2t_one_checkout.paymentFormSerialize[el.name] : '';
                    }
                }
            }
        });
        
    },
    refreshPaymentMethodInputs: function () {
        j2t_one_checkout.backupPaymentMethodInputs();
        j2t_one_checkout.stepPaymentMethodOk = false;
        var paymentUrl = this.paymentMethod;
        if ($('j2t-payment-please-wait')){
            new Ajax.Request(
                paymentUrl,
                {
                    asynchronous: true,
                    method: 'get',
                    onComplete: function(transport) {
                        $('checkout-payment-method-load').replace(transport.responseText);
                        j2t_one_checkout.stepPaymentMethodOk = true;
                        
                        
                        $$('#co-payment-form input').invoke('enable');
                        $$('#co-payment-form radio').invoke('enable');
                        $$('#co-payment-form select').invoke('enable');
                        
                        var j2t_onechekout_payment_checked = false;
                        var j2t_onechekout_payment_form = $('co-payment-form');
                        var j2t_onechekout_payment_radios = j2t_onechekout_payment_form.getInputs('radio');
                        
                        var j2t_onechekout_payment_inputs = j2t_onechekout_payment_form.getInputs('text', 'password');
                        var j2t_onechekout_payment_selects = $('co-payment-form').select('select');
                        
                        j2t_onechekout_payment_radios.each(function (el, index){
                            el.stopObserving();
                            el.observe('click', function (event){
                                j2t_one_checkout.paymentFormSerializeText = $('co-payment-form').serialize(); //not json format
                                j2t_one_checkout.paymentFormSerialize = $('co-payment-form').serialize(true); //in json format
                            });
                        });
                        
                        j2t_onechekout_payment_selects.each(function (el, index){
                            el.stopObserving();
                            el.observe('change', function (event){
                                //this.paymentFormSerialize = $('co-payment-form').serialize(); not json format
                                j2t_one_checkout.paymentFormSerializeText = $('co-payment-form').serialize(); //not json format
                                j2t_one_checkout.paymentFormSerialize = $('co-payment-form').serialize(true); //in json format
                            });
                        });
                        
                        j2t_onechekout_payment_inputs.each(function (el, index){
                            el.stopObserving();
                            el.observe('keyup', function (event){
                                j2t_one_checkout.paymentFormSerializeText = $('co-payment-form').serialize(); //not json format
                                j2t_one_checkout.paymentFormSerialize = $('co-payment-form').serialize(true); //in json format
                            });
                        });
                        
                        j2t_onechekout_payment_radios.each(function (el, index){
                            //el.stopObserving();
                            el.observe('click', function (event){
                                payment.saveNocheck();
                            });
                            if (el.checked){
                                j2t_onechekout_payment_checked = true;
                            }
                        });
                        
                        
                        /*if (j2t_one_checkout.selectedPaymentId != 'none'){
                            j2t_onechekout_payment_checked = true;
                            $(this.selectedPaymentId).checked = true;
                        }*/
                        
                        j2t_one_checkout.restorePaymentMethodInputs();

                        if (!j2t_onechekout_payment_checked && j2t_onechekout_payment_radios.length > 0){
                            j2t_onechekout_payment_radios[0].checked = true;
                        }
                        
                        if (j2t_one_checkout.doSubmitPayment){
                            payment.saveNocheck();
                            j2t_one_checkout.doSubmitPayment = false;
                        }
                        
                        //if (!j2t_one_checkout.reloadPaymentMethodOnShipping){
                            j2t_one_checkout.hideLoading();
                        //}
                        if($$('.cvv-what-is-this').length > 0){
                            $$('.cvv-what-is-this').each(function(element){
                                Event.observe(element,'click', function(event) {
                                    toggleToolTip(event);
                                    $('payment-tool-tip').style.right = "0px";
                                  });
                            });
                        }
                    }
                }
            );
        }
        
    },
    refreshPaymentSave: function(){
        j2t_one_checkout.doSubmitPayment = true;
        j2t_one_checkout.doRefreshReview = true;
        j2t_one_checkout.refreshPaymentMethodInputs();
    },
    reloadReviewFromSubmitShipping: function (){
        //if (shippingMethod.validate()) {
            var request = new Ajax.Request(
                shippingMethod.saveUrl,
                {
                    asynchronous: true,
                    method:'post',
                    onSuccess: function (){
                        j2t_one_checkout.refreshPaymentSave();
                    }, 
                    parameters: Form.serialize(shippingMethod.form)
                }
            );  
        //}
    },
    
    reloadReviewFromSubmitOnlyShipping: function (){
        //if (shippingMethod.validate()) {
        
            var request = new Ajax.Request(
                shippingMethod.saveUrl,
                {
                    asynchronous: true,
                    method:'post',
                    parameters: Form.serialize(shippingMethod.form),
                    //onSuccess: this.shippingMethodSave.bind(this),
                    onSuccess: function (){
                        j2t_one_checkout.reloadReview();
                    } 
                    
                }
            );  
        //}
    },
    
    clickSaveShipping: function (){
        j2t_one_checkout.reloadReviewOnShippingSave = true;
        if (j2t_one_checkout.saveRealShipping){
            j2t_one_checkout.showLoading();
            //submit shipping first then shippingMethod.save();
            j2t_one_checkout.doSaveShipping = true;
            billing.save();
        } else {
            shippingMethod.save();
        }
    },    
    reloadShippingSaveReloadReview: function (){
        j2t_one_checkout.stepShippingMethodOk = false;
       
        if (j2t_one_checkout.stepReloadShippingAtEnd == true){
            //reloadSave Shipping Fist
            j2t_one_checkout.refreshPaymentSave();
        } else {
            ////////////////////////////////////////////
            var shippingUrl = this.shippingUrl;
            //if ($('j2t-shipping-please-wait')){
                new Ajax.Request(
                    shippingUrl,
                    {
                        asynchronous: true,
                        method: 'get',
                        onComplete: function(transport) {
                            //$('checkout-shipping-method-load').innerHTML = transport.responseText;
                            //if (!$('login:register')){
                                $('checkout-shipping-method-load').innerHTML = transport.responseText;
                            //}
                            j2t_one_checkout.stepShippingMethodOk = true;
                            j2t_one_checkout.hideLoading();

                            var j2t_onechekout_shipment_form = $('co-shipping-method-form');
                            var j2t_onechekout_shipment_radios = j2t_onechekout_shipment_form.getInputs('radio');

                            j2t_onechekout_shipment_radios.each(function (el, index){
                                el.stopObserving();
                                el.observe('click', function (event){

                                    j2t_one_checkout.stepShippingMethodOk = false;
                                    j2t_one_checkout.stepPaymentMethodOk = false;
                                    j2t_one_checkout.stepOrderReviewOk = false;
                                    j2t_one_checkout.clickSaveShipping();
                                });
                            });


                            var j2t_onechekout_checked = false;
                            j2t_onechekout_shipment_radios.each(function (elmt, index){
                                if (elmt.checked){
                                    j2t_onechekout_checked = true;
                                }
                            });

                            if (!j2t_onechekout_checked && j2t_onechekout_shipment_radios.length > 0){
                                j2t_onechekout_shipment_radios[0].checked = true;
                            }
                            
                            if (typeof j2t_onecheckout_reload !== "undefined"){
                                j2t_onecheckout_reload();
                            }
                            /*if (j2t_one_checkout.stepReloadShippingAtEnd == true){
                                j2t_one_checkout.refreshPaymentSave();
                            } else {*/
                                j2t_one_checkout.reloadReviewFromSubmitShipping();
                            //}
                        }
                    }
                );
            //}
        }
    },
    refreshShippingMethodRadio: function (){
       j2t_one_checkout.stepShippingMethodOk = false;
       var shippingUrl = this.shippingUrl;
        if ($('j2t-shipping-please-wait')){
            new Ajax.Request(
                shippingUrl,
                {
                    asynchronous: true,
                    method: 'get',
                    onComplete: function(transport) {
                        
                        
                        /////////////////////////////////
                        $('checkout-shipping-method-load').innerHTML = transport.responseText;
                        j2t_one_checkout.stepShippingMethodOk = true;
                        j2t_one_checkout.hideLoading();

                        var j2t_onechekout_shipment_form = $('co-shipping-method-form');
                        var j2t_onechekout_shipment_radios = j2t_onechekout_shipment_form.getInputs('radio');
                        
                        var process_submit = true;
                        j2t_onechekout_shipment_radios.each(function (el, index){
                            el.stopObserving();
                            var process_autosubmit = true;
                            if (j2t_address_completion == 1){
                                el.autocheck = false;
                                process_submit = false;
                            } 
                            el.observe('click', function (event){
                                if (j2t_address_completion == 1){
                                    if (billing.validateSilent()){
                                        el.checked = true;
                                        process_submit = true;
                                    } else {
                                        el.checked = false;
                                        process_submit = false;
                                    }
                                }
                                
                                if (process_submit){
                                    //kialapoint-select
                                    if (el.value.indexOf("kiala") != -1) {
                                        if ($$('.kialapoint-select').length > 0){
                                            var kialapoint = $$('.kialapoint-select');
                                            var first_kiala = kialapoint[0];
                                            eval(first_kiala.getAttribute('onclick'));
                                        } else {
                                            j2t_one_checkout.saveRealShipping = false;
                                            if (j2t_address_completion == 1){
                                                j2t_one_checkout.saveRealShipping = true;
                                            }
                                            j2t_one_checkout.stepSoColissimo = false;
                                            j2t_one_checkout.stepShippingMethodOk = false;
                                            j2t_one_checkout.stepPaymentMethodOk = false;
                                            j2t_one_checkout.stepOrderReviewOk = false;
                                            j2t_one_checkout.clickSaveShipping();
                                        }
                                        
                                    } else if (el.value == rateCodeSoColissimoSimplicite && el.checked) {
                                        j2t_one_checkout.saveRealShipping = true;
                                        j2t_one_checkout.stepSoColissimo = true;
                                        j2t_one_checkout.stepShippingMethodOk = false;
                                        j2t_one_checkout.stepPaymentMethodOk = false;
                                        j2t_one_checkout.stepOrderReviewOk = false;
                                        j2t_one_checkout.clickSaveShipping();
                                        //open so colissimo iframe
                                        //redirectToColissimo();
                                       // $$('.so-colissimo').invoke('show');
                                    } else {
                                        j2t_one_checkout.saveRealShipping = false;
                                        if (j2t_address_completion == 1){
                                            j2t_one_checkout.saveRealShipping = true;
                                        }
                                        j2t_one_checkout.stepSoColissimo = false;
                                        j2t_one_checkout.stepShippingMethodOk = false;
                                        j2t_one_checkout.stepPaymentMethodOk = false;
                                        j2t_one_checkout.stepOrderReviewOk = false;
                                        j2t_one_checkout.clickSaveShipping();
                                    }
                                }
                            });
                        });



                        var j2t_onechekout_checked = false;
                        var is_socolissimo = false;
                        j2t_onechekout_shipment_radios.each(function (elmt, index){
                            if (elmt.checked){
                                j2t_onechekout_checked = true;
                                if (elmt.value == rateCodeSoColissimoSimplicite && elmt.checked) {
                                    //so colissimo selected - check if necessary to configure
                                    $$('.so-colissimo').invoke('show');
                                }
                            }
                        });

                        if (j2t_address_completion != 1 && !j2t_onechekout_checked && j2t_onechekout_shipment_radios.length > 0){
                            j2t_onechekout_shipment_radios[0].checked = true;
                        }
                        /////////////////////////////////
                        
                        if (j2t_one_checkout.stepReloadShippingAtEnd == true && j2t_one_checkout.stepReloadShippingAtEndAction == true){
                            //j2t_one_checkout.refreshShippingMethodRadio();
                            j2t_one_checkout.stepReloadShippingAtEnd = false;
                            j2t_one_checkout.stepReloadShippingAtEndAction = false;
                            //1.save shipping
                            //1.2.reload review
                            j2t_one_checkout.reloadReviewFromSubmitOnlyShipping();
                        }
                        
                        if (typeof j2t_onecheckout_reload !== "undefined"){
                            j2t_onecheckout_reload();
                        }
                        
                    }
                }
            );
        }
       
       
    },
    shippingMethodSave: function (){
        var j2t_onechekout_shipment_form = $('co-shipping-method-form');
        var j2t_onechekout_shipment_radios = j2t_onechekout_shipment_form.getInputs('radio');
        var j2t_onechekout_checked = false;
        j2t_onechekout_shipment_radios.each(function (elmt, index){
            if (elmt.checked){
                j2t_onechekout_checked = true;
            }
        });

        if (j2t_address_completion != 1 && !j2t_onechekout_checked && j2t_onechekout_shipment_radios.length > 0){
            j2t_onechekout_shipment_radios[0].checked = true;
        }

        setTimeout(function (){shippingMethod.saveRefresh();}, 200);
        //REFRESH PAYMENT + SAVE PAYMENT
    },
    reloadAll: function (){
        this.reloadShippingMethod();
        this.reloadPayment();
        this.reloadReview();
        this.reloadReviewCoupon();
    },
    reloadShippingMethod: function (){
        new Ajax.Request(
            this.shippingMethod,
            {
                asynchronous: true,
                method: 'get',
                onComplete: function(transport) {
                    $('co-shipping-method-form').innerHTML = transport.responseText;
                },
                onSuccess: this.shippingMethodSave.bind(this)
            }
        );
    },
    shippingSave: function (){
        setTimeout(function (){shippingMethod.save();}, 200);
    },
    reloadShippingSave: function(){
        
        var shippingUrl = this.shippingUrl;
        new Ajax.Request(
            shippingUrl,
            {
                asynchronous: true,
                method: 'get',
                onSuccess: function(transport) {
                    j2t_one_checkout.stepShippingComplete = false;
                    
                    $('checkout-shipping-method-load').innerHTML = transport.responseText;
                    var j2t_onechekout_shipment_form = $('co-shipping-method-form');
                    var j2t_onechekout_shipment_radios = j2t_onechekout_shipment_form.getInputs('radio');

                    var j2t_onechekout_checked = false;
                    j2t_onechekout_shipment_radios.each(function (elmt, index){
                        if (elmt.checked){
                            j2t_onechekout_checked = true;
                        }
                    });

                    if (j2t_address_completion != 1 && !j2t_onechekout_checked && j2t_onechekout_shipment_radios.length > 0){
                        var is_verified = false;
                        j2t_onechekout_shipment_radios.each(function (el, index){
                            if (!is_verified){
                                el.checked = true;
                                is_verified = true;
                            }
                        });
                    }
                    
                    if (typeof j2t_onecheckout_reload !== "undefined"){
                        j2t_onecheckout_reload();
                    }
                },
                onComplete: this.shippingSave.bind(this)
            }
        );
        
    },
    reloadShipping: function(){
        var shippingUrl = this.shippingUrl;
        if ($('j2t-shipping-please-wait')){
            new Ajax.Request(
                shippingUrl,
                {
                    asynchronous: true,
                    method: 'get',
                    onComplete: function(transport) {
                        j2t_one_checkout.stepShippingComplete = false;
                        $('checkout-shipping-method-load').innerHTML = transport.responseText;

                        var j2t_onechekout_shipment_form = $('co-shipping-method-form');
                        var j2t_onechekout_shipment_radios = j2t_onechekout_shipment_form.getInputs('radio');
                        
                        var j2t_onechekout_checked = false;
                        j2t_onechekout_shipment_radios.each(function (elmt, index){
                            if (elmt.checked){
                                j2t_onechekout_checked = true;
                            }
                        });
                        
                        if (j2t_address_completion != 1 && !j2t_onechekout_checked && j2t_onechekout_shipment_radios.length > 0){
                            j2t_onechekout_shipment_radios[0].checked = true;
                        }

                        j2t_one_checkout.stepShippingComplete = true;
                        j2t_one_checkout.stepProcess++;

                        j2t_one_checkout.reloadPayment();
                        
                        if (typeof j2t_onecheckout_reload !== "undefined"){
                            j2t_onecheckout_reload();
                        }

                    }
                }
            );
        }
    },

    reloadProcess: function(){
    }
}

var J2t_Billing = Class.create();
J2t_Billing.prototype = {
    initialize: function(form, addressUrl, saveUrl, shippingUrl, paymentUrl, reviewUrl){
        this.form = form;
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){
                Event.stop(event);
            }.bind(this));
        }
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.shippingUrl = shippingUrl;
        this.paymentUrl = paymentUrl;
        this.reviewUrl = reviewUrl;
        this.onSave = this.preNextStep.bindAsEventListener(this);
    },
    newAddress: function(isNew){
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('billing-new-address-form');
        } else {
            Element.hide('billing-new-address-form');
        }
    },
    resetSelectedAddress: function(){
        var selectElement = $('billing-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },
    preNextStep: function(transport){
        
        if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }

        if (response.error){
            if ((typeof response.message) == 'string') {
                alert(response.message);
            } else {
                if (window.billingRegionUpdater) {
                    billingRegionUpdater.update();
                }
                alert(response.message.join("\n"));
            }
            
            //CAPTCHA MODIFICATION
            /*if (response.error_type){
                if (response.error_type == 'captcha-guest'){
                    //captcha-image-box-guest_checkout
                    if ($('captcha-image-box-guest_checkout')){
                        $('guest_checkout').captcha.refresh($('captcha-reload'));
                    }
                } else if (response.error_type == 'captcha-register'){
                    if ($('captcha-image-box-register_during_checkout')){
                        $('register_during_checkout').captcha.refresh($('captcha-reload'));
                    }
                }
            }*/
            
            
            return false;
        } else {
            this.nextStep.bind(this);
        }

    },
    nextStep: function(parent){
        if (parent && parent.responseText){
            try{
                response = eval('(' + parent.responseText + ')');
            }
            catch (e) {
                response = {};
            }
        }

        if (response.error){
            j2t_one_checkout.stepShippingMethodOk = true;
            j2t_one_checkout.stepPaymentMethodOk = true;
            j2t_one_checkout.stepOrderReviewOk = true;
            j2t_one_checkout.hideLoading();
            if ((typeof response.message) == 'string') {
                alert(response.message);
            }
            
            //CAPTCHA MODIFICATION
            /*if (response.error_type){
                if (response.error_type == 'captcha-guest'){
                    //captcha-image-box-guest_checkout
                    if ($('captcha-image-box-guest_checkout')){
                        $('guest_checkout').captcha.refresh($('captcha-reload'));
                    }
                } else if (response.error_type == 'captcha-register'){
                    if ($('captcha-image-box-register_during_checkout')){
                        $('register_during_checkout').captcha.refresh($('captcha-reload'));
                    }
                }
            }*/
            
            return false;
        } else {
            if (j2t_one_checkout.doSaveShipping){
                if (j2t_one_checkout.saveRealShipping && j2t_one_checkout.stepSoColissimo){
                    //open so colissimo iframe
                    redirectToColissimo();
                }
                if (!$('billing:use_for_shipping_no') && !$('billing:use_for_shipping_yes')){
                    shippingMethod.save();
                } else if ($('billing:use_for_shipping_yes').checked){
                    shippingMethod.save();
                } else {
                    shipping.save();
                }            
                j2t_one_checkout.doSaveShipping = false;
            } else {
                j2t_one_checkout.refreshShippingMethodRadio();  
            }
        }
    },
    checkSave: function(){
        
        //submitShippingQuote
        var shippingQuoteCountryTxt = "";
        var shippingQuotePostCodeTxt = "";
        var shippingQuoteRegionTxt = "";
        if ($('billing:use_for_shipping_yes').checked){
            shippingQuoteCountryTxt = $F('billing:country_id');
            shippingQuotePostCodeTxt = $F('billing:postcode');
            if ($('billing:region_id'))
                shippingQuoteRegionTxt = $F('billing:region_id');
        } else {
            shippingQuoteCountryTxt = $F('shipping:country_id');
            shippingQuotePostCodeTxt = $F('shipping:postcode');
            if ($('shipping:region_id'))
                shippingQuoteRegionTxt = $F('shipping:region_id');
        }
        if (j2t_one_checkout.shippingQuoteCountry != shippingQuoteCountryTxt || j2t_one_checkout.shippingQuoteRegion != shippingQuoteRegionTxt || j2t_one_checkout.shippingQuotePostCode != shippingQuotePostCodeTxt){
            j2t_one_checkout.submitShippingQuote();
            
            j2t_one_checkout.shippingQuoteCountry = shippingQuoteCountryTxt;
            j2t_one_checkout.shippingQuoteRegion = shippingQuoteRegionTxt;
            j2t_one_checkout.shippingQuotePostCode = shippingQuotePostCodeTxt;
        } 
        
        var continue_process = true;
    },
            
    validateSilent: function(){
        if ($('billing:use_for_shipping_yes').checked){
            var validator = new Validation(this.form, {stopOnFirst: false, focusOnError:false});
            if (!validator.validate()) {
                validator.reset();
                return false;
            } else {
                return true;
            }
        } else {
            return shipping.validateSilent();
        }
        
    },        
            
    save: function(){
        var validator = new Validation(this.form);
        if (validator.validate()) {
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    asynchronous: true,
                    method: 'post',                    
                    onSuccess: this.nextStep.bind(this),
                    parameters: Form.serialize(this.form)
                }
            );
        }
    }
}

var J2t_Shipping = Class.create();
J2t_Shipping.prototype = {
    initialize: function(form, addressUrl, saveUrl, methodsUrl, paymentUrl, reviewUrl){
        this.form = form;
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){
                Event.stop(event);
            }.bind(this));
        }
        this.addressUrl = addressUrl;
        this.saveUrl = saveUrl;
        this.methodsUrl = methodsUrl;
        this.paymentUrl = paymentUrl;
        this.reviewUrl = reviewUrl;
    },
     newAddress: function(isNew){
        if (isNew) {
            this.resetSelectedAddress();
            Element.show('shipping-new-address-form');
        } else {
            Element.hide('shipping-new-address-form');
        }
        shipping.setSameAsBilling(false);

    },

    resetSelectedAddress: function(){
        var selectElement = $('shipping-address-select')
        if (selectElement) {
            selectElement.value='';
        }
    },

    setSameAsBilling: function(flag) {
        $('shipping:same_as_billing').checked = flag;

        if (flag) {
            this.syncWithBilling();
        }
    },
    syncWithBilling: function () {
        $('billing-address-select') && this.newAddress(!$('billing-address-select').value);
        $('shipping:same_as_billing').checked = true;
        if (!$('billing-address-select') || !$('billing-address-select').value) {
            arrElements = Form.getElements(this.form);
            for (var elemIndex in arrElements) {
                if (arrElements[elemIndex].id) {
                    var sourceField = $(arrElements[elemIndex].id.replace(/^shipping:/, 'billing:'));
                    if (sourceField){
                        arrElements[elemIndex].value = sourceField.value;
                    }
                }
            }
            shippingRegionUpdater.update();
            if ($('shipping:region_id'))
                $('shipping:region_id').value = $('billing:region_id').value;
            $('shipping:region').value = $('billing:region').value;
        } else {
            $('shipping-address-select').value = $('billing-address-select').value;
        }
    },
    nextStep: function(parent){
        if (j2t_one_checkout.doSaveShippingMethod){
            shippingMethod.save();
            j2t_one_checkout.doSaveShippingMethod = false;
        } else if (j2t_one_checkout.userLoggedAddress){
            j2t_one_checkout.refreshPaymentMethodInputs();
            j2t_one_checkout.refreshShippingMethodRadio();
        } else {
            j2t_one_checkout.reloadShippingSave();
        }
    },
    checkSaveReloadShippingMethod: function(){
        var validator = new Validation(this.form, {stopOnFirst: true, focusOnError:false});
        if (!validator.validate()) {
            validator.reset();
        } else if ($('billing:use_for_shipping_no') && $('billing:use_for_shipping_yes')){
            if ($('billing:use_for_shipping_no').checked) {
                this.save();
            } else if ($('billing:use_for_shipping_yes').checked) {
                this.save();
            }
        } else if (!$('billing:use_for_shipping_no') && !$('billing:use_for_shipping_yes')){
            this.save();
        }
    },
    validateSilent: function(){
        var validator = new Validation(this.form, {stopOnFirst: false, focusOnError:false});
        if (!validator.validate()) {
            validator.reset();
            return false;
        } else if ($('billing:use_for_shipping_no') && $('billing:use_for_shipping_yes')){
            if ($('billing:use_for_shipping_no').checked) {
                return true;
            } else if ($('billing:use_for_shipping_yes').checked) {
                return true;
            }
        } else {
            return true;
        }
        
    },
    checkSave: function(){
        var validator = new Validation(this.form, {stopOnFirst: true, focusOnError:false});
        if (!validator.validate()) {
            validator.reset();
        } else if ($('billing:use_for_shipping_no') && $('billing:use_for_shipping_yes')){
            if ($('billing:use_for_shipping_no').checked) {
                this.save();
            } else if ($('billing:use_for_shipping_yes').checked) {
                this.save();
            }
        }
        
    },
    nextStepSave: function(parent){
        
    },
    save: function(){
        var validator = new Validation(this.form);
        if (validator.validate()) {
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    asynchronous: true,
                    method:'post',
                    onSuccess: this.nextStep.bind(this),
                    parameters: Form.serialize(this.form)
                }
            );
        }
    }
}

var J2t_ShippingMethod = Class.create();
J2t_ShippingMethod.prototype = {
    initialize: function(form, saveUrl, shippingUrl, paymentUrl, reviewUrl, isVirtual){
        this.form = form;
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){
                Event.stop(event);
            }.bind(this));
        }
        this.saveUrl = saveUrl;
        this.validator = new Validation(this.form);
        this.shippingUrl = shippingUrl;
        this.paymentUrl = paymentUrl;
        this.reviewUrl = reviewUrl;
        this.isVirtual = isVirtual;
    },

    validate: function() {
        if (this.isVirtual) {
            return true;
        }
        var methods = document.getElementsByName('shipping_method');
        if (methods.length==0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no shipping methods available for it. Please make neccessary changes in your shipping address.'));
            return false;
        }

        if(!this.validator.validate()) {
            return false;
        }

        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }

        for (var i=0; i<methods.length; i++) {
            methods[i].checked = true;
            return true;
        }

        alert(Translator.translate('Please specify shipping method.'));
        return false;
    },
    nextStep: function(parent){
        if (j2t_one_checkout.doSavePaymentMethod){
            payment.save();
            j2t_one_checkout.doSavePaymentMethod = false;
            //j2t_one_checkout.refreshPaymentMethodInputs();
        } else {
            j2t_one_checkout.stepShippingComplete = true;
            if ((j2t_one_checkout.reloadShippingOnBilling && j2t_one_checkout.saveShippingOnBilling)){
                j2t_one_checkout.reloadPayment();
            } 
            if(j2t_one_checkout.reloadReviewOnShippingSave){
                j2t_one_checkout.stepShippingMethodOk = true;
                j2t_one_checkout.stepPaymentMethodOk = true;
                j2t_one_checkout.reloadReview();
                j2t_one_checkout.reloadReviewOnShippingSave = false;
            }
        }
        if (j2t_one_checkout.reloadPaymentMethodOnShipping){
            //j2t_one_checkout.showLoading();
            j2t_one_checkout.stepPaymentMethodOk = false;
            j2t_one_checkout.refreshPaymentSave();
        }
    },
    nextStepRefresh: function(parent){
        j2t_one_checkout.reloadReview();
        j2t_one_checkout.stepShippingComplete = true;
        j2t_one_checkout.refreshShippingMethodRadio();
        if (j2t_one_checkout.reloadPaymentMethodOnShipping){
            //j2t_one_checkout.showLoading();
            j2t_one_checkout.stepPaymentMethodOk = false;
            j2t_one_checkout.refreshPaymentSave();
        }
    },
    saveRefresh: function(){
        if (this.validate()) {
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    asynchronous: true,
                    method:'post',
                    onSuccess: this.nextStepRefresh.bind(this), 
                    parameters: Form.serialize(this.form)
                }
            );
        } else {
            j2t_one_checkout.hideLoading();
        }

    },
    save: function(){
        if (this.validate()) {
            var form_serialize = Form.serialize(this.form);
            j2t_one_checkout.showLoading();
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    asynchronous: true,
                    method:'post',
                    onSuccess: this.nextStep.bind(this), 
                    parameters: form_serialize
                }
            );
            j2t_one_checkout.completeOff();
        }
    }
}

var J2t_Payment = Class.create();
J2t_Payment.prototype = {
    beforeInitFunc:$H({}),
    afterInitFunc:$H({}),
    beforeValidateFunc:$H({}),
    afterValidateFunc:$H({}),
    initialize: function(form, saveUrl, shippingUrl, paymentUrl, reviewUrl){
        this.form = form;
        this.saveUrl = saveUrl;
        this.shippingUrl = shippingUrl;
        this.paymentUrl = paymentUrl;
        this.reviewUrl = reviewUrl;
    },

    beforeInit : function() {
        (this.beforeInitFunc).each(function(init){
           (init.value)();
        });
    },

    init : function () {
        this.beforeInit();
        var elements = Form.getElements(this.form);
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){
                Event.stop(event);
            }.bind(this));
        }
        var method = null;
        for (var i=0; i<elements.length; i++) {
            if (elements[i].name=='payment[method]') {
                if (elements[i].checked) {
                    method = elements[i].value;
                }
            } else {
                //elements[i].disabled = true;
            }
            elements[i].setAttribute('autocomplete','off');
        }
        if (method) this.switchMethod(method);
        this.afterInit();
    },
    addAfterInitFunction : function(code, func) {
        this.afterInitFunc.set(code, func);
    },

    afterInit : function() {
        (this.afterInitFunc).each(function(init){
            (init.value)();
        });
    },

    checkSave: function(){
        var validator = new Validation(this.form, {stopOnFirst: true, focusOnError:false});
        if (!validator.validate()) {
            validator.reset();
        } else {
            this.save();
        }
    },

    switchMethod: function(method){
        if (this.currentMethod && $('payment_form_'+this.currentMethod)) {
            var form = $('payment_form_'+this.currentMethod);
            form.style.display = 'none';
            var elements = form.select('input', 'select', 'textarea');
            //for (var i=0; i<elements.length; i++) elements[i].disabled = true;
        }
        if ($('payment_form_'+method)){
            var form = $('payment_form_'+method);
            form.style.display = '';
            var elements = form.select('input', 'select', 'textarea');
            //for (var i=0; i<elements.length; i++) elements[i].disabled = false;
        } else {
            //Event fix for payment methods without form like "Check / Money order"
            document.body.fire('payment-method:switched', {method_code : method});
        }
        this.currentMethod = method;
        
        var j2t_onechekout_payment_form = $('co-payment-form');
        var j2t_onechekout_payment_inputs = j2t_onechekout_payment_form.getInputs('text', 'password');
        
    },

    addBeforeValidateFunction : function(code, func) {
        this.beforeValidateFunc.set(code, func);
    },

    beforeValidate : function() {
        j2t_one_checkout.refreshPaymentMethodInputs();
        var validateResult = true;
        var hasValidation = false;
        (this.beforeValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },
    nextStep: function(parent){
        if (j2t_one_checkout.checkRedirect){
            //check if redirection is required
            if (parent && parent.responseText){
                try{
                    response = eval('(' + parent.responseText + ')');
                    if (response.redirect) {
                        location.href = response.redirect;
                        return true;
                    }
                }
                catch (e) {
                    response = {};
                }
            }
        }
        if (j2t_one_checkout.doSubmitOrder){
            j2t_one_checkout.reloadReview();
            j2t_one_checkout.doSubmitOrder = false;
        } else {
            if (j2t_one_checkout.needPaymentRefresh){
                //RELOAD PAYMENT METHOD & SUBMIT
                j2t_one_checkout.needPaymentRefresh = false;
            }
            j2t_one_checkout.reloadReview();
            j2t_one_checkout.stepPaymentComplete = true;
        }
    },
    saveNocheck: function (){
        j2t_one_checkout.showLoading();

        $$('#co-payment-form input').invoke('enable');
        $$('#co-payment-form radio').invoke('enable');
        $$('#co-payment-form select').invoke('enable');
        
        var request = new Ajax.Request(
            this.saveUrl,
            {
                asynchronous: true,
                method:'post',
                onSuccess: function (){
                    j2t_one_checkout.stepShippingMethodOk = true;
                    j2t_one_checkout.stepPaymentMethodOk = true;
                    j2t_one_checkout.reloadReview();
                },
                parameters: Form.serialize(this.form)
            }
        );
    },
    save: function(){
        var validator = new Validation(this.form);
        $$('#co-payment-form input').invoke('enable');
        $$('#co-payment-form radio').invoke('enable');
        $$('#co-payment-form select').invoke('enable');
        if (this.validate() && validator.validate()) {
            var request = new Ajax.Request(
                this.saveUrl,
                {
                    asynchronous: true,
                    method:'post',
                    onSuccess: this.nextStep.bind(this),
                    parameters: Form.serialize(this.form)
                }
            );
            j2t_one_checkout.showLoading();
            
        } else {
            j2t_one_checkout.hideLoading();
        }
    },
    afterValidate : function() {
        var validateResult = true;
        var hasValidation = false;
        (this.afterValidateFunc).each(function(validate){
            hasValidation = true;
            if ((validate.value)() == false) {
                validateResult = false;
            }
        }.bind(this));
        if (!hasValidation) {
            validateResult = false;
        }
        return validateResult;
    },
    validate: function() {
        var result = this.beforeValidate();
        if (result) {
            return true;
        }
        var methods = document.getElementsByName('payment[method]');
        if (methods.length==0) {
            alert(Translator.translate('Your order cannot be completed at this time as there is no payment methods available for it.'));
            return false;
        }
        for (var i=0; i<methods.length; i++) {
            if (methods[i].checked) {
                return true;
            }
        }
        result = this.afterValidate();
        if (result) {
            return true;
        }
        alert(Translator.translate('Please specify payment method.'));
        return false;
    }
}

var J2t_Review = Class.create();
J2t_Review.prototype = {
    initialize: function(saveUrl, successUrl, agreementsForm){
        this.saveUrl = saveUrl;
        this.successUrl = successUrl;
        this.agreementsForm = agreementsForm;
        this.onSave = this.nextStep.bindAsEventListener(this);
    },
    save: function(){
        
        var continue_order = true;
        
        //check shippping form
        var validator_shipping = new Validation(shipping.form, {stopOnFirst: false, focusOnError:false});
        if (!validator_shipping.validate()) {
            validator_shipping.reset();
            continue_order = false;
        }
        
        //check billing form
        var validator_billing = new Validation(billing.form, {stopOnFirst: false, focusOnError:false});
        if (!validator_billing.validate()) {
            validator_billing.reset();
            continue_order = false;
        } 
        
        //check shippping method form
        var validator_shippingmethod = new Validation(shippingMethod.form, {stopOnFirst: false, focusOnError:false});
        if (!validator_shippingmethod.validate()) {
            validator_shippingmethod.reset();
            continue_order = false;
        } 
        
        //check payment method form        
        var validator_payment = new Validation(payment.form, {stopOnFirst: false, focusOnError:false});
        if (!validator_payment.validate()) {
            validator_payment.reset();
            continue_order = false;
        } 
        
        if (continue_order){
            //j2t_one_checkout.submittingOrder = true;
            j2t_one_checkout.showLoading();
            j2t_one_checkout.checkRedirect = true;
            /*j2t_one_checkout.savePayment = true;
            j2t_one_checkout.goReview = true;
            j2t_one_checkout.needPaymentRefresh = true;*/
            
            j2t_one_checkout.saveAndSubmitAll();
            //billing.save();
        } else {
            alert(Translator.translate('Please review the checkout forms.'));
        }
        
    },
    
    saveGoReview: function () {
        j2t_one_checkout.setLoadWaiting('review');
        var params = Form.serialize(payment.form);
        if (this.agreementsForm) {
            params += '&'+Form.serialize(this.agreementsForm);
        }
        params.save = true;
        var request = new Ajax.Request(
            this.saveUrl,
            {
                asynchronous: true,
                method:'post',
                parameters:params,
                onComplete: function(transport){j2t_one_checkout.hideLoading();},
                onSuccess: this.onSave
            }
        );
        j2t_one_checkout.stepShippingMethodOk = true;
        j2t_one_checkout.stepPaymentMethodOk = true;
        j2t_one_checkout.stepOrderReviewOk = true;
        j2t_one_checkout.hideLoading();
    },
    
    nextStep: function(transport){
        j2t_one_checkout.hideLoading();
        if (transport && transport.responseText) {
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
            if (response.redirect) {
                location.href = response.redirect;
                return;
            }
            if (response.success) {
                this.isSuccess = true;
                window.location=this.successUrl;
            } else if (response.update_section) {
                
                if (!$('checkout-' + response.update_section.name + '-load')){
                    $$('.j2t-onecheckout-left-4')[0].insert({bottom: new Element('div', {id: 'checkout-' + response.update_section.name + '-load'})});
                    $('checkout-' + response.update_section.name + '-load').className = 'authentication';
                }
                var element_tobe_inserted = response.update_section.html;
                element_tobe_inserted = element_tobe_inserted.replace("checkout.accordion.container.readAttribute('id')",'\'\'');
                element_tobe_inserted = element_tobe_inserted.replace("checkout.loadWaiting",'var void_element');
                element_tobe_inserted = element_tobe_inserted.replace("checkout.setLoadWaiting(false);",'');
                element_tobe_inserted = element_tobe_inserted.replace("checkout.setLoadWaiting(true);",'');
                element_tobe_inserted = element_tobe_inserted.replace("checkout.accordion.currentSection", '\'opc-\'+void_element');
                
                /*$('checkout-' + response.update_section.name + '-load').update(response.update_section.html);
                response.update_section.html.evalScripts();*/
                $('checkout-' + response.update_section.name + '-load').update(element_tobe_inserted);
                element_tobe_inserted.evalScripts();
            } else{
                var msg = response.error_messages;
                if (typeof(msg)=='object') {
                    msg = msg.join("\n");
                }
                $('review-please-wait').hide();
                alert(msg);
            }

            if (response.update_section) {
                //$('checkout-'+response.update_section.name+'-load').update(response.update_section.html);
                //response.update_section.html.evalScripts();
            }

            if (response.goto_section) {
                //checkout.gotoSection(response.goto_section);
                //checkout.reloadProgressBlock();
            }
        }
    }
};