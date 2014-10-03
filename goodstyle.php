<?php

require_once __DIR__ .'/vendor/autoload.php';

use oDeskTest\AnsiTable;

$data = array(
  array(
    'Name' => 'Trixie',
    'Color' => 'Green',
    'Element' => 'Earth',
    'Likes' => 'Flowers'
  ),
  array(
    'Name' => 'Tinkerbell',
    'Element' => 'Air',
    'Likes' => 'Singning and Singing',
    'Color' => 'Blue'
  ),

  array(), // Test for empty rows
  array(
    'Element' => 'Water',
    'Likes' => 'Dancing',
    'Name' => 'Blum',
    'Color' => 'Pink'
  ),
  array(
    'Element' => 'Water',
    'Likes' => 'Dancing',
    'Name' => 'Blum',
    'Weight' => 'Heavy'
  ),
  array(
    'Element' => 'Water',
    'Weight' => 'Light',
    'Column With Very Long Header' => 'Test'
  ),
);

$colors = array(
  'Name' => 1,
  'Color' => 2,
  'Element' => 3,
  'Likes' => 4,
    // We'll intentionally skip Weight color to demonstrate default behavior
  'Column With Very Long Header' => 5,
);


$table = new AnsiTable();

$table->setColors($colors)->printTable($data);
