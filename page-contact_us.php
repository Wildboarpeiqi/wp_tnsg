<?php
/**
 * Template Name: Contact Us
 * page-contact_us.php — 联系我们页
 * WordPress 按页面 slug = contact_us 自动匹配本模板（后台无需手动选模板）；
 * 若 slug 不同，可在后台「页面属性 → 模板」手动选择 "Contact Us"。
 * 静态版对照：E:\jingxiang\site\contact.html
 *
 * 字段读取规则（与《ACF字段清单.md》一致）：
 *  - 本页专属字段（site_banner / contact_eyebrow / contact_title / contact_desc /
 *    contact_form_note / contact_map_embed）挂在 Contact Us 页面自己身上，
 *    用 $page_id 读取（$pf 闭包，空则回退静态默认值）。
 *  - 4 张联系卡的值 = 公共字段页 62（JC_GLOBAL_FIELD_ID）的 company_whatsapp /
 *    company_phone / company_email / company_address（jc_get 自动读取，与
 *    header/footer/弹窗同一套数据，改一处全站生效）。
 *  - 表单：Fluent Form ID=5（用户 2026-09-06 已确认）；未装插件时显示静态占位表单
 *    未装 Fluent Forms 或字段为空时显示静态兜底表单。
 *  - 地图：contact_map_embed 存 iframe 嵌入代码（默认英文版 Google 地图），
 *    换地址只需改该字段。
 * 不散架原则：字段没建/没填 → 回退静态默认内容。
 */
get_header();

$page_id   = get_the_ID();
$theme_uri = get_template_directory_uri();

/* 图片/文件字段取值：兼容 ACF 三种返回格式（图片 ID / 数组 / URL 字符串），其余原样 */
$pimg = function ($v) {
    if (is_numeric($v)) {
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

/* 公共联系信息（页 62，与 header/footer/弹窗同一套） */
$company_name = get_bloginfo('name');
$wa           = jc_get('company_whatsapp', '8618596356103');
$phone        = jc_get('company_phone', '8618596356103');
$email        = jc_get('company_email', 'jiucheng@jcforging.com');
$address      = jc_get('company_address', '7th Floor, Tower A, Xingguang Entrepreneurship Building, Huangshan South Road, Liaocheng Economic Development Zone, Shandong Province');

/* 电话显示格式化：纯数字 → "+86 18596356103"（与静态版一致）；非 86 开头 → "+" + 纯数字 */
$jc_fmt_phone = function ($v) {
    $d = preg_replace('/\D+/', '', $v);
    if ($d === '') { return $v; }
    if (strpos($d, '86') === 0 && strlen($d) > 3) { return '+86 ' . substr($d, 2); }
    return '+' . $d;
};
$wa_display    = $jc_fmt_phone($wa);
$phone_display = $jc_fmt_phone($phone);
$phone_link    = 'tel:+' . preg_replace('/\D+/', '', $phone);

/* ===================== BANNER（背景 = site_banner，标题 = 页面标题，面包屑自动） ===================== */
$banner = $pf('site_banner', '');
if ($banner === '') { $banner = $theme_uri . '/assets/images/contact-banner.jpg'; }
$banner_title = get_the_title();
?>

  <!-- ===================== PAGE BANNER ===================== -->
  <section class="page-banner" style="background-image:url('<?php echo esc_url($banner); ?>');">
    <div class="page-banner-inner">
      <div class="page-banner-title"><?php echo esc_html($banner_title); ?></div>
      <div class="breadcrumb">
        <a href="<?php echo esc_url(jc_home_url()); ?>">HOME</a> &gt;
        <span><?php echo esc_html($banner_title); ?></span>
      </div>
    </div>
  </section>

  <!-- ===================== 标题区（字段：contact_eyebrow / contact_title / contact_desc） ===================== -->
  <?php
  $eyebrow = $pf('contact_eyebrow', 'Contact us');
  $title   = $pf('contact_title', 'Start a collaboration');
  $desc    = $pf('contact_desc', 'Whether you have product inquiries, customization needs, technical consultations, or after-sales support, we will respond as quickly as possible and provide you with professional solutions.');
  ?>
  <section class="contact-head section" id="contactHead">
    <div class="container contact-head-inner">
      <p class="contact-eyebrow"><?php echo jc_icon('mail'); ?><?php echo esc_html($eyebrow); ?></p>
      <h2 class="contact-title"><?php echo esc_html($title); ?></h2>
      <p class="contact-desc"><?php echo esc_html($desc); ?></p>
    </div>
  </section>

  <!-- ===================== 联系卡 4 列（值 = 公共字段页 62） ===================== -->
  <section class="contact-cards" id="contactCards">
    <div class="container contact-cards-grid">

      <!-- WhatsApp -->
      <a class="contact-card" href="<?php echo esc_url(jc_wa_url()); ?>" target="_blank" rel="noopener">
        <span class="contact-card-icon"><?php echo jc_icon('whatsapp'); ?></span>
        <span class="contact-card-name">WhatsApp</span>
        <span class="contact-card-value"><?php echo esc_html($wa_display); ?></span>
      </a>

      <!-- Phone -->
      <a class="contact-card" href="<?php echo esc_url($phone_link); ?>">
        <span class="contact-card-icon"><?php echo jc_icon('phone'); ?></span>
        <span class="contact-card-name">Phone</span>
        <span class="contact-card-value"><?php echo esc_html($phone_display); ?></span>
      </a>

      <!-- Email -->
      <a class="contact-card" href="mailto:<?php echo esc_attr($email); ?>">
        <span class="contact-card-icon"><?php echo jc_icon('mail'); ?></span>
        <span class="contact-card-name">Email</span>
        <span class="contact-card-value"><?php echo esc_html($email); ?></span>
      </a>

      <!-- Address（点击打开 Google 地图定位） -->
      <a class="contact-card" href="https://www.google.com/maps/search/?api=1&amp;query=<?php echo esc_attr(rawurlencode($address)); ?>" target="_blank" rel="noopener">
        <span class="contact-card-icon"><?php echo jc_icon('map-pin'); ?></span>
        <span class="contact-card-name">Address</span>
        <span class="contact-card-value"><?php echo esc_html($address); ?></span>
      </a>

    </div>
  </section>

  <!-- ===================== 主体两列：左询盘表单 + 右地图
       表单 = Fluent Form [fluentform id="5"]（ID 已确认）；未装插件时显示静态占位表单
       地图 = 字段 contact_map_embed（换地址改该字段即可） ===================== -->
  <?php
  $form_note = $pf('contact_form_note', 'Reach out via our contact form or use the details. We’re here to help!');
  $map_embed = $pf('contact_map_embed', '<iframe src="https://maps.google.com/maps?ll=36.453677729319,116.03362175227&amp;z=12&amp;output=embed&amp;hl=en" width="100%" height="100%" style="border:0;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Liaocheng Jiucheng Auto Parts Co., Ltd. location" allowfullscreen></iframe>');
  ?>
  <section class="contact-body section" id="contactBody">
    <div class="container contact-body-grid">

      <!-- 左：表单 -->
      <div class="contact-form-wrap">
        <p class="contact-form-note"><?php echo esc_html($form_note); ?></p>
        <?php
        // ★★★ 表单 ID 待用户确认：联系页新建 Fluent Form 后，把下面 id="5" 换成实际 ID ★★★
        if (shortcode_exists('fluentform')) {
            echo do_shortcode('[fluentform id="5"]');
        } else { ?>
        <!-- Fluent Forms 未启用时的兜底表单 -->
        <form id="contactForm" class="contact-form" action="#" method="post">
          <div class="form-row">
            <input type="text" name="name" placeholder="Name" autocomplete="name">
            <input type="tel" name="phone" placeholder="Phone" autocomplete="tel">
          </div>
          <input type="email" name="email" placeholder="Email" required autocomplete="email">
          <textarea name="message" rows="5" placeholder="Message" required></textarea>
          <button type="submit" class="btn-main contact-submit">Submit</button>
        </form>
        <?php } ?>
      </div>

      <!-- 右：地图（iframe = contact_map_embed；左上浮卡 = 公司名 + company_phone + company_address） -->
      <div class="contact-map">
        <?php echo $map_embed; // 已信任的 embed 代码（ACF textarea 后台） ?>
        <div class="contact-map-card">
          <h3 class="contact-map-company"><?php echo esc_html($company_name); ?></h3>
          <p class="contact-map-row"><?php echo jc_icon('phone'); ?><a href="<?php echo esc_url($phone_link); ?>"><?php echo esc_html($phone_display); ?></a></p>
          <p class="contact-map-row"><?php echo jc_icon('map-pin'); ?><span><?php echo esc_html($address); ?></span></p>
        </div>
      </div>

    </div>
  </section>

<?php get_footer(); ?>
