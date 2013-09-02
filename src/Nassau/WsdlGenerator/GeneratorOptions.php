<?php

namespace Nassau\WsdlGenerator;

class GeneratorOptions
{
	/**
	 * @var string
	 */
	private $namespace;
	/**
	 * @var string
	 */
	private $targetDir;
	/**
	 * @var \ArrayObject
	 */
	private $skipMethods;

	public function __construct()
	{
		$this->skipMethods = new \ArrayObject;
	}

	/**
	 * @param \ArrayObject $skipMethods
	 */
	public function setSkipMethods(\ArrayObject $skipMethods)
	{
		$this->skipMethods = $skipMethods;
	}

	/**
	 * @return \ArrayObject
	 */
	public function getSkipMethods()
	{
		return $this->skipMethods;
	}


	/**
	 * @param string $namespace
	 */
	public function setNamespace($namespace)
	{
		$this->namespace = ltrim($namespace, '\\');
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @param string $targetDir
	 */
	public function setTargetDir($targetDir)
	{
		$this->targetDir = rtrim($targetDir, '/');
	}

	/**
	 * @return string
	 */
	public function getTargetDir()
	{
		$dir = str_replace($this->namespace, '\\', DIRECTORY_SEPARATOR);
		if ($dir === substr($this->targetDir, - strlen($dir)))
		{
			return substr($this->targetDir, 0, - strlen($dir) - 1);
		}
		return $this->targetDir;
	}

}