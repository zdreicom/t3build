<?php
declare(strict_types=1);

namespace Z3\T3build\Utility;

class FileDirectoryUtility
{

    /**
     * @param string $path
     * @param string $extensionList
     * @param bool $fullPath
     * @param int $startDeep
     * @param int $endDeep
     * @param int $currentDeep
     * @return array
     */
    public static function getFilesInDir(string $path, string $extensionList = '', bool $fullPath = false, int $startDeep = 0, int $endDeep = 0, int $currentDeep = 0): array
    {
        $extensionListArray = explode(',', $extensionList);
        $files = [];
        if (is_dir($path)) {
            $fileNames = scandir($path);
            foreach ($fileNames as $fileName) {
                if ($fileName === '..' || $fileName === '.') {
                    continue;
                }
                if (is_dir($path . '/' . $fileName)) {
                    if ($currentDeep < $endDeep) {
                        $subFiles = self::getFilesInDir($path . '/' . $fileName, $extensionList, $fullPath, $startDeep, $endDeep, $currentDeep + 1);
                        $files = array_merge($files, $subFiles);
                    }
                } else {
                    if (strlen($extensionList) > 0 && in_array(pathinfo($fileName, PATHINFO_EXTENSION), $extensionListArray) === false) {
                        continue;
                    }

                    if ($fullPath) {
                        $files[] = $path . '/' . $fileName;
                    } else {
                        $files[] = $fileName;
                    }
                }
            }
        }
        return $files;
    }

    public static function getFilesInDirTypo3($path, $extensionList = '', $prependPath = false, $order = '', $excludePattern = '')
    {
        $excludePattern = (string)$excludePattern;
        $path = rtrim($path, '/');
        if (!@is_dir($path)) {
            return [];
        }

        $rawFileList = scandir($path);
        if ($rawFileList === false) {
            return 'error opening path: "' . $path . '"';
        }

        $pathPrefix = $path . '/';
        $extensionList = ',' . str_replace(' ', '', $extensionList) . ',';
        $files = [];
        foreach ($rawFileList as $entry) {
            $completePathToEntry = $pathPrefix . $entry;
            if (!@is_file($completePathToEntry)) {
                continue;
            }

            if (
                ($extensionList === ',,' || stripos($extensionList, ',' . pathinfo($entry, PATHINFO_EXTENSION) . ',') !== false)
                && ($excludePattern === '' || !preg_match(('/^' . $excludePattern . '$/'), $entry))
            ) {
                if ($order !== 'mtime') {
                    $files[] = $entry;
                } else {
                    // Store the value in the key so we can do a fast asort later.
                    $files[$entry] = filemtime($completePathToEntry);
                }
            }
        }

        $valueName = 'value';
        if ($order === 'mtime') {
            asort($files);
            $valueName = 'key';
        }

        $valuePathPrefix = $prependPath ? $pathPrefix : '';
        $foundFiles = [];
        foreach ($files as $key => $value) {
            // Don't change this ever - extensions may depend on the fact that the hash is an md5 of the path! (import/export extension)
            $foundFiles[md5($pathPrefix . ${$valueName})] = $valuePathPrefix . ${$valueName};
        }

        return $foundFiles;
    }

    /**
     * @param string $path
     * @param bool $fullPath
     * @param bool $recursive
     * @param int $recursiveDeep
     * @return array
     */
    public static function getDirectoriesInPath(string $path, bool $fullPath = false, int $startDeep = 0, int $endDeep = 0, int $currentDeep = 0): array
    {
        $directories = [];
        if (is_dir($path)) {
            $directoryNames = scandir($path);
            foreach ($directoryNames as $directoryName) {
                if (is_dir($path . '/' . $directoryName) && $directoryName !== '..' && $directoryName !== '.') {
                    if ($startDeep <= $currentDeep) {
                        if ($fullPath) {
                            $directories[] = $path . '/' . $directoryName;
                        } else {
                            $directories[] = $directoryName;
                        }
                    }
                    if ($currentDeep < $endDeep) {
                        $subDirectories = self::getDirectoriesInPath($path . '/' . $directoryName, $fullPath, $startDeep, $endDeep, $currentDeep + 1);
                        $directories = array_merge($directories, $subDirectories);
                    }
                }
            }
        }
        return $directories;
    }

    /**
     * @param string $packagePath
     * @return bool|mixed
     */
    public static function getComposerConfig(string $packagePath)
    {
        $composerJsonPath = $packagePath . '/composer.json';

        if (!is_file($composerJsonPath)) {
            return false;
        }

        $composerConfig = json_decode(file_get_contents($composerJsonPath));
        if ($composerConfig === null) {
            return false;
        }

        return $composerConfig;
    }
}
