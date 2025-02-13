<?php
class Form_Controller
{
     public static function register_shortcode()
     {
          add_shortcode('form_manager', array(__CLASS__, 'render_form'));
     }

     public static function render_form($atts)
     {
          $atts = shortcode_atts(['id' => 0], $atts);

          if (
               $_SERVER['REQUEST_METHOD'] === 'POST' &&
               isset($_POST['form_manager_nonce']) &&
               wp_verify_nonce($_POST['form_manager_nonce'], 'form_manager_save')
          ) {

               $form_id  = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
               $form     = Form_Model::get_form($form_id);

               if ($form) {
                    $fields             = json_decode($form->fields, true);
                    $submission_data    = array();
                    $files              = array();

                    foreach ($fields as $field) {
                         $field_name = $field['name'];
                         if ($field['type'] === 'file' && isset($_FILES[$field_name])) {
                              $files[$field_name] = $_FILES[$field_name];
                         } elseif (isset($_POST[$field_name])) {
                              $submission_data[$field_name] = sanitize_text_field($_POST[$field_name]);
                         }
                    }

                    if (!empty($submission_data) || !empty($files)) {
                         Form_Model::save_submission_with_file($form_id, $submission_data, $files);
                         return '<p>Merci pour votre soumission!</p>';
                    }
               }
          }

          return Form_View::display_form($atts['id']);
     }

     public static function add_admin_menu()
     {
          add_menu_page(
               'Form Manager',
               'Form Manager',
               'manage_options',
               'form_manager',
               array(__CLASS__, 'admin_page')
          );
     }

     public static function admin_page()
     {
          if (
               isset($_GET['action']) && $_GET['action'] === 'delete_form' &&
               isset($_GET['form_id']) && isset($_GET['_wpnonce'])
          ) {

               if (wp_verify_nonce($_GET['_wpnonce'], 'delete_form_' . $_GET['form_id'])) {
                    $form_id = intval($_GET['form_id']);
                    Form_Model::delete_form($form_id);
                    add_action('admin_notices', function () {
                         echo '<div class="notice notice-success is-dismissible"><p>Formulaire supprimé avec succès.</p></div>';
                    });
                    wp_redirect(admin_url('admin.php?page=form_manager'));
                    exit;
               }
          }
          if (isset($_GET['view']) && $_GET['view'] === 'submissions') {
               $form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : null;
               Form_View::display_submissions_page($form_id);
          } else {
               if (
                    $_SERVER['REQUEST_METHOD'] === 'POST' &&
                    isset($_POST['form_creator_nonce']) &&
                    wp_verify_nonce($_POST['form_creator_nonce'], 'create_form')
               ) {

                    $fields = array();
                    for ($i = 0; $i < count($_POST['field_type']); $i++) {
                         $fields[] = array(
                              'desc'         =>sanitize_text_field($_POST['field_desc'][$i]),
                              'exempl'       =>sanitize_text_field($_POST['field_exempl'][$i]),
                              'icon'         => sanitize_text_field($_POST['field_icon'][$i]),
                              'type'         => sanitize_text_field($_POST['field_type'][$i]),
                              'label'        => sanitize_text_field($_POST['field_label'][$i]),
                              'name'         => sanitize_text_field($_POST['field_name'][$i]),
                              'required'     => isset($_POST['field_required'][$i])
                         );
                    }

                    Form_Model::create_form(
                         sanitize_text_field($_POST['form_title']),
                         $fields
                    );
               }

               Form_View::display_admin_page();
          }
     }
}
