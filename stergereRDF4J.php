<?php
require_once 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $title = $data->title;
}

$graphUri = 'http://localhost:8080/rdf4j-server/repositories/grafexamen/statements';

$client = new EasyRdf\Sparql\Client($graphUri);

EasyRdf\RdfNamespace::set('schema', 'http://schema.org/');

$query = '
    DELETE
    WHERE {
        ?movie rdf:type schema:Movie ;
        rdfs:label "' . $title . '" ;
        schema:aparutIn ?year .
    }';

try {
    $client->update($query);
    echo 'Data deleted successfully';
} catch (Exception $e) {
    echo 'Error deleting data: ' . $e->getMessage();
}

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

?>