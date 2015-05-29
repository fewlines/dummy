<?php

namespace Fewlines\Application;

use Fewlines\Helper\NamespaceHelper;
use Fewlines\Handler\Error as ErrorHandler;
use Fewlines\Locale\Locale;
use Fewlines\Session\Session;

class Bootstrap
{
	/**
	 * The auto call mehtod flag
	 *
	 * @var string
	 */
	const AUTO_CALL_FLAG = 'init';

	/**
	 * Holds the methods which has
	 * already been called
	 *
	 * @var array
	 */
	private static $called = array();

	/**
	 * The application from which the
	 * bootstrap was called
	 *
	 * @var Application
	 */
	protected $application;

	/**
	 * The config instance
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Start the bootstrap with the
	 * application instance
	 *
	 * @param Application $app
	 */
	public function __construct(Application $application) {
		$this->application = $application;
		$this->config = Config::getInstance();
	}

	/**
	 * This function will call all methods
	 * which begins with the auto call flag
	 */
	final public function autoCall() {
		foreach ((new \ReflectionClass($this))->getMethods() as $method) {
			if (preg_match('/^' . self::AUTO_CALL_FLAG . '(.*)$/', $method->name) &&
				! array_key_exists($method->name, self::$called)) {
				call_user_method($method->name, $this);
				self::$called[$method->name] = true;
			}
		}
	}

	/**
	 * Inits the error handler
	 */
	public function initErrorHandler() {
		$handler = new ErrorHandler;

        set_error_handler(array($handler, ErrorHandler::ERROR_FNC));
        register_shutdown_function(array($handler, ErrorHandler::SHUTDOWN_FNC));
	}

	/**
	 * Create new environment and give it
	 * to the registry to make it usable
	 */
	private function initEnvironment() {
		Registry::set('environment', new Environment);
	}

	/**
	 * Simply sets the default locale
	 */
	private function initLocale() {
		Locale::set(DEFAULT_LOCALE);
	}

	/**
	 * Starts the session component
	 */
	private function initSession() {
        Session::startSession();
        Session::initCookies();
	}

	/**
	 * Init the environment types from
	 * the config files
	 */
	private function initEnvironmentTypes() {
		$environment = Registry::get('environment');

        foreach ($this->config->getElementsByPath('environment/type') as $type) {
            $flags = $type->getAttribute('flags');
            $name = $type->getAttribute('name');

            /**
             * If the name is not given "ignore"
             * this element
             */

            if ( ! $name) {
                continue;
            }

            /**
             * Create name with appended
             * flags to parse them later
             */

            $nameWf = $name;

            if ($flags) {
                $nameWf .= Environment::TYPE_FLAG_SEPERTOR;
                $nameWf .= ltrim($flags, Environment::TYPE_FLAG_SEPERTOR);
            }

            $environment->addType($nameWf);

            /**
             * Add conditions from type
             * if they are given
             */

            foreach ($type->getChildrenByName('condition') as $condition) {
                $type = $condition->getAttribute('type');
                $value = $condition->getAttribute('value');

                /**
                 * Add a specific type to the environment
                 * defined with some aliases for each
                 * type
                 */

                if ($type && $value) {
                    switch ($type) {
                        case 'url':
                        case 'urlpattern':
                        case 'url-pattern':
                            $environment->addUrlPattern($name, $value);
                            break;

                        case 'host':
                        case 'hostname':
                            $environment->addHostname($name, $value);
                            break;
                    }
                }
            }
        }
	}

	/**
	 * Init all projects defined
	 * in the config
	 */
	private function initProjects() {
		$projects = $this->config->getElementByPath('project');

        if ($projects) {
            foreach ($projects->getChildren() as $proj) {
                /**
                 * Collect necessary informations
                 * from the xml element frame
                 */

                $id = $proj->getChildByName('id');
                $name = $proj->getChildByName('name');
                $description = $proj->getChildByName('description');
                $nsName = $proj->getChildByName('namespace');

                /**
                 * Get php namespace as default
                 * if not namespace is given
                 * ignore this optional parameter
                 * and set it to an empty string
                 */

                if ($nsName) {
                    $nsName = $nsName->getAttribute('php');
                    $nsName = NamespaceHelper::getNamespace($nsName, 'php');
                }
                else {
                    $nsName = "";
                }

                /**
                 * Add a new project to the list
                 * with the information from
                 * the xml config element
                 */

                if ($id && $name && $description) {
                    $project = ProjectManager::addProject(
                        $id->getContent(),
                        $name->getContent(),
                        $description->getContent(),
                        $nsName
                    );

                    /**
                     * Check if the project is initial
                     * activated and set the flag
                     * as if is so
                     */

                    $project->setActive(filter_var($proj->getAttribute('active'), FILTER_VALIDATE_BOOLEAN));
                }
            }
        }
	}
}