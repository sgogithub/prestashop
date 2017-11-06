<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class EspayValidationModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
        public $display_column_left = false;
        public $display_column_right = false;
        
        private $_product_code;
        private $_bank_code;
        private $_order_id;
        
        private $_total;
        

	public function postProcess()
	{
            $this->_product_code = Tools::getValue('productcode');
            $this->_total = Tools::getValue('totaltrx');
            $this->_bank_code = Tools::getValue('bankcode');
            $fee = Tools::getValue('fee');
            $cart = $this->context->cart;
            
            
            $this->_order_id = $this->module->createOrder($cart->id,$this->_total, $this->_product_code, $fee);
            $cart->delete();

	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
                 $this->display_column_left = false;
                 $this->display_column_right = false;
                
                $env = ConfigurationCore::get('ESPAY_ENVIRONMENT');
                $url_js = ($env === 'sandbox' ? 'http://sandbox-kit.espay.id' : 'https://kit.espay.id');
                 
		$this->context->smarty->assign(array(
			'bank_code' => $this->_bank_code,
                        'product_code' => $this->_product_code,
                        'order_id' => $this->_order_id,
                        'url_js' => $url_js,
                        'key' => ConfigurationCore::get('ESPAY_PAYMENT_KEY'),
                        'this_path' => $this->module->getPathUri(),//keep for retro compat
			'this_path_cod' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));

		$this->setTemplate('validation.tpl');
	}
}
