<!-- Featured Carousel -->
<?php include 'includes/carousel.php'; ?>

<div class="main-layout">
    <div class="main-content">
        <h1 class="section-title">Recent Intelligence Reports</h1>
        
        <!-- Recent Posts Grid -->
        <div class="blog-grid">
            <?php foreach ($recentPosts as $post): ?>
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

        <!-- Archives Section -->
        <?php if (!empty($archiveData)): ?>
        <div class="archives-section">
            <h2 class="section-title">Mission Archives</h2>
            <p class="archive-subtitle">Classified operations by deployment period</p>
            
            <div class="archives-grid">
                <?php foreach ($archiveData as $archive): ?>
                    <div class="archive-card" onclick="location.href='index.php?page=archive&period=<?= urlencode($archive['sortKey']) ?>'">
                        <div class="archive-month"><?= $archive['month'] ?></div>
                        <div class="archive-year"><?= $archive['year'] ?></div>
                        <div class="archive-count"><?= $archive['count'] ?> <?= $archive['count'] === 1 ? 'report' : 'reports' ?></div>
                        <div class="archive-badge"><?= $archive['count'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>
</div>