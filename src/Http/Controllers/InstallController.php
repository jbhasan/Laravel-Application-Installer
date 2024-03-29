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

		return view('application_installer::install');
	}

	public function checkServer()
	{
		//Check for .env file
		$this->isInstalled();
		$this->installSettings();

		$output = [];
		$composer = json_decode(file_get_contents(base_path('composer.json')));
		$output['required_php'] = $composer->require->php;
		$required_php = explode('|', $composer->require->php);
		$output['php'] = false;
		foreach($required_php as $required_php_version) {
			$version_exploded = substr($required_php_version, 0, 1);
			if(!is_int($version_exploded)) {
				$required_version = substr($required_php_version, 1);
				if(in_array($version_exploded, ['^', '>']) && PHP_VERSION > $required_version) {
					$output['php'] = true;
				}
			} else {
				if(PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION == $required_php_version) {
					$output['php'] = true;
				}
			}
		}

		// dd($output['php']);

		//Check for php version
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

		return view('application_installer::partials.check-requirements')
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
				$stmt2 = $dbh->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".$request->db_database."'");
				if ($stmt2->fetchColumn() > 0) {
					return response(['status' => 'error2', 'message' => 'Database exists and its not empty']);
				}
				//return response(['status' => 'error2', 'message' => 'Database already exists. Please try another.']);
			}
			return response(['status' => 'success', 'message' => 'Successfully Connected']);
		}
		catch(\PDOException $ex){
			return response(['status' => 'error', 'message' => $ex->getMessage()]);
		}
	}
	public function checkSmtpConnection(Request $request) {
		try{
			$transport = new \Swift_SmtpTransport($request->mail_host, $request->mail_port, $request->mail_encryption);
			$transport->setUsername($request->mail_username);
			$transport->setPassword($request->mail_password);
			$mailer = new \Swift_Mailer($transport);
			$mailer->getTransport()->start();
			return response(['status' => 'success', 'message' => 'Successfully Connected']);
		} catch (\Swift_TransportException $e) {
			return response(['status' => 'error', 'message' => $e->getMessage()]);
		} catch (\Exception $e) {
		  return response(['status' => 'error', 'message' => $e->getMessage()]);
		}
	}

	public function process(Request $request) {
		$this->installSettings();
		$checkConnection  = $this->checkConnection($request);
		if ($checkConnection->original['status'] == 'success') {
			try {
				$envExample['APP_NAME'] = Str::slug($request->application_name);
				$envExample['APP_URL'] = $request->application_url;
				$envExample['ASSET_URL'] = $request->asset_url;
				$envExample['DB_HOST'] = $request->db_host;
				$envExample['DB_PORT'] = $request->db_port;
				$envExample['DB_DATABASE'] = $request->db_database;
				$envExample['DB_USERNAME'] = $request->db_username;
				$envExample['DB_PASSWORD'] = $request->db_password;
				$envExample['MAIL_HOST'] = $request->mail_host;
				$envExample['MAIL_PORT'] = $request->mail_port;
				$envExample['MAIL_ENCRYPTION'] = $request->mail_encryption;
				$envExample['MAIL_USERNAME'] = $request->mail_username;
				$envExample['MAIL_PASSWORD'] = $request->mail_password;

				$isDatabaseCreated = $this->createDatabase($request);
				if (!$isDatabaseCreated) {
					throw new \Exception('Failed to create database');
				}
				$isFileCreated = $this->createEnvFile($envExample);
				if (!$isFileCreated) {
					throw new \Exception('Failed to create DOT ENV file');
				}
			} catch (\Exception $exception) {
				$this->deleteDatabase($request);
				$this->deleteEnv();
				return response(['status' => 'error', 'message' => $exception->getMessage()]);
			}
			return response(['status' => 'success', 'message' => 'Application Done. Migration is running...']);
		}
		return response($checkConnection->original);
	}

	protected function createEnvFile($dataArray) {
		try {
			copy(base_path('.env.example'), base_path('.env'));
			foreach ($dataArray as $key => $value) {
				$this->writeNewEnvironmentFileWith(base_path('.env'), $key, $value);
			}
			Artisan::call('key:generate');
			Artisan::call('config:clear');
			Artisan::call('cache:clear');
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

		$stmt = $dbh->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$request->db_database."'");
		if ($stmt->fetchColumn()) {
			$stmt2 = $dbh->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".$request->db_database."'");
			if ($stmt2->fetchColumn() > 0) {
				return false;
			}
			return true;
		} else {
			$stmt = $dbh->query("CREATE DATABASE " . $request->db_database . "");
			if (intval($stmt->errorCode()) == 0) {
				return true;
			}
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

	public function runMigration() {
		try {
			Artisan::call('config:clear');
			Artisan::call('cache:clear');
			Artisan::call('route:clear');
			Artisan::call('view:clear');

			Artisan::call('migrate');
		} catch (\Exception $exception) {
			return response(['status' => 'error', 'message' => $exception->getMessage()]);
		}
		return response(['status' => 'success', 'message' => '']);
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
