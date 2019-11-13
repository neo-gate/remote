<?php
mb_language("Japanese");
mb_internal_encoding("UTF-8");
$email = $_POST["email"];
$subject = "【東京リフレ】セラピスト募集";
$body = "名前：".$_POST["name"].
"\n電話番号：".$_POST["tel"].
"\nEmail：".$_POST["email"].
"\n年齢：".$_POST["old"].
"\n面接日時(第1希望)：".$_POST["date1"]."　(".$_POST["time1"].")".
"\n面接日時(第2希望)：".$_POST["date2"]."　(".$_POST["time2"].")".
"\n面接日時(第3希望)：".$_POST["date3"]."　(".$_POST["time3"].")".
"\nその他ご質問：".$_POST["others"];
$to = "job@tokyo-refle.com";
$header = "From: $email\nReply-To: $email\n";
$param = "-fmurase@neo-gate.jp";
if (!mb_send_mail($to, $subject, $body, $header,$param)) {
  exit("error");
}
?>
<!doctype html>
<html>
<head>
  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-T9S7L2Z');</script>
  <!-- End Google Tag Manager -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=640px">
<title>【東京リフレ】応募完了</title>
<meta name="Description" content="" />
<meta name="Keywords" content="" />
<link rel="stylesheet" type="text/css" href="css/thanks.css">
</head>
  <body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T9S7L2Z"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <!--フォーム-->
    <div id="formWrap">
      <img src="img/step_03.gif"/>
      <p class="yoyaku_kakunin">応募完了</p>
      <div align="center" style="background-color: #ffebf0; padding: 24px 0 18px 0; line-height: 140%;">
        <span style="font-weight: bold;">ご応募を受け付けました。</span><br>
        担当者よりお電話もしくはメールにてご連絡させていただきます。<br>
        ご応募ありがとうございました。
      </div>
      <div align="center" style="margin-top: 12px;">
      <a href="http://www.tokyo-refle.com/recruit.php" ><img src="img/button_02.jpg"></a>
      </div>
    </div>
</body>
</html>
