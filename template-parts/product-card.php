<?php
/**
 * Product card — 首页热销产品 / 产品列表的单个卡片
 *
 * 特色图：
 * 优先读取 WordPress 媒体库图片及 Alt Text；
 * 没有特色图时使用主题静态演示图。
 */

$jc_pid   = get_the_ID();
$jc_title = get_the_title();

$jc_thumb_id = get_post_thumbnail_id(
    $jc_pid
);

$jc_fb = array(
    'stainless-steel-forged-ring-blank.jpg',
    'slewing-bearing-rings.jpg',
    'forged-ring.jpg',
    'spur-gear-supplier.jpg',
    'helical-gear-1.jpg',
    'helical-gear-2.jpg',
    'spur-gear.jpg',
    'bevel-gear.jpg',
);

if ($jc_thumb_id) {

    $jc_image = jc_image_data(
        $jc_thumb_id,
        $jc_title,
        'jc-card'
    );

} else {

    $jc_image = array(
        'url' => get_template_directory_uri()
            . '/assets/images/products/'
            . $jc_fb[$jc_pid % 8],

        'alt' => $jc_title,
    );
}
?>

<li class="listBox">

  <a
    class="listBoxHref"
    href="<?php echo esc_url(get_permalink()); ?>"
  >

    <div class="ImghidCont">
      <div class="imgHoverAn">

        <img
          loading="lazy"
          src="<?php echo esc_url($jc_image['url']); ?>"
          alt="<?php echo esc_attr($jc_image['alt']); ?>"
        >

      </div>
    </div>

    <div class="listTxt">
      <div class="textLineP">
        <?php echo esc_html($jc_title); ?>
      </div>
    </div>

    <div class="listTxt">
      <span class="card-more card-fill">
        <?php echo esc_html(jc_t('Learn More')); ?> &gt;&gt;
      </span>
    </div>

  </a>

</li>