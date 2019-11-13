<?php
include('../module/common/function.php');
$now = date('Ymd');
$area = "tokyo";
$now_tp = now_tp($now, $area);
/*
echo('<pre>');
var_dump($now_tp);
echo('</pre>');
*/
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
    <title>入力ページ｜出張マッサージ【東京リフレ】</title>
    <meta name="google" content="notranslate" />
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
        </div>
        <p class="text-center h6">※個人情報については予約にのみ使用します。</p>
        <!--フォーム-->
        <form method="post" action="check.php">
          <div class="form-group row">
            <label for="inputDay" class="col-sm-4 col-form-label font-weight-bold">ご利用日<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-4">
              <select class="form-control" id="day" name="day">
                <option value="-1">選択してください</option>
                <?php for ($i=0; $i<30; $i++) { ?> 
                <option value="<?php echo date("Y/m/d",strtotime("$i day"));?>"><?php echo date("Y/m/d",strtotime("$i day"));?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="inputTime" class="col-sm-4 col-form-label font-weight-bold">ご利用予定時間<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-4">
              <select class="form-control" id="time" name="time">
                <option value="-1">選択してください</option>
                <?php
                $t = strtotime('18:00');
                $j = 1;
                for ($i = 0; $i <= 30 * 2 * 11; $i += 30) {
                  $time_stamp = strtotime("+{$i} minutes", $t);
                  $time = date('H:i', $time_stamp);
                  $now_stamp = strtotime(now);
                  if($time_stamp >= $now_stamp){
                ?>
                <option value="<?php echo $j?>"><?php echo $time;?></option>
                <?php
                  }
                  $j++;
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="inputCourse" class="col-sm-4 col-form-label font-weight-bold">ご利用予定コース<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-4">
              <select class="form-control" id="course" name="course">
                <option value="-1">選択してください</option>
                <?php for ($i=90; $i<=240; $i += 30) { ?> 
                <option value="<?php echo $i;?>"><?php echo $i;?>分コース</option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="inputTherapist" class="col-sm-4 col-form-label font-weight-bold">ご指名セラピスト<span class="small mx-3 p-1 bg-primary text-white ">任意</span></label>
            <div class="col-sm-4">
              <select class="form-control" id="therapist" name="therapist">
                <option value="-1">セラピスト指名なし</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="inputName" class="col-sm-4 col-form-label font-weight-bold">お名前<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-8">
              <input class="form-control" type="name" name="name" id="inputName" placeholder="東京花子" required="">
            </div>
          </div>
          <div class="form-group row">
            <label for="inputEmail" class="col-sm-4 col-form-label font-weight-bold">Email<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-8">
              <input class="form-control" type="email" name="email" id="inputEmail" placeholder="○○○@gmail.com" required="">
            </div>
          </div>
          <div class="form-group row">
            <label for="inputTel" class="col-sm-4 col-form-label font-weight-bold">電話番号<span class="small mx-3 p-1 bg-danger text-white ">必須</span></label>
            <div class="col-sm-8">
              <input class="form-control" type="tel" name="tel" id="inputTel" placeholder="09012345678" required="">
            </div>
          </div>
          <div class="form-group row">
            <label for="inputAddress" class="col-sm-4 col-form-label font-weight-bold">派遣先<span class="small mx-3 p-1 bg-danger text-white ">必須</span><p style="font-size:14px;" class="m-0">※ホテルの場合はホテル名とお部屋番号</p></label>
            <div class="col-sm-8">
              <input class="form-control" type="address" name="address" id="inputAddress" placeholder="○○区△△町12-3　□□マンション　201号室" required="">
            </div>
          </div>
          <div class="form-group row">
            <label for="inputOtherwise" class="col-sm-4 col-form-label font-weight-bold">その他ご要望など<span class="small mx-3 p-1 bg-primary text-white ">任意</span></label>
            <div class="col-sm-8">
              <textarea class="form-control" name="otherwise" placeholder="アロママッサージ希望"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <div class="mx-auto d-block">
              <button type="submit" class="btn btn-primary">内容の確認</button>
            </div>
          </div>
        </form>
      </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="text/javascript" src="../js/form.js"></script>
  </body>
</html>
