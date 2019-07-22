<?php

namespace codexten\yii\dataView\widgets;

use Closure;
use hiqdev\higrid\DataColumn;
use hiqdev\higrid\DetailView;
use hiqdev\yii2\assets\JqueryResizableColumns\ResizableColumnsAsset;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

class GridView extends \kartik\grid\GridView
{
    use GridViewTrait;

    /*
    * {@inheritdoc}
    */
    public $dataColumnClass = DataColumn::class;

    /**
     * {@inheritdoc}
     */
    public $detailViewClass = DetailView::class;

    /**
     * @var array|boolean
     *  - array: options for Jquery Resizable Columns plugin initiate call
     *  - boolean false: resizable is disabled
     *
     * Defaults to `['store' => new JsExpression('store')]`
     * @see registerResizableColumns()
     */
    public $resizableColumns = [];

    public function run()
    {
        $this->registerResizableColumns();
        parent::run();
    }

    /**
     * {@inheritdoc}
     */
    public function getId($autoGenerate = true)
    {
        if ($autoGenerate && parent::getId(false) === null) {
            $this->id = hash('crc32b', Json::encode($this->columns));
        }

        return parent::getId();
    }

    /**
     * Registers ResizableColumns plugin when [[resizableColumns]] is not false.
     * TODO: move somewhere.
     */
    public function registerResizableColumns()
    {
        if (!$this->resizableColumns !== false) {
            return;
        }

        $this->tableOptions['data-resizable-columns-id'] = $this->id;

        ResizableColumnsAsset::register($this->getView());
        $resizableColumns = Json::encode(ArrayHelper::merge([
            'store' => new JsExpression('store'),
        ], $this->resizableColumns));
        $this->getView()->registerJs("$('#{$this->id} table[data-resizable-columns-id]').resizableColumns($resizableColumns);");
    }

    /**
     * Runs DetailView widget based on this GridView.
     *
     * @param array $config Config that will be passed to [[detailViewClass]] initialisation.
     * Special element `gridOptions` will be merged to `GridView` initialisation config array.
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     *
     */
    public static function detailView(array $config = [])
    {
        /** @var \hiqdev\higrid\GridView $grid */
        $grid = Yii::createObject(ArrayHelper::merge([
            'class' => get_called_class(),
            'dataProvider' => new ArrayDataProvider(['allModels' => [$config['model']]]),
        ], ArrayHelper::remove($config, 'gridOptions', [])));
        $class = $grid->detailViewClass ?: DetailView::class;
        $config['grid'] = $grid;

        return call_user_func([$class, 'widget'], $config);
    }

    /**
     * Returns array of columns configurations that will be used by widget to create
     * data columns and render them.
     *
     * Array format:
     *  key - column alias
     *  value - column configuration array
     *
     * Example:
     *
     * ```php
     * return [
     *     'login_and_avatar' => [
     *         'format' => 'raw',
     *         'value' => function ($model) {
     *             return Html::img($model->avatar) . $model->username;
     *         }
     *     ]
     * ];
     * ```
     *
     * Despite model does not have a `login_and_avatar` attribute, the following widget call will
     * use the definition above to render value:
     *
     * ```php
     * echo GridView::widget([
     *     'dataProvider' => $dataProvider,
     *     'columns' => ['login_and_avatar', 'status', 'actions'],
     * ]);
     * ```
     *
     * @return array
     */
    public function columns()
    {
        return [];
    }

    /**
     * Creates a [[DataColumn]] object with given config.
     *
     * @param array $config config for [[DataColumn]]
     *
     * @return DataColumn the column instance
     */
    protected function createDataColumnByConfig(array $config = [])
    {
        return Yii::createObject(array_merge([
            'class' => $this->dataColumnClass ?: \yii\grid\DataColumn::class,
            'grid' => $this,
        ], $config));
    }

    /**
     * {@inheritdoc}
     */
    protected function createDataColumn($text)
    {
        $columns = $this->columns();

        if (!isset($columns[$text]) || !is_array($columns[$text])) {
            return parent::createDataColumn($text);
        }

        $config = array_merge(['attribute' => $text], $columns[$text]);

        return $this->createDataColumnByConfig($config);
    }

    /**
     * @var Closure use it to change default summary rendering
     * Method signature:
     *
     * ```php
     * function ($grid, $defaultSummaryCallback)
     * ```
     *
     * Argument `$defaultSummaryCallback` will contain a Closure that will
     * render default summary.
     * ```
     */
    public $summaryRenderer;

    /**
     * {@inheritdoc}
     */
    public function renderSummary()
    {
        if ($this->summaryRenderer instanceof Closure) {
            return call_user_func($this->summaryRenderer, $this, function () {
                return parent::renderSummary();
            });
        }

        return parent::renderSummary();
    }
}
