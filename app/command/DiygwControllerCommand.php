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


class DiygwControllerCommand extends DiygwMakeCommand
{
    protected $type = "Controller";

    protected function configure()
    {
        parent::configure();
        // 指令配置
        $this->setName('diygw:controller')
            ->setDescription('创建Controller类');
    }


    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'controller.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\controller';
    }

}
