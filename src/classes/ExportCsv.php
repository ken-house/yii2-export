<?php
/**
 * @author   ken
 * @date-time: 2022/1/7 15:10
 */

namespace KenHouse\Yii2Export\classes;

use yii\db\QueryInterface;

class ExportCsv extends Export
{
    /**
     * @var array 导出参数
     */
    protected $params;

    /**
     * @var |null 查询query
     */
    protected $query = null;

    /**
     * @var array 表头
     */
    protected $header = [];

    /**
     * @var string 文件存储路径
     */
    protected $filePath = "";

    /**
     * @var string 文件名前缀
     */
    protected $namePrefix = "";

    /**
     * @var int 一页查询条数
     */
    protected $pageSize = 1000;

    /**
     * @var int 同步导出最大条数
     */
    private $syncLimit = 10000;

    /**
     * @var int 总条数
     */
    private $count = 0;

    /**
     * ExportCsv constructor.
     *
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;

        // 文件存储路径
        $this->filePath = $this->getFilePath();

        // 获取表头
        $this->header = $this->getHeader();

        // 获取查询SQL或语句
        $this->query = $this->getExportQuery();
    }

    /**
     * 导出数据
     *
     * @return array
     *
     * @author     ken
     * @date-time  2022/1/7 17:31
     */
    public function export()
    {
        if (empty($this->filePath)) {
            throw new \Exception('filePath is Empty');
        }

        // 获取查询总条数
        if ($this->query instanceof QueryInterface) {
            $query = clone $this->query;
            $this->count = $query->count();
            unset($query);
        }

        // 定义匿名方法获取数据
        $callback = $this->getCallback($this);

        try {
            //写入表头 \xEF\xBB\xBF 该段字符是utf-8的bom头，不要乱修改
            file_put_contents($this->filePath, "\xEF\xBB\xBF" . implode(',', $this->header) . PHP_EOL, FILE_APPEND);

            // 写入数据记录
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
                    file_put_contents($this->filePath, $rowsContent, FILE_APPEND);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception('error:' . $e->getMessage());
        }

        return [
            'type' => Export::SYNC_EXPORT,
            'filePath' => $this->filePath
        ];
    }

    /**
     * 根据对象定义的查询语句定义匿名方法来获取数据
     *
     * @param object $object
     *
     * @return \Closure
     *
     * @author     ken
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
     * @author     ken
     * @date-time  2022/1/7 17:24
     */
    private function formatRow($originRowData)
    {
        return str_replace(["\n", "\x0D"], '', str_replace('"', '“', str_replace(',', '，', $originRowData)));
    }
}