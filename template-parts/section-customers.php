<?php
/**
 * Section: Customers — 客户 logo 墙（首页 + About 页共用）
 * 字段：customers_section > customers_title / customers_subtitle / logo_1..10（Image，无默认图）
 * 读取方式：get_field + jc_field_id（自动读当前语言首页字段；logo 留空回退占位文字 Customer NN）
 * 调用：get_template_part('template-parts/section-customers')
 * 背景：默认白色（style.css .customers）
 */
?>
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
