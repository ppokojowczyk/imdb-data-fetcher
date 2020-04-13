<?php

require('./ImdbDataFetcher/Fetcher.php');

use ImdbDataFetcher\Fetcher as ImdbDataFetcher;

$Test = new ImdbDataFetcher();
$Test->setMovieId("0111161"); // The Shawshank Redemption
$Test->process();
$Test->parseContent();

var_dump($Test->getJSON());
