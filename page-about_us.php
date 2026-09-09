<?php
/**
 * Template Name: About Us
 *
 * page-about_us.php — 关于我们页
 * WordPress 按页面 slug = about_us 自动匹配本模板（后台无需手动选模板）。
 * 静态版对照：E:\jingxiang\site\about.html
 *
 * 字段读取规则（与《ACF字段清单.md》十 一致）：
 *  - 本页专属字段（site_banner / about_intro_* / cnc_* / about_cert_* / test_*）
 *    挂在 About Us 页面自己身上（位置规则：页面 == About Us），用 $page_id 读取。
 *  - 公司名 about_company：公共字段页 62（首页 About 区块同款），兜底读首页 about_section_about_company。
 *  - Our service 区块：复用首页 svc_1~5 + svc_cta_*（jc_get 自动读首页 ID，About 页零重复录入）。
 *  - Certificate 轮播：certificate CPT（与首页同一套），无图片字段。
 * 不散架原则：字段没建/没填 → 回退静态默认内容。
 */
get_header();

$page_id   = get_the_ID();
$theme_uri = get_template_directory_uri();

/* 图片/文件字段取值：兼容 ACF 三种返回格式（图片 ID / 数组 / URL 字符串），其余原样 */
$pimg = function ($v) {
    if (is_numeric($v)) {
        // ACF 返回图片 ID → 转完整 URL（先试图片，再试普通附件，覆盖 Image/File 两种字段）
        $u = function_exists('wp_get_attachment_image_url') ? wp_get_attachment_image_url((int) $v, 'full') : '';
        if (!$u && function_exists('wp_get_attachment_url')) { $u = wp_get_attachment_url((int) $v); }
        return $u ? $u : '';
    }
    if (is_array($v)) { return isset($v['url']) ? $v['url'] : ''; }
    return is_string($v) ? $v : '';
};

/* 页面专属字段读取（图片字段经 $pimg 兼容 ID/数组/URL；文本字段原样，空则兜底） */
$pf = function ($key, $default = '') use ($page_id, $pimg) {
    $v = function_exists('get_field') ? get_field($key, $page_id) : '';
    $v = $pimg($v);
    return ($v !== '' && $v !== null && $v !== false) ? $v : $default;
};

/* 公司名：优先公共字段页 62 的 about_company，其次首页 About 区块，最后硬编码兜底 */
$about_company = function_exists('get_field') ? get_field('about_company', jc_global_field_id()) : '';
if ($about_company === '' || $about_company === null || $about_company === false) {
    $about_company = jc_get('about_section_about_company', '');
}
if ($about_company === '') {
    $about_company = 'Liaocheng Jiucheng Auto Parts Co., Ltd.';
}

/* ===================== BANNER（背景 = site_banner，标题 = 页面标题，面包屑自动） ===================== */
$banner = $pf('site_banner', '');
if ($banner === '') { $banner = $theme_uri . '/assets/images/about-bg.jpg'; }
$banner_title = get_the_title();
?>

  <!-- ===================== PAGE BANNER ===================== -->
  <section class="page-banner" style="background-image:url('<?php echo esc_url($banner); ?>');">
    <div class="page-banner-inner">
      <div class="page-banner-title"><?php echo esc_html($banner_title); ?></div>
      <div class="breadcrumb">
        <a href="<?php echo esc_url(jc_home_url()); ?>"><?php echo esc_html(jc_t('HOME')); ?></a> &gt;
        <span><?php echo esc_html($banner_title); ?></span>
      </div>
    </div>
  </section>

  <!-- ===================== 公司介绍 INTRO（左文右图/视频） ===================== -->
  <?php
  $intro_title = $pf('about_intro_title', 'About Us');
  $intro_desc  = $pf('about_intro_desc', '');
  if ($intro_desc === '') {
      $intro_desc = 'Established in 2015, Liaocheng Jiucheng Auto Parts Co., Ltd. is a high-tech enterprise integrating R&D, production, and sales. The company specializes in the manufacturing and processing of precision-forged automotive synchronizer blanks, high-precision gear blanks, and large-specification chain sleeves. It has a registered capital of RMB 1 million, fixed assets of RMB 10 million, a total building area of 8,000 m&sup2; (including 6,000 m&sup2; of workshop space), and dedicated workshops for precision forging and machining. The workforce consists of 45 employees, including one senior engineer, three mechanical engineers, one quality engineer, eight quality inspectors, and one electrical engineer. The company focuses on producing high-end automotive components and precision forgings, with a product portfolio that includes automotive synchronizer sleeves, synchronizer hubs, differential housings, and various other gear forgings.';
  }
  $intro_img   = $pf('about_intro_img', '');
  if ($intro_img === '') { $intro_img = $theme_uri . '/assets/images/about-intro.png'; }
  $intro_embed = $pf('about_intro_video_embed', '');   // 有值优先（YouTube iframe 等）
  $intro_file  = $pf('about_intro_video_file', '');    // mp4（embed 空时用）
  ?>
  <section class="about-intro section" id="aboutIntro">
    <div class="container intro-grid">
      <div class="intro-text">
        <p class="section-eyebrow intro-eyebrow"><?php echo esc_html($intro_title); ?></p>
        <h2 class="about-company"><?php echo esc_html($about_company); ?></h2>
        <div class="intro-desc"><?php echo wpautop(wp_kses_post($intro_desc)); ?></div>
      </div>

      <!-- 右侧媒体：16:9 固定容器（加载前不塌陷）；embed 优先 → file 其次 → 图片兜底 -->
      <div class="intro-media">
        <div class="intro-media-inner">
          <?php if ($intro_embed !== '') : ?>
            <div class="intro-media-video"><?php echo $intro_embed; // 已信任的 embed 代码（ACF textarea 后台） ?></div>
          <?php elseif ($intro_file !== '') : ?>
            <div class="intro-media-video"><video controls preload="none" poster="<?php echo esc_url($intro_img); ?>"><source src="<?php echo esc_url($intro_file); ?>" type="video/mp4"><?php echo esc_html(jc_t('Your browser does not support the video tag.'));?></video></div>
          <?php else : ?>
            <img
              src="<?php echo esc_url($intro_img); ?>"
              alt="<?php echo esc_attr($about_company . ' ' . jc_t('factory')); ?>"
              class="intro-media-img"
            >
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== CNC MACHINING（左轮播右文 + 询盘弹窗） ===================== -->
  <?php
  $cnc_title = $pf('cnc_title', 'CNC Machining');
  $cnc_desc  = $pf('cnc_desc', '');
  if ($cnc_desc === '') {
      $cnc_desc = 'Its precision-forged products&mdash;such as automotive synchronizer sleeves&mdash;achieve the goals of eliminating or minimizing the need for turning, conserving raw materials and energy, and reducing production costs; material utilization rates have increased by 50% compared to traditional processes, overall production costs have dropped by 20%, and electricity savings exceed 30%. The "Jiucheng" team is committed to scaling new heights through continuous self-breakthroughs, writing new chapters of success, and maintaining strong momentum for growth.';
  }
  $cnc_quote = $pf('cnc_quote_text', 'Get Free Quote');

  // 左列轮播图：cnc_imgs Group（img_1~img_6），空则用主题演示图
  $cnc_imgs = function_exists('get_field') ? get_field('cnc_imgs', $page_id) : array();
  if (!is_array($cnc_imgs)) { $cnc_imgs = array(); }
  $cnc_fb = array(
      '/assets/images/workshops/cnc-1.png',
      '/assets/images/workshops/cnc-2.png',
      '/assets/images/workshops/cnc-3.png',
      '/assets/images/workshops/cnc-4.png',
      '/assets/images/workshops/cnc-5.png',
      '/assets/images/workshops/forging-1.png',
  );
  ?>
  <section class="cnc-section section" id="cncMachining">
    <div class="container cnc-grid">
      <!-- 左列：一屏 3 张轮播（6 张 = 2 屏；移动端 2 张），一次动 1 张 + 圆点 + 箭头 + 手势 -->
      <div class="cnc-slider" id="cncSlider">
        <div class="cnc-viewport">
          <div class="cnc-track" id="cncTrack">
            <?php for ($ci = 1; $ci <= 6; $ci++) :
                $img = isset($cnc_imgs['img_' . $ci]) ? $pimg($cnc_imgs['img_' . $ci]) : '';
                if ($img === '') { $img = $theme_uri . $cnc_fb[$ci - 1]; }
            ?>
            <div class="cnc-slide">
              <figure class="cnc-figure">
                <div class="cnc-img"><img loading="lazy" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(jc_t('CNC Machining Workshop')); ?>"></div>
                <figcaption class="cnc-caption"><?php echo esc_html(jc_t('CNC Machining Workshop')); ?></figcaption>
              </figure>
            </div>
            <?php endfor; ?>
          </div>
        </div>
        <button class="cnc-arrow cnc-prev" aria-label="<?php echo esc_attr(jc_t('Previous slide')); ?>"><?php echo jc_icon('arrow-left'); ?></button>
        <button class="cnc-arrow cnc-next" aria-label="<?php echo esc_attr(jc_t('Next slide')); ?>"><?php echo jc_icon('arrow-right'); ?></button>
        <div class="cnc-dots" id="cncDots" aria-label="<?php echo esc_attr(jc_t('Slide pages')); ?>"></div>
      </div>

      <!-- 右列：标题 + 段落（固定高度下滑）+ 询盘按钮 -->
      <div class="cnc-text">
        <h2 class="cnc-title"><?php echo esc_html($cnc_title); ?></h2>
        <div class="cnc-desc"><?php echo wpautop(wp_kses_post($cnc_desc)); ?></div>
        <button class="btn-main btn-quote" data-lightbox="contactModal"><?php echo esc_html($cnc_quote); ?></button>
      </div>
    </div>
  </section>

  <!-- ===================== CERTIFICATE 证书（标题/文案 + certificate CPT 轮播） ===================== -->
  <?php
  $cert_title = $pf('about_cert_title', 'Certificate');
  $cert_desc  = $pf('about_cert_desc', '');
  if ($cert_desc === '') {
      $cert_desc = 'Quality is the foundation of the enterprise\'s survival, while innovation paves the path for its development. Mindset determines the way forward; the company seeks sincere, mutually beneficial cooperation with you to deliver superior product performance and create the industrial value you envision, working together to achieve excellence and continued success.';
  }
  ?>
  <section class="certificates section" id="certificates">
    <div class="container">
      <div class="section-head center">
        <h2 class="section-title" style="color:#101B4D;"><?php echo esc_html($cert_title); ?></h2>
        <p class="section-sub"><?php echo esc_html($about_company); ?></p>
      </div>
      <p class="cert-desc"><?php echo esc_html($cert_desc); ?></p>
      <div class="cert-slider">
        <div class="cert-track">
          <?php
          // 证书轮播：直接读取「证书」栏目（certificate CPT），每篇的特色图即证书图；没建时回退静态演示图
          $jc_certs_q = new WP_Query(array(
              'post_type'      => 'certificate',
              'posts_per_page' => -1,
              'orderby'        => 'menu_order date',
              'order'          => 'ASC',
          ));
          if ($jc_certs_q->have_posts()) {
              while ($jc_certs_q->have_posts()) {
                  $jc_certs_q->the_post();
                  $jc_cert_img = get_the_post_thumbnail_url(get_the_ID(), 'large');
                  if ($jc_cert_img) {
                      echo '<figure class="cert-item"><img loading="lazy" src="' . esc_url($jc_cert_img) . '" alt="' . esc_attr(get_the_title()) . '"></figure>';
                  }
              }
              wp_reset_postdata();
          } else {
              // 证书还没录入时的兜底：静态演示图
              $jc_certs = array(
                  '/assets/images/certs/cert-iso9001-2015.jpg',
                  '/assets/images/certs/cert-license-1.jpg',
                  '/assets/images/certs/cert-3.jpg',
                  '/assets/images/certs/cert-9001.jpg',
                  '/assets/images/certs/cert-license-2.jpg',
              );
              foreach ($jc_certs as $jc_c) {
                  echo '<figure class="cert-item"><img loading="lazy" src="' . esc_url($theme_uri . $jc_c) . '" alt="' .esc_attr(jc_t('Certificate')) . '"></figure>';
              }
          }
          ?>
        </div>
        <button class="slider-arrow cert-prev" aria-label="<?php echo esc_attr(jc_t('Previous')); ?>"><?php echo jc_icon('arrow-left'); ?></button>
        <button class="slider-arrow cert-next" aria-label="<?php echo esc_attr(jc_t('Next')); ?>"><?php echo jc_icon('arrow-right'); ?></button>
      </div>
    </div>
  </section>

  <!-- ===================== TESTING EQUIPMENT 检测设备（左大图 + 右缩略图联动） ===================== -->
  <?php
  $test_title = $pf('test_title', 'Testing equipment');
  // test_imgs Group：img_1~img_6（图）+ name_1~name_6（设备名，同一组；空则隐藏名称）
  $test_imgs = function_exists('get_field') ? get_field('test_imgs', $page_id) : array();
  if (!is_array($test_imgs)) { $test_imgs = array(); }
  $test_fb = array(
      array('/assets/images/workshops/testing-1.jpg', 'Universal Materials Testing Machine'),
      array('/assets/images/workshops/testing-2.jpg', 'UV Notched Tensile Tester'),
      array('/assets/images/workshops/testing-3.jpg', 'Brinell hardness tester'),
      array('/assets/images/workshops/testing-4.jpg', 'Impact testing machine'),
      array('/assets/images/workshops/testing-5.jpg', 'Metallographic Analysis System'),
      array('/assets/images/workshops/testing-6.jpg', 'Metallographic Polishing Machine &ndash; Notch Tester'),
  );
  ?>
  <section class="test-section section" id="testEquipment">
    <div class="container">
      <div class="section-head">
        <h2 class="section-title" style="color:#101B4D;"><?php echo esc_html($test_title); ?></h2>
      </div>
      <div class="test-stage">
        <!-- 左：大图 + 图下标题（无背景）；JS 从这里的 6 个 figure 提取数据渲染右侧缩略图 -->
        <div class="test-featured" id="testFeatured">
          <div class="test-featured-track" id="testMainTrack">
            <?php for ($ti = 1; $ti <= 6; $ti++) :
                $t_img  = isset($test_imgs['img_' . $ti]) ? $pimg($test_imgs['img_' . $ti]) : '';
                if ($t_img === '') { $t_img = $theme_uri . $test_fb[$ti - 1][0]; }
                $t_name = isset($test_imgs['name_' . $ti]) && $test_imgs['name_' . $ti] !== '' ? $test_imgs['name_' . $ti] : $test_fb[$ti - 1][1];
                if (is_array($t_name) && isset($t_name['text'])) { $t_name = $t_name['text']; }
            ?>
            <figure class="test-featured-slide">
              <div class="test-img"><img loading="lazy" src="<?php echo esc_url($t_img); ?>" alt="<?php echo esc_attr($t_name); ?>"></div>
              <figcaption class="test-caption"><?php echo esc_html($t_name); ?></figcaption>
            </figure>
            <?php endfor; ?>
          </div>
        </div>

        <!-- 右：缩略图窗口（JS 渲染：当前大图的下一张起 2 张，轨道 flex 露边）+ 底部左右箭头 -->
        <div class="test-side">
          <div class="test-thumbs-viewport">
            <ul class="test-thumbs-track" id="testThumbs"></ul>
          </div>
          <div class="test-controls">
            <button class="test-arrow test-prev" aria-label="<?php echo esc_attr(jc_t('Previous equipment')); ?>"><?php echo jc_icon('arrow-left'); ?></button>
            <button class="test-arrow test-next" aria-label="<?php echo esc_attr(jc_t('Next equipment')); ?>"><?php echo jc_icon('arrow-right'); ?></button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== OUR SERVICE（复用首页 svc_1~5 + svc_cta_* 字段，零重复录入） ===================== -->
  <section class="service section" id="service">
    <div class="container">
      <div class="section-head center">
        <h2 class="section-title" style="color:#101B4D;"><?php echo esc_html(jc_t('Our service')); ?></h2>
        <p class="section-sub"><?php echo esc_html($about_company); ?></p>
      </div>
      <div class="service-grid">
        <?php
        // CTA 联系卡：service_section > svc_cta_bg / svc_cta_text / svc_cta_link（首页字段）
        $jc_cta = jc_link('service_section_svc_cta_link', jc_page_url('contact_us'), 'Contact Us');
        $jc_cta_bg   = jc_img('service_section_svc_cta_bg', '/assets/images/service-bg.jpg');
        $jc_cta_text = jc_get('service_section_svc_cta_text', 'Please contact us as soon as possible for cooperation!');
        // 5 个服务卡：service_section > svc_1..svc_5（每个内含 svc_N_title / svc_N_desc / svc_N_link）
        $jc_svcs = array(
            1 => array('icon' => 'svc-chain',  'title' => 'Complete Industrial Chain Services', 'desc' => 'Integrated R&D, production, and sales, ensuring efficient end-to-end connectivity.', 'url' => 'about_us'),
            2 => array('icon' => 'svc-zap',    'title' => 'Energy-Saving Precision Forging Technology', 'desc' => 'Material utilization increased by 50%, comprehensive production cost reduced by 20%, power saving rate over 30%.', 'url' => 'about_us'),
            3 => array('icon' => 'svc-shield', 'title' => 'Strict Quality Control System', 'desc' => 'Professional engineering and inspection team, equipped with spectrometer to guarantee precision of forged parts.', 'url' => 'about_us'),
            4 => array('icon' => 'svc-factory','title' => 'Large-Scale Intelligent Production Capacity', 'desc' => 'Multiple CNC forging lines with an annual output of 2.9 million sets, supporting major domestic automobile manufacturers.', 'url' => 'about_us'),
            5 => array('icon' => 'svc-badge',  'title' => 'High Cost-Effectiveness + Efficient Delivery', 'desc' => 'Advantages of large-scale production + fast delivery, earning long-term cooperation and recognition from well-known enterprises.', 'url' => 'about_us'),
        );
        $jc_si = 0;
        foreach ($jc_svcs as $jc_n => $jc_svc) {
            $jc_si++;
            $jc_svc_prefix = 'service_section_svc_' . $jc_si . '_';
            $jc_slink = jc_link($jc_svc_prefix . 'svc_' . $jc_si . '_link', jc_page_url($jc_svc['url']), 'Learn More');
            $jc_svc_ttl = jc_get($jc_svc_prefix . 'svc_' . $jc_si . '_title', $jc_svc['title']);
            $jc_svc_dsc = jc_get($jc_svc_prefix . 'svc_' . $jc_si . '_desc', $jc_svc['desc']);
            echo '<div class="service-card">';
            echo '<div class="service-icon">' . jc_icon($jc_svc['icon']) . '</div>';
            echo '<h3>' . esc_html($jc_svc_ttl) . '</h3>';
            echo '<p>' . esc_html($jc_svc_dsc) . '</p>';
            echo '<a href="' . esc_url($jc_slink['url']) . '" class="service-more">' . esc_html($jc_slink['text']) . ' &rarr;</a>';
            echo '</div>';
            // 第 4 张卡之后插入 CTA 卡（作为第 5 位，位于第二行中间：1,2,3 / 4,CTA,6）
            if ($jc_si === 4) {
                echo '<div class="service-card service-card-cta">';
                echo '<div class="service-cta-bg" style="background-image:url(\'' . esc_url($jc_cta_bg) . '\');"></div>';
                echo '<p class="service-cta-text">' . esc_html($jc_cta_text) . '</p>';
                echo '<a href="#" class="btn-main" data-lightbox="contactModal">' . esc_html($jc_cta['text']) . '</a>';
                echo '</div>';
            }
        }
        ?>
      </div>
    </div>
  </section>

<?php get_footer(); ?>
