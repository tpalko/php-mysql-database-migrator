set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . 'model');

require_once 'env.php';

require_once dirname(__FILE__) . 'php-activerecord/ActiveRecord.php';

$cfg = ActiveRecord\Config::initialize(

	function($cfg) use ($connections){
	
		global $envkey;

		$modeldir = 'model';

		$cfg->set_model_directory($modeldir);
		$cfg->set_connections($connections);
		$cfg->set_default_connection($envkey);	
	}
);

require_once dirname(__FILE__) . 'service/Migration.php';

$m = new Migration(dirname(__FILE__) . "migrations");
$m->run();

