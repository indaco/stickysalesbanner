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

class StickysalesbannerDisplayBeforeBodyClosingTagController
{
    /** @var $module */
    protected $module = null;
    /** @var $file */
    protected $file = null;
    /** @var Context|null */
    protected $context = null;
    /** @var $_path */
    protected $_path = null;
    /** @var $db */
    private $db = null;
    /** @var string */
    private $production_file = null;

    public function __construct($module, $file, $path)
    {
        $this->module = $module;
        $this->file = $file;
        $this->context = Context::getContext();
        $this->_path = $path;
        $this->db = Db::getInstance(_PS_USE_SQL_SLAVE_);
    }

    public function run()
    {
        return $this->runner();
    }

    protected function runner()
    {
        $data = array();
        $results = $this->db->executeS('SELECT `name`, `value` FROM `' . _DB_PREFIX_ . $this->module->name . '`');

        foreach ($results as $row) {
                $data[Tools::strtolower($row["name"])] = $row["value"];
        }

        $this->context->smarty->assign($data);
        $output = $this->module->fetch('module:' . $this->module->hooks_tpl_path . '/stickysalesbanner.tpl');
        return $output;
    }
}
