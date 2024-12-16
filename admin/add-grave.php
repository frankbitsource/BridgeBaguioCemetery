<?php
require_once 'auth.php';
require_once '../includes/config.php';
require_once '../includes/db.php';
checkAdmin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $birth_date = trim($_POST['birth_date']);
    $death_date = trim($_POST['death_date']);
    $notes = trim($_POST['notes']);

    try {
        $stmt = $conn->prepare("INSERT INTO graves (name, latitude, longitude, birth_date, death_date, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $latitude, $longitude, $birth_date, $death_date, $notes]);
        $message = "Grave location added successfully!";
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Grave - Cemetery Locator</title>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: rgba(26, 26, 26, 0.9);
            border-radius: 10px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
        }

        .form-group input:focus, .form-group textarea:focus {
            outline: 2px solid var(--accent-color);
            background: rgba(255, 255, 255, 0.15);
        }

        .map-container {
            height: 400px;
            margin-bottom: 1.5rem;
            border-radius: 10px;
            overflow: hidden;
        }

        .success-message {
            background: rgba(0, 255, 0, 0.1);
            border-left: 4px solid #00ff00;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #00ff00;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.1);
            border-left: 4px solid #ff0000;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #ff0000;
        }

        .submit-btn {
            background: var(--accent-color);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: #876a31;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <h1>Add New Grave</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="form-container">
        <?php if($message): ?>
            <div class="success-message"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Deceased Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div id="map" class="map-container"></div>

            <div class="form-group">
                <label for="latitude">Latitude</label>
                <input type="number" id="latitude" name="latitude" step="any" required>
            </div>

            <div class="form-group">
                <label for="longitude">Longitude</label>
                <input type="number" id="longitude" name="longitude" step="any" required>
            </div>

            <div class="form-group">
                <label for="birth_date">Birth Date</label>
                <input type="date" id="birth_date" name="birth_date">
            </div>

            <div class="form-group">
                <label for="death_date">Death Date</label>
                <input type="date" id="death_date" name="death_date">
            </div>

            <div class="form-group">
                <label for="notes">Additional Notes</label>
                <textarea id="notes" name="notes" rows="4"></textarea>
            </div>

            <button type="submit" class="submit-btn">Add Grave</button>
        </form>
    </div>

    <script src='https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js'></script>
    <script>
        mapboxgl.accessToken = '<?php echo MAPBOX_TOKEN; ?>';
        
        const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/dark-v10',
            center: [120.57508914689474, 16.411633262870357],
            zoom: 17
        });

        // Add navigation controls
        map.addControl(new mapboxgl.NavigationControl());

        // Create a marker that can be moved
        const marker = new mapboxgl.Marker({
            draggable: true
        })
        .setLngLat([120.57508914689474, 16.411633262870357])
        .addTo(map);

        // Update form fields when marker is moved
        function onDragEnd() {
            const lngLat = marker.getLngLat();
            document.getElementById('latitude').value = lngLat.lat;
            document.getElementById('longitude').value = lngLat.lng;
        }

        marker.on('dragend', onDragEnd);

        // Initialize form fields with starting position
        onDragEnd();
    </script>
</body>
</html>
