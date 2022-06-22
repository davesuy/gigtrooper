<?php

namespace Gigtrooper\Traits;

trait IOHelper
{
	public function getAllClassesByAppFolder($folderName, $baseClass = null, $byTitle = false)
	{
		$classPaths = glob( app_path( "$folderName/*.php") );

		$classes = array();

		$namespace = "\\Gigtrooper\\$folderName\\";

		foreach ($classPaths as $classPath)
		{
			$segments = explode('/', $classPath);

			$segments = explode('\\', $segments[count($segments) - 1]);

			$classFile = $segments[count($segments) - 1];

			$classTitle = preg_replace('/.[^.]*$/', '', $classFile);

			$className = $namespace . $classTitle;

			if ($baseClass != null)
			{
				$namespaceBase = "\\Gigtrooper\\$folderName\\" . $baseClass;

				if ($className != $namespaceBase)
				{
					if ($byTitle == true)
					{
						$classes[] = $classTitle;
					}
					else
					{
						$classes[] = new $className;
					}
				}
			}
			else
			{
				if ($byTitle == true)
				{
					$classes[] = $classTitle;
				}
				else
				{
					$classes[] = new $className;
				}
			}
		}

		return $classes;
	}
}