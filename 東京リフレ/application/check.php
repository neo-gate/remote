<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=750px, user-scalable=no, target-densitydpi=device-dpi">
    <link rel="stylesheet" type="text/css" href="css/check.css">
    <script src="js/jquery-3.1.1.min.js"></script>
    <title>【東京リフレ】確認画面</title>
  </head>
  <body>
    <!--フォーム-->
    <div id="formWrap">
      <div class="step_01">
        <input type="button" value="" id="return-btn" onclick="history.back()">
      </div>
      <img src="img/step_02.gif"/>
      <p class="yoyaku_kakunin">応募内容の確認</p>
      <form method="post" action="thanks.php">
        <div class="formTable">
          <div>
            <p class="box_name">お名前</p>
            <div class="txt">
              <?php echo $_POST['name']; ?>
              <input type="hidden" name="name" value="<?php echo $_POST['name']; ?>">
            </div>
          </div>
          <br clear="left">
          <div>
            <p  class="box_tel">電話番号</p>
            <div class="txt1">
              <?php echo $_POST['tel']; ?>
              <input type="hidden" name="tel" value="<?php echo $_POST['tel']; ?>">
            </div>
          </div>
          <br clear="left">
          <div>
            <p class="box_email">メール</p>
            <div class="txt">
              <?php echo $_POST['email']; ?>
              <input type="hidden" name="email" value="<?php echo $_POST['email']; ?>"/>
            </div>
          </div>
          <br clear="left">
          <div>
            <p class="box_old">年齢</p>
            <div class="txt1">
              <?php echo $_POST['old']; ?>
              <input type="hidden" name="old" value="<?php echo $_POST['old']; ?>"/>
            </div>
          </div>
          <br clear="left">
          <div>
            <p class="box_date">　　　　　　</p>
            <div class="txt">
              第1希望:<?php echo $_POST['date1']; ?>　<?php echo $_POST['time1']; ?>~
              <input type="hidden" name="date1" value="<?php echo $_POST['date1']; ?>"/>
              <input type="hidden" name="time1" value="<?php echo $_POST['time1']; ?>"/>
            </div>
          </div>
          <br clear="left">
          <div>
            <p class="box_date">面接希望日時</p>
            <div class="txt1">
              第2希望:<?php echo $_POST['date2']; ?>　<?php echo $_POST['time2']; ?>~
              <input type="hidden" name="date2" value="<?php echo $_POST['date2']; ?>"/>
              <input type="hidden" name="time2" value="<?php echo $_POST['time2']; ?>"/>
            </div>
          </div>
          <br clear="left">
          <div>
            <p class="box_date">　　　　　　</p>
            <div class="txt">
              第3希望:<?php echo $_POST['date3']; ?>　<?php echo $_POST['time3']; ?>~
              <input type="hidden" name="date3" value="<?php echo $_POST['date3']; ?>"/>
              <input type="hidden" name="time3" value="<?php echo $_POST['time3']; ?>"/>
            </div>
          </div>
          <br clear="left">
          <div class="others_bk clearfix">
            <p class="box_others">備考</p>
            <div class="txt2">
              <p><?php echo $_POST['others']; ?></p>
              <input type="hidden" name="others" value="<?php echo $_POST['others']; ?>"/>
            </div>
          </div>
        </div>
          <input type="submit" value="" id="submit_btn"/>
      </form>
    </div>
  </body>
</html>
