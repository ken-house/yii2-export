<?php
/**
 * @author   xudt<xudengtang@km.com>
 * @date-time: 2022/1/7 15:10
 */

namespace KenHouse\Yii2Export\classes;

use yii\db\QueryInterface;

class ExportCsv extends Export
{
    /**
     * @var array 导出参数
     */
    private $params;

    protected $query = null;

    protected $header = [];

    /**
     * @var int 总条数
     */
    private $count = 0;

    /**
     * @var int 一页查询条数
     */
    private $pageSize = 1000;

    /**
     * @var int 同步导出最大条数
     */
    private $syncLimit = 10000;

    /**
     * ExportCsv constructor.
     *
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;

        // 获取查询SQL或语句
        $this->query = $this->getExportQuery();
    }

    /**
     * 导出数据
     *
     * @return array
     *
     * @author     xudt
     * @date-time  2022/1/7 17:31
     */
    public function export()
    {
        // 获取查询总条数
        if ($this->query instanceof QueryInterface) {
            $query = clone $this->query;
            $this->count = $query->count();
            unset($query);
        }

        // 定义匿名方法获取数据
        $callback = $this->getCallback($this);

        try {
            $filePath = ''; // todo
            if ($this->count > 0) {
                $pageCount = ceil($this->count / $this->pageSize);
            } else {
                $pageCount = 999999999;
            }
            for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
                $rowsContent = "";
                $rowsData = $callback($pageNum, $this->pageSize);
                if (empty($rowsData)) {
                    break;
                }
                foreach ($rowsData as $rowData) {
                    $rowsContent .= $this->formatRow(implode(',', $rowData)) . PHP_EOL;
                }
                if ($rowsContent !== "") {
                    file_put_contents($filePath, $rowsContent, FILE_APPEND);
                }
            }
        } catch (\Exception $e) {
            throw new Exception('error:' . $e->getMessage());
        }

        return [
            'type' => Export::SYNC_EXPORT,
            'filePath' => $filePath
        ];
    }

    /**
     * 根据对象定义的查询语句定义匿名方法来获取数据
     *
     * @param object $object
     *
     * @return \Closure
     *
     * @author     xudt
     * @date-time  2022/1/7 16:12
     */
    private function getCallback(object $object)
    {
        $callback = function ($pageNum, $limit) use ($object) {
            $pageData = [];
            $query = clone $object->query;
            if (get_class($query) == 'yii\db\Query') {
                $dataList = $query->limit($limit)->offset(($pageNum - 1) * $limit)->all();
            } else {
                $dataList = $query->limit($limit)->offset(($pageNum - 1) * $limit)->asArray()->all();
            }
            $rows = $object->prepareRows($dataList, $object->params);
            if (!empty($rows)) {
                $pageData = array_merge($pageData, $rows);
            }
            return $pageData;
        };
        return $callback;
    }

    /**
     * 替换特殊字符
     *
     * @param $originRowData
     *
     * @return string|string[]
     *
     * @author     xudt
     * @date-time  2022/1/7 17:24
     */
    private function formatRow($originRowData)
    {
        return str_replace(["\n", "\x0D"], '', str_replace('"', '“', $originRowData));
    }
}