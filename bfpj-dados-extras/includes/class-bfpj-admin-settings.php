<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class BFPJ_Admin_Settings {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_plugin_page' ] );
        add_action( 'admin_init', [ $this, 'save_settings' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    public function enqueue_admin_assets( $hook ) {
        if ( 'woocommerce_page_bfpj-settings' !== $hook ) {
            return;
        }
        wp_enqueue_style( 'bfpj-admin-styles', plugin_dir_url( __DIR__ ) . 'assets/css/bfpj-admin-styles.css', [], BFPJ_VERSION );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'bfpj-admin-scripts', plugin_dir_url( __DIR__ ) . 'assets/js/bfpj-admin-scripts.js', [ 'jquery', 'jquery-ui-sortable' ], BFPJ_VERSION, true );
    }

    public function add_plugin_page() {
        add_submenu_page(
            'woocommerce', 'Campos de Cadastro PJ', 'Campos de Cadastro PJ',
            'manage_woocommerce', 'bfpj-settings', [ $this, 'create_admin_page' ]
        );
    }

    public function create_admin_page() {
        $fields = get_option( 'bfpj_fields', [] );
        $field_order = get_option( 'bfpj_field_order', [] );

        $sorted_fields = [];
        if (!empty($field_order)) {
            foreach ( $field_order as $field_id ) {
                if ( isset( $fields[ $field_id ] ) ) {
                    $sorted_fields[ $field_id ] = $fields[ $field_id ];
                }
            }
        }
        $sorted_fields += $fields;

        ?>
        <div class="wrap bfpj-admin-wrap">
            <h1>Configurações dos Campos de Cadastro PJ</h1>
            <p class="bfpj-subtitle">Adicione, remova e reordene os campos que aparecerão no cadastro e checkout para clientes PJ.</p>
            
            <?php if ( isset( $_GET['settings-updated'] ) ) : ?>
                <div id="message" class="updated notice is-dismissible"><p><strong>Configurações salvas com sucesso!</strong></p></div>
            <?php endif; ?>

            <form method="post" action="admin-post.php">
                <input type="hidden" name="action" value="bfpj_save_settings">
                <?php wp_nonce_field( 'bfpj_save_settings_nonce' ); ?>

                <div id="bfpj-fields-container" class="bfpj-fields-container">
                    <?php
                    if ( ! empty( $sorted_fields ) ) {
                        foreach ( $sorted_fields as $field_id => $field_data ) {
                            $this->render_field_card( $field_id, $field_data );
                        }
                    } else {
                        echo '<p class="bfpj-no-fields">Nenhum campo configurado. Clique em "Adicionar Novo Campo" para começar.</p>';
                    }
                    ?>
                </div>

                <div class="bfpj-actions">
                    <button type="button" id="bfpj-add-field" class="button button-secondary">
                        <span class="dashicons dashicons-plus-alt"></span> Adicionar Novo Campo
                    </button>
                    <?php submit_button('Salvar Alterações'); ?>
                </div>

                <div id="bfpj-field-template" style="display: none;">
                    <?php $this->render_field_card( 'bfpj_new_field_{index}', [] ); ?>
                </div>
            </form>
        </div>
        <?php
    }
    
    private function render_field_card( $id, $data ) {
        $label         = $data['label'] ?? '';
        $placeholder   = $data['placeholder'] ?? '';
        $enabled       = $data['enabled'] ?? 'yes';
        $required      = $data['required'] ?? 'no';
        $position      = $data['position'] ?? 'form-row-wide';
        $validate_cnpj = $data['validate_cnpj'] ?? 'no';
        $is_template   = strpos( $id, '{index}' ) !== false;
        ?>
        <div class="bfpj-card" data-id="<?php echo esc_attr( $id ); ?>">
            <div class="bfpj-card-header">
                <span class="bfpj-drag-handle dashicons dashicons-move"></span>
                <h3 class="bfpj-card-title"><?php echo esc_html( $label ?: 'Novo Campo' ); ?></h3>
                <div class="bfpj-card-actions">
                    <a href="#" class="bfpj-remove-field">Remover</a>
                    <span class="bfpj-toggle-icon dashicons dashicons-arrow-down-alt2"></span>
                </div>
            </div>
            <div class="bfpj-card-body">
                <input type="hidden" class="bfpj-field-id" name="bfpj_fields[<?php echo esc_attr($id); ?>][id]" value="<?php echo esc_attr($id); ?>">
                <table class="form-table">
                     <tr>
                        <th>Ativar / Obrigatório</th>
                        <td>
                            <label class="bfpj-switch">
                                <input type="checkbox" name="bfpj_fields[<?php echo esc_attr($id); ?>][enabled]" value="yes" <?php checked( $enabled, 'yes' ); ?>>
                                <span class="bfpj-slider"></span>
                            </label> Ativar Campo
                            <label class="bfpj-switch bfpj-required-switch">
                                <input type="checkbox" name="bfpj_fields[<?php echo esc_attr($id); ?>][required]" value="yes" <?php checked( $required, 'yes' ); ?>>
                                <span class="bfpj-slider"></span>
                            </label> Campo Obrigatório
                        </td>
                    </tr>
                    <tr>
                        <th><label for="label_<?php echo esc_attr($id); ?>">Rótulo (Label)</label></th>
                        <td><input type="text" id="label_<?php echo esc_attr($id); ?>" name="bfpj_fields[<?php echo esc_attr($id); ?>][label]" value="<?php echo esc_attr( $label ); ?>" class="regular-text bfpj-label-input" required></td>
                    </tr>
                    <tr>
                        <th><label for="meta_key_<?php echo esc_attr($id); ?>">Meta Key (ID do Campo)</label></th>
                        <td>
                            <input type="text" id="meta_key_<?php echo esc_attr($id); ?>" name="bfpj_fields[<?php echo esc_attr($id); ?>][meta_key]" value="<?php echo esc_attr( $id ); ?>" class="regular-text bfpj-meta-key-input" <?php if ( ! $is_template ) echo 'readonly'; ?>>
                            <p class="description"><?php echo $is_template ? 'Use letras minúsculas, números e underlines. Ex: <code>billing_outro_documento</code>. Após salvar, não poderá ser alterado.' : 'O ID do campo não pode ser alterado após a criação.'; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="placeholder_<?php echo esc_attr($id); ?>">Placeholder</label></th>
                        <td><input type="text" id="placeholder_<?php echo esc_attr($id); ?>" name="bfpj_fields[<?php echo esc_attr($id); ?>][placeholder]" value="<?php echo esc_attr( $placeholder ); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label for="position_<?php echo esc_attr($id); ?>">Posição / Largura</label></th>
                        <td>
                            <select id="position_<?php echo esc_attr($id); ?>" name="bfpj_fields[<?php echo esc_attr($id); ?>][position]">
                                <option value="form-row-wide" <?php selected( $position, 'form-row-wide' ); ?>>Largo (Largura total)</option>
                                <option value="form-row-first" <?php selected( $position, 'form-row-first' ); ?>>Esquerda (Metade)</option>
                                <option value="form-row-last" <?php selected( $position, 'form-row-last' ); ?>>Direita (Metade)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Validação</th>
                        <td>
                            <label>
                                <input type="checkbox" name="bfpj_fields[<?php echo esc_attr($id); ?>][validate_cnpj]" value="yes" <?php checked( $validate_cnpj, 'yes' ); ?>>
                                Validar este campo como um CNPJ brasileiro.
                            </label>
                            <p class="description">Impede o envio de formulários com CNPJs matematicamente inválidos.</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }
    
    public function save_settings() {
        if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'bfpj_save_settings' || ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'bfpj_save_settings_nonce' ) || ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( 'Acesso negado ou falha de segurança.' );
        }

        $fields = $_POST['bfpj_fields'] ?? [];
        $sanitized_fields = [];
        $field_order = [];

        foreach ( $fields as $temp_id => $field_data ) {
            $meta_key = sanitize_key( $field_data['meta_key'] );
            if ( empty( $meta_key ) ) continue;
            if ( strpos($meta_key, 'billing_') !== 0 ) {
                $meta_key = 'billing_' . $meta_key;
            }
            
            $sanitized_fields[$meta_key]['id']            = $meta_key;
            $sanitized_fields[$meta_key]['label']         = sanitize_text_field( $field_data['label'] );
            $sanitized_fields[$meta_key]['placeholder']   = sanitize_text_field( $field_data['placeholder'] );
            $sanitized_fields[$meta_key]['enabled']       = isset( $field_data['enabled'] ) ? 'yes' : 'no';
            $sanitized_fields[$meta_key]['required']      = isset( $field_data['required'] ) ? 'yes' : 'no';
            $sanitized_fields[$meta_key]['position']      = sanitize_text_field( $field_data['position'] );
            $sanitized_fields[$meta_key]['validate_cnpj'] = isset( $field_data['validate_cnpj'] ) ? 'yes' : 'no';
            
            $field_order[] = $meta_key;
        }

        update_option( 'bfpj_fields', $sanitized_fields );
        update_option( 'bfpj_field_order', $field_order );
        
        wp_redirect( admin_url( 'admin.php?page=bfpj-settings&settings-updated=true' ) );
        exit;
    }
}