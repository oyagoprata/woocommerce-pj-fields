<?php
/**
 * Plugin Name:       Dados Extras para Cadastro PJ (BFPJ)
 * Plugin URI:        https://github.com/oyagoprata/woocommerce-pj-fields
 * Description:       Adiciona e valida campos personalizados para Pessoa Jurídica no registro e checkout do WooCommerce.
 * Version:           1.4.0
 * Author:            Yago Prata (MXR Studio)
 * Author URI:        https://mxrstudio.com.br
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       bfpj
 * Domain Path:       /languages
 * Requires at least: 6.5
 * Tested up to:      6.6
 * Requires PHP:      7.4
 * WC requires at least: 8.8
 * WC tested up to: 9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// INÍCIO - CÓDIGO PARA ATUALIZAÇÕES VIA GITHUB
require_once __DIR__ . '/lib/plugin-update-checker/plugin-update-checker.php';
try {
    $bfpjUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
        'https://github.com/oyagoprata/woocommerce-pj-fields/', // ATUALIZADO com seu repositório
        __FILE__,
        'bfpj-dados-extras'
    );
    // Define o branch principal como 'main', que é o padrão para novos repositórios.
    $bfpjUpdateChecker->setBranch('main');
} catch (Exception $e) {
    error_log('Erro ao inicializar o Plugin Update Checker para BFPJ: ' . $e->getMessage());
}
// FIM - CÓDIGO PARA ATUALIZAÇÕES VIA GITHUB


define( 'BFPJ_VERSION', '1.4.0' );
define( 'BFPJ_PLUGIN_FILE', __FILE__ );
define( 'BFPJ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

final class BFPJ_Dados_Extras {

    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        add_action( 'before_woocommerce_init', [ $this, 'declare_hpos_compatibility' ] );
        add_action( 'plugins_loaded', [ $this, 'init' ] );
        add_filter( 'plugin_action_links_' . plugin_basename( BFPJ_PLUGIN_FILE ), [ $this, 'add_settings_link' ] );
    }

    public function declare_hpos_compatibility() {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }

    public function init() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            add_action( 'admin_notices', [ $this, 'woocommerce_missing_notice' ] );
            return;
        }
        $this->includes();
        $this->update_check();
    }

    public function includes() {
        require_once BFPJ_PLUGIN_DIR . 'includes/class-bfpj-admin-settings.php';
        require_once BFPJ_PLUGIN_DIR . 'includes/class-bfpj-frontend-fields.php';
        new BFPJ_Admin_Settings();
        new BFPJ_Frontend_Fields();
    }
    
    public function add_settings_link( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=bfpj-settings' ) . '">' . __( 'Configurações', 'bfpj' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public function update_check() {
        $current_version = get_option( 'bfpj_version', '0' );
        if ( version_compare( BFPJ_VERSION, $current_version, '>' ) ) {
            $this->set_default_options();
            update_option( 'bfpj_version', BFPJ_VERSION );
        }
    }

    private function set_default_options() {
        if ( get_option( 'bfpj_fields' ) === false ) {
            $default_fields = [
                'billing_company' => [
                    'id'          => 'billing_company',
                    'label'       => 'Nome da Empresa',
                    'placeholder' => 'Digite o nome da sua empresa',
                    'enabled'     => 'yes',
                    'required'    => 'yes',
                    'position'    => 'form-row-wide'
                ],
                'billing_cnpj' => [
                    'id'          => 'billing_cnpj',
                    'label'       => 'CNPJ',
                    'placeholder' => '00.000.000/0000-00',
                    'enabled'     => 'yes',
                    'required'    => 'yes',
                    'position'    => 'form-row-first',
                    'validate_cnpj' => 'yes'
                ],
                'billing_ie' => [
                    'id'          => 'billing_ie',
                    'label'       => 'Inscrição Estadual (IE)',
                    'placeholder' => 'Digite a Inscrição Estadual',
                    'enabled'     => 'yes',
                    'required'    => 'no',
                    'position'    => 'form-row-last'
                ]
            ];
            update_option( 'bfpj_fields', $default_fields );
            update_option( 'bfpj_field_order', array_keys($default_fields) );
        }
    }

    public function woocommerce_missing_notice() {
        echo '<div class="error"><p>' .
            sprintf( __( 'O plugin "Dados Extras para Cadastro PJ" requer que o %sWooCommerce%s esteja ativo.', 'bfpj' ), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">', '</a>' ) .
            '</p></div>';
    }
}

BFPJ_Dados_Extras::instance();