<?php
$servername = '127.0.0.1'; // адрес сервера БД
$username = 'root'; // имя пользователя БД
$password = ""; // пароль пользователя БД
$dbname = 'dbip'; // имя БД

// создаём подключение к БД
$conn = new mysqli($servername, $username, $password, $dbname);
// проверяем соединение
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// запрашиваем данные из БД
$sql = "SELECT COUNT(DISTINCT ip) AS unique_visitors, DATE_FORMAT(created_at, '%Y-%m-%d %H:00:00') AS hour FROM counter GROUP BY hour ORDER BY hour ASC";
$result = $conn->query($sql);
$visitors_data = array();
while ($row = $result->fetch_assoc()) {
  $visitors_data[$row['hour']] = $row['unique_visitors'];
}

// запрашиваем данные по городам
$sql = "SELECT COUNT(DISTINCT ip) AS unique_visitors, city FROM counter GROUP BY city ORDER BY unique_visitors DESC";
$result = $conn->query($sql);
$cities_data = array();
while ($row = $result->fetch_assoc()) {
  $cities_data[$row['city']] = $row['unique_visitors'];
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Page visits counter</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    canvas {
      max-width: 50%;
      margin: 0 auto;
    }
  </style>
</head>
<body>
  <h1>Страница посещений</h1>
  <h2>Посещения по времени(UTC)</h2>
  <canvas id="visitors-chart"></canvas>
  <script>
    var visitorsData = <?php echo json_encode(array_values($visitors_data)); ?>;
    var labels = <?php echo json_encode(array_keys($visitors_data)); ?>;
    var ctx = document.getElementById('visitors-chart').getContext('2d');
    var chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Unique visitors',
          data: visitorsData,
          borderColor: 'blue'
        }]
      },
      options: {
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    });
  </script>
  <h2>Посещения по городам</h2>
  <canvas id="cities-chart"></canvas>
  <script>
    var citiesData = <?php echo json_encode(array_values($cities_data)); ?>;
    var citiesLabels = <?php echo json_encode(array_keys($cities_data)); ?>;
    var citiesCtx = document.getElementById('cities-chart').getContext('2d');
    var citiesChart = new Chart(citiesCtx, {
        type: 'doughnut',
        data: {
            labels: citiesLabels,
            datasets: [{
                data: citiesData,
                backgroundColor: [
                    'blue',
                    'green',
                    'red',
                    'yellow',
                    'purple',
                    'orange',
                    'brown',
                    'grey',
                    'pink'
                ]
            }]
        }
    });
  </script>
</body>
</html>
