<?php
/**
 * front-page.php — 前台首页
 * 所有区块数据都通过 jc_* 辅助函数读取 ACF 字段（挂在"前台首页"页面 ID 39 下）。
 * 字段按嵌套 Group 结构组织（homepage_slides / product_section / about_section /
 * application_section / service_section），读取时用下划线拼全路径。
 * 字段还没建/没填时自动回退到静态默认内容，页面不会散架。
 */
get_header();

$posts_page_id = jc_posts_page_id();

$news_url = $posts_page_id
    ? get_permalink($posts_page_id)
    : jc_home_url();
?>

  <!-- ===================== HERO SLIDER ===================== -->
  <?php
  // hero_company 在 homepage_slides 组内
  $hero_company = jc_get('homepage_slides_hero_company', 'TNSG');
  // 每个 slide 是一个 group：homepage_slides > hero1 / hero2 / hero3
  $hero_slides = array(
      1 => array(
          'slug'  => 'hero1',
          'default_bg'   => '/assets/images/hero-1.jpg',
          'default_title'=> 'Energy-saving forging, quality first',
          'default_desc' => 'Primarily manufactures high-end automotive components and precision automotive forgings.',
          'default_align'=> '',
      ),
      2 => array(
          'slug'  => 'hero2',
          'default_bg'   => '/assets/images/hero-2.webp',
          'default_title'=> 'Deeply Rooted in Precision Forging',
          'default_desc' => 'High-End Auto Components & Custom Forging Parts Manufacturer',
          'default_align'=> 'center',
      ),
      3 => array(
          'slug'  => 'hero3',
          'default_bg'   => '/assets/images/hero-3.jpg',
          'default_title'=> 'Precision Custom Forging Solutions',
          'default_desc' => 'Primarily manufactures high-end automotive components and precision automotive forgings.',
          'default_align'=> 'right',
      ),
  );
  $hero_align_map = array('left' => '', 'center' => 'hero-content-center', 'right' => 'hero-content-right');
  ?>
  <section class="hero-slider" id="heroSlider" aria-label="<?php echo esc_attr(jc_t('Hero')); ?>">
    <div class="hero-track">
      <?php $hc_i = 0; foreach ($hero_slides as $hc_slide) : $hc_i++; ?>
      <?php
      // 嵌套读取：homepage_slides_hero1_hero_1_title 等
      $hc_prefix = 'homepage_slides_' . $hc_slide['slug'] . '_';
      $hc_bg   = jc_get($hc_prefix . 'hero_' . $hc_i . '_bg', $hc_slide['default_bg']);
      $hc_ttl  = jc_get($hc_prefix . 'hero_' . $hc_i . '_title', $hc_slide['default_title']);
      $hc_dsc  = jc_get($hc_prefix . 'hero_' . $hc_i . '_desc', $hc_slide['default_desc']);
      $hc_alg  = jc_get($hc_prefix . 'hero_' . $hc_i . '_align', $hc_slide['default_align']);
      $hc_btn  = jc_link($hc_prefix . 'hero_' . $hc_i . '_btn', jc_page_url('about_us'), jc_t('Learn More'));
      $hc_btn2 = jc_link($hc_prefix . 'hero_' . $hc_i . '_btn2', jc_page_url('contact_us'), jc_t('Contact Us'));
      // 视频支持（2026-09-06）：hero_N_video_file（本地文件）优先，其次 hero_N_video_url（嵌入链接），都没有才用图片
      $hc_vfile = jc_get($hc_prefix . 'hero_' . $hc_i . '_video_file', '');
      $hc_vurl  = jc_get($hc_prefix . 'hero_' . $hc_i . '_video_url', '');
      if (is_array($hc_vfile) && !empty($hc_vfile['url'])) { $hc_vfile = $hc_vfile['url']; }
      $hc_video = ($hc_vfile !== '' && $hc_vfile !== null) ? 'file' : (($hc_vurl !== '' && $hc_vurl !== null) ? 'embed' : '');
      ?>
      <div class="hero-slide<?php echo $hc_i === 1 ? ' hero-slide-active' : ''; ?><?php echo $hc_video !== '' ? ' hero-slide-has-video' : ''; ?>">
        <?php if ($hc_video === 'file') : ?>
        <div class="hero-video">
          <video src="<?php echo esc_url($hc_vfile); ?>" muted loop playsinline preload="metadata"></video>
        </div>
        <?php elseif ($hc_video === 'embed') : ?>
        <div class="hero-video">
          <iframe data-src="<?php echo esc_url(jc_video_embed_url($hc_vurl)); ?>" frameborder="0" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
        </div>
        <?php else : ?>
        <div class="hero-bg" style="background-image:url('<?php echo esc_url(is_string($hc_bg) && $hc_bg !== '' ? $hc_bg : get_template_directory_uri() . $hc_slide['default_bg']); ?>');"></div>
        <?php endif; ?>
        <div class="hero-content<?php
          echo isset($hero_align_map[$hc_alg]) ? ' ' . esc_attr($hero_align_map[$hc_alg]) : '';
        ?>">
          <p class="hero-company"><?php echo esc_html($hero_company); ?></p>
          <h1 class="hero-title"><?php echo esc_html($hc_ttl); ?></h1>
          <p class="hero-desc"><?php echo esc_html($hc_dsc); ?></p>
          <div class="hero-btns">
            <a href="<?php echo esc_url($hc_btn['url']); ?>" class="btn-main btn-fill"><?php echo esc_html($hc_btn['text']); ?>&nbsp;&rarr;</a>
            <?php if (!empty($hc_btn2['url']) && $hc_btn2['url'] !== '#' ) : ?>
            <a href="<?php echo esc_url($hc_btn2['url']); ?>" class="btn-ghost"><?php echo esc_html($hc_btn2['text']); ?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <button class="hero-arrow hero-prev" aria-label="<?php echo esc_attr(jc_t('Previous slide')); ?>"><?php echo jc_icon('arrow-left'); ?></button>
    <button class="hero-arrow hero-next" aria-label="<?php echo esc_attr(jc_t('Next slide')); ?>"><?php echo jc_icon('arrow-right'); ?></button>
    <div class="hero-dots"></div>
  </section>

  <!-- ===================== PRODUCTS ===================== -->
  <section class="products section" id="products">
    <div class="container">
      <div class="section-head products-head">
        <p class="section-eyebrow"><?php echo esc_html(jc_get('product_section_products_eyebrow', 'Our Hot Products')); ?></p>
        <div class="products-head-row">
          <h2 class="section-title"><?php echo esc_html(jc_get('product_section_products_title', 'Meet Your Products Needs')); ?></h2>
          <?php $jc_pmore = jc_link('product_section_products_more', jc_products_url(), jc_t('Learn More')); ?>
          <a href="<?php echo esc_url($jc_pmore['url']); ?>" class="btn-main ph-more btn-fill"><?php echo esc_html($jc_pmore['text']); ?></a>
        </div>
      </div>
      <ul class="proList">
        <?php
        // 查出全部产品，在 PHP 里按 product_sort 排序，取前 8 个 ID
        $jc_prod_all = new WP_Query(array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        ));
        $jc_top_ids = array();
        if ($jc_prod_all->have_posts()) {
            $jc_all = $jc_prod_all->posts;
            // 排序规则：已填 product_sort 的按数字小在前；未填的排在后面（彼此按发布倒序，新的在前）
            $jc_with_sort = array();
            $jc_no_sort   = array();
            foreach ($jc_all as $jc_p) {
                $jc_s = get_field('product_sort', $jc_p->ID);
                if ($jc_s !== '' && $jc_s !== null && $jc_s !== false) {
                    $jc_with_sort[] = array('id' => $jc_p->ID, 'sort' => (int) $jc_s, 'date' => strtotime($jc_p->post_date));
                } else {
                    $jc_no_sort[] = $jc_p;
                }
            }
            // 已填排序的：product_sort 小在前，同值按新日期在前
            usort($jc_with_sort, function ($a, $b) {
                if ($a['sort'] === $b['sort']) { return $b['date'] <=> $a['date']; }
                return $a['sort'] <=> $b['sort'];
            });
            // 未填排序的：发布时间新在前
            usort($jc_no_sort, function ($a, $b) {
                return strtotime($b->post_date) <=> strtotime($a->post_date);
            });
            // 合并 ID：已填在前、未填在后
            $jc_top_ids = array_merge(
                array_column($jc_with_sort, 'id'),
                array_map(function ($p) { return $p->ID; }, $jc_no_sort)
            );
            $jc_top_ids = array_slice($jc_top_ids, 0, 8);
        }
        // 用标准 WP_Query + post__in 严格按上面的 ID 顺序正式渲染
        if (!empty($jc_top_ids)) {
            $jc_prod = new WP_Query(array(
                'post_type'      => 'product',
                'post__in'       => $jc_top_ids,
                'orderby'        => 'post__in',
                'posts_per_page' => 8,
            ));
            if ($jc_prod->have_posts()) {
                while ($jc_prod->have_posts()) {
                    $jc_prod->the_post();
                    get_template_part('template-parts/product-card');
                }
                wp_reset_postdata();
            }
        }
        // 产品 CPT 还没建任何产品时的兜底：显示静态演示卡片
        else {
            $jc_fb_products = array(
                array('Stainless Steel Forged Ring Blank', 'stainless-steel-forged-ring-blank.jpg'),
                array('Slewing bearing rings', 'slewing-bearing-rings.jpg'),
                array('Forged Ring', 'forged-ring.jpg'),
                array('Spur gear supplier', 'spur-gear-supplier.jpg'),
                array('Helical Gear', 'helical-gear-1.jpg'),
                array('Helical Gear', 'helical-gear-2.jpg'),
                array('Spur gear', 'spur-gear.jpg'),
                array('Bevel gear', 'bevel-gear.jpg'),
            );
            foreach ($jc_fb_products as $jc_fbp) {
                echo '<li class="listBox"><a class="listBoxHref" href="' . esc_url(jc_products_url()) . '">';
                echo '<div class="ImghidCont"><div class="imgHoverAn"><img loading="lazy" src="' . esc_url(get_template_directory_uri() . '/assets/images/products/' . $jc_fbp[1]) . '" alt="' . esc_attr($jc_fbp[0]) . '"></div></div>';
                echo '<div class="listTxt"><div class="textLineP">' . esc_html($jc_fbp[0]) . '</div></div>';
                echo '<div class="listTxt"><span class="card-more card-fill">' . esc_html(jc_t('Learn More')) . ' &gt;&gt;</span></div></a></li>';
            }
        }
        ?>
      </ul>
    </div>
  </section>

  <!-- ===================== TAKE A TOUR ===================== -->
  <section class="tour section" id="tour">
    <div class="container">
      <div class="section-head center">
        <h2 class="section-title"><?php echo esc_html(jc_get('tour_section_tour_title', 'Take a Tour of Our Forging Facility')); ?></h2>
        <p class="section-sub"><?php echo esc_html(jc_get('tour_section_tour_subtitle', 'Step Inside Our Workshop: See Every Stage of the Forging Process')); ?></p>
      </div>
      <div class="tour-slider" id="tourSlider">
        <div class="tour-track" id="tourTrack">
          <?php
          // 6 个 slide：tour_section > slide_1..6 > slide_N_image / slide_N_caption
          // Image 字段留空时回退到主题默认车间图（jc_img 主题路径 fallback）
          $jc_tour_slides = array(
              1 => array('caption' => 'Precision Forging Press Line',   'img' => '/assets/images/workshops/forging-1.png'),
              2 => array('caption' => 'High-Temperature Die Forging',   'img' => '/assets/images/workshops/forging-3.png'),
              3 => array('caption' => 'CNC Precision Machining',        'img' => '/assets/images/workshops/cnc-1.png'),
              4 => array('caption' => 'Automated Turning & Finishing',  'img' => '/assets/images/workshops/cnc-3.png'),
              5 => array('caption' => 'Metallurgical Testing Lab',      'img' => '/assets/images/workshops/testing-2.jpg'),
              6 => array('caption' => 'Quality Assurance & Inspection', 'img' => '/assets/images/workshops/testing-4.jpg'),
          );
          foreach ($jc_tour_slides as $jc_ti => $jc_ts) {
              $jc_t_prefix = 'tour_section_slide_' . $jc_ti . '_';
              $jc_t_img = jc_img($jc_t_prefix . 'image', $jc_ts['img']);
              $jc_t_cap = jc_get($jc_t_prefix . 'caption', $jc_ts['caption']);
              echo '<div class="tour-slide' . ($jc_ti === 1 ? ' is-active' : '') . '">';
              echo '<div class="tour-slide-bg" style="background-image:url(\'' . esc_url($jc_t_img) . '\');"></div>';
              echo '<div class="tour-slide-cap"><span>' . esc_html(str_pad((string)$jc_ti, 2, '0', STR_PAD_LEFT)) . '</span>' . esc_html($jc_t_cap) . '</div>';
              echo '</div>';
          }
          ?>
        </div>
        <button class="tour-arrow tour-prev" aria-label="<?php echo esc_attr(jc_t('Previous')); ?>">&#10094;</button>
        <button class="tour-arrow tour-next" aria-label="<?php echo esc_attr(jc_t('Next')); ?>">&#10095;</button>
        <div class="tour-dots"></div>
      </div>
    </div>
  </section>

  <!-- ===================== ABOUT ===================== -->
  <section class="about" id="about">
    <?php $jc_about_bg = jc_img('about_section_about_bg', '/assets/images/about-bg.jpg'); ?>
    <div class="about-bg" style="background-image:url('<?php echo esc_url($jc_about_bg); ?>');"></div>
    <div class="container">
      <div class="about-text">
        <p class="about-company"><?php echo esc_html(jc_get('about_section_about_company', 'TNSG')); ?></p>
        <?php
        // About 正文：about_section > about_text（Textarea），空行分段由 wpautop 自动处理
        $jc_about_default = 'TNSG was established in 2015 and is a high-tech enterprise integrating R&D, production and sales. The company focuses on the forging processing of high-end automotive gears, wind power gearboxes, mechanical, and marine gear parts, providing raw materials and processing services for precision forgings, gears, bevel gears, spiral gears, reducer gears, planetary gears, transmission shafts, spline shafts, etc. The company\'s registered capital is 1 million RMB, with fixed assets of 20 million RMB. The factory area is 8,000 square meters and the workshop area is 6,000 square meters, including a precision forging workshop and a mechanical processing workshop. The company currently has 55 employees, including 1 senior engineer, 3 mechanical engineers, 1 quality engineer, 8 quality inspectors, and 1 electrical engineer.' . "\n\n" . 'The company\'s marketing network covers all over the country. It provides professional and specialized production for large and medium-sized automotive mainframe manufacturers such as Dongfeng, Jiefang, SAIC, and Shanghai GM, as well as wind turbine gear box manufacturers. The company has lofty aspirations and has won the favor and appreciation of domestic and foreign customers with its high-quality, efficient, energy-saving and high-grade products. Meeting the product demands of different customer groups has always been the driving force that prompts the "TNSG" people to constantly strive for progress.' . "\n\n" . 'The company leads the industry in areas such as energy conservation and environmental protection, steel saving, improvement of production efficiency, and transformation to automated production lines. The precision forged products produced by the company achieve the goals of no or less turning, saving raw materials and energy, and reducing production costs. The material utilization rate is 50% higher than traditional processes, and the comprehensive production cost is reduced by 20%, with an electricity saving rate of over 30%. The "TNSG" people will climb to new heights in continuous self-breakthrough, write a new chapter, and maintain a strong development momentum.' . "\n\n" . 'Quality is the root for the survival of an enterprise, and innovation is the path for its development. Concepts determine the way forward. The company is willing to cooperate sincerely with you, achieving mutual benefit and win-win results, enabling customers to obtain the most excellent product performance, creating the industrial value as you wish, creating excellence together, and continuing to achieve glory.';
        echo wpautop(wp_kses_post(jc_get('about_section_about_text', $jc_about_default)));
        ?>
        <div class="about-stats">
          <?php
          $jc_stats = array(
              1 => array('icon' => 'stat-handshake', 'num' => '200', 'label' => 'TRUSTED PARTNERS',   'type' => 'stat-card-light'),
              2 => array('icon' => 'stat-users',     'num' => '253', 'label' => 'HIGH-QUALITY WORKFORCE', 'type' => 'stat-card-blue'),
              3 => array('icon' => 'stat-veteran',   'num' => '188', 'label' => 'DEDICATED PROFESSIONALS', 'type' => 'stat-card-light'),
          );
          foreach ($jc_stats as $jc_si => $jc_s) {
              echo '<div class="stat-card ' . esc_attr($jc_s['type']) . '">';
              echo '<span class="stat-icon">' . jc_icon($jc_s['icon']) . '</span>';
              echo '<div class="stat-num"><span class="count" data-count="' . esc_attr(jc_get('about_section_stat_' . $jc_si . '_num', $jc_s['num'])) . '">0</span></div>';
              echo '<div class="stat-label">' . esc_html(jc_get('about_section_stat_' . $jc_si . '_label', $jc_s['label'])) . '</div></div>';
          }
          ?>
        </div>
        <?php $jc_about_btn = jc_link('about_section_about_btn', jc_page_url('about_us'), jc_t('Learn More')); ?>
        <a href="<?php echo esc_url($jc_about_btn['url']); ?>" class="btn-main btn-about btn-fill"><?php echo esc_html($jc_about_btn['text']); ?></a>
      </div>
    </div>
  </section>

  <!-- ===================== CERTIFICATE ===================== -->
  <section class="certificates section" id="certificates">
    <div class="container">
      <div class="section-head center">
        <h2 class="section-title" style="color:#101B4D;"><?php echo esc_html(jc_t('Certificate')); ?></h2>
        <p class="section-sub"><?php echo esc_html($hero_company); ?></p>
      </div>
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
                  echo '<figure class="cert-item"><img loading="lazy" src="' . esc_url(get_template_directory_uri() . $jc_c) . '" alt="' .esc_attr(jc_t('Certificate')) .'"></figure>';
              }
          }
          ?>
        </div>
        <button class="slider-arrow cert-prev" aria-label="<?php echo esc_attr(jc_t('Previous')); ?>"><?php echo jc_icon('arrow-left'); ?></button>
        <button class="slider-arrow cert-next" aria-label="<?php echo esc_attr(jc_t('Next')); ?>"><?php echo jc_icon('arrow-right'); ?></button>
      </div>
    </div>
  </section>

  <!-- ===================== APPLICATION ===================== -->
  <section class="application" id="application">
    <div class="section-head">
      <h2 class="section-title" style="color:#101B4D;"><?php echo esc_html(jc_t('application')); ?></h2>
      <p class="section-sub"><?php echo esc_html($hero_company); ?></p>
    </div>
    <div class="app-slider" id="appSlider">
      <div class="app-track" id="appTrack">
        <?php
        // application_section > slide1..5（每张 slide 内含 app_N_bg/title/desc + ind_N_label/icon/icon_white/link）
        $jc_apps = array(
            1 => array('slug' => 'slide1', 'default_bg' => '/assets/images/apps/automotive.jpg',          'title' => 'Automotive Accessories',  'desc' => 'CNC machining is commonly used in the automotive industry. All of our services can be leveraged to meet industry trends like autonomous driving, hybrid drive trains'),
            2 => array('slug' => 'slide2', 'default_bg' => '/assets/images/apps/consumer-electronics.jpg','title' => 'Consumer Electronics',   'desc' => 'The consumer products industry covers the vast majority of products that are used in our everyday lives. VIEW WELL mainly produce high-precision hardware parts for consumer'),
            3 => array('slug' => 'slide3', 'default_bg' => '/assets/images/apps/industrial.jpg',          'title' => 'Industrial Product',     'desc' => 'we have the expertise and equipment needed to deliver high-quality precision machined parts and products for a wide range of applications, including those within the'),
            4 => array('slug' => 'slide4', 'default_bg' => '/assets/images/apps/telecom.jpg',             'title' => 'Telecommunication Parts','desc' => 'Many telecommunication products molding rely on computer numeric control (CNC) machining and milling to ensure optimal precision and consistency.'),
            5 => array('slug' => 'slide5', 'default_bg' => '/assets/images/apps/medical.jpg',             'title' => 'Medical Parts',          'desc' => 'Our experience in manufacturing solutions for medical devices allows us to meet the health and fitness sectors\' stringent and unique needs. We are a quality-driven'),
        );
        $jc_ai = 0;
        foreach ($jc_apps as $jc_a => $jc_app) {
            $jc_ai++;
            $jc_app_prefix = 'application_section_' . $jc_app['slug'] . '_';
            $jc_app_bg = jc_img($jc_app_prefix . 'app_' . $jc_a . '_bg', $jc_app['default_bg']);
            $jc_app_ttl = jc_get($jc_app_prefix . 'app_' . $jc_a . '_title', $jc_app['title']);
            $jc_app_dsc = jc_get($jc_app_prefix . 'app_' . $jc_a . '_desc', $jc_app['desc']);
            echo '<div class="app-slide' . ($jc_ai === 1 ? ' app-slide-active' : '') . '">';
            echo '<div class="app-bg" style="background-image:url(\'' . esc_url($jc_app_bg) . '\');"></div>';
            echo '<div class="app-content">';
            echo '<h3>' . esc_html($jc_app_ttl) . '</h3>';
            echo '<p>' . esc_html($jc_app_dsc) . '</p>';
            echo '<a href="' . esc_url(jc_products_url()) . '" class="btn-app">' . esc_html(jc_t('View more')) . ' &gt;&gt;</a>';
            echo '</div></div>';
        }
        ?>
      </div>

      <!-- 5 industry tabs control the slider above -->
      <div class="container">
        <ul class="industry-grid" id="industryTabs">
          <?php
          // 行业标签同样来自 application_section > slideN > ind_N_label/icon/icon_white/link
          $jc_inds = array(
              1 => array('slug' => 'slide1', 'default_label' => 'Automotive Manufacturing',          'icon' => 'auto',      'link' => ''),
              2 => array('slug' => 'slide2', 'default_label' => 'Construction Machinery',           'icon' => 'machinery', 'link' => ''),
              3 => array('slug' => 'slide3', 'default_label' => 'Petrochemicals',                   'icon' => 'petro',     'link' => ''),
              4 => array('slug' => 'slide4', 'default_label' => 'Wind Power Generation',            'icon' => 'wind',      'link' => ''),
              5 => array('slug' => 'slide5', 'default_label' => 'Shipbuilding and Marine Engineering','icon' => 'ship',    'link' => ''),
          );
          foreach ($jc_inds as $jc_ii => $jc_ind) {
              $jc_ind_prefix = 'application_section_' . $jc_ind['slug'] . '_';
              $jc_ilink = jc_link($jc_ind_prefix . 'ind_' . $jc_ii . '_link', jc_products_url(), $jc_ind['default_label']);
              $jc_ind_label = jc_get($jc_ind_prefix . 'ind_' . $jc_ii . '_label', $jc_ind['default_label']);
              $jc_ind_icon = jc_img($jc_ind_prefix . 'ind_' . $jc_ii . '_icon', '/assets/images/icons/' . $jc_ind['icon'] . '.png');
              $jc_ind_icon_w = jc_img($jc_ind_prefix . 'ind_' . $jc_ii . '_icon_white', '/assets/images/icons/' . $jc_ind['icon'] . '-white.png');
              echo '<li class="industry-item" data-index="' . esc_attr($jc_ii - 1) . '">';
              $jc_ind_aria = sprintf(jc_t('View %s products'), $jc_ind_label);
              echo '<a href="' . esc_url($jc_ilink['url']) . '" class="industry-link" aria-label="' . esc_attr($jc_ind_aria) . '">';
              echo '<div class="industry-icon">';
              echo '<img src="' . esc_url($jc_ind_icon) . '" alt="" class="icon-normal">';
              echo '<img src="' . esc_url($jc_ind_icon_w) . '" alt="' . esc_attr($jc_ind_label) . '" class="icon-white">';
              echo '</div>';
              echo '<p>' . esc_html($jc_ind_label) . '</p>';
              echo '</a></li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </section>

  <!-- ===================== CUSTOMERS ===================== -->
  <section class="customers section" id="customers">
    <div class="container">
      <div class="section-head center">
        <h2 class="section-title"><?php echo esc_html(jc_get('customers_section_customers_title', 'Customers')); ?></h2>
        <p class="section-sub"><?php echo esc_html(jc_get('customers_section_customers_subtitle', 'We work for some of the leading companies in the mobility, energy, machinery and engineering sectors.')); ?></p>
      </div>
      <ul class="customers-grid">
        <?php
        // 10 个客户 logo：customers_section > logo_1..logo_10（Image，无默认图）
        // Image 留空时回退占位文字 Customer 01..10，页面不散架
        for ($jc_li = 1; $jc_li <= 10; $jc_li++) {
            $jc_logo = function_exists('get_field')
                ? get_field('customers_section_logo_' . $jc_li, jc_field_id('customers_section_logo_' . $jc_li))
                : '';
            if (is_array($jc_logo) && !empty($jc_logo['url'])) { $jc_logo = $jc_logo['url']; }
            $jc_has = ($jc_logo !== '' && $jc_logo !== null && $jc_logo !== false);
            $jc_name = 'Customer ' . str_pad((string)$jc_li, 2, '0', STR_PAD_LEFT);
            echo '<li>';
            if ($jc_has) {
                echo '<img class="cus-logo" src="' . esc_url($jc_logo) . '" alt="' . esc_attr($jc_name) . '" loading="lazy">';
            } else {
                echo '<span class="cus-logo" data-name="' . esc_attr($jc_name) . '">' . esc_html($jc_name) . '</span>';
            }
            echo '</li>';
        }
        ?>
      </ul>
    </div>
  </section>

  <!-- ===================== OUR FACILITIES ===================== -->
  <section class="facility section" id="facility">
    <div class="container">
      <div class="section-head center">
        <h2 class="section-title"><?php echo esc_html(jc_get('facility_section_facility_title', 'Our Facilities')); ?></h2>
        <p class="section-sub"><?php echo esc_html(jc_get('facility_section_facility_subtitle', 'A Vertically Integrated Forging Facility from Raw Material to Finished Part')); ?></p>
      </div>
    </div>
    <div class="facility-accordion" id="facilityAccordion">
      <?php
      // 4 个设施：facility_section > item_1..4 > item_N_name / item_N_image / item_N_desc / item_N_link（Link 字段，文字填在 Title）
      $jc_facilities = array(
          1 => array('name' => 'Forging Workshop',       'img' => '/assets/images/workshops/forging-2.png', 'desc' => 'Equipped with advanced forging presses for producing high-precision automotive and industrial forgings.'),
          2 => array('name' => 'CNC Machining',          'img' => '/assets/images/workshops/cnc-2.png',      'desc' => 'Multi-axis CNC centers ensure tight tolerances and consistent quality on every finished part.'),
          3 => array('name' => 'Die & Tooling',          'img' => '/assets/images/workshops/forging-4.png',  'desc' => 'In-house die design and maintenance shorten lead times and improve forging repeatability.'),
          4 => array('name' => 'Testing & Validation',   'img' => '/assets/images/workshops/testing-3.jpg',  'desc' => 'Laboratory testing validates material properties and product integrity at every stage.'),
      );
      foreach ($jc_facilities as $jc_fi => $jc_fc) {
          $jc_f_prefix = 'facility_section_item_' . $jc_fi . '_';
          $jc_f_img  = jc_img($jc_f_prefix . 'image', $jc_fc['img']);
          $jc_f_name = jc_get($jc_f_prefix . 'name', $jc_fc['name']);
          $jc_f_desc = jc_get($jc_f_prefix . 'desc', $jc_fc['desc']);
          $jc_f_link = jc_link($jc_f_prefix . 'link', jc_home_url(), 'Explore Now');
          echo '<div class="facility-item' . ($jc_fi === 1 ? ' is-active' : '') . '" data-facility="' . esc_attr($jc_fi) . '">';
          echo '<div class="facility-item-bg" style="background-image:url(\'' . esc_url($jc_f_img) . '\');"></div>';
          echo '<div class="facility-item-content">';
          echo '<h3 class="facility-item-title">' . esc_html($jc_f_name) . '</h3>';
          echo '<p class="facility-item-desc">' . esc_html($jc_f_desc) . '</p>';
          echo '<a href="' . esc_url($jc_f_link['url']) . '" class="facility-item-btn btn-main btn-fill">' . esc_html($jc_f_link['text']) . ' &rarr;</a>';
          echo '</div></div>';
      }
      ?>
    </div>
  </section>

  <!-- ===================== NEWS ===================== -->
  <section class="news section" id="news">
    <div class="container">
      <div class="section-head center">
        <h2 class="section-title" style="color:#101B4D;"><?php echo esc_html(jc_t('News')); ?></h2>
        <p class="section-sub"><?php echo esc_html($hero_company); ?></p>
      </div>
      <ul class="newsList">
        <?php
        $jc_news = new WP_Query(array(
            'post_type'      => 'post',
            'posts_per_page' => 3,
        ));
        if ($jc_news->have_posts()) {
            while ($jc_news->have_posts()) {
                $jc_news->the_post();
                $jc_news_img = get_the_post_thumbnail_url(get_the_ID(), 'jc-card');
                if (!$jc_news_img) {
                    $jc_news_img = get_template_directory_uri() . '/assets/images/news/news-' . ($jc_news->current_post % 3 + 1) . '.jpg';
                }
                echo '<li class="newsBox">';
                echo '<a href="' . esc_url(get_permalink()) . '">';
                echo '<div class="newsImg"><img loading="lazy" src="' . esc_url($jc_news_img) . '" alt="' . esc_attr(get_the_title()) . '"></div>';
                echo '<div class="newsDate">' . esc_html(get_the_date()) . '</div>';
                echo '<h3>' . esc_html(get_the_title()) . '</h3>';
                echo '<p class="newsDesc">' . esc_html(get_the_excerpt()) . '</p>';
                echo '<span class="newsMore news-fill">' . esc_html(jc_t('Learn More')) . ' +</span>';
                echo '</a></li>';
            }
            wp_reset_postdata();
        }
        // 文章还没发布时的兜底：显示静态演示新闻
        else {
            $jc_fb_news = array(
                array('news-1.jpg', 'Jun 22, 2026', 'Batch delivery of large slewing bearing rings and gear rings to customers in the construction machinery industry.', 'Jiucheng Forging continues to expand its applications in the construction machinery sector, recently delivering a batch of large slewing bearing rings and matching gear ring forgings to a prominent domestic crawler crane manufacturer.'),
                array('news-2.jpg', 'Jun 22, 2026', 'Successfully developed custom-shaped stainless steel forgings for a German client, earning recognition for custom service capabilities.', 'Leveraging 20 years of forging expertise and an agile technical team, Jiucheng Forging recently completed the custom development of a complex, irregularly shaped stainless steel forging for a German industrial equipment manufacturer.'),
                array('news-3.jpg', 'Jun 22, 2026', 'Service Process', 'The client provides product drawings, samples, or technical specifications (material, dimensions, quantity, surface treatment, etc.). Our technical team evaluates manufacturing feasibility and provides a preliminary proposal and quotation within 24 hours.'),
            );
            foreach ($jc_fb_news as $jc_fbn) {
                echo '<li class="newsBox"><a href="' . esc_url($news_url) . '">';
                echo '<div class="newsImg"><img loading="lazy" src="' . esc_url(get_template_directory_uri() . '/assets/images/news/' . $jc_fbn[0]) . '" alt=""></div>';
                echo '<div class="newsDate">' . esc_html($jc_fbn[1]) . '</div>';
                echo '<h3>' . esc_html($jc_fbn[2]) . '</h3>';
                echo '<p class="newsDesc">' . esc_html($jc_fbn[3]) . '</p>';
                echo '<span class="newsMore news-fill">' . esc_html(jc_t('Learn More')) . ' +</span></a></li>';
            }
        }
        ?>
      </ul>
    </div>
  </section>

  <!-- ===================== OUR SERVICE ===================== -->
  <section class="service section" id="service">
    <div class="container">
      <div class="section-head center">
        <h2 class="section-title" style="color:#101B4D;"><?php echo esc_html(jc_t('Our service')); ?></h2>
        <p class="section-sub"><?php echo esc_html($hero_company); ?></p>
      </div>
      <div class="service-grid">
        <?php
        // CTA 联系卡：service_section > svc_cta_bg / svc_cta_text / svc_cta_link
        $jc_cta = jc_link('service_section_svc_cta_link', jc_page_url('contact_us'), jc_t('Contact Us'));
        $jc_cta_bg = jc_img('service_section_svc_cta_bg', '/assets/images/service-bg.jpg');
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
            $jc_slink = jc_link($jc_svc_prefix . 'svc_' . $jc_si . '_link', jc_page_url($jc_svc['url']), jc_t('Learn More'));
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
