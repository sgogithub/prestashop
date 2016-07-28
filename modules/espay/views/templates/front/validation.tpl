{capture name=path}{l s='ESPay Payment' mod='espay'}{/capture}

<h2 class="page-heading">{l s='Order Summary' mod='espay'}</h2>

{assign var='current_step' value='validation'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="box cheque-box">
   
        <h3 class="page-subheading">{l s='Payment via ESPay.' mod='espay'}</h3>
        <p>
            <img src="{$this_path}logo_big.png" alt="{l s='ESPay' mod='espay'}" height="49" style="float:left; margin: 0px 10px 5px 0px;" />
            <br/><b>{l s='You have chosen to pay via ESPay.' mod='espay'}</b><br/>
        </p>

        <p style="margin-top:20px;">
            {l s='We are Redirecting You to The Payment Page'}
        </p>
        <p style="margin-top:20px;">
            {l s='Your Order Id is'} {$order_id}
        </p>
        <p style="margin-top:20px;">
            {l s='Please Do Not'} <b>{l s='Close'}</b>, <b>{l s='Refresh'}</b>,{l s='or click'} <b>{l s='Back'} </b> {l s='on this page'}.
        </p>
        
        <script type="text/javascript" src="{$url_js}/public/signature/js"></script>
        <script type="text/javascript">
        
        $( document ).ready(function() {
          var data = {
                   paymentId: '{$order_id}',
                   key: '{$key}',
                   backUrl: encodeURIComponent('{$link->getModuleLink('espay', 'confirmation', ['order_id'=>$order_id], true)}'),
                   bankCode:'{$bank_code}',
                   bankProduct :'{$product_code}'
               },
               sgoPlusIframe = document.getElementById("sgoplus-iframe");
               if (sgoPlusIframe !== null) {
                   sgoPlusIframe.src = SGOSignature.getIframeURL(data);
               }
         SGOSignature.receiveForm();
        });
        </script>
<iframe id="sgoplus-iframe" src="" scrolling="no" allowtransparency="true" frameborder="0" height="300"></iframe>


</div>