Usage
-----

```php
$wsdl = '...';
$generator = new \Nassau\WsdlGenerator\WSDLGenerator();
$options = new \Nassau\WsdlGenerator\GeneratorOptions;
$options->setNamespace('\\Acme\\WebService');
$options->setTargetDir(__DIR__ . "/src");
$generator->generate($wsdl, $options);
```

**Experimental**
