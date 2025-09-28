<?php

// Calculate reading time based on word count
function calculateReadTime($text) {
    $wordsPerMinute = 225;
    $words = str_word_count(strip_tags($text));
    $readTimeMinutes = ceil($words / $wordsPerMinute);
    
    if ($readTimeMinutes === 1) {
        return "1 min read";
    } else {
        return $readTimeMinutes . " min read";
    }
}

// Generate simple 3-character captcha
function generateSimpleCaptcha() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $captcha = '';
    
    for ($i = 0; $i < 3; $i++) {
        $captcha .= $characters[random_int(0, strlen($characters) - 1)];
    }
    
    return $captcha;
}

// Get all blog posts
function getAllPosts() {
    $postsData = file_get_contents(__DIR__ . '/../data/posts.json');
    $posts = json_decode($postsData, true);
    
    // Auto-calculate read times if missing
    foreach ($posts as &$post) {
        if (empty($post['readTime'])) {
            $post['readTime'] = calculateReadTime($post['content']);
        }
    }
    
    return $posts;
}

// Get featured posts for carousel
function getFeaturedPosts($posts) {
    return array_filter($posts, function($post) {
        return isset($post['featured']) && $post['featured'] === true;
    });
}

// Get recent non-featured posts
function getRecentPosts($posts, $limit = 12) {
    $nonFeatured = array_filter($posts, function($post) {
        return !isset($post['featured']) || $post['featured'] !== true;
    });
    
    // Sort by date (newest first)
    usort($nonFeatured, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return array_slice($nonFeatured, 0, $limit);
}

// Get archive posts (older than recent limit)
function getArchivePosts($posts, $recentLimit = 12) {
    $nonFeatured = array_filter($posts, function($post) {
        return !isset($post['featured']) || $post['featured'] !== true;
    });
    
    // Sort by date (newest first)
    usort($nonFeatured, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return array_slice($nonFeatured, $recentLimit);
}

// Get archive data grouped by month/year
function getArchiveData($posts) {
    $archivePosts = getArchivePosts($posts);
    $groups = [];
    
    foreach ($archivePosts as $post) {
        $date = new DateTime($post['date']);
        $sortKey = $date->format('Y-m');
        $month = $date->format('F');
        $year = $date->format('Y');
        
        if (!isset($groups[$sortKey])) {
            $groups[$sortKey] = [
                'sortKey' => $sortKey,
                'month' => $month,
                'year' => $year,
                'count' => 0,
                'displayName' => "$month $year"
            ];
        }
        $groups[$sortKey]['count']++;
    }
    
    // Sort by date (newest first)
    uksort($groups, function($a, $b) {
        return strcmp($b, $a);
    });
    
    return array_values($groups);
}

// Get single post by ID
function getPostById($posts, $id) {
    foreach ($posts as $post) {
        if ($post['id'] == $id) {
            return $post;
        }
    }
    return null;
}

// Get posts by time period
function getPostsByPeriod($posts, $period) {
    if (empty($period)) return [];
    
    $archivePosts = getArchivePosts($posts);
    $filteredPosts = [];
    
    foreach ($archivePosts as $post) {
        $date = new DateTime($post['date']);
        $postPeriod = $date->format('Y-m');
        
        if ($postPeriod === $period) {
            $filteredPosts[] = $post;
        }
    }
    
    // Sort by date (newest first)
    usort($filteredPosts, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return $filteredPosts;
}

// Get human-readable period name
function getPeriodName($period) {
    if (empty($period)) return '';
    
    $parts = explode('-', $period);
    if (count($parts) !== 2) return '';
    
    $year = $parts[0];
    $month = $parts[1];
    
    $date = DateTime::createFromFormat('Y-m', "$year-$month");
    if ($date) {
        return $date->format('F Y');
    }
    
    return '';
}

// Save email subscription
function saveSubscription($email, $ipAddress = '') {
    $dataDir = __DIR__ . '/../data';
    
    // Create directory if it doesn't exist
    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0755, true);
    }
    
    $subscriptionsFile = $dataDir . '/subscriptions.json';
    
    // Create file if it doesn't exist
    if (!file_exists($subscriptionsFile)) {
        file_put_contents($subscriptionsFile, json_encode([]));
    }
    
    $subscriptions = json_decode(file_get_contents($subscriptionsFile), true);
    
    // Check if email already exists
    foreach ($subscriptions as $sub) {
        if ($sub['email'] === $email) {
            return ['success' => false, 'message' => 'Email already registered in our secure network.'];
        }
    }
    
    // Add new subscription
    $subscriptions[] = [
        'email' => $email,
        'date' => date('Y-m-d H:i:s'),
        'ip' => $ipAddress,
        'confirmed' => false,
        'id' => uniqid()
    ];
    
    // Save to file
    if (file_put_contents($subscriptionsFile, json_encode($subscriptions, JSON_PRETTY_PRINT))) {
        return ['success' => true, 'message' => 'Welcome to the network, operative. Your secure briefings will begin shortly.'];
    } else {
        return ['success' => false, 'message' => 'Secure channel temporarily unavailable. Please try again.'];
    }
}

// Get all subscriptions (for admin panel)
function getAllSubscriptions() {
    $subscriptionsFile = __DIR__ . '/../data/subscriptions.json';
    
    if (!file_exists($subscriptionsFile)) {
        return [];
    }
    
    return json_decode(file_get_contents($subscriptionsFile), true) ?: [];
}

?>