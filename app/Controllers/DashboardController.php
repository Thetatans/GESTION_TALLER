<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index(): string
    {
        return view('dashboard/index', [
            'title'     => 'Dashboard - Control de Produccion',
            'pageTitle' => 'Dashboard',
        ]);
    }
}
