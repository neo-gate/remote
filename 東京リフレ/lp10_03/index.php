<?php

include('module/common/function.php');
require_once("../include/lp_2.php");
$now = date('Ymd');
$area = "tokyo";
$now_tp = now_tp($now, $area);
$date = date('n月j日');
$week = array( "日", "月", "火", "水", "木", "金", "土" );
$tp_cnt = count($now_tp);
$time = date('G:i');

/*
echo('<pre>');
var_dump($now_tp);
echo('</pre>');
*/
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <script type="text/javascript">
        if ((navigator.userAgent.indexOf('iPhone') > 0 && navigator.userAgent.indexOf('iPad') == -1) || navigator.userAgent.indexOf('iPod') > 0 || navigator.userAgent.indexOf('Android') > 0) {
            location.href = '/lp10_03/sp';
        }else{
            
        }
        </script>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-T9S7L2Z');</script>
        <!-- End Google Tag Manager -->
        <meta charset="UTF-8">
        <meta name="robots" content="noindex,nofollow" />
        <meta name="viewport" content="width=device-width,user-scalable=0">
        <link rel="stylesheet" type="text/css" href="css/common.css"  />
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <link rel="stylesheet" type="text/css" href="slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="slick/slick-theme.css"/>
        <title>出張マッサージ｜東京リフレ</title>
    </head>
    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T9S7L2Z"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <header class="clearfix">
            <img src="img/rogo.jpg" alt="東京リフレ" class="logo">
            <h1>東京で出張マッサージならご利用実績No1の東京リフレへ</h1>
            <div class="time_box">
                <p>【受付時間】10:00 ～ 翌5:00</p>
                <p>【営業時間】18:00 ～ 翌8:00</p>
                <p>年中無休</p>
            </div>
            <div id="header-fixed">
                <div id="header-bk">
                    <div id="header">
                        <img src="img/shape.jpg" alt="今スグ！TEL予約">
                        <img src="img/tel.jpg" alt="電話ボタン">
                        <a href="contact/form.php" class="clearfix"><img src="img/button_web.jpg" alt="ウェブ予約ボタン" class="web_button"></a>
                    </div>
                </div>
            </div>
        </header>
        <main>
            <div class="main_image">
                <img src="img/main_image.jpg" alt="お客様満足度5点満点中4.75点">
            </div>
            <div class="main_text">
                <h2>品川、新宿、渋谷、新橋、秋葉原など東京23区のご自宅やホテルへ最高のマッサージをお届けする出張マッサージ店です。</h2>
            </div>
            <div class="tp_text">
                <p>高い技術を持つ選び抜かれた</p>
                <p>女性セラピストのみ在籍</p>
            </div>
            <div class="now_info">
                <p><?php echo $date;?>(<?php echo $week[date("w")];?>) <span>出勤セラピスト<strong><?php echo $tp_cnt;?></strong>名</span></p>
                <p><?php echo $time;?>現在 <?php echo $reservation_message;?></p>
            </div>
            <div class="now_tp">
                <?php 
                $i = 0;
                foreach ($now_tp as $row => $val) {
                ?>
                <form method="POST" class="now_tp_box clearfix tp_info_<?php echo $i;?>">
                    <input type="hidden" id="img_<?php echo $i;?>" name="img" value="<?php echo $val['img'];?>">
                    <input type="hidden" id="name_<?php echo $i;?>" name="name" value="<?php echo $val['name'];?>">
                    <input type="hidden" id="age_<?php echo $i;?>" name="age" value="<?php echo $val['age'];?>">
                    <input type="hidden" id="history_<?php echo $i;?>" name="history" value="<?php echo $val['history'];?>">
                    <input type="hidden" id="main_skill_<?php echo $i;?>" name="main_skill" value="<?php echo $val['main_skill'];?>">
                    <input type="hidden" id="skill_<?php echo $i;?>" name="skill" value="<?php echo $val['skill'];?>">
                    <input type="hidden" id="pr_<?php echo $i;?>" name="pr" value="<?php echo $val['pr'];?>">
                    <img src="http://s3-refle.s3-website-ap-northeast-1.amazonaws.com/<?php echo $val['img'];?>" alt="セラピストアイコン">
                    <p><?php echo $val['name'];?><span>(<?php echo $val['age'];?>)</span></p>
                    <p>セラピスト歴<?php echo $val['history'];?></p>
                    <?php $main_skill = explode(",",$val['main_skill']);?>
                    <p><?php echo mb_strimwidth( $skill_data[$main_skill[0]], 0, 10, "...", "UTF-8" );?></p>
                    <p><?php echo mb_strimwidth( $skill_data[$main_skill[1]], 0, 10, "...", "UTF-8" );?></p>
                    <p><?php echo mb_strimwidth( $skill_data[$main_skill[2]], 0, 10, "...", "UTF-8" );?></p>
                    <p><?php echo mb_strimwidth( $val['pr'], 0, 90, "...", "UTF-8" );?></p>
                </form>
                <?php 
                    $i++;
                }
                ?>
            </div>
            <!-- モーダルエリアここから -->
            <section id="modalArea" class="modalArea">
                <div id="modalBg" class="modalBg"></div>
                <div class="modalWrapper">
                    <div class="modalContents">
                        <div class="result"></div>
                    </div>
                    <div id="closeModal" class="closeModal">×</div>
                </div>
            </section>
            <!-- モーダルエリアここまで -->
            <div class="massage_box clearfix">
                <p class="massage_title">多彩なマッサージ</p>
                <p class="massage_text_top">東京リフレのセラピストは様々なマッサージスキルを保有しております。お好きなマッサージのご希望がございましたらお気軽にリクエストしてください。</p>
                <div class="massage clearfix">
                    <div>
                        <img src="img/massage_01.jpg" alt="リフレクソロジー">
                        <p>リフレ</p><p>クソロジー</p>
                    </div>
                    <div>
                        <img src="img/massage_02.jpg" alt="指圧・ボディケア">
                        <p>指圧</p><p>ボディケア</p>
                    </div>
                    <div>
                        <img src="img/massage_03.jpg" alt="アロマトリートメント">
                        <p>アロマ</p><p>トリートメント</p>
                    </div>
                    <div>
                        <img src="img/massage_04.jpg" alt="リンパドレナージュ">
                        <p>リンパ</p><p>ドレナージュ</p>
                    </div>
                    <div>
                        <img src="img/massage_05.jpg" alt="タイ古式マッサージ">
                        <p>タイ古式</p><p>マッサージ</p>
                    </div>
                    <div>
                        <img src="img/massage_06.jpg" alt="ヘッドマッサージ">
                        <p>ヘッド</p><p>マッサージ</p>
                    </div>
                </div>
                <div class="massage_text_btm">
                    <p>他にもフェイシャル、小顔、ストレッチ、強もみ、眼精疲労、瘦身など各セラピストの多彩な経歴により数多くのマッサージが受けられます。</p>
                </div>
            </div>
            <div class="omakase">
                <p>セラピストにおまかせ</p>
                <p>
                    東京リフレのセラピストは経験豊富なセラピストのみ在籍。そのためマニュアル通りの施術ではなく、お客様のお身体の状態に合わせ最適なマッサージをご提供することができます。
                </p>
            </div>
            <div class="manzokudo">
                <img src="img/manzokudo.jpg" alt="お客様満足度4.75点">
            </div>
            <div class="voice_title">
                <p>お客様からの声</p>
                <p>多くのお客様にご満足いただけています</p>
            </div>
            <div class="voice_list_1">
                <div class="clearfix">
                    <p>到着と同時にとても丁寧な挨拶・カウンセリングから始まり、なんといってもマッサージの心地よさ。出張マッサージをよく利用しますが、今までで断トツNo.1の技術でした。終わった後の体の軽さがとても気持ち良かったです。</p>
                    <p>広様＜30代女性＞★★★★★</p>
                </div>
                <img src="img/fukidashi_hidari.jpg" alt="料金表">
            </div>
            <div class="voice_list_2">
                <div class="clearfix">
                    <p>とても心地よく施術中に何度も寝てしまいました。 肩から首の張り、コリがほぐれスッキリしました。清潔感のある丁寧なセラピストさんで、快適な時間を過ごせました。</p>
                    <p>K様＜40代男性＞★★★★★</p>
                </div>
                <img src="img/fukidashi_migi.jpg" alt="料金表" style="padding:0 0 1px 0;">
            </div>
            <div class="voice_list_3">
                <div class="clearfix">
                    <p>接客、施術、会話すべて期待以上で大変満足しました。しっかりとした指圧に続き、滑らかなオイルマッサージ…。120分があっという間に過ぎ、施術後はカラダが軽くなっただけではなく心もすっかり癒されました。セラピストさんの確かな技術とお人柄のおかげだとつくづく感じました。ホスピタリティに溢れ、是非またリピートしたいです。</p>
                    <p>H様＜50代男性＞★★★★★</p>
                </div>
                <img src="img/fukidashi_hidari.jpg" alt="料金表">
            </div>
            <div class="course_area">
                <div class="course">
                    <div class="course_title">
                        <p>コース料金</p>
                    </div>
                    <div class="course_text">
                        <p>高品質のリラックスタイムを今ならキャンペーン価格でご利用いただけます。</p>
                    </div>
                    <div class="price">
                        <img src="img/price.jpg" alt="料金表">
                    </div>
                </div>
                <div class="area">
                    <div class="area_title">
                        <p>出張エリア</p>
                        <p>品川、新宿、渋谷、新橋、秋葉原など東京23区のご自宅やホテルへお伺いします。</p>
                    </div>
                    <div class="clearfix">
                        <img src="img/map.gif" alt="出張エリア" class="area_title_img">
                        <p class="price_text">移動目安時間 / 出張交通費</p>
                        <div class="price_box_1">
                            <p>15～30分 / 1000円</p>
                            <p>新宿区｜品川区｜渋谷区｜台東区</p>
                            <p>文京区｜中央区｜墨田区｜豊島区</p>
                            <p>港区｜千代田区｜目黒区</p>
                        </div>
                        <div class="price_box_2">
                            <p>20～40分 / 2000円</p>
                            <p>江東区｜荒川区｜中野区｜世田谷区</p>
                        </div>
                        <div class="price_box_3">
                            <p>30～50分 / 3000円</p>
                            <p>大田区｜練馬区｜杉並区｜葛飾区</p>
                            <p>江戸川区｜板橋区｜足立区｜北区</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="goriyou_title">
                <p>ご利用までの流れ</p>
            </div>
            <div class="step_box">
                <img src="img/step_01.jpg" alt="ステップ１">
                <p>お電話orWEB予約よりご連絡ください</p>
                <p>お電話もしくはWEB予約からご連絡ください。東京23区のご自宅・ホテル等のご利用場所、ご希望のお伺い時間をお伝えください。ご質問のみも大歓迎！</p>
            </div>
            <div class="step_box">
                <img src="img/step_02.jpg" alt="ステップ２">
                <p>セラピストが到着後、カウンセリング</p>
                <p>セラピスト到着後に簡単なカウンセリングを行います。リクエストがありましたら、お気軽にお伝えください。</p>
            </div>
            <div class="step_box">
                <img src="img/step_03.jpg" alt="ステップ３">
                <p>施術開始</p>
                <p>施術開始からコースのお時間はスタートとなります。力加減の強弱など、何なりとお申し付けください。</p>
            </div>
            <div class="step_box">
                <img src="img/step_04.jpg" alt="ステップ４">
                <p>施術終了</p>
                <p>セラピストがベットを元通りにし、退出致します。そのままごゆっくりお休みください。</p>
            </div>
            <div class="question">
            <div class="question_title"><p>よくあるご質問</p></div>
            <div class="question_box">
                <div>
                    <p>Q.２名での利用はできますか？</p>
                    <p>A.はい、ご利用可能です。</p>
                    <p>ご夫婦様、お友達同士などのご利用の際は、セラピストを２名出張致します。</p>
                </div>
            </div>
            <div class="question_box">
                <div>
                    <p>Q.何か準備しておくことはありますか？</p>
                    <p>A.いいえ、何もございません。</p>
                    <p>タオルなどの道具はすべてお持ちします。事前にシャワーを浴びられることをオススメしております。</p>
                </div>
            </div>
            <div class="question_box">
                <div>
                    <p>Q.宿泊しているホテルで利用できるかわからないのですが…</p>
                    <p>A.当店でお調べ致します。</p>
                    <p>出張可能なホテルかどうかは当店でお調べします。お気軽にお問い合わせください。</p>
                </div>
            </div>
            <div class="question_box">
                <div>
                    <p>Q.どんなマッサージが良いかわからないのですが…</p>
                    <p>A.担当セラピストにおまかせください。</p>
                    <p>カウンセリングやお客様のお身体の状態から判断し、最適なマッサージをご提供致します。</p>
                </div>
            </div>
            <div class="question_box">
                <div>
                    <p>Q.クレジットカードは利用できますか？</p>
                    <p>A.はい、ご利用可能です。</p>
                    <p>VISA、MASTER、AMEXのカードが利用可能です。現地での決済となります。手数料は無料です。</p>
                </div>
            </div>
            <div class="question_box">
                <div>
                    <p>Q.領収書は発行できますか？</p>
                    <p>A.はい、発行可能です。</p>
                    <p>ご予約時、もしくは担当セラピストにお申し付けください。</p>
                </div>
            </div>
            <div class="question_text">
                <p>他にも気になることがございましたら、お電話や予約フォームでお気軽にご連絡ください。ご質問だけのご連絡も大歓迎です。</p>
            </div>
            </div>
        </main>
        <footer>
            <div class="chushaku">
                <p>※1　2018年当社調べ</p>
                <p>※2　当社サイト内お客様の声より。2016/1/1~2019/4/4の東京エリア平均</p>
            </div>
            <div class="link"><a href="company.php" target="_blank">会社概要</a></div>
            <div class="copy_light">(C)出張マッサージ東京リフレ</div>
        </footer>
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="crossorigin="anonymous"></script>
        <script type="text/javascript" src="slick/slick.min.js"></script>
        <script type="text/javascript" src="js/therapist.js"></script>
    </body>
</html>