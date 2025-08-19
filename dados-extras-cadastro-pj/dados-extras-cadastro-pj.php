<?php
/**
 * Plugin Name:       Dados Extras para Cadastro PJ
 * Plugin URI:        https://mxrstudio.com.br/plugins
 * Description:       Adiciona e valida campos personalizados para Pessoa Jurídica no registro e checkout do WooCommerce.
 * Version:           1.5.0
 * Author:            Yago Prata (MXR Studio)
 * Author URI:        https://mxrstudio.com.br
 * License:           GPLv2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       decpj
 * Domain Path:       /languages
 * Requires at least: 6.5
 * Tested up to:      6.6
 * Requires PHP:      7.4
 * WC requires at least: 8.8
 * WC tested up to: 9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DECPJ_VERSION', '1.5.0' );
define( 'DECPJ_PLUGIN_FILE', __FILE__ );
define( 'DECPJ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

final class DECPJ_Dados_Extras {

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
        add_filter( 'plugin_action_links_' . plugin_basename( DECPJ_PLUGIN_FILE ), [ $this, 'add_settings_link' ] );
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
        require_once DECPJ_PLUGIN_DIR . 'includes/class-decpj-admin-settings.php';
        require_once DECPJ_PLUGIN_DIR . 'includes/class-decpj-frontend-fields.php';
        new DECPJ_Admin_Settings();
        new DECPJ_Frontend_Fields();
    }
    
    public function add_settings_link( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=decpj-settings' ) . '">' . __( 'Configurações', 'decpj' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public function update_check() {
        $current_version = get_option( 'decpj_version', '0' );
        if ( version_compare( DECPJ_VERSION, $current_version, '>' ) ) {
            $this->set_default_options();
            update_option( 'decpj_version', DECPJ_VERSION );
        }
    }

    private function set_default_options() {
        if ( get_option( 'decpj_fields' ) === false ) {
            $default_fields = [
                'billing_company' => [ 'id' => 'billing_company', 'label' => 'Nome da Empresa', 'placeholder' => 'Digite o nome da sua empresa', 'enabled' => 'yes', 'required' => 'yes', 'position' => 'form-row-wide' ],
                'billing_cnpj' => [ 'id' => 'billing_cnpj', 'label' => 'CNPJ', 'placeholder' => '00.000.000/0000-00', 'enabled' => 'yes', 'required' => 'yes', 'position' => 'form-row-first', 'validate_cnpj' => 'yes' ],
                'billing_ie' => [ 'id' => 'billing_ie', 'label' => 'Inscrição Estadual (IE)', 'placeholder' => 'Digite a Inscrição Estadual', 'enabled' => 'yes', 'required' => 'no', 'position' => 'form-row-last' ]
            ];
            update_option( 'decpj_fields', $default_fields );
            update_option( 'decpj_field_order', array_keys($default_fields) );
        }
    }

    public function woocommerce_missing_notice() {
        echo '<div class="error"><p>' . sprintf( __( 'O plugin "Dados Extras para Cadastro PJ" requer que o %sWooCommerce%s esteja ativo.', 'decpj' ), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">', '</a>' ) . '</p></div>';
    }
}

DECPJ_Dados_Extras::instance();