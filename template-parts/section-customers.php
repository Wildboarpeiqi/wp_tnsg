<?php
/**
 * Section: Customers — 客户 Logo 墙（首页 + About 页共用）
 *
 * 标题 / 副标题：
 * 从当前语言首页 ACF customers_section 读取。
 *
 * 客户数据：
 * Customer CPT
 * - Title          = 客户名称
 * - Featured Image = 客户 Logo
 * - customer_sort  = 显示顺序
 *
 * 前端分页：
 * PC 每页最多 10 个（5 列 × 2 行）
 * 移动端每页最多 12 个（3 列 × 4 行）
 *
 * 调用：
 * get_template_part('template-parts/section-customers')
 */


/* =========================================================
   Customer CPT
   ========================================================= */

$jc_customers = get_posts(array(
    'post_type'      => 'customer',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'no_found_rows'  => true,
));


/*
 * 排序规则：
 *
 * 1. customer_sort 数字越小越靠前
 * 2. 未填写 customer_sort 的客户排到最后
 * 3. 排序值相同时按客户名称排序
 */
usort($jc_customers, function ($a, $b) {

    $a_sort = get_post_meta(
        $a->ID,
        'customer_sort',
        true
    );

    $b_sort = get_post_meta(
        $b->ID,
        'customer_sort',
        true
    );

    $a_sort = (
        $a_sort !== ''
        && is_numeric($a_sort)
    )
        ? (int) $a_sort
        : PHP_INT_MAX;

    $b_sort = (
        $b_sort !== ''
        && is_numeric($b_sort)
    )
        ? (int) $b_sort
        : PHP_INT_MAX;

    if ($a_sort !== $b_sort) {
        return $a_sort <=> $b_sort;
    }

    return strcasecmp(
        $a->post_title,
        $b->post_title
    );
});


/*
 * 没有 Customer 时不输出整个板块。
 */
if (empty($jc_customers)) {
    return;
}
?>

<!-- ===================== CUSTOMERS ===================== -->
<section class="customers section" id="customers">
  <div class="container">

    <div class="section-head center">

      <h2 class="section-title">
        <?php
        echo esc_html(
            jc_get(
                'customers_section_customers_title',
                'Customers'
            )
        );
        ?>
      </h2>

      <p class="section-sub">
        <?php
        echo esc_html(
            jc_get(
                'customers_section_customers_subtitle',
                'We work for some of the leading companies in the mobility, energy, machinery and engineering sectors.'
            )
        );
        ?>
      </p>

    </div>


    <div class="customers-slider">

      <div class="customers-viewport">

        <div class="customers-track">

          <ul class="customers-grid">

            <?php foreach ($jc_customers as $jc_customer) : ?>

              <?php
              $jc_logo_id = get_post_thumbnail_id(
                  $jc_customer->ID
              );

              /*
               * 没有特色图片的 Customer 不输出，
               * 避免前端出现空白卡片。
               */
              if (!$jc_logo_id) {
                  continue;
              }

              $jc_customer_name = get_the_title(
                  $jc_customer->ID
              );

              /*
               * 优先使用媒体库中的 Alt Text；
               * 没填写时使用 Customer 标题。
               */
              $jc_logo_alt = get_post_meta(
                  $jc_logo_id,
                  '_wp_attachment_image_alt',
                  true
              );

              if ($jc_logo_alt === '') {
                  $jc_logo_alt = $jc_customer_name;
              }
              ?>

              <li>

                <?php
                echo wp_get_attachment_image(
                    $jc_logo_id,
                    'full',
                    false,
                    array(
                        'class'     => 'cus-logo',
                        'alt'       => $jc_logo_alt,
                        'loading'   => 'lazy',
                        'draggable' => 'false',
                    )
                );
                ?>

              </li>

            <?php endforeach; ?>

          </ul>

        </div>

      </div>


      <div class="customers-nav" hidden>

        <button
          type="button"
          class="customers-arrow customers-prev"
          aria-label="<?php echo esc_attr(jc_t('Previous customers')); ?>"
        >
          <?php echo jc_icon('arrow-left'); ?>
        </button>

        <button
          type="button"
          class="customers-arrow customers-next"
          aria-label="<?php echo esc_attr(jc_t('Next customers')); ?>"
        >
          <?php echo jc_icon('arrow-right'); ?>
        </button>

      </div>

    </div>

  </div>
</section>