<?php
class Extendware_EWPageCache_Model_Observer_Frontend
{
	
	static public function processPreDispatch(Varien_Event_Observer $observer)
	{
		if (Mage::helper('ewcore')->getRequestRoute() != 'directory/currency/switch') {
			$currencyCookie = defined('Mage_Core_Model_Store::COOKIE_CURRENCY') ? Mage_Core_Model_Store::COOKIE_CURRENCY : 'currency';
			if (isset($_COOKIE[$currencyCookie]) === true) {
				if ($_COOKIE[$currencyCookie] != Mage::app()->getStore()->getCurrentCurrencyCode()) {
					Mage::app()->getStore()->setCurrentCurrencyCode($_COOKIE[$currencyCookie]);
				} elseif ($_COOKIE[$currencyCookie] == Mage::app()->getStore()->getDefaultCurrencyCode()) {
					Mage::app()->getCookie()->delete($currencyCookie);
					unset($_COOKIE[$currencyCookie]);
				} else {
					Mage::app()->getCookie()->set($currencyCookie, $_COOKIE[$currencyCookie]);
				}
			} else {
				if (Mage::app()->getStore()->getCurrencyCurrencyCode() and Mage::app()->getStore()->getCurrencyCurrencyCode() != Mage::app()->getStore()->getDefaultCurrencyCode()) {
					Mage::app()->getStore()->setCurrentCurrencyCode(Mage::app()->getStore()->getDefaultCurrencyCode());
				}
			}
			
			if (Mage::app()->getStore()->getData('current_currency')) {
				if (Mage::app()->getStore()->getData('current_currency')->getCode() != Mage::app()->getStore()->getCurrencyCurrencyCode()) {
					Mage::app()->getStore()->setData('current_currency', null);
				}
			}
		}
		
		if (isset($_GET['___store']) === false) {
			$storeCookie = defined('Mage_Core_Model_Store::COOKIE_NAME') ? Mage_Core_Model_Store::COOKIE_NAME : 'store';
			if (isset($_COOKIE[$storeCookie]) === true) {
				if ($_COOKIE[$storeCookie] != Mage::app()->getStore()->getCode() or $_COOKIE[$storeCookie] == Mage::app()->getStore()->getWebsite()->getDefaultStore()->getCode()) {
					Mage::app()->getCookie()->delete($storeCookie);
					unset($_COOKIE[$storeCookie]);
				} else {
					Mage::app()->getCookie()->set($storeCookie, $_COOKIE[$storeCookie]);
				}
			} else {
				if (Mage::app()->getStore()->getCurrencyCurrencyCode() and Mage::app()->getStore()->getCurrencyCurrencyCode() != Mage::app()->getStore()->getDefaultCurrencyCode()) {
					Mage::app()->getStore()->setCurrentCurrencyCode(Mage::app()->getStore()->getDefaultCurrencyCode());
				}
			}
		}
		
		
		if (Mage::helper('ewpagecache')->isPageCacheEnabled() === true) {
			$helper = Mage::helper('ewpagecache');
			$action = $observer->getControllerAction();
			if ($action) {
				if ($action instanceof Mage_Core_Controller_Front_Action) {
					Mage::getSingleton('core/session')->setData('_form_key', $helper->getFrontendFormKey());
				}
			}
			
			$helper->log(sprintf('Running processPreDispatch()'));
			
			$cache2 = Mage::getSingleton('ewpagecache/cache_secondary');
			$activeKeys = $helper->getPageRule('active_virtual_keys');
			if (empty($activeKeys) === false) {
				$virtualKeys = $helper->getActiveVirtualKeys($activeKeys);
				$cookieValues = $helper->getVirtualKeysValuesFromCookies(null, true);
				foreach ($virtualKeys as $virtualKey) {
					$cookie = isset($cookieValues[$virtualKey['alias']]) ? $cookieValues[$virtualKey['alias']] : array();
					foreach ($virtualKey['model_params'] as $name => $source) {
						$model = Mage::getSingleton($source['model']);
						if ($model) {
							if ($source['key']) {
								$value = (isset($cookie[$name]) ? $cookie[$name] : null);
								$model->setData($source['key'], $value);
							}
						}
					}
				}
			}
		}
	}
	
	static public function sendResponseBefore(Varien_Event_Observer $observer)
	{
		if (Mage::helper('ewpagecache')->isPageCacheEnabled() === false) return;
		$buffer = ob_get_contents();
		ob_end_clean();
		if ($buffer) {
			Mage::app()->getResponse()->setBody($buffer . Mage::app()->getResponse()->getBody());
		}
		
		$cache1 = Mage::getSingleton('ewpagecache/cache_primary');
		$cache2 = Mage::getSingleton('ewpagecache/cache_secondary');
		
		$helper = $cache2->getHelper();
		$config = $cache2->getConfig();
		
		$helper->log(sprintf('Running sendResponseBefore()'));
		
		$helper->setAndCheckState();
		$helper->sendIsNotDefaultRequestCookie();
		
		$enabled = $helper->isPageCacheEnabled();
		if ($enabled === false) return;
		
		if ($config->isPrimaryCacheReentryEnabled() === true) {
			$hasMessages = false;
			$sessionKeys = array('core/session', 'catalog/session', 'checkout/session', 'customer/session');
			foreach ($sessionKeys as $sessionKey) {
				$messageCount = Mage::getSingleton($sessionKey)->getMessages()->count();
				if ($messageCount > 0) {
					$helper->sendCookie($config->getPrimaryDisablerCounterCookieName(), 1);
					$hasMessages = true;
					break;
				}
			}

			if ($hasMessages === false) {
				if (isset($_COOKIE[$config->getPrimaryDisablerCounterCookieName()]) and $_COOKIE[$config->getPrimaryDisablerCounterCookieName()] > 0) {
					$counter = $_COOKIE[$config->getPrimaryDisablerCounterCookieName()] - 1;
					if ($counter <= 0) $helper->deleteCookie($config->getPrimaryDisablerCounterCookieName());
					else $helper->sendCookie($config->getPrimaryDisablerCounterCookieName(), $counter);
				}
			}
		}
		
		$helper->recordRecentlyViewedProductFromRequest(Mage::app()->getRequest());
		
		$helper->sendVirtualKeyCookies($cache2->getVirtualKeysForSave());
		$helper->sendVirtualKeyValueCookies($helper->getVirtualKeysCookieValue($cache2->getVirtualKeysForSave()));
		
		if (Extendware_EWPageCache_Model_Cache_Secondary::$hasPassedOptions === false) {
			if ($enabled === true) {
				if ($config->isOutputHeadersEnabled() === true) {
					if (@$helper->isAllowedByIpRules() === true) {
						Mage::app()->getResponse()->setHeader('X-PageCache-Defaultable', (int)$cache2->isDefaultRequest());
						Mage::app()->getResponse()->setHeader('X-PageCache-PagePath', Mage::app()->getRequest()->getModuleName().'-'.Mage::app()->getRequest()->getControllerName().'-'.Mage::app()->getRequest()->getActionName());
					}
				}
			}
		} else {
			if ($cache2->getPassedOptions('destroy_session')) {
				@session_destroy();
			}
			
			if ($cache2->getPassedOptions('exit_before_output')) {
				// ensure the page gets saved in the cache before exiting
				self::sendResponseAfter($observer);
				exit;
			}
		}
	}
	
	static public function sendResponseAfter(Varien_Event_Observer $observer)
	{
		if (Mage::helper('ewpagecache')->isPageCacheEnabled() === false) return;
		
		$cache1 = Mage::getSingleton('ewpagecache/cache_primary');
		$cache2 = Mage::getSingleton('ewpagecache/cache_secondary');
		
		$cache1->getHelper()->log(sprintf('Running sendResponseAfter()'));
		
		if ($cache2->isEnabled() === true) {
			$cache2->save();
		} elseif ($cache1->isEnabled() === true) {
			if ($cache2->isDefaultRequest() === true) {
				$cache2->save();
			}
		}
	}
	
	static public function logoutSuccessAction(Varien_Event_Observer $observer)
	{
		if (Mage::helper('ewpagecache/config')->isPrimaryCacheReentryEnabled() === true) {
			$helper = Mage::helper('ewpagecache');
			$helper->setIsNotDefaultRequestReason(false, 'post');
			
			$quote = Mage::getSingleton('checkout/session')->getQuote();
			$helper->setIsNotDefaultRequestReason(($quote->getItemsCount() > 0), 'cart');
			
			$compareCount = Mage::getSingleton('catalog/product_compare_list')->hasItems(Mage::getSingleton('customer/session')->getCustomer()->getId(), Mage::getSingleton('log/visitor')->getId());
			$helper->setIsNotDefaultRequestReason(($compareCount > 0), 'compare');
		}
	}
	
	static public function setNotDefaultRequest(Varien_Event_Observer $observer)
	{
		$reason = 'default';
		$value = true;
		$eventName = $observer->getEvent()->getName();
		if (in_array($eventName, array('customer_login', 'customer_logout'))) {
			$reason = 'login';
			$value = in_array($eventName, array('customer_login'));
		} elseif (in_array($eventName, array('catalog_product_compare_add_product', 'catalog_product_compare_remove_product', 'controller_action_postdispatch_catalog_product_compare_clear'))) {
			$reason = 'compare';
			$compareCount = Mage::getSingleton('catalog/product_compare_list')->hasItems(Mage::getSingleton('customer/session')->getCustomer()->getId(), Mage::getSingleton('log/visitor')->getId());
			$value = (bool)($compareCount > 0 or in_array($eventName, array('catalog_product_compare_add_product')));
		}
		Mage::helper('ewpagecache')->log(sprintf('Running observer setNotDefaultRequest(%s, %s)', (int)$value, $reason));
		Mage::helper('ewpagecache')->setIsNotDefaultRequestReason($value, $reason);
	}

	static public function salesQuoteSaveAfter(Varien_Event_Observer $observer)
	{
		Mage::helper('ewpagecache')->log(sprintf('Running salesQuoteSaveAfter()'));
		if (Mage::helper('ewpagecache/config')->isPrimaryCacheReentryEnabled() === true) {
			$quote = $observer->getQuote();
			if ($quote->getItemsCount() > 0) Mage::helper('ewpagecache')->setIsNotDefaultRequestReason(true, 'cart');
			else {
				Mage::helper('ewpagecache')->setIsNotDefaultRequestReason(false, 'cart');
				Mage::helper('ewpagecache')->setIsNotDefaultRequestReason(false, 'post');
			}
		} else Mage::helper('ewpagecache')->setIsNotDefaultRequestReason(true, 'cart');
	}
	
	static public function cmsPageRender(Varien_Event_Observer $observer)
	{
		if (Mage::helper('ewpagecache/config')->isTaggingEnabled() === true) {
			if (in_array(Mage::helper('ewpagecache/config')->getCmsAutoTaggingMode(), array('lite', 'medium', 'heavy')) === true) {
				$pageId = $observer->getEvent()->getPage()->getId();
				Mage::helper('ewpagecache/api')->addCmsPageIdsAsTagsForSave(array($pageId));
			}
		}
	}
	
	static public function catalogProductView(Varien_Event_Observer $observer)
	{
		if (Mage::helper('ewpagecache/config')->isTaggingEnabled() === true) {
			if (in_array(Mage::helper('ewpagecache/config')->getProductAutoTaggingMode(), array('lite', 'medium', 'heavy')) === true) {
				$productId = $observer->getEvent()->getProduct()->getId();
				if ($productId > 0) Mage::helper('ewpagecache/api')->addProductIdsAsTagsForSave(array($productId));
			}
			
			if (in_array(Mage::helper('ewpagecache/config')->getCategoryAutoTaggingMode(), array('medium', 'heavy')) === true) {
				$categoryId = (int) Mage::app()->getRequest()->getParam('category', false);
				if ($categoryId) Mage::helper('ewpagecache/api')->addCategoryIdsAsTagsForSave(array($categoryId));
			}
		}
	}
	
	static public function catalogCategoryView(Varien_Event_Observer $observer)
	{
		if (Mage::helper('ewpagecache/config')->isTaggingEnabled() === true) {
			if (in_array(Mage::helper('ewpagecache/config')->getCategoryAutoTaggingMode(), array('lite', 'medium', 'heavy')) === true) {
				$category = $product = $observer->getEvent()->getCategory();
				if (!$category) return;
				
				$categoryId = (int)$category->getId();
				if ($categoryId) Mage::helper('ewpagecache/api')->addCategoryIdsAsTagsForSave(array($categoryId));
			}
		}
	}
	
	static public function catalogBlockProductListCollection(Varien_Event_Observer $observer)
	{
		if (Mage::helper('ewpagecache/config')->isTaggingEnabled() === true) {
			if (in_array(Mage::helper('ewpagecache/config')->getProductAutoTaggingMode(), array('medium')) === true) {
				$collection = $observer->getEvent()->getCollection();
				$ids = $collection->getColumnValues('entity_id');
				if (empty($ids) === false) Mage::helper('ewpagecache/api')->addProductIdsAsTagsForSave($ids);
			}
			
			if (in_array(Mage::helper('ewpagecache/config')->getCategoryAutoTaggingMode(), array('lite', 'medium', 'heavy')) === true) {
				$category = Mage::registry('current_category');
				if ($category) Mage::helper('ewpagecache/api')->addCategoryIdsAsTagsForSave(array($category->getId()));
			}
		}
	}
	
	static public function catalogProductCollectionLoadAfter(Varien_Event_Observer $observer) {
		if (Mage::helper('ewpagecache/config')->isTaggingEnabled() === true) {
			if (in_array(Mage::helper('ewpagecache/config')->getProductAutoTaggingMode(), array('heavy')) === true) {
				$collection = $observer->getEvent()->getCollection();
				$ids = $collection->getColumnValues('entity_id');
				if (empty($ids) === false) Mage::helper('ewpagecache/api')->addProductIdsAsTagsForSave($ids);
			}
		}
	}
	
	static public function catalogProductLoadAfter(Varien_Event_Observer $observer) {
		if (Mage::helper('ewpagecache/config')->isTaggingEnabled() === true) {
			if (in_array(Mage::helper('ewpagecache/config')->getProductAutoTaggingMode(), array('heavy')) === true) {
				$product = $observer->getEvent()->getProduct();
				if ($product and $product->getId() > 0) {
					Mage::helper('ewpagecache/api')->addProductIdsAsTagsForSave(array($product->getId()));
				}
			}
		}
	}
}