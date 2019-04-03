<?php
require 'vendor/autoload.php';

use JiraRestApi\Project\ProjectService;
use JiraRestApi\Project\Project;
use JiraRestApi\JiraException;
use JiraRestApi\User\UserService;
use function Rap2hpoutre\RemoveStopWords\remove_stop_words;

function isProjectFound($project) {
    try {
        $proj = new ProjectService();
    
        $prjs = $proj->getAllProjects();
    
        foreach ($prjs as $p) {
            if ($p->name == $project) {
                return true;
            }
        }

        return false;
    } catch (JiraException $e) {
        print "Error Occured! " . $e->getMessage() . "\n";
        return true;
    }
}

function isUserExists($userEmail) {
    try {
        $us = new UserService();
        $paramArray = [
            'username' => $userEmail
        ];
        
        $user = $us->findUsers($paramArray);
        
        if ($user)
            return $user[0]->key;
        else
            return false;
    } catch (JiraException $e) {
        print "Error Occured! " . $e->getMessage() . "\n";

        return false;
    }
}

$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();
$jiraHost = getenv('JIRA_HOST');

print 'Enter Job Number: ';
$jobNumber = trim(fgets(STDIN));

print 'Enter Project Name: ';
$name = trim(fgets(STDIN));

print 'Enter Lead Email Address: ';
$email = trim(fgets(STDIN));

print "##### STARTING ##### \n";
if (empty($jobNumber) || empty($name) || empty($email)) {
    print "\nPlease input values correctly.\n";
    exit;
}

$removeStopWordResult = remove_stop_words($name);
$explodedString = explode(" ", $removeStopWordResult);
foreach($explodedString as $exString) {
    if (!empty($exString) && strlen($exString) >= 3) {
        $projectKey = strtoupper(substr($exString, 0, 3));
        break;
    }
}

$projectName = '[' . $jobNumber. '] ' . $name;
$userKey = isUserExists($email);

if (!$userKey) {
    print "##### Error Occured: User not found! ##### \n";
    exit;
}

if (isProjectFound($projectName)) {
    print "##### Error Occured: Project name already exists! ##### \n";
    exit;
}

try {
    $project = new Project();
    $project->setKey($projectKey)
        ->setName($projectName)
        ->setProjectTypeKey('software')
        ->setLead($userKey)
        ->setAssigneeType('PROJECT_LEAD');

    $projectService = new ProjectService();
    $projectData = $projectService->createProject($project);

    print "\n##### Successfully Created: ". $jiraHost . "/projects/" . $projectData->key . "\n";
} catch (JiraException $e) {
    print "Error Occured! " . $e->getMessage() . "\n";
}

print "\n##### END ##### \n";