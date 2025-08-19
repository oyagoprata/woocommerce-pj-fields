jQuery(function ($) {
    'use strict';

    // Inicia todos os campos como recolhidos, exceto o primeiro
    $('.bfpj-card:not(:first)').addClass('is-closed');

    // 1. Lógica de Reordenação (Drag and Drop)
    $('#bfpj-fields-container').sortable({
        handle: '.bfpj-drag-handle',
        placeholder: 'ui-sortable-placeholder',
        opacity: 0.7
    });

    // 2. Lógica para Adicionar um Novo Campo
    $('#bfpj-add-field').on('click', function() {
        var template = $('#bfpj-field-template').html();
        var newIndex = new Date().getTime(); // Gera um ID único para o novo campo
        template = template.replace(/\{index\}/g, newIndex);
        
        var newField = $(template);
        $('#bfpj-fields-container').append(newField);
        $('.bfpj-no-fields').hide();

        // Expande o novo campo e foca no primeiro input
        newField.removeClass('is-closed');
        newField.find('input[type="text"]').first().focus();
    });

    // 3. Lógica para Remover um Campo
    $('#bfpj-fields-container').on('click', '.bfpj-remove-field', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Impede que o evento de clique chegue ao header e acione o toggle
        if (confirm('Tem certeza que deseja remover este campo? Esta ação não pode ser desfeita.')) {
            $(this).closest('.bfpj-card').remove();
            if ($('#bfpj-fields-container .bfpj-card').length === 0) {
                 $('.bfpj-no-fields').show();
            }
        }
    });

    // 4. Lógica para Recolher/Expandir (Accordion)
    $('#bfpj-fields-container').on('click', '.bfpj-card-header', function(e) {
        // Ignora o clique se for no drag handle ou no botão de remover
        if ($(e.target).is('.bfpj-drag-handle, .bfpj-remove-field')) {
            return;
        }
        $(this).closest('.bfpj-card').toggleClass('is-closed');
    });

    // 5. Lógica para atualizar título do card e meta key
    $('#bfpj-fields-container').on('input', '.bfpj-label-input', function() {
        var newTitle = $(this).val() || 'Novo Campo';
        $(this).closest('.bfpj-card').find('.bfpj-card-title').text(newTitle);
    });

    $('#bfpj-fields-container').on('input', '.bfpj-meta-key-input', function() {
        var card = $(this).closest('.bfpj-card');
        var newId = $(this).val().replace(/\s+/g, '_').toLowerCase();
        
        // Atualiza todos os 'name' e 'for' attributes dentro do card
        card.find('[name]').each(function() {
            var oldName = $(this).attr('name');
            var newName = oldName.replace(/bfpj_new_field_(\d+)/, newId);
            $(this).attr('name', newName);
        });
    });
});