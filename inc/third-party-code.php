<?php
/**
 * Third-party tracking / advertising / verification code
 *
 * 所有第三方统计、广告、验证、埋点代码统一放在这里。
 */


/* ==========================================================================
   1. HEAD
   输出位置：<head> 内靠前位置

   可放：
   Google Analytics
   Google Tag Manager HEAD
   Google Ads
   Meta Pixel
   Microsoft Clarity
   Bing / Microsoft
   网站验证代码
   ========================================================================== */

function jc_third_party_head_code() {
    ?>

    <!-- ==================== HEAD THIRD-PARTY CODE START ==================== -->


    <!-- 第一个 HEAD 第三方代码放这里 -->


    <!-- 第二个 HEAD 第三方代码继续放这里 -->


    <!-- 第三个 HEAD 第三方代码继续放这里 -->


    <!-- ===================== HEAD THIRD-PARTY CODE END ===================== -->

    <?php
}
add_action('wp_head', 'jc_third_party_head_code', 1);


/* ==========================================================================
   2. BODY OPEN
   输出位置：<body> 标签刚打开之后

   可放：
   Google Tag Manager noscript
   明确要求紧跟 <body> 后面的代码
   ========================================================================== */

function jc_third_party_body_open_code() {
    ?>

    <!-- ================= BODY OPEN THIRD-PARTY CODE START ================= -->


    <!-- 第一个 BODY OPEN 第三方代码放这里 -->


    <!-- 第二个 BODY OPEN 第三方代码继续放这里 -->


    <!-- ================== BODY OPEN THIRD-PARTY CODE END ================== -->

    <?php
}
add_action('wp_body_open', 'jc_third_party_body_open_code', 1);


/* ==========================================================================
   3. BODY END / FOOTER
   输出位置：</body> 标签之前

   可放：
   明确要求放在 body 最后的 JS
   在线客服
   转化追踪代码
   其他第三方脚本
   ========================================================================== */

function jc_third_party_footer_code() {
    ?>

    <!-- ================== BODY END THIRD-PARTY CODE START ================== -->


    <!-- 第一个 BODY END 第三方代码放这里 -->


    <!-- 第二个 BODY END 第三方代码继续放这里 -->


    <!-- =================== BODY END THIRD-PARTY CODE END =================== -->

    <?php
}
add_action('wp_footer', 'jc_third_party_footer_code', 100);