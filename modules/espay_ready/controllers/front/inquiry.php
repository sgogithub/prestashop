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
class EspayInquiryModuleFrontController extends ModuleFrontController {

    protected $display_header = false;
    protected $display_footer = false;

    public function initContent() {
        parent::initContent();
        $this->display_header = false;
        $this->display_footer = false;
    }

    public function postProcess() {
        $password = Tools::getValue('password');
        $rqDatetime = Tools::getValue('rq_datetime');
        $order_id = Tools::getValue('order_id');
        $webSignature = Tools::getValue('signature');
        $mode = 'inquiry';
        $selfSignature = $this->module->genSignature($rqDatetime,$order_id, $mode );
        if ($selfSignature === $webSignature) {

            if ($password === ConfigurationCore::get('ESPAY_PAYMENT_PASS')) {
                $order = new OrderCore($order_id);


                $total_fee = $order->total_paid;

                if ($total_fee !== 0) {
                    $payment_method = $order->payment;
                    $fee = ConfigurationCore::get('ESPAY_' . $payment_method . '_FEE');
                    $total = $total_fee - floatval($fee);


                    echo '0;Success;' . $order_id . ';' . $total . ';IDR; ' . $this->module->l('Payment Order') . ' ' . $order_id . ';' . date('Y/m/d H:i:s');
                } else {
                    echo '1;' . $this->module->l('Order Id Does Not Exist') . ';;;;;';
                }
            } else {
                echo '1;' . $this->module->l('Error') . ';;;;;';
            }
        }else {
             echo '1;' . $this->module->l('Invalid Signatue') . ';;;;;';
        }
    }

}
