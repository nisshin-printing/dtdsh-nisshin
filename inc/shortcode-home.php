<?php

/*
 * 本体サイトの最新情報を取得
 */
add_shortcode( 'feed_home', 'dtdsh_feed_home' );
function dtdsh_feed_home( $atts ) {
	extract( shortcode_atts( array(
		'url' => 'https://www.law-yamashita.com/feed',
		'count' => 3
	), $atts ) );
	if ( ! $url ) return false;
	$url = sprintf( esc_html( "%s" ), $url );
	add_filter( 'wp_feed_cache_transient_lifetime', function() {
		return 1800;
	} );
	include_once( ABSPATH . WPINC . '/feed.php' );
	$feed = fetch_feed( $url );
	remove_filter( 'wp_feed_cache_transient_lifetime', function() {
		return 1800;
	} );
	$maxitems = 0;
	if ( ! is_wp_error( $feed ) ) {
		$maxitems = $feed->get_item_quantity( $count );
		$rss_items = $feed->get_items( 0, $maxitems );
	}
	$site_title = $feed->get_title();
	$site_url = $feed->get_permalink();
	
	$str = '';
	if ( 0 !== $maxitems ) {
		date_default_timezone_set('Asia/Tokyo');
		foreach ( $rss_items as $item ) {
			$f_link = esc_url( $item->get_permalink() );
			$f_date = $item->get_date( 'Y年m月d日' );
			$f_title = esc_html( $item->get_title() );
			if ( preg_match_all( '/<img.*?src=(["\'])(.+?)\1.*?>/i', $item->get_content(), $img_array ) ) {
				$site_img = $img_array[2][1];
				$str .= <<< EOM
<li class="dtdsh-entry dtdsh-clearfix">
	<div class="dtdsh-float-left"><img src="{$site_img}" alt="山下江法律事務所最新情報のサムネイル画像"></div>
	<div class="dtdsh-float-target">
		<p class="dtdsh-date">{$f_date}</p>
		<h3 class="dtdsh-title"><a href="{$f_link}" title="{$f_title}" target="_blank">{$f_title}</a></h3>
	</div>
</li>
EOM;
			} else {
				$str .= <<< EOM
<li class="dtdsh-entry">
	<p class="dtdsh-date">{$f_date}</p>
	<h3 class="dtdsh-title"><a href="{$f_link}" title="{$f_title}" target="_blank">{$f_title}</a></h3>
</li>
EOM;
			}
		}
	} else {
		$str .= '表示するものがありません。';
	}
	$output = <<< EOM
<ul class="dtdsh-feed-home">
	<h2>山下江法律事務所の最新情報<a href="{$site_url}" target="_blank" class="dtdsh-link_more">サイトを見る<i class="fa fa-external-link"></i></a></h2>
	{$str}
</ul>
EOM;
	return $output;
}


/*
 * スタイルシート
 */
add_action( 'wp_head', function() {
	$min_css = file_get_contents( DTDSH_PLUGIN_CSS . 'style.css' );
	echo <<< EOS
<style>{$min_css}</style>
EOS;
});