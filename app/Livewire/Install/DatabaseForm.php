<?php

namespace App\Livewire\Install;

use App\Services\InstallerService;
use Livewire\Component;

class DatabaseForm extends Component
{
    public $dbType = 'mysql';

    public $host = '';

    public $port = '3306';

    public $database = '';

    public $username = '';

    public $password = '';

    public function mount(InstallerService $installer)
    {
        $config = $installer->getDbConfig();
        $this->dbType = $config['db_type'];
        $this->host = $config['host'];
        $this->port = $config['port'];
        $this->database = $config['database'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function save(InstallerService $installer)
    {
        // Base validation rules
        $rules = [
            'dbType' => ['required', 'in:mysql,sqlite'],
        ];

        // Add MySQL-specific validation rules
        if ($this->dbType === 'mysql') {
            $rules = array_merge($rules, [
                'host' => ['required', 'string', 'max:255'],
                'port' => ['required', 'integer', 'min:1', 'max:65535'],
                'database' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255'],
                'password' => ['nullable', 'string'],
            ]);
        }

        $validated = $this->validate($rules);

        // Add db_type to validated data
        $validated['db_type'] = $this->dbType;

        if (! $installer->testDatabaseConnection($validated)) {
            if ($this->dbType === 'sqlite') {
                $this->addError('database', 'Failed to create SQLite database. Please ensure the database directory is writable.');
            } else {
                $this->addError('database', 'Failed to connect to database. Please check your credentials.');
            }

            return;
        }

        try {
            $installer->createConfig($validated);

            // Use regular redirect (not wire:navigate) to ensure fresh page load with new config
            return redirect()->route('install.admin');
        } catch (\Exception $e) {
            $this->addError('database', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.install.database-form');
    }
}
