<?php

namespace Fewlines\Application;

class ProjectManager
{
	/**
	 * Holds all created projects
	 *
	 * @var array
	 */
	private static $projects = array();

	/**
	 * Adds a new project to the list
	 *
	 * @param string $name
	 * @param string $description
	 * @param string $id
	 * @param string $nsName The name of the namespace defined in
	 *                       {config}/core/Namespace.xml
	 * @return ProjectManager\Project
	 */
	public static function addProject($id, $name, $description, $nsName = "") {
		return self::$projects[] = new ProjectManager\Project($id, $name, $description, $nsName);
	}

	/**
	 * Get's the active project.
	 * Note that only one project can
	 * be active at once.
	 *
	 * @return ProjectManager\Project
	 */
	public static function getActiveProject() {
		foreach (self::$projects as $project) {
			if ($project->isActive()) {
				return $project;
			}
		}

		return null;
	}

    /**
     * Gets all projects
     *
     * @return array
     */
    public static function getProjects() {
        return self::$projects;
    }
}