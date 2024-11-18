<?php

require_once 'init.php';

// Define o tema e a classe solicitada
$theme  = $ini['general']['theme'] ?? 'theme3'; // Tema padrão definido no application.ini
$class  = $_REQUEST['class'] ?? ''; // Classe solicitada via URL
$public = true; // Torna todas as classes públicas (desativa autenticação)

// Função para renderizar o layout com o menu
function renderLayout($theme, $menuFile = 'menu.xml') {
    $content = file_get_contents("app/templates/{$theme}/layout.html");
    $menu = AdiantiMenuBuilder::parse($menuFile, $theme);
    $content = str_replace('{MENU}', $menu, $content);
    return $content;
}

// Renderiza o layout completo
$content = renderLayout($theme);

// Traduz e parseia o conteúdo
$content = ApplicationTranslator::translateTemplate($content);
$content = AdiantiTemplateParser::parse($content);

// Exibe o layout
echo $content;

// Processa a classe solicitada
if ($class) {
    $method = $_REQUEST['method'] ?? null;
    AdiantiCoreApplication::loadPage($class, $method, $_REQUEST);
}
