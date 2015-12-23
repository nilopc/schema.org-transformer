<?php

class SchemaProperty
{
	public function __construct($value, $name, $fullSchema)
	{
		$this->value = $value;
		$this->name = $name;
		$this->fullSchema = $fullSchema;
	}

	public function value(){ return $this->value; }
	public function name(){ return $this->name; }
	public function schema(){ return $this->fullSchema; }
}

class SchemaMapping
{

	//implements ArrayAccess
	
	/**
	 * @returns SchemaClassMapping
	 */
	public function get($className) {
		return $this->mapping[$className];
	}
}

class SchemaClass
{
	//implements ArrayAccess

	/**
	 * @return SchemaProperty
	 */
	public function property($propertyName)
	{
		return $this->properties[$propertyName];
	}
}

class SchemaAdapter
{
	public function __construct(SchemaMapping $mappings) {
		$this->mappings = $mappings;
	}

	/**
  	 * @param string $className
  	 * @param string $propertyName
	 */
	public function get($className, $propertyName)
	{
		return $this->mappings->get($className)->property($propertyName);
	}
}


$mappings = new SchemaMapping(); //Objeto con los mapeos.

$adapter = new SchemaAdapter($mappings);

/** @var SchemaClass $class */
$userSchema = $adapter->get(User::class);
$property = $userSchema->property('name');

echo $property->name();		// name (references Person#name)
echo $property->value(); 	// Nil
echo $property->schema(); 	// http://schema.org/Person
