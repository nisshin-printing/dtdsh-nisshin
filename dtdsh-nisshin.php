<?php
/*
Plugin Name: dtdsh.nisshin
Plugin URI: http://dtdsh.com/
Description: 日進印刷株式会社が作成するカスタム設定用のプラグインです。
Author: 日進印刷株式会社
Author URI: http://dtdsh.com/
Text Domain: dtdsh
Domain Path: /languages/
Version: 1.0.0
*/
//========================  Define ========================================================================//
define( 'DTDSH_VERSION', '1.0.0' );
define( 'DTDSH_REQUIRED_WP_VERSION', '4.5' );
define( 'DTDSH_PLUGIN', __FILE__ );
define( 'DTDSH_HOME_URL', home_url( '/' ) );
define( 'DTDSH_PLUGIN_DIR', untrailingslashit( dirname( DTDSH_PLUGIN ) ) );
define( 'DTDSH_PLUGIN_URL', plugin_dir_url( DTDSH_PLUGIN ) );
define( 'DTDSH_PLUGIN_IMG', DTDSH_PLUGIN_URL . 'assets/img/' );
define( 'DTDSH_PLUGIN_CSS', DTDSH_PLUGIN_URL . 'assets/css/' );
define( 'DTDSH_PLUGIN_JS', DTDSH_PLUGIN_URL . 'assets/js/' );
define( 'DTDSH_PLUGIN_INC', DTDSH_PLUGIN_DIR . '/inc/' );

// ============================== パブリサイズ共有の文言を変更 ============================================================================= //
if ( ! function_exists( 'change_jetpack_publicize_content' ) ) :
function change_jetpack_publicize_content( $post_id, $post ) {
	$POST_MESS = '_wpas_mess';
	if ( ! in_array( $post->post_status, array( 'publish', 'future' ) ) ) {
		return;
	}
	if ( ! empty( $_POST['wpas_title'] ) ) {
		return;
	}
	if ( get_post_meta( $post_id, $POST_MESS, TRUE ) ) {
		return;
	}
	$publicize_custom_message = '弁護士ブログランキングに参加しています！ブログを開いて「弁護士」バナーをポチッと！押していただけると嬉しいです。(^_^)/';
	update_post_meta( $post_id, $POST_MESS, $publicize_custom_message );
	$_POST['wpas_title'] = $publicize_custom_message;
}
add_action( 'save_post', 'change_jetpack_publicize_content', 19, 2 );
endif;
// ============================== 独自OGP導入 ============================================================================= //
if ( ! function_exists( 'dtdsh_load_ogp' ) && ! is_admin() ) :
function dtdsh_load_ogp() {
	global $post;
	$url = '';
	$title = wp_get_document_title();
	$site_name = get_bloginfo( 'name' );
	if ( is_singular() ) {
		$cont = $post->post_content;
		$preg = '/<img.*?src=(["\'])(.+?)\1.*?>i/';
		$url = get_the_permalink();
		if ( has_post_thumbnail() ) {
			$img_id = get_post_thumbnail_id();
			$img_arr = wp_get_attachment_image_src( $img_id, 'full' );
			$img = $img_arr[0];
		} elseif ( preg_match( $preg, $cont, $img_url ) ) {
			$img = $img_url[2];
		} else {
			$img = null;
		}
	} else {
		$url = get_bloginfo( 'url' );
		$title = $site_name;
		$img = null;
	}
	$img = ( $img != null ) ? $img : 'http://www.law-yamashita.com/wp-content/uploads/2015/05/k-yamashita.png';
	$desc = '弁護士ブログランキングに参加しています！ブログを開いて「弁護士」バナーをポチッと！押していただけると嬉しいです。(^_^)/';
?>
<meta property="og:type" content="<?php echo ( is_singular() ? 'article' : 'website' ); ?>">
<meta property="og:url" content="<?php echo $url; ?>">
<meta property="og:title" content="<?php echo $title; ?>">
<meta property="og:description" content="<?php echo $desc; ?>">
<meta property="og:image" content="<?php echo $img; ?>">
<meta property="og:site_name" content="<?php echo $site_name; ?>">
<meta property="og:locale" content="ja_JP">
<?php
if( is_singular() ) :
	$published_time = get_post( $post->ID )->post_date;
	$published_time = str_replace( ' ', 'T', $published_time ) . 'Z';
	$modified_time = get_post( $post->ID )->post_modified;
	$modified_time = str_replace( ' ', 'T', $modified_time ) . 'Z';
?>
<meta property="article:published_time" content="<?php echo $published_time ?>">
<meta property="article:modified_time" content="<?php echo $modified_time ?>"><?php
endif;
}
add_filter( 'wp_head', 'dtdsh_load_ogp', 3 );
endif;
// ============================== JetpackのOGPを無効化 ============================================================================= //
add_filter( 'jetpack_enable_open_graph', '__return_false' );
// ============================== Functions ============================================================================= //
// HTML_Format
if ( ! function_exists( 'dtdsh_html_format' ) ) :
function dtdsh_html_format( $contents, $on_s = true ) {
	// 連続改行削除
	if ( ! $on_s ) {
		$contents = preg_replace( '/(\n|\r|\r\n)+/us', "", $contents );
	} else {
		$contents = preg_replace( '/(\n|\r|\r\n)+/us', "\n", $contents );
	}
	// 行頭の余計な空白削除
	$contents = preg_replace( '/\n+\s*</', "\n" . '<', $contents );
	// タグ間の余計な空白や改行の削除
	$contents = preg_replace( '/>\s*?</', '><',$contents );
	// タブの削除
	$contents = str_replace( "\t", '', $contents );
	// ?ver= と ?=fit の除去
	$contents = preg_replace( '/\?(?:ver|fit)=(?:\S+)([\'|\"])/', '\1', $contents );
	return $contents;
}
// add_filter( 'wp_nav_menu', 'dtdsh_html_format', 10, 2 );
endif;