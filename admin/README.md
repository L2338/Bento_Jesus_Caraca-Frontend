# Painel Administrativo - Bento de Jesus Caraça

Este é o painel administrativo para gerenciamento do conteúdo do site Bento de Jesus Caraça.

## Estrutura do Projeto

- **auth/**: Autenticação e gestão de usuários
- **config/**: Configurações do sistema
- **core/**: Funções utilitárias do sistema
- **templates/**: Templates reutilizáveis (header, footer, sidebar)
- **assets/**: Recursos estáticos (CSS, JavaScript, imagens)
- **obras/**: Gestão de obras literárias
- **timeline/**: Gestão da linha do tempo histórica
- **escolas/**: Gestão de escolas profissionais
- **legado/**: Gestão do legado histórico e cultural
- **galeria/**: Gestão de imagens e mídia
- **dashboard.php**: Página principal do painel
- **index.php**: Login e redirecionamento

## Dependências

- PHP 8.x
- MySQL/MariaDB
- Bootstrap 5.2.3
- jQuery 3.6.4
- DataTables 1.13.4
- Bootstrap Icons 1.10.5

## Como Iniciar

1. Configure as constantes em `config/app-config.php`
2. Garanta acesso ao banco de dados
3. Acesse o painel via `/admin/`

## Arquitetura

O painel segue uma arquitetura modular com separação clara de responsabilidades:

- **Configuração**: Variáveis de ambiente e constantes em `config/app-config.php`
- **Lógica central**: Funções utilitárias reutilizáveis em `core/functions.php`
- **UI**: Templates padronizados em `templates/`
- **Funcionalidades**: Organizadas em seções independentes (obras, escolas, etc.)

## Segurança

- Validação de entrada em todas as rotas
- Proteção contra CSRF
- Sanitização de saída
- Controle de acesso baseado em sessão

## Guia de Estilo

- Utilizar camelCase para funções e variáveis
- Utilizar snake_case para nomes de arquivos
- Seguir padrões PSR para organização de código
- Documentar funções com comentários PHPDoc 