<?php
/**
 * Woocommerce Settings Addon setup
 *
 * @package Woocommerce Settings Addon
 * @since   1.0.0
 */
defined('ABSPATH') || exit;

/**
 * Main Woocommerce_Addon_Settings Class.
 */
class Woocommerce_Addon_Settings {

    /**
     * Woocommerce_Addon_Settings version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * The single instance of the class.
     *
     * @var Woocommerce_Addon_Settings
     */
    protected static $_instance = null;
    
     /**
     * Woocommerce_Addon_Settings Constructor.
     */
    public function __construct() {
//      To check Woocommerce is installed or not  
        register_activation_hook(WMA_PLUGIN_FILE, array('Woocommerce_Addon_Settings', 'activation_check'));
        add_action('admin_init', array($this, 'check_woocommerce_installed'));
        
        $this->init_hooks();
    }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {

        //this action callback is triggered when wordpress is ready to add new items to menu.
        add_action("admin_menu", array($this, "add_new_menu_items"));
        add_action("admin_init", array($this, "display_options"));
        
        
        

        add_filter('plugin_action_links_' . plugin_basename(WMA_PLUGIN_FILE), array($this, 'add_plugin_page_settings'));

        
    }

    /**
     * @return Woocommerce_Addon_Settings - Main instance.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /*
     * Check woocommerce is activate while installing plugin
     */
    public static function activation_check() {

        if (!self::is_woocommerce_installed()) {
            die('woocommerc is required. Please Install woocommerce.');
        }
    }

    /*
     * Check woocommerce activation while plugin already installed.
     */
    public function check_woocommerce_installed() {

        if (!self::is_woocommerce_installed()) {
            if (is_plugin_active(plugin_basename(WMA_PLUGIN_FILE))) {
                deactivate_plugins(plugin_basename(WMA_PLUGIN_FILE));
                add_action('admin_notices', array($this, 'add_notice_message'));
                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
            }
        }
    }
    
    /*
     * Admin notice when woocommerce is not installed.
     */
    public function add_notice_message() {
        echo 'woocommerc is required. Please Install woocommerce.';
    }

    /**
     * Checks if WooCommerce is installed and activated by looking at the 'active_plugins' array
     * @return bool True if WooCommerce is installed
     */
    public static function is_woocommerce_installed() {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        $slug = 'woocommerce/woocommerce.php';

        return is_plugin_active($slug) || is_plugin_active_for_network($slug);
    }

    /* 
     * Add WordPress Menus by using API settings.
     */
    public function add_new_menu_items() {
        
        add_menu_page(
                "Woocommerce Addon Settings",
                "Woocommerce Addon Settings",
                "manage_options",
                "woocommerce-addon-settings",
                array($this, "woocommerce_addon_settings_page"),
                "",
                30
        );
    }
    
    /*
     * add section in admin menu Woocommerce Addon Settings page.
     * add settings feild in section
     * register the settings
     */
    public function display_options() {


        add_settings_section("woocommerce_addon_settings_section", "Settings", '', "woocommerce-addon-settings");
        add_settings_field("woo_product_title_prefix", "Product Title Prefix", array($this, 'display_form_field'), "woocommerce-addon-settings", "woocommerce_addon_settings_section");

        register_setting("woocommerce_addon_settings_section", "woo_product_title_prefix");
    }
    
    /*
     * display form field in admin menu page
     */
    public function display_form_field() {

        echo '<input type="text" name="woo_product_title_prefix" id="woo_product_title_prefix" value="' . esc_attr( get_option('woo_product_title_prefix') ) . '" placeholder="Write prefix here" />';
        echo '<p class="description">Add a prefix to the single product page titles</p>';
    }
    


    
    
    /*
     * Add fields in meta box
     * 
     * $param object $post
     */
    public function add_meta_box_fields($post) {

        wp_nonce_field('meta_index_nonce', 'meta_index_nonce');

        $value = get_post_meta($post->ID, '_global_notice', true);
        $checked = ($value) ? 'checked' : '';

        echo '<input type="checkbox" ' . esc_attr( $checked ) . ' id="global_notice" name="global_notice" value="1"> NoIndex';
    }

   

    
    /*
     * Render setting in Woocommerce addon setting
     */
    public function woocommerce_addon_settings_page() {
        ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
                <?php
                //add_settings_section callback is displayed here. For every new section we need to call settings_fields.
                settings_fields("woocommerce_addon_settings_section");

                // all the add_settings_field callbacks is displayed here
                do_settings_sections("woocommerce-addon-settings");

                // Add the submit button to serialize the options
                submit_button();
                ?>          
            </form>
        </div>
        <?php
    }
    
    /*
     * Add setting link on plugin
     * 
     * @param array $links
     * return $links
     */
    public function add_plugin_page_settings($links) {
        $links[] = '<a href="' .
                admin_url('admin.php?page=woocommerce-addon-settings') .
                '">' . __('Settings') . '</a>';
        return $links;
    }

}
