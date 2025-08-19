jQuery(function ($) {
    'use strict';

    // Função principal para aplicar as máscaras nos campos
    function apply_masks() {
        // Seletores para os campos. Busca tanto o campo direto quanto o campo dentro do wrapper do WooCommerce.
        var cnpjField = $('#billing_cnpj, #billing_cnpj_field input');
        var ieField = $('#billing_ie, #billing_ie_field input');
        var imField = $('#billing_im, #billing_im_field input');
        var phoneField = $('#billing_phone, #billing_phone_field input');
        
        // Máscara para CNPJ
        if (cnpjField.length > 0) {
            cnpjField.mask('00.000.000/0000-00');
        }

        // Máscara genérica para Inscrição Estadual.
        if (ieField.length > 0) {
            ieField.mask('000.000.000.000', { reverse: true });
        }
        
        // Máscara genérica para Inscrição Municipal.
        if (imField.length > 0) {
             imField.mask('000.000.000', { reverse: true });
        }

        // Máscara inteligente para telefone/celular com 8 ou 9 dígitos
        if (phoneField.length > 0) {
            var phoneOptions = {
                onKeyPress: function(val, e, field, options) {
                    var masks = ['(00) 0000-00009', '(00) 00000-0000'];
                    var mask = (val.length > 14) ? masks[1] : masks[0];
                    phoneField.mask(mask, options);
                }
            };
            phoneField.mask('(00) 0000-00009', phoneOptions);
        }
    }

    // Aplica as máscaras na carga inicial da página
    apply_masks();

    // Re-aplica as máscaras após o WooCommerce atualizar o checkout via AJAX (ex: ao mudar o CEP)
    $(document.body).on('updated_checkout', function () {
        apply_masks();
    });
});