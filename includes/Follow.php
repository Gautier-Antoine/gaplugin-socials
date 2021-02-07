<?php
/**
 * @package GAP-Socials
 */
namespace GAPlugin;
/**
* Class Follow
* manage the social media in your Follow ShortcodeNav
*/
class Follow extends AdminSocials {

    const
      /**
      * @var string name of the page
      */
      PAGE = 'follow',

      /**
      * @var string name for the option
      */
      OPTION = 'gap_follow';

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

    /**
     * Text to display with the settings section
     */
    public static function registerSettingsText () {
      printf(
        __( 'Which social media do you want to show to your visitors', static::LANGUAGE) . '<br>' .
        __('Put the link to your social media to activate', static::LANGUAGE) . '<br>' .
        __('You can reorder them too', static::LANGUAGE) .
        '<br>Shortcode = [GAP-' . static::PAGE . ']'
      );
    }

    /**
    * Create Admin Page Functions for each field
    * @param array $args list from registerSettings()
    */
    public static function addPageFunction( $args ) {
        $option_name = static::getOptionName();
        // cols="30"
        ?>
          <textarea
            name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][url]' ?>"
            id="<?= esc_attr( $args['label_for'] ) ?>"
            rows= "1"
            title="<?php printf( __('Put your %1$s URL', static::LANGUAGE), esc_attr( $args['label_for'] ) ) ?>"
          ><?php
            if ($args['label_for'] == 'Email' || $args['label_for'] == 'Phone') {
              echo esc_attr( $args['url'] );
            } else {
              echo esc_url( $args['url'] );
            }
          ?></textarea>
          <input type="hidden" name="<?= esc_attr( $option_name ) . '[' . esc_attr( $args['id'] ) . '][label_for]' ?>" value="<?= esc_attr( $args['label_for'] ) ?>"></input>
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
              $shortcode .=  '<div class="' . static::PAGE . '-text">' .
                  esc_attr( $option['text'] )
              . '</div>';
              }
            } else {
              if ( !empty ( $option['url'] ) ) {
                if ($option['label_for'] === 'Email') {
                  $link = 'mailto:' . esc_attr( $option['url'] );
                } elseif ($option['label_for'] === 'Phone') {
                  $link = 'tel:' . esc_attr( $option['url'] );
                } else {
                  $link = esc_url( $option['url'] );
                }

                $shortcode .= '
                  <a
                    target="_blank"
                    title="' . __( 'Link to', static::LANGUAGE ) . ' ' . esc_attr( $option['label_for'] ) . '"
                    href="' . $link . '"
                  >
                    <div class="' . strtolower( esc_attr( $option['label_for'] ) ) . '"></div>
                  </a>
                ';
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
      $options = (get_option( $option_name )) ?: static::$list;
      foreach ( $options as $id => $option ) {
        if ( $id !== 'settings' ) {
          $title = static::PAGE . static::EXTENSION . '_' . strtolower( $option['label_for'] );
          add_settings_field(
            $title,
            esc_attr( $option['label_for'] ),
            [static::class, 'addPageFunction'],
            static::PAGE . static::EXTENSION, // Page
            static::PAGE . static::EXTENSION . '_section',
            [
              'label_for' => $option['label_for'],
              'url' => ( $option['url'] ) ?: null,
              'id' => $id,
              'class' => strtolower( $option['label_for'] )
            ]
          );
        } else {
            $title = static::PAGE . static::EXTENSION . '_' . strtolower( $option['label_for'] );
            add_settings_field(
              $title,
              $option['label_for'],
              [static::class, 'showText'],
              static::PAGE . static::EXTENSION, // Page
              static::PAGE . static::EXTENSION . '_section',
              [
                'label_for' => $option['label_for'],
                'text' => ( $option['text'] ) ?: null,
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

        if ( empty( $option['url'] ) &&  ( $option['label_for'] !== 'Text before' ) ) {
          $empty_input[$id]['label_for'] = sanitize_text_field( $option['label_for'] );
          $empty_input[$id]['url'] = false;
        } else {
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
      }
      $return_input = array_merge($valid_input, $empty_input);
      return $return_input;
    }

}
