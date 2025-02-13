<?php
class Form_View
{
     public static function display_admin_page()
     {
          $forms = Form_Model::get_all_forms();
?>
          <div class="wrap">
               <h1>Gestion des formulaires</h1>

               <div class="form-creation-section">
                    <h2>Créer un nouveau formulaire</h2>
                    <form method="post" action="">
                         <?php wp_nonce_field('create_form', 'form_creator_nonce'); ?>
                         <div class="form-field">
                              <label for="form_title">Titre du formulaire:</label>
                              <input type="text" id="form_title" name="form_title" required>
                         </div>

                         <div id="fields-container">
                              <h3>Champs du formulaire</h3>
                              <div class="field-row">
                                   <div class="row-inputs">
                                        <select name="field_type[]" class="field-type-select">
                                             <option value="text">Texte</option>
                                             <option value="email">Email</option>
                                             <option value="textarea">Zone de texte</option>
                                             <option value="file">Fichier</option>
                                             <option value="date">Date</option>
                                        </select>
                                        <input type="text" name="field_desc[]" placeholder="description">
                                        <input type="text" name="field_exempl[]" placeholder="texte explicatif">
                                        <input type="text" name="field_icon[]" placeholder="icon">
                                   </div>
                                   <div class="row-inputs">
                                        <input type="text" name="field_label[]" placeholder="Label" required>
                                        <input type="text" name="field_name[]" placeholder="Nom du champ" required>
                                        <div class="file-options" style="display: none;">
                                             <select name="file_type[]">
                                                  <option value="all">Tous les fichiers</option>
                                                  <option value="image/*">Images uniquement</option>
                                                  <option value="application/pdf">PDF uniquement</option>
                                             </select>
                                             <input type="number" name="max_file_size[]" placeholder="Taille max (MB)" value="2">
                                        </div>
                                        <label>
                                             <input type="checkbox" name="field_required[]"> Requis
                                        </label>
                                        <button type="button" class="remove-field button-secondary">Supprimer</button>
                                   </div>
                              </div>
                         </div>

                         <div class="form-actions">
                              <button type="button" id="add-field" class="button button-secondary">Ajouter un champ</button>
                              <input type="submit" class="button button-primary" value="Créer le formulaire">
                         </div>
                    </form>
               </div>

               <div class="existing-forms-section">
                    <h2>Formulaires existants</h2>
                    <table class="wp-list-table widefat fixed striped">
                         <thead>
                              <tr>
                                   <th>ID</th>
                                   <th>Titre</th>
                                   <th>Shortcode</th>
                                   <th>Actions</th>
                              </tr>
                         </thead>
                         <tbody>
                              <?php if ($forms): ?>
                                   <?php foreach ($forms as $form): ?>
                                        <tr>
                                             <td><?php echo esc_html($form->id); ?></td>
                                             <td><?php echo esc_html($form->title); ?></td>
                                             <td><code>[form_manager id="<?php echo esc_html($form->id); ?>"]</code></td>
                                             <td>
                                                  <a href="?page=form_manager&view=submissions&form_id=<?php echo esc_attr($form->id); ?>"
                                                       class="button button-secondary">
                                                       Voir données
                                                  </a>
                                                  <a href="?page=form_manager&action=delete_submissions&form_id=<?php echo esc_attr($form->id); ?>"
                                                       class="button button-secondary delete-submissions">
                                                       Supprimer données
                                                  </a>
                                                  <a href="?page=form_manager&action=delete_form&form_id=<?php echo esc_attr($form->id); ?>&_wpnonce=<?php echo wp_create_nonce('delete_form_' . $form->id); ?>"
                                                       class="button button-link-delete delete-form"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce formulaire ? Cette action supprimera également toutes les soumissions associées.');">
                                                       Supprimer form
                                                  </a>
                                             </td>
                                        </tr>
                                   <?php endforeach; ?>
                              <?php else: ?>
                                   <tr>
                                        <td colspan="4">Aucun formulaire créé.</td>
                                   </tr>
                              <?php endif; ?>
                         </tbody>
                    </table>
               </div>
          </div>
     <?php
     }

     public static function display_form($form_id)
     {
          $form = Form_Model::get_form($form_id);
          if (!$form) return 'Formulaire non trouvé.';

          $fields = json_decode($form->fields, true);
          if (!$fields) return 'Erreur lors du chargement du formulaire.';

          ob_start();
     ?>
          <form method="post" class="libscode-form-manager-form" enctype="multipart/form-data">
               <?php wp_nonce_field('form_manager_save', 'form_manager_nonce'); ?>
               <input type="hidden" name="form_id" value="<?php echo esc_attr($form_id); ?>">

               <?php foreach ($fields as $field): ?>
                    <div class="libscode-form-group">
                         <label>
                              <?php echo esc_html($field['label']); ?>

                              <?php if (isset($field['required']) && $field['required']): ?>
                                   <span class="libscode-required">*</span>
                              <?php endif; ?>
                         </label>
                         <?php if (isset($field['desc'])): ?>
                              <span class="libscode-description-input"> <?php echo esc_html($field['desc']) ?></span>
                         <?php endif; ?>
                         <div class="libscode-input-wrapper">
                              <?php
                              switch ($field['type']) {
                                   case 'text':
                                        echo '<span class="libscode-input-icon"><i class="fas fa-arrow-right"></i></span>';
                                        echo '<input type="text" name="' . esc_attr($field['name']) . '" ' .
                                             (isset($field['required']) && $field['required'] ? 'required' : '') .
                                             ' placeholder="">';
                                        echo (isset($field['exempl'])) ? '<span class="input-exemple"> - Exemple : ' . esc_attr($field['exempl']) . '</span>' : '';
                                        break;
                                   case 'email':
                                        echo '<span class="libscode-input-icon"><i class="fas fa-arrow-right"></i></span>';
                                        echo '<input type="email" name="' . esc_attr($field['name']) . '" ' .
                                             (isset($field['required']) && $field['required'] ? 'required' : '') .
                                             ' placeholder="">';
                                        echo (isset($field['exempl'])) ? '<span class="input-exemple"> - Exemple : ' . esc_attr($field['exempl']) . '</span>' : '';
                                        break;
                                   case 'date':
                                        echo '<input type="date" name="' . esc_attr($field['name']) . '" ' .
                                             (isset($field['required']) && $field['required'] ? 'required' : '') .
                                             ' placeholder="">';
                                        echo (isset($field['exempl'])) ? '<span class="input-exemple"> - Exemple : ' . esc_attr($field['exempl']) . '</span>' : '';
                                        break;
                                   case 'textarea':
                                        echo '<textarea name="' . esc_attr($field['name']) . '" ' .
                                             (isset($field['required']) && $field['required'] ? 'required' : '') .
                                             ' placeholder=""></textarea>';
                                        echo (isset($field['exempl'])) ? '<span class="input-exemple"> - Exemple : ' . esc_attr($field['exempl']) . '</span>' : '';
                                        break;
                                   case 'file':
                                        $accept = isset($field['file_type']) && $field['file_type'] !== 'all' ?
                                             'accept="' . esc_attr($field['file_type']) . '"' : '';
                                        echo '<input type="file" name="' . esc_attr($field['name']) . '" ' .
                                             $accept . ' ' .
                                             (isset($field['required']) && $field['required'] ? 'required' : '') . '>';
                                        echo (isset($field['exempl'])) ? '<span class="input-exemple"> - Exemple : ' . esc_attr($field['exempl']) . '</span>' : '';
                                        if (isset($field['max_file_size'])) {
                                             echo '<p class="libscode-description">Taille maximale: ' .
                                                  esc_html($field['max_file_size']) . 'MB</p>';
                                        }
                                        break;
                              }
                              ?>
                         </div>
                    </div>
               <?php endforeach; ?>
               <button type="submit" class="libscode-form-submit">Envoyer</button>
          </form>
     <?php
          return ob_get_clean();
     }




     public static function display_submissions_page($form_id = null)
     {
          $submissions = Form_Model::get_submissions($form_id);
     ?>
          <div class="wrap">
               <h1>
                    Soumissions des formulaires
                    <a href="?page=form_manager" class="page-title-action">Retour aux formulaires</a>
               </h1>
               <table class="wp-list-table widefat fixed striped">
                    <thead>
                         <tr>
                              <th>ID</th>
                              <th>Formulaire</th>
                              <th>Données</th>
                              <th>Date</th>
                         </tr>
                    </thead>
                    <tbody>
                         <?php foreach ($submissions as $submission): ?>
                              <tr>
                                   <td><?php echo esc_html($submission->id); ?></td>
                                   <td><?php echo esc_html($submission->form_title); ?></td>
                                   <td><?php
                                        $data = json_decode($submission->form_data, true);
                                        foreach ($data as $key => $value) {
                                             echo esc_html($key) . ': ' . esc_html($value) . '<br>';
                                        }
                                        ?></td>
                                   <td><?php echo esc_html($submission->created_at); ?></td>
                              </tr>
                         <?php endforeach; ?>
                    </tbody>
               </table>
          </div>
<?php
     }
}
