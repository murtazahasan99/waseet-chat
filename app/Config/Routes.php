<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('/', function ($routes) {
    $routes->get('', 'Home::index');
    $routes->get('login', 'Home::login');
    $routes->get('logout', 'Home::logout');
    $routes->post('login', 'Home::postLogin');
    
    $routes->get('general-chats', 'Home::getGeneralChats');
    $routes->get('personal-chats', 'Home::getPersonalChats');
    $routes->get('archived-chats', 'Home::getArchivedChats');
    $routes->get('chat-msgs', 'Home::getChatMsgs');
    $routes->get('start-chat', 'Home::startChat');
    $routes->post('send-msg', 'Home::sendMsg');
    $routes->get('close-chat', 'Home::closeChat');
    $routes->get('reopen-chat', 'Home::reopenChat');
    $routes->post('send-img', 'Home::sendImg');

});


$routes->group('merchant/api/v1', function ($routes) {
    $routes->get('init', 'merchant\Api::init');
    $routes->post('open-new-chat', 'merchant\Api::open_new_chat');
    $routes->post('add-reply', 'merchant\Api::add_reply');
    $routes->get('get-chat-msgs', 'merchant\Api::get_chat_msgs');
    $routes->post('rate-chat', 'merchant\Api::rate_chat');

});

$routes->add('/hello', 'Home::hello');
$routes->add('/world', 'Home::world');


