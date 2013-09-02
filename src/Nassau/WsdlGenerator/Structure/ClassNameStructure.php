<?php


namespace Nassau\WsdlGenerator\Structure;


class ClassNameStructure
{
	/**
	 * @var string
	 */
	private $namespace;

	/**
	 * @var string
	 */
	private $className;

	private $extractSuffixes = ['Response', 'Request'];

	function __construct($className, $baseNs = "")
	{
		$ns = explode("\\", $baseNs);

		foreach ($this->extractSuffixes as $suffix)
		{
			if ($suffix === substr($className, - strlen($suffix)))
			{
				$ns[] = $suffix;
			}
		}

		$this->namespace = implode("\\", $ns);
		$this->className = $className;
	}

	public function getClassName()
	{
		return $this->namespace ? $this->namespace . "\\" . $this->className : $this->className;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

}