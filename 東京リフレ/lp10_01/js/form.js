$(function(){
  
    //selectタグ（親） が変更された場合
    $('[name=day]').on('change', function(){
      var day = $(this).val();
      var time = document.getElementById('time').value;
      var course = document.getElementById('course').value;
      //day値 を select.php へ渡す
      $.ajax({
        url: "../module/common/select.php",
        type: "POST",
        dataType: 'json',
        data: {
          day: day,
          time: time,
          course: course
        }
      })
      .done(function(data){
        //selectタグ（子） の option値 を一旦削除
        $('#therapist option').remove();
        //select.php から戻って来た data の値をそれそれ optionタグ として生成し、
        // #therapist に optionタグ を追加する
        $.each(data, function(id,name){
          $('#therapist').append($('<option>').text(name).attr('value', name));
        });
      })
      .fail(function(){
        console.log("失敗");
      });
    });

    //selectタグ（親） が変更された場合
    $('[name=time]').on('change', function(){
      var day = document.getElementById('day').value;
      var time = $(this).val();
      var course = document.getElementById('course').value;
      //day値 を select.php へ渡す
      $.ajax({
        url: "../module/common/select.php",
        type: "POST",
        dataType: 'json',
        data: {
          day: day,
          time: time,
          course: course
        }
      })
      .done(function(data){
        //selectタグ（子） の option値 を一旦削除
        $('#therapist option').remove();
        //select.php から戻って来た data の値をそれそれ optionタグ として生成し、
        // #therapist に optionタグ を追加する
        $.each(data, function(id,name){
          $('#therapist').append($('<option>').text(name).attr('value', name));
        });
      })
      .fail(function(){
        console.log("失敗");
      });
    });

    //selectタグ（親） が変更された場合
    $('[name=course]').on('change', function(){
      var day = document.getElementById('day').value;
      var time = document.getElementById('time').value;
      var course = $(this).val();
      //day値 を select.php へ渡す
      $.ajax({
        url: "../module/common/select.php",
        type: "POST",
        dataType: 'json',
        data: {
          day: day,
          time: time,
          course: course
        }
      })
      .done(function(data){
        //selectタグ（子） の option値 を一旦削除
        $('#therapist option').remove();
        //select.php から戻って来た data の値をそれそれ optionタグ として生成し、
        // #therapist に optionタグ を追加する
        $.each(data, function(id,name){
          $('#therapist').append($('<option>').text(name).attr('value', name));
        });
      })
      .fail(function(){
        console.log("失敗");
      });
    });

  });