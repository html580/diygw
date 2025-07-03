<?php
// +----------------------------------------------------------------------
// | Diygw PHP
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2022 https://www.diygw.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: diygw <diygwcom@diygw.com>
// +----------------------------------------------------------------------

declare (strict_types = 1);

namespace app\command;

use think\console\command\Make;
use think\console\Input;
use think\console\Output;
use think\helper\Str;


/**
 * Class DiygwMakeCommand
 * @package app\command
 * @author diygw
 */
abstract class DiygwMakeCommand extends Make
{
    protected $module;

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));

        $classname = $this->getClassName($name);


        $pathname = $this->getPathName($classname);

        if (is_file($pathname)) {
            $output->writeln('<error>' . $this->type . ':' . $classname.ucfirst($this->type) . ' already exists!</error>');
            return false;
        }

        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }

        file_put_contents($pathname, $this->buildClass($classname));

        $output->writeln('<info>' . $this->type . ':' . $classname.ucfirst($this->type) . ' created successfully.</info>');
    }

    protected function getPathName(string $name): string
    {
        $name = substr($name, 4);

        return $this->app->getBasePath() . ltrim(str_replace('\\', '/', $name), '/') .ucfirst($this->type). '.php';
    }

    protected function buildClass(string $name)
    {
        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);

        return str_replace(['{%className%}','{%classNameLower%}','{%year%}','{%module%}', '{%actionSuffix%}', '{%namespace%}', '{%app_namespace%}'], [
            $class,
            Str::snake($class,"_"),
            date('Y'),
            $this->module,
            $this->app->config->get('route.action_suffix'),
            $namespace,
            $this->app->getNamespace(),
        ], $stub);
    }


    protected function getClassName(string $name): string
    {
        if (strpos($name, '\\') !== false) {
            return $name;
        }

        if (strpos($name, '@')) {
            [$app, $name] = explode('@', $name);
            if($app!='common'){
                $this->module = $app."_";
            }
        } else {
            $app = '';
        }

        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->getNamespace($app) . '\\' . $name;
    }

}
