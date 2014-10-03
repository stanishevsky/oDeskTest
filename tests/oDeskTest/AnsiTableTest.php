<?php
/**
 * Created by PhpStorm.
 * User: constantine
 * Date: 03.10.14
 * Time: 4:11
 */

use oDeskTest\AnsiTable;
class AnsiTableTest extends PHPUnit_Framework_TestCase {
    /**
     * @var AnsiTable
     */
    protected $table;


    public function testSetColors()
    {
        $this->table->setColors(null);
        $this->assertEquals(null, $this->table->getColors());

        $this->table->setColors( self::colors() );
        $this->assertEquals(self::colors(), $this->table->getColors());
    }

    public function testGetColumnWidths() {
        $this->assertEquals(
          [
              'Name' => 14,
              'Color' => 25,
              'Element' => 11,
              'Likes' => 24,
              'Weight' => 14,
              'Column With Very Long Header' => 32,
          ],
          $this->table->calculateColumnWidths( $this->data() )
        );
    }

    public function testRowSeparator() {
        $this->table->calculateColumnWidths(self::data());
        $this->assertEquals(
          "┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼" . PHP_EOL,
          $this->table->buildRowSeparator()
        );
    }


    public function testGetRowFormat() {
        $this->table->calculateColumnWidths(self::data());
        $this->assertEquals(
          "│\x1b[31m%-14s\x1b[0m│\x1b[32m%-25s\x1b[0m│\x1b[33m%-11s\x1b[0m│\x1b[34m%-24s\x1b[0m│\x1b[0m%-14s\x1b[0m│\x1b[35m%-32s\x1b[0m│" . PHP_EOL,
          $this->table->getRowFormat()
        );
    }

    public function testBuildHeader() {
        $this->table->calculateColumnWidths(self::data());
        $this->assertEquals(
          "│     \x1b[31;1mName\x1b[0m     │          \x1b[32;1mColor\x1b[0m          │  \x1b[33;1mElement\x1b[0m  │         \x1b[34;1mLikes\x1b[0m          │    \x1b[0;1mWeight\x1b[0m    │  \x1b[35;1mColumn With Very Long Header\x1b[0m  │" . PHP_EOL,
          $this->table->buildHeader()
        );
    }


    public function testBuildBody() {
        $this->table->calculateColumnWidths($this->data());
        $body = <<<BODY
│\x1b[31m  Trixie      \x1b[0m│\x1b[32m  LightGoldenRodYellow   \x1b[0m│\x1b[33m  Earth    \x1b[0m│\x1b[34m  Flowers               \x1b[0m│\x1b[0m              \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m  Tinkerbell  \x1b[0m│\x1b[32m  Blue                   \x1b[0m│\x1b[33m  Air      \x1b[0m│\x1b[34m  Singning and Singing  \x1b[0m│\x1b[0m              \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m              \x1b[0m│\x1b[32m                         \x1b[0m│\x1b[33m           \x1b[0m│\x1b[34m                        \x1b[0m│\x1b[0m              \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m  Blum        \x1b[0m│\x1b[32m  Pink                   \x1b[0m│\x1b[33m  Water    \x1b[0m│\x1b[34m  Dancing               \x1b[0m│\x1b[0m              \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m  Blum        \x1b[0m│\x1b[32m                         \x1b[0m│\x1b[33m  Water    \x1b[0m│\x1b[34m  Dancing               \x1b[0m│\x1b[0m  Heavy       \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m              \x1b[0m│\x1b[32m                         \x1b[0m│\x1b[33m  Water    \x1b[0m│\x1b[34m                        \x1b[0m│\x1b[0m  UltraLight  \x1b[0m│\x1b[35m  Test                          \x1b[0m│

BODY;
        $this->assertEquals( $body, $this->table->buildBody($this->data()));
    }

    public function testPrintTable() {
        $output = <<<OUTPUT
┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼
│     \x1b[31;1mName\x1b[0m     │          \x1b[32;1mColor\x1b[0m          │  \x1b[33;1mElement\x1b[0m  │         \x1b[34;1mLikes\x1b[0m          │    \x1b[0;1mWeight\x1b[0m    │  \x1b[35;1mColumn With Very Long Header\x1b[0m  │
┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼
│\x1b[31m  Trixie      \x1b[0m│\x1b[32m  LightGoldenRodYellow   \x1b[0m│\x1b[33m  Earth    \x1b[0m│\x1b[34m  Flowers               \x1b[0m│\x1b[0m              \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m  Tinkerbell  \x1b[0m│\x1b[32m  Blue                   \x1b[0m│\x1b[33m  Air      \x1b[0m│\x1b[34m  Singning and Singing  \x1b[0m│\x1b[0m              \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m              \x1b[0m│\x1b[32m                         \x1b[0m│\x1b[33m           \x1b[0m│\x1b[34m                        \x1b[0m│\x1b[0m              \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m  Blum        \x1b[0m│\x1b[32m  Pink                   \x1b[0m│\x1b[33m  Water    \x1b[0m│\x1b[34m  Dancing               \x1b[0m│\x1b[0m              \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m  Blum        \x1b[0m│\x1b[32m                         \x1b[0m│\x1b[33m  Water    \x1b[0m│\x1b[34m  Dancing               \x1b[0m│\x1b[0m  Heavy       \x1b[0m│\x1b[35m                                \x1b[0m│
│\x1b[31m              \x1b[0m│\x1b[32m                         \x1b[0m│\x1b[33m  Water    \x1b[0m│\x1b[34m                        \x1b[0m│\x1b[0m  UltraLight  \x1b[0m│\x1b[35m  Test                          \x1b[0m│
┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┄┼

OUTPUT;

        $this->table->printTable($this->data());
        $this->expectOutputString($output);
    }
    protected static function colors() {
        $colors = array(
          'Name' => 1,
          'Color' => 2,
          'Element' => 3,
          'Likes' => 4,
            // We'll intentionally skip Weight color to demonstrate default behavior
          'Column With Very Long Header' => 5,
        );

        return $colors;
    }

    protected static function data() {
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
        return $data;
    }

    protected function setUp() {
        $this->table = new AnsiTable( self::colors() );
        $this->assertEquals(self::colors(), $this->table->getColors());
    }
}
 