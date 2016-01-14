<?php

namespace ConfigReader;

use ConfigReader\Exception\InvalidArgumentTypeException;

/**
 * Class Parser
 * @package ConfigReader
 */
class Parser
{
    private $string;

    /**
     * @param $string
     */
    public function __construct($string)
    {
        if (gettype($string) !== "string") {
            throw new InvalidArgumentTypeException(sprintf("Argument should be a type of string, %s given", gettype($string)));
        }

        $this->string = $string;
    }

    /**
     * @return array
     */
    public function parse()
    {
        $lines = $this->makeLines();
        return $this->buildTree($lines);
    }

    /**
     * Build tree-like array of configuration keys and values
     *
     * @param array $lines
     * @return array
     */
    private function buildTree(array $lines)
    {
        $tree = [];
        foreach ($lines as $line) {
            // Split by dot or equal sign
            $values = preg_split("/\.|=/", $line);

            $len = count($values);
            $arr = [$values[$len - 2] => $values[$len - 1]];

            for ($i = 3; $i <= count($values); $i++) {
                $arr = [$values[$len - $i] => $arr];
            }

            $tree = array_merge_recursive($tree, $arr);
        }

        return $tree;
    }

    /**
     * Making array of lines from configuration string
     * @return array
     */
    private function makeLines()
    {
        $this->string = trim($this->string);
        $lines = explode("\n", $this->string);

        $filtered = [];
        foreach ($lines as $line) {
            // If string is not empty
            if ($trimmed = trim($line)) {
                // Remove all spaces from string
                $filtered[] = str_replace(' ', '', $trimmed);
            }
        }

        return $filtered;
    }
}
