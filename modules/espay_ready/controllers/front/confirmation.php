<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of confirmation
 *
 * @author fredy
 */
class EspayConfirmationModuleFrontController extends ModuleFrontController
{
	public $ssl = true;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
                
                $order_id = Tools::getValue('order_id');
                $order = new OrderCore($order_id);
                $cart_id = $order->id_cart;
                
                $customer = new Customer((int)$order->id_customer);
                $espay = new Espay();

		Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.$cart_id.'&id_module='.$espay->id.'&id_order='.Tools::getValue('order_id').'&key='.$customer->secure_key);
	}

}

