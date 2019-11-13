$(document).ready(function(){
    $('.now_tp').slick({
        arrows: true,
        centerMode:true,
        centerPadding: '40px'
    });
});

$('.now_tp_box').click(function(){
    $('#modalArea').fadeIn();
});
$('#closeModal , #modalBg').click(function(){
    $('#modalArea').fadeOut();
});

for(var i =0; i<100; i++){
    (function(i){
        $(document).on("click", "#tp_info_"+i, function(){
            $.ajax({
                    url: '../module/common/request_sp.php',
                    type: 'POST',
                    data: {
                        'img': $('#img_'+i).val(),
                        'name': $('#name_'+i).val(),
                        'age': $('#age_'+i).val(),
                        'history': $('#history_'+i).val(),
                        'main_skill': $('#main_skill_'+i).val(),
                        'skill': $('#skill_'+i).val(),
                        'pr': $('#pr_'+i).val()
                    },
                    dataType: 'html'
                })
                // Ajaxリクエストが成功した時発動
                .done((data) => {
                    $('.result').html(data);
                    console.log(data);
                })
                // Ajaxリクエストが失敗した時発動
                .fail((data) => {
                    $('.result').html(data);
                    console.log(data);
                })
                // Ajaxリクエストが成功・失敗どちらでも発動
                .always((data) => {
                    
                });
        });
    })(i);
}