<?php
/**
 * Section: Customers — 客户 Logo 墙（首页 + About 页共用）
 *
 * 当前数据：
 * customers_section > customers_title / customers_subtitle / logo_1..10
 *
 * 前端：
 * PC 每页最多 10 个（5 列 × 2 行）
 * 移动端每页最多 12 个（3 列 × 4 行）
 *
 * 调用：
 * get_template_part('template-parts/section-customers')
 */
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

            <?php
            for ($jc_li = 1; $jc_li <= 10; $jc_li++) {

                $jc_logo = function_exists('get_field')
                    ? get_field(
                        'customers_section_logo_' . $jc_li,
                        jc_field_id('customers_section_logo_' . $jc_li)
                    )
                    : '';

                if (
                    is_array($jc_logo)
                    && !empty($jc_logo['url'])
                ) {
                    $jc_logo = $jc_logo['url'];
                }

                $jc_has = (
                    $jc_logo !== ''
                    && $jc_logo !== null
                    && $jc_logo !== false
                );

                $jc_name = 'Customer '
                    . str_pad(
                        (string) $jc_li,
                        2,
                        '0',
                        STR_PAD_LEFT
                    );
                ?>

                <li>
                  <?php if ($jc_has) : ?>

                    <img
                      class="cus-logo"
                      src="<?php echo esc_url($jc_logo); ?>"
                      alt="<?php echo esc_attr($jc_name); ?>"
                      loading="lazy"
                      draggable="false"
                    >

                  <?php else : ?>

                    <span
                      class="cus-logo"
                      data-name="<?php echo esc_attr($jc_name); ?>"
                    >
                      <?php echo esc_html($jc_name); ?>
                    </span>

                  <?php endif; ?>
                </li>

            <?php } ?>

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