<?php
  $time = $_POST['time'];
  if($time == '1'){
    $time = '18:00';
  }elseif($time == '2'){
    $time = '18:30';
  }elseif($time == '3'){
    $time = '19:00';
  }elseif($time == '4'){
    $time = '19:30';
  }elseif($time == '5'){
    $time = '20:00';
  }elseif($time == '6'){
    $time = '20:30';
  }elseif($time == '7'){
    $time = '21:00';
  }elseif($time == '8'){
    $time = '21:30';
  }elseif($time == '9'){
    $time = '22:00';
  }elseif($time == '10'){
    $time = '22:30';
  }elseif($time == '11'){
    $time = '23:00';
  }elseif($time == '12'){
    $time = '23:30';
  }elseif($time == '13'){
    $time = '0:00';
  }elseif($time == '14'){
    $time = '0:30';
  }elseif($time == '15'){
    $time = '1:00';
  }elseif($time == '16'){
    $time = '1:30';
  }elseif($time == '17'){
    $time = '2:00';
  }elseif($time == '18'){
    $time = '2:30';
  }elseif($time == '19'){
    $time = '3:00';
  }elseif($time == '20'){
    $time = '3:30';
  }elseif($time == '21'){
    $time = '4:00';
  }elseif($time == '22'){
    $time = '4:30';
  }elseif($time == '23'){
    $time = '5:00';
  }
  $therapist = $_POST['therapist'];
  if($therapist == '-1'){
    $therapist = 'セラピスト指名なし';
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
    <title>確認ページ｜出張マッサージ【東京リフレ】</title>
  </head>
  <body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T9S7L2Z"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <section>
      <div class="container">
        <div class="progress my-4">
          <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100" style="width: 33%">入力</div>
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="34" aria-valuemin="0" aria-valuemax="100" style="width: 34%">確認</div>
        </div>
        <!--フォーム-->
        <form method="post" action="thanks.php">
          <div class="form-group row">
            <label for="inputDay" class="col-sm-4 col-form-label font-weight-bold">ご利用日<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-4">
              <?php echo $_POST['day'];?>
              <input type="hidden" name="day" value="<?php echo $_POST['day']; ?>" />
            </div>
          </div>
          <div class="form-group row">
            <label for="inputTime" class="col-sm-4 col-form-label font-weight-bold">ご利用予定時間<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-4">
              <?php echo $time;?>
              <input type="hidden" name="time" value="<?php echo $time; ?>" />
            </div>
          </div>
          <div class="form-group row">
            <label for="inputCourse" class="col-sm-4 col-form-label font-weight-bold">ご利用予定コース<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-4">
              <?php echo $_POST['course'];?>分コース
              <input type="hidden" name="course" value="<?php echo $_POST['course']; ?>" />
            </div>
          </div>
          <div class="form-group row">
            <label for="inputTherapist" class="col-sm-4 col-form-label font-weight-bold">ご指名セラピスト<span class="small mx-3 p-1 bg-primary text-white ">任意</span></label>
            <div class="col-sm-4">
              <?php echo $therapist; ?>
              <input type="hidden" name="therapist" value="<?php echo $therapist; ?>" />
            </div>
          </div>
          <div class="form-group row">
            <label for="inputName" class="col-sm-4 col-form-label font-weight-bold">お名前<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-8">
              <?php echo $_POST['name'];?>
              <input type="hidden" name="name" value="<?php echo $_POST['name']; ?>" />
            </div>
          </div>
          <div class="form-group row">
            <label for="inputEmail" class="col-sm-4 col-form-label font-weight-bold">Email<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-8">
              <?php echo $_POST['email'];?>
              <input type="hidden" name="email" value="<?php echo $_POST['email']; ?>" />
            </div>
          </div>
          <div class="form-group row">
            <label for="inputTel" class="col-sm-4 col-form-label font-weight-bold">電話番号<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-8">
              <?php echo $_POST['tel'];?>
              <input type="hidden" name="tel" value="<?php echo $_POST['tel']; ?>" />
            </div>
          </div>
          <div class="form-group row">
            <label for="inputAddress" class="col-sm-4 col-form-label font-weight-bold">派遣先<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-8">
              <?php echo $_POST['address'];?>
              <input type="hidden" name="address" value="<?php echo $_POST['address']; ?>" />
            </div>
          </div>
          <div class="form-group row">
            <label for="inputOtherwise" class="col-sm-4 col-form-label font-weight-bold">その他ご要望など<span class="small mx-3 p-1 bg-primary text-white ">任意</span></label>
            <div class="col-sm-8">
              <?php echo $_POST['otherwise'];?>
              <input type="hidden" name="otherwise" value="<?php echo $_POST['otherwise']; ?>" />
            </div>
          </div>
          <div class="form-group row">
            <div class="mx-auto d-block">
              <button type="submit" class="btn btn-primary">この内容で送信</button>
            </div>
          </div>
        </form>
      </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
</html>
