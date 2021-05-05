<?php
/**
 * Wraps tables in <div class="table-responsive"></div>.
 */

$docPath = isset($argv[1]) ? $argv[1] : 'doc';
$docPath = sprintf('%s/%s', getcwd(), $docPath);
$docPath = realpath($docPath);

$rdi = new RecursiveDirectoryIterator($docPath . '/html');
$rii = new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST);
$files = new RegexIterator($rii, '/\.html$/', RecursiveRegexIterator::GET_MATCH);

$process = function () use ($files) {
    $fileInfo = $files->getInnerIterator()->current();
    if (! $fileInfo->isFile()) {
        return true;
    }

    if ($fileInfo->getBasename('.html') === $fileInfo->getBasename()) {
        return true;
    }

    $file = $fileInfo->getRealPath();
    $html = file_get_contents($file);
    if (! preg_match('#<table[^>]*>.*?(<\/table>)#s', $html)) {
        return true;
    }
    $html = preg_replace(
        '#(<table[^>]*>.*?(?:<\/table>))#s',
        '<div class="table-responsive">$1</div>',
        $html
    );
    file_put_contents($file, $html);

    return true;
};

iterator_apply($files, $process);
