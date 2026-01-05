<?php
use yii\helpers\Html;

$this->title = 'Сказка';
?>
<h1>Сказка</h1>
<div style="white-space: pre-wrap; font-family: monospace;">
    <?= Html::encode($story) ?>
</div>
<p>
    <?= Html::a('Назад', ['index'], ['class'=>'btn btn-secondary']) ?>
</p>
