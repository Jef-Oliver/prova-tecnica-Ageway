<?php
<<<<<<< HEAD
require_once 'init.php';
$theme  = $ini['general']['theme'];
$class  = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
$public = in_array($class, $ini['permission']['public_classes']);

// AdiantiCoreApplication::setRouter(array('AdiantiRouteTranslator', 'translate'));

new TSession;
ApplicationTranslator::setLanguage( TSession::getValue('user_language'), true );

if ( TSession::getValue('logged') )
{
    if (isset($_REQUEST['template']) AND $_REQUEST['template'] == 'iframe')
    {
        $content = file_get_contents("app/templates/{$theme}/iframe.html");
    }
    else
    {
        $content = file_get_contents("app/templates/{$theme}/layout.html");
        $menu    = AdiantiMenuBuilder::parse('menu.xml', $theme);
        $content = str_replace('{MENU}', $menu, $content);
    }
}
else
{
    if (isset($ini['general']['public_view']) && $ini['general']['public_view'] == '1')
    {
        $content = file_get_contents("app/templates/{$theme}/public.html");
        $menu    = AdiantiMenuBuilder::parse('menu-public.xml', $theme);
        $content = str_replace('{MENU}', $menu, $content);
    }
    else
    {
        $content = file_get_contents("app/templates/{$theme}/login.html");
    }
}

$content = ApplicationTranslator::translateTemplate($content);
$content = AdiantiTemplateParser::parse($content);

echo $content;

if (TSession::getValue('logged') OR $public)
{
    if ($class)
    {
        $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
        AdiantiCoreApplication::loadPage($class, $method, $_REQUEST);
    }
}
else
{
    if (isset($ini['general']['public_view']) && $ini['general']['public_view'] == '1')
    {
        if (!empty($ini['general']['public_entry']))
        {
            AdiantiCoreApplication::loadPage($ini['general']['public_entry'], '', $_REQUEST);
        }
    }
    else
    {
        AdiantiCoreApplication::loadPage('LoginForm', '', $_REQUEST);
    }
=======

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
>>>>>>> d7a83cc (CRUD completo finalizado, com regras de negócio e ajustes finais a serem implementados)
}
