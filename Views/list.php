<?php
use Database\MySQLWrapper;

$db = new MySQLWrapper();
$stmt = $db->prepare("SELECT * FROM images");
        $stmt->execute();
        $result = $stmt->get_result();
        $images = $result->fetch_all(MYSQLI_ASSOC);


?>



<div class="container">
    <div class="row">
        <div class="col">
            <?php if (empty($images)): ?>
                <div class="alert alert-info">スニペットは登録されていません。</div>
                
            <?php else: ?>
          <img src="/images/Screenshottest.png" alt="">
          <img src="https://www.hitachi-solutions-create.co.jp/column/img/image-generation-ai.jpg" alt="">
          <img src="https://www.hitachi-solutions-create.co.jp/column/img/image-generation-ai.jpg" alt="">
                
                <ul class="list-group">
                    <?php foreach ($images as $image): ?>
                        <li class="list-group-item">
                            <a href="/show?path=<?= htmlspecialchars($image['id']) ?>" class="text-decoration-none">
                                <h5><?= htmlspecialchars($image['title']) ?></h5>
                                <small>ファイル名: <?= htmlspecialchars($image['file_name']) ?></small><br>
                                <small>Expire: <?= $image['title'] ? htmlspecialchars($image['title']) : "Never" ?></small>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
