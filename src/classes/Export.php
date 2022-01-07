<?php
/**
 * 数据导出
 *
 * @author   xudt<xudengtang@km.com>
 * @date-time: 2022/1/7 14:15
 */

namespace KenHouse\Yii2Export\classes;

class Export
{
    //同步导出
    const SYNC_EXPORT = 'sync';
    //异步导出
    const ASYNC_EXPORT = 'async';

    /**
     * @var $context
     */
    private $context;

    /**
     * @var string $classNameSuffix 导出类后缀，主要为了规范类的命名
     */
    private $classNameSuffix = "Export";

    /**
     * 实例化类对象
     *
     * @param string $namespace
     * @param string $contextName
     * @param array  $params
     *
     * @return $this
     *
     * @author     xudt
     * @date-time  2022/1/7 14:50
     */
    public function getContext(string $namespace, string $contextName, array $params)
    {
        try {
            // 获取类文件所在路径
            $class = $namespace . ucfirst($contextName . $this->classNameSuffix);
            if (class_exists($class)) {
                // 实例化类对象
                $this->context = new $class($params);
            } else {
                $this->context = '';
            }
        } catch (\Exception $e) {
            $this->context = '';
        }
        return $this;
    }
}