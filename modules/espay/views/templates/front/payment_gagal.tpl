{*
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


{capture name=path}{l s='Order confirmation'}{/capture}
{include file=$tpl_dir./breadcrumb.tpl}

<h2>{l s='Order confirmation'}</h2>

{assign var='current_step' value='payment'}
{include file=$tpl_dir./order-steps.tpl}

{include file=$tpl_dir./errors.tpl}

{l s='Pembayaran Anda Gagal di Proses'}<br><br>
{l s='Silahkan Ulangi Proses pembayaran Anda.'}<br><br>


<br />
<a href="{$base_dir_ssl}order.php" title="{l s='Back to '}"><img src="{$img_dir}icon/order.gif" alt="{l s='Back to orders'}" class="icon" /></a>
<a href="{$base_dir_ssl}order.php" title="{l s='Back to orders'}">{l s='Back to orders'}</a>
