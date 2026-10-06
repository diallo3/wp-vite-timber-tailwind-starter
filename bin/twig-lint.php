<?php
/**
 * Parses every Twig template and checks that static include/extends/embed/import
 * targets exist. Unknown filters/functions are stubbed, so this checks syntax
 * and paths only. Run with `npm run lint` (needs `composer install`).
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader([$root . '/templates', $root]);

// Keep in sync with the timber/loader/loader filter in lib/timber/lib-timber-filters.php.
$namespaces = [
	'Components' => 'templates/app/components/',
	'Content' => 'templates/app/components/content/',
	'Layouts' => 'templates/app/layouts/',
	'Globals' => 'templates/app/globals/',
	'Elements' => 'templates/app/elements/',
	'Pages' => 'templates/pages',
];
foreach ($namespaces as $namespace => $path) {
	$loader->addPath($root . '/' . $path, $namespace);
}

$twig = new \Twig\Environment($loader);
$twig->registerUndefinedFilterCallback(fn($name) => new \Twig\TwigFilter($name, fn() => null));
$twig->registerUndefinedFunctionCallback(fn($name) => new \Twig\TwigFunction($name, fn() => null));

$problems = 0;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/templates'));

foreach ($files as $file) {
	if ($file->getExtension() !== 'twig') {
		continue;
	}

	$relative = substr($file->getPathname(), strlen($root . '/templates/'));
	$source = file_get_contents($file->getPathname());

	try {
		$twig->parse($twig->tokenize(new \Twig\Source($source, $relative)));
	} catch (\Throwable $e) {
		$problems++;
		echo "SYNTAX  {$relative}: {$e->getMessage()}\n";
	}

	// Literal targets only; names built with `~` are resolved at runtime.
	preg_match_all('/\{%-?\s*(?:include|extends|embed|import|from)\s+[\'"]([^\'"]+)[\'"]\s*+(?!~)/', $source, $matches);
	foreach ($matches[1] as $target) {
		if (!$loader->exists($target)) {
			$problems++;
			echo "MISSING {$relative} -> {$target}\n";
		}
	}
}

echo $problems ? "twig: {$problems} problem(s)\n" : "twig: ok\n";
exit($problems ? 1 : 0);
