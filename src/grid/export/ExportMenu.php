<?php

namespace codexten\yii\dataView\grid\export;

use codexten\yii\dataView\widgets\GridViewTrait;
use Yii;

class ExportMenu extends \kartik\export\ExportMenu
{
    use GridViewTrait;

    public $clearBuffers = true;
    public $folder = '@runtime/export';

    public function columns()
    {
        return [];
    }

    public function run()
    {
        ini_set("pcre.backtrack_limit", "5000000");
        return parent::run();
    }


    public function inputParameters()
    {
        $items[] = [
            'Input Parameters',
        ];
        $items[] = [];
        $items[] = [
            0 => 'Input',
            1 => 'Value',
        ];

        $models = Yii::$app->request->get();

        foreach ($models as $model) {
            if (!is_array($model)) {
                continue;
            }
            foreach ($model as $attributeName => $value) {
                $items[] = [
                    0 => $attributeName,
                    1 => is_string($value) ? $value : implode(', ', $value),
                ];
            }

        }

        return $items;
    }

}
