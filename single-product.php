<?php
/**
 * Single Product — 产品详情页
 * CPT: product（ACF 管理）
 * 字段来源约定：
 *   - 公共字段（page_banner_*、side_*、ws_*、quote_*、related_*、btn_*）：公共字段页 62 → jc_g62()
 *   - 产品字段（product_gallery）：当前产品 → get_field($key, get_the_ID())
 *   - 分类字段（product_category 的 sort_order）：get_term_meta / get_field('sort_order', 'product_category_'.$term_id)
 * 表单：[fluentform id="3"]（左栏）/ [fluentform id="4"]（底部）硬编码 + shortcode_exists() 判断（同 footer.php）
 * 证书：WP_Query(post_type='certificate') 轮播（与首页一致）
 */

get_header();

$post_id           = get_the_ID();
$post_title        = get_the_title();
$theme_uri         = get_template_directory_uri();

// ---- 当前产品的第一个分类（左栏高亮 + Related 筛选用）----
$cur_cats = get_the_terms($post_id, 'product_category');
$cur_cat  = ($cur_cats && !is_wp_error($cur_cats)) ? $cur_cats[0] : null;
$cur_cat_url = $cur_cat ? get_term_link($cur_cat) : jc_products_url();

// ---- 左栏 use：所有 product_category，按 sort_order 排序 ----
$all_cats = get_terms(array('taxonomy' => 'product_category', 'hide_empty' => false));
if (!is_wp_error($all_cats)) {
    usort($all_cats, function ($a, $b) {
        $sa = (int) get_field('sort_order', 'product_category_' . $a->term_id);
        $sb = (int) get_field('sort_order', 'product_category_' . $b->term_id);
        if ($sa === $sb) {
            return ($a->name <=> $b->name);
        }
        return $sa <=> $sb;
    });
} else {
    $all_cats = array();
}

// ---- 产品图集：product_gallery Group (img_1 ~ img_6)，主图=img_1 或特色图，缩略图=全部非空 ----
$gallery   = function_exists('get_field') ? get_field('product_gallery', $post_id) : null;
$gallery   = is_array($gallery) ? $gallery : array();
$gallery   = array_filter($gallery, function ($v) { return !empty($v); });       // 去掉空值，重置索引稍后处理
$gallery   = array_values($gallery);                                              // 保证 0,1,2...
$thumb_url = get_the_post_thumbnail_url($post_id, 'large');

// 主图优先取图集 img_1，否则取特色图，否则主题默认产品图
$main_img = !empty($gallery[0]) ? $gallery[0] : ($thumb_url ? $thumb_url : $theme_uri . '/assets/images/products/main-1.jpg');
// 缩略图：图集非空取图集，否则用特色图兜底
$thumbs   = !empty($gallery) ? $gallery : (($thumb_url) ? array($thumb_url) : array($theme_uri . '/assets/images/products/main-1.jpg'));

// ---- 联系方式（公共字段 company_whatsapp / company_email，jc_get 自动读 62 页）----
$wa_text      = jc_get('company_whatsapp_text', '');
$wa_num       = preg_replace('/\D+/', '', jc_get('company_whatsapp', ''));
$email        = jc_get('company_email', '');
$company_name = jc_get('company_name', '');
if ($wa_num === '') { $wa_num = '8618596356103'; }
$wa_href = $wa_num !== '' ? 'https://wa.me/' . $wa_num : '#';

// ---- banner 图（62 页 page_banner_img，Image URL 格式）----
$banner_img = jc_g62('page_banner_img', $theme_uri . '/assets/images/banner-products.png');
?>

<!-- ===================== PAGE BANNER（内容页公共横幅） ===================== -->
<section class="page-banner" style="background-image:url('<?php echo esc_url($banner_img); ?>');">
  <div class="page-banner-inner">
    <div class="page-banner-title"><?php echo esc_html(jc_g62('page_banner_title', jc_t('PRODUCTS'))); ?></div>
    <div class="breadcrumb">
      <a href="<?php echo esc_url(jc_home_url()); ?>"><?php echo esc_html(jc_t('HOME')); ?></a> &gt;
      <a href="<?php echo esc_url(jc_products_url()); ?>"><?php echo esc_html(jc_t('PRODUCTS')); ?></a> &gt;
      <?php if ($cur_cat) : ?><a href="<?php echo esc_url(get_term_link($cur_cat)); ?>"><?php echo esc_html($cur_cat->name); ?></a> &gt;<?php endif; ?>
      <span><?php echo esc_html($post_title); ?></span>
    </div>
  </div>
</section>

<!-- ===================== 主体：左侧快捷导航 + 右侧产品内容 ===================== -->
<div class="container page-wrap">

  <!-- ============ 左侧快捷导航 ============ -->
  <aside class="page-sidebar">
    <div class="side-box">
      <h3 class="side-title"><?php echo esc_html(jc_g62('side_nav_title', jc_t('Navigation'))); ?></h3>

      <!-- 搜索栏 -->
      <form class="side-search" action="<?php echo esc_url(jc_home_url()); ?>" method="get" role="search">
        <input type="text" name="s" placeholder="<?php echo esc_attr(jc_t('Search starts here')); ?>" autocomplete="off">
        <button type="submit" aria-label="<?php echo esc_attr(jc_t('Search')); ?>"><?php echo jc_icon('search'); ?></button>
      </form>

      <!-- product_category 分类列表（sort_order 升序） -->
      <ul class="side-cats">
        <?php foreach ($all_cats as $cat) : ?>
          <li>
            <?php if ($cur_cat && $cat->term_id === $cur_cat->term_id) : ?>
              <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="side-cat-link selected"><p><?php echo esc_html($cat->name); ?></p></a>
            <?php else : ?>
              <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="side-cat-link"><p><?php echo esc_html($cat->name); ?></p></a>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- 小标题 / 自由段（可空隐藏） -->
      <?php $side_sub = jc_g62('side_sub_text', ''); if ($side_sub !== '') : ?>
        <h4 class="side-sub"><?php echo esc_html($side_sub); ?></h4>
      <?php endif; ?>

      <!-- 蓝底邮箱：优先 side_email_text，空则用 company_email -->
      <?php
      $side_email = jc_g62('side_email_text', '');
      if ($side_email === '') { $side_email = $email; }
      if ($side_email !== '') :
      ?>
        <a href="mailto:<?php echo esc_attr($side_email); ?>" class="side-email"><?php echo esc_html($side_email); ?></a>
      <?php endif; ?>

      <!-- 左栏询盘表单：[fluentform id="3"] -->
      <div class="side-form-box">
        <h4 class="side-form-title"><?php echo esc_html(jc_t('Send Us A Message')); ?></h4>
        <?php if (shortcode_exists('fluentform')) {
            echo do_shortcode('[fluentform id="3"]');
        } else { ?>
          <form class="side-form" action="#" method="post">
            <input type="text" name="name" placeholder="<?php echo esc_attr(jc_t('Name')); ?>" required>
            <input type="tel" name="whatsapp" placeholder="<?php echo esc_attr(jc_t('WhatsApp')); ?>">
            <input type="text" name="company" placeholder="<?php echo esc_attr(jc_t('Company')); ?>">
            <input type="email" name="email" placeholder="<?php echo esc_attr(jc_t('Email')); ?>" required>
            <textarea name="message" rows="4" placeholder="<?php echo esc_attr(jc_t('Message')); ?>" required></textarea>
            <button type="submit" class="btn-submit"><?php echo esc_html(jc_t('Submit')); ?></button>
          </form>
        <?php } ?>
      </div>
    </div>
  </aside>

  <!-- ============ 右侧产品内容 ============ -->
  <main class="page-main">

    <!-- 产品头部：主图 + 缩略图 | 标题 + 联系方式 + 按钮 -->
    <div class="product-head">
      <div class="product-gallery" id="productGallery">
        <div class="main-image">
          <img id="mainImage" src="<?php echo esc_url($main_img); ?>" alt="<?php echo esc_attr($post_title); ?>">
        </div>
        <div class="thumb-slider">
          <button type="button" class="thumb-nav thumb-prev" aria-label="<?php echo esc_attr(jc_t('Previous thumbnails')); ?>"><?php echo jc_icon('arrow-left'); ?></button>
          <div class="thumb-list" id="thumbList">
            <?php $i = 0; foreach ($thumbs as $th) : $i++; ?>
              <button type="button" class="thumb<?php echo $i === 1 ? ' active' : ''; ?>" data-src="<?php echo esc_url($th); ?>" aria-label="<?php echo esc_attr(sprintf(jc_t('View image %d'),$i));?>">
                <img src="<?php echo esc_url($th); ?>" alt="<?php echo esc_attr($post_title . ' ' . $i); ?>">
              </button>
            <?php endforeach; ?>
          </div>
          <button type="button" class="thumb-nav thumb-next" aria-label="<?php echo esc_attr(jc_t('Next thumbnails')); ?>"><?php echo jc_icon('arrow-right'); ?></button>
        </div>
      </div>

      <div class="product-info">
        <h1 class="product-title"><?php echo esc_html($post_title); ?></h1>
        <p class="product-desc"><?php
          // 描述优先级：SEO 插件的 meta description（The SEO Framework）→ 摘要 → 正文纯文本
          $jc_meta = '';
          if (function_exists('the_seo_framework') && is_object(the_seo_framework())) {
              $jc_meta = trim(the_seo_framework()->get_description());
          }
          if ($jc_meta === '') {
              $jc_meta = trim(get_the_excerpt());
          }
          if ($jc_meta === '') {
              $jc_meta = trim(wp_strip_all_tags(get_the_content()));
          }
          echo esc_html($jc_meta);
        ?></p>

        <!-- 联系方式区域 -->
        <div class="contact-area">
          <div class="contact-row">
            <?php echo jc_icon('whatsapp'); ?>
            <a href="<?php echo esc_url($wa_href); ?>" target="_blank" rel="noopener" class="contact-value"><?php echo $wa_text !== '' ? esc_html($wa_text) : esc_html($wa_num); ?></a>
            <span class="contact-label"><?php echo esc_html(jc_t('WhatsApp')); ?></span>
          </div>
          <div class="contact-row">
            <?php echo jc_icon('mail'); ?>
            <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-value"><?php echo esc_html($email); ?></a>
            <span class="contact-label"><?php echo esc_html(jc_t('Email')); ?></span>
          </div>
        </div>

        <!-- 2 个按钮：Inquiry 平滑滚动到底部询盘区；Related Products 跳所属分类列表页 -->
        <div class="product-actions">
          <a href="#quote" class="btn-inquiry" data-scroll="#quote"><?php echo esc_html(jc_g62('btn_inquiry_text', 'Inquiry')); ?></a>
          <a href="<?php echo esc_url($cur_cat_url); ?>" class="btn-related"><?php echo esc_html(jc_g62('btn_related_text', 'Related Products')); ?></a>
        </div>
      </div>
    </div>

    <!-- ============ 下方小标题区块 ============ -->

    <!-- Product Parameters：正文 the_content（富文本表格等） -->
    <?php if (trim(get_the_content()) !== '') : ?>
    <section class="detail-block">
      <h2 class="detail-title"><span class="detail-mark"></span><?php echo esc_html(jc_t('Product Parameters')); ?></h2>
      <div class="table-parent">
        <?php the_content(); ?>
      </div>
    </section>
    <?php endif; ?>

    <?php
    // ---- 车间图组渲染辅助：ws_forging_imgs / ws_cnc_imgs / ws_testing_imgs（62 页 Group img_1~img_6）----
    function jc_render_workshop_group($imgs_group, $title, $group_name) {
        if ($title === '' || !is_array($imgs_group) || empty($imgs_group)) {
            return;
        }
        $imgs = array_filter($imgs_group, function ($v) { return !empty($v); });
        if (empty($imgs)) { return; }
        ?>
        <section class="detail-block">
          <h2 class="detail-title"><span class="detail-mark"></span><?php echo esc_html($title); ?></h2>
          <ul class="workshop-grid">
            <?php $n = 0; foreach ($imgs as $url) : $n++; ?>
              <li>
                <a href="<?php echo esc_url($url); ?>" class="lightbox" data-lightbox="<?php echo esc_attr($group_name); ?>">
                  <img loading="lazy" src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($title . ' ' . $n); ?>">
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
        <?php
    }

    // ---- 车间视频：ws_video_embed（有值优先）否则 ws_video_file ----
    $video_embed = jc_g62('ws_video_embed', '');
    $video_file  = jc_g62('ws_video_file', '');
    $video_title = jc_g62('ws_video_title', 'Production Workshop Video');
    $video_poster = jc_g62('ws_video_poster', '');

    if ($video_title !== '' && ($video_embed !== '' || $video_file !== '')) :
    ?>
    <section class="detail-block">
      <h2 class="detail-title"><span class="detail-mark"></span><?php echo esc_html($video_title); ?></h2>
      <div class="video-box">
        <?php if ($video_embed !== '') : ?>
          <?php echo $video_embed; // 已信任的 embed 代码（ACF textarea 后台），按原样输出 ?>
        <?php else : ?>
          <video controls preload="none" <?php if ($video_poster !== '') : ?>poster="<?php echo esc_url($video_poster); ?>"<?php endif; ?>>
            <source src="<?php echo esc_url($video_file); ?>" type="video/mp4">
            <?php echo esc_html(jc_t('Your browser does not support the video tag.'));?>
          </video>
        <?php endif; ?>
      </div>
    </section>
    <?php endif;

    // ---- 三个车间图组（标题可空隐藏，整块空则隐藏）----
    jc_render_workshop_group(jc_g62('ws_forging_imgs'), jc_g62('ws_forging_title', 'Forging workshop'), 'forging');
    jc_render_workshop_group(jc_g62('ws_cnc_imgs'),     jc_g62('ws_cnc_title', 'CNC Machining Workshop'), 'cnc');
    jc_render_workshop_group(jc_g62('ws_testing_imgs'), jc_g62('ws_testing_title', 'Testing equipment'), 'testing');
    ?>

    <!-- Certificate：certificate CPT 轮播（标题 62 页字段，内容自动拉取） -->
    <?php
    $cert_title = jc_g62('ws_cert_title', 'Certificate');
    $certs = new WP_Query(array(
        'post_type'      => 'certificate',
        'posts_per_page' => 8,
        'no_found_rows'  => true,
    ));
    if ($cert_title !== '' && $certs->have_posts()) :
    ?>
    <section class="detail-block">
      <h2 class="detail-title"><span class="detail-mark"></span><?php echo esc_html($cert_title); ?></h2>
      <div class="cert-slider-detail" id="certSlider">
        <div class="cert-viewport">
          <div class="cert-track" id="certTrack">
            <?php $ci = 0; while ($certs->have_posts()) : $certs->the_post(); $ci++; ?>
              <?php if (has_post_thumbnail()) : ?>
                <div class="cert-item">
                  <?php the_post_thumbnail('medium'); ?>
                </div>
              <?php endif; ?>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
        </div>
        <button class="cert-prev" aria-label="<?php echo esc_attr(jc_t('Previous certificate')); ?>"><?php echo jc_icon('arrow-left'); ?></button>
        <button class="cert-next" aria-label="<?php echo esc_attr(jc_t('Next certificate')); ?>"><?php echo jc_icon('arrow-right'); ?></button>
      </div>
    </section>
    <?php endif; ?>

    <!-- Get Your Free Quote Now!：底部询盘区 -->
    <?php $quote_title = jc_g62('quote_title', 'Get Your Free Quote Now!'); ?>
    <?php if ($quote_title !== '') : ?>
    <section class="detail-block quote-block" id="quote">
      <h2 class="detail-title"><span class="detail-mark"></span><?php echo esc_html($quote_title); ?></h2>
      <?php if (shortcode_exists('fluentform')) {
          echo '<div class="quote-form">' . do_shortcode('[fluentform id="4"]') . '</div>';
      } else { ?>
        <form class="quote-form" action="#" method="post">
          <div class="quote-row">
            <input type="text" name="name" placeholder="<?php echo esc_attr(jc_t('Name')); ?>" required>
            <input type="tel" name="whatsapp" placeholder="<?php echo esc_attr(jc_t('WhatsApp')); ?>">
          </div>
          <div class="quote-row">
            <input type="email" name="email" placeholder="<?php echo esc_attr(jc_t('Email')); ?>" required>
            <input type="text" name="company" placeholder="<?php echo esc_attr(jc_t('Company Name')); ?>">
          </div>
          <textarea name="message" rows="5" placeholder="<?php echo esc_attr(jc_t('Message')); ?>" required></textarea>
          <button type="submit" class="btn-submit"><?php echo esc_html(jc_t('Submit')); ?></button>
        </form>
      <?php } ?>
    </section>
    <?php endif; ?>

    <!-- Related Products：当前分类下的其他产品轮播（排除自己） -->
    <?php
    $related_title = jc_g62('related_title', 'Related Products');
    $related_args  = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
        'post__not_in'   => array($post_id),
        'no_found_rows'  => true,
    );
    if ($cur_cat) {
        $related_args['tax_query'] = array(array(
            'taxonomy' => 'product_category',
            'field'    => 'term_id',
            'terms'    => $cur_cat->term_id,
        ));
    }
    $related = new WP_Query($related_args);
    if ($related_title !== '' && $related->have_posts()) :
    ?>
    <section class="detail-block">
      <h2 class="detail-title"><span class="detail-mark"></span><?php echo esc_html($related_title); ?></h2>
      <div class="related-slider">
        <div class="related-viewport">
          <div class="related-track" id="relatedTrack">
            <?php while ($related->have_posts()) : $related->the_post(); ?>
              <div class="related-item">
                <a href="<?php the_permalink(); ?>" class="related-link">
                  <div class="related-img">
                    <?php if (has_post_thumbnail()) { the_post_thumbnail('medium'); } else { ?>
                      <img src="<?php echo esc_url($theme_uri . '/assets/images/products/main-1.jpg'); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php } ?>
                  </div>
                  <div class="related-name"><?php the_title(); ?></div>
                  <span class="related-more"><?php echo esc_html(jc_t('Learn More')); ?> &gt;&gt;</span>
                </a>
              </div>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
        </div>
        <button class="related-prev" aria-label="<?php echo esc_attr(jc_t('Previous products')); ?>"><?php echo jc_icon('arrow-left'); ?></button>
        <button class="related-next" aria-label="<?php echo esc_attr(jc_t('Next products')); ?>"><?php echo jc_icon('arrow-right'); ?></button>
      </div>
    </section>
    <?php endif; ?>

  </main>
</div>

<?php get_footer(); ?>