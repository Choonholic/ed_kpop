<?php
    function getDirectorySize($dir) {
        $file_list = getDirectoryContents($dir);
        $dir_count = 0;
        $file_count = 0;
        $file_size = 0;

        foreach ($file_list as $file) {
            switch ($file->type) {
                case 0:
                    $file_count++;
                    $file_size += $file->size;
                    break;
                case 1:
                    $dir_count++;
                    break;
            }
        }

        return $file_size;
    }

    function getDirectoryContents($dir, &$results = array()) {
        $files = scandir($dir);

        foreach ($files as $key => $value) {
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);

            if (!is_dir($path)) {
                $file_item = new stdClass();
                $file_item->type = 0;
                $file_item->path = $path;
                $file_item->size = filesize($path);
                $results[] = $file_item;
                unset($file_item);
            } else if ($value != "." && $value != "..") {
                getDirectoryContents($path, $results);

                $file_item = new stdClass();
                $file_item->type = 1;
                $file_item->path = $path;
                $file_item->size = filesize($path);
                $results[] = $file_item;
                unset($file_item);
            }
        }

        return $results;
    }

    function createDirectory($dir) {
        if (!file_exists($dir)) {
            mkdir($dir);
        }
    }

    function moveObject($oldObject, $newObject) {
        if (file_exists($oldObject)) {
            rename($oldObject, $newObject);
        }
    }

    function removeFile($file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    function removeDirectory($dir) {
        if (is_dir($dir)) {
            if (substr($dir, strlen($dir) - 1, 1) != DIRECTORY_SEPARATOR) {
                $dir .= DIRECTORY_SEPARATOR;
            }

            $files = glob($dir.'*', GLOB_MARK);

            foreach ($files as $file) {
                if (is_dir($file)) {
                    removeDirectory($file);
                } else {
                    unlink($file);
                }
            }

            rmdir($dir);
        }
    }
?>
