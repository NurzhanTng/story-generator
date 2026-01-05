<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\story\models\StoryForm;

$characterOptions = StoryForm::getCharacterOptions();
?>

<h1>Генератор сказок</h1>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
<?php endif; ?>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'age')->textInput(['type'=>'number']) ?>
<?= $form->field($model, 'language')->dropDownList(['ru'=>'Русский','kk'=>'Казахский']) ?>
<?= $form->field($model, 'characters')->checkboxList($characterOptions) ?>
<div class="form-group">
    <?= Html::submitButton('Сгенерировать сказку', ['class'=>'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>

<?php if (!empty($story)): ?>
    <h2>Сказка</h2>
    <div style="white-space: pre-wrap; font-family: monospace;">
        <?= Html::encode($story) ?>
    </div>
<?php endif; ?>
