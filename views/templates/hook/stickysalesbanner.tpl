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
<div
    x-data="{ open: true }"
    class="sticky bottom-0 m-auto w-full h-[90px] z-10"
    :class="open ? 'h-[90px]' : 'h-[60px]'"
    style="background-color: {$bg_color};"
>
    <button
        @click="open = !open"
        class="absolute z-20 focus:outline-none mt-[3px] rounded-full left-1/2 top-[-10%] cursor-pointer material-icons"
        :class="open ? 'mt-[3px]' : 'mt-0'"
        style="background-color: {$bg_color}; color: {$text_color}"
    >
        keyboard_arrow_down
    </button>

    <div class="relative my-0 mx-auto pt-2.5">
            <div class="relative text-center m-0 p-2.5">
                <p class="m-0" style="color: {$text_color}">
                        <span>{$message_first_line}</span>
                </p>
                <p :class="open ? '' : 'hidden'" style="color: {$text_color}" >
                    {$message_second_line}:&nbsp;<span class="font-bold underline">{$coupon_code}</span>
                </p>
            </div>
        </div>
</div>
{/if}
