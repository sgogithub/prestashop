{capture name=path}{l s='ESPay Payment' mod='espay'}{/capture}

<h2 class="page-heading">{l s='Order Summary' mod='espay'}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="box cheque-box">
    {if $nbProducts <= 0}
        <p class="warning">{l s='Your shopping cart is empty.' mod='espay'}</p>
    {elseif $error_message != ""}
        <p class="warning">{$error_message}</p>
    {else}
        <h3 class="page-subheading">{l s='Payment via ESPay.' mod='espay'}</h3>
        {* <form action="{$url}" method="post" name="payment_form" class="std"> *}
        {if (version_compare(Configuration::get('PS_VERSION_DB'), '1.5') == -1)}
            <form action="{$link->getModuleLink('espay', 'validation', [], true)}" method="post" class="std"> 
            {else}
                <form action="{$link->getModuleLink('espay', 'validation', [], true)}" method="post" class="std"> 
                {/if}
                <p>
                    <img src="{$this_path}logo_big.png" alt="{l s='ESPay' mod='espay'}" height="49" style="float:left; margin: 0px 10px 5px 0px;" />
                    <br/><b>{l s='You have chosen to pay via ESPay.' mod='espay'}</b><br/>
                </p>

                <p style="margin-top:20px;">
                    - {l s='The total amount of your order is' mod='espay'}
                    <span id="amount" class="price"><b>{displayPrice price=$total}</b></span>
                    {if $use_taxes == 1}
                        {l s='(tax incl.)' mod='espay'}
                    {/if}  
                </p>
                
                    {if $config.ESPAY_ENABLE_FEE == '1'}
                        <p>
                    - {l s='Transaction Fee' mod='espay'} 
                         <span id="amount" class="price"><b>{displayPrice price=$fee}</b></span>
                         </p>
                     <p>
                    - {l s='Total Transaction that have to be paid' mod='espay'} 
                         <span id="amount" class="price"><b>{displayPrice price=$total_trx}</b></span>
                         </p>
                    {/if}
                
                
              

                
                <h3 class="page-subheading">Confirm Order</h3>

                <p class="cart_navigation clearfix" id="cart_navigation">
                    <input type="hidden" value="{$product_code}" name="productcode" id="productcode">
                    <input type="hidden" value="{$total_trx}" name="totaltrx" id="totaltrx">
                    <input type="hidden" value="{$bank_code}" name="bankcode" id="bankcode">
                     <input type="hidden" value="{$fee}" name="fee" id="fee">
                   
                    <input type="submit" value="{l s='Place my order' mod='espay'}" class="exclusive_large" />
                    <a href="{$link->getPageLink('order', true, NULL, "step=3")}" class="button_large">{l s='Other payment methods' mod='espay'}
                    </a>
                </p>
            </form>
        {/if}
</div>