<?php
/**
 * Section: Our Facilities — 横向图片手风琴（首页 + About 页共用）
 * 字段：facility_section > facility_title / facility_subtitle / item_1..4 > item_N_name / item_N_image / item_N_desc / item_N_link
 * 读取方式：jc_get() / jc_img() / jc_link()（自动读当前语言首页字段）
 * 交互：main.js 的 facilityAccordion 初始化（公共 JS，无需本文件脚本）
 * 调用：get_template_part('template-parts/section-facility')
 * 背景：默认浅灰（style.css .facility）；About 页桌面白底在 about.css 覆盖
 */
?>
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
        // 子字段名带 item_N_ 前缀（与 hero1 > hero_1_title 同款惯例）
        // 实际 key：facility_section_item_1_item_1_image / item_1_name / item_1_desc / item_1_link
        $jc_f_prefix = 'facility_section_item_' . $jc_fi . '_item_' . $jc_fi . '_';
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
