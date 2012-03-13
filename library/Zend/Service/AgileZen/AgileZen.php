<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\AgileZen;

use DOMDocument,
    Zend\Http\Client as HttpClient;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage AgileZen
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AgileZen 
{
    const URL            = 'https://agilezen.com/api/v1';
    const HEADER_KEY     = 'X-Zen-ApiKey';
    const ERR_UNKNOWN    = 'Error unknown';
    const ERR_ID_PROJECT = 'You didn\'t specify the id of the project';
    const ERR_ID_STORY   = 'You didn\'t specify the id of the story';
    const ERR_ID_ROLE    = 'You didn\'t specify the id of the role';
    const ERR_ID_PHASE   = 'You didn\'t specify the id of the phase';
    const ERR_ID_INVITE  = 'You didn\'t specify the id of the invite';
    const ERR_ID_TAG     = 'You didn\'t specify the id of the tag';
    const ERR_ID_ATTACH  = 'You didn\'t specify the id of the attachment';
    const ERR_ID_COMMENT = 'You didn\'t specify the id of the comment';
    const ERR_DATA       = 'You didn\'t specify the data array';
    const ERR_DATA_ROLE  = 'You didn\'t specify the data of the role';
    const ERR_NAME_KEY   = 'You didn\'t specify the name key in data';
    const ERR_EMAIL_KEY  = 'You didn\'t specify the email key in data';
    const ERR_ROLEID_KEY = 'You didn\'t specify the role key in data';
    const ERR_FILE_KEY   = 'You didn\'t specify the filename key in data';
    const ERR_TEXT_KEY   = 'You didn\'t specify the text key in data';

    /**
     * API KEY
     * 
     * @var string
     */    
    protected $apiKey;

    /**
     * Http Client
     * 
     * @var HttpClient 
     */
    protected $httpClient;

    /**
     * Constructore
     * 
     * @param string $apiKey 
     */
    public function __construct($apiKey)
    {
        if (empty($apiKey)) {
            throw new Exception\InvalidArgumentException("You need an API key to use AgileZen");
        }
        $this->setApiKey($apiKey);
    }

    /**
     * Set Api Key
     * 
     * @param string $apiKey 
     */
    public function setApiKey($apiKey) 
    {
        if (!empty($apiKey)) {
            $this->apiKey = $apiKey;
        }
    }

    /**
     * Get Api Key
     * 
     * @return string 
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Get the HttpClient instance
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (empty($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }
        return $this->httpClient;
    }

    /**
     * HTTP call
     *
     * @param  string $url
     * @param  string $method
     * @param  array  $data
     * @param  string $body
     * @return \Zend\Http\Response
     */
    protected function httpCall($url, $method, $body=null)
    {
        $client = $this->getHttpClient();
        $client->resetParameters();
        
        $headers = array();
        $headers[self::HEADER_KEY]= $this->getApiKey();
        
        if (!empty($body)) {
            if (is_array($body)) {
                $body = json_encode($body);
            } else if (!is_numeric($body)) {
                $body = '"' . $body . '"';
            }
            $client->setRawBody($body);
        }

        $client->setMethod($method);
        $client->setHeaders($headers);
        $client->setUri(self::URL . $url);
        $this->errorMsg = null;
        $this->errorCode = null;
        
        $result = $client->send();
        $status = $result->getStatusCode();
        if ($status != 200) {
            $this->errorMsg = $this->getErrorFromResponse($result);
            $this->errorCode= $status;
            return false;
        }
        return $result;
    }

    /**
     * Return true is the last call was successful
     * 
     * @return boolean 
     */
    public function isSuccessful()
    {
        return (empty($this->errorMsg));
    }

    /**
     * Get the error msg of the last HTTP call
     *
     * @return string
     */
    public function getErrorMsg() 
    {
        return $this->errorMsg;
    }

    /**
     * Get the error code of the last HTTP call
     * 
     * @return string 
     */
    public function getErrorCode() 
    {
        return $this->errorCode;
    }

    /**
     * Authenticate
     * 
     * @return boolean 
     */
    public function authenticate()
    {
        $result = $this->httpCall('/projects','GET');
        return ($result!==false);
    }

    /**
     * Get the list of the projects
     * 
     * @param  array $params
     * @return Container|boolean 
     */
    public function getProjects($params=array())
    {
        $result = $this->httpCall('/projects' . $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $list = json_decode($result->getBody(),true);
        if (is_array($list) && !empty($list['items'])) {
            return new Container($this, $list['items'], 'project');
        } 
        return false;    
    }

    /**
     * Get project
     * 
     * @param  integer $id
     * @param  array   $params
     * @return Resources\Project|boolean 
     */
    public function getProject($projectId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        $result = $this->httpCall("/projects/$projectId" . $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $project = json_decode($result->getBody(),true);
        if (is_array($project)) {
            return new Resources\Project($this, $project);
        } 
        return false;       
    }

    /**
     * Modifies a single project’s metadata.
     * 
     * @param  integer $id
     * @param  array $data (valid keys are: 'name', 'description', 'details', 'owner')
     * @return Resources\Project|boolean 
     */
    public function updateProject($id, $data)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (!is_array($data) || !Resources\Project::validKeys($data)) {
            throw new Exception\InvalidArgumentException('The array of values is not valid for the project');
        }
        $result = $this->httpCall("/projects/$id",'PUT', $data);
        if ($result===false) {
            return false;
        }
        $project = json_decode($result->getBody(),true);
        if (is_array($project)) {
            return new Resources\Project($this, $project);
        } 
        return false;    
    }

    /**
     * Get the members of a project
     * 
     * @param  integer $projectId
     * @param  array   $params
     * @return Container|boolean
     */
    public function getMembers($projectId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        $result = $this->httpCall("/projects/$projectId/members" . 
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $members = json_decode($result->getBody(),true);
        if (is_array($members) && !empty($members['items'])) {
            return new Container($this, $members['items'], 'user');
        } 
        return false;
    }

    /**
     * Add a member to a project
     * 
     * @param  integer $projectId
     * @param  string|integer $member can be the user id or the name
     * @return boolean 
     */
    public function addProjectMember($projectId, $member)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($member)) {
            throw new Exception\InvalidArgumentException('You must specify id or name of the member');
        }
        $result = $this->httpCall("/projects/$projectId/members/", 'POST', $member);
        return ($result!==false);
    }

    /**
     * Remove a member of a project
     * 
     * @param  integer $projectId
     * @param  string|integer $member can be the user id or the name
     * @return boolean 
     */
    public function removeProjectMember($projectId, $member)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($member)) {
            throw new Exception\InvalidArgumentException('You must specify id or name of the member');
        }
        $result = $this->httpCall("/projects/$projectId/members/$member", 'DELETE');
        return ($result!==false);
    }

    /**
     * Get the phases of a project
     * 
     * @param  integer $id
     * @param  array   $params
     * @return Container|boolean
     */
    public function getPhases($projectId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        $result = $this->httpCall("/projects/$projectId/phases" . $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $phases = json_decode($result->getBody(),true);
        if (is_array($phases) && !empty($phases['items'])) {
            return new Container($this, $phases['items'], 'phase', $projectId);
        } 
        return false;
    }

    /**
     * Get the phases of a project
     * 
     * @param  integer $projectId
     * @param  array $params
     * @return Container|boolean
     */
    public function getStories($projectId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        $result = $this->httpCall("/projects/$projectId/stories" . $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $stories = json_decode($result->getBody(),true);
        if (is_array($stories) && !empty($stories['items'])) {
            return new Container($this, $stories['items'], 'story', $projectId);
        } 
        return false;
    }

    /**
     * Get stories in a phase
     * 
     * @param  integer $projectId
     * @param  integer $phaseId
     * @param  arary   $params
     * @return Container 
     */
    public function getStoriesPhase ($projectId, $phaseId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($phaseId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PHASE);
        }
        $result = $this->httpCall("/projects/$projectId/phases/$phaseId/stories" . 
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $stories = json_decode($result->getBody(),true);
        if (is_array($stories) && !empty($stories['items'])) {
            return new Container($this, $stories['items'], 'story', $projectId);
        } 
        return false;
    }

    /**
     * Get the metrics of a project
     * 
     * @param  integer $id
     * @return array|boolean 
     */
    public function getProjectMetrics($id)
    {
        if (empty($id)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        $result = $this->httpCall("/projects/$id?with=metrics",'GET');
        if ($result===false) {
            return false;
        }
        $result = json_decode($result->getBody(),true);
        if (isset($result['metrics'])) {
            return $result['metrics'];
        }
        return false;
            
    }

    /**
     * Get a story of a project
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  array   $params
     * @return Resources\Story|boolean 
     */
    public function getStory($projectId, $storyId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId" . 
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $story = json_decode($result->getBody(),true);
        if (!empty($story) && is_array($story)) {
            $story['projectId'] = $projectId;
            return new Resources\Story($this, $story);
        }
        return false;
    }

    /**
     * Get a phase of a project
     * 
     * @param  integer $id
     * @param  array $params
     * @return Resources\Phase|boolean 
     */
    public function getPhase($projectId, $phaseId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($phaseId)) {
            throw new Exception\InvalidArgumentException("You did not specify the id of the phase");
        }
        $result = $this->httpCall("/projects/$projectId/phases/$phaseId" . 
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $phase = json_decode($result->getBody(),true);
        if (!empty($phase) && is_array($phase)) {
            $phase['projectId'] = $projectId;
            return new Resources\Phase($this, $phase);
        }
        return false;
    }

    /**
     * Get project roles
     * 
     * @param  integer $id
     * @param  array   $params
     * @return Container 
     */
    public function getRoles($projectId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        $result = $this->httpCall("/projects/$projectId/roles" . $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $roles = json_decode($result->getBody(),true);
        if (is_array($roles) && !empty($roles['items'])) {
            return new Container($this, $roles['items'], 'role', $projectId);
        } 
        return false;       
    }

    /**
     * Get a phase of a project
     * 
     * @param  integer $projectId
     * @param  integer $roleId
     * @param  array   $params
     * @return Resources\Role|boolean 
     */
    public function getRole($projectId, $roleId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($roleId)) {
            throw new Exception\InvalidArgumentException("You did not specify the id of the role");
        }
        $result = $this->httpCall("/projects/$projectId/roles/$roleId" . 
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $role = json_decode($result->getBody(),true);
        if (!empty($role) && is_array($role)) {
            $role['projectId'] = $projectId;
            return new Resources\Role($this, $role);
        }
        return false;
    }

    /**
     * Get invites of the project
     *  
     * @param  integer $id
     * @param  array   $params
     * @return Container 
     */
    public function getInvites($projectId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        $result = $this->httpCall("/projects/$projectId/invites" . 
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        } 
        $invites = json_decode($result->getBody(),true);
        if (is_array($invites) && !empty($invites['items'])) {
            return new Container($this, $invites['items'], 'invite', $projectId);
        } 
        return false;    
    }

    /**
     * Get an invite of a project
     * 
     * @param  integer $projectId
     * @param  integer $inviteId
     * @param  array   $params
     * @return Resources\Invite|boolean 
     */
    public function getInvite($projectId, $inviteId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($inviteId)) {
            throw new Exception\InvalidArgumentException("You did not specify the id of the invite");
        }
        $result = $this->httpCall("/projects/$projectId/invites/$inviteId",'GET');
        if ($result===false) {
            return false;
        }
        $invite = json_decode($result->getBody(),true);
        if (is_array($invite) && !empty($invite)) {
            $invite['projectId'] = $projectId;
            return new Resources\Invite($this, $invite);
        }
        return false;    
    }

    /**
     * Get the tasks of a story
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  array   $params
     * @return Container|boolean
     */
    public function getTasks($projectId, $storyId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/tasks" . 
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $tasks = json_decode($result->getBody(),true);
        if (is_array($tasks['items']) && !empty($tasks['items'])) {
            return new Container($this, $tasks['items'], 'task', $projectId);
        }
        return false;
    }

    /**
     * Get the task of a story
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  integer $taskId
     * @param  array   $params
     * @return Resources\Task|boolean
     */
    public function getTask($projectId, $storyId, $taskId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($taskId)) {
            throw new Exception\InvalidArgumentException("You did not specify the id of the task");
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/tasks/$taskId" .
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $task = json_decode($result->getBody(),true);
        if (is_array($task) && !empty($task)) {
            $task['projectId'] = $projectId;
            return new Resources\Task($this, $task);
        }
        return false;
    }

    /**
     * Add a task to a story
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  array $data valid keys are 'text' (required) and 'status'
     * @return Resources\Task|boolean
     */
    public function addTask($projectId, $storyId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['text'])) {
            throw new Exception\InvalidArgumentException("You did not specify the text key in data");
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/tasks",'POST',$data);
        if ($result===false) {
            return false;
        }
        $task = json_decode($result->getBody(),true);
        if (is_array($task) && !empty($task)) {
            $task['projectId'] = $projectId;
            return new Resources\Task($this, $task);
        }
        return false;   
    }

    /**
     * Update a task of a story
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  integer $taskId
     * @param  array $data valid keys are 'text' (required) and 'status'
     * @return Resources\Task|boolean 
     */
    public function updateTask($projectId, $storyId, $taskId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($taskId)) {
            throw new Exception\InvalidArgumentException("You did not specify the id of the task");
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['text'])) {
            throw new Exception\InvalidArgumentException("You did not specify the text key in data");
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/tasks/$taskId",'PUT',$data);
        if ($result===false) {
            return false;
        }
        $task = json_decode($result->getBody(),true);
        if (is_array($task) && !empty($task)) {
            $task['projectId'] = $projectId;
            return new Resources\Task($this, $task);
        }
        return false;
    }

    /**
     * Remove a task from a story
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  integer $taskId
     * @return boolean 
     */
    public function removeTask($projectId, $storyId, $taskId)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($taskId)) {
            throw new Exception\InvalidArgumentException("You did not specify the id of the task");
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/tasks/$taskId",'DELETE');
        return ($result!==false);
    }

    /**
     * Add a story to a project
     * 
     * @param  integer $projectId
     * @param  array $data
     * @param  aray  $params
     * @return Resources\Story|boolean
     */
    public function addStory($projectId, $data, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['text'])) {
            throw new Exception\InvalidArgumentException("You did not specify the text key in data");
        }
        $result = $this->httpCall("/projects/$projectId/stories" . 
                $this->getUrlParameters($params), 'POST', $data);
        if ($result===false) {
            return false;
        }
        $story = json_decode($result->getBody(),true);
        if (is_array($story) && !empty($story)) {
            $story['projectId'] = $projectId;
            return new Resources\Story($this, $story);
        }
        return false;
            
    }

    /**
     * Update a story
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  array $data
     * @return Resources\Story|boolean
     */
    public function updateStory($projectId, $storyId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['text'])) {
            throw new Exception\InvalidArgumentException("You did not specify the text key in data");
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId", 'PUT', $data);
        if ($result===false) {
            return false;
        }
        $story = json_decode($result->getBody(),true);
        if (is_array($story) && !empty($story)) {
            $story['projectId'] = $projectId;
            return new Resources\Story($this, $story);
        }
        return false;
            
    }

    /**
     * Remove a story
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @return boolean 
     */
    public function removeStory($projectId, $storyId)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId", 'DELETE');
        return ($result!==false);
    }

    /**
     * Add a role
     * 
     * @param  integer $projectId
     * @param  array $data
     * @return Resources\Role|boolean
     */
    public function addRole($projectId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['name'])) {
            throw new Exception\InvalidArgumentException(self::ERR_NAME_KEY);
        }
        $result = $this->httpCall("/projects/$projectId/roles", 'POST', $data);
        if ($result===false) {
            return false;
        }
        $role = json_decode($result->getBody(),true);
        if (is_array($role) && !empty($role)) {
            $role['projectId'] = $projectId;
            return new Resources\Role($this, $role);
        }
        return false;   
    }

    /**
     * Update a role
     * 
     * @param  integer $projectId
     * @param  integer $roleId
     * @param  array $data
     * @return Resources\Role|boolean 
     */
    public function updateRole($projectId, $roleId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($roleId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_ROLE);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['name'])) {
            throw new Exception\InvalidArgumentException(self::ERR_NAME_KEY);
        }
        $result = $this->httpCall("/projects/$projectId/roles/$roleId", 'PUT', $data);
        if ($result===false) {
            return false;
        }
        $role = json_decode($result->getBody(),true);
        if (is_array($role) && !empty($role)) {
            $role['projectId'] = $projectId;
            return new Resources\Role($this, $role);
        }
        return false; 
    }

    /**
     * Remove a role
     * 
     * @param  integer $projectId
     * @param  integer $roleId
     * @return boolean 
     */
    public function removeRole($projectId, $roleId)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($roleId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_ROLE);
        }
        $result = $this->httpCall("/projects/$projectId/roles/$roleId", 'DELETE');
        return ($result!==false);
    }

    /**
     * Add a phase
     * 
     * @param  integer $projectId
     * @param  array $data
     * @return Resources\Phase|boolean
     */
    public function addPhase($projectId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['name'])) {
            throw new Exception\InvalidArgumentException(self::ERR_NAME_KEY);
        }
        if (!isset($data['description'])) {
            throw new Exception\InvalidArgumentException("You did not specify the description key in data");
        }
        $result = $this->httpCall("/projects/$projectId/phases", 'POST', $data);
        if ($result===false) {
            return false;
        }
        $phase = json_decode($result->getBody(),true);
        if (is_array($phase) && !empty($phase)) {
            $phase['projectId'] = $projectId;
            return new Resources\Phase($this, $phase);
        }
        return false;    
    }

    /**
     * Update a phase
     * 
     * @param  integer $projectId
     * @param  integer $phaseId
     * @param  array $data
     * @return Resources\Phase|boolean 
     */
    public function updatePhase($projectId, $phaseId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($phaseId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PHASE);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['name'])) {
            throw new Exception\InvalidArgumentException(self::ERR_NAME_KEY);
        }
        $result = $this->httpCall("/projects/$projectId/phases/$phaseId", 'PUT', $data);
        if ($result===false) {
            return false;
        }
        $phase = json_decode($result->getBody(),true);
        if (is_array($phase) && !empty($phase)) {
            $phase['projectId'] = $projectId;
            return new Resources\Phase($this, $phase);
        }
        return false;   
    }

    /**
     * Remove a phase
     * 
     * @param  integer $projectId
     * @param  integer $phaseId
     * @return boolean 
     */
    public function removePhase($projectId, $phaseId)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($phaseId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PHASE);
        }
        $result = $this->httpCall("/projects/$projectId/phases/$phaseId", 'DELETE');
        return ($result!==false);
    }

    /**
     * Create a new invite
     * 
     * @param  integer $projectId
     * @param  array $data
     * @return Resources\Invite|boolean
     */
    public function addInvite($projectId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['email'])) {
            throw new Exception\InvalidArgumentException(self::ERR_EMAIL_KEY);
        }
        if (!isset($data['role'])) {
            throw new Exception\InvalidArgumentException(self::ERR_ROLEID_KEY);
        }
        $result = $this->httpCall("/projects/$projectId/invites", 'POST', $data);
        if ($result===false) {
            return false;
        }
        $invite = json_decode($result->getBody(),true);
        if (is_array($invite) && !empty($invite)) {
            $invite['projectId'] = $projectId;
            return new Resources\Invite($this, $invite);
        }
        return false;
            
    }

    /**
     * Remove an invite
     * 
     * @param  integer $projectId
     * @param  integer $inviteId
     * @return boolean 
     */
    public function removeInvite($projectId, $inviteId)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($inviteId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_INVITE);
        }
        $result = $this->httpCall("/projects/$projectId/invites/$inviteId", 'DELETE');
        return ($result!==false);
    }

    /**
     * Get a tag
     * 
     * @param  integer $projectId
     * @param  integer $tagId
     * @param  array   $params
     * @return Resources\Tag|boolean 
     */
    public function getTag($projectId, $tagId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($tagId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_TAG);
        }
        $result = $this->httpCall("/projects/$projectId/tags/$tagId" . 
                $this->getUrlParameters($params), 'GET');
        if ($result===false) {
            return false;
        } 
        $tag = json_decode($result->getBody(),true);
        if (is_array($tag) && !empty($tag)) {
            $tag['projectId'] = $projectId;
            return new Resources\Tag($this, $tag);
        }
        return false; 
    }

    /**
     * Get the tags of a project
     * 
     * @param  integer $projectId
     * @param  array   $params
     * @return Container 
     */
    public function getTags($projectId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        $result = $this->httpCall("/projects/$projectId/tags" . $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $tags = json_decode($result->getBody(),true);
        if (is_array($tags) && !empty($tags['items'])) {
            return new Container($this, $tags['items'], 'tag', $projectId);
        } 
        return false;    
    }

    /**
     * Add a tag
     * 
     * @param  integer $projectId
     * @param  array $data
     * @return Resources\Tag 
     */
    public function addTag($projectId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['name'])) {
            throw new Exception\InvalidArgumentException(self::ERR_NAME_KEY);
        }
        $result = $this->httpCall("/projects/$projectId/tags", 'POST', $data);
        if ($result===false) {
            return false;
        }
        $tag = json_decode($result->getBody(),true);
        if (is_array($tag) && !empty($tag)) {
            $tag['projectId'] = $projectId;
            return new Resources\Tag($this, $tag);
        }
        return false;
    }

    /**
     * Update a tag
     * 
     * @param  integer $projectId
     * @param  array $data
     * @return Resources\Task 
     */
    public function updateTag($projectId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['name'])) {
            throw new Exception\InvalidArgumentException(self::ERR_NAME_KEY);
        }
        $result = $this->httpCall("/projects/$projectId/tasks", 'PUT', $data);
        if ($result===false) {
            return false;
        }
        $task = json_decode($result->getBody(),true);
        if (is_array($task) && !empty($task)) {
            $task['projectId'] = $projectId;
            return new Resources\Task($this, $task);
        }
        return false;        
    }

    /**
     * Remove a tag
     * 
     * @param  integer $projectId
     * @param  integer $tagId
     * @return boolean 
     */
    public function removeTag($projectId, $tagId)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($tagId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_TAG);
        }
        $result = $this->httpCall("/projects/$projectId/tags/$tagId", 'DELETE');
        return ($result!==false);
    }

    /**
     * Get an attachment
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  integer $attachId
     * @param  array   $params
     * @return Resources\Attachment|boolean 
     */
    public function getAttachment($projectId, $storyId, $attachId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($attachId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_ATTACH);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/attachments/$attachId" . 
                $this->getUrlParameters($params), 'GET');
        if ($result===false) {
            return false;
        }
        $attach = json_decode($result->getBody(),true);
        if (is_array($attach) && !empty($attach)) {
            $attach['projectId'] = $projectId;
            return new Resources\Attachment($this, $attach);
        }
        return false;       
    }

    /**
     * Get the attachments of a story
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  array   $params
     * @return Container 
     */
    public function getAttachments($projectId, $storyId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/attachments" .
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $attachments = json_decode($result->getBody(),true);
        if (is_array($attachments) && !empty($attachments['items'])) {
            return new Container($this, $attachments['items'], 'attachment', $projectId);
        } 
        return false;    
    }

    /**
     * Add one or more files (attachments)
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  array $data
     * @return Container 
     */
    public function addAttachment($projectId, $storyId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        foreach ($data as $file) {
            $this->getHttpClient()->setFileUpload($file,'attachment');
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/attachments", 'POST');
        if ($result===false) {
            return false;
        }
        $attachments = json_decode($result->getBody(),true);
        if (is_array($attachments) && !empty($attachments['items'])) {
            return new Container($this, $attachments['items'], 'attachment', $projectId);
        } 
        return false;   
    }

    /**
     * Update the filename of an attachment
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  integer $attachId
     * @param  array $data
     * @return Resources\Attachment
     */
    public function updateAttachment($projectId, $storyId, $attachId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($attachId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_ATTACH);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['filename'])) {
            throw new Exception\InvalidArgumentException(self::ERR_FILE_KEY);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/attachments/$attachId", 'PUT', $data);
        if ($result===false) {
            return false;
        }
        $attachment = json_decode($result->getBody(),true);
        if (is_array($attachment) && !empty($attachment)) {
            $attachment['projectId'] = $projectId;
            return new Resources\Attachment($this, $attachment);
        }
        return false;    
    }

    /**
     * Remove an attachment
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  integer $attachId
     * @return boolean 
     */
    public function removeAttachment($projectId, $storyId, $attachId)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($attachId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_ATTACH);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/attachments/$attachId", 'DELETE');
        return ($result!==false);
    }

    /**
     * Get a comment
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  integer $commentId
     * @param  array   $params
     * @return Resources\Comment|boolean 
     */
    public function getComment($projectId, $storyId, $commentId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($commentId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_COMMENT);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/comments/$commentId" . 
                $this->getUrlParameters($params), 'GET');
        if ($result===false) {
            return false;
        }
        $comment = json_decode($result->getBody(),true);
        if (is_array($comment) && !empty($comment)) {
            $comment['projectId'] = $projectId;
            return new Resources\Comment($this, $comment);
        }
        return false; 
    }

    /**
     * Get the comments of a story
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  array   $params
     * @return Container 
     */
    public function getComments($projectId, $storyId, $params=array())
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/comments" .
                $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $comments = json_decode($result->getBody(),true);
        if (is_array($comments) && !empty($comments['items'])) {
            return new Container($this, $comments['items'], 'comment', $projectId);
        } 
        return false;   
    }

    /**
     * Add a comment
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  array $data
     * @return Container 
     */
    public function addComment($projectId, $storyId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['text'])) {
            throw new Exception\InvalidArgumentException(self::ERR_TEXT_KEY);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/comments", 'POST', $data);
        if ($result===false) {
            return false;
        }
        $comment = json_decode($result->getBody(),true);
        if (is_array($comment) && !empty($comment)) {
            $comment['projectId'] = $projectId;
            return new Resources\Comment($this, $comment);
        } 
        return false;    
    }

    /**
     * Update a comment
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  integer $commentId
     * @param  array $data
     * @return Resources\Attachment
     */
    public function updateComment($projectId, $storyId, $commentId, $data)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($commentId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_COMMENT);
        }
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        if (!isset($data['text'])) {
            throw new Exception\InvalidArgumentException(self::ERR_TEXT_KEY);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/comments/$commentId", 'PUT', $data);
        if ($result===false) {
            return false;
        }
        $comment = json_decode($result->getBody(),true);
        if (is_array($comment) && !empty($comment)) {
            $comment['projectId'] = $projectId;
            return new Resources\Comment($this, $comment);
        }
        return false;  
    }

    /**
     * Remove a comment
     * 
     * @param  integer $projectId
     * @param  integer $storyId
     * @param  integer $commentId
     * @return boolean 
     */
    public function removeComment($projectId, $storyId, $commentId)
    {
        if (empty($projectId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_PROJECT);
        }
        if (empty($storyId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_STORY);
        }
        if (empty($commentId)) {
            throw new Exception\InvalidArgumentException(self::ERR_ID_COMMENT);
        }
        $result = $this->httpCall("/projects/$projectId/stories/$storyId/comments/$commentId", 'DELETE');
        return ($result!==false);
    }

    /**
     * Get information about Me
     * 
     * @return Resources\User|boolean
     */
    public function getMe()
    {
        $result = $this->httpCall("/me", 'GET');
        if ($result===false) {
            return false;
        }
        $me = json_decode($result->getBody(),true);
        if (is_array($me) && !empty($me)) {
            return new Resources\User($this, $me);
        }
        return false;   
    }

    /**
     * Update information about Me
     * 
     * @param  array $data
     * @return Resources\User|boolean
     */
    public function updateMe($data)
    {
        if (empty($data) || !is_array($data)) {
            throw new Exception\InvalidArgumentException(self::ERR_DATA);
        }
        $result = $this->httpCall("/me", 'PUT', $data);
         if ($result===false) {
            return false;
        }
        $me = json_decode($result->getBody(),true);
        if (is_array($me) && !empty($me)) {
            return new Resources\User($this, $me);
        }
        return false;
    }

    /**
     * Get my stories
     * 
     * @param  array $params
     * @return Container|boolean
     */
    public function getMyStories($params=array())
    {
        $result = $this->httpCall("/me/stories" . $this->getUrlParameters($params),'GET');
        if ($result===false) {
            return false;
        }
        $stories = json_decode($result->getBody(),true);
        if (is_array($stories) && !empty($stories['items'])) {
            return new Container($this, $stories['items'], 'story');
        } 
        return false;    
    }

    /**
     * Get the error message from the HTML body of the response
     * 
     * @param  \Zend\Http\Response $response 
     * @return string
     */
    protected function getErrorFromResponse($response) 
    {
        $dom = new DOMDocument;
        $dom->loadHTML($response->getBody());
        $title = $dom->getElementsByTagName('title')->item(0);
        if (!empty($title)) {
            $msg = $title->nodeValue;
            $h1 = $dom->getElementsByTagName('h1')->item(0);
            if (!empty($h1)) {
                $msg.= ' : '. $h1->nodeValue;
            }
            return $msg;
        }
        return self::ERR_UNKNOWN;
    }
    /**
     * Format the URL with the parameters filter, enrichments, and pagination
     * 
     * @param  array $params
     * @return string 
     */
    protected function getUrlParameters($params)
    {
        if (empty($params)) {
            return null;
        }
        if (!is_array($params)) {
            throw new Exception\InvalidArgumentException('You didn\'t specify the array of parameters');
        }
        $validKeys = array ('where', 'with', 'page', 'pageSize');
        $url = '?';
        foreach ($validKeys as $key) {
             if (isset($params[$key])) { 
                 if ($url!=='?') {
                     $url.= '&';
                 }
                 $url.= $key . '=' . urlencode($params[$key]);
             }
        }
        if ($url==='?') {
            return null;
        }
        return $url;
    }
}
