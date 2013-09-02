<?php

namespace Nassau\WsdlGenerator;

use DOMDocument;
use DOMElement;
use Nassau\WsdlGenerator\Structure\MemberStructure;
use SoapClient;
use SoapFault;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\DocBlock\Tag;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Generator\PropertyGenerator;

class WSDLGenerator
{
	private $namespace;
	/**
	 * @var Structure\ServiceStructure
	 */
	private $service;
	private $wsdl;
	private $targetNamespace;
	private $documentation = [];
	private $serviceDescription = "";

	/**
	 * @var DomDocument
	 */
	private $dom;

	public function __construct()
	{
		$this->dom = new DomDocument;
	}


	public function generate($wsdl, GeneratorOptions $options = null)
	{
		try {
			$client = new SoapClient($wsdl);
		} catch(SoapFault $e) {
			throw new \RuntimeException('Cannot parse the WSDL file: ' . $wsdl);
		}
		$this->dom->load($wsdl);

		$this->wsdl = $wsdl;

		$this->getDocumentation();
		$this->getTargetNamespace();
		$this->declareService();

		$this->getOperations($client);
		$this->getTypes($client);

		$this->saveCode($options->getTargetDir(), $options->getNamespace());

return;

// class level docblock
$code .= "/**\n";
$code .= " * ".$service['class']." class\n";
$code .= " * \n";
$code .= parse_doc(" * ", $service['doc']);
$code .= " * \n";
$code .= " * @author    {author}\n";
$code .= " * @copyright {copyright}\n";
$code .= " * @package   {package}\n";
$code .= " */\n";
$code .= "class ".$service['class']." extends SoapClient {\n\n";

// add classmap
$code .= "  private static \$classmap = array(\n";
foreach($service['types'] as $type) {
	$code .= "                                    '".$type['class']."' => '".$type['class']."',\n";
}
$code .= "                                   );\n\n";
$code .= "  public function ".$service['class']."(\$wsdl = \"".$service['wsdl']."\", \$options = array()) {\n";

// initialize classmap (merge)
$code .= "    foreach(self::\$classmap as \$key => \$value) {\n";
$code .= "      if(!isset(\$options['classmap'][\$key])) {\n";
$code .= "        \$options['classmap'][\$key] = \$value;\n";
$code .= "      }\n";
$code .= "    }\n";
$code .= "    parent::__construct(\$wsdl, \$options);\n";
$code .= "  }\n\n";

foreach($service['functions'] as $function) {
	$code .= "  /**\n";
	$code .= parse_doc("   * ", $function['doc']);
	$code .= "   *\n";

	$signature = array(); // used for function signature
	$para = array(); // just variable names
	if(count($function['params']) > 0) {
		foreach($function['params'] as $param) {
			$code .= "   * @param ".(isset($param[0])?$param[0]:'')." ".(isset($param[1])?$param[1]:'')."\n";
			/*$typehint = false;
			  foreach($service['types'] as $type) {
			if($type['class'] == $param[0]) {
			  $typehint = true;
			}
			  }
			  $signature[] = ($typehint) ? implode(' ', $param) : $param[1];*/
			$signature[] = (in_array($param[0], $primitive_types) or substr($param[0], 0, 7) == 'ArrayOf') ? $param[1] : implode(' ', $param);
			$para[] = $param[1];
		}
	}
	$code .= "   * @return ".$function['return']."\n";
	$code .= "   */\n";
	$code .= "  public function ".$function['name']."(".implode(', ', $signature).") {\n";
	//  $code .= "    return \$this->client->".$function['name']."(".implode(', ', $para).");\n";
	$code .= "    return \$this->__soapCall('".$function['method']."', array(";
	$params = array();
	if(count($signature) > 0) { // add arguments
		foreach($signature as $param) {
			if(strpos($param, ' ')) { // slice
				$param = array_pop(explode(' ', $param));
			}
			$params[] = $param;
		}
		//$code .= "\n      ";
		$code .= implode(", ", $params);
		//$code .= "\n      ),\n";
	}
	$code .= "), ";
	//$code .= implode(', ', $signature)."),\n";
	$code .= "      array(\n";
	$code .= "            'uri' => '".$targetNamespace."',\n";
	$code .= "            'soapaction' => ''\n";
	$code .= "           )\n";
	$code .= "      );\n";
	$code .= "  }\n\n";
}
$code .= "}\n\n";
print "done\n";

print "Writing ".$service['class'].".php...";
$fp = fopen($service['class'].".php", 'w');
fwrite($fp, "<?php\n".$code."?>\n");
fclose($fp);
print "done\n";

function parse_doc($prefix, $doc) {
	$code = "";
	$words = split(' ', $doc);
	$line = $prefix;
	foreach($words as $word) {
		$line .= $word.' ';
		if( strlen($line) > 90 ) { // new line
			$code .= $line."\n";
			$line = $prefix;
		}
	}
	$code .= $line."\n";
	return $code;
}

/**
 * Look for enumeration
 *
 * @param DOM $dom
 * @param string $class
 * @return array
 */
function checkForEnum(&$dom, $class) {
	$values = array();

	$node = findType($dom, $class);
	if(!$node) {
		return $values;
	}

	$value_list = $node->getElementsByTagName('enumeration');
	if($value_list->length == 0) {
		return $values;
	}

	for($i=0; $i<$value_list->length; $i++) {
		$values[] = $value_list->item($i)->attributes->getNamedItem('value')->nodeValue;
	}
	return $values;
}

function generatePHPSymbol($s) {
	global $reserved_keywords;

	if(!preg_match('/^[A-Za-z_]/', $s)) {
		$s = 'value_'.$s;
	}
	if(in_array(strtolower($s), $reserved_keywords)) {
		$s = '_'.$s;
	}
	return preg_replace('/[-.\s]/', '_', $s);
}



	}

	private function getDocumentation()
	{
		$nodes = $this->dom->getElementsByTagName('documentation');
		$this->documentation = [];
		foreach($nodes as $node)
		{
			if ($node->parentNode->localName == 'service')
			{
				$this->serviceDescription = trim($node->parentNode->nodeValue);
			}
			elseif ($node->parentNode->localName == 'operation')
			{
				$operation = $node->parentNode->getAttribute('name');
				$doc[$operation] = trim($node->nodeValue);
			}
		}
	}

	private function getTargetNamespace()
	{
		$this->targetNamespace = "";
		/** @var DomElement[] $nodes */
		$nodes = $this->dom->getElementsByTagName('definitions');
		foreach($nodes as $node)
		{
			$this->targetNamespace = $node->getAttribute('targetNamespace');
		}

	}

	private function declareService()
	{
		/** @var DomElement $serviceNode */
		$serviceNode = $this->dom->getElementsByTagNameNS('*', 'service')->item(0);
		$name = $serviceNode->getAttribute('name');

		$this->service = new Structure\ServiceStructure([
			'name' => $name,
			'wsdl' => $this->wsdl,
			'doc'  => $this->serviceDescription,
		]);
	}

	private function getOperations(SoapClient $client)
	{
		foreach($client->__getFunctions() as $operation)
		{
			$function = $this->addOperation($operation);
			if (false === $this->service->getFunctions()->offsetExists($function->getName()))
			{
				$this->service->getFunctions()->offsetSet($function->getName(), $function);
			}
		}
	}

	/**
	 * @param string $operation
	 *
	 * @return Structure\FunctionStructure
	 * @throws \InvalidArgumentException
	 */
	private function addOperation($operation)
	{
		$matches = [];
		if (false === preg_match('/^(?<returns>\w[\w\d_]*) (?<method>\w[\w\d_]*)\((?<params>[\w\$\d,_ ]*)\)$/', $operation, $matches))
		{
			throw new \InvalidArgumentException('Cannot parse operation: ' . $operation);
		}

		preg_match_all('/(?<type>[^\s]+)\s+\$(?<name>[^,]+)/', $matches['params'], $params, PREG_SET_ORDER);

		$function = new Structure\FunctionStructure($this->service, [
			'method' => $matches['method'],
			'returns' => $matches['returns'],
		]);
		$function->setDoc($this->getDocumentationForMethod($matches['method']));

		array_walk($params, function ($param) use ($function)
		{
			$function->getParams()->append(new MemberStructure($param['name'], $param['type']));
		});

		return $function;
	}

	private function getDocumentationForMethod($method)
	{
		return isset($this->documentation[$method]) ? $this->documentation[$method] : "";
	}

	private function getTypes(SoapClient $client)
	{
		foreach($client->__getTypes() as $type)
		{
			$type = $this->getTypeDefinition($type);
			if (null === $type)
			{
				continue;
			}
			$this->service->getTypes()->offsetSet($type->getClassName(), $type);
		}
	}

	/**
	 * @param string $type
	 *
	 * @return Structure\TypeStructure
	 */
	private function getTypeDefinition($type)
	{
		preg_match('/^(?<type>[\w_]+) \s+ (?!ArrayOf) (?<class>[\w\d_]+) (?!\[\])? \s* {(?<members>.*)} $/xs', $type, $type);
		if (0 === sizeof($type))
		{
			return null;
		}

		preg_match_all('/\s* (?<type>[\w\d_]+) \s+ (?<ns>.*:)? (?<name>[\w\d_]+);/x', $type['members'], $members, PREG_SET_ORDER);

		$type = new Structure\TypeStructure($type['class']);
		array_walk($members, function ($member) use ($type)
		{
			$type->getMembers()->append(new Structure\MemberStructure($member['name'], $member['type']));
		});

		return $type;
	}

	private function saveCode($targetDir, $namespace)
	{
		$this->namespace = $namespace;
		$types = $this->getTypesCode() + ["service" => $this->getServiceCode()];
		foreach ($types as $type)
		{
			$path = $this->getPathForNamespace($targetDir, $type->getNamespaceName());
			if (false === is_dir($path))
			{
				mkdir($path, 0770, true);
			}
			file_put_contents($path . DIRECTORY_SEPARATOR . $type->getName() . ".php",
				sprintf("<?php \n %s", $type->generate()));
		}
	}

	/**
	 * @return ClassGenerator[]
	 */
	private function getTypesCode()
	{
		$generator = $this;
		return array_map(function (Structure\TypeStructure $type) use ($generator)
		{
			$code = new ClassGenerator($type->getClassName());
			$code->setNamespaceName($generator->getFullNamespace($type->getClassName(), true));
			foreach ($type->getMembers() as $member)
			{
				$tag = new Tag();
				$tag->setName('var');
				if ($member->isPrimitive())
				{
					$tag->setDescription($member->getType());
				}
				else
				{
					$tag->setDescription("\\" . $generator->getFullNamespace($member->getType()));
				}

				$docBlock = new DocBlockGenerator(null, null, [$tag]);
				$property = new PropertyGenerator();
				$property->setName($member->getName());
				$property->setDocBlock($docBlock);
				$code->addPropertyFromGenerator($property);
			}
			return $code;
		}, $this->service->getTypes()->getArrayCopy());
	}

	public function getFullNamespace($className, $nsOnly = false)
	{
		$className = new Structure\ClassNameStructure($className, $this->namespace . "\\Structure");
		return $nsOnly ? $className->getNamespace() : $className->getClassName();
	}

	private function getPathForNamespace($targetDir, $namespace)
	{
		$path = str_replace('\\', DIRECTORY_SEPARATOR, ltrim($namespace, "\\"));
		return rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;
	}

	private function getServiceCode()
	{
		$code = new ClassGenerator($this->service->getName(), $this->namespace, null, '\SoapClient');
		$doc = $this->service->getDoc();
		if ($doc)
		{
			$docBlock = new DocBlockGenerator($doc);
			$code->setDocBlock($docBlock);
		}

		foreach ($this->service->getFunctions() as $function)
		{
			$method = new MethodGenerator($function->getMethod());
			$docBlock = new DocBlockGenerator($function->getDoc());
			foreach ($function->getParams() as $param)
			{
				$methodParam = new ParameterGenerator($param->getName());
				if (false === $param->isPrimitive())
				{
					$methodParam->setType('\\' . $this->getFullNamespace($param->getType()));
				}
				$method->setParameter($methodParam);

				$tag = new Tag;
				$tag->setName('property');
				$type = $param->getType();
				if (false === $param->isPrimitive())
				{
					$type = '\\' . $this->getFullNamespace($param->getType());
				}
				$tag->setDescription(sprintf('%s $%s', $type, $param->getName()));
				$docBlock->setTag($tag);
			}

			$tag = new Tag;
			$tag->setName('returns');
			$tag->setDescription("\\" . $this->getFullNamespace($function->getReturns()));
			$docBlock->setTag($tag);

			$method->setBody(sprintf('return $this->__soapCall("%s", func_get_args());', $function->getName()));

			$method->setDocBlock($docBlock);
			$code->addMethodFromGenerator($method);

		}
		return $code;
	}

}