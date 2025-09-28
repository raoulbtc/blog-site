<header>
    <div class="logo">CLASSIFIED</div>
    <div class="tagline">Intelligence • Secrets • Truth</div>
    <nav class="nav">
        <a href="index.php" class="nav-btn <?= (!isset($_GET['page']) || $_GET['page'] === 'home') ? 'active' : '' ?>">Home</a>
        <a href="index.php?page=about" class="nav-btn <?= (isset($_GET['page']) && $_GET['page'] === 'about') ? 'active' : '' ?>">About</a>
        <a href="index.php?page=contact" class="nav-btn <?= (isset($_GET['page']) && $_GET['page'] === 'contact') ? 'active' : '' ?>">Contact</a>
    </nav>
</header>