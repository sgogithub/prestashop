{*
/*
* 2007-2013 PrestaShop
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
*  @author PT Square Gate One <business@sgo.co.id>
*  @copyright  2014 Square Gate One
*  International Registered Trademark & Property of PT Square Gate One
*}

<!--<script type="text/javascript" src="http://secure-dev.sgo.co.id/public/signature/js"></script>-->
<script type="text/javascript" src="{$this_path}payment.js"></script>

<p class="payment_module">
	<form name="sgopayment" id="sgopayment"  action="" method="post">
	<div align="center">
	<input type="radio" name="product" id="product" value="008:MANDIRIIB">  <img src="{$this_path}img/mandiri.jpg">
	<input type="radio" name="product" id="product" value="016:BIIATM"> <img src="{$this_path}img/atm-gabungan.gif">
	</div>
	<input type="hidden" value="{$sgo_payment_id}" name="sgopaymentid" id="sgopaymentid">
	<input type="hidden" value="{$payment_id}" name="cartid" id="cartid">
	<input type="hidden" value="{$payment_amount}" name="paymentamount" id="paymentamount">
	<input type="hidden" value="{$back_url}" name="back_url" id="back_url">

</p>

		<input  type="submit" name="submit" value="Submit" class="exclusive" />

</form>
</p>