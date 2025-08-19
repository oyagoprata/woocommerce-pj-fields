<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class DECPJ_Frontend_Fields {

    private $fields;
    private $field_order;

    public function __construct() {
        $this->fields = get_option( 'decpj_fields', [] );
        $this->field_order = get_option( 'decpj_field_order', [] );

        // Hooks para adicionar e salvar campos
        add_filter( 'woocommerce_billing_fields', [ $this, 'add_custom_billing_fields' ], 100 );
        add_action( 'woocommerce_register_form', [ $this, 'add_fields_to_registration_form' ] );
        add_action( 'woocommerce_created_customer', [ $this, 'save_registration_fields' ] );
        add_action( 'woocommerce_checkout_update_user_meta', [ $this, 'update_user_meta_from_checkout' ], 10, 2 );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        // Hooks para validação
        add_filter( 'woocommerce_registration_errors', [ $this, 'validate_custom_fields' ], 10, 3 );
        add_action( 'woocommerce_checkout_process', [ $this, 'validate_custom_fields_checkout' ] );

        // Hooks para exibição e exportação de dados
        add_action( 'woocommerce_admin_order_data_after_billing_address', [ $this, 'display_custom_fields_in_admin_order' ], 10, 1 );
        add_filter( 'woocommerce_customer_export_columns', [ $this, 'add_export_columns' ] );
        add_filter( 'woocommerce_customer_export_row_data', [ $this, 'add_export_row_data' ], 10, 2 );
    }

    public function enqueue_scripts() {
        if ( is_checkout() || is_account_page() || is_wc_endpoint_url( 'edit-account' ) ) {
            wp_enqueue_script( 'jquery-mask', plugin_dir_url( __DIR__ ) . 'assets/js/jquery.mask.min.js', ['jquery'], '1.14.16', true );
            wp_enqueue_script( 'decpj-public-scripts', plugin_dir_url( __DIR__ ) . 'assets/js/decpj-public-scripts.js', ['jquery', 'jquery-mask'], DECPJ_VERSION, true );
        }
    }

    private function get_active_fields() {
        $active_fields = [];
        foreach ($this->field_order as $field_id) {
            if (isset($this->fields[$field_id]) && !empty($this->fields[$field_id]['enabled']) && $this->fields[$field_id]['enabled'] === 'yes') {
                $active_fields[$field_id] = $this->fields[$field_id];
            }
        }
        return $active_fields;
    }

    public function add_custom_billing_fields( $fields ) {
        $custom_fields = $this->get_active_fields();
        if (empty($custom_fields)) return $fields;

        $priority = 35; 

        foreach ($custom_fields as $key => $settings) {
            if ($key === 'billing_company' && isset($fields['billing_company'])) {
                $fields['billing_company']['label'] = esc_html($settings['label']);
                $fields['billing_company']['placeholder'] = esc_html($settings['placeholder']);
                $fields['billing_company']['required'] = $settings['required'] === 'yes';
                $fields['billing_company']['class'] = [$settings['position']];
                $fields['billing_company']['priority'] = $priority;
            } else {
                 $fields[$key] = [
                    'label'       => esc_html($settings['label']),
                    'placeholder' => esc_html($settings['placeholder']),
                    'required'    => $settings['required'] === 'yes',
                    'class'       => [$settings['position']],
                    'clear'       => false,
                    'priority'    => $priority,
                ];
            }
            $priority += 10;
        }

        return $fields;
    }

    public function add_fields_to_registration_form() {
        $custom_fields = $this->get_active_fields();
        if (empty($custom_fields)) return;
        
        echo '<h2>' . esc_html__('Detalhes da Empresa', 'decpj') . '</h2>';
        foreach ($custom_fields as $key => $settings) {
            woocommerce_form_field(
                $key,
                [
                    'type'        => 'text',
                    'label'       => esc_html($settings['label']),
                    'placeholder' => esc_html($settings['placeholder']),
                    'required'    => $settings['required'] === 'yes',
                    'class'       => [$settings['position']],
                ],
                (isset($_POST[$key])) ? esc_attr(wp_unslash($_POST[$key])) : ''
            );
        }
    }

    public function validate_custom_fields( $errors, $username, $email ) {
        $active_fields = $this->get_active_fields();
        foreach ($active_fields as $key => $settings) {
            if (!empty($settings['required']) && $settings['required'] === 'yes' && empty($_POST[$key])) {
                $errors->add($key . '_error', sprintf(__('O campo <strong>%s</strong> é obrigatório.', 'decpj'), $settings['label']));
            }
            if ( !empty($settings['validate_cnpj']) && $settings['validate_cnpj'] === 'yes' && !empty($_POST[$key]) ) {
                if ( !$this->is_cnpj_valid( $_POST[$key] ) ) {
                     $errors->add($key . '_error', sprintf(__('O <strong>%s</strong> informado não é válido.', 'decpj'), $settings['label']));
                }
            }
        }
        return $errors;
    }

    public function validate_custom_fields_checkout() {
        $active_fields = $this->get_active_fields();
        foreach ($active_fields as $key => $settings) {
            if ( !empty($settings['validate_cnpj']) && $settings['validate_cnpj'] === 'yes' && !empty($_POST[$key]) ) {
                if ( !$this->is_cnpj_valid( $_POST[$key] ) ) {
                     wc_add_notice( sprintf(__('O <strong>%s</strong> informado não é válido.', 'decpj'), $settings['label']), 'error' );
                }
            }
        }
    }

    public function save_registration_fields( $customer_id ) {
        $custom_fields = $this->get_active_fields();
        foreach (array_keys($custom_fields) as $field_key) {
            if (isset($_POST[$field_key])) {
                update_user_meta($customer_id, $field_key, sanitize_text_field($_POST[$field_key]));
            }
        }
    }

    public function update_user_meta_from_checkout( $customer_id, $posted_data ) {
        $custom_fields = $this->get_active_fields();
        foreach (array_keys($custom_fields) as $key) {
             if (isset($posted_data[$key])) {
                update_user_meta( $customer_id, $key, sanitize_text_field( $posted_data[$key] ) );
            }
        }
    }

    public function display_custom_fields_in_admin_order($order) {
        $active_fields = $this->get_active_fields();
        $output = '';
        foreach ($active_fields as $key => $settings) {
            $value = $order->get_meta($key);
            if (!empty($value)) {
                $output .= '<p><strong>' . esc_html($settings['label']) . ':</strong> ' . esc_html($value) . '</p>';
            }
        }
        if (!empty($output)) {
            echo '<div><h4>' . __('Dados Adicionais PJ', 'decpj') . '</h4>' . $output . '</div>';
        }
    }

    public function add_export_columns($columns) {
        $active_fields = $this->get_active_fields();
        foreach ($active_fields as $key => $settings) {
            $columns[$key] = $settings['label'];
        }
        return $columns;
    }

    public function add_export_row_data($row, $customer) {
        $active_fields = $this->get_active_fields();
        foreach (array_keys($active_fields) as $key) {
            $row[$key] = get_user_meta($customer->get_id(), $key, true);
        }
        return $row;
    }

    private function is_cnpj_valid( $cnpj ) {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
        if (strlen($cnpj) != 14 || preg_match('/(\d)\1{13}/', $cnpj)) return false;
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) return false;
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
}