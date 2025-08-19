jQuery(function ($) {
    'use strict';

    // Aplica a máscara assim que a página carregar e também em updates de AJAX no checkout
    function apply_masks() {
        var cnpjField = $('#billing_cnpj, #billing_cnpj_field input');
        var ieField = $('#billing_ie, #billing_ie_field input');
        var imField = $('#billing_im, #billing_im_field input');
        var phoneField = $('#billing_phone, #billing_phone_field input');
        
        // Opções para a máscara do CNPJ, permitindo colar com ou sem formatação
        var cnpjOptions = {
            onKeyPress: function(cnpj, e, field, options) {
                var masks = ['00.000.000/0000-00'];
                cnpjField.mask(masks[0], options);
            }
        };

        // Máscara para telefone/celular
        var phoneOptions = {
            onKeyPress: function(val, e, field, options) {
                var masks = ['(00) 0000-00009', '(00) 00000-0000'];
                var mask = (val.length > 14) ? masks[1] : masks[0];
                phoneField.mask(mask, options);
            }
        };

        if (cnpjField.length > 0) {
            cnpjField.mask('00.000.000/0000-00', cnpjOptions);
        }

        if (ieField.length > 0) {
            ieField.mask('000.000.000.000', { reverse: true });
        }
        
        if (imField.length > 0) {
            imField.mask('000.000.000', { reverse: true });
        }

        if (phoneField.length > 0) {
            phoneField.mask('(00) 00000-0000', phoneOptions);
        }
    }

    // Aplica as máscaras na carga inicial da página
    apply_masks();

    // Re-aplica as máscaras após o WooCommerce atualizar o checkout (ex: mudança de CEP)
    $(document.body).on('updated_checkout', function () {
        apply_masks();
    });
});