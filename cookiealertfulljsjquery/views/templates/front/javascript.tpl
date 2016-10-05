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
*  @author    eworld Accelerator <prestashop@eworld-accelerator.com>
*}

<script type="text/javascript">
var prestashopModeDev = {$smarty.const._PS_MODE_DEV_};
var cookieName = "{$cookieName}";
var cookieForced = {$cookieForced};
var cookieLang = {$lang_iso};
if (jQuery.length > 0) {
    jQuery(document).ready(function() {
        if (prestashopModeDev == 1) {
            console.log('Starting cookie alert module');
        }

        // If cookie exists, nothing to do
        if (jQuery.cookie(cookieName).length > 0 && cookieForced != 1) {
            if (prestashopModeDev == 1) {
                console.log('Cookie exists, nothing to do');
            }
        }
        // Display Cookie Alert Bar
        else {
            function closeCookiealertfulljsjqueryBar() {
                jQuery('#cookiealertfulljsjqueryBar').hide();
                if (prestashopModeDev == 1) {
                    console.log('Cookie Bar closed');
                }
            }
            function addCookieAlertDivToBody(html) {
                if (html.length > 0) {
                    jQuery('body').append('<div id="cookiealertfulljsjqueryBar">' + html + '</div>');
                    if (prestashopModeDev == 1) {
                        console.log('Cookie Alert Bar added to body');
                    }
                }
            }
            function addCookie() {
                // Create the cookie, for 1 year
                jQuery.cookie(cookieName, jQuery.now(), { expires: 365 });
                if (prestashopModeDev == 1) {
                    console.log('Cookie added');
                }
            }

            jQuery.ajax({
                method: "POST",
                url: "{$smarty.const._PS_BASE_URL_|escape:'htmlall':'UTF-8'}{$smarty.const.__PS_BASE_URI__|escape:'htmlall':'UTF-8'}modules/cookiealertfulljsjquery/cookiealertbar.php",
                cache: false,
                dataType: "html",
                success: function(data) {
                    addCookieAlertDivToBody(data);
                    addCookie();
                }
            });
        }

        if (prestashopModeDev == 1) {
            console.log('Ending cookie alert module');
        }
    });
}
else {
    console.log('Cookie Alert in JS/jQuery needs jQuery to work');
}
</script>
