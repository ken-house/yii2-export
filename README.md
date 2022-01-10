# Yii2的数据导出扩展
该扩展为[Yii framework 2.0](http://www.yiiframework.com)添加了数据导出扩展，支持Mysql查询数据导出.

文档在 [README.md](README.md)。

安装
----

安装此扩展的首选方法是通过 [composer](http://getcomposer.org/download/).

```
composer require ken-house/yii2-export
```

开始
----
以下为配置过程。

配置
---
在common/bootstrap.php文件中，设置导出文件所在地址别名，这里配置在项目的根目录/data/,若需要更改可自定义，并确保目录是否存在，若不存在则创建。

```
Yii::setAlias('@export', dirname(dirname(__DIR__)) . '/data/export');
```

导出类文件
---

请将demo/TestExport.php文件放到项目common\services\export目录下；


调用
---

在controller下创建ExportController.php文件，复制下面调用的方法即可实现导出。

```
<?php
/**
 * @author   ken
 * @date-time: 2022/1/7 18:33
 */

namespace frontend\modules\api\controllers;

use KenHouse\Yii2Export\ExportService;
use yii\web\Controller;

class ExportController extends Controller
{
    public function actionIndex()
    {
        $params = [
            'start_date' => '2021-04-01',
            'end_date' => '2021-05-01'
        ];
        $testExport = new ExportService("Test", $params);
        $result = $testExport->export();
        echo "<pre>"; print_r($result);
        die;
    }
}
```




备注
----
如有疑问可留言。
