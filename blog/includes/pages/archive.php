<div class="archive-view">
    <button class="back-btn" onclick="location.href='index.php'">← Back to Home</button>
    
    <div class="archive-header">
        <h1 class="archive-title"><?= htmlspecialchars($periodName) ?></h1>
        <p class="archive-subtitle"><?= count($archivePosts) ?> classified <?= count($archivePosts) === 1 ? 'report' : 'reports' ?></p>
    </div>

    <div class="blog-grid">
        <?php foreach ($archivePosts as $post): ?>
            <div class="blog-card fade-in" onclick="location.href='index.php?page=post&id=<?= $post['id'] ?>'">
                <div class="card-thumbnail" style="background-image: url('<?= htmlspecialchars($post['thumbnail']) ?>')"></div>
                <div class="card-content">
                    <h3 class="card-title"><?= htmlspecialchars($post['title']) ?></h3>
                    <p class="card-excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>
                    <div class="card-meta">
                        <span><?= htmlspecialchars($post['date']) ?></span>
                        <span><?= htmlspecialchars($post['readTime']) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>