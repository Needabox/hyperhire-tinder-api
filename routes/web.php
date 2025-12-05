<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/documentation', function () {
    $documentation = config('l5-swagger.default');
    $documentations = config('l5-swagger.documentations');

    if (!isset($documentations[$documentation])) {
        abort(404, 'Documentation not found');
    }

    $docConfig = $documentations[$documentation];
    $documentationTitle = $docConfig['api']['title'] ?? 'L5 Swagger UI';

    $formatToUseForDocs = $docConfig['paths']['format_to_use_for_docs'] ?? 'json';
    $docsJson = $docConfig['paths']['docs_json'] ?? 'api-docs.json';
    $docsYaml = $docConfig['paths']['docs_yaml'] ?? 'api-docs.yaml';

    $docsFile = $formatToUseForDocs === 'yaml' ? $docsYaml : $docsJson;
    $docsRoute = config('l5-swagger.defaults.routes.docs', 'docs');

    $useAbsolutePath = $docConfig['paths']['use_absolute_path'] ?? config('l5-swagger.defaults.paths.use_absolute_path', true);

    $docsUrl = route('l5-swagger.' . $documentation . '.docs', [], $useAbsolutePath);

    // Pastikan URL menggunakan HTTPS jika halaman dimuat via HTTPS (fallback)
    if (request()->isSecure() && str_starts_with($docsUrl, 'http://')) {
        $docsUrl = str_replace('http://', 'https://', $docsUrl);
    }

    $urlsToDocs = [
        $documentationTitle => $docsUrl
    ];

    $operationsSorter = config('l5-swagger.defaults.operations_sort');
    $configUrl = config('l5-swagger.defaults.additional_config_url');
    $validatorUrl = config('l5-swagger.defaults.validator_url');

    return view('l5-swagger::index', compact(
        'documentation',
        'documentationTitle',
        'urlsToDocs',
        'useAbsolutePath',
        'operationsSorter',
        'configUrl',
        'validatorUrl'
    ));
})->withoutMiddleware(['web']);
