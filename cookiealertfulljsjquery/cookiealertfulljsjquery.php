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

if (!defined('_PS_VERSION_')) {
    exit;
}

class CookieAlertFullJsJquery extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'cookiealertfulljsjquery';
        $this->tab = 'administration';
        $this->version = '0.1.0';
        $this->author = 'eworld Acceleraotor';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Cookie Alert in JS/jQuery');
        $this->description = $this->l('This module displays the legal cookies use alert at the top or at the bottom of the page. It\'s fully written in JavaScript/jQuery to be fully compatible with a cache system');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('EACC_COOKIE_JQUERY_ACTIVE', 2);
        Configuration::updateValue('EACC_COOKIE_JQUERY_TEST_IP', '');
        Configuration::updateValue('EACC_COOKIE_JQUERY_BAR_POSITION', 'top');
        Configuration::updateValue('EACC_COOKIE_JQUERY_BAR_STYLES', 'background:black;color:white;padding:8px 30px;');
        Configuration::updateValue('EACC_COOKIE_JQUERY_TEXT', array(
            'fr'=>'En poursuivant votre navigation sur ce site, vous acceptez l\'utilisation de cookies pour vous proposer des contenus et services adaptés à vos centres d\'intérêts.',
            'en'=>'Our website uses cookies. By continuing we assume your permission to deploy cookies in order to give you services and contents adapted to your centers of interest',
        ));
        Configuration::updateValue('EACC_COOKIE_JQUERY_TEXT_STYLES', 'color:white;font-family:Arial;font-size:11px;text-align:center;');
        Configuration::updateValue('EACC_COOKIE_JQUERY_BUTTON_TEXT', array(
            'fr'=>'Fermer',
            'en'=>'Close'
        ));
        Configuration::updateValue('EACC_COOKIE_JQUERY_BUTTON_STYLES', 'border-radius:4px;padding:4px 8px;background:#AAA;color:white;font-weight:bold;');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('EACC_COOKIE_JQUERY_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitCookie-alert-full-js-jqueryModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        if (isset($this->errors) && is_array($this->errors) && count($this->errors) > 0) {
            $output .= join("\r\n", $this->errors);
        }

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCookie-alert-full-js-jqueryModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Enable'),
                        'name' => 'EACC_COOKIE_JQUERY_ACTIVE',
                        'class' => 't',
                        'required' => true,
                        'is_bool' => false,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 2,
                                'label' => $this->l('Disabled')
                            ),
                            array(
                                'id' => 'active_test',
                                'value' => 3,
                                'label' => $this->l('Test mode only (you must specify IP)')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-lock"></i>',
                        'desc' => $this->l('This module will be active only for this public IP. Your IP is ' . $_SERVER['REMOTE_ADDR']),
                        'name' => 'EACC_COOKIE_JQUERY_TEST_IP',
                        'label' => $this->l('Test mode IP'),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Bar Position'),
                        'name' => 'EACC_COOKIE_JQUERY_BAR_POSITION',
                        'class' => 't',
                        'required' => true,
                        'is_bool' => false,
                        'values' => array(
                            array(
                                'id' => 'position_top',
                                'value' => 'top',
                                'label' => $this->l('Top')
                            ),
                            array(
                                'id' => 'position_bottom',
                                'value' => 'bottom',
                                'label' => $this->l('Bottom')
                            )
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Bar CSS styles'),
                        'name' => 'EACC_COOKIE_JQUERY_BAR_STYLES',
                        'desc' => 'You can fully configure and modifiy the &lt;div&gt; where cookie alert is displayed',
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Alert\'s text FR'),
                        'name' => 'EACC_COOKIE_JQUERY_TEXT_FR',
                        'desc' => 'The text displayed inside a &lt;p&gt; in the alert bar (FR)',
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Alert\'s text EN'),
                        'name' => 'EACC_COOKIE_JQUERY_TEXT_EN',
                        'desc' => 'The text displayed inside a &lt;p&gt; in the alert bar (EN)',
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Text CSS styles'),
                        'name' => 'EACC_COOKIE_JQUERY_TEXT_STYLES',
                        'desc' => 'You can fully configure and modifiy the &lt;p&gt; where text is displayed',
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Alert\'s button FR'),
                        'name' => 'EACC_COOKIE_JQUERY_BUTTON_TEXT_FR',
                        'desc' => 'The text for the closing button (FR)',
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Alert\'s button EN'),
                        'name' => 'EACC_COOKIE_JQUERY_BUTTON_TEXT_EN',
                        'desc' => 'The text for the closing button (EN)',
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Button CSS styles'),
                        'name' => 'EACC_COOKIE_JQUERY_BUTTON_STYLES',
                        'desc' => 'You can fully configure and modifiy the &lt;a&gt; where button is displayed',
                        'required' => true
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $textTranslations = Configuration::get('EACC_COOKIE_JQUERY_TEXT');
        if (!is_array($textTranslations) || !array_key_exists('fr', $textTranslations)) {
            $textTranslations = array('fr'=>'', 'en'=>'');
        }
        $buttonTranslations = Configuration::get('EACC_COOKIE_JQUERY_BUTTON_TEXT');
        if (!is_array($buttonTranslations) || !array_key_exists('fr', $buttonTranslations)) {
            $buttonTranslations = array('fr'=>'', 'en'=>'');
        }
        return array(
            'EACC_COOKIE_JQUERY_ACTIVE' => Configuration::get('EACC_COOKIE_JQUERY_ACTIVE'),
            'EACC_COOKIE_JQUERY_TEST_IP' => Configuration::get('EACC_COOKIE_JQUERY_TEST_IP'),
            'EACC_COOKIE_JQUERY_BAR_POSITION' => Configuration::get('EACC_COOKIE_JQUERY_BAR_POSITION'),
            'EACC_COOKIE_JQUERY_BAR_STYLES' => Configuration::get('EACC_COOKIE_JQUERY_BAR_STYLES'),
            'EACC_COOKIE_JQUERY_TEXT_FR' => $textTranslations['fr'],
            'EACC_COOKIE_JQUERY_TEXT_EN' => $textTranslations['en'],
            'EACC_COOKIE_JQUERY_TEXT_STYLES' => Configuration::get('EACC_COOKIE_JQUERY_TEXT_STYLES'),
            'EACC_COOKIE_JQUERY_BUTTON_TEXT_FR' => $buttonTranslations['fr'],
            'EACC_COOKIE_JQUERY_BUTTON_TEXT_EN' => $buttonTranslations['en'],
            'EACC_COOKIE_JQUERY_BUTTON_STYLES' => Configuration::get('EACC_COOKIE_JQUERY_BUTTON_STYLES'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $postFormList = $this->getConfigFormValues();
        $oneEmptyField = false;

        foreach ($postFormList as $currentIndex=>$currentValue) {
            $postValue = trim(Tools::getValue($currentIndex));
            if ($currentIndex == 'EACC_COOKIE_JQUERY_ACTIVE') {
                $enabled = (int) $postValue;

                if ($enabled != 1 && $enabled != 2 && $enabled != 3) {
                    $this->errors[] = $this->displayError($this->l('Enabled value is not recognized'));
                }
                else {
                    Configuration::updateValue($currentIndex, $enabled);

                    if ($enabled == 1 && $currentValue != 1) {
                        $this->errors[] = $this->displayConfirmation($this->l('System enabled'));
                    }
                    else if ($enabled == 2 && $currentValue != 2) {
                        $this->errors[] = $this->displayConfirmation($this->l('System disabled'));
                    }
                    else if ($enabled == 3 && $currentValue != 3) {
                        $this->errors[] = $this->displayConfirmation($this->l('Test mode enabled'));
                    }
                }
            }
            else if ($currentIndex == 'EACC_COOKIE_JQUERY_TEST_IP') {
                if ($postValue != '') {
                    Configuration::updateValue($currentIndex, $postValue);
                    if (!self::isValidIpAddress($postValue)) {
                        $this->errors[] = $this->displayError($this->l('IP address is not valid'));
                    }
                }
                else {
                    $enabled = (int) Configuration::get('EACC_COOKIE_JQUERY_ACTIVE');
                    if ($enabled == 3) {
                        $this->errors[] = $this->displayError($this->l('Test mode is active and IP address is empty'));
                    }
                }
            }
            else {
                if ($postValue == '') {
                    $oneEmptyField = true;
                }
                else {
                    Configuration::updateValue($currentIndex, $postValue);
                }
            }
        }

        if ($oneEmptyField) {
            $this->errors[] = $this->displayError($this->l('At least, one field is empty'));
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
            $this->context->controller->addJS($this->_path.'/views/js/jquery.cookie.js');
        }
        else {
            $this->context->controller->addJS($this->_path.'/views/js/jquery.cookie.min.js');
        }
        $this->context->controller->addJS($this->_path.'/views/js/front.js');

        // Add script element on head, to configure the cookie JS alert
        $this->context->smarty->assign(array(
           'cookieName' => Configuration::get('EACC_COOKIE_JQUERY_NAME'),
           'cookieForced' => isset($_GET['force_eacc_cookie']) && $_GET['force_eacc_cookie'] == 1,
        ));
        $this->smarty->display($this->local_path . 'views/templates/front/javascript.tpl');
    }

    public function hookDisplayHeader()
    {
        $this->hookHeader();
    }

    /**
     * @param string $ip
     * @return bool
     */
    private static function isValidIpAddress($ip) {
        return !filter_var($ip, FILTER_VALIDATE_IP) === false;
    }
}
