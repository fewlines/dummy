<?php

namespace Fewlines\Core\Application\ProjectManager;

use Fewlines\Core\Application\Config;
use Fewlines\Core\Http\Router;

class Project
{
	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var boolean
	 */
	private $active = false;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var string
	 */
	private $nsName;

	/**
	 * Holds the bootstrap instance of
	 * from the given namespace - if exists
	 *
	 * @var {lib/php/$ns}\Application\Bootstrap
	 */
	private $bootstrap;

	/**
	 * @param string $id
	 * @param string $name
	 * @param string $description
	 * @param string $nsName
	 */
	public function __construct($id, $name, $description, $nsName) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->nsName = $nsName;
	}

    /**
     * Gets the value of id.
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Gets the value of description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return boolean
     */
    public function isActive() {
    	return $this->active;
    }

    /**
     * @param  boolean $isActive
     * @return boolean
     */
    public function setActive($isActive) {
    	return $this->active = $isActive;
    }

    /**
     * Gets the value of nsName.
     *
     * @return string
     */
    public function getNsName() {
        return $this->nsName;
    }

    /**
     * Tells if this project has
     * a namespace
     *
     * @return boolean
     */
    public function hasNsName() {
    	return ! empty($this->nsName);
    }

    /**
	 * Calls the bootstrap of the project
	 * with the given namespace
	 *
     * @param  \Fewlines\Core\Application\Application $app
     * @return {lib/php/$ns}\Application\Bootstrap
     */
    public function bootstrap(\Fewlines\Core\Application\Application $app) {
    	if ($this->hasNsName()) {
            // Get bootstrap class
            $class = $this->getNsName() . BOOTSTRAP_RL_NS;

            // Create and call bootstrap
    		if (class_exists($class)) {
            	$this->bootstrap = new $class($app);
                $this->bootstrap->autoCall();
            }
    	}

        Router::getInstance()->update();

    	return $this->bootstrap;
    }

    /**
     * Returns the bootstrap (if it was called)
     *
     * @return {lib/php/$ns}\Application\Bootstrap
     */
    public function getBootstrap() {
    	return $this->bootstrap;
    }
}