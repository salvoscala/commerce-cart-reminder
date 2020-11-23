<?php

/**
 * @file
 * Hooks for the commerce_cart_reminder module.
 *

/**
 * Alter variation html on commerce cart reminder modal.
 *
 * @param Product Variation $variation
 *   The product variation.
 * @param markup $variation_html
 *   The generated markup based on View mode set in configuration.
 */
function foo_commerce_cart_reminder_modal_variation_html_alter($variation, &$variation_html) {
   $variation_html = 'Variation sku is ' . $variation->getSku();
}
