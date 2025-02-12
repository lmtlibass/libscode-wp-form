<?php

class Form_Manager
{
     public function run()
     {
          add_action('init', array('Form_Controller', 'register_shortcode'));
          add_action('admin_menu', array($this, 'add_admin_menu'));
          add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
          add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
     }

     public function add_admin_menu()
     {
          add_menu_page(
               'Form Manager',
               'Form Manager',
               'manage_options',
               'form_manager',
               array('Form_Controller', 'admin_page'),
               'dashicons-feedback'
          );
     }

     public function enqueue_admin_scripts($hook)
     {
          if ($hook !== 'toplevel_page_form_manager') return;

          wp_enqueue_style(
               'form-manager-admin',
               FORM_MANAGER_URL . 'assets/css/admin.css',
               array(),
               FORM_MANAGER_DB_VERSION
          );

          wp_enqueue_script(
               'form-manager-admin',
               FORM_MANAGER_URL . 'assets/js/admin.js',
               array('jquery'),
               FORM_MANAGER_DB_VERSION,
               true
          );
     }

     public function enqueue_frontend_scripts()
     {
          wp_enqueue_style(
               'form-manager-frontend',
               FORM_MANAGER_URL . 'assets/css/frontend.css',
               array(),
               FORM_MANAGER_DB_VERSION
          );
     }
}
