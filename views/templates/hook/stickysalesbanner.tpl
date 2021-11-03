{*
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
*}

{if $live_mode eq '1'}
    <div class="stickybanner-section" style="background-color: {$bg_color};">
        <div aria-label="Toggle menu" id="stickybanner-button" style="background-color: {$bg_color};">
            <span class="material-icons" style="color: {$text_color}; font-size: xx-large">keyboard_arrow_down</span>
        </div>

        <div class="stickybanner-wrapper">
                <div class="stickybanner-content">
                    <p class="stickybanner-head" style="color: {$text_color}">
                            <span>{$message_first_line}</span>
                    </p>
                    <p id="coupon-code" class="stickybanner-text" style="color: {$text_color}">{$message_second_line}:&nbsp;<span style="text-decoration: underline">{$coupon_code}</span></p>
                </div>
            </div>
    </div>
{/if}
