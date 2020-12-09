<?php

require __DIR__ . "/vendor/autoload.php";

use ubitcorp\Client;


$client = new Client("your_client_key", "your_client_secret");



// To Get Export List
$client->getExports(
    [
        'reference_id' => '65351-1', // String reference_id
        'code' => 'e30a16685d79dc59fc404669c' //String code
    ]
);

// Create Exam Url
$client->getExamUrl(
    [
        'reference_id' => 'Test23das', // String reference_id
        'code' => 'ecc943209d466f58682a773ff', // String code
        'personal_details' => [ // Array Personal Details
            'name' => 'Sergen',
            'surname' => 'Temel'
        ]
    ]
);


// Get Examinees
$client->getExaminees(
    [ // String reference_id | String code
        'reference_id' => 'Test23das'
    ],
    'ecc943209d466f58682a773ff' // export_code
);


// Create Exam

$client->createExam(
    'Test Exam 1',  //name
    'testReference', //reference_id
    [ //conf
        'lastLetter' => 'D',
        'language' => 'tr'
    ],
    [ //parts
        ['name' => 'Part1', 'reference_id' => 'clientTest1'],
        ['name' => 'Part2', 'reference_id' => 'clientTest2']
    ]
);
