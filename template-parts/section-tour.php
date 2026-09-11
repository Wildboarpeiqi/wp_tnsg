<?php
/**
 * Section: Take a Tour — 车间实景轮播（首页 + About 页共用）
 * 字段：tour_section > tour_title / tour_subtitle / slide_1..6 > slide_N_image / slide_N_caption
 * 读取方式：jc_get() / jc_img()（自动读当前语言首页字段，Image 留空回退主题默认车间图）
 * 交互：main.js 的 tourSlider 初始化（公共 JS，无需本文件脚本）
 * 调用：get_template_part('template-parts/section-tour')
 * 背景：默认浅灰（style.css .tour）；About 页如需调整在 about.css 覆盖
 */
?>
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
            // 子字段名带 slide_N_ 前缀（与 hero1 > hero_1_title 同款惯例）
            // 实际 key：tour_section_slide_1_slide_1_image / tour_section_slide_1_slide_1_caption
            $jc_t_prefix = 'tour_section_slide_' . $jc_ti . '_slide_' . $jc_ti . '_';
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
