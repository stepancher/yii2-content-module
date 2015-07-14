<?php
use yii\helpers\Url;
?>

<?php if($list): ?>
    <?php foreach($list as $type => $title) : ?>
        <li class=" <?php if(in_array(Yii::$app->requestedRoute, ['content/admin/index', 'content/admin/create', 'content/admin/update', 'content/admin/archives'])
            && Yii::$app->request->get('type') === $type) : ?>active<?php endif; ?>">
            <a href="<?= Url::toRoute(['/content/admin/index', 'type' => $type]) ?>">
                <i class="fa fa-newspaper-o"></i>
                <?= $title ?>
            </a>
        </li>
    <?php endforeach; ?>
<?php else: ?>
    <li class=" <?php if(in_array(Yii::$app->requestedRoute, ['content/admin/index', 'content/admin/create', 'content/admin/update', 'content/admin/archives'])) : ?>active<?php endif; ?>">
        <a href="<?= Url::toRoute(['/content/index']) ?>">
            <i class="fa fa-newspaper-o"></i> <span>Статьи</span>
        </a>
    </li>
<?php endif; ?>