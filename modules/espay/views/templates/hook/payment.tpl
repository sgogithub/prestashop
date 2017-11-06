{*
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
*}
<p class="payment_module">
    <a class="bankwire" data-toggle="collapse" data-target="#espaytab" title="{l s='Pay by' mod='espay'} {$fees.ESPAY_PAYMENT_TITLE} ">
        <img src="{$this_path_espay}logo_big.png" alt="{l s='Pay by' mod='espay'} {$fees.ESPAY_PAYMENT_TITLE} " width="85" height="35" />
        {l s='Pay by' mod='espay'} {$fees.ESPAY_PAYMENT_TITLE} {l s='(order will be processing directly)' mod='espay'}
    </a>
</p>
<div id="espaytab" class="collapse">
    {if $error_code == '0000'}

        <form action="{$link->getModuleLink('espay', 'payment', [], true)}" method='post'>
            <ul class="nav nav-tabs">
                {foreach from=$list_tab item=tabname}
                    <li><a data-toggle="tab" {if $i == 0 }class="active"{/if}  href="#espaysection{$i}">{$tabname}</a></li>

                    {$i = $i+1} 
                {/foreach}
            </ul>
            <div class="tab-content">
                {foreach from=$list_tab item=tabname}
                    <div id="espaysection{$ii}" class="tab-pane fade in {if $ii == 0 }active{/if}" >
                        <h3>{$tabname}</h3>
                        {foreach from=$list_product.$tabname item=product}
                            <div class="col-sm-3 col-md-3 col-lg-3">
                                <label class="thumbnail">
                                    <input type="radio" name="espayproduct" id="espayproduct" value=" {$product.bank}: {$product.product}">
                                    <img src="https://kit.espay.id/images/products/{$product.product}.png" >	
                                    <ul>

                                        {assign var="productfee" value="ESPAY_`$product.product`_FEE"}
                                            {if $fees.ESPAY_ENABLE_FEE == 1}
                                                {if $product.product == 'CREDITCARD'}
                                                    <li class="text-center"> {l s='MDR Fee' mod='espay'}: {$fees.ESPAY_CREDITCARD_MDR}</li>
                                                {/if}
                                                <li class="text-center"> {l s='Transaction Fee' mod='espay'}: Rp. {$fees.$productfee}</li>
                                            {/if}
                                        <li class="text-center">{l s='Automatic Verification' mod='espay'}</li>
                                    </ul>
                                </label>
                            </div>

                        {/foreach}
                    </div> 
                    {$ii = $ii+1}
                {/foreach}
            </div>
            <br style="clear:both;" />
            <br style="clear:both;" />
            <p class="cart_navigation clearfix" id="cart_navigation">
                <input type="submit" value="{l s='Continue' mod='espay'}" class="exclusive_large" />
            </p>
        </form>

    {else}
        Error
    {/if}
</div>