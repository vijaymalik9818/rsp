<?php
// require_once ("lang.php");

function includeFileWithVariables($filePath, $variables = array(), $print = true)
{
    $output = NULL;
    if(file_exists($filePath)){
        // Extract the variables to a local namespace
        extract($variables);

        // Start output buffering
        ob_start();

        // Include the template file
        include $filePath;

        // End buffering and return its contents
        $output = ob_get_clean();
    }
    if ($print) {
        print $output;
    }
    return $output;
}

$isScssconverted = false;

require_once(base_path('resources/views/scssphp/scss.inc.php')); // Assuming your scss.inc.php is in the resources/views directory

use ScssPhp\ScssPhp\Compiler;

if($isScssconverted){
    $compiler = new Compiler();

    $compine_css = public_path('assets/css/app.min.css'); // Update path using public_path()
    $source_scss = public_path('assets/scss/config/creative/app.scss'); // Update path using public_path()

    $scssContents = file_get_contents($source_scss);

    $import_path = public_path('assets/scss/config/creative'); // Update path using public_path()
    $compiler->addImportPath($import_path);
    $target_css = $compine_css;

    $css = $compiler->compile($scssContents);

    if (!empty($css) && is_string($css)) {
        file_put_contents($target_css, $css);
    }
}
?>
<!doctype html>
<html lang="en" data-layout="twocolumn" data-sidebar="light" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">
