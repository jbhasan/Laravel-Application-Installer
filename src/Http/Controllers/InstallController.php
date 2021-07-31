<?php

namespace Sayeed\ApplicationInstaller\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class InstallController extends Controller
{
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->installSettings();
    }

    /**
     * Initialize all install functions
     *
     */
    private function installSettings()
    {
        config(['app.debug' => true]);
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
		Artisan::call('config:clear');
		Artisan::call('cache:clear');
		Artisan::call('route:clear');
		Artisan::call('view:clear');
		Artisan::call('optimize');
    }

    /**
     * Check if project is already installed then show 404 error
     *
     */
    private function isInstalled()
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            return true;
        }
        return false;
    }

    /**
     * Installation
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        //Check for .env file
        if ($this->isInstalled()) {
        	return redirect('/home');
		}
        $this->installSettings();

		return view('installer.install');
    }

    public function checkServer()
    {
        //Check for .env file
        $this->isInstalled();
        $this->installSettings();

        $output = [];
        
        //Check for php version
        $output['php'] = PHP_MAJOR_VERSION >= 7 && PHP_MINOR_VERSION >=1;
        $output['php_version'] = PHP_VERSION;

        //Check for php extensions
        $output['openssl'] = extension_loaded('openssl');
        $output['pdo'] = extension_loaded('pdo');
        $output['mbstring'] = extension_loaded('mbstring');
        $output['tokenizer'] = extension_loaded('tokenizer');
        $output['xml'] = extension_loaded('xml');
        $output['curl'] = extension_loaded('curl');
        $output['zip'] = extension_loaded('zip');
        $output['gd'] = extension_loaded('gd');

        //Check for writable permission. storage and the bootstrap/cache directories should be writable by your web server
        $output['storage_writable'] = is_writable(storage_path());
        $output['cache_writable'] = is_writable(base_path('bootstrap/cache'));
        
        $output['next'] = $output['php'] && $output['openssl'] && $output['pdo'] && $output['mbstring'] && $output['tokenizer'] && $output['xml'] && $output['curl'] && $output['zip'] && $output['gd'] && $output['storage_writable'] && $output['cache_writable'];

        return view('installer.partials.check-requirements')
            ->with(compact('output'));
    }

    public function checkConnection(Request $request) {
		try{
			$dbh = new \PDO('mysql:host='.$request->db_host.':'.$request->db_port.';',
				$request->db_username,
				$request->db_password,
				array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

			$stmt = $dbh->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$request->db_database."'");
			if ($stmt->fetchColumn()) {
				return response(['status' => 'error2', 'message' => 'Database already exists. Please try another.']);
			}
			return response(['status' => 'success', 'message' => 'Successfully Connected']);
		}
		catch(\PDOException $ex){
			return response(['status' => 'error', 'message' => 'Unable to connect']);
		}
	}

    public function process(Request $request) {
    	$this->installSettings();
    	$checkConnection  = $this->checkConnection($request);
		if ($checkConnection->original['status'] == 'success') {
			try {
				$envExample['APP_NAME'] = Str::slug($request->application_name);
				$envExample['APP_KEY'] = config('app.key');
				$envExample['DB_HOST'] = $request->db_host;
				$envExample['DB_PORT'] = $request->db_port;
				$envExample['DB_DATABASE'] = $request->db_database;
				$envExample['DB_USERNAME'] = $request->db_username;
				$envExample['DB_PASSWORD'] = $request->db_password;


				$isDatabaseCreated = $this->createDatabase($request);
				if (!$isDatabaseCreated) {
					throw new \Exception('Failed to create database');
				}
				$isFileCreated = $this->createEnvFile($envExample);
				if (!$isFileCreated) {
					throw new \Exception('Failed to create DOT ENV file');
				}
				$this->runMigration();
				$isMigrated = $this->runMigration();
				if (!$isMigrated) {
					throw new \Exception('Failed to migrate');
				}
			} catch (\Exception $exception) {
				$this->deleteDatabase($request);
				$this->deleteEnv();
				return response(['status' => 'error', 'message' => $exception->getMessage()]);
			}
		}
		return response($checkConnection->original);
	}

	protected function createEnvFile($dataArray) {
    	try {
    		copy(base_path('.env.example'), base_path('.env'));
			foreach ($dataArray as $key => $value) {
				$this->writeNewEnvironmentFileWith(base_path('.env'), $key, $value);
			}
		} catch (\Exception $exception) {
    		return false;
		}
		return true;
	}

	/**
	 * This function deletes .env file.
	 *
	 */
	private function deleteEnv()
	{
		$envPath = base_path('.env');
		if ($envPath && file_exists($envPath)) {
			unlink($envPath);
		}
		return true;
	}

	protected function createDatabase($request) {
		$dbh = new \PDO('mysql:host='.$request->db_host.':'.$request->db_port.';',
			$request->db_username,
			$request->db_password,
			array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

		$stmt = $dbh->query("CREATE DATABASE ".$request->db_database."");
		if (intval($stmt->errorCode()) == 0) {
			return true;
		}
		return false;
	}

	protected function deleteDatabase($request) {
		$dbh = new \PDO('mysql:host='.$request->db_host.':'.$request->db_port.';',
			$request->db_username,
			$request->db_password,
			array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

		$stmt = $dbh->query("DROP DATABASE ".$request->db_database."");
		if (intval($stmt->errorCode()) == 0) {
			return true;
		}
		return false;
	}

	protected function runMigration() {
    	try {
			Artisan::call('config:clear');
			Artisan::call('cache:clear');
			Artisan::call('route:clear');
			Artisan::call('view:clear');
			Artisan::call('optimize');

			Artisan::call('migrate');
		} catch (\Exception $exception) {
    		return false;
		}
		return true;
	}

	/**
	 * @param string $path
	 * @param string $key
	 * @param string $val
	 */
	protected function writeNewEnvironmentFileWith(string $path, string $key, string $val)
	{
		$data = file($path); // reads an array of lines
		$data = array_map(function ($datum) use ($key,$val) {
			if (Str::startsWith($datum, $key)) {
				return "$key=$val\n";
			}
			return $datum;
		}, $data);
		file_put_contents($path, implode('', $data));
	}
}
