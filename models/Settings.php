<?php namespace Shohabbos\Payme\Models;

use Model;
use Illuminate\Filesystem\Filesystem;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'shohabbos_payme_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';

    protected $files;

    public function __construct()
    {
        parent::__construct();
        $this->files = new Filesystem;
    }

	public function afterSave()
	{
		$path = self::get('handler');
		$template = "<?php Route::any('{$path}', 'Shohabbos\Payme\Controllers\Payme@index');";

	    $this->files->put(__DIR__ . '/'.'../routes.php', $template);


        // write code
        $code = self::get('code', null);
        if ($code) {
            $this->files->put(__DIR__ . '/'.'../init.php', $code);
        }
	}


}