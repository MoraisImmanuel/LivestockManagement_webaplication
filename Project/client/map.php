<?php

require_once '../config/db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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
$reports = array();

while ($row = $result->fetch_assoc()) {
    // Get coordinates for the city
    $cityName = trim($row['location']);
    if (isset($namibiaCities[$cityName])) {
        $coordinates = $namibiaCities[$cityName];
        
        $severity = 'low';
        if ($row['affected_count'] > 50) {
            $severity = 'high';
        } elseif ($row['affected_count'] > 20) {
            $severity = 'medium';
        }

        $reports[] = array(
            'id' => $row['id'],
            'location' => $coordinates,
            'cityName' => $cityName,
            'disease' => $row['disease_name'],
            'severity' => $severity,
            'date' => $row['reported_date'],
            'affectedAnimals' => $row['animal_type'],
            'cases' => $row['affected_count'],
            'status' => $row['status']
        );
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Namibia Disease Outbreak Map</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <style>
             header {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: center; /* Centers the content horizontally */
            align-items: center; /* Aligns items vertically */
        }
        #back {
            position: absolute;
            left: 20px; /* Adjust the value as needed */
            color: white;
            font-size: 18px;
            text-decoration: underline; /* Remove underline if it's a link */
        }

        header h1 {
            margin: 0;
        }
        #map {
            border:5px solid blue;
            height: 400px;
            width: 60%;
            max-width: 1200px;
            margin: 20px auto;
            margin-top:50px;
        }
        ul li {
            list-style:none;
            
        }
        table {
            width: 60%;
            border-collapse: collapse;
            margin: 20px auto;
            max-width: 1200px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .status-Active { background-color: #dc3545; color: white; padding: 2px 8px; border-radius: 3px; }
        .status-Controlled { background-color: #ffc107; color: white; padding: 2px 8px; border-radius: 3px; }
        .status-Resolved { background-color: #28a745; color: white; padding: 2px 8px; border-radius: 3px; }
    </style>
</head>
<body>
<header>
<ul>
        <li><a id="back" href="home.php">BACK</a></li>
    </ul>
            <h1>Welcome to the Livestock Disease Monitoring System</h1>
          
        </header>
    <div id="map"></div>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Disease Name</th>
                <th>Location</th>
                <th>Animal Type</th>
                <th>Affected Count</th>
                <th>Reported Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
                <tr>
                    <td><?php echo htmlspecialchars($report['id']); ?></td>
                    <td><?php echo htmlspecialchars($report['disease']); ?></td>
                    <td><?php echo htmlspecialchars($report['cityName']); ?></td>
                    <td><?php echo htmlspecialchars($report['affectedAnimals']); ?></td>
                    <td><?php echo htmlspecialchars($report['cases']); ?></td>
                    <td><?php echo htmlspecialchars($report['date']); ?></td>
                    <td class="status-<?php echo $report['status']; ?>">
                        <?php echo htmlspecialchars($report['status']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
    <script>
        // Convert PHP array to JavaScript
        const diseaseReports = <?php echo json_encode($reports); ?>;
        const namibiaCities = <?php echo json_encode($namibiaCities); ?>;

        // Initialize the map centered on Namibia
        const map = L.map('map').setView([-22.5609, 17.0658], 6);

        // Add the OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Add markers for each disease report
        diseaseReports.forEach(report => {
            const marker = L.marker(report.location);
            const popupContent = `
                <div style="min-width: 200px;">
                    <h3>${report.disease}</h3>
                    <p><strong>Location:</strong> ${report.cityName}</p>
                    <p><strong>Date Reported:</strong> ${report.date}</p>
                    <p><strong>Animal Type:</strong> ${report.affectedAnimals}</p>
                    <p><strong>Affected Count:</strong> ${report.cases}</p>
                    <p><strong>Status:</strong> ${report.status}</p>
                </div>
            `;
            marker.bindPopup(popupContent).addTo(map);
        });
    </script>
</body>
</html>