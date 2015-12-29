<?php

/**
 * Class SchemaProperty
 */
class SchemaProperty
{
    /**
     * SchemaProperty constructor.
     * @param $value
     * @param $name
     * @param $fullSchema
     */
    public function __construct($value, $name, $fullSchema)
	{
		$this->value = $value;
		$this->name = $name;
		$this->fullSchema = $fullSchema;
	}

    /**
     * @return mixed
     */
    public function value(){ return $this->value; }

    /**
     * @return mixed
     */
    public function name(){ return $this->name; }

    /**
     * @return mixed
     */
    public function schema(){ return $this->fullSchema; }
}


/**
 * Class SchemaMappingException
 */
class SchemaMappingException extends \Exception
{
    /**
     * SchemaPropertyException constructor.
     * @param string $message
     * @param null $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = null, Exception $previous = null)
    {
        $message = sprintf('Class %s not found in mapping.', $message);

        parent::__construct($message, $code, $previous);
    }
}


/**
 * Class SchemaMapping
 */
class SchemaMapping implements ArrayAccess
{
    /**
     * @var array
     */
    private $mappings = [];

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->mappings);
    }

    /**
     * @param mixed $offset
     * @return mixed|void
     * @throws SchemaMappingException
     */
    public function offsetGet($offset)
    {
        if (false === array_key_exists($offset, $this->mappings)) {
            throw new SchemaMappingException($offset);
        }
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }

    /**
	 * @returns SchemaClass
	 */
	public function get($className) {
		return $this->mappings[$className];
	}
}

/**
 * Class SchemaPropertyException
 */
class SchemaPropertyException extends \Exception
{
    /**
     * SchemaPropertyException constructor.
     * @param string $message
     * @param null $code
     * @param Exception|null $previous
     */
	public function __construct($message, $code = null, Exception $previous = null)
	{
		$message = sprintf('Property %s not found', $message);

		parent::__construct($message, $code, $previous);
	}
}


/**
 * Class SchemaClass
 */
class SchemaClass implements ArrayAccess
{
	/**
	 * @var array
	 */
	private $properties = [];

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->properties);
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 * @throws SchemaPropertyException
	 */
	public function offsetGet($offset)
	{
		if (false === array_key_exists($offset, $this->properties)) {
			throw new SchemaPropertyException($offset);
		}

		return $this->properties[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
        $this->guard($value);
		$this->properties[$offset] = $value;
	}

	/**
	 * @param mixed $offset
	 * @throws SchemaPropertyException
	 */
	public function offsetUnset($offset)
	{
		if (false === array_key_exists($offset, $this->properties)) {
			throw new SchemaPropertyException($offset);
		}

		unset($this->properties[$offset]);
	}

    /**
     * @param $value
     */
    private function guard($value)
    {
        if (false === $value instanceof SchemaProperty) {
            throw new \InvalidArgumentException(sprintf('Provided value is not instance of %s.', SchemaProperty::class));
        }
    }

	/**
	 * @return SchemaProperty
	 */
	public function property($propertyName)
	{
		return $this->properties[$propertyName];
	}
}

/**
 * Class SchemaAdapter
 */
class SchemaAdapter
{
    /**
     * @var SchemaMapping
     */
    private $mappings;

    /**
     * SchemaAdapter constructor.
     * @param SchemaMapping $mappings
     */
    public function __construct(SchemaMapping $mappings) {
		$this->mappings = $mappings;
	}

    /**
     * @param $className
     * @return SchemaClass
     */
	public function get($className)
	{
		return $this->mappings->get($className);
	}
}

/**
 * Class User
 */
class User
{
    /**
     * @var
     */
    private $name;

    /**
     * User constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
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
