<?php
/**
 * 导出接口类
 * @author   ken
 * @date-time: 2022/1/10 11:16
 */
namespace KenHouse\Yii2Export\classes;

interface ExportInterface
{
    /**
     * 导出文件存储位置
     *
     * @return mixed
     *
     * @author     ken
     * @date-time  2022/1/10 11:20
     */
    public function getFilePath();

    /**
     * 表头
     *
     * @return mixed
     *
     * @author     ken
     * @date-time  2022/1/10 11:20
     */
    public function getHeader();

    /**
     * 查询数据的query对象
     *
     * @return mixed
     *
     * @author     ken
     * @date-time  2022/1/10 11:20
     */
    public function getExportQuery();

    /**
     * 对查询数据进行格式化处理
     *
     * @param $dataList
     * @param $params
     *
     * @return mixed
     *
     * @author     ken
     * @date-time  2022/1/10 11:21
     */
    public function prepareRows($dataList, $params);
}