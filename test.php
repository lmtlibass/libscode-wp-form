<?php
class Form_Controller {
    public static function render_form($atts) {
        $atts = shortcode_atts(['id' => 0], $atts);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && 
            isset($_POST['form_manager_nonce']) && 
            wp_verify_nonce($_POST['form_manager_nonce'], 'form_manager_save')) {
            
            $form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
            $form = Form_Model::get_form($form_id);
            
            if ($form) {
                $fields = json_decode($form->fields, true);
                $submission_data = array();
                $files = array();
                
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

    public static function admin_page() {
        // Gérer la suppression des soumissions
        if (isset($_GET['action']) && $_GET['action'] === 'delete_submissions' && 
            isset($_GET['form_id'])) {
            $form_id = intval($_GET['form_id']);
            Form_Model::delete_submissions($form_id);
            wp_redirect(admin_url('admin.php?page=form_manager'));
            exit;
        }
     }