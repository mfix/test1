<?php

function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT, $encoding = 'UTF-8')
{
    $input_length = mb_strlen($input, $encoding);
    $pad_string_length = mb_strlen($pad_string, $encoding);

    if ($pad_length <= 0 || ($pad_length - $input_length) <= 0) {
        return $input;
    }

    $num_pad_chars = $pad_length - $input_length;

    switch ($pad_type) {
        case STR_PAD_RIGHT:
            $left_pad = 0;
            $right_pad = $num_pad_chars;
            break;

        case STR_PAD_LEFT:
            $left_pad = $num_pad_chars;
            $right_pad = 0;
            break;

        case STR_PAD_BOTH:
            $left_pad = floor($num_pad_chars / 2);
            $right_pad = $num_pad_chars - $left_pad;
            break;
    }

    $result = '';
    for ($i = 0; $i < $left_pad; ++$i) {
        $result .= mb_substr($pad_string, $i % $pad_string_length, 1, $encoding);
    }
    $result .= $input;
    for ($i = 0; $i < $right_pad; ++$i) {
        $result .= mb_substr($pad_string, $i % $pad_string_length, 1, $encoding);
    }

    return $result;
}

class AsciiTable
{
    const VERTICAL_CHAR = '|';
    const HORIZONTAL_CHAR = '-';
    const CROSSING_CHAR = '+';
    const SPACING_CHAR = ' ';

    protected $data = array();
    protected $lengths;
    protected $headerData = array();

    /**
     * @param $data Data to display as an ascii table
     */
    public function __construct($data)
    {
        $this->data = $this->sanitize($data);
        $this->createHeaderData();
    }

    /**
     * Sanitizes data for those mentioned special meaning bytes
     * Removing non-printable characters
     *
     * @param $data array
     * @return array
     */
    protected function sanitize($data)
    {
        $sanitizedData = array();
        foreach ($data AS $row) {
            $newRow = array();
            foreach ($row AS $k => $v) {
                $newRow[preg_replace('/\p{Cc}+/u', '', $k)] = preg_replace('/\p{Cc}+/u', '', $v);
            }
            $sanitizedData[] = $newRow;
        }

        return $sanitizedData;
    }

    /**
     * Prints to the output the ascii table.
     *
     * @return void
     */
    public function printAsciiTable()
    {
        $this->countDataLength(); /* initialize column width */

        $this->printLine();
        $this->printData($this->headerData);
        $this->printLine();

        foreach ($this->data AS $data) {
            $this->printData($data);
        }
        $this->printLine();
    }

    /**
     * Prints line like +------+----+------+
     */
    protected function printLine()
    {
        foreach ($this->lengths AS $length) {
            echo str_pad(
                AsciiTable::CROSSING_CHAR,
                $length+1,
                AsciiTable::HORIZONTAL_CHAR,
                STR_PAD_RIGHT
            );
        }
        echo AsciiTable::CROSSING_CHAR . PHP_EOL;

    }

    /*
     * Extracts data from first data row to create a header line.
     *
     * @return void
     */
    protected function createHeaderData()
    {
        foreach (array_keys($this->data[0]) AS $key) {
            $this->headerData[$key] = $key;
        }
    }

    /**
     * Prints one line of data
     *
     * @param $data array
     * @return void
     */
    protected function printData($data)
    {
        foreach($this->lengths AS $index => $length) {
            echo AsciiTable::VERTICAL_CHAR . mb_str_pad(
                $data[$index],
                $length,
                AsciiTable::SPACING_CHAR,
                STR_PAD_BOTH
            );
        }

        echo AsciiTable::VERTICAL_CHAR . PHP_EOL;
    }

    /**
     * Finds max data length and adds 2 as a spacing from both sides
     *
     * @return void
     */
    protected function countDataLength()
    {
        $lengths = array();

        foreach ($this->headerData AS $key) {
            $this->lengths[$key] = mb_strlen($key, 'UTF-8') + 2;
        }

        foreach ($this->data AS $row) {
            foreach ($row AS $key => $column) {
                $length = mb_strlen($column, 'UTF-8') + 2;
                if ($length > $this->lengths[$key]) {
                    $this->lengths[$key] = $length;
                }
            }
        }
    }
}



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
        'Likes' => 'Singning',
        'Color' => 'Blue'
    ),
    array(
        'Element' => 'Water',
        'Likes' => 'Dancing',
        'Name' => 'Blum',
        'Color' => 'Pink'
    ),
);

$table = new AsciiTable($data);
echo '<pre>';
$table->printAsciiTable();
echo '</pre>';