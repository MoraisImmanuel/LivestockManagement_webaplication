<?php
// reports.php - Reports & Analytics page
session_start();
require_once '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disease Reports & Analytics</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center mb-6">
                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                </svg>
                <h1 class="text-2xl font-bold ml-2">Disease Reports & Analytics</h1>
            </div>

            <!-- Filter Section -->
            <!--<div class="mb-8">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-2">Animal Type</label>
                        <select name="animal_type" 
                                class="w-full px-4 py-2 border rounded-lg">
                            <option value="">All Animals</option>
                            <option value="cattle">Cattle</option>
                            <option value="sheep">Sheep</option>
                            <option value="goat">Goat</option>
                            <option value="pig">Pig</option>
                            <option value="poultry">Poultry</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-2">Date Range</label>
                        <input type="date" name="start_date" 
                               class="w-full px-4 py-2 border rounded-lg mb-2">
                        <input type="date" name="end_date" 
                               class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>-->

            <!-- Reports Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Disease Frequency Chart -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-bold mb-4">Disease Frequency</h2>
                    <?php
                    $query = "SELECT disease_name, COUNT(*) as count 
                             FROM disease_cases 
                             GROUP BY disease_name 
                             ORDER BY count DESC 
                             LIMIT 5";
                    $result = mysqli_query($conn, $query);
                    
                    if ($result) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $percentage = ($row['count'] / 100) * 100;
                            echo "<div class='mb-2'>";
                            echo "<div class='flex justify-between mb-1'>";
                            echo "<span>{$row['disease_name']}</span>";
                            echo "<span>{$row['count']} cases</span>";
                            echo "</div>";
                            echo "<div class='w-full bg-gray-200 rounded-full h-2'>";
                            echo "<div class='bg-blue-600 h-2 rounded-full' 
                                  style='width: {$percentage}%'></div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "Error: " . mysqli_error($conn);
                    }
                    ?>
                </div>

                <!-- Latest Outbreaks -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-bold mb-4">Latest Outbreaks</h2>
                    <div class="space-y-4">
                        <?php
                        $query = "SELECT * FROM disease_cases 
                                 ORDER BY reported_date DESC 
                                 LIMIT 5";
                        $result = mysqli_query($conn, $query);
                        
                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<div class='flex justify-between items-center'>";
                                echo "<div>";
                                echo "<h3 class='font-medium'>{$row['disease_name']}</h3>";
                                echo "<p class='text-sm text-gray-600'>{$row['location']}</p>";
                                echo "</div>";
                                echo "<div class='text-right'>";
                                echo "<p class='text-sm'>" . date('M d, Y', strtotime($row['reported_date'])) . "</p>";
                                echo "<p class='text-sm text-gray-600'>{$row['affected_count']} cases</p>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "Error: " . mysqli_error($conn);
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Detailed Reports Table -->
            <h2 class="text-xl font-bold mb-4">Detailed Reports</h2>
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2">Disease</th>
                            <th class="px-4 py-2">Location</th>
                            <th class="px-4 py-2">Animal Type</th>
                            <th class="px-4 py-2">Cases</th>
                            <th class="px-4 py-2">Date Reported</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM disease_cases 
                                 ORDER BY reported_date DESC";
                        $result = mysqli_query($conn, $query);
                        
                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td class='border px-4 py-2'>{$row['disease_name']}</td>";
                                echo "<td class='border px-4 py-2'>{$row['location']}</td>";
                                echo "<td class='border px-4 py-2'>{$row['animal_type']}</td>";
                                echo "<td class='border px-4 py-2'>{$row['affected_count']}</td>";
                                echo "<td class='border px-4 py-2'>" . date('M d, Y', strtotime($row['reported_date'])) . "</td>";
                                echo "<td class='border px-4 py-2'>";
                                echo "<span class='px-2 py-1 rounded-full text-sm ";
                                if ($row['status'] == 'Active') {
                                    echo "bg-red-100 text-red-800";
                                } elseif ($row['status'] == 'Controlled') {
                                    echo "bg-yellow-100 text-yellow-800";
                                } else {
                                    echo "bg-green-100 text-green-800";
                                }
                                echo "'>{$row['status']}</span>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='border px-4 py-2'>Error: " . mysqli_error($conn) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
