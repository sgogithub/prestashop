<?php
/*
 * 2007-2013 PrestaShop NOTICE OF LICENSE This source file is subject to the Academic Free License (AFL 3.0) that is bundled with this package in the file LICENSE.txt. It is also available through the world-wide-web at this URL: http://opensource.org/licenses/afl-3.0.php If you did not receive a copy of the license and are unable to obtain it through the world-wide-web, please send an email to license@prestashop.com so we can send you a copy immediately. DISCLAIMER Do not edit or add to this file if you wish to upgrade PrestaShop to newer versions in the future. If you wish to customize PrestaShop for your needs please refer to http://www.prestashop.com for more information. @author PT Square Gate One <business@sgo.co.id> @copyright 2014 PT Square Gate One @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0) International Registered Trademark & Property of PT Square Gate One
 */
if (! defined ( '_PS_VERSION_' ))
	exit ();
class Espay extends PaymentModule {
	private $_html = '';
	private $_postErrors = array ();
	public $sgopayment_id;
	public $sgopayment_pass;
	public $sgopayment_ip;
	private $typePayment = array();
	// public $extra_mail_vars;
	public function __construct() {
		$this->name = 'espay';
		$this->tab = 'payments_gateways';
		$this->version = '0.1';
		$this->author = 'PT. Square Gate One';
		
		$this->typePayment = array(
				'PERMATAATM' 	=> "VA",
				'BIIATM'	 	=> "VA",
				'BCAKLIKPAY' 	=> "ONLINEPAYMENT",
				'DKIIB'		 	=> "ONLINEPAYMENT",
				"EPAYBRI"	 	=> "ONLINEPAYMENT",
				"MANDIRIIB"	 	=> "ONLINEPAYMENT",
				"PERMATANETPAY" => "ONLINEPAYMENT",
				"NOBUPAY"	 	=> "WALLET",
				"MANDIRIECASH"	=> "WALLET",
				"XLTUNAI"		=> "WALLET",
				"TCASH"			=> "WALLET",
				"MAYAPADAIB"	=> "ONLINEPAYMENT",
				"DANAMONOB"		=> "ONLINEPAYMENT"
		);
		// $this->sgopayment_ip = '116.90.162.170';
		$this->currencies = true;

		parent::__construct ();

		$config = Configuration::getMultiple ( array (
				'SGO_PAYMENT_ID',
				'SGO_PAYMENT_PASS',
				'SGO_PAYMENT_IP'
		) );
		if (isset ( $config ['SGO_PAYMENT_ID'] )) {
			$this->sgopayment_id = $config ['SGO_PAYMENT_ID'];
		}
		if (isset ( $config ['SGO_PAYMENT_PASS'] )) {
			$this->sgopayment_pass = $config ['SGO_PAYMENT_PASS'];
		}
		if (isset ( $config ['SGO_PAYMENT_PASS'] ) && $config ['SGO_PAYMENT_IP'] != '') {
			$this->sgopayment_ip = $config ['SGO_PAYMENT_IP'];
		} else {
			$this->sgopayment_ip = '116.90.162.170';
		}

		$this->displayName = $this->l ( 'Espay Payment Gateways' );
		$this->description = $this->l ( 'Accept payments for your products via Espay Payment Gateways.' );

		$this->confirmUninstall = $this->l ( 'Are you sure about removing these details?' );
		if (! isset ( $this->sgopayment_id ) || ! isset ( $this->sgopayment_pass )) {
			$this->warning = $this->l ( 'Sgo Payment ID and payment password  must be configured before using this module.' );
		}
		if (! count ( Currency::checkPaymentCurrencies ( $this->id ) )) {
			$this->warning = $this->l ( 'No currency has been set for this module.' );
		}

		// $this->currencies_mode = 'checkbox';
	}
	public function install() {
		if (! parent::install () || ! $this->registerHook ( 'payment' )) {
			return false;
		} else {
			$this->_createTable ();
			#$this->_insertNewOrderState ( 1 );
			#$this->_insertNewOrderState ( 2 );
			#$this->_insertNewOrderState ( 3 );
			#$this->_insertNewOrderState ( 4 );
			return TRUE;
		}
	}
	public function uninstall() {
		if (! Configuration::deleteByName ( 'SGO_PAYMENT_ID' ) or ! Configuration::deleteByName ( 'SGO_PAYMENT_PASS' ) or ! parent::uninstall ()) {
			return false;
		} else {
			#$this->_deleteOrderState ();
			$this->_delTable ();
			return TRUE;
		}
	}
	private function _postValidation() {
		if (Tools::isSubmit ( 'btnSubmit' )) {
			if (! Tools::getValue ( 'sgopaymentid' )) {
				$this->_postErrors [] = $this->l ( 'SGO payment id is required.' );
			}
			if (! Tools::getValue ( 'sgopaymentpass' )) {
				$this->_postErrors [] = $this->l ( 'SGO payment password is required.' );
			}
			if (! Tools::getValue ( 'sgopaymentip' )) {
				$this->_postErrors [] = $this->l ( 'SGO payment ip is required.' );
			}
		}
	}
	private function _postProcess() {
		if (Tools::isSubmit ( 'btnSubmit' )) {
			Configuration::updateValue ( 'SGO_PAYMENT_ID', Tools::getValue ( 'sgopaymentid' ) );
			Configuration::updateValue ( 'SGO_PAYMENT_PASS', Tools::getValue ( 'sgopaymentpass' ) );
			Configuration::updateValue ( 'SGO_PAYMENT_IP', Tools::getValue ( 'sgopaymentip' ) );
		}else if (Tools::isSubmit ( 'btnUpdate' )){
			$this->initiateProduct();
		}
		$this->_html .= '<div class="conf confirm"> ' . $this->l ( 'Settings updated' ) . '</div>';
	}
	private function _displaySgoPayment() {
		$this->_html .= '<img src="../modules/espay/img/sgo.png" style="float:left; margin-right:15px;" width="86" height="49"><b>' . $this->l ( 'This module allows you to accept secure payments by bank wire.' ) . '</b><br /><br />
		' . $this->l ( 'If the client chooses to pay by sgo payment gateways, the order\'s status will change to "Confirmation Order."' ) . '<br />
		';
	}
	private function _displayForm() {
		$this->_html .= '<form action="' . $_SERVER ['REQUEST_URI'] . '" method="post">
		<fieldset>
		<legend><img src="../img/admin/contact.gif" />' . $this->l ( 'Contact details' ) . '</legend>
		<table border="0" width="500" cellpadding="0" cellspacing="0" id="form">
		<tr><td colspan="2">' . $this->l ( 'Please specify the sgo payment id and password' ) . '.<br /><br /></td></tr>
		<tr><td width="130" style="height: 35px;">' . $this->l ( 'SGO Payment ID' ) . '</td><td><input type="text" name="sgopaymentid" value="' . htmlentities ( Tools::getValue ( 'owner', $this->sgopayment_id ), ENT_COMPAT, 'UTF-8' ) . '" style="width: 300px;" /></td></tr>
		<tr><td width="130" style="height: 35px;">' . $this->l ( 'SGO Payment Ip Address' ) . '</td><td><input type="text" name="sgopaymentip" value="' . htmlentities ( Tools::getValue ( 'owner', $this->sgopayment_ip ), ENT_COMPAT, 'UTF-8' ) . '" style="width: 300px;" /></td></tr>
		<tr><td width="130" style="height: 35px;">' . $this->l ( 'SGO Payment Password' ) . '</td><td><input type="password" name="sgopaymentpass" value="' . htmlentities ( Tools::getValue ( 'sgopaymentpass', $this->sgopayment_pass ), ENT_COMPAT, 'UTF-8' ) . '" style="width: 300px;" /></td></tr>
		'.$this->showEspayProduct().'
		<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="' . $this->l ( 'Update settings' ) . '" type="submit" />&nbsp;<input class="button" name="btnUpdate" value="' . $this->l ( 'Update Product' ) . '" type="submit" /></td></tr>
		</table>
		</fieldset>
		</form><br><br>';



	}
	public function getContent() {
		$this->_html = '<h2>' . $this->displayName . '</h2>';

		if (Tools::isSubmit ( 'btnSubmit' ) || Tools::isSubmit ( 'btnUpdate' )) {
			$this->_postValidation ();
			if (! count ( $this->_postErrors ))
				$this->_postProcess ();
			else
				foreach ( $this->_postErrors as $err )
					$this->_html .= '<div class="alert error">' . $err . '</div>';
		} else
			$this->_html .= '<br />';

		$this->_displaySgoPayment ();
		$this->_displayForm ();

		return $this->_html;
	}
	public function hookPayment($params) {

		// print_r($params);
		if (! $this->active)
			return;
		if (! $this->checkCurrency ( $params ['cart'] ))
			return;

		$currency = new Currency ( ( int ) $params ['cart']->id_currency );

		$this->smarty->assign ( array (
				'currency_id' => ( int ) $params ['cart']->id_currency,
				'this_path' => $this->_path,
				'firstdata_ps_version',
				_PS_VERSION_,
				'this_path_bw' => $this->_path,
				'this_path_ssl' => Tools::getShopDomainSsl ( true, true ) . __PS_BASE_URI__ . 'modules/' . $this->name . '/',
				'sgo_payment_id' => $this->sgopayment_id,
				'this_path' => $this->_path,
				'list_product_va' => $this->listproduct("VA"),
				'list_product_wallet' => $this->listproduct('WALLET'),
				'list_product_onlinepayment' => $this->listproduct('ONLINEPAYMENT'),
				'payment_id' => $params ['cart']->id,
				'payment_amount' => number_format ( $this->context->cart->getOrderTotal ( true ), 2, '.', '' ),
				'back_url' => (Configuration::get ( 'PS_SSL_ENABLED' ) ? 'https://' : 'http://') . htmlspecialchars ( $_SERVER ['HTTP_HOST'], ENT_COMPAT, 'UTF-8' ) . __PS_BASE_URI__ . 'modules/' . $this->name . '/validation.php?cartid=' . $params ['cart']->id . '&securekey=' . $params ['cart']->secure_key . ''
		)
		 );

		return $this->display ( __FILE__, 'payment.tpl' );
	}
	public function checkCurrency($cart) {
		$currency_order = new Currency ( $cart->id_currency );
		$currencies_module = $this->getCurrency ( $cart->id_currency );

		if (is_array ( $currencies_module ))
			foreach ( $currencies_module as $currency_module )
				if ($currency_order->id == $currency_module ['id_currency'])
					return true;
		return false;
	}
	private function _savePayment($cart_id, $total, $currency, $ket, $product) {
		$db = Db::getInstance ();
		$sql_check = "select * from `" . _DB_PREFIX_ . "sgopayment` where `cart_id` = '" . $cart_id . "'";
		$result = $db->ExecuteS ( $sql_check );
		$date = date ( "d/m/Y H:i:s" );

		if ($db->NumRows ( $result ) == 0) {
			$sql = "INSERT INTO `" . _DB_PREFIX_ . "sgopayment` (`cart_id`,`total`,`currency`,`ket`, `date`, `product`) VALUES (" . $cart_id . ", '" . $total . "', '" . $currency . "', '" . $ket . "', '" . $date . "', '".$product."')";
		} else {
			$sql = "UPDATE `" . _DB_PREFIX_ . "sgopayment`
			SET `total`='" . $total . "', `currency`='" . $currency . "', `ket` = '" . $ket . "', `date` = '" . $date . "', `product` = '" . $product. "'
			WHERE `cart_id`=" . $cart_id . "";
		}
		$db->ExecuteS ( $sql );
	}
	public function savePayment() {
		$cart_id = $_POST ["cartid"];
		$total = $_POST ["paymentamount"];
		$currency_id = $_POST ["currency"];
		$ket = "pembelian cart " . $cart_id;
		$product = $_POST ["product"];

		$length = strlen($product);
		$pos = strpos($product, ':');

		$product = substr($product, $pos+1);

		$currency = new Currency ( $currency_id );
		$currencyIsoCode = $currency->iso_code;
		$this->_savePayment ( $cart_id, $total, $currencyIsoCode, $ket, $product );
	}
	private function _addHistory($orderid, $orderState) {
		$db = Db::getInstance ();
		$sql = "INSERT INTO `" . _DB_PREFIX_ . "order_history` (`id_employee`,`id_order`,`id_order_state`, `date_add`) VALUES (0," . $orderid . "," . $orderState . ", '" . date ( "Y-m-d H:i:s" ) . "')";

		$db->ExecuteS ( $sql );
	}

	/**
	 * these below function is used for sending rest function
	 * when sgo payment gateway send the notification
	 * that payment has been accepted
	 */
	public function notification() {
		$db = Db::getInstance ();

		$uri = $_SERVER ['REQUEST_URI'];
		$method = $_SERVER ['REQUEST_METHOD'];

		$id_cart = $_POST ['order_id'];
		// if($_SERVER['REMOTE_ADDR'] == $this->sgopayment_ip ){
		if ($method == "POST") {
			$id_cart = urldecode ( $_POST ['order_id'] );
			$password = urldecode ( $_POST ['password'] );
			$bankCode = urlencode ( $_POST ['debit_from_bank'] );

			$sql = 'select * from `' . _DB_PREFIX_ . 'sgopayment` where cart_id = ' . $id_cart . '';
			$result = $db->getRow ( $sql );

			$total = $result ["total"];
			$currency = $result ["currency"];
			$ket = $result ["ket"];
			$date = $result ['date'];
			$status = $result ['status'];

			if ($db->NumRows ( $result ) > 0) {
				header ( 'HTTP/1.1 200 OK' );
				if ($this->sgopayment_pass == $password) {

					if ($status == 'waiting') {
						$order_id = Order::getOrderByCartId ( $id_cart );
						$orderObject = new Order ( $order_id );

						$orderHistory = new OrderHistory ();
						$orderHistory->id_order = ( int ) $orderObject->id;
						$orderHistory->changeIdOrderState ( $this->getOrderState ( $id_cart ), ( int ) $orderObject->id, true );
						$orderHistory->add ();

						$reference = $orderObject->reference;
						$this->_updateStatuspaymen ( 'success', $id_cart );
						echo '0,Success,' . $reference . ',' . $id_cart . ',' . date ( 'Y-m-d H:i:s' ) . '';
					} else {
						$orderStatus = $this->validateOrder ( $id_cart, $this->getOrderState ( $id_cart ), $total, $this->displayName, NULL, $this->_getCurrencyId ( $currency ) );
						if ($orderStatus) {
							$reference = $this->currentOrderReference;
							$this->_updateStatuspaymen ( 'success', $id_cart );
							echo '0,Success,' . $reference . ',' . $id_cart . ',' . date ( 'Y-m-d H:i:s' ) . '';
						} else {
							echo '1,Cart does not exist,,' . $id_cart . ',' . date ( 'Y-m-d H:i:s' ) . '';
						}
					}
				} else {
					echo '1,Password does not match,,' . $id_cart . ',' . date ( 'Y-m-d H:i:s' ) . '';
				}
			} else {
				header ( 'HTTP/1.1 200 OK' );
				echo '1,Cart does not exist,,' . $id_cart . ',' . date ( 'Y-m-d H:i:s' ) . '';
			}
		} else {
			header ( 'HTTP/1.1 404 Not Found' );
		}
		// }else{
		// header('HTTP/1.1 404 Not Found');
		// }
	}

	/**
	 * these below function is used for sending rest function
	 * when sgo payment gateway to check the inquiry
	 */
	public function serve() {
		$db = Db::getInstance ();

		$uri = $_SERVER ['REQUEST_URI'];
		$method = $_SERVER ['REQUEST_METHOD'];
		$id_cart = $_POST ['order_id'];
		$test = $_SERVER ['REMOTE_ADDR'];

		// if($_SERVER['REMOTE_ADDR'] == $this->sgopayment_ip){
		if ($method == "POST") {
			$id_cart = urldecode ( $_POST ['order_id'] );
			$password = urldecode ( $_POST ['password'] );

			$sql = 'select * from `' . _DB_PREFIX_ . 'sgopayment` where cart_id = ' . $id_cart . '';
			$result = $db->getRow ( $sql );

			$total = $result ["total"];
			$currency = $result ["currency"];
			$ket = $result ["ket"];
			$date = $result ['date'];

			if ($db->NumRows ( $result ) > 0) {
				header ( 'HTTP/1.1 200 OK' );

				if ($this->sgopayment_pass == $password) {

					echo '0;Success;' . $id_cart . ';' . $total . ';' . $currency . ';' . $ket . ';' . $date . '';
				} else {
					echo '1;Password does not match;' . $id_cart . ';' . $total . ';' . $currency . ';' . $ket . ';' . $date . '';
				}
			} else {

				header ( 'HTTP/1.1 200 OK' );
				echo '1;cart does not exist ' . $id_cart . ';' . $total . ';' . $currency . ';' . $ket . ';' . $date . '';
			}
		} else {
			header ( 'HTTP/1.1 404 Not Found' );
		}
		// }else{
		// header('HTTP/1.1 404 Not Found');
		// }
	}
	private function _updateStatuspaymen($status, $cartid) {
		$sql = "UPDATE `" . _DB_PREFIX_ . "sgopayment`
			SET `status`='" . $status . "'
			WHERE `cart_id`=" . $cartid . "";
		$db = Db::getInstance ();
		$db->execute ( $sql );
	}

	private function _createTable() {


		$db = Db::getInstance ();
		$sql = "CREATE TABLE `" . _DB_PREFIX_ . "sgopayment` (
		`id`  int(255) NOT NULL AUTO_INCREMENT,
		`cart_id`  int(255) NOT NULL,
		`total`  varchar(255) NOT NULL,
		`currency` VARCHAR(255) NOT NULL,
		`ket` VARCHAR(255) NOT NULL,
		`date` VARCHAR(255) NOT NULL,
		`product` VARCHAR(255) NOT NULL,
		`status` VARCHAR(255) NOT NULL DEFAULT '',
		PRIMARY KEY (`id`)
		)";

		$sqllog = "CREATE TABLE `"._DB_PREFIX_."sgopayment_log` (
		`uuid`  varchar(255) NOT NULL,
		`datetime` varchar(255) NOT NULL,
		`service`  varchar(255) NOT NULL,
		`request` TEXT NOT NULL,
		`respond` TEXT NOT NULL,
		PRIMARY KEY (`uuid`)
		)";

		$sqlproduct = "CREATE TABLE `"._DB_PREFIX_."sgopayment_product` (
		`id`  int(10) NOT NULL AUTO_INCREMENT,
		`bankcode` varchar(255) NOT NULL,
		`productcode`  varchar(255) NOT NULL,
		`productname`  varchar(255) NOT NULL,
		PRIMARY KEY (`id`)
		)";

		$result = $db->ExecuteS($sql);
		if(!$result){
			return FALSE;
		}else{
			$result2 = $db->ExecuteS($sqllog);
			if (!$result2){
				return FALSE;
			}else{
				$db->ExecuteS($sqlproduct);
				return TRUE;
			}

		}
	}
	private function _delTable() {
		$db = Db::getInstance ();
		$sql = "DROP TABLE `" . _DB_PREFIX_ . "sgopayment`";
		$db->ExecuteS ( $sql );
		$sqllog = "DROP TABLE `" . _DB_PREFIX_ . "sgopayment_log`";
		$db->ExecuteS ( $sqllog );
		$sqlproduct = "DROP TABLE `" . _DB_PREFIX_ . "sgopayment_product`";
		$db->ExecuteS ( $sqlproduct );
	}




	private function _deleteOrderState() {
		$config = Configuration::getMultiple ( array (
				'SGO_PAYMENT_MANDIRI',
				'SGO_PAYMENT_BII',
				'SGO_PAYMENT_NOBU',
				'WAITING_SGO_PAYMENT_BII'
		) );
		$mandiriOrderState = $config ["SGO_PAYMENT_MANDIRI"];
		$biiOrderState = $config ["SGO_PAYMENT_BII"];
		$nobuOrderState = $config ["SGO_PAYMENT_NOBU"];

		Configuration::deleteByName ( 'SGO_PAYMENT_MANDIRI' );
		Configuration::deleteByName ( 'SGO_PAYMENT_BII' );
		Configuration::deleteByName ( 'SGO_PAYMENT_NOBU' );
		Configuration::deleteByName ( 'WAITING_SGO_PAYMENT_BII' );
	}

	public function confirmation() {

		$cart_id = $_GET ["cartid"];
		$key = $_GET ["securekey"];
		$product = $_GET["product"];
		$id_order = Order::getOrderByCartId ( ( int ) ($cart_id) );

		if ($this->_getPaymentStatus ( $cart_id ) != '') {
			Tools::redirect ( 'index.php?controller=order-confirmation&id_cart=' . $cart_id . '&id_module=' . $this->id . '&id_order=' . $id_order . '&key=' . $key );
		} else {

			$db = Db::getInstance ();
			$sql = 'select * from `' . _DB_PREFIX_ . 'sgopayment` where cart_id = ' . $cart_id . '';
			$result = $db->getRow ( $sql );

			$total = $result ["total"];
			$currency = $result ["currency"];
			$ket = $result ["ket"];
			$date = $result ['date'];

			// add not succesfull payment or waiting payment
			if ($product == 'BIIATM' || $product == 'PERMATAATM'){
				$orderState = Configuration::get('SGO_PAYMENT_WAITING_'.$product);
				$this->_updateStatuspaymen ('waiting', $cart_id );
				$this->validateOrder ( $cart_id, $orderState, $total, $this->displayName, NULL, $this->_getCurrencyId ( $currency ) );

				Tools::redirect ( 'index.php?controller=order-confirmation&id_cart=' . $cart_id . '&id_module=' . $this->id . '&id_order=' . $id_order . '&key=' . $key );
			}else{
				Tools::redirect ( 'index.php?controller=order-confirmation&id_cart=' . $cart_id . '&id_module=' . $this->id . '&id_order=' . $id_order . '&key=' . $key );

			}
		}
	}

	private function _getCurrencyId($currencyCode) {
		$db = Db::getInstance ();
		$sql_check = "select * from `" . _DB_PREFIX_ . "currency` where `iso_code` = " . $currencyCode . "";
		$result = $db->getRow ( $sql_check );

		return $result ['id_currency'];
	}

	private function _getPaymentStatus($cart_id) {
		$db = Db::getInstance ();
		$sql_check = "select * from `" . _DB_PREFIX_ . "sgopayment` where `cart_id` = " . $cart_id . "";
		$result = $db->getRow ( $sql_check );

		return $result ['status'];
	}

	public function initiateProduct() {
		include (dirname ( __FILE__ ) . '/curl.php');
		$db = Db::getInstance ();
		$products = '';
		$curl = new CurlCall ();
		$requestMerchant = new stdClass ();
		$requestProduct = new stdClass ();
		$sqlDelete = 'TRUNCATE TABLE `' . _DB_PREFIX_ . 'sgopayment_product`';
		@$db->ExecuteS ( $sqlDelete );

		$urlMerchant = 'https://116.90.162.172:812/rest/merchant/merchantinfo';
		$requestMerchant->key = $this->sgopayment_id;
		$responseMerchant = $curl->Call ( $urlMerchant, $requestMerchant );
		$responseMerchant = json_decode ( $responseMerchant );

		#var_dump($responseMerchant);
		foreach ( $responseMerchant->data as $productValue ) {
			$sqlInsertProduct = "INSERT INTO `" . _DB_PREFIX_ . "sgopayment_product`  (`bankcode`,`productcode`,`productname`) values ('" . $productValue->bankCode . "','" . $productValue->productCode . "','" . $productValue->productName . "') ";
			#var_dump($sqlInsertProduct);
			$db->ExecuteS ( $sqlInsertProduct );
			$this->_insertOrderStateProduct ( $productValue->productCode, $productValue->productName );
		}
	}

	private function _insertOrderStateProduct($product, $productname){
		$db = Db::getInstance();

				if ($product == 'PERMATAATM' || $product == 'BIIATM'){
					$sqlcheck = "SELECT * FROM `"._DB_PREFIX_."order_state_lang` where `name` ='Waiting Payment Via ".$productname."' LIMIT 1";

					$result = @$db->ExecuteS($sqlcheck);
					$jumlah =  @$db->NumRows($result);
					if ($jumlah  == 0 ){

						$sqlpaymentnew = "INSERT INTO `"._DB_PREFIX_."order_state` (`invoice`,`color`, `unremovable`, `module_name`) VALUES (1,'#DDEEFF', 0, 'espay')";
						@$db->ExecuteS($sqlpaymentnew);
						$lastID = $db->Insert_ID();

						Configuration::updateValue('SGO_PAYMENT_WAITING_'.$product,$lastID);
						$sqlpaymentLang = "INSERT INTO `"._DB_PREFIX_."order_state_lang` (`id_order_state`,`id_lang`,`name`) VALUES (".$lastID.",1,'Waiting Payment Via ".$productname."')";
						@$db->ExecuteS($sqlpaymentLang);
					}
				}
				$sqlcheck = "SELECT * FROM `"._DB_PREFIX_."order_state_lang` where `name` = 'Payment Accepted Via ".$productname."' LIMIT 1";

				$result = @$db->ExecuteS($sqlcheck);
				$jumlah =  @$db->NumRows($result);
				if ($jumlah  == 0 ){


					$sqlpayment = "INSERT INTO `"._DB_PREFIX_."order_state` (`invoice`,`color`, `unremovable`) VALUES (1,'#DDEEFF', 0)";
					@$db->ExecuteS($sqlpayment);
					$lastID = $db->Insert_ID();


					Configuration::updateValue('SGO_PAYMENT_'.$product,$lastID);
					$sqlpaymentLang = "INSERT INTO `"._DB_PREFIX_."order_state_lang` (`id_order_state`,`id_lang`,`name`) VALUES (".$lastID.",1,'Payment Accepted Via ".$productname."')";
					@$db->ExecuteS($sqlpaymentLang);
				}


	}


	private function showEspayProduct(){
		$db = Db::getInstance();
		$sqlcheck = "SELECT * FROM `"._DB_PREFIX_."sgopayment_product`";
		$result = @$db->ExecuteS($sqlcheck);
		$jumlah =  @$db->NumRows($result);
		$list = '';

		if ($jumlah != 0){

			foreach ($result as $row){
				$list .= '<tr><td>'.$row['productname'].'</td><td><input checked disabled type="checkbox"></td></tr>';
			}

		}

		return $list;
	}

	private function listproduct($paymentType){
		$db = Db::getInstance ();
		$sqlcheck = "SELECT * FROM `"._DB_PREFIX_."sgopayment_product`";
		$result = @$db->ExecuteS($sqlcheck);
		$jumlah =  @$db->NumRows($result);
		$list = '';

		if ($jumlah != 0){
			
			foreach ($result as $row){
				if ($this->typePayment[$row['productcode']] == $paymentType){
					$list .= '<p><input type="radio" name="product" id="product" value="'.$row['bankcode'].':'.$row['productcode'].'"> '.$row['productname'].'</p>';
				}
				#$list .= '<input type="radio" name="product" id="product" value="'.$row['bankcode'].':'.$row['productcode'].'"> <img src="'.$this->_path.'img/'.$row['productcode'].'.png"><br>';
				
			}

		}

		return $list;

	}

	private function getOrderState($orderid){
		$db = Db::getInstance();
		$sqlcheck = "SELECT * FROM `"._DB_PREFIX_."sgopayment` where cart_id= '" . $orderid ."'";
		$result = $db->getRow ( $sqlcheck );
		$arrayindex = 'SGO_PAYMENT_'.$result['product'];

		$config = Configuration::get($arrayindex);
		return $config;

	}


}
