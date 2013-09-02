<?php


namespace Nassau\WsdlGenerator\Structure;

use ArrayObject;

class ServiceStructure
{
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $wsdl;
	/**
	 * @var string
	 */
	private $doc;

	/**
	 * @var \ArrayObject|FunctionStructure[]
	 */
	private $functions;

	/**
	 * @var \ArrayObject|TypeStructure[]
	 */
	private $types;

	public function __construct(array $data)
	{
		$this->setName($data['name']);
		$this->wsdl = $data['wsdl'];
		$this->doc = $data['doc'];
		$this->functions = new ArrayObject;
		$this->types = new ArrayObject;
	}

	/**
	 * @param string $doc
	 */
	public function setDoc($doc)
	{
		$this->doc = $doc;
	}

	/**
	 * @return string
	 */
	public function getDoc()
	{
		return $this->doc;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$name = strtr($name, [" " => '_', "." => "_", "-" => "_"]);
		if ("Service" !== substr($name, - strlen("Service")))
		{
			$name .= 'Service';
		}
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $wsdl
	 */
	public function setWsdl($wsdl)
	{
		$this->wsdl = $wsdl;
	}

	/**
	 * @return string
	 */
	public function getWsdl()
	{
		return $this->wsdl;
	}

	/**
	 * @return \ArrayObject|FunctionStructure[]
	 */
	public function getFunctions()
	{
		return $this->functions;
	}

	/**
	 * @return \ArrayObject|TypeStructure[]
	 */
	public function getTypes()
	{
		return $this->types;
	}

}