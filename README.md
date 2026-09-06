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

    Admin->>WP: Acessa página do plugin
    WP->>Plugin: Chama callback registrado (renderizar_pagina)
    Plugin->>Woo: wc_get_orders(status: pago/processando)
    Woo-->>Plugin: Retorna lista de pedidos
    Plugin->>DB: Busca apelidos/quantidades/visibilidade salvos
    DB-->>Plugin: Retorna configurações salvas
    Plugin->>Plugin: Monta estrutura da tabela (aplica apelidos, quantidades, totais)
    Plugin-->>WP: Retorna HTML renderizado
    WP-->>Admin: Exibe página com a lista de pedidos
```

## Diagrama de Sequência — Editar apelido do produto

```mermaid
sequenceDiagram
    actor Admin
    participant JS as Navegador (JS)
    participant WP as WordPress Admin
    participant Plugin as Plugin (RelatorioPedidosPagos)
    participant DB as Banco de Dados (config. salvas)

    Admin->>JS: Clica em "editar" no produto
    JS-->>Admin: Exibe campo de texto inline para apelido
    Admin->>JS: Digita apelido (repete para outros produtos, se desejar)
    Admin->>JS: Clica em "Atualizar página"
    JS->>WP: Submete formulário (POST com todos os apelidos editados)
    WP->>Plugin: Chama callback de processamento do formulário
    Plugin->>DB: Salva cada apelido editado (produto -> apelido)
    DB-->>Plugin: Confirma gravação
    Plugin-->>WP: Redireciona (PRG) para a página do relatório
    WP->>Plugin: Chama callback renderizar_pagina novamente
    Plugin->>DB: Busca apelidos salvos atualizados
    DB-->>Plugin: Retorna apelidos
    Plugin-->>WP: Retorna HTML atualizado com novos apelidos
    WP-->>Admin: Exibe lista com apelidos atualizados
```

## Status do projeto

🚧 Em desenvolvimento — fase de modelagem (diagramas de caso de uso, sequência e classe).

## Stack

- WordPress + WooCommerce
- PHP 8.1+ (recomendado 8.3+, conforme padrão atual da WooCommerce)