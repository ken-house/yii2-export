<?php
/**
 * @author   xudt<xudengtang@km.com>
 * @date-time: 2022/1/7 15:21
 */

namespace KenHouse\Yii2Export;

use KenHouse\Yii2Export\classes\ExportCsv;

class TestExport extends ExportCsv
{
    /**
     * TestExport constructor.
     *
     * @param $params
     */
    public function __construct($params)
    {
        parent::__construct($params);

        // 获取表头
        $this->header = $this->getHeader();
    }

    /**
     * 获取表头
     *
     * @return string[]
     *
     * @author     xudt
     * @date-time  2022/1/7 18:00
     */
    public function getHeader()
    {
        return [
            'id' => '用户ID',
            'nickname' => '昵称',
        ];
    }

    /**
     * 获取查询语句
     *
     *
     * @author     xudt
     * @date-time  2022/1/7 16:12
     */
    public function getExportQuery()
    {
        $query = ""; // todo
        return $query;
    }

    /**
     * 格式化整理数据
     *
     * @param $dataList
     * @param $params
     *
     * @return array
     *
     * @author     xudt
     * @date-time  2022/1/7 17:50
     */
    public function prepareRows($dataList, $params)
    {
        $fieldArr = array_keys($this->header);
        $rows = [];
        foreach ($dataList as $item) {
            $row = [];
            foreach ($fieldArr as $field) {
                //过滤','
                $val = $item[$field] ?? '';
                if ($val) {
                    $val = str_replace(',', '', $val);
                }
                $row[] = $val;
            }
            $rows[] = $row;
        }

        return $rows;
    }
}