<div class="content">
    <? if($hasPost): ?>
        <div class="article">
            <h1><?=$post['title']?></h1>
            <div><?=$post['content']?></div>
            <hr>
            <a href="/posts/<?=$id?>/edit" style="margin-right: 50px">Edit</a>
            <a href="/posts/<?=$id?>/delete">Remove</a>
        </div>
    <? else: ?>
        <div class="e404">
            <h1>Страница не найдена!</h1>
        </div>
    <? endif; ?>
</div>
<hr>
<a href="index.php">Move to main page</a>