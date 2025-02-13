<?php
class Form_Model
{
     public static function install()
     {
          global $wpdb;

          $charset_collate = $wpdb->get_charset_collate();

          $forms_table        = form_manager_table_name('_forms');
          $submissions_table  = form_manager_table_name('_submissions');

          $sql_forms = "CREATE TABLE $forms_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            fields text NOT NULL,
            created_at datetime DEFAULT current_timestamp,
            PRIMARY KEY (id)
        ) $charset_collate;";

          $sql_submissions = "CREATE TABLE $submissions_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            form_id mediumint(9) NOT NULL,
            form_data text NOT NULL,
            created_at datetime DEFAULT current_timestamp,
            PRIMARY KEY (id),
            KEY form_id (form_id)
        ) $charset_collate;";

          require_once ABSPATH . 'wp-admin/includes/upgrade.php';
          dbDelta($sql_forms);
          dbDelta($sql_submissions);
     }

     public static function create_form($title, $fields)
     {
          global $wpdb;
          return $wpdb->insert(
               form_manager_table_name('_forms'),
               [
                    'title' => $title,
                    'fields' => json_encode($fields),
                    'created_at' => current_time('mysql')
               ],
               ['%s', '%s', '%s']
          );
     }

     public static function get_form($id)
     {
          global $wpdb;
          return $wpdb->get_row($wpdb->prepare(
               "SELECT * FROM " . form_manager_table_name('_forms') . " WHERE id = %d",
               $id
          ));
     }

     public static function get_all_forms()
     {
          global $wpdb;
          return $wpdb->get_results("SELECT * FROM " . form_manager_table_name('_forms'));
     }

     public static function save_submission($form_id, $data)
     {
          global $wpdb;
          return $wpdb->insert(
               form_manager_table_name('_submissions'),
               [
                    'form_id' => $form_id,
                    'form_data' => json_encode($data),
                    'created_at' => current_time('mysql')
               ],
               ['%d', '%s', '%s']
          );
     }

     public static function get_submissions($form_id = null)
     {
          global $wpdb;
          $table = form_manager_table_name('_submissions');
          $forms_table = form_manager_table_name('_forms');

          $sql = "SELECT s.*, f.title as form_title 
               FROM $table s 
               JOIN $forms_table f ON s.form_id = f.id";

          if ($form_id) {
               $sql .= $wpdb->prepare(" WHERE s.form_id = %d", $form_id);
          }

          return $wpdb->get_results($sql);
     }

     public static function save_submission_with_file($form_id, $data, $files)
     {
          global $wpdb;

          // Créer le dossier des uploads si nécessaire
          $upload_dir = wp_upload_dir();
          $form_upload_dir = $upload_dir['basedir'] . '/form-manager/' . $form_id;
          if (!file_exists($form_upload_dir)) {
               wp_mkdir_p($form_upload_dir);
          }

          // Traiter les fichiers
          $attachments = []; // Tableau pour stocker les chemins des fichiers à joindre
          foreach ($files as $field => $file) {
               if ($file['error'] === UPLOAD_ERR_OK) {
                    $file_name = sanitize_file_name($file['name']);
                    $destination = $form_upload_dir . '/' . time() . '_' . $file_name;

                    if (move_uploaded_file($file['tmp_name'], $destination)) {
                         $data[$field] = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $destination);
                         $attachments[] = $destination; // Ajouter le fichier à la liste des pièces jointes
                    }
               }
          }

          // Sauvegarder la soumission dans la base de données
          $insert_result = $wpdb->insert(
               form_manager_table_name('_submissions'),
               [
                    'form_id' => $form_id,
                    'form_data' => json_encode($data),
                    'created_at' => current_time('mysql')
               ],
               ['%d', '%s', '%s']
          );
          
          $form = Form_Model::get_form($form_id);
          // Envoyer un e-mail après la sauvegarde
          if ($insert_result) {
               $to = 'badianelifa@gmail.com';
               $subject = $form->title;
               $message = 'Une nouvelle soumission a été enregistrée pour le formulaire ID: ' . $form_id . "\n\n";
               $message .= 'Informations du demandeur : ';
               foreach ($data as $key => $value) {
                    $message .= "$key : $value\n";
               };

               $headers = ['From: Votre Nom <badianelifa@gmail.com>'];

               // Envoyer l'e-mail avec pièces jointes si elles existent
               if (!empty($attachments)) {
                    $mail_sent = wp_mail($to, $subject, $message, $headers, $attachments);
               } else {
                    $mail_sent = wp_mail($to, $subject, $message, $headers, '');
               }
               // Vérifier si l'e-mail a été envoyé
               if (!$mail_sent) {
                    var_dump('Erreur wp_mail: ' . print_r(error_get_last(), true));

               }
          }
          

          return $insert_result;
     }

     public static function delete_submissions($form_id)
     {
          global $wpdb;
          return $wpdb->delete(
               form_manager_table_name('_submissions'),
               ['form_id' => $form_id],
               ['%d']
          );
     }

     public static function delete_form($form_id)
     {
          global $wpdb;

          // Supprimer d'abord les soumissions liées
          $wpdb->delete(
               form_manager_table_name('_submissions'),
               ['form_id' => $form_id],
               ['%d']
          );

          // Supprimer le formulaire
          $wpdb->delete(
               form_manager_table_name('_forms'),
               ['id' => $form_id],
               ['%d']
          );

          // Supprimer le dossier des fichiers uploadés s'il existe
          $upload_dir = wp_upload_dir();
          $form_upload_dir = $upload_dir['basedir'] . '/form-manager/' . $form_id;
          if (file_exists($form_upload_dir)) {
               self::delete_directory($form_upload_dir);
          }
     }

     private static function delete_directory($dir)
     {
          if (!file_exists($dir)) {
               return true;
          }

          if (!is_dir($dir)) {
               return unlink($dir);
          }

          foreach (scandir($dir) as $item) {
               if ($item == '.' || $item == '..') {
                    continue;
               }

               if (!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                    return false;
               }
          }

          return rmdir($dir);
     }
}
