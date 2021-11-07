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

namespace StickySalesBanner\Helpers;

class FormValidator
{
    private $data;
    private $errors;
    private static $fields = [
        'message_first_line',
        'message_second_line',
        'coupon_code',
        'bg_color',
        'text_color'
    ];

    public function __construct($form_data)
    {
        $this->data = $form_data;
    }

    public function validateForm()
    {
        foreach (self::$fields as $field) {
            if (!array_key_exists($field, $this->data)) {
                trigger_error("$field is not present in data");
                return;
            }
        }
        $this->validateMessageFirstLine();
        $this->validateMessageSecondLine();
        $this->validateCouponCode();
        $this->validateBackgroundColor();
        $this->validateTextColor();
        return $this->errors;
    }

    private function validateMessageFirstLine()
    {
        $val = trim($this->data['message_first_line']);
        if (empty($val)) {
            $this->addError('message_first_line', 'Invalid Configuration: <strong>Message First Line</strong> is required.');
        }
    }

    private function validateMessageSecondLine()
    {
        $val = trim($this->data['message_second_line']);
        if (empty($val)) {
            $this->addError('message_second_line', 'Invalid Configuration: <strong>Message Second Line</strong> is required.');
        }
    }

    private function validateCouponCode()
    {
        $val = trim($this->data['coupon_code']);
        if (empty($val)) {
            $this->addError('coupon_code', 'Invalid Configuration: <strong>Coupon Code</strong> is required.');
        }
    }

    private function validateBackgroundColor()
    {
        $val = trim($this->data['bg_color']);
        if (empty($val)) {
            $this->addError('bg_color', 'Invalid Configuration: <strong>Background Color</strong> is required.');
        } elseif (preg_match('/^#[a-f0-9]{6}$/i', $val)) {
            $this->addError('bg_color', 'Invalid Configuration: <strong>Background Color</strong> must be a valid color code.');
        }
    }

    private function validateTextColor()
    {
        $val = trim($this->data['text_color']);
        if (empty($val)) {
            $this->addError('text_color', 'Invalid Configuration: <strong>Text Color</strong> is required.');
        } elseif (preg_match('/^#[a-f0-9]{6}$/i', $val)) {
            $this->addError('text_color', 'Invalid Configuration: <strong>Text Color</strong> must be a valid color code.');
        }
    }

    private function addError($key, $value)
    {
        $this->errors[$key] = $value;
    }
}
