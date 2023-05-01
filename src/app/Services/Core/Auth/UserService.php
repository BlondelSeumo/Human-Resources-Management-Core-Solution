<?php


namespace App\Services\Core\Auth;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\FileHandler;
use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Core\Traits\Helpers;
use App\Hooks\User\AfterLogin;
use App\Hooks\User\BeforeLogin;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Auth\Traits\HasUserActions;
use App\Services\Core\BaseService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserService extends BaseService
{
    use FileHandler, Helpers, HasWhen, HasUserActions;

    protected $role;

    public function __construct(User $user, Role $role)
    {
        $this->model = $user;
        $this->role = $role;
    }

    public function create()
    {
        $status = Status::findByNameAndType('status_active', 'user')->id;

        parent::save($this->getFillAble(array_merge(request()->only(
            'first_name',
            'last_name',
            'email',
            'password'
        ), ['status_id' => $status])));

        return $this;
    }


    public function update()
    {
        $this->model->fill($this->getFillAble(request()->only('first_name', 'last_name', 'status_id')));

        throw_if(
            $this->model->isDirty('status_id') && ($this->model->isAppAdmin() || $this->model->id == auth()->id()),
            new GeneralException(trans('default.action_not_allowed'))
        );

        $this->when($this->model->isDirty(), function (UserService $service) {
            $service->notify('user_updated');
        });

        $this->model->save();

        $this->when(request()->get('roles'), function (UserService $service) {
            $service->assignRole(request()->get('roles'));
        });

        return $this->model;
    }

    public function assignRole($roles)
    {
        $this->model->assignRole($roles);

        return $this;
    }

    public function delete(User $user)
    {
        if ($user->isAppAdmin() && !$user->isInvited())
            throw new GeneralException(trans('default.action_not_allowed'));

        if ($user->id == auth()->id())
            throw new GeneralException(trans('default.cant_delete_own_account'));

        return $user->delete();
    }


    public function attachRole()
    {
        if ((($this->model->isAppAdmin() && !auth()->user()->isAppAdmin()) || $this->model->id == auth()->id()) && !$this->model->isInvited())
            throw new GeneralException(trans('default.action_not_allowed'));

        $roles = $this->checkMakeArray(request('roles'));
        $this->model->roles()->sync(array_unique($roles));
        return $this->model;
    }

    public function detachRole()
    {
        if (($this->model->isAppAdmin() && !auth()->user()->isAppAdmin()) || $this->model->id == auth()->id())
            throw new GeneralException(trans('default.action_not_allowed'));

        $roles = $this->checkMakeArray(request('roles'));
        $this->model->roles()->detach($roles);
        $this->model->load('roles');
        return $this->model;
    }

    public function changeSetting($user = null)
    {
        $this->setModel($user ?? auth()->user());

        $this->attachSettings(
            request()->only('gender', 'date_of_birth', 'address', 'contact')
        );

        return true;
    }

    public function attachSettings($settings)
    {
        $settings_models = [];
        foreach ($settings as $key => $setting) {
            $setting_model = $this->model
                ->settings()
                ->firstOrNew([
                    'name' => $key,
                    'context' => 'user'
                ]);

            $setting_model->fill([
                'value' => $key == 'date_of_birth' ? Carbon::parse($setting)->format('Y-m-d') : $setting,
                'public' => 1
            ]);

            array_push($settings_models, $setting_model);
        }

        return $this->model
            ->settings()
            ->saveMany($settings_models);
    }

    public function storeThumbnail($user = null)
    {
        $user = $user ?? auth()->user();

        $this->deleteImage(optional($user->profilePicture)->path);

        $file_path = $this->uploadImage(
            request()->file('profile_picture'),
            config('file.profile_picture.folder'),
            config('file.profile_picture.height')
        );

        $user->profilePicture()->updateOrCreate([
            'type' => 'profile_picture'
        ], [
            'path' => $file_path
        ]);

        return $user->load('profilePicture');

    }


    public function login()
    {
        /**@var $user User*/
        $user = $this->model::findByEmail( request()->get('email') );

        BeforeLogin::new(true)
            ->setModel($user)
            ->handle();

        if (!$user->roles->count())
            throw new AuthenticationException(trans('default.no_roles_found'));

        if (Hash::check(request()->get('password'), optional($user)->password)) {
            auth()->login(
                $user,
                request()->get('remember_me',false)
            );

            AfterLogin::new(true)
                ->setModel($user)
                ->handle();

            return $user;
        }

        throw new AuthenticationException(
            trans('default.incorrect_user_password', [
                'password' => trans('default.password'),
                'email' => trans('default.email')
            ])
        );
    }


    public function getFormattedSettings()
    {
        return resolve(SettingRepository::class)
            ->getFormattedSettings('user', User::class, auth()->id());
    }

    public function findAndCacheUser($id)
    {
        return cache()->remember('user-'.$id, 86400, function () use ($id) {
            return $this->select('id', 'first_name', 'last_name')->with('profilePicture')
                ->find($id);
        });
    }

    public function validateIsNotDemoVersion(): self
    {
        throw_if(
            env('IS_DEMO', false),
            new GeneralException(__t('action_not_allowed_in_demo'))
        );

        return $this;
    }

    public function validate()
    {
        validator(request()->all(),[
            'first_name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore(optional($this->model)->id)
            ],
            'employee_id' => 'required|min:2|unique:profiles,employee_id,'.optional($this->model)->id.',user_id',
            'gender' => 'required'
        ])->validate();

        return $this;
     }

}
