<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RelatorioDados {

	private const STATUS_PEDIDOS = [ 'processing' ];

	public function buscar_pedidos(): array {
		return wc_get_orders( [
			'status'  => self::STATUS_PEDIDOS,
			'limit'   => -1,
			'orderby' => 'date',
			'order'   => 'ASC',
		] );
	}

	public function montar_estrutura(): array {
		$linhas = [];

		foreach ( $this->buscar_pedidos() as $pedido ) {
			$linhas[] = [
				'numero'  => $pedido->get_order_number(),
				'cliente' => $this->nome_cliente( $pedido ),
			];
		}

		return [
			'colunas' => [
				'numero'  => 'Pedido',
				'cliente' => 'Cliente',
			],
			'linhas'  => $linhas,
		];
	}

	private function nome_cliente( WC_Order $pedido ): string {
		$nome = trim( $pedido->get_shipping_first_name() . ' ' . $pedido->get_shipping_last_name() );

		if ( '' === $nome ) {
			$nome = trim( $pedido->get_billing_first_name() . ' ' . $pedido->get_billing_last_name() );
		}

		return $nome;
	}
}