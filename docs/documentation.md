# Documentação do Projeto - Biblioteca Digital Bento de Jesus Caraça

## Estrutura do Projeto

```
projeto/
├── admin/              # Área administrativa
├── assets/            # Recursos estáticos
│   ├── css/          # Arquivos CSS
│   ├── js/           # Arquivos JavaScript
│   ├── img/          # Imagens
│   ├── pdf/          # Arquivos PDF das obras
│   └── vendor/       # Bibliotecas de terceiros
├── docs/             # Documentação
└── forms/            # Formulários do sistema
```

## Arquivos Principais

### Páginas PHP

1. **index.php**
   - Página inicial do site
   - Apresenta visão geral do projeto
   - Seções principais: banner, sobre, destaques

2. **obras.php**
   - Catálogo de obras
   - Visualizador de PDF integrado
   - Sistema de filtro por temas

3. **vida.php**
   - Biografia de Bento de Jesus Caraça
   - Timeline de eventos importantes
   - Galeria de fotos

4. **retratos.php**
   - Galeria de retratos e fotografias
   - Linha do tempo visual
   - Descrições detalhadas

5. **legado.php**
   - Impacto e contribuições
   - Citações e referências
   - Material histórico

6. **contact.php**
   - Formulário de contato
   - Informações de contato
   - Mapa de localização

### Componentes

1. **Menu.php**
   - Menu de navegação principal
   - Responsivo para mobile
   - Links para todas as seções

2. **footer.php**
   - Rodapé padrão
   - Links sociais
   - Informações de copyright

3. **ConfigBD.php**
   - Configurações do banco de dados
   - Conexão MySQL
   - Constantes do sistema

### JavaScript

1. **pdf-viewer.js**
   - Visualizador de PDF personalizado
   - Animação de virada de página
   - Controles de navegação
   - Responsivo

2. **main.js**
   - Funcionalidades gerais do site
   - Animações e transições
   - Gerenciamento de formulários
   - Interações do usuário

### CSS

1. **main.css**
   - Estilos globais
   - Layout responsivo
   - Temas e cores
   - Componentes UI

## Banco de Dados

### Tabelas Principais

1. **Obras**
   ```sql
   CREATE TABLE Obras (
       id INT PRIMARY KEY AUTO_INCREMENT,
       titulo VARCHAR(255) NOT NULL,
       descricao TEXT,
       pdf VARCHAR(255) NOT NULL,
       imagem_capa VARCHAR(255),
       autor VARCHAR(255),
       id_tema INT,
       ano INT
   );
   ```

2. **Temas**
   ```sql
   CREATE TABLE Temas (
       id_tema INT PRIMARY KEY AUTO_INCREMENT,
       Nome_tema VARCHAR(255) NOT NULL
   );
   ```

3. **Administradores**
   ```sql
   CREATE TABLE Administradores (
       id INT PRIMARY KEY AUTO_INCREMENT,
       admin VARCHAR(255) NOT NULL,
       senha VARCHAR(255) NOT NULL
   );
   ```

## Funcionalidades Principais

### 1. Visualizador de PDF
- Renderização de PDFs no navegador
- Animação de virada de página
- Adaptação para mobile/desktop
- Controles de navegação intuitivos

### 2. Sistema de Administração
- Login seguro
- Gerenciamento de obras
- Upload de PDFs
- Edição de conteúdo

### 3. Catálogo de Obras
- Filtro por temas
- Visualização em grid
- Download direto
- Leitura online

### 4. Responsividade
- Layout adaptativo
- Imagens otimizadas
- Navegação mobile-friendly
- Performance otimizada

## Guias de Desenvolvimento

### Padrões de Código

1. **PHP**
   - PSR-4 para autoloading
   - Comentários PHPDoc
   - Validação de entrada
   - Prepared statements

2. **JavaScript**
   - ES6+ features
   - Módulos organizados
   - Documentação JSDoc
   - Tratamento de erros

3. **CSS**
   - BEM methodology
   - Mobile-first
   - Variáveis CSS
   - Prefixos vendor

### Segurança

1. **Autenticação**
   - Hash de senhas
   - Tokens CSRF
   - Sessões seguras
   - Validação de entrada

2. **Banco de Dados**
   - Prepared statements
   - Escape de dados
   - Validação de tipos
   - Backup regular

### Performance

1. **Otimizações**
   - Minificação de assets
   - Compressão de imagens
   - Lazy loading
   - Caching

2. **Monitoramento**
   - Logs de erro
   - Métricas de performance
   - Análise de uso
   - Backup automático

## Manutenção

### Rotinas

1. **Diárias**
   - Verificação de logs
   - Backup de dados
   - Monitoramento de erros

2. **Semanais**
   - Atualização de conteúdo
   - Verificação de links
   - Limpeza de cache

3. **Mensais**
   - Análise de métricas
   - Otimização de banco
   - Revisão de segurança

## Contato e Suporte

Para questões técnicas ou suporte:
- Email: suporte@biblioteca-bjc.pt
- Tel: +351 XX XXX XXXX
- Horário: 9h-18h (GMT)

---

Última atualização: Abril 2024 