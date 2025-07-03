<?php
declare(strict_types=1);

namespace diygw\model;

use diygw\db\DiygwQuery;
use diygw\db\TestQuery;
use diygw\traits\BaseDbTrait;
use think\model\concern\SoftDelete;


/**
 * Class DiygwModel
 */
abstract class DiygwModel extends \think\Model
{
    use SoftDelete,BaseDbTrait;
    protected $deleteTime = 'delete_time';
    protected $pk = '';

    // 数据转换为驼峰命名
    protected $convertNameToCamel = true;

    protected $autoWriteTimestamp = true;

    // 分页 Limit
    public const LIMIT = 10;
    // 开启
    public const ENABLE = 1;
    // 禁用
    public const DISABLE = 2;

    public function __construct(array $data = [])
    {
        parent::__construct($data);

        if(empty($this->pk)){
            $this->pk = $this->diygwPk();
        }
    }
}
