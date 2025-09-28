<div class="carousel-section">
    <h2 class="section-title">Priority Intelligence</h2>
    <div class="carousel-container">
        <div class="carousel-wrapper" id="carousel-wrapper">
            <?php foreach ($featuredPosts as $index => $post): ?>
                <div class="carousel-slide" style="background-image: url('<?= htmlspecialchars($post['thumbnail']) ?>')" onclick="location.href='index.php?page=post&id=<?= $post['id'] ?>'">
                    <div class="carousel-content">
                        <h3 class="carousel-title"><?= htmlspecialchars($post['title']) ?></h3>
                        <p class="carousel-excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>
                        <div class="carousel-meta"><?= htmlspecialchars($post['date']) ?> • <?= htmlspecialchars($post['readTime']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($featuredPosts) > 1): ?>
            <button class="carousel-nav prev" onclick="prevSlide()">‹</button>
            <button class="carousel-nav next" onclick="nextSlide()">›</button>
            <div class="carousel-indicators" id="carousel-indicators">
                <?php foreach ($featuredPosts as $index => $post): ?>
                    <div class="carousel-dot <?= $index === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $index ?>)"></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>