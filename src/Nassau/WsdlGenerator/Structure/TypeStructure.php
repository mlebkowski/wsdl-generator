<?php


namespace Nassau\WsdlGenerator\Structure;


class TypeStructure
{

	/**
	 * @var string
	 */
	private $className;

	/**
	 * @var MemberStructure[]|\ArrayObject
	 */
	private $members;

	function __construct($className)
	{
		$this->setClassName($className);
		$this->members = new \ArrayObject;
	}

	/**
	 * @param string $className
	 */
	public function setClassName($className)
	{
		$this->className = ucfirst($className);
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return $this->className;
	}

	/**
	 * @return \Nassau\WsdlGenerator\Structure\MemberStructure[]|\ArrayObject
	 */
	public function getMembers()
	{
		return $this->members;
	}


}