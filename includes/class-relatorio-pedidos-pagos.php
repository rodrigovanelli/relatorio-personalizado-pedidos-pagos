<?php
if (! defined('ABSPATH')) {
    exit;
}

require_once RPPP_PATH . 'includes/class-relatorio-dados.php';
require_once RPPP_PATH . 'includes/class-relatorio-view.php';

class RelatorioPedidosPagos
{

    public function registrar_hooks(): void
    {
        add_action('admin_menu', [$this, 'registrar_menu']);
    }

    public function registrar_menu(): void
    {
        add_menu_page(
            'Relatório de Pedidos Pagos',        // título da página
            'Pedidos Pagos',                     // texto no menu lateral
            'manage_woocommerce',                // permissão necessária
            'rppp-relatorio',                    // slug da página
            [$this, 'renderizar_pagina'],      // método que desenha a página
            $this->icone_menu(),                 // ícone personalizado (SVG)
            56                                   // posição no menu
        );
    }

    private function icone_menu(): string
    {
        $svg = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="black" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0m0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0"/>
        </svg>
        SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function renderizar_pagina(): void
    {
        $dados = new RelatorioDados();
        $view  = new RelatorioView();

        // A view escapa toda a saída (esc_html) ao montar o HTML.
        echo $view->renderizar_tabela($dados->montar_estrutura()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
