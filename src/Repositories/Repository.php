<?php
namespace Caffeinated\Modules\Repositories;

use Caffeinated\Modules\Contracts\RepositoryInterface;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @var string $path Path to the defined modules directory
     */
    protected $path;

    /**
     * Constructor method.
     *
     * @param \Illuminate\Config\Repository      $config
     * @param \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Config $config, Filesystem $files)
    {
        $this->config = $config;
        $this->files  = $files;
    }

    /**
	 * Get all module basenames
	 *
	 * @return array
	 */
	protected function getAllBasenames()
	{
		$path = $this->getPath();

        try {
            $collection = collect($this->files->directories($path));

            $basenames  = $collection->map(function($item, $key) {
                return basename($item);
            });

    		return $basenames;
        } catch (\InvalidArgumentException $e) {
            return collect(array());
        }
	}

    /**
	 * Get modules path.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path ?: $this->config->get('modules.path');
	}

    /**
	 * Set modules path in "RunTime" mode.
	 *
	 * @param  string $path
	 * @return object $this
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

    /**
	 * Get path for the specified module.
	 *
	 * @param  string $slug
	 * @return string
	 */
	public function getModulePath($slug)
	{
		$module = studly_case($slug);

		return $this->getPath()."/{$module}/";
	}

    /**
     * Get path of module manifest file.
     *
     * @param  string $module
     * @return string
     */
    protected function getManifestPath($slug)
    {
        return $this->getModulePath($slug).'module.json';
    }

    /**
     * Get modules namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return rtrim($this->config->get('modules.namespace'), '/\\');
    }
}
