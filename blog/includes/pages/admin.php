<?php if (isset($showAdminLogin)): ?>
    <!-- Admin Login Form -->
    <div class="access-denied">
        <div class="denied-icon">🔒</div>
        <h1 class="denied-title">Restricted Access</h1>
        <p class="denied-message">
            This area requires security clearance. Enter your authorization code to access the intelligence network administration panel.
        </p>
        
        <?php if (isset($_GET['login_error'])): ?>
            <div class="alert alert-error" style="max-width: 400px; margin: 1rem auto;">
                Invalid authorization code. Access denied.
            </div>
        <?php endif; ?>
        
        <form method="POST" action="index.php" class="auth-form">
            <input type="hidden" name="action" value="admin_login">
            <input type="password" name="admin_password" placeholder="Authorization Code" class="auth-input" required>
            <button type="submit" class="auth-btn">Access Panel</button>
        </form>
        
        <p style="color: #666; font-size: 0.8rem; margin-top: 1rem;">
            💡 Demo password: <code style="color: #ff4757;">classified2025</code>
        </p>
    </div>

<?php else: ?>
    <!-- Admin Panel Dashboard -->
    <div class="admin-panel">
        <div class="admin-header">
            <h1 class="admin-title">Intelligence Network</h1>
            <p class="admin-subtitle">Administration Panel • Security Clearance: Level 5</p>
            <a href="index.php?page=logout" style="color: #ff4757; text-decoration: none; font-size: 0.9rem;">🚪 Secure Logout</a>
        </div>

        <!-- Statistics Cards -->
        <div class="admin-stats">
            <div class="stat-card">
                <span class="stat-number"><?= count($subscribers) ?></span>
                <div class="stat-label">Total Operatives</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count(array_filter($subscribers, function($s) { return $s['confirmed']; })) ?></span>
                <div class="stat-label">Confirmed Agents</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count(array_filter($subscribers, function($s) { return !$s['confirmed']; })) ?></span>
                <div class="stat-label">Pending Clearance</div>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?= count(array_filter($subscribers, function($s) { return strtotime($s['date']) > strtotime('-7 days'); })) ?></span>
                <div class="stat-label">New This Week</div>
            </div>
        </div>

        <!-- Subscribers Table -->
        <h2 style="color: #ff4757; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem;">Network Operatives</h2>
        
        <?php if (empty($subscribers)): ?>
            <div style="text-align: center; color: #888; padding: 2rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">📭</div>
                No operatives have joined the network yet.
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="subscribers-table">
                    <thead>
                        <tr>
                            <th>Email Address</th>
                            <th>Registration Date</th>
                            <th>IP Address</th>
                            <th>Status</th>
                            <th>Agent ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($subscribers) as $subscriber): ?>
                            <tr>
                                <td><?= htmlspecialchars($subscriber['email']) ?></td>
                                <td><?= date('M j, Y H:i', strtotime($subscriber['date'])) ?></td>
                                <td><code><?= htmlspecialchars($subscriber['ip']) ?></code></td>
                                <td>
                                    <span class="<?= $subscriber['confirmed'] ? 'status-confirmed' : 'status-pending' ?>">
                                        <?= $subscriber['confirmed'] ? '✅ Confirmed' : '⏳ Pending' ?>
                                    </span>
                                </td>
                                <td><code><?= htmlspecialchars($subscriber['id']) ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Export Options -->
        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,71,87,0.2);">
            <h3 style="color: #ff4757; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1rem;">Data Export</h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button onclick="exportCSV()" class="newsletter-btn" style="margin: 0;">📊 Export CSV</button>
                <button onclick="exportJSON()" class="newsletter-btn" style="margin: 0;">📋 Export JSON</button>
                <button onclick="showEmailList()" class="newsletter-btn" style="margin: 0;">📧 Email List</button>
            </div>
        </div>

        <!-- Hidden export data -->
        <div id="export-data" style="display: none;">
            <h4 style="color: #ff4757; margin: 1rem 0;">Email List for Newsletter Distribution:</h4>
            <textarea readonly style="width: 100%; height: 200px; background: rgba(0,0,0,0.5); color: #e0e0e0; border: 1px solid rgba(255,71,87,0.3); border-radius: 4px; padding: 1rem; font-family: monospace;">
<?php 
echo implode("\n", array_map(function($sub) { 
    return $sub['email']; 
}, $subscribers)); 
?>
            </textarea>
        </div>
    </div>

    <script>
        function exportCSV() {
            const subscribers = <?= json_encode($subscribers) ?>;
            let csv = 'Email,Registration Date,IP Address,Status,Agent ID\n';
            
            subscribers.forEach(sub => {
                csv += `"${sub.email}","${sub.date}","${sub.ip}","${sub.confirmed ? 'Confirmed' : 'Pending'}","${sub.id}"\n`;
            });
            
            downloadFile(csv, 'intelligence-network-operatives.csv', 'text/csv');
        }
        
        function exportJSON() {
            const subscribers = <?= json_encode($subscribers, JSON_PRETTY_PRINT) ?>;
            downloadFile(JSON.stringify(subscribers, null, 2), 'intelligence-network-operatives.json', 'application/json');
        }
        
        function showEmailList() {
            document.getElementById('export-data').style.display = 'block';
            document.getElementById('export-data').scrollIntoView({ behavior: 'smooth' });
        }
        
        function downloadFile(content, filename, contentType) {
            const blob = new Blob([content], { type: contentType });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }
    </script>

<?php endif; ?>