<?php
require __DIR__ . '/vendor/autoload.php';
use GuzzleHttp\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents("php://input"));
  array_shift($data);
}

$client = new GuzzleHttp\Client([
  'base_uri' => 'http://localhost:3000',
]);

$mutation = 'mutation {';
foreach ($data as $index => $movie) {
  $title = $movie->value[0]->value;
  $year = $movie->value[1]->value;
  $mutation .= "movie$index: createMovie(title: \"$title\", year: \"$year\") {id title year} ";
}
$mutation .= '}';

$response = $client->post('http://localhost:3000', [
  'json' => ['query' => $mutation],
  'headers' => ['Content-Type' => 'application/json']
]);

$data = json_decode($response->getBody(), true)['data'];

$query = '{allMovies {id title year}}';

$response = $client->post('http://localhost:3000', [
  'json' => ['query' => $query],
  'headers' => ['Content-Type' => 'application/json']
]);

$data = json_decode($response->getBody(), true)['data'];

echo '<table style="border-collapse: collapse; margin: 20px;">';
echo '<style>th, td {border: 1px solid black; padding: 10px;}</style>';

echo "<tr><th>Titlu</th><th>An</th></tr>";

$movies = $data['allMovies'];
foreach ($movies as $movies) {
  echo "<tr><td>" . $movies['title'] . "</td><td>" . $movies['year'] . "</td></tr>";
}
?>