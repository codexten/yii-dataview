<?php


namespace codexten\yii\dataView\widgets;


trait GridViewTrait
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        if (empty($this->columns)) {
            $this->columns = $this->columns();
        }
        parent::init();
    }
}
