<?php
include '../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Livestock Disease Monitoring System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a237e;
            --secondary-color: #283593;
            --accent-color: #3949ab;
            --text-light: #ffffff;
            --text-dark: #333333;
            --background-light: #f5f7fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-light);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: var(--primary-color);
            color: var(--text-light);
            padding: 2rem 0;
            position: fixed;
            height: 100vh;
            transition: all var(--transition-speed) ease;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .sidebar.closed {
            transform: translateX(-280px);
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
        }

        .nav-menu {
            list-style: none;
            padding: 1rem 0;
        }

        .nav-item {
            padding: 0.5rem 1.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--text-light);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all var(--transition-speed) ease;
        }

        .nav-link i {
            margin-right: 1rem;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .nav-link:hover {
            background-color: var(--secondary-color);
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: var(--accent-color);
        }

        .main-content {
            flex: 1;
            margin-left: 280px;
            transition: margin var(--transition-speed) ease;
            padding: 2rem;
            padding-bottom: 5rem; /* Space for footer */
        }

        .main-content.shifted {
            margin-left: 0;
        }

        .dashboard-header {
            background: var(--text-light);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .dashboard-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 600;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .card {
            background: var(--text-light);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            transition: transform var(--transition-speed) ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-icon {
            font-size: 2.5rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .card h3 {
            font-size: 2.5rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .card p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .card-link {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--accent-color);
            color: var(--text-light);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: background var(--transition-speed) ease;
        }

        .card-link:hover {
            background: var(--secondary-color);
        }

        .toggle-btn {
            position: fixed;
            top: 1rem;
            left: 1rem;
            background: var(--primary-color);
            color: var(--text-light);
            border: none;
            padding: 0.75rem;
            border-radius: 0.5rem;
            cursor: pointer;
            z-index: 1000;
            transition: all var(--transition-speed) ease;
        }

        .toggle-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.1);
        }

        footer {
            background: var(--primary-color);
            color: var(--text-light);
            text-align: center;
            padding: 1rem;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 999;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-280px);
                z-index: 1001;
            }
            
            .sidebar.closed {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }

            .toggle-btn {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Dashboard</h2>
        </div>
        <ul class="nav-menu">
            <!-- Register Users moved higher in the menu -->
            <li class="nav-item">
                <a href="javascript:void(0)" onclick="loadContent('add_user.php')" class="nav-link">
                    <i class="fas fa-user-plus"></i>
                    <span>Manage Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" onclick="loadContent('disease_managemnt.php')" class="nav-link">
                    <i class="fas fa-virus"></i>
                    <span>Manage Diseases</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" onclick="loadContent('reports.php')" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>View Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" onclick="loadContent('../profile.php')" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" onclick="loadContent('../settings.php')" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="../logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <h1>Welcome to the Admin Dashboard</h1>
        </div>

        <div id="content-area">
            <div class="dashboard-cards">
                <div class="card">
                    <i class="fas fa-users card-icon"></i>
                    <h3>
                        <?php
                        if ($conn) {
                            try {
                                $query = $conn->query("SELECT COUNT(*) AS user_count FROM users WHERE role = 'client'");
                                if ($query) {
                                    $result = $query->fetch_assoc();
                                    echo $result['user_count'];
                                } else {
                                    echo "0";
                                }
                            } catch (Exception $e) {
                                error_log("Database query error: " . $e->getMessage());
                                echo "0";
                            }
                        } else {
                            echo "0";
                        }
                        ?>
                    </h3>
                    <h3>Registered Users</h3>
                    <p>Manage system users and access</p>
                    <a href="javascript:void(0)" onclick="loadContent('add_user.php')" class="card-link">Manage Users</a>
                </div>
                <div class="card">
                    <i class="fas fa-virus card-icon"></i>
                    <h3>Disease Management</h3>
                    <p>Add, edit, and monitor livestock diseases</p>
                    <a href="javascript:void(0)" onclick="loadContent('disease_managemnt.php')" class="card-link">Manage Diseases</a>
                </div>
                <div class="card">
                    <i class="fas fa-chart-line card-icon"></i>
                    <h3>Reports & Analytics</h3>
                    <p>View detailed disease outbreak reports</p>
                    <a href="javascript:void(0)" onclick="loadContent('reports.php')" class="card-link">View Reports</a>
                </div>
            </div>
        </div>
    </div>

    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <footer>
        <p>&copy; 2024 Livestock Disease Monitoring System | All Rights Reserved</p>
    </footer>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('closed');
            document.querySelector('.main-content').classList.toggle('shifted');
        }

        function loadContent(page) {
            const contentArea = document.getElementById('content-area');
            
            fetch(page)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    contentArea.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error loading content:', error);
                    contentArea.innerHTML = `
                        <div style="text-align: center; padding: 2rem;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #dc3545;"></i>
                            <p style="margin-top: 1rem; color: #666;">Error loading content. Please try again.</p>
                        </div>
                    `;
                });
        }
    </script>
</body>
</html>