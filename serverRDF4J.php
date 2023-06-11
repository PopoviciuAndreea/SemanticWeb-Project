<?php
require 'vendor/autoload.php';
use EasyRdf\Sparql\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    array_shift($data);
}

$graf = new EasyRdf\Graph("http://samson_popoviciu.ro#grafNou");
$prefixe = new EasyRdf\RdfNamespace();
$prefixe->setDefault("http://samson_popoviciu.ro#");

foreach ($data as $inregistrare) {
    $title = $inregistrare->value[0]->value;
    $year = $inregistrare->value[1]->value;

    $movieUri = 'http://samson_popoviciu.ro/grafNou/' . urlencode($title);

    $graf->addResource($movieUri, 'rdf:type', 'schema:Movie');
    $graf->addLiteral($movieUri, 'rdfs:label', $title);
    $graf->addLiteral($movieUri, 'schema:aparutIn', $year);
}
$client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/grafexamen/statements");
$client->insert($graf, "http://samson_popoviciu.ro#grafNou");


$client = new EasyRdf\Sparql\Client("http://localhost:8080/rdf4j-server/repositories/grafexamen");

$graph = new EasyRdf\Graph();
$graph->load("http://localhost:8080/rdf4j-server/repositories/grafexamen/statements");

$data = array();
foreach ($graph->allOfType('schema:Movie') as $movie) {
    $title = $movie->getLiteral('rdfs:label');
    $year = $movie->getLiteral('schema:aparutIn');

    $data[] = array(
        'title' => $title,
        'year' => $year
    );
}

if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

$tableHTML = '<table border="1">';
$tableHTML .= '<thead><tr><th>Title</th><th>Year</th></tr></thead>';
$tableHTML .= '<tbody>';

foreach ($data as $row) {
    $title = $row['title'];
    $year = $row['year'];

    $tableHTML .= '<tr>';
    $tableHTML .= '<td>' . $title . '</td>';
    $tableHTML .= '<td>' . $year . '</td>';
    $tableHTML .= '</tr>';
}

$tableHTML .= '</tbody></table>';

echo $tableHTML;

$jsonLDArray = array();
foreach ($data as $row) {
    $title = $row['title'];
    $year = $row['year'];

    $jsonLDArray[] = array(
        '@type' => 'schema:Movie',
        'rdfs:label' => $title,
        'schema:aparutIn' => $year
    );
}

$jsonLD = json_encode($jsonLDArray);
echo '<script type="application/ld+json">' . $jsonLD . '</script>';

?>