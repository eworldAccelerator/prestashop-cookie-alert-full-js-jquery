<?php
/**
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
 */
include(dirname(__FILE__).'/../../config/config.inc.php');

function removeCRLF($str) {
    return str_replace(array("\r\n", "\r", "\n"), '', $str);
}

if (isset($smarty) && isset($cookie)) {
    $cookieBarPosition = Configuration::get('EACC_COOKIE_JQUERY_BAR_POSITION');
    $cookieBarStyles = Configuration::get('EACC_COOKIE_JQUERY_BAR_STYLES');
    $cookieTextStyles = Configuration::get('EACC_COOKIE_JQUERY_TEXT_STYLES');
    $cookieCloseButtonStyles = Configuration::get('EACC_COOKIE_JQUERY_BUTTON_STYLES');
    $cookieTextList = Configuration::get('EACC_COOKIE_JQUERY_TEXT');
    $cookieButtonTextList = Configuration::get('EACC_COOKIE_JQUERY_BUTTON_TEXT');
    $iso_code = Language::getIsoById( (int)$cookie->id_lang );

    $smarty->assign(array(
        'cookieBarPosition' => removeCRLF($cookieBarPosition),
        'cookieBarStyles' => removeCRLF($cookieBarStyles),
        'cookieTextStyles' => removeCRLF($cookieTextStyles),
        'cookieCloseButtonStyles' => removeCRLF($cookieCloseButtonStyles),
        'cookieText' => $cookieTextList[$iso_code],
        'cookieButtonText' => $cookieButtonTextList[$iso_code],
    ));
    $smarty->display(dirname(__FILE__) . 'views/templates/front/bar.tpl');
}