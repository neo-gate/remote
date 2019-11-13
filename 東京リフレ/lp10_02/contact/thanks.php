<?php
  mb_language("Japanese");
  mb_internal_encoding("UTF-8");
  $email = $_POST["email"];
  $subject = "【東京リフレ】予約フォーム(LP)";
  $body = "ご利用日：".$_POST["day"].
  "\nご利用予定時間：".$_POST["time"].
  "\nご利用予定コース：".$_POST["course"]."分\nご指名セラピスト：".$_POST["therapist"].
  "\nお名前：".$_POST["name"].
  "\nEmail：".$_POST["email"].
  "\n電話番号：".$_POST["tel"].
  "\n住所：".$_POST["address"].
  "\nその他ご要望：".$_POST["otherwise"];
  $to = "order@tokyo-refle.com";
  $header = "From: $email\nReply-To: $email\n";
  $param = "-fmurase@neo-gate.jp";
  if (!mb_send_mail($to, $subject, $body, $header,$param)) {
    exit("mail_error");
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
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>完了ページ｜出張マッサージ【東京リフレ】</title>
  </head>
  <body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T9S7L2Z"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <section>
      <div class="container">
        <div class="progress my-5">
          <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%">入力</div>
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="34" aria-valuemin="0" aria-valuemax="100" style="width: 34%">確認</div>
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%">完了</div>
        </div>
        <div class="row">
          <p class="w-100 mx-auto text-center h4 pb-4">ご予約ありがとうございます。</p>
        </div>
        <div class="row">
          <p class="w-100 mx-auto text-center">すぐにご予約内容の確認メールを送らせていただきます</p>
          <p class="w-100 px-2 text-center">※お急ぎの場合は下記、電話番号までお電話ください。</p>
          <p class="w-100 mx-auto text-center"><a href="tel:03-5206-5134">TEL:03-5206-5134</a></p>
        </div>
        <div class="row">
          <a href="../" class="mx-auto d-block mb-4"><button type="submit" class="btn btn-primary">サイトトップへ</button></a>
        </div>
      </div>
    </section>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!--
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
