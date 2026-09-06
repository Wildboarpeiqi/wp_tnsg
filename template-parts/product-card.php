<?php
/**
 * Product card — 首页热销产品/产品列表的单个卡片
 * 在 WP_Query(product) 循环内调用：get_template_part('template-parts/product-card')
 * 特色图（缩略图）→ 主题 jc-card 尺寸裁剪；没设特色图时按 id 轮换静态演示图兜底。
 */
$jc_pid    = get_the_ID();
$jc_thumb  = get_the_post_thumbnail_url($jc_pid, 'jc-card');
$jc_fb     = array(
    'stainless-steel-forged-ring-blank.jpg',
    'slewing-bearing-rings.jpg',
    'forged-ring.jpg',
    'spur-gear-supplier.jpg',
    'helical-gear-1.jpg',
    'helical-gear-2.jpg',
    'spur-gear.jpg',
    'bevel-gear.jpg',
);
$jc_img = $jc_thumb ? $jc_thumb : (get_template_directory_uri() . '/assets/images/products/' . $jc_fb[$jc_pid % 8]);
$jc_title = get_the_title();
?>
<li class="listBox">
  <a class="listBoxHref" href="<?php echo esc_url(get_permalink()); ?>">
    <div class="ImghidCont"><div class="imgHoverAn"><img loading="lazy" src="<?php echo esc_url($jc_img); ?>" alt="<?php echo esc_attr($jc_title); ?>"></div></div>
    <div class="listTxt"><div class="textLineP"><?php echo esc_html($jc_title); ?></div></div>
    <div class="listTxt"><span class="card-more card-fill">Learn More &gt;&gt;</span></div>
  </a>
</li>
