<?php
/**
 * @package Socials-GA
 */
namespace GAPlugin;
/**
* Class Share
* manage the social media where we can share the article in your Share ShortcodeNav
*/
class Share extends AdminSocials {

    const
      /**
      * @var string name of the page
      */
      PAGE = 'share',
      /**
      * @var string name for the option
      */
      OPTION = 'gap_share';

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

    /**
     * Text to display with the settings section
     */
    public static function registerSettingsText () {
      printf(
        __( 'Which social media do you want to share with your visitors', static::LANGUAGE ) . '<br>' .
        __( 'You can reorder them too', static::LANGUAGE ) .
        '<br>Shortcode = [GAP-' . static::PAGE . ']'
      );
    }

    /**
    * Create Admin Page Functions for each field
    * @param array $args list from registerSettings()
    */
    public static function addPageFunction( $args ) {
        $option_name = static::getOptionName();
        ?>
          <input
            type="checkbox"
            class="checkbox"
            id="<?= $args['label_for'] ?>"
            name="<?= $option_name . '[' . $args['id'] . '][active]' ?>"
            title="<?php printf(__('Checkbox for %1$s', static::LANGUAGE), $args['label_for']) ?>"
            <?php if ($args['active']) {echo ' checked';} ?>
          >
        </td><td>
          <input type="hidden" name="<?= $option_name . '[' . $args['id'] . '][label_for]' ?>" value="<?= $args['label_for'] ?>"></input>
        </td><td>
          <input type="hidden" name="<?= $option_name . '[' . $args['id'] . '][url]' ?>" value="<?= $args['url'] ?>"></input>

        <?php
    }

    /**
     * Create ShortCode
     */
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

    /**
     * Create Fields for the admin page
     *
     * @param string $option_name
     */
    public static function getFields( $option_name ) {
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

}
