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
class EspayPaymentModuleFrontController extends ModuleFrontController {

    public $ssl = true;
    public $display_column_left = false;
    public $display_column_right = false;
    
    private $_payment;
    private $_fee;
    private $_product_code;
    private $_bank_code;
    
    public function postProcess() {
        $this->_payment = Tools::getValue('espayproduct');
        
        $cart = $this->context->cart;
        $product = explode(':', $this->_payment);
        $config = $this->module->getConfigFieldsValues();
        $this->_fee = $config['ESPAY_'.strtoupper(trim($product[1])).'_FEE'];
        
        if (strtoupper(trim($product[1])) == 'CREDITCARD'){
            $totalTrx = $cart->getOrderTotal(true, Cart::BOTH) + $this->_fee;
            $ccFee = floatval($config['ESPAY_CREDITCARD_MDR'] / 100)  * $totalTrx;
            
            $this->_fee += $ccFee;
        }
      
        $this->_product_code = trim($product[1]);
        $this->_bank_code = trim($product[0]);
        
                
    }
    /**
     * @see FrontController::initContent()
     */
    public function initContent() {

        parent::initContent();



        $cart = $this->context->cart;
        if (!$this->module->checkCurrency($cart))
            Tools::redirect('index.php?controller=order');
        
        $config = $this->module->getConfigFieldsValues();
        if ($config['ESPAY_ENABLE_FEE'] == 1){
            $totalTrx = $cart->getOrderTotal(true, Cart::BOTH) + $this->_fee;
        }else {
            $totalTrx = $cart->getOrderTotal(true, Cart::BOTH);
        }
        $this->context->smarty->assign(array(
            'error_message' => '',
            'fee' => $this->_fee,
            'config' => $this->module->getConfigFieldsValues(),
            'nbProducts' => $cart->nbProducts(),
            'cust_currency' => $cart->id_currency,
            'total_trx' => $totalTrx,
            'bank_code' => $this->_bank_code,
            'product_code' => $this->_product_code,
            'currencies' => $this->module->getCurrency((int) $cart->id_currency),
            'total' => $cart->getOrderTotal(true, Cart::BOTH),
            'this_path' => $this->module->getPathUri(),
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->module->name . '/'
        ));
        $this->setTemplate('payment_execution.tpl');
    }

}
