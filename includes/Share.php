<?php
/**
 * @package GAP-Socials
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

        $checked = ( isset( $args['active'] ) && $args['active'] === '1' ) ? ' checked' : '';
        ?>
          <input
            type="checkbox"
            class="checkbox"
            id="<?= esc_attr( $args['label_for'] ) ?>"
            name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][active]' ?>"
            title="<?php printf( __('Checkbox for %1$s', static::LANGUAGE), esc_attr( $args['label_for'] ) ) ?>"
            <?= $checked ?>
          >
        </td><td>
          <input type="hidden" name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][label_for]' ?>" value="<?= esc_attr( $args['label_for'] ) ?>"></input>
        </td><td>
          <input type="hidden" name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][url]' ?>" value="<?= esc_url( $args['url'] ) ?>"></input>
        </td><td>
          <input type="hidden" name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][imgurl]' ?>" value="<?= esc_html( $args['imgurl'] ) ?>"></input>
        </td><td>
          <input type="hidden" name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][titleurl]' ?>" value="<?= esc_html( $args['titleurl'] ) ?>"></input>

        <?php
    }

    /**
     * Create ShortCode
     */
    public static function ShortcodeNav() {
      $option_name = static::getOptionName();
      $shortcode = '<div class="' . static::PAGE . '">';
      foreach ( get_option( $option_name ) as $id => $option ) {
        if ( $id === 'settings' ) {
          if (!empty( $option['text'] ) ) {
            $shortcode .= '<div class="' . static::PAGE . '-text">' .
              esc_attr( $option['text'] )
            . '</div>';
          }
        } else {
          if ( $option['active'] === true ) {
          // var_dump ($option);

            $img = null;
            if ( !empty( $option['imgurl'] ) && !empty( get_the_post_thumbnail_url( get_the_ID(), 'full' ) )) {
              $img =  $option['imgurl']  . get_the_post_thumbnail_url( get_the_ID(), 'full' );
            }
            $title = null;
            if ( !empty( $option['titleurl'] ) && !empty( get_the_title() ) ) {
              $title =  $option['titleurl']  . get_the_title();
            }
            $url = $option['url'] . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] .  $img  .  $title;

            $shortcode .= '
              <a
                target="_blank"
                title="' . __( 'Share this on', static::LANGUAGE ) . ' ' . esc_attr( $option['label_for'] ) . '"
                href="' . esc_url( $url ) . '"
              >
                <div class="' . strtolower( esc_attr( $option['label_for'] ) ) . '"></div>
              </a>';
          }
        }
      }
      $shortcode .= '</div>';
      return $shortcode;
    }

    /**
     * Create Fields for the admin page
     *
     * @param string $option_name
     */
    public static function getFields( $option_name ) {
      $options = ( get_option( $option_name ) ) ?: static::$list;
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
              'url' => ($option['url']) ? esc_url( $option['url'] ) : null,
              'imgurl' => (!empty($option['imgurl'])) ? esc_html( $option['imgurl'] ) : false,
              'titleurl' => ( !empty( $option['titleurl'] ) ) ? esc_html( $option['titleurl'] ) : null,
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
          $valid_input[$key]['imgurl'] = ( isset( $option['imgurl'] ) ) ? wp_filter_post_kses( $option['imgurl'] ) : false;
          $valid_input[$key]['titleurl'] = ( isset( $option['titleurl'] ) ) ? wp_filter_post_kses( $option['titleurl'] ) : false;
          $valid_input[$key]['active'] = ( isset( $option['active'] ) ) ? true : false;
        }
      }
      return $valid_input;
    }

}
