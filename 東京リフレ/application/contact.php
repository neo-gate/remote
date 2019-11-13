<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=750px, user-scalable=no, target-densitydpi=device-dpi">
<title>【東京リフレ】応募フォーム</title>
<meta name="Description" content="" />
<meta name="Keywords" content="" />
<link rel="stylesheet" type="text/css" href="css/contact.css">
</head>
<body>
<img src="img/step.gif">
<div id="formWrap">
  <form method="post" action="check.php">
    <div class="formTable">
			<div class="box_name">
				<p class="txt">お名前<span class="hissu">必須</span></p>
				<input type="text" name="name" value=""  placeholder="例:東京花子" required>
			</div>
			<div class="box_tel">
				<p class="txt">電話番号<span class="hissu">必須</span></p>
				<input type="tel" name="tel" value=""  placeholder="例:09012345678" required>
			</div>
			<div class="box_email">
				<p class="txt">Email<span class="hissu">必須</span></p>
				<input type="email" name="email" value=""  placeholder="例:job@tokyo-refle.com" required>
			</div>
			<div class="box_old">
				<p class="txt">年齢<span class="nimni">任意</span></p>
				<input type="number" name="old" value=""  placeholder="例:28">
			</div>
			<div class="box_date">
				<p>面接希望日時<span class="nimni">任意</span></p>
			</div>
			<div style="float:left;">
			<div class="box">
				<p><span class="hope_day">第1希望</span><br>
          <select class="form-control" name="date1">
            <option>選択してください</option>
            <option><?php echo date("Y/m/d",strtotime("0 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("1 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("2 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("3 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("4 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("5 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("6 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("7 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("8 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("9 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("10 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("11 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("12 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("13 day"));?></option>
          </select>
				</p>
				<p>
					<select name="time1">
		        <option value="">選択してください</option>
		        <option value="10:00">10:00</option>
						<option value="10:30">10:30</option>
						<option value="11:00">11:00</option>
						<option value="11:30">11:30</option>
						<option value="12:00">12:00</option>
						<option value="12:30">12:30</option>
						<option value="13:00">13:00</option>
						<option value="13:30">13:30</option>
						<option value="14:00">14:00</option>
						<option value="14:30">14:30</option>
						<option value="15:00">15:00</option>
						<option value="15:30">15:30</option>
						<option value="16:00">16:00</option>
						<option value="16:30">16:30</option>
						<option value="17:00">17:00</option>
						<option value="17:30">17:30</option>
						<option value="18:00">18:00</option>
		        <option value="18:30">18:30</option>
		        <option value="19:00">19:00</option>
		        <option value="19:30">19:30</option>
		        <option value="20:00">20:00</option>
		        <option value="20:30">20:30</option>
		        <option value="21:00">21:00</option>
		        <option value="21:30">21:30</option>
		        <option value="22:00">22:00</option>
		        <option value="22:30">22:30</option>
		        <option value="23:00">23:00</option>
		      </select>
				</p>
			</div>
			<div class="box">
				<p><span class="hope_day">第2希望</span><br>
          <select class="form-control" name="date2">
            <option>選択してください</option>
            <option><?php echo date("Y/m/d",strtotime("0 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("1 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("2 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("3 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("4 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("5 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("6 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("7 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("8 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("9 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("10 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("11 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("12 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("13 day"));?></option>
          </select>
				</p>
				<p>
					<select name="time2">
		        <option value="">選択してください</option>
		        <option value="10:00">10:00</option>
						<option value="10:30">10:30</option>
						<option value="11:00">11:00</option>
						<option value="11:30">11:30</option>
						<option value="12:00">12:00</option>
						<option value="12:30">12:30</option>
						<option value="13:00">13:00</option>
						<option value="13:30">13:30</option>
						<option value="14:00">14:00</option>
						<option value="14:30">14:30</option>
						<option value="15:00">15:00</option>
						<option value="15:30">15:30</option>
						<option value="16:00">16:00</option>
						<option value="16:30">16:30</option>
						<option value="17:00">17:00</option>
						<option value="17:30">17:30</option>
						<option value="18:00">18:00</option>
		        <option value="18:30">18:30</option>
		        <option value="19:00">19:00</option>
		        <option value="19:30">19:30</option>
		        <option value="20:00">20:00</option>
		        <option value="20:30">20:30</option>
		        <option value="21:00">21:00</option>
		        <option value="21:30">21:30</option>
		        <option value="22:00">22:00</option>
		        <option value="22:30">22:30</option>
		        <option value="23:00">23:00</option>
		      </select>
				</p>
			</div>
			<div class="box">
				<p><span class="hope_day">第3希望</span><br>
          <select class="form-control" name="date3">
            <option>選択してください</option>
            <option><?php echo date("Y/m/d",strtotime("0 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("1 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("2 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("3 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("4 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("5 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("6 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("7 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("8 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("9 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("10 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("11 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("12 day"));?></option>
            <option><?php echo date("Y/m/d",strtotime("13 day"));?></option>
          </select>
				</p>
				<p>
					<select name="time3">
		        <option value="">選択してください</option>
		        <option value="10:00">10:00</option>
						<option value="10:30">10:30</option>
						<option value="11:00">11:00</option>
						<option value="11:30">11:30</option>
						<option value="12:00">12:00</option>
						<option value="12:30">12:30</option>
						<option value="13:00">13:00</option>
						<option value="13:30">13:30</option>
						<option value="14:00">14:00</option>
						<option value="14:30">14:30</option>
						<option value="15:00">15:00</option>
						<option value="15:30">15:30</option>
						<option value="16:00">16:00</option>
						<option value="16:30">16:30</option>
						<option value="17:00">17:00</option>
						<option value="17:30">17:30</option>
						<option value="18:00">18:00</option>
		        <option value="18:30">18:30</option>
		        <option value="19:00">19:00</option>
		        <option value="19:30">19:30</option>
		        <option value="20:00">20:00</option>
		        <option value="20:30">20:30</option>
		        <option value="21:00">21:00</option>
		        <option value="21:30">21:30</option>
		        <option value="22:00">22:00</option>
		        <option value="22:30">22:30</option>
		        <option value="23:00">23:00</option>
		      </select>
				</p>
			</div>
		</div>
		<br clear="left">
		<div class="box_others">
			<p class="txt">備考<span class="nimni">任意</span></p>
			<textarea name="others" rows="4" cols="40"></textarea>
		</div>
    </div>
      <input type="submit" value="" id="submit_btn"/>
      <!--<input type="reset" value="リセット" />-->
  </form>
</div>
</body>
</html>
