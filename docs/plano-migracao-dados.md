# Plano de Migração de Dados - Estáticos para Banco de Dados

## Visão Geral
Este documento descreve o plano para migrar o conteúdo estático do site para o banco de dados MySQL, incluindo estatísticas, textos de seções e blocos cronológicos da timeline.

## Objetivos
- Facilitar a atualização de conteúdo através de interface administrativa
- Manter consistência dos dados em todo o site
- Permitir gestão centralizada de estatísticas e textos informativos
- Possibilitar a manutenção dinâmica dos blocos cronológicos da timeline
- Integrar gerenciamento de texto introdutório e timeline em um único módulo (Vida)

## Status das Tarefas

| # | Tarefa | Status | Data Conclusão | Responsável |
|---|--------|--------|----------------|-------------|
| 1 | Análise inicial dos arquivos | ✅ Concluído | 2024-06-15 | Equipe Dev |
| 2 | Criação das tabelas no banco de dados | ✅ Concluído | 2024-06-16 | Equipe DB |
| 3 | Migração dos dados estáticos | ✅ Concluído | 2024-06-16 | Equipe DB |
| 4 | Modificação da página index.php | ✅ Concluído | 2024-06-17 | Equipe Front |
| 5 | Modificação da página vida.php | ✅ Concluído | 2024-06-17 | Equipe Front |
| 6 | Criação de interface admin para estatísticas | ✅ Concluído | 2024-06-18 | Equipe Back |
| 7 | Criação de interface admin para textos e integração da timeline | ✅ Concluído | 2024-06-18 | Equipe Back |
| 8 | Testes | ⏳ Em andamento | - | QA |
| 9 | Documentação | ✅ Concluído | 2024-06-20 | Equipe Doc |
| 10 | Implantação em produção | 🕒 Pendente | - | DevOps |
| 11 | Implementação do módulo Vida & Timeline integrado | ⏳ Em andamento | - | Equipe Back |

## Detalhes Técnicos

### Estrutura das Tabelas

#### Tabela: `estatisticas`
```sql
CREATE TABLE estatisticas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(50) NOT NULL UNIQUE,
    valor INT NOT NULL,
    descricao VARCHAR(100) NOT NULL,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Tabela: `textos_secoes`
```sql
CREATE TABLE textos_secoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(50) NOT NULL UNIQUE,
    conteudo TEXT NOT NULL,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### Tabela: `timeline_blocos`
```sql
CREATE TABLE timeline_blocos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    data_periodo VARCHAR(100) NOT NULL,
    conteudo TEXT NOT NULL,
    conteudo_expandido TEXT NOT NULL,
    imagem VARCHAR(255) NULL,
    ordem INT NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Mudanças Implementadas
1. Criação das tabelas no banco de dados MySQL
2. Migração dos dados estáticos para as tabelas
3. Adaptação das páginas PHP para ler dados do banco
4. Criação da interface de administração para gestão dos dados
5. Testes de funcionalidade e integração

### Próximos Passos
- Finalizar a documentação para usuários finais
- Preparar ambiente de produção para implantação
- Treinar equipe de conteúdo para utilização da nova interface

## Plano de Implementação: Módulo Vida & Timeline

### Visão Geral
Implementar um módulo administrativo integrado que permita gerenciar tanto o texto introdutório da página Vida quanto os blocos cronológicos da timeline dentro de uma única estrutura de interface, simplificando o gerenciamento de conteúdo.

### Desafios
- Gerenciar eficientemente um grande número de blocos (28+)
- Oferecer interface amigável para edição de conteúdos extensos
- Implementar funcionalidade de upload e gestão de imagens
- Garantir a ordenação correta dos blocos cronológicos
- Integrar gerenciamento de texto introdutório e blocos de timeline

### Estrutura de Arquivos
Todos os arquivos do módulo Vida serão centralizados na pasta `admin/vida/` sem subpastas, incluindo:

- `index.php` - Página principal com opções para gerenciar texto e timeline
- `timeline_list.php` - Listagem de blocos da timeline com ações
- `timeline_add.php` - Formulário para adicionar novos blocos
- `timeline_edit.php` - Edição de blocos existentes
- `timeline_view.php` - Visualização detalhada de blocos
- `timeline_reorder.php` - Interface para reordenar blocos
- `intro_edit.php` - Edição do texto introdutório da página Vida

### Abordagem Implementada

#### 1. Estrutura Visual
- **Página Integrada**: Acesso centralizado para gerenciar texto introdutório e timeline
- **Lista de Blocos**: Exibir blocos da timeline com paginação e pesquisa
- **Editor WYSIWYG**: Para texto introdutório e conteúdo dos blocos
- **Ordenação Intuitiva**: Interface para reorganizar os blocos da timeline
- **Visualização Contextual**: Prévia dos blocos como aparecem no site

#### 2. Funcionalidades Principais
- **Gerenciamento Texto Introdutório**: Edição completa do texto principal
- **CRUD Timeline**: Adicionar, visualizar, editar e excluir blocos
- **Upload de Imagens**: Sistema para gerenciar imagens dos blocos
- **Ativação/Desativação**: Controle de visibilidade dos blocos
- **Reordenação**: Arrastar e soltar para definir a ordem dos blocos

#### 3. Implementação Técnica
- **Interface Única**: Todas as funções acessíveis a partir do menu Vida & Timeline
- **Validação de Dados**: Garantir integridade e formato correto
- **Feedback Visual**: Confirmações e alertas para ações do usuário
- **Navegação Intuitiva**: Fluxo lógico entre as diferentes ações de gerenciamento
- **Responsividade**: Interface adaptável para diferentes dispositivos

### Cronograma de Desenvolvimento
| Etapa | Descrição | Tempo Estimado |
|-------|-----------|----------------|
| 1 | Desenvolvimento da estrutura base | 1 dia |
| 2 | Implementação do gerenciamento de texto | 1 dia |
| 3 | Implementação do CRUD da timeline | 2 dias |
| 4 | Sistema de upload de imagens | 1 dia |
| 5 | Funcionalidade de ordenação | 1 dia |
| 6 | Testes e ajustes | 1 dia |
| 7 | Documentação e treinamento | 1 dia |

### Progresso Atual
A implementação do módulo Vida está em andamento, com foco na integração do gerenciamento de texto introdutório e blocos da timeline em uma única interface coesa. 