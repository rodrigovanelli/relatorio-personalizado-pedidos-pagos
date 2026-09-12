# Relatório Personalizado de Pedidos Pagos WooCommerce — Plugin WordPress/WooCommerce

Plugin administrativo para WooCommerce que gera uma lista personalizada de pedidos pagos, com edição de nomes e quantidades personalizadas de produtos, com exportação em PDF.

## Motivação

Em lojas WooCommerce com nomenclatura técnica de produtos (SKUs, variações, nomes longos), o relatório operacional do dia a dia (ex: separação/expedição de pedidos) fica mais difícil de ler do que precisaria. Este plugin permite:

- Consolidar rapidamente quais produtos saíram, em qual quantidade, por pedido — sem precisar abrir cada pedido individualmente no admin padrão do WooCommerce.
- Usar apelidos internos para os produtos (em vez do nome de cadastro), facilitando a leitura por quem opera o processo físico (separação, embalagem, etc).
- Ajustar manualmente a quantidade exibida e somada quando houver divergência entre o pedido registrado e o que efetivamente será processado, somando por exemplo kits com 3, 6, etc.
- Exportar essa lista em PDF para uso offline (impressão, conferência manual).

## Funcionalidades planejadas

1. Ícone e página personalizada na barra lateral do admin do WordPress.
2. Lista de pedidos com status pago/processando, com botão de exportação em PDF — o PDF reflete os apelidos, quantidades e colunas visíveis configurados na tela, não os dados originais do sistema.
3. Colunas: número do pedido, nome do cliente no endereço de entrega, produtos vendidos (com quantidade individual), frete e total do pedido — com totalizadores ao final de cada coluna numérica e opção de ocultar/exibir colunas.
4. Edição de apelido por produto (persistente — reutilizado sempre que o produto aparecer novamente na lista).
5. Edição de quantidade por produto (persistente — reutilizado sempre que o produto aparecer novamente na lista) diretamente na lista.

## Diagrama de Caso de Uso

```mermaid
flowchart TD
    Admin([Administrador])
    UC1(("Visualizar lista de pedidos"))
    UC2(("Editar apelido do produto"))
    UC3(("Editar quantidade do produto"))
    UC4(("Baixar PDF da lista"))
    UC5(("Ocultar/Exibir coluna da lista"))

    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC4
    Admin --> UC5

    UC2 -.->|"<<include>>"| UC1
    UC3 -.->|"<<include>>"| UC1
    UC4 -.->|"<<include>>"| UC1
    UC5 -.->|"<<include>>"| UC1
    UC4 -.->|"<<include>>"| UC5
    UC4 -.->|"<<include>>"| UC2
    UC4 -.->|"<<include>>"| UC3
```

## Diagrama de Sequência — Visualizar lista de pedidos

```mermaid
sequenceDiagram
    actor Admin
    participant WP as WordPress Admin
    participant Plugin as Plugin (RelatorioPedidosPagos)
    participant Woo as WooCommerce (wc_get_orders)
    participant DB as Banco de Dados (config. salvas)
    participant Cache as Transient API (cache temporário)

    Admin->>WP: Acessa página do plugin
    WP->>Plugin: Chama callback registrado (renderizar_pagina)
    Plugin->>Woo: wc_get_orders(status: pago/processando)
    Woo-->>Plugin: Retorna lista de pedidos
    Plugin->>DB: Busca apelidos/quantidades/visibilidade salvos
    DB-->>Plugin: Retorna configurações salvas
    Plugin->>Plugin: Monta estrutura da tabela (aplica apelidos, quantidades, totais)
    Plugin->>Cache: Salva dados montados em transient (para uso posterior no PDF)
    Cache-->>Plugin: Confirma gravação
    Plugin-->>WP: Retorna HTML renderizado
    WP-->>Admin: Exibe página com a lista de pedidos
```

## Diagrama de Sequência — Editar apelido e/ou quantidade do produto

```mermaid
sequenceDiagram
    actor Admin
    participant JS as Navegador (JS)
    participant WP as WordPress Admin
    participant Plugin as Plugin (RelatorioPedidosPagos)
    participant DB as Banco de Dados (config. salvas)

    Admin->>JS: Clica em "editar" no produto
    JS-->>Admin: Exibe campos inline (apelido e/ou quantidade)
    Admin->>JS: Edita apelido e/ou quantidade (repete para outros produtos, se desejar)
    Admin->>JS: Clica em "Atualizar página"
    JS->>WP: Submete formulário (POST com todas as edições em lote)
    WP->>Plugin: Chama callback de processamento do formulário
    Plugin->>DB: Salva cada edição (produto -> apelido e/ou quantidade)
    DB-->>Plugin: Confirma gravação
    Plugin-->>WP: Redireciona (PRG) para a página do relatório
    WP->>Plugin: Chama callback renderizar_pagina novamente
    Plugin->>DB: Busca apelidos/quantidades salvos atualizados
    DB-->>Plugin: Retorna configurações atualizadas
    Plugin-->>WP: Retorna HTML atualizado com apelidos/quantidades
    WP-->>Admin: Exibe lista com edições atualizadas
```

## Diagrama de Sequência — Baixar PDF da lista

```mermaid
sequenceDiagram
    actor Admin
    participant WP as WordPress Admin
    participant Plugin as Plugin (RelatorioPedidosPagos)
    participant Cache as Transient API (cache temporário)
    participant PDF as DomPDF

    Admin->>WP: Clica em "Baixar PDF"
    WP->>Plugin: Chama callback de geração de PDF
    Plugin->>Cache: Recupera dados já montados da lista (transient)
    Cache-->>Plugin: Retorna dados (apelidos, quantidades, colunas visíveis)
    Plugin->>Plugin: Monta HTML da tabela respeitando colunas visíveis
    Plugin->>PDF: Converte HTML em PDF (DomPDF)
    PDF-->>Plugin: Retorna arquivo PDF gerado
    Plugin-->>WP: Envia resposta com headers de download (Content-Type: application/pdf)
    WP-->>Admin: Navegador inicia o download do arquivo
```

## Status do projeto

🚧 Em desenvolvimento — fase de modelagem (diagramas de caso de uso, sequência e classe).

## Stack

- WordPress + WooCommerce
- PHP 8.1+ (recomendado 8.3+, conforme padrão atual da WooCommerce)