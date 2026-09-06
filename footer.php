<?php
/**
 * Footer — 公共底部
 * 包含 <footer>…</footer>、悬浮侧栏、弹窗、移动端底栏、</body></html>（按笔记约定）
 * 钩子：wp_footer（页尾 JS / 表单脚本 / 统计代码）
 * 联系方式全部来自公共字段（首页上创建）：company_email / company_whatsapp / company_phone
 */
?>
  <!-- ===================== FOOTER ===================== -->
  <footer id="SITE_FOOTER" class="no-response">
    <div class="container footer-top">
      <div class="footer-logo">
        <a href="<?php echo esc_url(home_url('/')); ?>" title="HOME" class="footer-logo-link">
          <?php if (function_exists('the_custom_logo') && has_custom_logo()) {
              the_custom_logo();
          } else { ?>
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="<?php bloginfo('name'); ?>" width="362" height="407">
          <?php } ?>
        </a>
      </div>
      <p class="footer-slogan"><?php echo esc_html(jc_get('company_slogan', 'Looking ahead, we will continue to uphold the philosophy of "professional export, value delivery".')); ?></p>

      <div class="footer-contacts">
        <div class="footer-contact">
          <?php echo jc_icon('mail'); ?>
          <div class="footer-contact-info">
            <span>email</span>
            <a href="mailto:<?php echo esc_attr(jc_get('company_email', 'jiucheng@jcforging.com')); ?>"><?php echo esc_html(jc_get('company_email', 'jiucheng@jcforging.com')); ?></a>
          </div>
        </div>
        <div class="footer-contact">
          <?php echo jc_icon('whatsapp'); ?>
          <div class="footer-contact-info">
            <span>Whatsapp</span>
            <a href="<?php echo esc_url(jc_wa_url()); ?>"><?php echo esc_html('+' . preg_replace('/\D+/', '', jc_get('company_whatsapp', '8618596356103'))); ?></a>
          </div>
        </div>
        <div class="footer-contact">
          <?php echo jc_icon('phone'); ?>
          <div class="footer-contact-info">
            <span>Phone</span>
            <a href="tel:+<?php echo esc_attr(preg_replace('/\D+/', '', jc_get('company_phone', '8618596356103'))); ?>"><?php echo esc_html('+' . preg_replace('/\D+/', '', jc_get('company_phone', '8618596356103'))); ?></a>
          </div>
        </div>
      </div>

      <div class="footer-columns">
        <div class="footer-col">
          <h4>Quick Links</h4>
          <?php
          if (has_nav_menu('footer_menu')) {
              wp_nav_menu(array(
                  'theme_location' => 'footer_menu',
                  'container'      => false,
                  'menu_class'     => 'footer-menu-list',
                  'depth'          => 1,
                  'fallback_cb'    => false,
              ));
          } else { ?>
          <ul>
            <li><a href="<?php echo esc_url(home_url('/')); ?>">HOME</a></li>
            <li><a href="<?php echo esc_url(jc_page_url('about_us')); ?>">ABOUT US</a></li>
            <li><a href="<?php echo esc_url(jc_products_url()); ?>">PRODUCTS</a></li>
            <li><a href="<?php echo esc_url(jc_page_url('faq')); ?>">FAQ</a></li>
            <li><a href="<?php echo esc_url(jc_page_url('news')); ?>">NEWS</a></li>
            <li><a href="<?php echo esc_url(jc_page_url('contact-us')); ?>">CONTACT US</a></li>
            <li><a href="#" >Resources</a></li>
          </ul>
          <?php } ?>
        </div>
        <div class="footer-col">
          <h4>Product</h4>
          <ul>
            <?php
            // 一级产品分类（后台 Products → Product Categories 里建）
            $jc_terms = get_terms(array(
                'taxonomy'   => 'product_category',
                'parent'     => 0,
                'hide_empty' => false,
                'number'     => 9,
            ));
            if (!empty($jc_terms) && !is_wp_error($jc_terms)) {
                // 按 ACF 里建的 sort_order 字段排序（数字小的在前，未填的排最后按名称）
                usort($jc_terms, function ($a, $b) {
                    $sa = (int) get_term_meta($a->term_id, 'sort_order', true);
                    $sb = (int) get_term_meta($b->term_id, 'sort_order', true);
                    if ($sa === $sb) { return strcmp($a->name, $b->name); }
                    return $sa - $sb;
                });
                foreach ($jc_terms as $jc_term) {
                    echo '<li><a href="' . esc_url(get_term_link($jc_term)) . '">' . esc_html($jc_term->name) . '</a></li>';
                }
            } else {
                // 分类还没建时的兜底（与静态模板一致）
                foreach (array('Forged gear rings','Forged gear shafts','Forged ring parts','Forged cylindrical parts','Forged Ring Blank','Wheel-type forgings','Slewing bearing rings','Flange forgings','Special-shaped forgings') as $jc_label) {
                    echo '<li><a href="' . esc_url(jc_products_url()) . '">' . esc_html($jc_label) . '</a></li>';
                }
            }
            ?>
          </ul>
        </div>
        <div class="footer-col footer-contact-form">
          <h4>Contact Us</h4>
          <?php
          // Footer 表单：已接入 Fluent Forms（表单 ID 1）。
          // 用 shortcode_exists 检测短代码是否注册（比 function_exists 更可靠）
          if (shortcode_exists('fluentform')) {
              echo do_shortcode('[fluentform id="1"]');
          } else { ?>
          <!-- Fluent Forms 未启用时的兜底表单 -->
          <form id="footerForm" action="#" method="post">
            <input type="text" name="name" placeholder="Please enter your name" required>
            <input type="tel" name="phone" placeholder="Please enter your phone number">
            <input type="email" name="email" placeholder="Please enter your email">
            <textarea name="message" rows="3" placeholder="Please enter your message"></textarea>
            <button type="submit" class="btn-submit">Submit</button>
          </form>
          <?php } ?>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="container">
        <p>Copyright &copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?></p>
        <a href="<?php echo esc_url(jc_page_url('privacy-policy')); ?>" class="privacy-link">Privacy Policy</a>
      </div>
    </div>
  </footer>

  <!-- ===================== MOBILE BOTTOM BAR ===================== -->
  <div class="mobile-bottom-bar">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="mb-btn"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 10.5V20h13v-9.5"/></svg><span>Home</span></a>
    <a href="mailto:<?php echo esc_attr(jc_get('company_email', 'jiucheng@jcforging.com')); ?>" class="mb-btn"><?php echo jc_icon('mail'); ?><span>Email</span></a>
    <a href="<?php echo esc_url(jc_wa_url()); ?>" class="mb-btn"><?php echo jc_icon('whatsapp'); ?><span>WhatsApp</span></a>
  </div>

  <!-- ===================== SIDE BAR ===================== -->
  <div class="sideBar" id="sideBar">
    <a href="<?php echo esc_url(jc_wa_url()); ?>" class="sideBtn" title="WhatsApp"><?php echo jc_icon('whatsapp'); ?><span>WhatsApp</span></a>
    <a href="tel:+<?php echo esc_attr(preg_replace('/\D+/', '', jc_get('company_phone', '8618596356103'))); ?>" class="sideBtn" title="Tel"><?php echo jc_icon('phone'); ?><span>Tel</span></a>
    <a href="mailto:<?php echo esc_attr(jc_get('company_email', 'jiucheng@jcforging.com')); ?>" class="sideBtn" title="Email"><?php echo jc_icon('mail'); ?><span>Email</span></a>
    <button class="sideBtn" id="backTop" title="top"><?php echo jc_icon('arrow-up'); ?><span>top</span></button>
  </div>

  <?php
  // 搜索弹窗（searchform.php，get_search_form() 自动加载主题内版本）
  get_search_form();
  ?>

  <!-- ===================== CONTACT MODAL ===================== -->
  <div class="modal" id="contactModal" aria-hidden="true">
    <div class="modal-box contact-modal">
      <button class="modal-close" data-close="contactModal" aria-label="Close">&times;</button>
      <div class="contact-modal-left">
        <h3>Please give us a message</h3>
        <?php
        // 弹窗表单：已接入 Fluent Forms（表单 ID 2）。
        // 用 shortcode_exists 检测短代码是否注册（比 function_exists 更可靠）
        if (shortcode_exists('fluentform')) {
            echo do_shortcode('[fluentform id="2"]');
        } else { ?>
        <form id="contactForm" action="#" method="post">
          <input type="text" name="name" placeholder="Please enter your name" required>
          <input type="tel" name="phone" placeholder="Please enter your phone number">
          <input type="email" name="email" placeholder="Please enter your email">
          <input type="text" name="company" placeholder="Please enter your company name">
          <textarea name="message" rows="4" placeholder="Please enter your message"></textarea>
          <button type="submit" class="btn-submit btn-submit-light">Submit</button>
        </form>
        <?php } ?>
      </div>
      <div class="contact-modal-right">
        <h3 class="contact-modal-company"><?php echo esc_html(jc_get('homepage_slides_hero_company', get_bloginfo('name'))); ?></h3>
        <a class="contact-modal-link" href="tel:+<?php echo esc_attr(preg_replace('/\D+/', '', jc_get('company_phone', '8618596356103'))); ?>"><?php echo jc_icon('phone'); ?><span>Phone ： <?php echo esc_html('+' . preg_replace('/\D+/', '', jc_get('company_phone', '8618596356103'))); ?></span></a>
        <a class="contact-modal-link" href="<?php echo esc_url(jc_wa_url()); ?>" target="_blank" rel="noopener"><?php echo jc_icon('whatsapp'); ?><span>WhatsApp ： <?php echo esc_html('+' . preg_replace('/\D+/', '', jc_get('company_whatsapp', '8618596356103'))); ?></span></a>
        <a class="contact-modal-link" href="mailto:<?php echo esc_attr(jc_get('company_email', 'jiucheng@jcforging.com')); ?>"><?php echo jc_icon('mail'); ?><span>Email ： <?php echo esc_html(jc_get('company_email', 'jiucheng@jcforging.com')); ?></span></a>
      </div>
    </div>
  </div>

</div><!-- /#document -->
<?php wp_footer(); ?>
</body>
</html>
