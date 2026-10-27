<?php
$dir = 'app/Controllers/Admin';
$calls = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (preg_match_all("/service\('([^']+)'/", $content, $matches)) {
            foreach ($matches[1] as $m) {
                $calls[$m] = ($calls[$m] ?? 0) + 1;
            }
        }
    }
}
arsort($calls);
echo "Chamadas service() nos controllers do admin:\n";
foreach ($calls as $name => $count) {
    echo "  $name: $count\n";
}
