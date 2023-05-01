<?php


namespace App\Helpers\Core\General;

use App\Exceptions\GeneralException;
use App\Repositories\Core\Auth\UserRepository;

class AuthorizeHelper
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function authorize($actions)
    {
        if (is_array($actions)) {

            if (in_array(0, $this->arrayAuthorizer($actions))) {
                throw new GeneralException(trans('default.action_not_allowed'));
            }

            return true;
        }

        if (is_string($actions)) {
            if (auth()->user()->can($actions)) {
                return true;
            }
        }

        throw new GeneralException(trans('default.action_not_allowed'));
    }

    public function getAuthorized($action)
    {
        $this->authorize($action);
        $permissions = $this->user->findAuthUserPermission($action);
        if (!empty($permissions['pivot']['meta'])) {
            return $permissions['pivot']['meta'];
        }
        return [];
    }

    public function isAuthorizedSpecific($action, $payload)
    {
        $authorized = $this->getAuthorized($action);

        if (count($authorized) > 0) {
            if (!in_array($payload, $authorized)) {
                throw new GeneralException(trans('default.action_not_allowed'));
            }
        }

        return true;
    }

    public function authorizeMultiple(array $actions)
    {
        return in_array(0, $this->arrayAuthorizer($actions));
    }

    public function authorizeAny(array $actions)
    {
        return in_array(1, $this->arrayAuthorizer($actions));
    }

    public function arrayAuthorizer(array $actions)
    {
        return collect($actions)->map(function ($action) {
            return auth()->user()->can($action) ? 1 : 0;
        })->toArray();
    }
}
