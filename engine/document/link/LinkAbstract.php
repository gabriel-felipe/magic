<?php 
/**
 * This is a file
 * @package MagicDocument\Link
 **/
/**
* Classe base para todas as classes de html links.
*/
Abstract class LinkAbstract extends AbstractAsset
{
	protected $media;
	protected $type;
	protected $rel;

    /**
     * Gets the value of media.
     *
     * @return mixed
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Sets the value of media.
     *
     * @param mixed $media the media
     *
     * @return self
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

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
     * Gets the value of rel.
     *
     * @return mixed
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * Sets the value of rel.
     *
     * @param mixed $rel the rel
     *
     * @return self
     */
    public function setRel($rel)
    {
        $this->rel = $rel;

        return $this;
    }

    function toString(){
        return "<link href='".$this->getRelPath()."' type='".$this->getType()."' rel='".$this->getRel()."' media='".$this->getMedia()."' >";
    }
    function __toString(){
        return $this->toString();
    }
}
?>