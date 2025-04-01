<?php
session_start();
require_once '../config/db.php';

// Authentication check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header("Location: ../login.php");
    exit();
}

// Namibia cities data
$namibiaCities = [
    'Windhoek' => [-22.5609, 17.0658],
    'Swakopmund' => [-22.6784, 14.5266],
    'Oshakati' => [-17.7883, 15.7044],
    'Gobabis' => [-22.4500, 18.9667],
    'Walvis Bay' => [-22.9575, 14.5053],
    'Otjiwarongo' => [-20.4637, 16.6477],
    'Keetmanshoop' => [-26.5833, 18.1333],
    'Rundu' => [-17.9333, 19.7667]
];

// Fetch disease cases
$query = "
    SELECT 
        id,
        disease_name,
        location,
        animal_type,
        affected_count,
        reported_date,
        status
    FROM disease_cases
    WHERE reported_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    ORDER BY reported_date DESC
";

$result = $conn->query($query);
$reports = [];

while ($row = $result->fetch_assoc()) {
    $cityName = trim($row['location']);
    if (isset($namibiaCities[$cityName])) {
        $coordinates = $namibiaCities[$cityName];
        
        $severity = 'low';
        if ($row['affected_count'] > 50) {
            $severity = 'high';
        } elseif ($row['affected_count'] > 20) {
            $severity = 'medium';
        }

        $reports[] = [
            'id' => $row['id'],
            'location' => $coordinates,
            'cityName' => $cityName,
            'disease' => $row['disease_name'],
            'severity' => $severity,
            'date' => $row['reported_date'],
            'affectedAnimals' => $row['animal_type'],
            'cases' => $row['affected_count'],
            'status' => $row['status']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Livestock Disease Monitoring System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            color: #2d3436;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: #ecf0f1;
            position: fixed;
            top: 0;
            left: 0;
            padding: 2rem 0;
            transition: all 0.3s ease;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar.closed {
            left: -280px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 2rem;
            padding: 0 1rem;
            font-size: 1.5rem;
            color: #ecf0f1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 1rem;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0 1rem;
        }

        .sidebar ul li {
            margin-bottom: 0.5rem;
        }

        .sidebar ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 1rem;
            display: block;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar ul li a i {
            width: 20px;
            text-align: center;
        }

        .sidebar ul li a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .logout-btn {
            color: #ff7675 !important;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Main Content Area */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            background-color: #f5f6fa;
            position: relative;
        }

        .main-content.shifted {
            margin-left: 0;
        }

        /* Header Styles */
        header {
            background: #fff;
            color: #2d3436;
            padding: 1.5rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 1.75rem;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Content Area Styles */
        #content-area {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        /* Card Styles */
        .card {
            background: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3436;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(44, 62, 80, 0.1);
        }

        /* Toggle Button */
        .toggle-btn {
            background-color: transparent;
            color: #2d3436;
            border: none;
            padding: 0.75rem;
            cursor: pointer;
            font-size: 1.25rem;
            display: none;
            transition: all 0.2s ease;
        }

        .toggle-btn:hover {
            color: #34495e;
        }

        /* Footer Styles */
        footer {
            background-color: #fff;
            color: #2d3436;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
            border-top: 1px solid #eee;
        }

        /* Button Styles */
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: #2c3e50;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #34495e;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -280px;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .toggle-btn {
                display: block;
            }
            
            .sidebar.closed {
                left: 0;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        /* Loading Animation */
        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1000;
        }

        .loading::after {
            content: '';
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2c3e50;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 1rem;
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: none;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
    </style>
</head>
<body>
    <!-- Loading Indicator -->
    <div class="loading" id="loading"></div>

    <!-- Notification Area -->
    <div class="notification" id="notification"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Client Dashboard</h2>
        <ul>
            <li><a href="map.php"><i class="fas fa-map-marked-alt"></i>Disease Reports Map</a></li>
            <li><a href="javascript:void(0)" onclick="loadContent('report.php')"><i class="fas fa-file-medical"></i>Report Disease</a></li>
            <li><a href="javascript:void(0)" onclick="loadContent('notifications.php')"><i class="fas fa-bell"></i>Notifications</a></li>
            <li><a href="javascript:void(0)" onclick="loadContent('../profile.php')"><i class="fas fa-user"></i>Profile</a></li>
            <li><a href="javascript:void(0)" onclick="loadContent('../settings.php')"><i class="fas fa-cog"></i>Settings</a></li>
            <li><a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <header>
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h1>Livestock Disease Monitoring System</h1>
            <div class="user-info">
                <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                <img src="/api/placeholder/40/40" alt="User Avatar">
            </div>
        </header>

        <!-- Dashboard Overview -->
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Active Cases</h3>
                    <div class="card-icon">
                        <i class="fas fa-virus"></i>
                    </div>
                </div>
                <p>Total active cases: <?php echo count(array_filter($reports, function($report) { return $report['status'] === 'active'; })); ?></p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Reports</h3>
                    <div class="card-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
                <p>Reports in last 30 days: <?php echo count(array_filter($reports, function($report) { 
                    return strtotime($report['date']) > strtotime('-30 days'); 
                })); ?></p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">High Severity Cases</h3>
                    <div class="card-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <p>High severity cases: <?php echo count(array_filter($reports, function($report) { 
                    return $report['severity'] === 'high'; 
                })); ?></p>
            </div>
        </div>

        <!-- Content Area -->
        <div id="content-area" class="card">
            <h2>Welcome to your Dashboard</h2>
            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="loadContent('report.php')">
                        <i class="fas fa-plus"></i>
                        New Report
                    </button>
                    
                    <button onclick="window.location.href='map.php'" class="btn btn-primary">
                    <i class="fas fa-map"></i>
                     View Map
                    </button>
                </div>
            </div>

            <!-- Recent Reports Table -->
            <div class="recent-reports">
                <h3>Recent Reports</h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Disease</th>
                                <th>Status</th>
                                <th>Severity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($reports, 0, 5) as $report): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(date('M d, Y', strtotime($report['date']))); ?></td>
                                <td><?php echo htmlspecialchars($report['cityName']); ?></td>
                                <td><?php echo htmlspecialchars($report['disease']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $report['status']; ?>">
                                        <?php echo ucfirst(htmlspecialchars($report['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="severity-badge <?php echo $report['severity']; ?>">
                                        <?php echo ucfirst(htmlspecialchars($report['severity'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-icon" onclick="viewReport(<?php echo $report['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer>
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> Livestock Disease Monitoring System | All Rights Reserved</p>
            </div>
        </footer>
    </div>

    <!-- Additional Styles for new elements -->
    <style>
        /* Quick Actions */
        .quick-actions {
            margin: 2rem 0;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
            margin-top: 1rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .data-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .data-table tr:hover {
            background-color: #f8f9fa;
        }

        /* Status and Severity Badges */
        .status-badge,
        .severity-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: #e3fcef;
            color: #0d6832;
        }

        .status-badge.closed {
            background-color: #f1f1f1;
            color: #666;
        }

        .severity-badge.high {
            background-color: #ffe4e4;
            color: #cc0000;
        }

        .severity-badge.medium {
            background-color: #fff4e4;
            color: #cc7700;
        }

        .severity-badge.low {
            background-color: #e3fcef;
            color: #0d6832;
        }

        /* Icon Button */
        .btn-icon {
            background: none;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            color: #2c3e50;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .btn-icon:hover {
            background-color: #f1f1f1;
            color: #34495e;
        }
    </style>

    <!-- JavaScript -->
    <script>
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('closed');
            mainContent.classList.toggle('shifted');
        }

        // Show loading indicator
        function showLoading() {
            document.getElementById('loading').style.display = 'block';
        }

        // Hide loading indicator
        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }

        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        // Load content dynamically
        function loadContent(page) {
            const contentArea = document.getElementById('content-area');
            showLoading();

            fetch(page)
                .then(response => response.text())
                .then(data => {
                    contentArea.innerHTML = data;
                    hideLoading();
                })
                .catch(error => {
                    console.error('Error loading content:', error);
                    contentArea.innerHTML = '<p>Error loading content. Please try again.</p>';
                    hideLoading();
                    showNotification('Error loading content', 'error');
                });
        }

        // View report details
        function viewReport(id) {
            loadContent(`view_report.php?id=${id}`);
        }

        // Handle mobile menu
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar').classList.add('closed');
                document.querySelector('.main-content').classList.add('shifted');
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar').classList.add('closed');
                document.querySelector('.main-content').classList.add('shifted');
            } else {
                document.querySelector('.sidebar').classList.remove('closed');
                document.querySelector('.main-content').classList.remove('shifted');
            }
        });
    </script>

</body>
</html>