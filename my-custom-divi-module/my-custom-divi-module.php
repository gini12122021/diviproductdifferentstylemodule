<?php

/**
 * Plugin Name
 *
 * @package           PluginPackage
 * @author            Gayatri Patel
 * @copyright         2024 Gayatri Patel
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Wocoomerce Divi Module
 * Plugin URI:        https://example.com/plugin-name
 * Description:       This is E Wocoomerce product Style! It makes  widgets for Divi!
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Gayatri Patel
 * Author URI:        https://example.com
 * Text Domain:       ella
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://example.com/my-plugin/
 */


if (!defined('ABSPATH')) exit(); // Exit if accessed directly

define('ELLA_VERSION', '1.0.0');
define('ELLA_ADDONS_PL_ROOT', __FILE__);
define('ELLA_PL_URL', plugins_url('/', ELLA_ADDONS_PL_ROOT));
define('ELLA_PL_PATH', plugin_dir_path(ELLA_ADDONS_PL_ROOT));
define('ELLA_PL_ASSETS', trailingslashit(ELLA_PL_URL . 'assets'));
define('ELLA_ADMIN_ASSETS', trailingslashit(ELLA_PL_URL . 'assets/admin'));
define('ELLA_PL_INCLUDE', trailingslashit(ELLA_PL_PATH . 'include'));
define('ELLA_PL_WIDGET', trailingslashit(ELLA_PL_PATH . 'widget'));
define('ELLA_PLUGIN_BASE', plugin_basename(ELLA_ADDONS_PL_ROOT));


// Include the custom module class
function my_custom_divi_module()
{
    if (class_exists('ET_Builder_Module')) {
        require_once plugin_dir_path(__FILE__) . 'includes/my-custom-module.php';
    }
}
add_action('et_builder_ready', 'my_custom_divi_module');


add_action('admin_enqueue_scripts', 'my_custom_switcher_scripts');
function my_custom_switcher_scripts($hook)
{
    if ($hook !== 'toplevel_page_my_custom_page') {
        return;
    }
    wp_enqueue_script('my_custom_switcher_script', plugins_url('js/switcher-script.js', __FILE__), array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'my_custom_divi_plugin_css_js');
function my_custom_divi_plugin_css_js()
{

    wp_enqueue_style('ella-swiper-bundle', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.7.0/swiper-bundle.min.css', array(), rand(1, 100));
    wp_enqueue_style('ella-widgets', ELLA_PL_ASSETS . 'css/ella-widgets.css', array(), rand(1, 100));
    wp_enqueue_script('ella-swiper-bundle-js',  'https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.7.0/swiper-bundle.min.js', array('jquery'), rand(1, 100), TRUE);
    wp_enqueue_script('ella-widgets-js', ELLA_PL_ASSETS . 'js/ella-widgets.js', array('ella-swiper-bundle-js'), rand(1, 100), TRUE);
}
