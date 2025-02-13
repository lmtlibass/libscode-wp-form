jQuery(document).ready(function ($) {
    // Fonction pour générer le HTML d'une ligne de champ
    function getFieldRowHtml() {
        return `
            <div class="field-row">
                <div class="row-inputs">
                    <select name="field_type[]" class="field-type-select">
                        <option value="text">Texte</option>
                        <option value="email">Email</option>
                        <option value="textarea">Zone de texte</option>
                        <option value="file">Fichier</option>
                        <option value="date">Date</option>
                    </select>
                    <input type="text" name="field_desc[]" placeholder="Description">
                    <input type="text" name="field_exempl[]" placeholder="Texte explicatif">
                    <input type="text" name="field_icon[]" placeholder="Icon">
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
        `;
    }

    // Ajouter un nouveau champ
    $("#add-field").on("click", function () {
        $("#fields-container").append(getFieldRowHtml());
    });

    // Supprimer un champ
    $(document).on("click", ".remove-field", function () {
        if ($(".field-row").length > 1) {
            $(this).closest(".field-row").remove();
        } else {
            alert("Vous devez garder au moins un champ.");
        }
    });

    // Gérer l'affichage des options de fichier
    $(document).on("change", ".field-type-select", function () {
        var $fileOptions = $(this).closest(".field-row").find(".file-options");
        if ($(this).val() === "file") {
            $fileOptions.show();
        } else {
            $fileOptions.hide();
        }
    });

    // Auto-générer le nom du champ basé sur le label
    $(document).on("input", 'input[name="field_label[]"]', function () {
        var $nameInput = $(this).closest(".field-row").find('input[name="field_name[]"]');
        if ($nameInput.val() === "") {
            var fieldName = $(this)
                .val()
                .toLowerCase()
                .replace(/[^a-z0-9]/g, "_") // Remplacer les caractères spéciaux par "_"
                .replace(/_+/g, "_") // Supprimer les underscores multiples
                .replace(/^_|_$/g, ""); // Supprimer les underscores en début et fin
            $nameInput.val(fieldName);
        }
    });

    // Confirmer la suppression des soumissions
    $(".delete-submissions").on("click", function (e) {
        if (
            !confirm("Êtes-vous sûr de vouloir supprimer toutes les soumissions de ce formulaire ?")
        ) {
            e.preventDefault();
        }
    });
});
