<?php

$file_name = basename($_SERVER['PHP_SELF']);
$ua=$_SERVER['HTTP_USER_AGENT'];
$header = getallheaders();
$agent = $header["User-Agent"] ;

//携帯電話の振替処理
if((preg_match("/DoCoMo/",$agent)) || (preg_match("/^UP.Browser|^KDDI/", $agent))
	 || (preg_match("/^J-PHONE|^Vodafone|^SoftBank/", $agent))){

	if( $file_name == 'index.html' ){
		header("Location: ./m/index.html");
		exit;
	}else if( $file_name == 'concept.html' ){
		header("Location: ./m/concept.html");
	}else if( $file_name == 'guidance.html' ){
		header("Location: ./m/guidance.html");
	}else if( $file_name == 'inquiry.html' ){
		header("Location: ./m/inquiry.html");
	}else if( $file_name == 'link.html' ){
		header("Location: ./m/link.html");
	}else if( $file_name == 'operation.html' ){
		header("Location: ./m/operation.html");
	}else if( $file_name == 'recruit.html' ){
		header("Location: ./m/recruit.html");
	}else if( $file_name == 'sitemap.html' ){
		header("Location: ./m/sitemap.html");
	}else if( $file_name == 'system.html' ){
		header("Location: ./m/system.html");
	}else if( $file_name == 'therapist.html' ){
		header("Location: ./m/therapist.html");
	}else if($file_name=='area_adachiku.html'){

		header("Location: ./m/area_adachiku.html");
		exit;

	}else if($file_name=='area_arakawaku.html'){

		header("Location: ./m/area_arakawaku.html");
		exit;

	}else if($file_name=='area_bunkyouku.html'){

		header("Location: ./m/area_bunkyouku.html");
		exit;

	}else if($file_name=='area_chiyodaku.html'){

		header("Location: ./m/area_chiyodaku.html");
		exit;

	}else if($file_name=='area_cyuouku.html'){

		header("Location: ./m/area_cyuouku.html");
		exit;

	}else if($file_name=='area_edogawaku.html'){

		header("Location: ./m/area_edogawaku.html");
		exit;

	}else if($file_name=='area_itabasiku.html'){

		header("Location: ./m/area_itabasiku.html");
		exit;

	}else if($file_name=='area_katsusikaku.html'){

		header("Location: ./m/area_katsusikaku.html");
		exit;

	}else if($file_name=='area_kitaku.html'){

		header("Location: ./m/area_kitaku.html");
		exit;

	}else if($file_name=='area_koutouku.html'){

		header("Location: ./m/area_koutouku.html");
		exit;

	}else if($file_name=='area_meguroku.html'){

		header("Location: ./m/area_meguroku.html");
		exit;

	}else if($file_name=='area_minatoku.html'){

		header("Location: ./m/area_minatoku.html");
		exit;

	}else if($file_name=='area_nakanoku.html'){

		header("Location: ./m/area_nakanoku.html");
		exit;

	}else if($file_name=='area_nerimaku.html'){

		header("Location: ./m/area_nerimaku.html");
		exit;

	}else if($file_name=='area_ootaku.html'){

		header("Location: ./m/area_ootaku.html");
		exit;

	}else if($file_name=='area_setagayaku.html'){

		header("Location: ./m/area_setagayaku.html");
		exit;

	}else if($file_name=='area_sibuyaku.html'){

		header("Location: ./m/area_sibuyaku.html");
		exit;

	}else if($file_name=='area_sinagawaku.html'){

		header("Location: ./m/area_sinagawaku.html");
		exit;

	}else if($file_name=='area_sinjyuku.html'){

		header("Location: ./m/area_sinjyuku.html");
		exit;

	}else if($file_name=='area_suginamiku.html'){

		header("Location: ./m/area_suginamiku.html");
		exit;

	}else if($file_name=='area_sumidaku.html'){

		header("Location: ./m/area_sumidaku.html");
		exit;

	}else if($file_name=='area_taitouku.html'){

		header("Location: ./m/area_taitouku.html");
		exit;

	}else if($file_name=='area_toshimaku.html'){

		header("Location: ./m/area_toshimaku.html");
		exit;

	}else if($file_name=='station_nippori.html'){

		header("Location: ./m/station_nippori.html");
		exit;

	}else if($file_name=='station_akasaka.html'){

		header("Location: ./m/station_akasaka.html");
		exit;

	}else if($file_name=='station_akihabara.html'){

		header("Location: ./m/station_akihabara.html");
		exit;

	}else if($file_name=='station_asakusa.html'){

		header("Location: ./m/station_asakusa.html");
		exit;

	}else if($file_name=='station_azabujyuuban.html'){

		header("Location: ./m/station_azabujyuuban.html");
		exit;

	}else if($file_name=='station_daikanyama.html'){

		header("Location: ./m/station_daikanyama.html");
		exit;

	}else if($file_name=='station_denenchoufu.html'){

		header("Location: ./m/station_denenchoufu.html");
		exit;

	}else if($file_name=='station_ebisu.html'){

		header("Location: ./m/station_ebisu.html");
		exit;

	}else if($file_name=='station_futagotamagawa.html'){

		header("Location: ./m/station_futagotamagawa.html");
		exit;

	}else if($file_name=='station_ginza.html'){

		header("Location: ./m/station_ginza.html");
		exit;

	}else if($file_name=='station_gotanda.html'){

		header("Location: ./m/station_gotanda.html");
		exit;

	}else if($file_name=='station_hamamatsuchou.html'){

		header("Location: ./m/station_hamamatsuchou.html");
		exit;

	}else if($file_name=='station_hiroo.html'){

		header("Location: ./m/station_hiroo.html");
		exit;

	}else if($file_name=='station_iidabasi.html'){

		header("Location: ./m/station_iidabasi.html");
		exit;

	}else if($file_name=='station_ikebukuro.html'){

		header("Location: ./m/station_ikebukuro.html");
		exit;

	}else if($file_name=='station_kagurazaka.html'){

		header("Location: ./m/station_kagurazaka.html");
		exit;

	}else if($file_name=='station_kameido.html'){

		header("Location: ./m/station_kameido.html");
		exit;

	}else if($file_name=='station_kanda.html'){

		header("Location: ./m/station_kanda.html");
		exit;

	}else if($file_name=='station_kayabachou.html'){

		header("Location: ./m/station_kayabachou.html");
		exit;

	}else if($file_name=='station_kiba.html'){

		header("Location: ./m/station_kiba.html");
		exit;

	}else if($file_name=='station_kinsichou.html'){

		header("Location: ./m/station_kinsichou.html");
		exit;

	}else if($file_name=='station_kyabachou.html'){

		header("Location: ./m/station_kyabachou.html");
		exit;

	}else if($file_name=='station_meguro.html'){

		header("Location: ./m/station_meguro.html");
		exit;

	}else if($file_name=='station_minamisenjyu.html'){

		header("Location: ./m/station_minamisenjyu.html");
		exit;

	}else if($file_name=='station_monzennakachou.html'){

		header("Location: ./m/station_monzennakachou.html");
		exit;

	}else if($file_name=='station_nakameguro.html'){

		header("Location: ./m/station_nakameguro.html");
		exit;

	}else if($file_name=='station_nakano.html'){

		header("Location: ./m/station_nakano.html");
		exit;

	}else if($file_name=='station_nihonbasi.html'){

		header("Location: ./m/station_nihonbasi.html");
		exit;

	}else if($file_name=='station_nippori.html'){

		header("Location: ./m/station_nippori.html");
		exit;

	}else if($file_name=='station_nisinippori.html'){

		header("Location: ./m/station_nisinippori.html");
		exit;

	}else if($file_name=='station_ochanomizu.html'){

		header("Location: ./m/station_ochanomizu.html");
		exit;

	}else if($file_name=='station_omotesandou.html'){

		header("Location: ./m/station_omotesandou.html");
		exit;

	}else if($file_name=='station_ootemachi.html'){

		header("Location: ./m/station_ootemachi.html");
		exit;

	}else if($file_name=='station_roppongi.html'){

		header("Location: ./m/station_roppongi.html");
		exit;

	}else if($file_name=='station_ryougoku.html'){

		header("Location: ./m/station_ryougoku.html");
		exit;

	}else if($file_name=='station_sakurasinmachi.html'){

		header("Location: ./m/station_sakurasinmachi.html");
		exit;

	}else if($file_name=='station_sangenchaya.html'){

		header("Location: ./m/station_sangenchaya.html");
		exit;

	}else if($file_name=='station_seijyougakuenmae.html'){

		header("Location: ./m/station_seijyougakuenmae.html");
		exit;

	}else if($file_name=='station_shinagawa.html'){

		header("Location: ./m/station_shinagawa.html");
		exit;

	}else if($file_name=='station_sibuya.html'){

		header("Location: ./m/station_sibuya.html");
		exit;

	}else if($file_name=='station_sinbasi.html'){

		header("Location: ./m/station_sinbasi.html");
		exit;

	}else if($file_name=='station_sinjyuku.html'){

		header("Location: ./m/station_sinjyuku.html");
		exit;

	}else if($file_name=='station_siroganetakanawa.html'){

		header("Location: ./m/station_siroganetakanawa.html");
		exit;

	}else if($file_name=='station_suidoubasi.html'){

		header("Location: ./m/station_suidoubasi.html");
		exit;

	}else if($file_name=='station_tokyo.html'){

		header("Location: ./m/station_tokyo.html");
		exit;

	}else if($file_name=='station_touyouchou.html'){

		header("Location: ./m/station_touyouchou.html");
		exit;

	}else if($file_name=='station_ueno.html'){

		header("Location: ./m/station_ueno.html");
		exit;

	}else if($file_name=='station_yotsuya.html'){

		header("Location: ./m/station_yotsuya.html");
		exit;

	}else if($file_name=='station_yoyogi.html'){

		header("Location: ./m/station_yoyogi.html");
		exit;

	}else if($file_name=='station_yurakuchou.html'){

		header("Location: ./m/station_yurakuchou.html");
		exit;

	}else if($file_name=='station_yuutenji.html'){

		header("Location: ./m/station_yuutenji.html");
		exit;

	}else if($file_name=='station_ziyuugaoka.html'){

		header("Location: ./m/station_ziyuugaoka.html");
		exit;

	}else if($file_name=='hotel_akasakaexcelhoteltokyu.html'){

		header("Location: ./m/hotel_akasakaexcelhoteltokyu.html");
		exit;

	}else if($file_name=='hotel_annintercontinentaltokyo.html'){

		header("Location: ./m/hotel_annintercontinentaltokyo.html");
		exit;

	}else if($file_name=='hotel_azurtakeshiba.html'){

		header("Location: ./m/hotel_azurtakeshiba.html");
		exit;

	}else if($file_name=='hotel_bellclassic.html'){

		header("Location: ./m/hotel_bellclassic.html");
		exit;

	}else if($file_name=='hotel_bluewaveinnasakusa.html'){

		header("Location: ./m/hotel_bluewaveinnasakusa.html");
		exit;

	}else if($file_name=='hotel_celestine.html'){

		header("Location: ./m/hotel_celestine.html");
		exit;

	}else if($file_name=='hotel_centurysoutherntower.html'){

		header("Location: ./m/hotel_centurysoutherntower.html");
		exit;

	}else if($file_name=='hotel_ceruleantowertokyu.html'){

		header("Location: ./m/hotel_ceruleantowertokyu.html");
		exit;

	}else if($file_name=='hotel_comsginza.html'){

		header("Location: ./m/hotel_comsginza.html");
		exit;

	}else if($file_name=='hotel_conradtokyo.html'){

		header("Location: ./m/hotel_conradtokyo.html");
		exit;

	}else if($file_name=='hotel_courtyardtokyoginza.html'){

		header("Location: ./m/hotel_courtyardtokyoginza.html");
		exit;

	}else if($file_name=='hotel_daiichihotelryogoku.html'){

		header("Location: ./m/hotel_daiichihotelryogoku.html");
		exit;

	}else if($file_name=='hotel_daiichihoteltokyoseafort.html'){

		header("Location: ./m/hotel_daiichihoteltokyoseafort.html");
		exit;

	}else if($file_name=='hotel_daiichitokyo.html'){

		header("Location: ./m/hotel_daiichitokyo.html");
		exit;

	}else if($file_name=='hotel_east21tokyo.html'){

		header("Location: ./m/hotel_east21tokyo.html");
		exit;

	}else if($file_name=='hotel_floracionaoyama.html'){

		header("Location: ./m/hotel_floracionaoyama.html");
		exit;

	}else if($file_name=='hotel_fourseasonshotelmarunouchitokyo.html'){

		header("Location: ./m/hotel_fourseasonshotelmarunouchitokyo.html");
		exit;

	}else if($file_name=='hotel_fourseasonshoteltokyoatchinzanso.html'){

		header("Location: ./m/hotel_fourseasonshoteltokyoatchinzanso.html");
		exit;

	}else if($file_name=='hotel_ginzanikko.html'){

		header("Location: ./m/hotel_ginzanikko.html");
		exit;

	}else if($file_name=='hotel_ginzaraffinato.html'){

		header("Location: ./m/hotel_ginzaraffinato.html");
		exit;

	}else if($file_name=='hotel_graceryginza.html'){

		header("Location: ./m/hotel_graceryginza.html");
		exit;

	}else if($file_name=='hotel_gracerytamachi.html'){

		header("Location: ./m/hotel_gracerytamachi.html");
		exit;

	}else if($file_name=='hotel_grandarchanzomon.html'){

		header("Location: ./m/hotel_grandarchanzomon.html");
		exit;

	}else if($file_name=='hotel_grandhillichigaya.html'){

		header("Location: ./m/hotel_grandhillichigaya.html");
		exit;

	}else if($file_name=='hotel_grandhyatt.html'){

		header("Location: ./m/hotel_grandhyatt.html");
		exit;

	}else if($file_name=='hotel_grandpacificledaiba.html'){

		header("Location: ./m/hotel_grandpacificledaiba.html");
		exit;

	}else if($file_name=='hotel_grandpalace.html'){

		header("Location: ./m/hotel_grandpalace.html");
		exit;

	}else if($file_name=='hotel_grandprincenewtakanawa.html'){

		header("Location: ./m/hotel_grandprincenewtakanawa.html");
		exit;

	}else if($file_name=='hotel_grandprincetakanawa.html'){

		header("Location: ./m/hotel_grandprincetakanawa.html");
		exit;

	}else if($file_name=='hotel_hanedaexceltokyu.html'){

		header("Location: ./m/hotel_hanedaexceltokyu.html");
		exit;

	}else if($file_name=='hotel_harumigrandhotel.html'){

		header("Location: ./m/hotel_harumigrandhotel.html");
		exit;

	}else if($file_name=='hotel_hilltop.html'){

		header("Location: ./m/hotel_hilltop.html");
		exit;

	}else if($file_name=='hotel_hiltontokyo.html'){

		header("Location: ./m/hotel_hiltontokyo.html");
		exit;

	}else if($file_name=='hotel_hyattregencytokyo.html'){

		header("Location: ./m/hotel_hyattregencytokyo.html");
		exit;

	}else if($file_name=='hotel_imperialhotel.html'){

		header("Location: ./m/hotel_imperialhotel.html");
		exit;

	}else if($file_name=='hotel_intercontinentaltokyo.html'){

		header("Location: ./m/hotel_intercontinentaltokyo.html");
		exit;

	}else if($file_name=='hotel_intercontinentaltokyobay.html'){

		header("Location: ./m/hotel_intercontinentaltokyobay.html");
		exit;

	}else if($file_name=='hotel_jalcityhanedatokyo.html'){

		header("Location: ./m/hotel_jalcityhanedatokyo.html");
		exit;

	}else if($file_name=='hotel_keioplazatokyo.html'){

		header("Location: ./m/hotel_keioplazatokyo.html");
		exit;

	}else if($file_name=='hotel_kkrhoteltokyo.html'){

		header("Location: ./m/hotel_kkrhoteltokyo.html");
		exit;

	}else if($file_name=='hotel_kodomonosiro.html'){

		header("Location: ./m/hotel_kodomonosiro.html");
		exit;

	}else if($file_name=='hotel_laforettokyo.html'){

		header("Location: ./m/hotel_laforettokyo.html");
		exit;

	}else if($file_name=='hotel_lottecityhotelkinshicho.html'){

		header("Location: ./m/hotel_lottecityhotelkinshicho.html");
		exit;

	}else if($file_name=='hotel_lungwood.html'){

		header("Location: ./m/hotel_lungwood.html");
		exit;

	}else if($file_name=='hotel_mandarinorientaltokyo.html'){

		header("Location: ./m/hotel_mandarinorientaltokyo.html");
		exit;

	}else if($file_name=='hotel_marunouchihoteltokyo.html'){

		header("Location: ./m/hotel_marunouchihoteltokyo.html");
		exit;

	}else if($file_name=='hotel_megurogajyoen.html'){

		header("Location: ./m/hotel_megurogajyoen.html");
		exit;

	}else if($file_name=='hotel_mercureginza.html'){

		header("Location: ./m/hotel_mercureginza.html");
		exit;

	}else if($file_name=='hotel_metropolitan.html'){

		header("Location: ./m/hotel_metropolitan.html");
		exit;

	}else if($file_name=='hotel_metropolitanedmont.html'){

		header("Location: ./m/hotel_metropolitanedmont.html");
		exit;

	}else if($file_name=='hotel_mitsuigardenhotelginzapremier.html'){

		header("Location: ./m/hotel_mitsuigardenhotelginzapremier.html");
		exit;

	}else if($file_name=='hotel_mitsuigardenueno.html'){

		header("Location: ./m/hotel_mitsuigardenueno.html");
		exit;

	}else if($file_name=='hotel_montereyakasaka.html'){

		header("Location: ./m/hotel_montereyakasaka.html");
		exit;

	}else if($file_name=='hotel_montereyginza.html'){

		header("Location: ./m/hotel_montereyginza.html");
		exit;

	}else if($file_name=='hotel_montereyhanzomon.html'){

		header("Location: ./m/hotel_montereyhanzomon.html");
		exit;

	}else if($file_name=='hotel_newhankyutokyo.html'){

		header("Location: ./m/hotel_newhankyutokyo.html");
		exit;

	}else if($file_name=='hotel_newotaniinntokyo.html'){

		header("Location: ./m/hotel_newotaniinntokyo.html");
		exit;

	}else if($file_name=='hotel_nikkotokyo.html'){

		header("Location: ./m/hotel_nikkotokyo.html");
		exit;

	}else if($file_name=='hotel_niwatokyo.html'){

		header("Location: ./m/hotel_niwatokyo.html");
		exit;

	}else if($file_name=='hotel_okuratokyo.html'){

		header("Location: ./m/hotel_okuratokyo.html");
		exit;

	}else if($file_name=='hotel_parkhoteltokyo.html'){

		header("Location: ./m/hotel_parkhoteltokyo.html");
		exit;

	}else if($file_name=='hotel_parkhyatttokyo.html'){

		header("Location: ./m/hotel_parkhyatttokyo.html");
		exit;

	}else if($file_name=='hotel_parksideueno.html'){

		header("Location: ./m/hotel_parksideueno.html");
		exit;

	}else if($file_name=='hotel_princesinagawa.html'){

		header("Location: ./m/hotel_princesinagawa.html");
		exit;

	}else if($file_name=='hotel_remmakihabara.html'){

		header("Location: ./m/hotel_remmakihabara.html");
		exit;

	}else if($file_name=='hotel_remmhibiya.html'){

		header("Location: ./m/hotel_remmhibiya.html");
		exit;

	}else if($file_name=='hotel_rihgaroyaltokyo.html'){

		header("Location: ./m/hotel_rihgaroyaltokyo.html");
		exit;

	}else if($file_name=='hotel_royalparkshiodometower.html'){

		header("Location: ./m/hotel_royalparkshiodometower.html");
		exit;

	}else if($file_name=='hotel_royalrarkhotel.html'){

		header("Location: ./m/hotel_royalrarkhotel.html");
		exit;

	}else if($file_name=='hotel_ryumeikantokyo.html'){

		header("Location: ./m/hotel_ryumeikantokyo.html");
		exit;

	}else if($file_name=='hotel_sakurafleuraoyama.html'){

		header("Location: ./m/hotel_sakurafleuraoyama.html");
		exit;

	}else if($file_name=='hotel_sakuratowertokyo.html'){

		header("Location: ./m/hotel_sakuratowertokyo.html");
		exit;

	}else if($file_name=='hotel_seiyoginza.html'){

		header("Location: ./m/hotel_seiyoginza.html");
		exit;

	}else if($file_name=='hotel_shangrilatokyou.html'){

		header("Location: ./m/hotel_shangrilatokyou.html");
		exit;

	}else if($file_name=='hotel_sheratonmiyakohoteltokyo.html'){

		header("Location: ./m/hotel_sheratonmiyakohoteltokyo.html");
		exit;

	}else if($file_name=='hotel_shinjukuprince.html'){

		header("Location: ./m/hotel_shinjukuprince.html");
		exit;

	}else if($file_name=='hotel_shinjukutheagnes.html'){

		header("Location: ./m/hotel_shinjukutheagnes.html");
		exit;

	}else if($file_name=='hotel_shinjukuwashington.html'){

		header("Location: ./m/hotel_shinjukuwashington.html");
		exit;

	}else if($file_name=='hotel_sibuyaexcelhoteltokyu.html'){

		header("Location: ./m/hotel_sibuyaexcelhoteltokyu.html");
		exit;

	}else if($file_name=='hotel_sunmemberstokyoshinjyuku.html'){

		header("Location: ./m/hotel_sunmemberstokyoshinjyuku.html");
		exit;

	}else if($file_name=='hotel_sunplaza.html'){

		header("Location: ./m/hotel_sunplaza.html");
		exit;

	}else if($file_name=='hotel_sunrouteakasaka.html'){

		header("Location: ./m/hotel_sunrouteakasaka.html");
		exit;

	}else if($file_name=='hotel_sunshinecity.html'){

		header("Location: ./m/hotel_sunshinecity.html");
		exit;

	}else if($file_name=='hotel_thecapitolhoteltokyu.html'){

		header("Location: ./m/hotel_thecapitolhoteltokyu.html");
		exit;

	}else if($file_name=='hotel_thenewotani.html'){

		header("Location: ./m/hotel_thenewotani.html");
		exit;

	}else if($file_name=='hotel_thepeninsula.html'){

		header("Location: ./m/hotel_thepeninsula.html");
		exit;

	}else if($file_name=='hotel_theritzcarlton.html'){

		header("Location: ./m/hotel_theritzcarlton.html");
		exit;

	}else if($file_name=='hotel_thewestintokyo.html'){

		header("Location: ./m/hotel_thewestintokyo.html");
		exit;

	}else if($file_name=='hotel_tobuhotellevanttokyo.html'){

		header("Location: ./m/hotel_tobuhotellevanttokyo.html");
		exit;

	}else if($file_name=='hotel_tokyodome.html'){

		header("Location: ./m/hotel_tokyodome.html");
		exit;

	}else if($file_name=='hotel_tokyograndhotel.html'){

		header("Location: ./m/hotel_tokyograndhotel.html");
		exit;

	}else if($file_name=='hotel_tokyogreenpalace.html'){

		header("Location: ./m/hotel_tokyogreenpalace.html");
		exit;

	}else if($file_name=='hotel_tokyoprince.html'){

		header("Location: ./m/hotel_tokyoprince.html");
		exit;

	}else if($file_name=='hotel_u-port.html'){

		header("Location: ./m/hotel_u-port.html");
		exit;

	}else if($file_name=='hotel_viainnakihabara.html'){

		header("Location: ./m/hotel_viainnakihabara.html");
		exit;

	}else{
		
		header("Location: ./m/index.html");
		exit;
		
	}
	exit;
}

//スマートフォンの振替処理
if((strpos($ua,'iPhone')!==false)||(strpos($ua,'iPod')!==false)||(strpos($ua,'Android')!==false)) {

	if( $file_name == 'index.html' ){
		
		header("Location: ./sp/index.html");
		exit;
		
	}else if( $file_name == 'concept.html' ){
		
		header("Location: ./sp/concept.html");
		exit;
		
	}else if( $file_name == 'guidance.html' ){
		
		header("Location: ./sp/guidance.html");
		exit;
		
	}else if( $file_name == 'inquiry.html' ){
		
		header("Location: ./sp/inquiry.html");
		exit;
		
	}else if( $file_name == 'link.html' ){
		
		header("Location: ./sp/link.html");
		exit;
		
	}else if( $file_name == 'operation.html' ){
		
		header("Location: ./sp/operation.html");
		exit;
		
	}else if( $file_name == 'recruit.html' ){
		
		header("Location: ./sp/recruit.html");
		exit;
		
	}else if( $file_name == 'sitemap.html' ){
		
		header("Location: ./sp/sitemap.html");
		exit;
		
	}else if( $file_name == 'system.html' ){
		
		header("Location: ./sp/system.html");
		exit;
		
	}else if( $file_name == 'therapist.html' ){
		
		header("Location: ./sp/therapist.html");
		exit;
		
	}else if($file_name=='area_adachiku.html'){

		header("Location: ./sp/area_adachiku.html");
		exit;

	}else if($file_name=='area_arakawaku.html'){

		header("Location: ./sp/area_arakawaku.html");
		exit;

	}else if($file_name=='area_bunkyouku.html'){

		header("Location: ./sp/area_bunkyouku.html");
		exit;

	}else if($file_name=='area_chiyodaku.html'){

		header("Location: ./sp/area_chiyodaku.html");
		exit;

	}else if($file_name=='area_cyuouku.html'){

		header("Location: ./sp/area_cyuouku.html");
		exit;

	}else if($file_name=='area_edogawaku.html'){

		header("Location: ./sp/area_edogawaku.html");
		exit;

	}else if($file_name=='area_itabasiku.html'){

		header("Location: ./sp/area_itabasiku.html");
		exit;

	}else if($file_name=='area_katsusikaku.html'){

		header("Location: ./sp/area_katsusikaku.html");
		exit;

	}else if($file_name=='area_kitaku.html'){

		header("Location: ./sp/area_kitaku.html");
		exit;

	}else if($file_name=='area_koutouku.html'){

		header("Location: ./sp/area_koutouku.html");
		exit;

	}else if($file_name=='area_meguroku.html'){

		header("Location: ./sp/area_meguroku.html");
		exit;

	}else if($file_name=='area_minatoku.html'){

		header("Location: ./sp/area_minatoku.html");
		exit;

	}else if($file_name=='area_nakanoku.html'){

		header("Location: ./sp/area_nakanoku.html");
		exit;

	}else if($file_name=='area_nerimaku.html'){

		header("Location: ./sp/area_nerimaku.html");
		exit;

	}else if($file_name=='area_ootaku.html'){

		header("Location: ./sp/area_ootaku.html");
		exit;

	}else if($file_name=='area_setagayaku.html'){

		header("Location: ./sp/area_setagayaku.html");
		exit;

	}else if($file_name=='area_sibuyaku.html'){

		header("Location: ./sp/area_sibuyaku.html");
		exit;

	}else if($file_name=='area_sinagawaku.html'){

		header("Location: ./sp/area_sinagawaku.html");
		exit;

	}else if($file_name=='area_sinjyuku.html'){

		header("Location: ./sp/area_sinjyuku.html");
		exit;

	}else if($file_name=='area_suginamiku.html'){

		header("Location: ./sp/area_suginamiku.html");
		exit;

	}else if($file_name=='area_sumidaku.html'){

		header("Location: ./sp/area_sumidaku.html");
		exit;

	}else if($file_name=='area_taitouku.html'){

		header("Location: ./sp/area_taitouku.html");
		exit;

	}else if($file_name=='area_toshimaku.html'){

		header("Location: ./sp/area_toshimaku.html");
		exit;

	}else if($file_name=='station_nippori.html'){

		header("Location: ./sp/station_nippori.html");
		exit;

	}else if($file_name=='station_akasaka.html'){

		header("Location: ./sp/station_akasaka.html");
		exit;

	}else if($file_name=='station_akihabara.html'){

		header("Location: ./sp/station_akihabara.html");
		exit;

	}else if($file_name=='station_asakusa.html'){

		header("Location: ./sp/station_asakusa.html");
		exit;

	}else if($file_name=='station_azabujyuuban.html'){

		header("Location: ./sp/station_azabujyuuban.html");
		exit;

	}else if($file_name=='station_daikanyama.html'){

		header("Location: ./sp/station_daikanyama.html");
		exit;

	}else if($file_name=='station_denenchoufu.html'){

		header("Location: ./sp/station_denenchoufu.html");
		exit;

	}else if($file_name=='station_ebisu.html'){

		header("Location: ./sp/station_ebisu.html");
		exit;

	}else if($file_name=='station_futagotamagawa.html'){

		header("Location: ./sp/station_futagotamagawa.html");
		exit;

	}else if($file_name=='station_ginza.html'){

		header("Location: ./sp/station_ginza.html");
		exit;

	}else if($file_name=='station_gotanda.html'){

		header("Location: ./sp/station_gotanda.html");
		exit;

	}else if($file_name=='station_hamamatsuchou.html'){

		header("Location: ./sp/station_hamamatsuchou.html");
		exit;

	}else if($file_name=='station_hiroo.html'){

		header("Location: ./sp/station_hiroo.html");
		exit;

	}else if($file_name=='station_iidabasi.html'){

		header("Location: ./sp/station_iidabasi.html");
		exit;

	}else if($file_name=='station_ikebukuro.html'){

		header("Location: ./sp/station_ikebukuro.html");
		exit;

	}else if($file_name=='station_kagurazaka.html'){

		header("Location: ./sp/station_kagurazaka.html");
		exit;

	}else if($file_name=='station_kameido.html'){

		header("Location: ./sp/station_kameido.html");
		exit;

	}else if($file_name=='station_kanda.html'){

		header("Location: ./sp/station_kanda.html");
		exit;

	}else if($file_name=='station_kayabachou.html'){

		header("Location: ./sp/station_kayabachou.html");
		exit;

	}else if($file_name=='station_kiba.html'){

		header("Location: ./sp/station_kiba.html");
		exit;

	}else if($file_name=='station_kinsichou.html'){

		header("Location: ./sp/station_kinsichou.html");
		exit;

	}else if($file_name=='station_kyabachou.html'){

		header("Location: ./sp/station_kyabachou.html");
		exit;

	}else if($file_name=='station_meguro.html'){

		header("Location: ./sp/station_meguro.html");
		exit;

	}else if($file_name=='station_minamisenjyu.html'){

		header("Location: ./sp/station_minamisenjyu.html");
		exit;

	}else if($file_name=='station_monzennakachou.html'){

		header("Location: ./sp/station_monzennakachou.html");
		exit;

	}else if($file_name=='station_nakameguro.html'){

		header("Location: ./sp/station_nakameguro.html");
		exit;

	}else if($file_name=='station_nakano.html'){

		header("Location: ./sp/station_nakano.html");
		exit;

	}else if($file_name=='station_nihonbasi.html'){

		header("Location: ./sp/station_nihonbasi.html");
		exit;

	}else if($file_name=='station_nippori.html'){

		header("Location: ./sp/station_nippori.html");
		exit;

	}else if($file_name=='station_nisinippori.html'){

		header("Location: ./sp/station_nisinippori.html");
		exit;

	}else if($file_name=='station_ochanomizu.html'){

		header("Location: ./sp/station_ochanomizu.html");
		exit;

	}else if($file_name=='station_omotesandou.html'){

		header("Location: ./sp/station_omotesandou.html");
		exit;

	}else if($file_name=='station_ootemachi.html'){

		header("Location: ./sp/station_ootemachi.html");
		exit;

	}else if($file_name=='station_roppongi.html'){

		header("Location: ./sp/station_roppongi.html");
		exit;

	}else if($file_name=='station_ryougoku.html'){

		header("Location: ./sp/station_ryougoku.html");
		exit;

	}else if($file_name=='station_sakurasinmachi.html'){

		header("Location: ./sp/station_sakurasinmachi.html");
		exit;

	}else if($file_name=='station_sangenchaya.html'){

		header("Location: ./sp/station_sangenchaya.html");
		exit;

	}else if($file_name=='station_seijyougakuenmae.html'){

		header("Location: ./sp/station_seijyougakuenmae.html");
		exit;

	}else if($file_name=='station_shinagawa.html'){

		header("Location: ./sp/station_shinagawa.html");
		exit;

	}else if($file_name=='station_sibuya.html'){

		header("Location: ./sp/station_sibuya.html");
		exit;

	}else if($file_name=='station_sinbasi.html'){

		header("Location: ./sp/station_sinbasi.html");
		exit;

	}else if($file_name=='station_sinjyuku.html'){

		header("Location: ./sp/station_sinjyuku.html");
		exit;

	}else if($file_name=='station_siroganetakanawa.html'){

		header("Location: ./sp/station_siroganetakanawa.html");
		exit;

	}else if($file_name=='station_suidoubasi.html'){

		header("Location: ./sp/station_suidoubasi.html");
		exit;

	}else if($file_name=='station_tokyo.html'){

		header("Location: ./sp/station_tokyo.html");
		exit;

	}else if($file_name=='station_touyouchou.html'){

		header("Location: ./sp/station_touyouchou.html");
		exit;

	}else if($file_name=='station_ueno.html'){

		header("Location: ./sp/station_ueno.html");
		exit;

	}else if($file_name=='station_yotsuya.html'){

		header("Location: ./sp/station_yotsuya.html");
		exit;

	}else if($file_name=='station_yoyogi.html'){

		header("Location: ./sp/station_yoyogi.html");
		exit;

	}else if($file_name=='station_yurakuchou.html'){

		header("Location: ./sp/station_yurakuchou.html");
		exit;

	}else if($file_name=='station_yuutenji.html'){

		header("Location: ./sp/station_yuutenji.html");
		exit;

	}else if($file_name=='station_ziyuugaoka.html'){

		header("Location: ./sp/station_ziyuugaoka.html");
		exit;

	}else if($file_name=='hotel_akasakaexcelhoteltokyu.html'){

		header("Location: ./sp/hotel_akasakaexcelhoteltokyu.html");
		exit;

	}else if($file_name=='hotel_annintercontinentaltokyo.html'){

		header("Location: ./sp/hotel_annintercontinentaltokyo.html");
		exit;

	}else if($file_name=='hotel_azurtakeshiba.html'){

		header("Location: ./sp/hotel_azurtakeshiba.html");
		exit;

	}else if($file_name=='hotel_bellclassic.html'){

		header("Location: ./sp/hotel_bellclassic.html");
		exit;

	}else if($file_name=='hotel_bluewaveinnasakusa.html'){

		header("Location: ./sp/hotel_bluewaveinnasakusa.html");
		exit;

	}else if($file_name=='hotel_celestine.html'){

		header("Location: ./sp/hotel_celestine.html");
		exit;

	}else if($file_name=='hotel_centurysoutherntower.html'){

		header("Location: ./sp/hotel_centurysoutherntower.html");
		exit;

	}else if($file_name=='hotel_ceruleantowertokyu.html'){

		header("Location: ./sp/hotel_ceruleantowertokyu.html");
		exit;

	}else if($file_name=='hotel_comsginza.html'){

		header("Location: ./sp/hotel_comsginza.html");
		exit;

	}else if($file_name=='hotel_conradtokyo.html'){

		header("Location: ./sp/hotel_conradtokyo.html");
		exit;

	}else if($file_name=='hotel_courtyardtokyoginza.html'){

		header("Location: ./sp/hotel_courtyardtokyoginza.html");
		exit;

	}else if($file_name=='hotel_daiichihotelryogoku.html'){

		header("Location: ./sp/hotel_daiichihotelryogoku.html");
		exit;

	}else if($file_name=='hotel_daiichihoteltokyoseafort.html'){

		header("Location: ./sp/hotel_daiichihoteltokyoseafort.html");
		exit;

	}else if($file_name=='hotel_daiichitokyo.html'){

		header("Location: ./sp/hotel_daiichitokyo.html");
		exit;

	}else if($file_name=='hotel_east21tokyo.html'){

		header("Location: ./sp/hotel_east21tokyo.html");
		exit;

	}else if($file_name=='hotel_floracionaoyama.html'){

		header("Location: ./sp/hotel_floracionaoyama.html");
		exit;

	}else if($file_name=='hotel_fourseasonshotelmarunouchitokyo.html'){

		header("Location: ./sp/hotel_fourseasonshotelmarunouchitokyo.html");
		exit;

	}else if($file_name=='hotel_fourseasonshoteltokyoatchinzanso.html'){

		header("Location: ./sp/hotel_fourseasonshoteltokyoatchinzanso.html");
		exit;

	}else if($file_name=='hotel_ginzanikko.html'){

		header("Location: ./sp/hotel_ginzanikko.html");
		exit;

	}else if($file_name=='hotel_ginzaraffinato.html'){

		header("Location: ./sp/hotel_ginzaraffinato.html");
		exit;

	}else if($file_name=='hotel_graceryginza.html'){

		header("Location: ./sp/hotel_graceryginza.html");
		exit;

	}else if($file_name=='hotel_gracerytamachi.html'){

		header("Location: ./sp/hotel_gracerytamachi.html");
		exit;

	}else if($file_name=='hotel_grandarchanzomon.html'){

		header("Location: ./sp/hotel_grandarchanzomon.html");
		exit;

	}else if($file_name=='hotel_grandhillichigaya.html'){

		header("Location: ./sp/hotel_grandhillichigaya.html");
		exit;

	}else if($file_name=='hotel_grandhyatt.html'){

		header("Location: ./sp/hotel_grandhyatt.html");
		exit;

	}else if($file_name=='hotel_grandpacificledaiba.html'){

		header("Location: ./sp/hotel_grandpacificledaiba.html");
		exit;

	}else if($file_name=='hotel_grandpalace.html'){

		header("Location: ./sp/hotel_grandpalace.html");
		exit;

	}else if($file_name=='hotel_grandprincenewtakanawa.html'){

		header("Location: ./sp/hotel_grandprincenewtakanawa.html");
		exit;

	}else if($file_name=='hotel_grandprincetakanawa.html'){

		header("Location: ./sp/hotel_grandprincetakanawa.html");
		exit;

	}else if($file_name=='hotel_hanedaexceltokyu.html'){

		header("Location: ./sp/hotel_hanedaexceltokyu.html");
		exit;

	}else if($file_name=='hotel_harumigrandhotel.html'){

		header("Location: ./sp/hotel_harumigrandhotel.html");
		exit;

	}else if($file_name=='hotel_hilltop.html'){

		header("Location: ./sp/hotel_hilltop.html");
		exit;

	}else if($file_name=='hotel_hiltontokyo.html'){

		header("Location: ./sp/hotel_hiltontokyo.html");
		exit;

	}else if($file_name=='hotel_hyattregencytokyo.html'){

		header("Location: ./sp/hotel_hyattregencytokyo.html");
		exit;

	}else if($file_name=='hotel_imperialhotel.html'){

		header("Location: ./sp/hotel_imperialhotel.html");
		exit;

	}else if($file_name=='hotel_intercontinentaltokyo.html'){

		header("Location: ./sp/hotel_intercontinentaltokyo.html");
		exit;

	}else if($file_name=='hotel_intercontinentaltokyobay.html'){

		header("Location: ./sp/hotel_intercontinentaltokyobay.html");
		exit;

	}else if($file_name=='hotel_jalcityhanedatokyo.html'){

		header("Location: ./sp/hotel_jalcityhanedatokyo.html");
		exit;

	}else if($file_name=='hotel_keioplazatokyo.html'){

		header("Location: ./sp/hotel_keioplazatokyo.html");
		exit;

	}else if($file_name=='hotel_kkrhoteltokyo.html'){

		header("Location: ./sp/hotel_kkrhoteltokyo.html");
		exit;

	}else if($file_name=='hotel_kodomonosiro.html'){

		header("Location: ./sp/hotel_kodomonosiro.html");
		exit;

	}else if($file_name=='hotel_laforettokyo.html'){

		header("Location: ./sp/hotel_laforettokyo.html");
		exit;

	}else if($file_name=='hotel_lottecityhotelkinshicho.html'){

		header("Location: ./sp/hotel_lottecityhotelkinshicho.html");
		exit;

	}else if($file_name=='hotel_lungwood.html'){

		header("Location: ./sp/hotel_lungwood.html");
		exit;

	}else if($file_name=='hotel_mandarinorientaltokyo.html'){

		header("Location: ./sp/hotel_mandarinorientaltokyo.html");
		exit;

	}else if($file_name=='hotel_marunouchihoteltokyo.html'){

		header("Location: ./sp/hotel_marunouchihoteltokyo.html");
		exit;

	}else if($file_name=='hotel_megurogajyoen.html'){

		header("Location: ./sp/hotel_megurogajyoen.html");
		exit;

	}else if($file_name=='hotel_mercureginza.html'){

		header("Location: ./sp/hotel_mercureginza.html");
		exit;

	}else if($file_name=='hotel_metropolitan.html'){

		header("Location: ./sp/hotel_metropolitan.html");
		exit;

	}else if($file_name=='hotel_metropolitanedmont.html'){

		header("Location: ./sp/hotel_metropolitanedmont.html");
		exit;

	}else if($file_name=='hotel_mitsuigardenhotelginzapremier.html'){

		header("Location: ./sp/hotel_mitsuigardenhotelginzapremier.html");
		exit;

	}else if($file_name=='hotel_mitsuigardenueno.html'){

		header("Location: ./sp/hotel_mitsuigardenueno.html");
		exit;

	}else if($file_name=='hotel_montereyakasaka.html'){

		header("Location: ./sp/hotel_montereyakasaka.html");
		exit;

	}else if($file_name=='hotel_montereyginza.html'){

		header("Location: ./sp/hotel_montereyginza.html");
		exit;

	}else if($file_name=='hotel_montereyhanzomon.html'){

		header("Location: ./sp/hotel_montereyhanzomon.html");
		exit;

	}else if($file_name=='hotel_newhankyutokyo.html'){

		header("Location: ./sp/hotel_newhankyutokyo.html");
		exit;

	}else if($file_name=='hotel_newotaniinntokyo.html'){

		header("Location: ./sp/hotel_newotaniinntokyo.html");
		exit;

	}else if($file_name=='hotel_nikkotokyo.html'){

		header("Location: ./sp/hotel_nikkotokyo.html");
		exit;

	}else if($file_name=='hotel_niwatokyo.html'){

		header("Location: ./sp/hotel_niwatokyo.html");
		exit;

	}else if($file_name=='hotel_okuratokyo.html'){

		header("Location: ./sp/hotel_okuratokyo.html");
		exit;

	}else if($file_name=='hotel_parkhoteltokyo.html'){

		header("Location: ./sp/hotel_parkhoteltokyo.html");
		exit;

	}else if($file_name=='hotel_parkhyatttokyo.html'){

		header("Location: ./sp/hotel_parkhyatttokyo.html");
		exit;

	}else if($file_name=='hotel_parksideueno.html'){

		header("Location: ./sp/hotel_parksideueno.html");
		exit;

	}else if($file_name=='hotel_princesinagawa.html'){

		header("Location: ./sp/hotel_princesinagawa.html");
		exit;

	}else if($file_name=='hotel_remmakihabara.html'){

		header("Location: ./sp/hotel_remmakihabara.html");
		exit;

	}else if($file_name=='hotel_remmhibiya.html'){

		header("Location: ./sp/hotel_remmhibiya.html");
		exit;

	}else if($file_name=='hotel_rihgaroyaltokyo.html'){

		header("Location: ./sp/hotel_rihgaroyaltokyo.html");
		exit;

	}else if($file_name=='hotel_royalparkshiodometower.html'){

		header("Location: ./sp/hotel_royalparkshiodometower.html");
		exit;

	}else if($file_name=='hotel_royalrarkhotel.html'){

		header("Location: ./sp/hotel_royalrarkhotel.html");
		exit;

	}else if($file_name=='hotel_ryumeikantokyo.html'){

		header("Location: ./sp/hotel_ryumeikantokyo.html");
		exit;

	}else if($file_name=='hotel_sakurafleuraoyama.html'){

		header("Location: ./sp/hotel_sakurafleuraoyama.html");
		exit;

	}else if($file_name=='hotel_sakuratowertokyo.html'){

		header("Location: ./sp/hotel_sakuratowertokyo.html");
		exit;

	}else if($file_name=='hotel_seiyoginza.html'){

		header("Location: ./sp/hotel_seiyoginza.html");
		exit;

	}else if($file_name=='hotel_shangrilatokyou.html'){

		header("Location: ./sp/hotel_shangrilatokyou.html");
		exit;

	}else if($file_name=='hotel_sheratonmiyakohoteltokyo.html'){

		header("Location: ./sp/hotel_sheratonmiyakohoteltokyo.html");
		exit;

	}else if($file_name=='hotel_shinjukuprince.html'){

		header("Location: ./sp/hotel_shinjukuprince.html");
		exit;

	}else if($file_name=='hotel_shinjukutheagnes.html'){

		header("Location: ./sp/hotel_shinjukutheagnes.html");
		exit;

	}else if($file_name=='hotel_shinjukuwashington.html'){

		header("Location: ./sp/hotel_shinjukuwashington.html");
		exit;

	}else if($file_name=='hotel_sibuyaexcelhoteltokyu.html'){

		header("Location: ./sp/hotel_sibuyaexcelhoteltokyu.html");
		exit;

	}else if($file_name=='hotel_sunmemberstokyoshinjyuku.html'){

		header("Location: ./sp/hotel_sunmemberstokyoshinjyuku.html");
		exit;

	}else if($file_name=='hotel_sunplaza.html'){

		header("Location: ./sp/hotel_sunplaza.html");
		exit;

	}else if($file_name=='hotel_sunrouteakasaka.html'){

		header("Location: ./sp/hotel_sunrouteakasaka.html");
		exit;

	}else if($file_name=='hotel_sunshinecity.html'){

		header("Location: ./sp/hotel_sunshinecity.html");
		exit;

	}else if($file_name=='hotel_thecapitolhoteltokyu.html'){

		header("Location: ./sp/hotel_thecapitolhoteltokyu.html");
		exit;

	}else if($file_name=='hotel_thenewotani.html'){

		header("Location: ./sp/hotel_thenewotani.html");
		exit;

	}else if($file_name=='hotel_thepeninsula.html'){

		header("Location: ./sp/hotel_thepeninsula.html");
		exit;

	}else if($file_name=='hotel_theritzcarlton.html'){

		header("Location: ./sp/hotel_theritzcarlton.html");
		exit;

	}else if($file_name=='hotel_thewestintokyo.html'){

		header("Location: ./sp/hotel_thewestintokyo.html");
		exit;

	}else if($file_name=='hotel_tobuhotellevanttokyo.html'){

		header("Location: ./sp/hotel_tobuhotellevanttokyo.html");
		exit;

	}else if($file_name=='hotel_tokyodome.html'){

		header("Location: ./sp/hotel_tokyodome.html");
		exit;

	}else if($file_name=='hotel_tokyograndhotel.html'){

		header("Location: ./sp/hotel_tokyograndhotel.html");
		exit;

	}else if($file_name=='hotel_tokyogreenpalace.html'){

		header("Location: ./sp/hotel_tokyogreenpalace.html");
		exit;

	}else if($file_name=='hotel_tokyoprince.html'){

		header("Location: ./sp/hotel_tokyoprince.html");
		exit;

	}else if($file_name=='hotel_u-port.html'){

		header("Location: ./sp/hotel_u-port.html");
		exit;

	}else if($file_name=='hotel_viainnakihabara.html'){

		header("Location: ./sp/hotel_viainnakihabara.html");
		exit;

	}else{
		
		header("Location: ./sp/index.html");
		exit;
		
	}
	exit;

}


?>