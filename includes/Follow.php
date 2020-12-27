<?php
namespace GAPlugin;
/**
* Class Follow
* manage the social media in your Follow ShortcodeNav
*/
class Follow extends AdminPage{
    const
      /**
      * @var string name of the page
      */
      PAGE = 'follow',
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
      OPTION = 'gap_follow';

    public static function getfolder(){
      return plugin_dir_url( __DIR__ );
    }
    /**
    * @var array list of social medias
    */
    public static $list = [
      'settings' => ['label_for' => 'Text before', 'text' => null ],
      0 => ['label_for' => 'FaceBook', 'url' => false ],
      1 => ['label_for' => 'Instagram', 'url' => false ],
      2 => ['label_for' => 'SnapChat', 'url' => false ],
      3 => ['label_for' => 'Twitter', 'url' => false ],
      4 => ['label_for' => 'LinkedIn', 'url' => false ],
      5 => ['label_for' => 'Viadeo', 'url' => false ],
      6 => ['label_for' => 'Pinterest', 'url' => false ],
      7 => ['label_for' => 'Tumblr', 'url' => false ],
      8 => ['label_for' => 'FlipBoard', 'url' => false ],
      9 => ['label_for' => 'Flickr', 'url' => false ],
      10 => ['label_for' =>'Skype', 'url' => false ],
      11 => ['label_for' =>'WhatsApp', 'url' => false ],
      12 => ['label_for' => 'Telegram', 'url' => false ],
      13 => ['label_for' => 'Viber', 'url' => false ],
      14 => ['label_for' => 'WeChat', 'url' => false ],
      15 => ['label_for' => 'Map', 'url' => false ],
      16 => ['label_for' => 'Email', 'url' => false ],
      17 => ['label_for' => 'Phone', 'url' => false ],
      18 => ['label_for' => 'DeviantArt', 'url' => false ],
      19 => ['label_for' => 'Discord', 'url' => false ],
      20 => ['label_for' => 'GitHub', 'url' => false ],
      21 => ['label_for' => 'Twitch', 'url' => false ],
      22 => ['label_for' => 'YouTube', 'url' => false ],
      23 => ['label_for' => 'Vimeo', 'url' => false ]
    ];

    public static function registerSettingsText () {
      printf(
        __( 'Which social media do you want to show to your visitors', static::LANGUAGE) . '<br>' .
        __('Put the link to your social media to activate', static::LANGUAGE) .
        '<br>Shortcode = [' . static::PAGE . '-nav]'
      );
    }

    public static function addPageFunction( $args ) {
        $option_name = static::getOptionName();
        // cols="30"
        ?>
          <textarea
            name="<?= $option_name . '[' . $args['id'] . '][url]' ?>"
            rows= "1"
            title="<?php printf(__('Put your %1$s URL', static::LANGUAGE), $args['label_for']) ?>"
          ><?=
            esc_html( $args['url'] );
          ?></textarea>
          <input type="hidden" name="<?= $option_name . '[' . $args['id'] . '][label_for]' ?>" value="<?= $args['label_for'] ?>"></input>
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
              if ($option['label_for'] === 'Email') {
                $link = 'mailto:';
              } elseif ($option['label_for'] === 'Phone') {
                $link = 'tel:';
              } else {
                $link = '';
              }
              if ( !empty ( $option['url'] ) ) {
                echo '
                  <a
                    target="_blank"
                    title="' . __( 'Link to', static::LANGUAGE ) . ' ' . $option['label_for'] . '"
                    href="' . $link . esc_html( $option['url'] ) . '"
                  >
                    <div class="' . strtolower($option['label_for']) . '"></div>
                  </a>
                ';
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
      foreach ( $input as $id => $option ) {
        $valid_input[$id]['label_for'] = sanitize_text_field( $option['label_for'] );
        if ($option['label_for'] === 'Email') {
          $valid_input[$id]['url'] = ( !empty($option['url']) ) ? sanitize_email( $option['url'] ) : false;
        } elseif ($option['label_for'] === 'Phone') {
          $valid_input[$id]['url'] = ( !empty($option['url']) ) ? sanitize_text_field( $option['url'] ) : false;
        } elseif ($option['label_for'] === 'Text before') {
          $valid_input[$id]['text'] = ( !empty($option['text']) ) ? sanitize_text_field( $option['text'] ) : false;
        } else {
          $valid_input[$id]['url'] = ( !empty($option['url']) ) ? sanitize_url( $option['url'] ) : false;
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
