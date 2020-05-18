<?php
namespace GAPlugin;
/**
* Class AdminPage
* create a AdminPage
*/
class AdminPage {

  const
    /**
    * @var string name of the page
    */
    PAGE = '',
    /**
    * @var string name for the language files
    */
    LANGUAGE = '',
    /**
    * @var string name for the files
    */
    FILE = '',
    /**
    * @var string name for the plugin folder
    */
    FOLDER = '',


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

  public static function getfolder(){
    return plugin_dir_url( __DIR__ );
  }
  /**
  * @var array list of social medias
  */
  public static $list =[];
  public static function registerSettingsText () {}
  /**
  * @param array $args list from registerSettings()
  */
  public static function addPageFunction($args) {}
  public static function ShortcodeNav() {}
  public static function getExtraSettings () {}
  public static function removeExtraOptions() {}
  public static function removeOptions(){
    foreach (static::$list as $option) {
      delete_option(static::PAGE . '-' . strtolower($option['name']));
    }
    static::removeExtraOptions();
  }
  public static function registerPublicScripts () {
    wp_register_style(static::FILE, static::getFolder() . 'includes/' . static::FILE . '.css');
    wp_enqueue_style(static::FILE);
  }
  public static function registerAdminScripts() {
          wp_register_style(static::FILE, static::getFolder() . 'includes/' . static::FILE . '.css');
          wp_register_style(static::FILE . '-admin', static::getFolder() . 'includes/' . static::FILE . '-admin.css', [static::FILE]);
          wp_enqueue_style(static::FILE . '-admin');
  }


    public static function register () {
        add_action('wp_enqueue_scripts', [static::class, 'registerPublicScripts']);
        add_action('admin_enqueue_scripts', [static::class, 'AdminScripts']);
        add_action('admin_init', [static::class, 'registerSettings']);
        add_action('admin_menu', [static::class, 'addMenu']);
        add_shortcode(static::PAGE . '-nav', [static::class, 'ShortcodeNav']);
        load_plugin_textdomain(static::LANGUAGE, false, static::FOLDER . '/languages/' );
    }
    /**
    * @param string $suffix is settings_page
    */
    public static function AdminScripts($suffix) {
        if ($suffix === (strtolower(static::MENU) . '_page_' . static::ADMINPAGE . '-' . static::PAGE)) {
            static::registerAdminScripts();
            }
    }
    public static function registerSettings () {
          add_settings_section(
            static::PAGE . static::EXTENSION . '_section',
            __( 'Parameters', static::LANGUAGE ),
            [static::class, 'registerSettingsText'],
            static::PAGE . static::EXTENSION
          );
          static::getExtraSettings();
          foreach (static::$list as $name){
              $class = strtolower($name['name']);
              $title = static::PAGE . static::EXTENSION . '_' . $class;
              register_setting(
                static::PAGE . static::EXTENSION,
                static::PAGE . '-' . $class
              );
              add_settings_field(
                $title,
                $name['name'],
                [static::class, 'addPageFunction'],
                static::PAGE . static::EXTENSION,
                static::PAGE . static::EXTENSION . '_section',
                ['class' => $class]
              );
          }
    }
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
    public static function render () {
        ?>
        <h1><?= _e('Navigation ', static::LANGUAGE) . ucfirst(static::PAGE) ?></h1>
        <form action="options.php" method="post">
            <?php settings_fields(static::PAGE . static::EXTENSION);
            do_settings_sections(static::PAGE . static::EXTENSION);
            submit_button();
            ?>
        </form>
        <?php
    }
}
