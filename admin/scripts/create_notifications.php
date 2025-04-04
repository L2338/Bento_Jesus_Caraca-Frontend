<?php
/**
 * Script para adicionar notificações ao sistema
 * Útil para testes iniciais
 */

// Incluir configurações e funções
require_once __DIR__ . '/../config/app-config.php';
require_once __DIR__ . '/../core/functions.php';

// Verificar se o usuário está logado como administrador
require_login();

// Obter conexão com o banco de dados
$conn = require_once __DIR__ . '/../../ConfigBD.php';

// Exemplos de notificações
$notifications = [
    [
        'id_admin' => 0, // 0 significa para todos os usuários
        'tipo' => 'success',
        'titulo' => 'Sistema de notificações ativado',
        'mensagem' => 'O sistema de notificações está funcionando corretamente.',
        'link' => null
    ],
    [
        'id_admin' => 0,
        'tipo' => 'obra',
        'titulo' => 'Nova obra cadastrada',
        'mensagem' => 'Uma nova obra foi adicionada ao sistema.',
        'link' => ADMIN_URL . 'obras/'
    ],
    [
        'id_admin' => 0,
        'tipo' => 'warning',
        'titulo' => 'Backup programado',
        'mensagem' => 'Um backup do sistema está programado para hoje às 23:00.',
        'link' => null
    ],
    [
        'id_admin' => 0,
        'tipo' => 'usuario',
        'titulo' => 'Novo usuário registrado',
        'mensagem' => 'Um novo administrador foi adicionado ao sistema.',
        'link' => ADMIN_URL . 'users.php'
    ],
    [
        'id_admin' => 0,
        'tipo' => 'sistema',
        'titulo' => 'Atualização do sistema',
        'mensagem' => 'O sistema foi atualizado para a versão 1.2.0',
        'link' => null
    ]
];

// Inserir notificações
$success_count = 0;
$error_count = 0;

foreach ($notifications as $notification) {
    if (add_notification(
        $notification['id_admin'],
        $notification['tipo'],
        $notification['titulo'],
        $notification['mensagem'],
        $notification['link']
    )) {
        $success_count++;
    } else {
        $error_count++;
    }
}

// Exibir resultado
echo "<h2>Resultado da criação de notificações</h2>";
echo "<p>Notificações adicionadas com sucesso: {$success_count}</p>";
echo "<p>Erros: {$error_count}</p>";

if ($success_count > 0) {
    echo "<p>As notificações foram criadas com sucesso! <a href='" . ADMIN_URL . "notifications.php'>Ver notificações</a></p>";
} else {
    echo "<p>Ocorreram erros ao criar as notificações. Verifique se a tabela existe e tente novamente.</p>";
}
?> 