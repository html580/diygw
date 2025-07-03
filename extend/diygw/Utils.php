<?php
declare(strict_types=1);

namespace diygw;

use think\facade\Cache;
use think\facade\Db;
use think\helper\Str;

class Utils
{
  /**
   * 字符串转换成数组
   *
   * @time 2019年12月25日
   * @param string $string
   * @param string $dep
   * @return array
   */
    public static function stringToArrayBy($string, $dep = ','): array
    {
        if (Str::contains(strval($string), $dep)) {
            return explode($dep, trim(strval($string), $dep));
        }

        return [$string];
    }

  /**
   * 搜索参数
   *
   * @time 2020年01月13日
   * @param array $params
   * @param array $range
   * @return array
   */
    public static function filterSearchParams(array $params, array $range = []): array
    {
        $search = [];

        if (!empty($range)) {
          foreach ($range as $field => $rangeField) {
            if (count($rangeField) === 1) {
              $search[$field] = [$params[$rangeField[0]]];
              unset($params[$rangeField[0]]);
            } else {
              $search[$field] = [$params[$rangeField[0]], $params[$rangeField[1]]];
              unset($params[$rangeField[0]], $params[$rangeField[1]]);
            }
          }
        }

        return array_merge($search, $params);
    }

    /**
     * 导入树形数据
     *
     * @time 2020年04月29日
     * @param $data
     * @param $table
     * @param string $pid
     * @param string $primaryKey
     * @return void
     */
    public static function importTreeData($data, $table, string $pid = 'parent_id', string $primaryKey = 'id')
    {
        foreach ($data as $value) {
            if (isset($value[$primaryKey])) {
                unset($value[$primaryKey]);
            }

            $children = $value['children'] ?? false;
            if($children) {
                unset($value['children']);
            }

            // 首先查询是否存在
            $menu = Db::name($table)
                        ->where('permission_name', $value['permission_name'])
                        ->where('module', $value['module'])
                        ->where('permission_mark', $value['permission_mark'])
                        ->find();

            if (!empty($menu)) {
                $id = $menu['id'];
            } else {
                $id = Db::name($table)->insertGetId($value);
            }
            if ($children) {
                foreach ($children as &$v) {
                    $v[$pid] = $id;
                    $v['level'] = !$value[$pid] ? $id : $value['level'] . '-' .$id;
                }
                self::importTreeData($children, $table, $pid);
            }
        }
    }

    /**
     *  解析 Rule 规则
     *
     * @time 2020年05月06日
     * @param $rule
     * @return array
     */
    public static function parseRule($rule): array
    {
        [$controller, $action] = explode(Str::contains($rule, '@') ? '@' : '/', $rule);

        $controller = explode('\\', $controller);

        $controllerName = lcfirst(array_pop($controller));

        array_pop($controller);

        $module = array_pop($controller);

        return [$module, $controllerName, $action];
    }


    /**
     * 表前缀
     *
     * @time 2020年05月22日
     * @return mixed
     */
    public static function tablePrefix()
    {
        return \config('database.connections.mysql.prefix');
    }

    /**
     * 删除表前缀
     *
     * @time 2020年12月01日
     * @param string $table
     * @return string|string[]
     */
    public static function tableWithoutPrefix(string $table)
    {
        return str_replace(self::tablePrefix(), '', $table);
    }

    /**
     * 添加表前缀
     *
     * @time 2020年12月26日
     * @param string $table
     * @return string
     */
    public static function tableWithPrefix(string $table): string
    {
        return Str::contains($table, self::tablePrefix()) ?
                    $table : self::tablePrefix() . $table;
    }



    /**
     * public path
     *
     * @param string $path
     * @time 2020年09月08日
     * @return string
     */
    public static function publicPath(string $path = ''): string
    {
        return root_path($path ? 'public/'. $path : 'public');
    }


    /**
     * 过滤空字符字段
     *
     * @time 2021年01月16日
     * @param $data
     * @return mixed
     */
    public static function filterEmptyValue($data)
    {
        foreach ($data as $k => $v) {
            if (!$v) {
                unset($data[$k]);
            }
        }

        return $data;
    }


    /**
     * 缓存操作
     *
     * @time 2021年06月18日
     * @param string $key
     * @param \Closure $callable
     * @param int $ttl
     * @param string $store
     * @return mixed
     *@throws \Psr\SimpleCache\InvalidArgumentException
     */
    public static function cache(string $key, \Closure $callable, int $ttl = 0, string $store = 'redis')
    {
        if (Cache::store($store)->has($key)) {
            return Cache::store($store)->get($store);
        }

        $cache = $callable();

        Cache::store($store)->set($key, $cache, $ttl);

        return $cache;
    }
}
