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
            $vals = preg_split("/\.|=/", $line);

            $this->insert($tree, $vals[0]);
            $ref = &$tree[$vals[0]];

            // Regular case. Like db.connection.type = remote
            if (count($vals) > 2) {
                for ($i = 1; $i < count($vals) - 2; $i++) {
                    $this->insert($ref, $vals[$i]);
                    $ref = &$ref[$vals[$i]];
                }
                $this->insert($ref, $vals[count($vals) - 2], $vals[count($vals) - 1]);
            }
            // One level nesting case. Like password = 123
            else {
                $this->insert($tree, $vals[0], $vals[1]);
            }

            unset($ref);
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

    private function insert(&$where, $key, $value = null)
    {
        if (!empty($where)) {
            if (is_string($where)) {
                $where = [
                    0 => $where,
                    $key => $value
                ];
            }
            if (is_array($where) && $value != NULL) {
                $where[$key] = $value;
            }

            return;
        }

        $where[$key] = $value;
    }
}
