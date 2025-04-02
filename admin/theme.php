<?php
/**
 * Gerador dinâmico de temas CSS
 * Este arquivo gera o CSS personalizado com base nas preferências do usuário
 */

// Definir o tipo de conteúdo como CSS
header('Content-Type: text/css');

// Carregar a versão de cache (para evitar cache de navegador)
$version = isset($_GET['v']) ? $_GET['v'] : time();

// Definir temas disponíveis e suas cores
$theme_colors = [
    // Cores padrão do sistema
    'blue' => ['primary' => '#4e73df', 'gradient_start' => '#4e73df', 'gradient_end' => '#224abe'],
    'green' => ['primary' => '#1cc88a', 'gradient_start' => '#1cc88a', 'gradient_end' => '#13855c'],
    'red' => ['primary' => '#e74a3b', 'gradient_start' => '#e74a3b', 'gradient_end' => '#be2617'],
    'purple' => ['primary' => '#7952b3', 'gradient_start' => '#7952b3', 'gradient_end' => '#543b7e'],
    'orange' => ['primary' => '#fd7e14', 'gradient_start' => '#fd7e14', 'gradient_end' => '#c46a0f'],
    'teal' => ['primary' => '#20c9a6', 'gradient_start' => '#20c9a6', 'gradient_end' => '#178066'],
    'dark' => ['primary' => '#5a5c69', 'gradient_start' => '#5a5c69', 'gradient_end' => '#373840'],
    
    // Cores das escolas EPBJC
    'epbjc_red' => ['primary' => '#ac062a', 'gradient_start' => '#ac062a', 'gradient_end' => '#750418'],
    
    // Cores específicas de cada escola conforme a imagem
    'barreiro' => ['primary' => '#00bcd4', 'gradient_start' => '#00bcd4', 'gradient_end' => '#0097a7'],
    'porto' => ['primary' => '#0072c6', 'gradient_start' => '#0072c6', 'gradient_end' => '#005299'],
    'beja' => ['primary' => '#ff9800', 'gradient_start' => '#ff9800', 'gradient_end' => '#f57c00'],
    'lisboa' => ['primary' => '#4caf50', 'gradient_start' => '#4caf50', 'gradient_end' => '#388e3c'],
    'seixal' => ['primary' => '#009688', 'gradient_start' => '#009688', 'gradient_end' => '#00796b'],
];

// Definir cores de fundo disponíveis
$backgrounds = [
    'light' => '#f8f9fc', 
    'dark' => '#1a202c',
    'gray' => '#eaecf4',
];

// Obter tema escolhido pelo usuário (cookie) ou usar o padrão
$selected_theme = isset($_COOKIE['admin_theme_color']) && array_key_exists($_COOKIE['admin_theme_color'], $theme_colors) 
    ? $_COOKIE['admin_theme_color'] 
    : 'epbjc_red'; // Padrão agora é o vermelho da escola

// Obter cor de fundo escolhida pelo usuário (cookie) ou usar o padrão
$selected_background = isset($_COOKIE['admin_theme_background']) && array_key_exists($_COOKIE['admin_theme_background'], $backgrounds) 
    ? $_COOKIE['admin_theme_background'] 
    : 'light';

// Obter as cores do tema selecionado
$primary_color = $theme_colors[$selected_theme]['primary'];
$gradient_start = $theme_colors[$selected_theme]['gradient_start'];
$gradient_end = $theme_colors[$selected_theme]['gradient_end'];
$background_color = $backgrounds[$selected_background];

// Definir cores de texto e componentes baseadas no fundo
if ($selected_background === 'dark') {
    $text_color = '#e2e8f0';
    $card_bg = '#2d3748';
    $card_text = '#e2e8f0';
    $border_color = '#4a5568';
    $input_bg = '#4a5568';
    $input_text = '#e2e8f0';
} elseif ($selected_background === 'gray') {
    $text_color = '#333333';
    $card_bg = '#ffffff';
    $card_text = '#333333';
    $border_color = '#d1d5db';
    $input_bg = '#ffffff';
    $input_text = '#333333';
} else {
    // Light theme
    $text_color = '#333333';
    $card_bg = '#ffffff';
    $card_text = '#333333';
    $border_color = '#e3e6f0';
    $input_bg = '#ffffff';
    $input_text = '#333333';
}

// Obter valores RGB para as cores (usado em transparências)
$primary_rgb = implode(', ', sscanf($primary_color, "#%02x%02x%02x"));

// Ler o arquivo CSS base
$css_template = file_get_contents(__DIR__ . '/assets/css/theme-customizer.css');

// Substituir as variáveis do template
$css = str_replace(
    [
        '{{PRIMARY_COLOR}}', 
        '{{GRADIENT_START}}', 
        '{{GRADIENT_END}}', 
        '{{BACKGROUND_COLOR}}', 
        '{{PRIMARY_COLOR_RGB}}',
        '{{TEXT_COLOR}}',
        '{{CARD_BG}}',
        '{{CARD_TEXT}}',
        '{{BORDER_COLOR}}',
        '{{INPUT_BG}}',
        '{{INPUT_TEXT}}'
    ],
    [
        $primary_color, 
        $gradient_start, 
        $gradient_end, 
        $background_color, 
        $primary_rgb,
        $text_color,
        $card_bg,
        $card_text,
        $border_color,
        $input_bg,
        $input_text
    ],
    $css_template
);

// Retornar o CSS personalizado
echo $css;
?> 