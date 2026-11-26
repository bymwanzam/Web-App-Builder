<?php

namespace App\Http\Controllers;

use App\Services\InstallerService;

class InstallController extends Controller
{
    public function __construct(
        private InstallerService $installer
    ) {}

    /**
     * Show welcome screen
     */
    public function welcome()
    {
        return view('install.welcome');
    }

    /**
     * Show system requirements check
     */
    public function requirements()
    {
        return view('install.requirements', [
            'dependencies' => $this->installer->dependencies,
        ]);
    }

    /**
     * Show file permissions check
     */
    public function permissions()
    {
        return view('install.permissions', [
            'permissions' => $this->installer->permissions,
        ]);
    }

    /**
     * Show database configuration form
     */
    public function database()
    {
        return view('install.database', [
            'config' => $this->installer->getDbConfig(),
        ]);
    }

    /**
     * Show admin account creation form
     */
    public function admin()
    {
        return view('install.admin');
    }

    /**
     * Show installation completed screen
     */
    public function completed()
    {
        return view('install.completed');
    }
}
