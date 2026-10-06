<?php

/**
 * Loads theme assets from the Vite dev server (when `npm run dev` is running)
 * or from the built manifest in `dist/`.
 */
class WPVite {
	public const HANDLE = 'wp-theme-timber-vite-acf';

	public string $distUri;
	public string $distPath;
	public string $entryPoint;
	public array $jsDeps = [];
	private string $hotFile;
	private array $moduleHandles = [];
	private ?array $manifest = null;
	public static ?WPVite $instance = null;

	public function __construct($isChild) {
		self::$instance = $this;
		$dir = $isChild ? get_stylesheet_directory() : get_template_directory();
		$dirUri = $isChild ? get_stylesheet_directory_uri() : get_template_directory_uri();

		// Same defaults as vite.config.js; `.env` is optional.
		$env = array_merge(
			['VITE_OUTPUT_DIR' => 'dist', 'VITE_ENTRY_POINT' => 'src/main.js'],
			file_exists($dir . '/.env') ? (parse_ini_file($dir . '/.env') ?: []) : []
		);

		$this->distUri = $dirUri . '/' . $env['VITE_OUTPUT_DIR'];
		$this->distPath = $dir . '/' . $env['VITE_OUTPUT_DIR'];
		$this->entryPoint = $env['VITE_ENTRY_POINT'];
		$this->hotFile = $dir . '/.vite-hot';
		$this->init();
	}

	public function init() {
		add_action('wp_enqueue_scripts', function () {
			if ($this->isDevServerRunning()) {
				$this->viteDevAssets();
			} else {
				$this->viteBuiltAssets();
			}
		});

		add_filter('script_loader_tag', function ($tag, $handle) {
			if (in_array($handle, $this->moduleHandles, true)) {
				$tag = str_replace('<script ', '<script type="module" ', $tag);
			}
			return $tag;
		}, 10, 2);
	}

	/**
	 * Whether `npm run dev` is running. Vite writes `.vite-hot` while the server
	 * is up; it is never trusted outside `local`/`development`.
	 */
	public function isDevServerRunning(): bool {
		return in_array(wp_get_environment_type(), ['local', 'development'], true)
			&& file_exists($this->hotFile);
	}

	/**
	 * Dev server origin, e.g. `https://localhost:3000`.
	 */
	public function devServerUrl(): string {
		return rtrim(trim((string) @file_get_contents($this->hotFile)), '/');
	}

	/**
	 * Reads Vite's manifest file once per request.
	 */
	public function getManifest(): array {
		if ($this->manifest === null) {
			$path = $this->distPath . '/.vite/manifest.json';
			$manifest = file_exists($path) ? json_decode(file_get_contents($path), true) : null;
			$this->manifest = is_array($manifest) ? $manifest : [];
		}
		return $this->manifest;
	}

	/**
	 * Built CSS and JavaScript files for a manifest entry.
	 *
	 * @param string|null $entry Manifest key, defaults to the main entry point.
	 *
	 * @return array{css: string[], js: string[]} Paths relative to the dist directory.
	 */
	public function getProductionAssets(?string $entry = null): array {
		$files = $this->getManifest()[$entry ?? $this->entryPoint] ?? [];
		$filelist = ['css' => $files['css'] ?? [], 'js' => []];

		if (isset($files['file'])) {
			$filelist[str_ends_with($files['file'], '.css') ? 'css' : 'js'][] = $files['file'];
		}
		return $filelist;
	}

	/**
	 * Enqueue a stylesheet entry (e.g. `src/admin.css`, `src/app.css`) from the
	 * dev server when it is running, otherwise from the build.
	 *
	 * @param string $source Source path relative to the theme root.
	 * @param string $handle Style handle.
	 * @param string|null $entry Manifest entry that builds this CSS, when it differs from $source.
	 */
	public function enqueueStyle(string $source, string $handle, ?string $entry = null): void {
		if ($this->isDevServerRunning()) {
			wp_enqueue_style($handle, $this->devServerUrl() . '/' . $source, [], null);
			return;
		}

		foreach ($this->getProductionAssets($entry ?? $source)['css'] as $i => $file) {
			wp_enqueue_style($handle . ($i ? '-' . $i : ''), $this->distUri . '/' . $file, [], null);
		}
	}

	/**
	 * Load the entry point from the Vite dev server.
	 */
	public function viteDevAssets(): void {
		$handle = self::HANDLE . '-dev';
		$this->moduleHandles[] = $handle;
		wp_enqueue_script($handle, $this->devServerUrl() . '/' . $this->entryPoint, $this->jsDeps, null, true);
	}

	/**
	 * Enqueue Vite-built CSS and JavaScript assets.
	 */
	public function viteBuiltAssets(): void {
		$filelist = $this->getProductionAssets();

		foreach ($filelist['css'] as $i => $file) {
			wp_enqueue_style(self::HANDLE . '-style-' . ($i + 1), $this->distUri . '/' . $file, [], null);
		}

		foreach ($filelist['js'] as $i => $file) {
			$handle = self::HANDLE . '-script-' . ($i + 1);
			$this->moduleHandles[] = $handle;
			wp_enqueue_script($handle, $this->distUri . '/' . $file, $this->jsDeps, null, true);
		}
	}
}
