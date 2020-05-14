<?php
namespace AGPlugin;
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
    FOLDER = 'agplugin-socials';

    public static function getfolder(){
      return plugin_dir_url( __DIR__ );
    }
  /**
  * @var array names of the share social medias and urls
  */
  public static $list = [
      ['name' => 'FaceBook', 'url' => 'https://www.facebook.com/sharer/sharer.php?u='],
      ['name' => 'Twitter', 'url' => 'https://twitter.com/share?url='],
      [
        'name' => 'Pinterest', 'url' => 'http://pinterest.com/pin/create/button/?url=',
        'imgurl' => '&amp;media=',
        'titleurl' => '&amp;description='
      ],
      ['name' => 'WhatsApp', 'url' => 'https://wa.me/?text='],
      ['name' => 'Telegram', 'url' => 'https://t.me/share/url?url='],
      ['name' => 'Email', 'url' => 'mailto:?body=']
      // CSS ready: insta,Map, Youtube, Twitch, linkedin, vimeo, github, WeChat, Tumblr, Viber, Snapchat, flipboard
  ];
  public static function removeExtraOptions() {
    delete_option(static::PAGE . '-agp-showtext' );
  }
  public static function getExtraSettings () {
    $text = __('Click to hide text before socials', static::LANGUAGE);
    register_setting(
      static::PAGE . static::EXTENSION,
      static::PAGE . '-agp-showtext'
    );
    add_settings_field(
      static::PAGE . static::EXTENSION . '_agp_showtext',
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
          name="<?= static::PAGE . '-agp-showtext' ?>"
          class="checkbox show-text"
          title="<?php printf(__('Checkbox for hiding text', static::LANGUAGE)) ?>"
          <?php if (get_option(static::PAGE . '-agp-showtext')) {echo ' checked';} ?>
        >
      <?php
  }

  public static function registerSettingsText () {
    printf(
      __( 'Which social media do you want to share with your visitors', static::LANGUAGE ) .
      '<br>Shortcode = [' . static::PAGE . '-nav]'
    );
  }
  public static function addPageFunction($args) {
      ?>
        <input
          type="checkbox"
          class="checkbox"
          name="<?= static::PAGE . '-' . $args['class'] ?>"
          title="<?php printf(__('Checkbox for %1$s', static::LANGUAGE), $args['class']) ?>"
          <?php if (get_option(static::PAGE . '-' . $args['class'])) {echo ' checked';} ?>
        >
      <?php
  }
  public static function ShortcodeNav() {
      echo '<div class="' . static::PAGE . '">';

     if (!get_option(static::PAGE . '-agp-showtext')){
        echo '<div class="' . static::PAGE . '-text">';
          printf(__( 'Share this on', static::LANGUAGE ));
        echo '</div>';
      }
        foreach (static::$list as $social) {
              $class = strtolower($social['name']);
              if (isset($social['imgurl'])) {
                $img = $social['imgurl'] . get_the_post_thumbnail_url(get_the_ID(),'full');
              } else { $img = '';}
              if (isset($social['titleurl'])) {
                $title = $social['titleurl'] . get_the_title();
              } else { $title = '';}

              if (get_option(static::PAGE . '-' . $class)) {
                echo '
                  <a
                    target="_blank"
                    title="' . __( 'Share this on', static::LANGUAGE ) . ' ' . $social['name'] . '"
                    href="' . $social['url'] . get_permalink() . $img . $title . '"
                  >
                    <div class="' . $class . '"></div>
                  </a>';
              }
        }
      echo '</div>';
  }
}
