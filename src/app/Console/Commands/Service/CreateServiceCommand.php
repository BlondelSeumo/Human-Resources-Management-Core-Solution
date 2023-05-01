<?php

namespace App\Console\Commands\Service;

use App\Exceptions\GeneralException;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class CreateServiceCommand extends GeneratorCommand
{

    protected $name = 'make:service';

    protected $description = 'This command will create service class';

    protected $type = "Service";

    protected function getStub()
    {
        if ($this->option('model')) {
            return app_path('Console/Stubs/service.stub');
        }

        return app_path('Console/Stubs/serviceWithoutModel.stub');
    }

    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('model')) {
            $replace = $this->createModel($replace);
        }

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    public function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Services';
    }

    public function createModel($replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ]);
    }

    public function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new GeneralException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate a resource controller for the given model.']
        ];
    }
}
