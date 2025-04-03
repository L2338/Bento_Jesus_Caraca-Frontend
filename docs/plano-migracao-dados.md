# Plano de Migração de Dados - Estáticos para Banco de Dados

## Visão Geral
Este documento descreve o plano para migrar o conteúdo estático do site para o banco de dados MySQL, incluindo estatísticas e textos de seções.

## Objetivos
- Facilitar a atualização de conteúdo através de interface administrativa
- Manter consistência dos dados em todo o site
- Permitir gestão centralizada de estatísticas e textos informativos

## Status das Tarefas

| # | Tarefa | Status | Data Conclusão | Responsável |
|---|--------|--------|----------------|-------------|
| 1 | Análise inicial dos arquivos | ✅ Concluído | 2024-06-15 | Equipe Dev |
| 2 | Criação das tabelas no banco de dados | ✅ Concluído | 2024-06-16 | Equipe DB |
| 3 | Migração dos dados estáticos | ✅ Concluído | 2024-06-16 | Equipe DB |
| 4 | Modificação da página index.php | ✅ Concluído | 2024-06-17 | Equipe Front |
| 5 | Modificação da página vida.php | ✅ Concluído | 2024-06-17 | Equipe Front |
| 6 | Criação de interface admin para estatísticas | ✅ Concluído | 2024-06-18 | Equipe Back |
| 7 | Criação de interface admin para textos | ✅ Concluído | 2024-06-18 | Equipe Back |
| 8 | Testes | ✅ Concluído | 2024-06-19 | QA |
| 9 | Documentação | ✅ Concluído | 2024-06-20 | Equipe Doc |
| 10 | Implantação em produção | 🕒 Pendente | - | DevOps |

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