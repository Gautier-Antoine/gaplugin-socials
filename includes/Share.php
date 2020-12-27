<?php
namespace GAPlugin;
/**
* Class Share
* manage the social media where we can share the article in your Share ShortcodeNav
*/
class Share extends AdminPage {
  const
    /**
    * @var string name of the page
    */
    PAGE = 'share',
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
    * @var string name for the option
    */
    OPTION = 'gap_share';

    public static function getfolder(){
      return plugin_dir_url( __DIR__ );
    }
  /**
  * @var array names of the share social medias and urls
  */
  public static $list = [
    'settings' => ['label_for' => 'Text before', 'text' => ''],
    0 =>  ['label_for' => 'FaceBook', 'url' => 'https://www.facebook.com/sharer/sharer.php?u=', 'active' => 0],
    1 =>  ['label_for' => 'Twitter', 'url' => 'https://twitter.com/share?url=', 'active' => 0],
    2 =>  [
        'label_for' => 'Pinterest', 'url' => 'http://pinterest.com/pin/create/button/?url=',
        'imgurl' => '&amp;media=',
        'titleurl' => '&amp;description=',
        'active' => 0
      ],
    3 =>  ['label_for' => 'WhatsApp', 'url' => 'https://wa.me/?text=', 'active' => 0],
    4 =>  ['label_for' => 'Telegram', 'url' => 'https://t.me/share/url?url=', 'active' => 0],
    5 =>  ['label_for' => 'Email', 'url' => 'mailto:?body=', 'active' => 0]
      // CSS ready: insta,Map, Youtube, Twitch, linkedin, vimeo, github, WeChat, Tumblr, Viber, Snapchat, flipboard
  ];

  public static function registerSettingsText () {
    printf(
      __( 'Which social media do you want to share with your visitors', static::LANGUAGE ) .
      '<br>Shortcode = [' . static::PAGE . '-nav]'
    );
  }
  public static function addPageFunction( $args ) {
      $option_name = static::getOptionName();
      ?>
        <input
          type="checkbox"
          class="checkbox"
          name="<?= $option_name . '[' . $args['id'] . '][active]' ?>"
          title="<?php printf(__('Checkbox for %1$s', static::LANGUAGE), $args['label_for']) ?>"
          <?php if ($args['active']) {echo ' checked';} ?>
        >
        <input type="hidden" name="<?= $option_name . '[' . $args['id'] . '][label_for]' ?>" value="<?= $args['label_for'] ?>"></input>
        <input type="hidden" name="<?= $option_name . '[' . $args['id'] . '][url]' ?>" value="<?= $args['url'] ?>"></input>
      <?php
  }

  public static function showText( $args ) {
      $option_name = static::getOptionName();
      ?>
        <input
          type="textarea"
          name="<?= $option_name . '[' . $args['id'] . '][text]' ?>"
          class="textarea show-text"
          title="<?php printf(__('Checkbox for showing text', static::LANGUAGE)) ?>"
          value="<?= $args['text'] ?>"
        ></input>
        <input type="hidden" name="<?= $option_name . '[' . $args['id'] . '][label_for]' ?>" value="<?= $args['label_for'] ?>"></input>
      <?php
  }

  public static function ShortcodeNav() {
      $option_name = static::getOptionName();
      echo '<div class="' . static::PAGE . '">';
      foreach ( get_option( $option_name ) as $id => $option ) {
        if ( $id === 'settings' ) {
          if (!empty( $option['text'] ) ) {
            echo '<div class="' . static::PAGE . '-text">';
              printf( $option['text'] );
            echo '</div>';
          }
        } else {
          if ($option['active'] === true) {
            $img = null;
            if ( isset($option['imgurl']) ) {
              $img = $option['imgurl'] . get_the_post_thumbnail_url(get_the_ID(),'full');
            }
            $title = null;
            if ( isset($option['titleurl']) ) {
              $title = $option['titleurl'] . get_the_title();
            }
            echo '
              <a
                target="_blank"
                title="' . __( 'Share this on', static::LANGUAGE ) . ' ' . $option['label_for'] . '"
                href="' . $option['url'] . get_permalink() . $img . $title . '"
              >
                <div class="' . strtolower($option['label_for']) . '"></div>
              </a>';
          }
        }
      }
      echo '</div>';
  }


  public static function registerAdminScripts() {
      wp_register_style(static::FILE, static::getFolder() . 'includes/' . static::FILE . '.css');
      wp_register_style(static::FILE . '-admin', static::getFolder() . 'includes/' . static::FILE . '-admin.css', [static::FILE]);
      wp_enqueue_style(static::FILE . '-admin');

      wp_register_style('admin_form_ylc', plugin_dir_url( __FILE__ ) . 'admin_form_ylc.css' );
      wp_enqueue_style('admin_form_ylc');
      wp_enqueue_script( 'jquery-ui-sortable' );
      wp_register_script('admin_form_ylc_js', plugin_dir_url( __FILE__ ) . 'admin_form_ylc_js.js' );
      wp_enqueue_script('admin_form_ylc_js');
  }

  public static function registerSettings () {
      static::checkOptionsCreated();
      static::getExtraSettings();
      // Instead of click to hide text, type text see setting array
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
      $options = (get_option( $option_name )) ?: static::$list;
      foreach ( $options as $id => $option ) {
        if ($id !== 'settings') {
          $title = static::PAGE . static::EXTENSION . '_' . strtolower($option['label_for']);
          add_settings_field(
            $title,
            $option['label_for'],
            [static::class, 'addPageFunction'],
            static::PAGE . static::EXTENSION, // Page
            static::PAGE . static::EXTENSION . '_section',
            [
              'label_for' => $option['label_for'],
              'url' => ($option['url']) ?: null,
              'active' => ($option['active']) ?: 0,
              'id' => $id,
              'class' => strtolower($option['label_for'])
            ]
          );
        } else {
            $title = static::PAGE . static::EXTENSION . '_' . strtolower($option['label_for']);
            add_settings_field(
              $title,
              $option['label_for'],
              [static::class, 'showText'],
              static::PAGE . static::EXTENSION, // Page
              static::PAGE . static::EXTENSION . '_section',
              [
                'label_for' => $option['label_for'],
                'text' => ($option['text']) ?: null,
                'id' => $id
              ]
            );
        }
      }
    }

    /**
     * Sanitize POST data from custom settings form
     *
     * @param array $input Contains custom settings which are passed when saving the form
     */
    public function sanitize_list( $input ) {
      foreach ( $input as $key => $option ) {
        if ($key === 'settings') {
          $valid_input[$key]['label_for'] = sanitize_text_field( $option['label_for'] );
          $valid_input[$key]['text'] = sanitize_text_field( $option['text'] );
        } else {
          $valid_input[$key]['label_for'] = sanitize_text_field( $option['label_for'] );
          $valid_input[$key]['url'] = sanitize_url( $option['url'] );
          $valid_input[$key]['active'] = ( isset($option['active']) ) ? true : false;
        }
      }
      return $valid_input;
    }

    /**
     * Delete option in db
     */
    public static function removeOptions(){
      $option_name = static::getOptionName();
      delete_option( $option_name );
    }
    /**
     * Return the name of the option
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
