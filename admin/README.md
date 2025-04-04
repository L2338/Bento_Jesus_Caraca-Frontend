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

# Sistema de Notificações

## Visão Geral

O sistema de notificações foi implementado para manter os administradores informados sobre eventos importantes no sistema. As notificações são exibidas em tempo real e permitem interação.

## Funcionalidades

1. **Botão de Notificação na Barra Superior**
   - Exibe o número de notificações não lidas
   - Dropdown com as 5 notificações mais recentes
   - Opções para marcar como lidas e ver todas

2. **Página de Notificações**
   - Acesse em `Sistema > Notificações` no menu lateral
   - Lista completa de todas as notificações (lidas e não lidas)
   - Opções para marcar individual ou coletivamente como lidas

3. **Tipos de Notificações**
   - Usuário (azul): Relacionadas a contas de usuários
   - Obra (azul escuro): Relacionadas ao cadastro de obras
   - Sistema (cinza): Atualizações e eventos do sistema
   - Sucesso (verde): Operações bem-sucedidas
   - Alerta (amarelo): Avisos e lembretes
   - Erro (vermelho): Problemas e erros críticos

## Como Usar

### Visualizar Notificações
1. Clique no ícone de sino na barra superior
2. As notificações não lidas terão destaque
3. Clique em uma notificação para abrir o link relacionado (se houver)

### Marcar como Lida
1. Ao clicar em uma notificação, ela é automaticamente marcada como lida
2. Na página de notificações, você pode marcar individualmente usando o botão verde
3. Use o botão "Marcar todas como lidas" para limpar todas as notificações

### Criar Notificações pelo Código

Para adicionar notificações programaticamente:

```php
// Verificar se a função existe (para compatibilidade)
if (function_exists('add_notification')) {
    add_notification(
        $user_id,   // ID do usuário (0 para todos os usuários)
        $tipo,      // 'success', 'warning', 'danger', 'obra', 'usuario', 'sistema'
        $titulo,    // Título da notificação
        $mensagem,  // Corpo da mensagem
        $link       // Link opcional para mais detalhes (ou null)
    );
}
```

## Configuração

Para criar notificações de teste, acesse: `/admin/scripts/create_notifications.php`

## Banco de Dados

As notificações são armazenadas na tabela `Notificacoes` com a seguinte estrutura:

- `id_notificacao`: ID único da notificação
- `id_admin`: ID do usuário (0 = todos os usuários)
- `tipo`: Categoria da notificação
- `titulo`: Título curto
- `mensagem`: Detalhes da notificação
- `link`: URL opcional para mais informações
- `lida`: Estado da notificação (0 = não lida, 1 = lida)
- `created_at`: Data e hora de criação 