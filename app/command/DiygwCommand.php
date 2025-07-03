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

use think\console\input\Argument;

class DiygwCommand extends DiygwMakeCommand
{
    protected $type = "Command";

    protected function configure()
    {
        parent::configure();
        $this->setName('diygw:command')
            ->addArgument('commandName', Argument::OPTIONAL, "The name of the command")
            ->setDescription('创建Command类');
    }

    protected function buildClass(string $name): string
    {
        $commandName = $this->input->getArgument('commandName') ?: strtolower(basename($name));
        $namespace   = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);
        $stub  = file_get_contents($this->getStub());


        return str_replace(['{%commandName%}', '{%className%}', '{%namespace%}', '{%app_namespace%}'], [
            $commandName,
            $class,
            $namespace,
            $this->app->getNamespace(),
        ], $stub);
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'command.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\command';
    }

}
