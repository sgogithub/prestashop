{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $enviroment eq '0'}
    <script type="text/javascript" src="http://secure-dev.sgo.co.id/public/signature/js"></script>
{elseif $enviroment eq '1'}
   <script type="text/javascript" src="https://secure.sgo.co.id/public/signature/js"></script>
{/if}

<div class="clear"><br></div>
<div class="payment_module">

	<form name="sgopayment_form" id="sgopayment_form">
		<div id="sgopaymentFrame">

			<label>{l s='Espay Payment Gateway' mod='espay'}</label>
			<p>
	<br>
	<div>
	<img src="../modules/espay/img/sgo.png" style="float:left; margin-right:15px;" >
	{l s='Please select the way that you want to pay.' mod='espay'}<br>
	{l s='Transfer bank account information will be displayed on the next page.' mod='espay'}

	<div>
	</br></br>
	<div class="bs-example">


    <ul class="nav nav-tabs">
    	{$list_tab}

    </ul>
    <div class="tab-content">
   		{$list_product}
   	</div>
</div>
	<p>

	<input type="hidden" value="{$sgo_payment_id}" name="sgopaymentid" id="sgopaymentid">
	<input type="hidden" value="{$payment_id}" name="cartid" id="cartid">
	<input type="hidden" value="{$payment_amount}" name="paymentamount" id="paymentamount">
	<input type="hidden" value="{$back_url}" name="back_url" id="back_url">
	<input type="hidden" value="{$currency_id}" name="currency" id="currency">
	</p>
	<br /><br />

</p><br/>


			<div class="clear"></div>

			<div class="clear"></div>
      	<b>{l s='Please Click \' Pay Now \' to Pay by Espay Payment Gateway' mod='espay'}.</b>
      </br>
			<input type="button" id="sgopayment_submit" onclick="submitdata('{$this_path_ssl}save.php')" value="{l s='Pay Now' mod='sgopayment'}" class="button" />

			<div class="clear"></div>
		</div>
	</form>
</div>
<div class="clear"></div>
<script type="text/javascript" src="{$this_path}payment.js"></script>

<br><br>
<iframe id="sgoplus-iframe" style="display:none" src="" scrolling="no" frameborder="0"></iframe>
