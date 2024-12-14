<?php
include 'database.php';

//---------------------------------------- Condition to check that POST value is not empty.
if (!empty($_POST)) {
    // Keep track of POST values
    $id = $_POST['id'];

    $myObj = (object)array();

    //----------------------------------------
    $pdo = Database::connect();

    // Table name: 'esp32_table_dht11_leds_update'.
    // This table is updated by ESP32 to store the latest DHT11 and MQ135 sensor data and LED states.
    $sql = 'SELECT * FROM esp32_table_dht11_leds_record WHERE id="' . $id . '"';
    foreach ($pdo->query($sql) as $row) {
        $date = date_create($row['date']);
        $dateFormat = date_format($date, "d-m-Y");

        // Populate JSON object with retrieved data
        $myObj->id = $row['id'];
        $myObj->temperature = $row['temperature'];
        $myObj->humidity = $row['humidity'];
        $myObj->status_read_sensor_dht11 = $row['status_read_sensor_dht11'];
        $myObj->air_quality = $row['air_quality']; // New field for MQ135 air quality
        $myObj->status_read_mq135 = $row['status_read_mq135']; // New field for MQ135 status
        $myObj->LED_01 = $row['LED_01'];
        $myObj->LED_02 = $row['LED_02'];
        $myObj->ls_time = $row['time'];
        $myObj->ls_date = $dateFormat;

        // Convert to JSON and output
        $myJSON = json_encode($myObj);
        echo $myJSON;
    }

    Database::disconnect();
    //----------------------------------------
}
?>
