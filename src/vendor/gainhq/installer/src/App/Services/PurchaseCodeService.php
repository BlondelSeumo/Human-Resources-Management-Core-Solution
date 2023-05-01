<?php


namespace Gainhq\Installer\App\Services;

use Gainhq\Installer\App\Managers\EnvironmentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PurchaseCodeService extends BaseService
{
    protected $environment;

    public function __construct(EnvironmentManager $environment)
    {
        $this->environment = $environment;
    }

    public function savePurchaseCode(Request $request)
    {
        $envFileData =
            'APP_READ_ONLY='.'false'."\n".
            'APP_NAME=\''.$request->get('app_name')."'\n".
            'APP_ENV='.$request->get('environment', 'production')."\n".
            'APP_KEY='.'base64:'.base64_encode(Str::random(32))."\n".
            'APP_URL='.$request->get('app_url')."\n".
            'APP_LOCALE='.$request->get('app_locale', 'en')."\n".
            'APP_FALLBACK_LOCALE='.$request->get('app_locale', 'en')."\n".
            'APP_LOCALE_PHP='.$request->get('app_locale_php', 'en_US')."\n".
            'APP_TIMEZONE='.$request->get('app_timezone', 'UTC')."\n".
            'LOG_CHANNEL='.$request->get('log_channel', 'daily')."\n".
            'DEBUGGER_ENABLED='.'false'."\n".
            'SINGLE_LOGIN='.'false'."\n".
            'APP_DEBUG='.$request->get('app_debug', false)."\n".
            'APP_INSTALLED='.'false'."\n\n".

            'DB_CONNECTION='.$request->get('database_connection', '"mysql"')."\n".
            'DB_HOST='.$request->get('database_hostname', '"localhost"')."\n".
            'DB_PORT='.$request->get('database_port', '"3306"')."\n".
            'DB_DATABASE='.$request->get('database_name', '"homestead"')."\n".
            'DB_USERNAME='.$request->get('database_username', '"homestead"')."\n".
            'DB_PASSWORD='.$request->get('database_password', '"secret"')."\n\n".

            'BROADCAST_DRIVER='.$request->get('broadcast_driver', 'log')."\n".
            'CACHE_DRIVER='.$request->get('cache_driver', 'file')."\n".
            'QUEUE_CONNECTION='.$request->get('queue_connection', 'database')."\n".
            'SESSION_DRIVER='.$request->get('session_driver', 'cookie')."\n".
            'SESSION_LIFETIME='.$request->get('session_lifetime', '120')."\n".
            'SESSION_ENCRYPT='.$request->get('session_encrypt', 'false')."\n\n".

            'REDIS_HOST='.$request->get('redis_hostname', '127.0.0.1')."\n".
            'REDIS_PASSWORD='.$request->get('redis_password', 'null')."\n".
            'REDIS_PORT='.$request->get('redis_port', '6379')."\n\n".

            'MAIL_DRIVER='.'smtp'."\n".
            'MAIL_HOST='.'localhost'."\n".
            'MAIL_PORT='.'2525'."\n".
            'MAIL_USERNAME='.'null'."\n".
            'MAIL_PASSWORD='.'null'."\n".
            'MAIL_ENCRYPTION='.'null'."\n".
            'MAIL_FROM_ADDRESS='.'hello@example.com'."\n".
            'MAIL_FROM_NAME='.$request->get('app_name')."\n\n".

            'AWS_ACCESS_KEY_ID='.''."\n".
            'AWS_SECRET_ACCESS_KEY='.''."\n".
            'AWS_DEFAULT_REGION='.'us-east-1'."\n".
            'AWS_BUCKET='.''."\n\n".

            'PUSHER_APP_ID='.$request->get('pusher_app_id', '"your pusher app id"')."\n".
            'PUSHER_APP_KEY='.$request->get('pusher_app_key', '"your pusher app key"')."\n".
            'PUSHER_APP_SECRET='.$request->get('pusher_app_secret', '"your pusher app secret"')."\n".
            'PUSHER_APP_CLUSTER='.$request->get('pusher_app_cluster', 'ap2')."\n".
            'MIX_PUSHER_APP_KEY='.$request->get('mix_pusher_app_key', '"Your mix pusher app key"')."\n".
            'MIX_PUSHER_APP_CLUSTER='.$request->get('mix_pusher_app_cluster', '"your mix pusher app cluster"')."\n\n".

            'ENABLE_REGISTRATION='.'true'."\n".
            'CHANGE_EMAIL='.'false'."\n".
            'PASSWORD_HISTORY='.'3'."\n".
            'PASSWORD_EXPIRES_DAYS='.'30'."\n\n".

            'REQUIRES_APPROVAL='.'false'."\n".
            'CONFIRM_EMAIL='.'true'."\n\n".

            'INSTALL_DEMO_DATA='.'false'."\n".
            'DEPLOYMENT_KEY='.''."\n".
            'PREPARE_RELEASE_FILES='.'false'."\n".
            'RESTRUCTURE='.'false'."\n".
            'PURCHASE_CODE='.$request->get('code')."\n".
            'IS_DEV='.'false'."\n\n";

        if ($this->environment->copyEnv()) {
            return file_put_contents($this->environment->getEnvPath(), $envFileData);
        }
    }

}