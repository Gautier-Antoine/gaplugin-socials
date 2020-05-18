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
      FOLDER = 'gaplugin-socials';

    public static function getfolder(){
      return plugin_dir_url( __DIR__ );
    }
    /**
    * @var array list of social medias
    */
    public static $list = [
      ['name' => 'FaceBook'], ['name' => 'Instagram'],
      ['name' => 'SnapChat'], ['name' => 'Twitter'],
      ['name' => 'LinkedIn'], ['name' => 'Viadeo'],
      ['name' => 'Pinterest'], ['name' => 'Tumblr'],
      ['name' => 'FlipBoard'], ['name' => 'Flickr'],
      ['name' =>'Skype'], ['name' =>'WhatsApp'],
      ['name' => 'Telegram'], ['name' => 'Viber'],
      ['name' => 'WeChat'], ['name' => 'Map'],
      ['name' => 'Email'], ['name' => 'Phone'],
      ['name' => 'DeviantArt'], ['name' => 'Discord'],
      ['name' => 'GitHub'], ['name' => 'Twitch'],
      ['name' => 'YouTube'], ['name' => 'Vimeo']
    ];
    public static function removeExtraOptions() {
      delete_option(static::PAGE . '-gap-showtext' );
    }
    public static function getExtraSettings () {
      $text = __('Click to hide text before socials', static::LANGUAGE);
      register_setting(
        static::PAGE . static::EXTENSION,
        static::PAGE . '-gap-showtext'
      );
      add_settings_field(
        static::PAGE . static::EXTENSION . '_gap_showtext',
        $text,
        [static::class, 'showText'],
        static::PAGE . static::EXTENSION,
        static::PAGE . static::EXTENSION . '_section'
      );
    }
    public static function showText() {
        ?>
          <input
            type="checkbox"
            name="<?= static::PAGE . '-gap-showtext' ?>"
            class="checkbox show-text"
            title="<?php printf(__('Checkbox for showing text', static::LANGUAGE)) ?>"
            <?php if (get_option(static::PAGE . '-gap-showtext')) {echo ' checked';} ?>
          >
        <?php
    }

    public static function registerSettingsText () {
      printf(
        __( 'Which social media do you want to show to your visitors', static::LANGUAGE) . '<br>' .
        __('Put the link to your social media to activate', static::LANGUAGE) .
        '<br>Shortcode = [' . static::PAGE . '-nav]'
      );
    }
    public static function addPageFunction($args) {
        ?>
          <textarea
            name="<?= static::PAGE . '-' . $args['class'] ?>"
            cols="30"
            rows= "1"
            title="<?php printf(__('Put your %1$s URL', static::LANGUAGE), $args['class']) ?>"
          ><?=
              esc_html(get_option(static::PAGE . '-' . $args['class']))
          ?></textarea>
        <?php
    }
    public static function ShortcodeNav() {
        echo '<div class="' . static::PAGE . '">';
          if (!get_option(static::PAGE . '-gap-showtext')){
            echo '<div class="' . static::PAGE . '-text">';
              printf(__( 'Follow us on', static::LANGUAGE ));
            echo '</div>';
          }
          foreach (static::$list as $social) {
              $class = strtolower($social['name']);
              if ($class === 'email') {
                $link = 'mailto:';
              } else {
                $link = '';
              }
              if (get_option(static::PAGE . '-' . $class)) {
                echo '
                  <a
                    target="_blank"
                    title="' . __( 'Link to', static::LANGUAGE ) . ' ' . $social['name'] . '"
                    href="' . $link . esc_html(get_option(static::PAGE . '-' . $class)) . '"
                  >
                    <div class="' . $class . '"></div>
                  </a>
                ';
              }
          }
        echo '</div>';
    }
}
