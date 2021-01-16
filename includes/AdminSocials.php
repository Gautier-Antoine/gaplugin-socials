<?php
/**
 * @package Socials-GA
 */
namespace GAPlugin;
/**
* Class AdminPage
* create a AdminPage
*/
class AdminSocials {

  const
    /**
    * @var string name of the page
    */
    PAGE = '',
    /**
    * @var string name for the option
    */
    OPTION = '',

    /**
    * @var string name of the language file
    */
    LANGUAGE = 'share-socials-text',
    /**
    * @var string name for the files
    */
    FILE = 'share-socials',
    /**
    * @var string name for the plugin folder
    */
    FOLDER = 'gaplugin-socials',

    /**
    * @var string name for the menu
    */
    MENU = 'GAPlugin',
    /**
    * @var string name for the extension title
    */
    EXTENSION = '_menu',
    /**
    * @var string name for the admin page
    */
    ADMINPAGE = 'ga-admin-page';

  /**
  * @var array list of social medias
  */
  public static $list = [];

  /**
   * Text to display with the settings section
   */
  public static function registerSettingsText () {}

  /**
  * Create Admin Page Functions for each field
  * @param array $args list from registerSettings()
  */
  public static function addPageFunction($args) {}

  /**
   * Create ShortCode
   */
  public static function ShortcodeNav() {}

  /**
   * Scripts to register on all pages
   */
  public static function registerPublicScripts () {
    // wp_register_style(static::FILE);
    wp_enqueue_style(static::FILE, static::getFolder() . 'includes/' . static::FILE . '.css');
  }

  /**
  * Check if on Admin page before registering the scripts
  * @param string $suffix is settings_page
  */
  public static function AdminScripts($suffix) {
      if ($suffix === (strtolower(static::MENU) . '_page_' . static::ADMINPAGE . '-' . static::PAGE)) {
        static::registerAdminScripts();
      }
  }

  /**
   * Scripts to register on admin pages
   */
  public static function registerAdminScripts() {
      wp_register_style(static::FILE, static::getFolder() . 'includes/' . static::FILE . '.css');
      wp_register_style(static::FILE . '-admin', static::getFolder() . 'includes/' . static::FILE . '-admin.css', [static::FILE]);
      wp_enqueue_style(static::FILE . '-admin');

      wp_enqueue_script( 'jquery-ui-sortable' );
      wp_register_script('admin-sort', plugin_dir_url( __FILE__ ) . 'admin-sort.js' );
      wp_enqueue_script('admin-sort');
  }

  /**
   * Start all the actions
   */
  public static function register () {
      add_action('wp_enqueue_scripts', [static::class, 'registerPublicScripts']);
      add_action('admin_enqueue_scripts', [static::class, 'AdminScripts']);
      add_action('admin_init', [static::class, 'registerSettings']);
      add_action('admin_menu', [static::class, 'addMenu']);
      if ( ! is_admin() ) {
        add_shortcode('GAP-' . static::PAGE, [static::class, 'ShortcodeNav']);
      }
      load_plugin_textdomain(static::LANGUAGE, false, static::FOLDER . '/languages/' );
  }

  /**
   * Create Page Architecture
   */
  public static function addMenu () {
    if ( empty ( $GLOBALS['admin_page_hooks'][static::ADMINPAGE] ) ){
        add_menu_page(
            'GAPlugins',
            static::MENU,
            'manage_options',
            static::ADMINPAGE,
            [static::class,'GAPlugin_admin_page'],
            'dashicons-share',
            30
        );
    }
    add_submenu_page(
      static::ADMINPAGE,
      ucfirst(static::PAGE),
      ucfirst(static::PAGE),
      'manage_options',
      static::ADMINPAGE . '-' . static::PAGE,
      [static::class, 'render']
    );
  }

  /**
   * To show on the admin Page
   */
  public static function render () {
      ?>
      <h1><?= _e('Navigation ', static::LANGUAGE) . ucfirst(static::PAGE) ?></h1>
      <form class="<?= static::PAGE . '-admin' ?>" action="options.php" method="post">
          <?php settings_fields(static::PAGE . static::EXTENSION);
          do_settings_sections(static::PAGE . static::EXTENSION);
          submit_button();
          ?>
      </form>
      <?php
  }

  /**
   * Text to display on the first page
   */
  public static function GAPlugin_admin_page(){
    ?>
    <div class="wrap">
      <h2><?=
       __('Welcome to GAPlugin Page', static::LANGUAGE) . '<h2>
         <p>' .
         __('You\'ll find the different sections in the tabs', static::LANGUAGE) . '</p><br />';
        ?>
    </div>
    <?php
  }

  /**
   * Create and manage the settings page
   */
  public static function registerSettings () {
      static::checkOptionsCreated();
      $option_name = static::getOptionName();
      register_setting(
          static::PAGE . static::EXTENSION, // Option group
          $option_name, // Option name
          array( static::class, 'sanitize_list' ) // Sanitize
      );
      add_settings_section(
        static::PAGE . static::EXTENSION . '_section', // ID
        __( 'Parameters', static::LANGUAGE ), // Title
        [static::class, 'registerSettingsText'], // Callback
        static::PAGE . static::EXTENSION // Page
      );
      static::getFields( $option_name );
    }

    /**
     * Create Fields for the admin page
     * @param string $option_name
     */
    public static function getFields( $option_name ) {}

    /**
     * GetFolder URL
     */
    public static function getFolder() {
      return plugin_dir_url( __DIR__ );
    }

    /**
     * ADMIN > show text
     */
    public static function showText( $args ) {
        $option_name = static::getOptionName();
        ?>
          <input
            type="textarea"
            id="<?= esc_attr( $args['label_for'] ) ?>"
            name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][text]' ?>"
            class="textarea show-text"
            title="<?php printf(__('Add some text before the links', static::LANGUAGE)) ?>"
            value="<?= esc_attr( $args['text'] ) ?>"
          ></input>
          <input type="hidden" name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][label_for]' ?>" value="<?= esc_attr( $args['label_for'] ) ?>"></input>
        <?php
    }

    /**
     * Delete option in db
     */
    public static function removeOptions(){
      $option_name = static::getOptionName();
      delete_option( $option_name );
    }
    /**
     * Activate plugin
     */
    public static function Activate() {
      static::checkOptionsCreated();
      flush_rewrite_rules();
    }
    /**
     * Deactivate plugin
     */
    public static function Deactivate() {
      flush_rewrite_rules();
    }
    /**
     * Return the Name of the option
     */
    protected static function getOptionName() {
      if (!is_multisite()){
        $option_name = static::OPTION;
      } else {
        $option_name = static::OPTION . '_' . get_current_blog_id();
      }
      return $option_name;
    }

    /**
     * Checking if multisite and creating option
     */
    protected static function checkOptionsCreated() {
      if (!is_multisite()){
        if (empty(get_option( static::OPTION ))) {
          add_option( static::OPTION, static::$list);
        }
      } else {
        global $wpdb;
        $blogs = $wpdb->get_results("
          SELECT blog_id
          FROM {$wpdb->blogs}
          WHERE site_id = '{$wpdb->siteid}'
          AND spam = '0'
          AND deleted = '0'
          AND archived = '0'
        ");
        $original_blog_id = get_current_blog_id();
        foreach ( $blogs as $blog_id ) {
          $id = $blog_id->blog_id;
          switch_to_blog( $id );
          if (empty(get_option( static::OPTION . '_' . $id ))) {
            add_option( static::OPTION . '_' . $id, static::$list);
          }
        }
        switch_to_blog( $original_blog_id );
      }
    }

}
