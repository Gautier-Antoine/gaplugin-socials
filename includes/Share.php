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
            id="<?= esc_attr( $args['label_for'] ) ?>"
            name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][active]' ?>"
            title="<?php printf( __('Checkbox for %1$s', static::LANGUAGE), esc_attr( $args['label_for'] ) ) ?>"
            <?php ( isset( $args['active'] ) ) ? ' checked' : ''; ?>
          >
        </td><td>
          <input type="hidden" name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][label_for]' ?>" value="<?= esc_attr( $args['label_for'] ) ?>"></input>
        </td><td>
          <input type="hidden" name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][url]' ?>" value="<?= esc_url( $args['url'] ) ?>"></input>

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
              printf( esc_attr( $option['text'] ) );
            echo '</div>';
          }
        } else {
          if ( $option['active'] === true ) {
            $img = null;
            if ( isset( $option['imgurl'] ) ) {
              $img = esc_url( $option['imgurl'] ) . get_the_post_thumbnail_url( get_the_ID(),'full' );
            }
            $title = null;
            if ( isset( $option['titleurl'] ) ) {
              $title = esc_url( $option['titleurl'] ) . get_the_title();
            }
            echo '
              <a
                target="_blank"
                title="' . __( 'Share this on', static::LANGUAGE ) . ' ' . esc_attr( $option['label_for'] ) . '"
                href="' . esc_url( $option['url'] ) . get_permalink() . esc_url( $img ) . esc_url( $title ) . '"
              >
                <div class="' . strtolower( esc_attr( $option['label_for'] ) ) . '"></div>
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
          $title = static::PAGE . static::EXTENSION . '_' . strtolower( esc_attr( $option['label_for'] ) );
          add_settings_field(
            $title,
            esc_attr( $option['label_for'] ),
            [static::class, 'addPageFunction'],
            static::PAGE . static::EXTENSION, // Page
            static::PAGE . static::EXTENSION . '_section',
            [
              'label_for' => esc_attr( $option['label_for'] ),
              'url' => ($option['url']) ? esc_attr( $option['url'] ) : null,
              'active' => ($option['active']) ? esc_attr( $option['active'] ) : 0,
              'id' => esc_attr( $id ),
              'class' => strtolower( esc_attr( $option['label_for'] ) )
            ]
          );
        } else {
          $title = static::PAGE . static::EXTENSION . '_' . strtolower( esc_attr( $option['label_for'] ) );
          add_settings_field(
            $title,
            esc_attr( $option['label_for'] ),
            [static::class, 'showText'],
            static::PAGE . static::EXTENSION, // Page
            static::PAGE . static::EXTENSION . '_section',
            [
              'label_for' => esc_attr( $option['label_for'] ),
              'text' => ( $option['text'] ) ? esc_attr( $option['text'] ) : null,
              'id' => esc_attr( $id )
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
