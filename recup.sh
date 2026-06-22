#!/opt/lampp/bin/php
<?php


// Disable mysqli error reporting for custom error handling
mysqli_report(MYSQLI_REPORT_OFF);

// 1. Database Configuration
$db_host = "127.0.0.1";
$db_user = "taho";
$db_pass = "rt";
$db_name = "sae23";

// 2. MQTT Broker Configuration
$mqtt_host = "mqtt.iut-blagnac.fr";
$mqtt_port = 8883; 
$mqtt_user = "student";
$mqtt_pass = "student";

// Target filtering rules
$target_rooms = ["E105", "E106", "B106", "B113"];
$wildcard_topic = "sensors/AM107/by-room/+/data";

echo "--- Continuous Pipe Secure Daemon Started: " . date('Y-m-d H:i:s') . " ---\n";

// 3. Build the mosquitto_sub command string safely
$command = "mosquitto_sub -h " . escapeshellarg($mqtt_host) . " -p " . $mqtt_port . " -u " . escapeshellarg($mqtt_user) . " -P " . escapeshellarg($mqtt_pass) . " -t " . escapeshellarg($wildcard_topic) . " 2>&1";

// Open persistent process pipe to read MQTT stream live
$handle = popen($command, 'r');

// Check if pipe creation succeeded
if (!$handle) {
    die("CRITICAL ERROR: Failed to execute mosquitto_sub pipe.\n");
}

// 4. Continuous stream reader loop
while (!feof($handle)) {
    // Read stream line by line and trim spaces
    $raw_payload = trim(fgets($handle));

    // Skip empty lines
    if (empty($raw_payload)) {
        continue;
    }

    // Catch network or connection errors from mosquitto_sub
    if (strpos($raw_payload, 'error') !== false || strpos($raw_payload, 'Refused') !== false) {
        echo "[" . date('H:i:s') . "] MQTT PROCESS ERROR: $raw_payload\n";
        sleep(5); // Cooldown before next iteration
        continue;
    }

    // Decode incoming JSON payload
    $parsed_json = json_decode($raw_payload, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        continue; // Skip invalid JSON
    }

    // Extract room name from metadata block (Index [1])
    $current_room = isset($parsed_json[1]['room']) ? $parsed_json[1]['room'] : null;

    if ($current_room !== null) {
        // Filter: Only process allowed target rooms
        if (in_array($current_room, $target_rooms)) {
            echo "[" . date('H:i:s') . "] [MATCH] Packet accepted for target room: $current_room\n";
            
            // Establish short-lived database connection
            $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
            if (!$conn) {
                echo "[" . date('H:i:s') . "] DATABASE ERROR: Connection failed.\n";
                continue;
            }

            // Extract metrics from measurements block (Index [0])
            $temperature = isset($parsed_json[0]['temperature']) ? $parsed_json[0]['temperature'] : null;
            $humidity    = isset($parsed_json[0]['humidity'])    ? $parsed_json[0]['humidity']    : null;

            // Save valid temperature data
            if ($temperature !== null) {
                insert_metric($conn, $current_room . "_Temp", $temperature);
            }
            // Save valid humidity data
            if ($humidity !== null) {
                insert_metric($conn, $current_room . "_Hum", $humidity);
            }

            // Close database connection
            mysqli_close($conn);
        } else {
            // Ignore rooms not belongs to the group
            echo "[" . date('H:i:s') . "] [IGNORED] Packet detected for room: $current_room (Not in your group list)\n";
        }
    }
}

// Close the process pipe handle
pclose($handle);

/**
 * Fetch sensor ID and insert measurement into database
 */
function insert_metric($conn, $sensor_name, $value) {
    // Prevent SQL Injection
    $escaped_name = mysqli_real_escape_string($conn, $sensor_name);
    
    // Find matching sensor ID
    $find_id_query = "SELECT id_capteur FROM capteur WHERE nom_capteur = '$escaped_name'";
    $result = mysqli_query($conn, $find_id_query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id_capteur = $row['id_capteur'];
        
        // Prepare data insertion query
        $insert_query = "INSERT INTO mesure (date_mesure, horaire_mesure, valeur, id_capteur) 
                         VALUES (CURRENT_DATE(), CURRENT_TIME(), $value, $id_capteur)";
        
        // Execute insertion and log outcome
        if (mysqli_query($conn, $insert_query)) {
            echo "[" . date('H:i:s') . "] -> DB SUCCESS: Saved $sensor_name = $value\n";
        } else {
            echo "[" . date('H:i:s') . "] -> DB SQL ERROR: Failed to insert value.\n";
        }
    } else {
        echo "[" . date('H:i:s') . "] -> DB WARNING: Sensor '$sensor_name' not found in database.\n";
    }
}
?>
