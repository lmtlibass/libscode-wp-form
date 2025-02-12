<?php

/**
 * Plugin Name: Form Manager
 * Description: Un plugin permettant de créer et gérer des formulaires via un shortcode.
 * Version: 1.0.1
 * Author: libasse
 */

if (!defined('ABSPATH')) {
    exit;
}

define('FORM_MANAGER_PATH', plugin_dir_path(__FILE__));
define('FORM_MANAGER_URL', plugin_dir_url(__FILE__));
define('FORM_MANAGER_DB_VERSION', '1.0');

require_once FORM_MANAGER_PATH . 'includes/class-form-manager.php';
require_once FORM_MANAGER_PATH . 'includes/class-form-controller.php';
require_once FORM_MANAGER_PATH . 'includes/class-form-model.php';
require_once FORM_MANAGER_PATH . 'includes/class-form-view.php';

function form_manager_table_name($suffix = '')
{
    global $wpdb;
    return $wpdb->prefix . 'form_manager' . $suffix;
}

function form_manager_activate()
{
    Form_Model::install();
}
register_activation_hook(__FILE__, 'form_manager_activate');

function run_form_manager()
{
    $plugin = new Form_Manager();
    $plugin->run();
}
run_form_manager();