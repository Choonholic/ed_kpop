<?php
    include_once 'setting_info.php';
    include_once 'setting_func.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'db_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'file_info.php';
    include_once $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'utils'.DIRECTORY_SEPARATOR.'const_info.php';

    echo '<table class="site_settings_table">'.PHP_EOL;
    echo '<thead>'.PHP_EOL;
    echo '<tr class="site_settings_header">'.PHP_EOL;
    echo '<th class="property">Property</th>'.PHP_EOL;
    echo '<th class="content">Content</th>'.PHP_EOL;
    echo '</tr>'.PHP_EOL;
    echo '</thead>'.PHP_EOL;
    echo '<tbody>'.PHP_EOL;

    $og_settings = loadSettings($fileinfo['settings'].DIRECTORY_SEPARATOR.$fileinfo['open_graph'].$fileinfo['settings_ext']);
    $og_property = array("og:title", "og:url", "og:image", "og:type", "og:description", "og:locale");
    $count = count($og_property);

    $head_tag = loadSetting($fileinfo['settings'].DIRECTORY_SEPARATOR.$fileinfo['head_tag'].$fileinfo['settings_ext']);

    if ($og_settings === FALSE) {
        $og_settings = array();
        $og_settings[] = "ED Training Studio";
        $og_settings[] = "https://edkpop.co/";
        $og_settings[] = "";
        $og_settings[] = "Website";
        $og_settings[] = "Experience The Real KPOP Idol Curriculum​ Anywhere, at Anytime";
        $og_settings[] = "en-US";

        saveSettings($fileinfo['settings'].DIRECTORY_SEPARATOR.$fileinfo['open_graph'].$fileinfo['settings_ext'], $og_settings);
    }

    for ($i = 0; $i < $count; $i++) {
        if (($i % 2) == 0) {
            echo '<tr class="site_settings_even">'.PHP_EOL;
        } else {
            echo '<tr class="site_settings_odd">'.PHP_EOL;
        }

        echo '<td class="property">'.$og_property[$i].'</td>'.PHP_EOL;
        echo '<td class="content">'.mb_strimwidth(htmlentities($og_settings[$i]), 0, 100, "…", "utf-8").'</td>'.PHP_EOL;
        echo '</tr>'.PHP_EOL;
    }

    unset($og_settings);

    if ($head_tag === FALSE) {
        $head_tag = "";
    }

    if (($count % 2) == 0) {
        echo '<tr class="site_settings_even">'.PHP_EOL;
    } else {
        echo '<tr class="site_settings_odd">'.PHP_EOL;
    }

    echo '<td class="property">&lt;head&gt; Tag</td>'.PHP_EOL;
    echo '<td class="content">'.htmlentities($head_tag).'</td>'.PHP_EOL;
    echo '</tr>'.PHP_EOL;
    echo '</tbody>'.PHP_EOL;
    echo '</table>'.PHP_EOL;
?>
