<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends AppController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.index', [
            'title' => 'Dashboard',
            'search' => '',
            'sub_header' => 'Dashboard',
            'sidebar_menu_active' => 'dashboard',
            'sidebar_submenu_active' => '',
            'sidebar_subsubmenu_active' => '',
        ]);
    }
}
