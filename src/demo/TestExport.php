<?php
/**
 * 导出测试服务类
 *
 * @author   ken
 * @date-time: 2022/1/7 15:21
 */

namespace common\services\export;

use common\models\User;
use KenHouse\Yii2Export\classes\ExportCsv;
use KenHouse\Yii2Export\classes\ExportInterface;
use Yii;

class TestExport extends ExportCsv implements ExportInterface
{
    /**
     * @var int 每页查询条数
     */
    protected $pageSize = 2000;

    /**
     * @var string 文件名前缀
     */
    protected $namePrefix = "测试导出";

    /**
     * 文件存储路径
     *
     * @return string
     *
     * @author     ken
     * @date-time  2022/1/7 19:02
     */
    public function getFilePath()
    {
        $path = Yii::getAlias("@export") . DIRECTORY_SEPARATOR;
        if ($this->namePrefix) {
            $fileName = $this->namePrefix . date("YmdHis") . substr(md5(time()), 0, 5) . ".csv";
        } else {
            $fileName = date("YmdHis") . '=' . substr(md5(time()), 0, 5) . ".csv";
        }
        if (!file_exists($path)) {
            //创建存储目录
            @mkdir($path);
        }
        return $path . $fileName;
    }

    /**
     * 获取表头
     *
     * @return string[]
     *
     * @author     ken
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
     * @author     ken
     * @date-time  2022/1/7 16:12
     */
    public function getExportQuery()
    {
        $startTime = strtotime($this->params['start_date']);
        $endTime = strtotime($this->params['end_date']);

        $query = User::find()->select(['id', 'nickname'])->where(['>=', 'created_at', $startTime])->andWhere(['<=', 'created_at', $endTime]);
        return $query;
    }

    /**
     * 格式化及整理数据
     *
     * @param $dataList
     * @param $params
     *
     * @return array
     *
     * @author     ken
     * @date-time  2022/1/7 17:50
     */
    public function prepareRows($dataList, $params)
    {
        $fieldArr = array_keys($this->header);
        $rows = [];
        foreach ($dataList as $item) {
            $row = [];
            foreach ($fieldArr as $field) {
                $val = $item[$field] ?? '';
                $row[] = $val;
            }
            $rows[] = $row;
        }
        return $rows;
    }
}