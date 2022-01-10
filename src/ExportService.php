<?php
/**
 * 数据导出服务类
 *
 * @author   ken
 * @date-time: 2022/1/7 14:19
 */

namespace KenHouse\Yii2Export;

use KenHouse\Yii2Export\classes\Export;

class ExportService
{
    /**
     * @var string $namespace 导出类所在的命名空间
     */
    private $namespace = 'common\services\export';

    /**
     * @var string $className 类名
     */
    private $className;

    /**
     * @var array $params 处理的参数
     */
    private $params;

    /**
     * ExportService constructor.
     *
     * @param $namespace
     * @param $className
     * @param $params
     */
    public function __construct($className, $params, $namespace = '')
    {
        if(!empty($namespace)){
            $this->namespace = $namespace;
        }
        $this->className = $className;
        $this->params = $params;
    }

    /**
     * 导出
     *
     * @return array
     *
     * @author     ken
     * @date-time  2022/1/7 17:51
     */
    public function export()
    {
        // 实例化对应的处理类
        $context = (new Export())->getContext($this->namespace, $this->className, $this->params);

        // 开始各自类的逻辑处理
        $result = [];
        if ($context != '') {
            $result = $context->export();
        }
        return $result;
    }
}