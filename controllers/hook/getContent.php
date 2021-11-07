<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    indaco <github@mircoveltri.me>
 * @copyright Since 2021 Mirco Veltri
 * @license   https://opensource.org/licenses/AFL-3.0  Academic Free License (AFL 3.0)
 */

require_once _PS_MODULE_DIR_ . 'stickysalesbanner/classes/helpers/FormValidator.php';

use StickySalesBanner\Helpers\FormValidator;

class StickysalesbannerGetContentController
{
    /** @var null */
    protected $module = null;
    /** @var Context|null */
    protected $context = null;
    /** @var \PrestaShopBundle\Translation\TranslatorComponent|null */
    protected $translator = null;
    private $db = null;

    public function __construct($module, $file, $path)
    {
        $this->module = $module;
        $this->context = Context::getContext();
        $this->translator = $this->context->getTranslator();
        $this->db = Db::getInstance(_PS_USE_SQL_SLAVE_);
    }

    public function run()
    {
        return $this->processConfiguration();
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    public function renderForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();

        // General
        $helper->table = $this->module->name;
        //$helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->module->name;

        // Title and toolbar
        $helper->title = $this->module->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = false;
        $helper->show_cancel_button = true;
        $helper->submit_action = 'submit' . $this->module->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        //$helper->identifier = $this->identifier;

        $helper->tpl_vars = [
            'fields_value' => $this->getModuleConfigFromDB(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $defaultLang,
        ];

        return $helper->generateForm([$this->makeConfigForm()]);
    }

    protected function makeConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->translator->trans('Settings', [], 'Modules.Stickysalesbanner.Admin'),
                    'icon' => 'icon-cogs',
                ],

                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->translator->trans('Enabled', [], 'Modules.Stickysalesbanner.Admin'),
                        'name' => $this->module->module_prefix . 'LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->translator->trans('Enable this module on your shop', [], 'Modules.Stickysalesbanner.Admin'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->translator->trans('Yes', [], 'Modules.Stickysalesbanner.Admin')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->translator->trans('No', [], 'Modules.Stickysalesbanner.Admin')
                            ]
                        ],
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'prefix' => '<i class="icon icon-text-width"></i>',
                        'label' => $this->translator->trans('First Line Message', [], 'Modules.Stickysalesbanner.Admin'),
                        'name' => $this->module->module_prefix . 'MESSAGE_FIRST_LINE',
                        'desc' => $this->translator->trans('Enter a message text for your banner. This text will be always visible when the module is enabled', [], 'Modules.Stickysalesbanner.Admin'),
                        'col' => 4,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'prefix' => '<i class="icon icon-text-width"></i>',
                        'label' => $this->translator->trans('Second Line Message', [], 'Modules.Stickysalesbanner.Admin'),
                        'name' => $this->module->module_prefix . 'MESSAGE_SECOND_LINE',
                        'desc' => $this->translator->trans('Enter a message text for your banner', [], 'Modules.Stickysalesbanner.Admin'),
                        'col' => 4,
                    ],
                    [
                        'type' => 'text',
                        'required' => true,
                        'prefix' => '<i class="icon icon-dollar"></i>',
                        'label' => $this->translator->trans('Coupon Code', [], 'Modules.Stickysalesbanner.Admin'),
                        'name' => $this->module->module_prefix . 'COUPON_CODE',
                        'desc' => $this->translator->trans('Enter a valid coupon code', [], 'Modules.Stickysalesbanner.Admin'),
                        'col' => 4,
                    ],
                    [
                        'type' => 'color',
                        'id'   => 'bg_color_0',
                        'label' => $this->translator->trans('Background Color (HEX value)', [], 'Modules.Stickysalesbanner.Admin'),
                        'name' => $this->module->module_prefix . 'BG_COLOR',
                        'data-hex' => true,
                        'col' => 20
                    ],
                    [
                        'type' => 'color',
                        'id'   => 'txt_color_0',
                        'label' => $this->translator->trans('Text color (HEX value)', [], 'Modules.Stickysalesbanner.Admin'),
                        'name' => $this->module->module_prefix . 'TEXT_COLOR',
                        'data-hex' => true,
                        'col' => 20
                    ],
                ],
                'submit' => [
                    'title' => $this->translator->trans('Save', [], 'Modules.Stickysalesbanner.Admin'),
                ],
            ],
        ];
    }

    /**
     * Save form data.
     *
     * @param $form_data
     *
     * @return array
     */
    protected function postProcess($form_data): array
    {
        $validation = new FormValidator($form_data);
        $errors = $validation->validateForm();
        if (!empty($errors)) {
            return [
                'is_valid' => false,
                'messages' => $errors
            ];
        }

        $resultMessage = null;

        $dbOp_response = $this->updateModuleConfigToDB($this->db, $form_data);
        if (!$dbOp_response['is_valid']) {
            return [
                'is_valid' => false,
                'messages' => array($dbOp_response['message'])
            ];
        }

        return [
            'is_valid' => true,
            'messages' => array($resultMessage),

        ];
    }

    /**
     * @return array
     *
     * @throws PrestaShopException
     */
    private function getModuleConfigFromDB(): array
    {
        $vars = array();
        $results = $this->db->executeS('SELECT `name`, `value` FROM `' . _DB_PREFIX_ . $this->module->name . '`');

        foreach ($results as $row) {
            $vars[$this->module->module_prefix . $row["name"]] = $row["value"];
        }

        unset($results);
        return $vars;
    }

    /**
     * @param $db
     * @param $form_data
     *
     * @return array
     */
    private function updateModuleConfigToDB($db, $form_data): array
    {
        foreach ($form_data as $key => $value) {
            if (!$db->update($this->module->name, [
                'value' => pSQL($value),
                'date_upd' => date('Y-m-d H:i:s'),
            ], 'name = "' . $key . '"', 0, false, false, true)
            ) {
                return [
                    'is_valid' => false,
                    'message' => 'Something went wrong updating config values on the database'
                ];
            }
        }

        return [
            'is_valid' => true,
            'message' => ''
        ];
    }

    /**
     * @return array
     */
    private function getConfigFormValues()
    {
        return [
            'live_mode' => (string)(Tools::getValue($this->module->module_prefix . 'LIVE_MODE')),
            'message_first_line' => (string)(Tools::getValue($this->module->module_prefix . 'MESSAGE_FIRST_LINE')),
            'message_second_line' => (string)(Tools::getValue($this->module->module_prefix . 'MESSAGE_SECOND_LINE')),
            'coupon_code' => (string)(Tools::getValue($this->module->module_prefix . 'COUPON_CODE')),
            'bg_color' => (string)(Tools::getValue($this->module->module_prefix . 'BG_COLOR')),
            'text_color' => (string)(Tools::getValue($this->module->module_prefix . 'TEXT_COLOR')),
        ];
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    private function processConfiguration()
    {
        $submitOutput = $this->handleFormSubmit();
        return $submitOutput . $this->renderForm();
    }

    private function handleFormSubmit(): string
    {
        $output = '';
        if ((bool)Tools::isSubmit('submit' . $this->module->name)) {

            $form_data = $this->getConfigFormValues();
            $processResult = $this->postProcess($form_data);
            if (!$processResult['is_valid']) {
                foreach ($processResult['messages'] as $message) {
                    $output .= $this->module->displayError($this->translator->trans($message, [], 'Modules.Stickysalesbanner.Admin'));
                }
            } else {
                $output .= $this->module->displayConfirmation($this->translator->trans('Settings updated', [], 'Modules.Stickysalesbanner.Admin'));
            }
        }
        return $output;
    }
}
