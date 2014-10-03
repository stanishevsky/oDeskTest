<?php
/**
 * Created by PhpStorm.
 * User: constantine
 * Date: 03.10.14
 * Time: 3:51
 */

namespace oDeskTest;


class AnsiTable {

    /**
     *    |     —    and     +     symbols in Unicode
     */
    const TABLE_CROSS = "\xE2\x94\xbc";
    const TABLE_HBAR = "\xE2\x94\x84";
    const TABLE_VBAR = "\xE2\x94\x82";

    /**
     * Horizonal padding (distance between text and borders)
     */
    const PADDING = 2;


    /**
     * @var []
     */
    private $colors = null;


    /**
     * Row format or vprint
     * @var null String
     */
    private $rowFormat = null;

    private $columnWidths;


    /**
     * @param array $colors Color table, optional
     */
    public function __construct(array $colors = [])
    {
        if ($colors)
            $this->setColors($colors);
    }


    /**
     * Sets colors mapping to the column names
     *
     * @param array $colors Color table
     *
     * @return AnsiTable
     */
    public function setColors($colors)
    {
        $this->colors = $colors;
        return $this;
    }

    /**
     * Retrieves colors mapping (or null)
     *
     * @return mixed
     */
    public function getColors()
    {
        return $this->colors;
    }


    /**
     * Returns ANSI escape-sequence for setting color to the column using class property @colors,
     * or resets color if column name is null or doesn't exist in @data
     *
     * @param null $columnName  Name of column
     * @param null $bright    Is table header?
     *
     * @return string
     */
    private function getColorANSI ($columnName = null, $bright = null) {
        return
          "\x1b[" .
          ( isset($this->colors[$columnName] ) ? $this->colors[$columnName] + 30 : 0) . // Will output 31..37 for colors 1..7
          ( $bright ? ";1"  : "" ) . // additional parameter for brighten text used for table header
          "m";
    }


    /**
     * Calculates column widths needed for printing table
     *
     * @param array $data Associative array
     *
     * @return array
     */
    public function calculateColumnWidths($data)
    {
        $this->columnWidths  = [];

        // For each row of $data
        array_walk (
          $data,

          function (&$row) use (&$columnWidths) {
              // For each column of row
              array_walk (
                $row,

                function (&$item, $key) {
                    // Finding maximal length of cell or header + left/right paddings
                    $this->columnWidths[$key] = max (
                      // For the very first cell let's use $key length, for other cells use stored maximal value
                      isset($this->columnWidths[$key]) ? $this->columnWidths[$key] : strlen($key) + self::PADDING * 2,
                      // As a second argument always use cell's value length
                      strlen($item) + self::PADDING * 2
                    );
                }

              );
          }

        );
        return $this->columnWidths;
    }

    /**
     * Builds string with horizonal separator needed for op, bottom and header of the table
     *
     * @return string
     */
    public function buildRowSeparator()
    {
        $rowSeparator =
          self::TABLE_CROSS .
          join (
            self::TABLE_CROSS,
            array_map(
              function ($item) {
                  return str_repeat( self::TABLE_HBAR, $item);
              }, $this->columnWidths
            )
          ) .
          self::TABLE_CROSS . PHP_EOL;

        return $rowSeparator;
    }


    /**
     * Builds vprintf format for row printing
     * @return string
     */
    public function buildRowFormat() {
        return
          self::TABLE_VBAR .
          join(
            self::TABLE_VBAR,
            array_map(
              function ($column) {
                  return $this->getColorANSI($column) . "%-" . $this->columnWidths[$column] . "s" . $this->getColorANSI();
              },
              array_keys($this->columnWidths)
            )
          ) . self::TABLE_VBAR . PHP_EOL;
    }


    /**
     * Builds table header
     * @return string
     */
    public function buildHeader() {
        return
            self::TABLE_VBAR .
              join(self::TABLE_VBAR, array_map(
                  function($item) {
                      $spaces = $this->columnWidths[$item] - strlen($item);
                      return str_repeat(' ', floor($spaces/2)) . $this->getColorANSI($item, true) . $item . $this->getColorANSI() . str_repeat(' ', ceil($spaces/2));
                  },array_keys($this->columnWidths))) .
            self::TABLE_VBAR . PHP_EOL;
    }


    /**
     * rowFormat getter
     * @return string
     */
    public function getRowFormat() {
        if (!$this->rowFormat)
            $this->rowFormat = $this->buildRowFormat();
        return $this->rowFormat;
    }

    /**
     * Builds table body
     * @param array $data table data in associaive array
     * @return string
     */
    public function buildBody($data) {
        return
            join (
              '',
              array_map(
                  function($row) {
                      return vsprintf(
                          $this->getRowFormat(),
                          array_map(
                              function ($item) use ($row) {
                                  return (isset($row[$item])? str_repeat(" ", self::PADDING) .$row[$item]:"");
                              },
                              array_keys( $this->columnWidths )
                          )
                      );
                  },
              $data)
            );
    }

    /**
     * Generates ANSI table
     *
     * @param array $data
     *
     * @return AnsiTable
     */
    public function generateTable($data) {
        $this->calculateColumnWidths($data);
        $rowSeparator = $this->buildRowSeparator();
        return
            $rowSeparator
            . $this->buildHeader()
            . $rowSeparator
            . $this->buildBody($data)
            . $rowSeparator;
    }

    /**
     * Prints data passed in array
     *
     * @param array $data
     *
     * @return AnsiTable
     */
    public function printTable($data)
    {
        print $this->generateTable($data);
        return $this;
    }

}