<?php
require 'database.php';

//---------------------------------------- Condition to check that POST value is not empty.
if (!empty($_POST)) {
    //........................................ keep track POST values
    $id = $_POST['id'];
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    $status_read_sensor_dht11 = $_POST['status_read_sensor_dht11'];
    $led_01 = $_POST['led_01'];
    $led_02 = $_POST['led_02'];
    $air_quality = $_POST['air_quality']; // Added
    $status_read_mq135 = $_POST['status_read_mq135']; // Added
    //........................................

    //........................................ Get the time and date.
    date_default_timezone_set("Asia/Kolkata"); // Look here for your timezone: https://www.php.net/manual/en/timezones.php
    $tm = date("H:i:s");
    $dt = date("Y-m-d");
    //........................................

    //........................................ Updating the data in the table.
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update `esp32_table_dht11_leds_update` with DHT11 and MQ135 data
    $sql = "UPDATE esp32_table_dht11_leds_record SET temperature = ?, humidity = ?, status_read_sensor_dht11 = ?, air_quality = ?, status_read_mq135 = ?, time = ?, date = ? WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($temperature, $humidity, $status_read_sensor_dht11, $air_quality, $status_read_mq135, $tm, $dt, $id));
    Database::disconnect();
    //........................................

    //........................................ Entering data into a table.
    $id_key;
    $board = $_POST['id'];
    $found_empty = false;

    $pdo = Database::connect();

    //:::::::: Process to check if "id" is already in use.
    while ($found_empty == false) {
        $id_key = generate_string_id(10);
        $sql = 'SELECT * FROM esp32_table_dht11_leds_record WHERE id="' . $id_key . '"';
        $q = $pdo->prepare($sql);
        $q->execute();

        if (!$data = $q->fetch()) {
            $found_empty = true;
        }
    }
    //::::::::

    //:::::::: The process of entering data into a table.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert new record into `esp32_table_dht11_leds_record` with DHT11 and MQ135 data
    $sql = "INSERT INTO esp32_table_dht11_leds_record (id, board, temperature, humidity, status_read_sensor_dht11, air_quality, status_read_mq135, LED_01, LED_02, time, date) 
            values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $q = $pdo->prepare($sql);
    $q->execute(array($id_key, $board, $temperature, $humidity, $status_read_sensor_dht11, $air_quality, $status_read_mq135, $led_01, $led_02, $tm, $dt));
    //::::::::

    Database::disconnect();
    //........................................ 
}
//---------------------------------------- 

//---------------------------------------- Function to create "id" based on numbers and characters.
function generate_string_id($strength = 16) {
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $input_length = strlen($permitted_chars);
    $random_string = '';
    for ($i = 0; $i < $strength; $i++) {
        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}
//---------------------------------------- 
?>
