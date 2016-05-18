<?php
/**
 * 2007-2013 PrestaShop.
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
 * 	@author PrestaShop SA <contact@prestashop.com>
 * 	@copyright  2007-2016 PrestaShop SA
 * 	@license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @since 1.5.0
 */
class SgopaymentPaymentModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	/**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->display_column_left = false;
        parent::initContent();

        $cart = $this->context->cart;
        if (!$this->module->checkCurrency($cart)) {
            Tools::redirect('index.php?controller=order');
        }

        $config = Configuration::getMultiple(array(
                'SGO_PAYMENT_ID',
                'SGO_PAYMENT_PASS',
        ));

        $paymentId = $config ['SGO_PAYMENT_ID'];
        /*
         * $currency = new CurrencyCore($cart->id_currency);
         * $currencyCode = $currency->iso_code;
         */

        $this->context->smarty->assign(array(
                'sgo_payment_id' => $paymentId,
                'payment_id' => $cart->id,
                'nbProducts' => $cart->nbProducts(),
                'cust_currency' => $cart->id_currency,
                'currencies' => $this->module->getCurrency((int) $cart->id_currency),
                'total' => $cart->getOrderTotal(true, Cart::BOTH),
                'this_path' => $this->module->getPathUri(),
                'list_product' => $this->listproduct(),
                'this_path_bw' => $this->module->getPathUri(),
                'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/',
                'back_url' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER ['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/validation.php',
        ));

        $this->setTemplate('payment_execution.tpl');
    }
}
