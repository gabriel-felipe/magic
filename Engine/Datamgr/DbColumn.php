<?php 
namespace Magic\Engine\Datamgr;

class DbColumn
{
	protected $type;
	protected $name;
	protected $null=TRUE;
	protected $default=NULL;
	protected $autoIncrement=FALSE;
	protected $references=FALSE;
	protected $primaryKey=FALSE;
    /**
     * Gets the value of type.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param mixed $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param mixed $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of null.
     *
     * @return mixed
     */
    public function getNull()
    {
        return $this->null;
    }

    /**
     * Sets the value of null.
     *
     * @param mixed $null the null
     *
     * @return self
     */
    public function setNull($null)
    {
        $this->null = $null;

        return $this;
    }

    /**
     * Gets the value of default.
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Sets the value of default.
     *
     * @param mixed $default the default
     *
     * @return self
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Gets the value of autoIncrement.
     *
     * @return mixed
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * Sets the value of autoIncrement.
     *
     * @param mixed $autoIncrement the auto increment
     *
     * @return self
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->autoIncrement = $autoIncrement;

        return $this;
    }

    /**
     * Gets the value of reference.
     *
     * @return mixed
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * Sets the value of reference.
     *
     * @param mixed $reference the reference
     *
     * @return self
     */
    public function setReferences($references)
    {
        $this->references = $references;

        return $this;
    }

    /**
     * Gets the value of primaryKey.
     *
     * @return mixed
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Sets the value of primaryKey.
     *
     * @param mixed $primaryKey the primary key
     *
     * @return self
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }
}
?>