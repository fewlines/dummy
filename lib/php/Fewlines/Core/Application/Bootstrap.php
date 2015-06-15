<?php

namespace Fewlines\Core\Application;

use Fewlines\Component\Session\Session;
use Fewlines\Core\Helper\NamespaceHelper;
use Fewlines\Core\Handler\Error as ErrorHandler;
use Fewlines\Core\Locale\Locale;
use Fewlines\Core\Http\Router;

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
     * @param Application $application
     * @internal param Application $app
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
     * Init the default fewlines project
     * to get static informations from
     * anywhere
     */
    final protected function initDefaultProject() {
        ProjectManager::setDefaultProject(
                DEFAULT_PROJECT_ID, DEFAULT_PROJECT_NAME, DEFAULT_PROJECT_NS
            );
    }

	/**
	 * Inits the error handler
	 */
	final protected function initErrorHandler() {
        if (DEVELOPER_DEBUG == true) {
            return;
        }

		$handler = new ErrorHandler;

        set_error_handler(array($handler, ErrorHandler::ERROR_FNC));
        register_shutdown_function(array($handler, ErrorHandler::SHUTDOWN_FNC));
	}

	/**
	 * Create new environment and give it
	 * to the registry to make it usable
	 */
	final protected function initEnvironment() {
		Registry::set('environment', new Environment);
	}

	/**
	 * Simply sets the default locale
	 */
	final protected function initDefaultLocale() {
		Locale::set(DEFAULT_LOCALE);
	}

	/**
	 * Starts the session component
	 */
	final protected function initSession() {
        if (class_exists('\\Fewlines\\Component\\Session\\Session')) {
            Session::startSession();
            Session::initCookies();
        }
	}

	/**
	 * Init all projects defined
	 * in the config
	 */
	final protected function initProjects() {
		$projects = $this->config->getElementByPath('project');

        if ($projects) {
            $activeCount = 0;

            foreach ($projects->getChildren() as $proj) {
                /**
                 * Collect necessary informations
                 * from the xml element frame
                 */

                $id = $proj->getAttribute('id');
                $name = $proj->getChildByName('name');
                $description = $proj->getChildByName('description');
                $nsName = NamespaceHelper::getNamespace($id, 'php');

                /**
                 * Add a new project to the list
                 * with the information from
                 * the xml config element
                 */

                if ( ! empty($id) && $name && $description) {
                    $project = ProjectManager::addProject(
                        $id, $name->getContent(), $description->getContent(), $nsName
                    );

                    /**
                     * Check if the project is initial
                     * activated and set the flag
                     * as if is so
                     */

                    $isActive = $project->setActive(filter_var($proj->getAttribute('active'), FILTER_VALIDATE_BOOLEAN));

                    if ($isActive) {
                        $activeCount++;

                        if ($activeCount > 1) {
                            // Init environment types manually
                            $this->initEnvironmentTypes();

                            throw new Bootstrap\Exception\TooManyProjectsException(
                                'Only one project can be active'
                            );
                        }
                        else {
                            // Add config files from this project
                            Config::getInstance()->addConfigFiles(array(
                                    array(
                                        'dir'  => CONFIG_PATH . DR_SP . $project->getId(),
                                        'type' => 'xml'
                                    )
                                ));

                            Router::getInstance()->update();
                        }
                    }
                }
            }
        }
	}

    /**
     * Init the environment types from
     * the config files
     */
    final protected function initEnvironmentTypes() {
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
}