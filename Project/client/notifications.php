<?php
// notifications.php
session_start();
require_once '../config/db.php';

// Database connection assumed to be already established
function getDiseaseNotifications() {
    global $conn;
    
    $query = "
        SELECT 
            id,
            disease_name,
            location,
            animal_type,
            affected_count,
            CASE 
                WHEN affected_count > 100 THEN 'critical'
                WHEN affected_count > 50 THEN 'high'
                WHEN affected_count > 20 THEN 'moderate'
                ELSE 'low'
            END as severity_level
        FROM disease_cases 
        ORDER BY affected_count DESC, id DESC
    ";
    
    $result = $conn->query($query);
    $notifications = [];
    
    while ($row = $result->fetch_assoc()) {
        // Create notification message based on severity
        $icon = '';
        $bgColor = '';
        
        switch($row['severity_level']) {
            case 'critical':
                $icon = 'fa-exclamation-triangle';
                $bgColor = 'bg-red-100';
                break;
            case 'high':
                $icon = 'fa-exclamation-circle';
                $bgColor = 'bg-orange-100';
                break;
            case 'moderate':
                $icon = 'fa-info-circle';
                $bgColor = 'bg-yellow-100';
                break;
            default:
                $icon = 'fa-info';
                $bgColor = 'bg-blue-100';
        }
        
        $notifications[] = [
            'id' => $row['id'],
            'message' => sprintf(
                "%s outbreak reported in %s affecting %d %s",
                $row['disease_name'],
                $row['location'],
                $row['affected_count'],
                $row['animal_type']
            ),
            'severity' => $row['severity_level'],
            'icon' => $icon,
            'bgColor' => $bgColor
        ];
    }
    
    return $notifications;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disease Notifications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .notifications-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .notification-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .notification-icon {
            margin-right: 15px;
            font-size: 20px;
        }

        .notification-content {
            flex-grow: 1;
        }

        .notification-severity {
            font-size: 0.875rem;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
            margin-left: 10px;
        }

        /* Severity-specific styles */
        .severity-critical {
            border-left-color: #dc2626;
            background-color: #fee2e2;
        }
        .severity-critical .notification-icon {
            color: #dc2626;
        }
        .severity-critical .notification-severity {
            background-color: #dc2626;
            color: white;
        }

        .severity-high {
            border-left-color: #ea580c;
            background-color: #ffedd5;
        }
        .severity-high .notification-icon {
            color: #ea580c;
        }
        .severity-high .notification-severity {
            background-color: #ea580c;
            color: white;
        }

        .severity-moderate {
            border-left-color: #d97706;
            background-color: #fef3c7;
        }
        .severity-moderate .notification-icon {
            color: #d97706;
        }
        .severity-moderate .notification-severity {
            background-color: #d97706;
            color: white;
        }

        .severity-low {
            border-left-color: #2563eb;
            background-color: #dbeafe;
        }
        .severity-low .notification-icon {
            color: #2563eb;
        }
        .severity-low .notification-severity {
            background-color: #2563eb;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="notifications-container">
        <h2>Disease Outbreak Notifications</h2>
        
        <?php
        $notifications = getDiseaseNotifications();
        
        if (empty($notifications)) {
            echo '<div class="empty-state">
                    <i class="fas fa-inbox fa-3x"></i>
                    <p>No notifications at this time</p>
                  </div>';
        } else {
            foreach ($notifications as $notification) {
                echo sprintf(
                    '<div class="notification-item severity-%s">
                        <div class="notification-icon">
                            <i class="fas %s"></i>
                        </div>
                        <div class="notification-content">
                            <span>%s</span>
                            <span class="notification-severity">%s</span>
                        </div>
                    </div>',
                    $notification['severity'],
                    $notification['icon'],
                    htmlspecialchars($notification['message']),
                    ucfirst($notification['severity'])
                );
            }
        }
        ?>
    </div>

    <script>
        // Add click handler for notifications if needed
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                // Handle notification click - e.g., mark as read, show details, etc.
            });
        });
    </script>
</body>
</html>