jQuery(function ($) {
    'use strict';

    // Inicia todos os campos como recolhidos, exceto o primeiro para melhor visualização inicial
    $('.decpj-card:not(:first)').addClass('is-closed');

    // 1. Lógica de Reordenação (Drag and Drop)
    $('#decpj-fields-container').sortable({
        handle: '.decpj-drag-handle',
        placeholder: 'ui-sortable-placeholder',
        opacity: 0.7
    });

    // 2. Lógica para Adicionar um Novo Campo
    $('#decpj-add-field').on('click', function() {
        // Pega o HTML do template, que está escondido na página
        let template = $('#decpj-field-template').html();
        // Gera um índice único baseado no tempo atual para evitar conflitos de ID
        let newIndex = new Date().getTime();
        template = template.replace(/\{index\}/g, newIndex);
        
        let newField = $(template);
        $('#decpj-fields-container').append(newField);
        $('.decpj-no-fields').hide(); // Esconde a mensagem "Nenhum campo configurado"

        // Expande o novo card e foca no primeiro input para facilitar o preenchimento
        newField.removeClass('is-closed');
        newField.find('input[type="text"]').first().focus();
    });

    // 3. Lógica para Remover um Campo
    // Usamos 'on' para que o evento funcione também em campos adicionados dinamicamente
    $('#decpj-fields-container').on('click', '.decpj-remove-field', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Impede que o clique acione o evento de recolher/expandir o card
        
        if (confirm('Tem certeza que deseja remover este campo? Esta ação não pode ser desfeita.')) {
            $(this).closest('.decpj-card').remove();
            
            // Se não houver mais campos, mostra a mensagem inicial novamente
            if ($('#decpj-fields-container .decpj-card').length === 0) {
                 $('.decpj-no-fields').show();
            }
        }
    });

    // 4. Lógica para Recolher/Expandir (Accordion)
    $('#decpj-fields-container').on('click', '.decpj-card-header', function(e) {
        // Ignora o clique se o alvo for o ícone de arrastar ou o botão de remover
        if ($(e.target).is('.decpj-drag-handle, .decpj-remove-field')) {
            return;
        }
        $(this).closest('.decpj-card').toggleClass('is-closed');
    });

    // 5. Lógica para atualizar título do card e meta key em tempo real
    $('#decpj-fields-container').on('input', '.decpj-label-input', function() {
        let newTitle = $(this).val() || 'Novo Campo';
        $(this).closest('.decpj-card').find('.decpj-card-title').text(newTitle);
    });

    $('#decpj-fields-container').on('input', '.decpj-meta-key-input', function() {
        let card = $(this).closest('.decpj-card');
        let newId = $(this).val().replace(/\s+/g, '_').toLowerCase();
        
        // Atualiza todos os atributos 'name' dentro do card para que o novo campo seja salvo corretamente
        card.find('[name]').each(function() {
            let oldName = $(this).attr('name');
            let newName = oldName.replace(/decpj_new_field_(\d+)/, newId);
            $(this).attr('name', newName);
        });
    });
});