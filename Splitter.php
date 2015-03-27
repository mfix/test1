<?php
class Splitter
{
    private $numbers = array();

    /**
     * @param $numbers array of numbers to split
     */
    public function __construct($numbers)
    {
        sort($numbers);
        $this->numbers = $numbers;
    }

    /**
     * Split number into groups to receive the closest sums of elements in each group
     * @param $groupsCount int
     * @return array
     */
    public function splitToGroups($groupsCount)
    {
        $numbers = $this->numbers;
        $result = array();
        $groups = array();
        $count = count($numbers);

        for ($i = 0; $i < $groupsCount; $i++) {
            if (0 == $count) {
                return $result;
            }
            $number = array_pop($numbers);
            $result[$i] = $number;
            $groups[$i] = array($number);
            $count--;
        }


        while ($count) {
            $minKey = array_search(min($result), $result);
            $currentNumber = array_pop($numbers);
            $result[$minKey] += $currentNumber;
            $groups[$minKey][] = $currentNumber;
            $count--;
        }

        return $groups;
    }
}


$numbers = array(1,2,4,7,1,6,2,8);
$splitter = new Splitter($numbers);
var_dump($splitter->splitToGroups(3));
var_dump($splitter->splitToGroups(2));
var_dump($splitter->splitToGroups(4));