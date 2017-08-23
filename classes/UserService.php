<?php

require "User.php";
require "Repository.php";

class UserService
{
    protected $repositories = [];
    protected $users = [];
    protected $searchedRepos = [];
    protected $shortestHops = PHP_INT_MAX;
    protected $shortestPath;

    function __construct() {

        // Convert test json into class objects, for later user.
        $repos = json_decode(file_get_contents("data/repositories.json"), true);

        foreach ($repos as $repoId => $repoData) {
            $userData = $repoData['collaborators'];
            $repo = new Repository();
            $repo->setId($repoId);
            $repo->setCollaborators($userData);
            $this->repositories[$repoId] = $repo;

            foreach ($userData as $userId) {

                if (array_key_exists($userId, $this->users)) {
                    $user = $this->users[$userId];
                }else {
                    $user = new User();
                    $user->setId($userId);
                    $this->users[$userId] = $user;
                }

                $userRepos = $user->getRepositories();
                $userRepos[] = $repoId;

                $user->setRepositories($userRepos);
            }
        }
    }

    /**
     * Validate the query string params,
     * then get the shortest hops between users.
     *
     * @param array $params
     * @return mixed
     */
    public function handleRequest($params = []) {

        if (!array_key_exists('userIds', $params)) {
            return $this->createResponse("UserIds must be set", 400);
        }

        $userIds = explode(',', $params['userIds']);

        if (count($userIds) != 2) {
            return $this->createResponse("Please specify 2 userIds.", 400);
        }

        $this->findHopsBetweenUsers($userIds[0], $userIds[1]);

        if ($this->shortestHops == PHP_INT_MAX) {
            return $this->createResponse("No path found between users", 200);
        }

        $response['hops']  = $this->shortestHops;
        $response['route'] = $this->shortestPath;
        $response['code']  = 200;

        return $response;
    }

    /**
     * Find the shortest number of hops between Github repo's that the given
     * users has contributed too.
     *
     * @param $userId
     * @param $targetUserId
     * @param string $key
     */
    protected function findHopsBetweenUsers($userId, $targetUserId, $key = '') {

        // If the key contains the current user, we've already examined its paths.
        if (strpos($key, $userId) !== false) return;

        // This would normally be an Api Call to get the user.
        /** @var User $user */
        $user = $this->users[$userId];

        // Return if no user is found.
        if (is_null($user)) return;

        if (strlen($key)) {
            $key .= '->' . $userId;
        }else $key = $userId;

        foreach ($user->getRepositories() as $repoId) {

            // Skip any repos already searched.
            if (in_array($repoId, $this->searchedRepos)) continue;

            // This would normally be an Api Call to get the repository.
            /** @var Repository $repo */
            $repo = $this->repositories[$repoId];

            if (is_null($repo)) continue;

            $users = $repo->getCollaborators();

            if (in_array($targetUserId, $users)) {
                $hops = count(explode("->", $key));
                if ($hops < $this->shortestHops) {
                    $this->shortestHops = $hops;
                    $this->shortestPath = $key;
                }
                continue;
            }

            // Don't need to scan this repo again.
            $this->searchedRepos[] = $repoId;

            // Search each of the collaborators.
            foreach ($users as $collaboratorId) {

                // Exclude the current user.
                if ($collaboratorId === $userId) continue;

                // Call the search recursively, until all nodes have been searched.
                $this->findHopsBetweenUsers($collaboratorId, $targetUserId, $key);
            }
        }
    }

    /**
     * Create a simple response object.
     *
     * @param $message
     * @param int $code
     * @return string
     */
    protected function createResponse($message, $code = 200) {
        $response['message'] = $message;
        $response['code'] = $code;
        return $response;
    }
}
