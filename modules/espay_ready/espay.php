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

if (!defined('_PS_VERSION_'))
    exit;

include (dirname(__FILE__) . '/lib/curl.php');

class Espay extends PaymentModule {

    public function __construct() {
        $this->name = 'espay';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.1';
        $this->author = 'PT. PLUS';
        $this->need_instance = 1;
        $this->controllers = array('validation', 'inquiry', 'paymentreport', 'confirmation');
        $this->is_eu_compatible = 0;
        $this->bootstrap = true;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';


        parent::__construct();

        $this->displayName = $this->l('ESPay');
        $this->description = $this->l('Accept Online Payment Via ESPay Payment Gateways');
        $this->confirmUninstall = $this->l('Are you sure about removing these details?');

        $this->_typePayment = array (
            'PERMATAATM' => "ATM Transfer",
            'BIIATM' => "ATM Transfer",
            'BCAKLIKPAY' => "Online Payment",
            'DKIIB' => "Online Payment",
            "EPAYBRI" => "Online Payment",
            "MANDIRIIB" => "Online Payment",
            "PERMATANETPAY" => "Online Payment",
            "NOBUPAY" => "E-Money",
            "SGOEMONEY" => "E-Money",
            "EMOEDIKK2" => "E-Money",
            "MANDIRIECASH" => "E-Money",
            "XLTUNAI" => "E-Money",
            "TCASH" => "E-Money",
            "MAYAPADAIB" => "Online Payment",
            "DANAMONOB" => "Online Payment",
            'CREDITCARD' => 'Credit Card',
            'MANDIRISMS' => 'Online Payment',
            'BNIDOB' => 'Online Payment',
            'FINPAY195' => 'Outlet Payment'
        );

        $this->_config_keys = array (
            'ESPAY_PAYMENT_TITLE',
            'ESPAY_ENABLE_FEE',
            'ESPAY_PAYMENT_KEY',
            'ESPAY_SIGNATURE_KEY',
            'ESPAY_SUCCESS_STATUS',
            'ESPAY_FAILED_STATUS',
            'ESPAY_WAITING_STATUS',
            'ESPAY_PAYMENT_PASS',
            'ESPAY_ENVIRONMENT',
            'ESPAY_MANDIRIIB_FEE',
            'ESPAY_BCAKLIKPAY_FEE',
            'ESPAY_EPAYBRI_FEE',
            'ESPAY_PERMATANETPAY_FEE',
            'ESPAY_PERMATAATM_FEE',
            'ESPAY_BIIATM_FEE',
            'ESPAY_CREDITCARD_FEE',
            'ESPAY_CREDITCARD_MDR',
            'ESPAY_FINPAY195_FEE',
            'ESPAY_MANDIRIECASH_FEE',
            'ESPAY_NOBUPAY_FEE',
            'ESPAY_BITCOIN_FEE',
            'ESPAY_DANAMONOB_FEE',
            'ESPAY_MANDIRISMS_FEE',
        );

        $config = Configuration::getMultiple($this->_config_keys);


        if (!isset($config['ESPAY_PAYMENT_TITLE'])) {
            Configuration::set('ESPAY_PAYMENT_TITLE', 'ESPay Payment Gateways');
        }
        if (!isset($config['ESPAY_ENABLE_FEE'])) {
            Configuration::set('ESPAY_ENABLE_FEE', 1);
        }
        if (!isset($config['ESPAY_BCAKLIKPAY_FEE'])) {
            Configuration::set('ESPAY_BCAKLIKPAY_FEE', 0);
        }
        if (!isset($config['ESPAY_MANDIRIIB_FEE'])) {
            Configuration::set('ESPAY_MANDIRIIB_FEE', 0);
        }
        if (!isset($config['ESPAY_EPAYBRI_FEE'])) {
            Configuration::set('ESPAY_EPAYBRI_FEE', 0);
        }
        if (!isset($config['ESPAY_PERMATANETPAY_FEE'])) {
            Configuration::set('ESPAY_PERMATANETPAY_FEE', 0);
        }
        if (!isset($config['ESPAY_PERMATAATM_FEE'])) {
            Configuration::set('ESPAY_PERMATAATM_FEE', 0);
        }
        if (!isset($config['ESPAY_BIIATM_FEE'])) {
            Configuration::set('ESPAY_BIIATM_FEE', 0);
        }
        if (!isset($config['ESPAY_CREDITCARD_FEE'])) {
            Configuration::set('ESPAY_CREDITCARD_FEE', 0);
        }
        if (!isset($config['ESPAY_CREDITCARD_MDR'])) {
            Configuration::set('ESPAY_CREDITCARD_MDR', '3%');
        }
        if (!isset($config['ESPAY_FINPAY195_FEE'])) {
            Configuration::set('ESPAY_FINPAY195_FEE', 0);
        }
        if (!isset($config['ESPAY_MANDIRIECASH_FEE'])) {
            Configuration::set('ESPAY_MANDIRIECASH_FEE', 0);
        }
        if (!isset($config['ESPAY_NOBUPAY_FEE'])) {
            Configuration::set('ESPAY_NOBUPAY_FEE', 0);
        }
        if (!isset($config['ESPAY_BITCOIN_FEE'])) {
            Configuration::set('ESPAY_BITCOIN_FEE', 0);
        }
        if (!isset($config['ESPAY_DANAMONOB_FEE'])) {
            Configuration::set('ESPAY_DANAMONOB_FEE', 0);
        }
        if (!isset($config['ESPAY_MANDIRISMS_FEE'])) {
            Configuration::set('ESPAY_MANDIRISMS_FEE', 0);
        }
    }
    
     public function install() {
        if (!parent::install() || !$this->registerHook('payment') || !$this->registerHook('orderConfirmation')) {
            return false;
        }


        $this->_createOrderState('ESPAY_WAITING_STATE', $this->l('Awaiting Payment'));


        return true;
    }

    public function uninstall() {
        $status = TRUE;
        $this->_deleteOrderState('ESPAY_WAITING_STATE');
        if (!parent::uninstall()) {
            $status = FALSE;
        }

        return $status;
    }
 private function _postValidation() {
        if (Tools::isSubmit('btnSubmit')) {
            if (!Tools::getValue('ESPAY_PAYMENT_KEY')) {
                $this->_postErrors [] = $this->l('ESPay Payment Key is required.');
            }
            if (!Tools::getValue('ESPAY_PAYMENT_PASS')) {
                $this->_postErrors [] = $this->l('ESPay Payment Password is required.');
            }
        }
    }

    public function getContent() {
        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= '<div class="alert alert-danger error">' . $err . '</div>';
                }
            }
        } else {
            $this->_html .= '<br />';
        }
        $this->_displayInfo();
        $this->_displayForm();

        return $this->_html;
    }

    public function getConfigFieldsValues() {
        $result = array();
        foreach ($this->_config_keys as $key) {
            $result[$key] = Tools::getValue($key, Configuration::get($key));
        }
        return $result;
    }

    public function checkCurrency($cart) {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module))
            foreach ($currencies_module as $currency_module)
                if ($currency_order->id == $currency_module['id_currency'])
                    return true;
        return false;
    }
 private function _displayForm() {
        $order_states = array();
        foreach (OrderState::getOrderStates($this->context->language->id) as $state) {
            array_push($order_states, array(
                'id_option' => $state['id_order_state'],
                'name' => $state['name']
                    )
            );
        }

        $environments = array(
            array(
                'id_option' => 'sandbox',
                'name' => 'Sandbox'
            ),
            array(
                'id_option' => 'production',
                'name' => 'Production'
            )
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Basic Information'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'select',
                        'label' => 'Environment',
                        'name' => 'ESPAY_ENVIRONMENT',
                        'required' => true,
                        'options' => array(
                            'query' => $environments,
                            'id' => 'id_option',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'ESPay Payment Title',
                        'name' => 'ESPAY_PAYMENT_TITLE',
                        'required' => true,
                        'desc' => $this->l('Please put the title of the Payment Gateways.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'ESPay Payment Key',
                        'name' => 'ESPAY_PAYMENT_KEY',
                        'required' => true,
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                     array(
                        'type' => 'text',
                        'label' => 'ESPay Signature Key',
                        'name' => 'ESPAY_SIGNATURE_KEY',
                        'required' => true,
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'ESPay Payment Password',
                        'name' => 'ESPAY_PAYMENT_PASS',
                        'required' => true,
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.')
                    ),
                    array(
                        'type' => 'select',
                        'label' => 'Map payment success status to:',
                        'name' => 'ESPAY_SUCCESS_STATUS',
                        'required' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    //'class' => ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => 'Map payment failure status to:',
                        'name' => 'ESPAY_FAILED_STATUS',
                        'required' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    //'class' => ''
                    ),
                    array(
                        'type' => 'select',
                        'label' => 'Map payment waiting status to:',
                        'name' => 'ESPAY_WAITING_STATUS',
                        'required' => true,
                        'options' => array(
                            'query' => $order_states,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                    //'class' => ''
                    ),
                    array(
                        'type' => 'switch',
                        'label' => 'Enable Fee',
                        'name' => 'ESPAY_ENABLE_FEE',
                        'is_bool' => true,
                        'desc' => $this->l('if no customer will not need to pay fee for using espay fee.'),
                        'values' => array(
                            array(
                                'id' => 'fee_yes',
                                'value' => 1,
                                'label' => 'Yes'
                            ),
                            array(
                                'id' => 'fee_no',
                                'value' => 0,
                                'label' => 'No'
                            )
                        ),
                    //'class' => ''
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'Mandiri Internet Banking Fee',
                        'name' => 'ESPAY_MANDIRIIB_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'BCA Klikpay Fee',
                        'name' => 'ESPAY_BCAKLIKPAY_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'EpayBri Fee',
                        'name' => 'ESPAY_EPAYBRI_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'PERMATANet Pay Fee',
                        'name' => 'ESPAY_PERMATANETPAY_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'PERMATA Virtual Account Fee',
                        'name' => 'ESPAY_PERMATAATM_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'Maybank Virtual Account Fee',
                        'name' => 'ESPAY_BIIATM_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'Credit Card Fee',
                        'name' => 'ESPAY_CREDITCARD_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'Credit Card Merchant Discount Rate(MDR)',
                        'name' => 'ESPAY_CREDITCARD_MDR',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'Outlet Payment Fee (Alfamart, Indomaret, Penggadaian, PT. POS)',
                        'name' => 'ESPAY_FINPAY195_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'Mandiri Ecash Payment Fee',
                        'name' => 'ESPAY_MANDIRIECASH_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'NobuPay Payment Fee ',
                        'name' => 'ESPAY_NOBUPAY_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'BitCoin Payment Fee ',
                        'name' => 'ESPAY_BITCOIN_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'Danamon Online Banking Payment Fee ',
                        'name' => 'ESPAY_DANAMONOB_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => 'Mandiri SMS Payment Fee ',
                        'name' => 'ESPAY_MANDIRISMS_FEE',
                        'desc' => $this->l('Consult to ESpay Merchant Administrator for the value of this field.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            )
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        // Load current value

        $this->_html .= $helper->generateForm(array($fields_form));
    }

    private function _displayInfo() {
        if (version_compare(Configuration::get('PS_VERSION_DB'), '1.5') == -1) {
            $output = $this->context->smarty->fetch(__DIR__ . '/views/templates/hook/info.tpl');
            $this->_html .= $output;
        } else {
            $this->_html .= $this->display(__FILE__, 'info.tpl');
        }
    }

    private function _postProcess() {
        if (Tools::isSubmit('btnSubmit')) {
            foreach ($this->_config_keys as $key) {

                ConfigurationCore::updateValue($key, Tools::getValue($key));
            }
        }
        $this->_html .= '<div class="alert alert-success conf confirm"> ' . $this->l('Settings updated') . '</div>';
    }

    private function _getListProduct() {
        $url = (Tools::getValue('espay_environment') === 'production' ? 'https://116.90.162.172:812/' : 'http://116.90.162.170:10809') . '/rest/merchant/merchantinfo';
        $curl = new Curl();
        $param['key'] = Configuration::get('ESPAY_PAYMENT_KEY');
        $products = $curl->call($param, $url);
        
        
        
        return $products;
    }

    private function _getProductView($listProduct) {
        $productView = array();
        $i = 0;
        foreach ($listProduct->data as $product) {
            if (array_key_exists($product->productCode, $this->_typePayment)) {

                $productView[$this->_typePayment[$product->productCode]][$i]['name'] = $product->productName;
                $productView[$this->_typePayment[$product->productCode]][$i]['bank'] = $product->bankCode;
                $productView[$this->_typePayment[$product->productCode]][$i]['product'] = $product->productCode;
                $i++;
            }
        }

        return $productView;
    }

    private function _listTabView($listProduct) {
        $tabView = array();


        foreach ($listProduct->data as $product) {
            if (array_key_exists($product->productCode, $this->_typePayment)) {
                $tabView[] = $this->_typePayment[$product->productCode];
            }
        }
        return array_unique($tabView);
    }
    
     public function hookPayment($params) {
        if (!$this->active) {
            return;
        }

        #var_dump($params['cart']->id);
        $cart = new CartCore($params['cart']->id);
        
        #var_dump($cart->getOrderTotal());
        global $smarty;
        
        $productView = '';
        $tabView = '';
        
        $productList = $this->_getListProduct();
        if ($productList->error_code === '0000'){
            $productView = $this->_getProductView($productList);
            $tabView = $this->_listTabView($productList);
        }
       
        
        $smarty->assign(array(
            'error_code' => $productList->error_code,
            'i' => 0,
            'ii' => 0,
            'list_product' => $productView,
            'fees' => $this->getConfigFieldsValues(),
            'list_tab' => $tabView,
            'this_path' => $this->_path, //keep for retro compat
            'this_path_espay' => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
        ));
        return $this->display(__FILE__, 'payment.tpl');
    }

    public function hookOrderConfirmation($params) {

        if (!$this->active)
            return;

        $status = 'gagal';
        $order_id = Tools::getValue('id_order');
        $order = new OrderCore($order_id);
        $order_state = $order->current_state;
        if ($order_state === ConfigurationCore::get('ESPAY_SUCCESS_STATUS')) {
            $status = 'sukses';
        } else {
            if ($order->payment === 'BIIATM' || $order->payment === 'PERMATAATM' || $order->payment === 'FINPAY195') {
                $status = 'waiting';
            }else{
                 $order_history = new OrderHistoryCore();
                 $order_history->id_order = $order_id;
                 
                 $order_history->changeIdOrderState(ConfigurationCore::get('ESPAY_FAILED_STATUS'), $order_id);
                 $order_history->add();
                   
            }
        }



        $this->context->smarty->assign(array(
            'status' => $status,
            'cart' => $this->context->cart,
            'this_path' => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
        ));
        if (version_compare(Configuration::get('PS_VERSION_DB'), '1.5') == -1) {
            return $this->display(__FILE__, 'views/templates/hook/order_confirmation.tpl');
        } else {
            return $this->display(__FILE__, 'order_confirmation.tpl');
        }
    }

    private function _createOrderState($db_name, $name) {

        if (!Configuration::get($db_name)) {//if status does not exist
            $orderState = new OrderState();
            $orderState->name = array_fill(0, 10, $name);
            $orderState->color = '#4169E1';
            $orderState->send_email = false;
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = false;
            $orderState->invoice = false;
            if ($orderState->add()) {//save new order status
                Configuration::updateValue($db_name, (int) $orderState->id);
            }
        }
    }
      private function _deleteOrderState($db_name) {
        $id_order_state = ConfigurationCore::get($db_name);
        ConfigurationCore::deleteByName($db_name);
        $orderState = new OrderState($id_order_state);
        $orderState->delete();
    }

    public function createOrder($id_cart, $total, $product_code, $fee) {

        $id_order_state = ConfigurationCore::get('ESPAY_WAITING_STATUS');
        $message = '';
        $extra_vars = array();
        $currency_special = null;
        $dont_touch_amount = false;

        $cart = new CartCore($id_cart);
        $customer = new CustomerCore($cart->id_customer);

        $this->validateOrder($id_cart, $id_order_state, $total, $product_code, $message, $extra_vars, $currency_special, $dont_touch_amount, $customer->secure_key);

        $order = new Order((int) $this->currentOrder);
        #$fee = floatval(ConfigurationCore::get('ESPAY_' . $product_code . '_FEE'));
        $order->total_paid += $fee;
        $order->total_paid_tax_excl += $fee;
        $order->total_paid_tax_incl += $fee;
        $order->total_products += $fee;
        $order->total_products_wt += $fee;


        $order->update();

        return $order->id;
    }
 public function addTransactionId($id_order, $id_transaction, $product_code) {
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $new_order = new OrderCore((int) $id_order);
            if (Validate::isLoadedObject($new_order)) {
                $payment = $new_order->getOrderPaymentCollection();
                if (isset($payment[0])) {
                    $payment[0]->transaction_id = pSQL($id_transaction);
                    $payment[0]->payment_method = pSQL('ESPay - ' . $product_code);
                    $payment[0]->save();
                }
            }
        }
    }
    
    public function genSignature($rqDatetime,$order_id,$mode){
      $key = ConfigurationCore::get('ESPAY_SIGNATURE_KEY');
      $data = "##".$key."##".$rqDatetime."##".$order_id."##".$mode."##";
      
      $upperCase = strtoupper($data);
      $signature = hash('sha256', $upperCase);
      return $signature;
    }

}