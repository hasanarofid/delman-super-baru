<?php
$content = file_get_contents('resources/views/adminNew/index.blade.php');

$phpSnippet = "@section('content')\n" .
              "@php\n" .
              "\$jenjangOptions = ['SMA', 'SMK', 'SKh'];\n" .
              "if (isset(\$akses_jenjang) && !empty(\$akses_jenjang) && !in_array('All', \$akses_jenjang)) {\n" .
              "    \$jenjangOptions = \$akses_jenjang;\n" .
              "}\n" .
              "@endphp";

$content = str_replace("@section('content')", $phpSnippet, $content);

$replacement = function($matches) {
    $id = $matches[1];
    $name = $matches[2];
    $class = $matches[3];
    $isGlobal = (strpos($id, 'global') !== false);
    
    $options = "                                        @if(empty(\$akses_jenjang) || in_array('All', \$akses_jenjang) || count(\$akses_jenjang) > 1)\n";
    if ($isGlobal) {
        $options .= "                                            <option value=\"all\">Semua Jenjang</option>\n";
    } else {
        $options .= "                                            <option value=\"all\">All</option>\n";
    }
    $options .= "                                        @endif\n" .
                "                                        @foreach(\$jenjangOptions as \$j)\n" .
                "                                            <option value=\"{{ \$j }}\">{{ \$j }}</option>\n" .
                "                                        @endforeach\n";

    return "<select id=\"$id\"" . ($name ? " name=\"$name\"" : "") . " class=\"$class\">\n" . $options . "                                    </select>";
};

$content = preg_replace_callback('/<select id="([^"]+)"(?: name="([^"]+)")? class="([^"]+)">(.*?)<\/select>/s', function($matches) use ($replacement) {
    if (strpos($matches[0], 'jenjang') !== false || strpos($matches[0], 'Jenjang') !== false) {
        return $replacement($matches);
    }
    return $matches[0];
}, $content);

file_put_contents('resources/views/adminNew/index.blade.php', $content);
echo "Updated resources/views/adminNew/index.blade.php\n";
