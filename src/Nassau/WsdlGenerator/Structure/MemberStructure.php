<?php


namespace Nassau\WsdlGenerator\Structure;


class MemberStructure
{
	private $primitiveTypes = ['string', 'int', 'long', 'float', 'boolean', 'dateTime', 'double', 'short', 'UNKNOWN', 'base64Binary', 'decimal', 'ArrayOfInt', 'ArrayOfFloat', 'ArrayOfString', 'decimal', 'hexBinary'];

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $type;

	function __construct($name, $type)
	{
		$this->name = $name;
		$this->type = $type;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
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
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	public function isPrimitive()
	{
		return in_array($this->type, $this->primitiveTypes);
	}

	public function isArray()
	{
		return 'ArrayOf' === substr($this->type, 0, strlen('ArrayOf'));
	}
}