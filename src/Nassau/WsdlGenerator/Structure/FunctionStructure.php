<?php


namespace Nassau\WsdlGenerator\Structure;


class FunctionStructure
{
	private $reservedKeywords = ['and', 'or', 'xor', 'as', 'break', 'case', 'cfunction', 'class', 'continue', 'declare', 'const', 'default', 'do', 'else', 'elseif', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'extends', 'for', 'foreach', 'function', 'global', 'if', 'new', 'old_function', 'static', 'switch', 'use', 'var', 'while', 'array', 'die', 'echo', 'empty', 'exit', 'include', 'include_once', 'isset', 'list', 'print', 'require', 'require_once', 'return', 'unset', '__file__', '__line__', '__function__', '__class__', 'abstract', 'private', 'public', 'protected', 'throw', 'try'];

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $method;

	/**
	 * @var MemberStructure[]|\ArrayObject
	 */
	private $params;

	/**
	 * @var string
	 */
	private $doc;

	/**
	 * @var string
	 */
	private $returns;

	/**
	 * @var ServiceStructure
	 */
	private $service;

	function __construct(ServiceStructure $service, array $data)
	{
		$this->service = $service;
		$this->setName($data['method']);
		$this->setMethod($data['method']);
		$this->setReturns($data['returns']);
		$this->params = new \ArrayObject;
	}


	/**
	 * @param string $method
	 */
	public function setMethod($method)
	{
		if (false === $this->isMethodNameValid($method))
		{
			$method = 'call' . ucfirst($method);
		}
		$this->method = $method;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
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
	 * @return \ArrayObject|\Nassau\WsdlGenerator\Structure\MemberStructure[]
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @param string $returns
	 */
	public function setReturns($returns)
	{
		$this->returns = $returns;
	}

	/**
	 * @return string
	 */
	public function getReturns()
	{
		return $this->returns;
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

	private function isMethodNameValid($method)
	{
		return false === in_array(strtolower($method), $this->reservedKeywords)
			&& strtolower($this->service->getName()) !== strtolower($method);
	}

}