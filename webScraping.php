<?php
$raspunsHTTP = file_get_contents("https://www.imdb.com/chart/moviemeter/?ref_=nv_mv_mpm");
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($raspunsHTTP);
$docInterogabil = new DOMXPath($doc);

$titluXPath = '//td[@class="titleColumn"]';

$primeleTrei = array_slice(iterator_to_array($docInterogabil->query($titluXPath)), 0, 3);

echo '<table style="border-collapse: collapse; margin: 20px;">';
echo '<style>th, td {border: 1px solid black; padding: 10px;}</style>';

echo "<tr><th>Titlu</th><th>An</th></tr>";

foreach ($primeleTrei as $tag) {
    $titluTag = $tag->getElementsByTagName('a')[0];

    $anTag = $tag->getElementsByTagName('span')[0];

    $titlu = trim($titluTag->nodeValue);
    $an = trim($anTag->nodeValue);

    echo "<tr><td>$titlu</td><td>$an</td></tr>";
}

echo "</table>";
?>