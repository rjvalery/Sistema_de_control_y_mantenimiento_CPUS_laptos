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
    $routes->get('dashboard/exportar-sql', 'Dashboard::exportarSql');


    $routes->post('usuarios/cambiar-rol', 'Usuarios::cambiarRol');
    $routes->post('usuarios/crear', 'Usuarios::crear');

    $routes->get('cargue-masivo', 'CargueMasivo::index');
    $routes->post('cargue-masivo/procesar', 'CargueMasivo::procesar');
    $routes->get('cargue-masivo/plantilla', 'CargueMasivo::plantilla');
    $routes->post('cargue-masivo/vaciar', 'CargueMasivo::vaciar');

    $routes->get('inventario/buscar-equipo', 'Inventario::buscarEquipo');

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
