<?php
declare(strict_types=1);

namespace Z3\T3build\Utility;

class ArrayUtility
{

    /**
     * @param array $array1
     * @param array $array2
     * @return array
     */
    public static function arrayMergeRecursive(array $array1, array $array2) : array
    {
        foreach ($array2 as $key => $value) {
            if ($key === 'condition') {
                continue;
            }
            $keyIsNew = array_key_exists($key, $array1) === false;
            $bothArray = $keyIsNew === false && is_array($array2[$key]) && is_array($array1[$key]);
            $bothValue = $keyIsNew === false && is_array($array2[$key]) === false && is_array($array1[$key]) === false;

            if ($bothArray) {
                $array1[$key] = self::arrayMergeRecursive($array1[$key], $array2[$key]);
            }

            if ($keyIsNew || $bothValue) {
                $array1[$key] = $array2[$key];
            }
        }

        return $array1;
    }

    /**
     * @param string $path
     * @param array $array1
     * @param array|string $value
     * @return array
     */
    public static function addOrOverrideToArray(string $path, array $array1, $value) : array
    {
        if (strlen($path) === 0) {
            return $value;
        }

        $pathArray = explode('/', $path);
        $currentKey = $pathArray[0];

        if (!array_key_exists($currentKey, $array1)) {
            $array1[$currentKey] = [];
        }

        if (count($pathArray) === 1) {
            $array1[$currentKey] = $value;
            return $array1;
        }

        $newPath = implode('/', array_slice($pathArray, 1));
        $array1[$currentKey] = self::addOrOverrideToArray($newPath, $array1[$currentKey], $value);
        return $array1;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @param array $tags
     * @return array
     */
    public static function arrayMergeRecursiveCondition(array $array1, array $array2, string $tag) : array
    {
        if (array_key_exists('condition', $array2) === false) {
            return $array1;
        }
        if (array_key_exists('config', $array2['condition']) === false) {
            return $array1;
        }
        if (array_key_exists('tags', $array2['condition']) === false || is_array($array2['condition']['tags']) === false) {
            return $array1;
        }
        if (in_array($tag, $array2['condition']['tags'], true)) {
            $array1 = self::arrayMergeRecursive($array1, $array2['condition']['config']);
        }
        return $array1;
    }
}
