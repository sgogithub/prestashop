<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of inquiry
 *
 * @author fredy
 */
class EspayPaymentreportModuleFrontController extends ModuleFrontController {

    protected $display_header = false;
    protected $display_footer = false;

    public function initContent() {
        parent::initContent();
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function postProcess() {
        $password = Tools::getValue('password');
        
        if ($password === ConfigurationCore::get('ESPAY_PAYMENT_PASS')) {
            $order_id = Tools::getValue('order_id');
            
            $order_history = new OrderHistoryCore();
            $order_history->id_order = $order_id;
                
            
            $order = new OrderCore($order_id);
            $total = $order->total_paid;
            $payment_method = $order->payment;
                    
            
            
            if ($payment_method !== NULL) {
                $trx_id = Tools::getValue('payment_ref');
                
                try{
                    $order_history->changeIdOrderState(ConfigurationCore::get('ESPAY_SUCCESS_STATUS'), $order_id);
                    $order_history->add();
                    $this->module->addTransactionId($order_id,$trx_id,$payment_method);
                  
                } catch (Exception $ex) {
                    var_dump($ex);
                }
                
                
               #echo  '0,Success,'.$order->reference.','.$order_id.','.  date('Y-m-d H:i:s');
            }else {
               echo  '1,Failed,,,';
            }
        }else {
             header('',true,'404');
        }
    }

}
