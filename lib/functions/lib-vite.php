<?php

class WPVite {
	public string $distUri;
	public string $distPath;
	public string $wpEnqueueId;
	public string $server;
	public string $entryPoint;
	public array $jsDeps = [];
	private array $moduleHandles = [];
	private ?array $manifest = null;
	public static ?WPVite $instance = null;

	public function __construct($isChild) {
		self::$instance = $this;
		$dir = $isChild ? get_stylesheet_directory() : get_template_directory();
		$dirUri = $isChild ? get_stylesheet_directory_uri() : get_template_directory_uri();
		$env = parse_ini_file($dir . '/.env');
		$this->distUri = $dirUri . '/' . $env['VITE_OUTPUT_DIR'];
		$this->distPath = $dir . '/' . $env['VITE_OUTPUT_DIR'];
		$this->wpEnqueueId = $env['WP_ENQUEUE_ID'];
		$this->server = $env['VITE_PROTOCOL'] . '://' . $env['VITE_HOST'] . ':' . $env['VITE_PORT'];
		$this->entryPoint = $env['VITE_ENTRY_POINT'];
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
	 * Whether the Vite dev server is reachable.
	 *
	 * Only checked in `local`/`development` environments so production never
	 * makes the HTTP request. The result is memoised for the request.
	 *
	 * @return bool
	 */
	public function isDevServerRunning(): bool {
		static $running = null;

		if ($running !== null) {
			return $running;
		}

		if (!in_array(wp_get_environment_type(), ['local', 'development'], true)) {
			return $running = false;
		}

		$ch = curl_init($this->server . '/' . $this->entryPoint);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 300);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
		// mkcert's local CA is usually not in PHP's CA bundle.
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return $running = ($httpcode === 200);
	}

	/**
	 * Reads Vite's manifest file once per request.
	 *
	 * @return array
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
	 * Retrieves the CSS and JavaScript files for a manifest entry.
	 *
	 * @param string|null $entry Manifest key, defaults to the main entry point.
	 *
	 * @return array An associative array with 'css' and 'js' file lists.
	 */
	public function getProductionAssets(?string $entry = null) {
		$entry = $entry ?? $this->entryPoint;
		$manifest = $this->getManifest();
		$filelist = [
			'css' => [],
			'js' => [],
		];

		if (isset($manifest[$entry])) {
			$files = $manifest[$entry];
			if (isset($files['css'])) {
				$filelist['css'] = $files['css'];
			}
			if (isset($files['file'])) {
				$filelist[str_ends_with($files['file'], '.css') ? 'css' : 'js'][] = $files['file'];
			}
		}
		return $filelist;
	}

	/**
	 * Load the entry point from the Vite dev server.
	 *
	 * @return void
	 */
	public function viteDevAssets() {
		$src = $this->server . '/' . $this->entryPoint;
		add_action('wp_head', function () use ($src) {
			echo '<script id="vite" type="module" crossorigin src="' . esc_url($src) . '"></script>';
		});
	}

	/**
	 * Enqueue Vite-built CSS and JavaScript assets.
	 *
	 * @return void
	 */
	public function viteBuiltAssets() {
		$filelist = $this->getProductionAssets();
		$i = 0;
		foreach ($filelist['css'] as $file) {
			$i++;
			wp_enqueue_style($this->wpEnqueueId . '-style-' . $i, $this->distUri . '/' . $file, [], null);
		}
		$i = 0;
		foreach ($filelist['js'] as $file) {
			$i++;
			$handle = $this->wpEnqueueId . '-script-' . $i;
			$this->moduleHandles[] = $handle;
			wp_enqueue_script($handle, $this->distUri . '/' . $file, $this->jsDeps, null, true);
		}
	}
}
