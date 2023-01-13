<?php
    function loadSettings($settingsPath) {
        $fileId = fopen($settingsPath, 'r');

        if ($fileId === FALSE) {
            return FALSE;
        }

        while (!feof($fileId)) {
            $settings[] = trim(fgets($fileId));
        }

        fclose($fileId);
        return $settings;
    }

    function saveSettings($settingsPath, $settings) {
        $fileId = fopen($settingsPath, 'w');

        foreach ($settings as $item) {
            $content = $item.PHP_EOL;

            fwrite($fileId, $content);
        }

        fclose($fileId);
    }

    function loadSetting($settingsPath) {
        $fileId = fopen($settingsPath, 'r');

        if ($fileId === FALSE) {
            return FALSE;
        }

        $setting = fread($fileId, filesize($settingsPath));

        fclose($fileId);
        return $setting;
    }

    function saveSetting($settingsPath, $setting) {
        $fileId = fopen($settingsPath, 'w');

        fwrite($fileId, $setting);
        fclose($fileId);
    }
?>
