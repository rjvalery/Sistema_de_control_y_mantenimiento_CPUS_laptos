<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('login', 'Auth::index', ['filter' => 'guest']);
$routes->post('login', 'Auth::authenticate', ['filter' => ['guest', 'csrf']]);
$routes->get('logout', 'Auth::logout', ['filter' => 'auth']);

$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');
    $routes->get('dashboard', 'Dashboard::index');

    $routes->get('analistas', 'Analistas::index');
    $routes->post('analistas/agregar', 'Analistas::agregar');
    $routes->get('analistas/eliminar/(:num)', 'Analistas::eliminar/$1');

    $routes->post('usuarios/cambiar-rol', 'Usuarios::cambiarRol');
    $routes->post('usuarios/crear', 'Usuarios::crear');

    $routes->get('equipos/formulario', 'Equipos::formulario');
    $routes->post('equipos/guardar', 'Equipos::guardar');
    $routes->get('equipos/bitacora', 'Equipos::bitacora');
    $routes->get('equipos/exportar', 'Equipos::exportar');

    $routes->get('soplado/formulario', 'Soplado::formulario');
    $routes->post('soplado/guardar', 'Soplado::guardar');
    $routes->get('soplado/bitacora', 'Soplado::bitacora');
    $routes->get('soplado/exportar', 'Soplado::exportar');

    $routes->get('portatiles/formulario', 'Portatiles::formulario');
    $routes->post('portatiles/guardar', 'Portatiles::guardar');
    $routes->get('portatiles/bitacora', 'Portatiles::bitacora');
    $routes->get('portatiles/exportar', 'Portatiles::exportar');
});
