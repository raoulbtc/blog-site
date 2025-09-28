<div class="single-post">
    <button class="back-btn" onclick="location.href='index.php'">← Back to Home</button>
    
    <div class="post-header">
        <h1 class="post-title"><?= htmlspecialchars($currentPost['title']) ?></h1>
        <div class="post-meta"><?= htmlspecialchars($currentPost['date']) ?> • <?= htmlspecialchars($currentPost['readTime']) ?></div>
    </div>

    <img src="<?= htmlspecialchars($currentPost['thumbnail']) ?>" alt="<?= htmlspecialchars($currentPost['title']) ?>" class="post-thumbnail">

    <div class="ad-space">
        <h3>CLASSIFIED BRIEFING</h3>
        <p>Secure intelligence reports delivered directly to cleared personnel</p>
    </div>

    <div class="post-content">
        <?php 
        $contentParagraphs = explode("\n", $currentPost['content']);
        foreach ($contentParagraphs as $paragraph): 
            $paragraph = trim($paragraph);
            if (!empty($paragraph)):
        ?>
            <p><?= htmlspecialchars($paragraph) ?></p>
        <?php 
            endif;
        endforeach; 
        ?>
    </div>

    <div class="ad-space">
        <h3>RELATED INTELLIGENCE</h3>
        <p>Access additional classified files and supplementary briefing materials</p>
    </div>
</div>