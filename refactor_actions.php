<?php
$dir = __DIR__ . '/resources/views/cook/';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.php') !== false) {
        $path = $file->getPathname();
        
        // Skip the partial itself
        if (strpos($path, 'partials/quick-actions.blade.php') !== false) {
            continue;
        }

        $content = file_get_contents($path);
        
        // Find the start of the block
        $startPos = strpos($content, '<div class="bg-white rounded-2xl shadow-xl p-6 relative overflow-hidden">');
        if ($startPos === false) {
            continue;
        }

        // We know it ends with "Configuración \n </a> \n </div> \n </div>"
        // Let's use a regex to capture from start Pos to the closing </div> of that block.
        // Or simply find "Configuración", then the next "</a>", then the next "</div>", then the next "</div>".
        
        $configPos = strpos($content, 'Configuración', $startPos);
        if ($configPos === false) continue;
        
        $endPos = strpos($content, '</div>', $configPos);
        $endPos = strpos($content, '</div>', $endPos + 6);
        $endPos = $endPos + 6; // Include the final </div>
        
        $originalBlock = substr($content, $startPos, $endPos - $startPos);
        
        $replacement = "@include('cook.partials.quick-actions')";
        $newContent = str_replace($originalBlock, $replacement, $content);
        
        file_put_contents($path, $newContent);
        echo "Refactored: $path\n";
    }
}
echo "Done.\n";
