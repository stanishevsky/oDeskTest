<?php
// |, — and + symbols in Unicode
define('VBAR',  "\xE2\x94\x82");
define('HBAR',  "\xE2\x94\x84");
define('CROSS', "\xE2\x94\xbc");
define('PADDING', 2);

// Source data
$colors = array(
  'Name' => 1,
  'Color' => 2,
  'Element' => 3,
  'Likes' => 4,
  // We'll intentionally skip Weight color to demonstrate default behavior
  'Column With Very Long Header' => 5,
);
$data = array(
    array(
        'Name' => 'Trixie',
        'Color' => 'LightGoldenRodYellow ',
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
        'Weight' => 'UltraLight',
        'Column With Very Long Header' => 'Test'
    ),
);


/**
 * Returns ANSI escape-sequence for setting color to the column
 * @param null $column  Name of column
 * @param null $bold    Is table header?
 * @return string
 */
function color ($column = null, $bold = null) {
    global $colors;
    return "\x1b[" . (isset($colors[$column])?$colors[$column]+30:0) . ($bold?";1":"") . "m";
}


// Calculate column widths

array_walk($data,function(&$item) use (&$cols){
      array_walk($item, function(&$item, $key) use (&$cols) {
            $cols[$key] = max( isset($cols[$key])? $cols[$key] : strlen($key)+ PADDING * 2, strlen($item) + PADDING * 2);
        });
  });

// Building row separator string
$rowSeparator = CROSS . join(CROSS, array_map(function($item){return str_repeat(HBAR, $item);}, $cols)) . CROSS . PHP_EOL;

// Building vprintf format for row printing
$format = VBAR . join(VBAR, array_map(function($col) use ($cols) {
          return color($col) . "%-" . $cols[$col] . "s" . color();
      },array_keys($cols))) . VBAR . PHP_EOL;



echo $rowSeparator;

//  Table header
echo    VBAR .
        join(VBAR, array_map(
            function($item) use($cols){
                $spaces = $cols[$item] - strlen($item);
                return str_repeat(' ', floor($spaces/2)) . color($item, true) . $item . color() . str_repeat(' ', ceil($spaces/2));
            },array_keys($cols))) .
        VBAR . PHP_EOL;

echo $rowSeparator;

// Table body
echo join ('', array_map(function($row) use ($format, $cols) {
        return vsprintf($format, array_map(function($item) use ($row){
                return (isset($row[$item])? str_repeat(" ", PADDING) .$row[$item]:"");
            }, array_keys($cols)));
    }, $data));

echo $rowSeparator;