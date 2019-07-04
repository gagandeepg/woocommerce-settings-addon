<?php

/*
  Plugin Name: Woocommerce Settings Addon
  Description: This plugin is use for add prefix on woocommerce product page title and add meta tag with no index.
  Author: Gagandeep
  Version: 1.0.0
 */

defined('ABSPATH') || exit;


// Define WMA_PLUGIN_FILE.
if (!defined('WMA_PLUGIN_FILE')) {
    define('WMA_PLUGIN_FILE', __FILE__);
}

if (!defined('WMA_PLUGIN_DIR')) {
    define('WMA_PLUGIN_DIR', dirname(__FILE__));
}

// Include the main Woocommerce_Addon_Settings class.
if (!class_exists('Woocommerce_Addon_Settings')) {
    include_once WMA_PLUGIN_DIR . '/includes/class-woocommerce-addon-settings.php';
}

/**
 * Returns the main instance of Woocommerce_Addon_Settings Class.
 * @return Woocommerce_Addon_Settings instance
 */
function woocommerceSettingsAddon() { 
    return Woocommerce_Addon_Settings::instance();
}

$GLOBALS['woocommerce_addon_settings'] = woocommerceSettingsAddon();

