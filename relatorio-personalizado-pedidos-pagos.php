<?php
/**
 * Plugin Name: Relatório Personalizado de Pedidos Pagos
 * Description: Lista personalizada de pedidos pagos do WooCommerce, com apelidos, quantidades e exportação em PDF.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * Author: Rodrigo Vanelli
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: relatorio-personalizado-pedidos-pagos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'RPPP_VERSION', '0.1.0' );
define( 'RPPP_PATH', plugin_dir_path( __FILE__ ) );

require_once RPPP_PATH . 'includes/class-relatorio-pedidos-pagos.php';

( new RelatorioPedidosPagos() )->registrar_hooks();