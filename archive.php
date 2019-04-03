<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\Project\Project;
use JiraRestApi\JiraException;

function getProjectData($project) {
    try {
        $proj = new ProjectService();
    
        $prjs = $proj->getAllProjects();
    
        foreach ($prjs as $p) {
            if ($p->name == $project) {
                return $p;
            }
        }

        return false;
    } catch (JiraException $e) {
        print "Error Occured! " . $e->getMessage() . "\n";
        return false;
    }
}

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$permissionSchemeId = getenv('ARCHIVED_PERMISSION_SCHEME_ID');

print 'Enter Job Number: ';
$jobNumber = trim(fgets(STDIN));

print 'Enter Project Name: ';
$name = trim(fgets(STDIN));

print "##### STARTING ##### \n";
if (empty($jobNumber) || empty($name)) {
    print "\nPlease input values correctly.\n";
    exit;
}

$projectName = '[' . $jobNumber. '] ' . $name;
$projectData = getProjectData($projectName);

if (!$projectData) {
    print "##### Error Occured: Project name not found! ##### \n";
    exit;
}

try {
    $project = new Project();
    $project->setName('[ARCHIVED] ' . $projectData->name)
            ->setPermissionScheme($permissionSchemeId);

    $projectService = new ProjectService();
    $projectArchiveData = $projectService->updateProject($project, $projectData->key);
   
    print "\n##### Successfully Archived: ". $projectArchiveData->name . "\n";
} catch (JiraException $e) {
    print("Error Occured! " . $e->getMessage() . "\n");
}

print "\n##### END ##### \n";