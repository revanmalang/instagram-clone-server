<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Domain\Models\User;
use Psr\Http\Message\ResponseInterface as Response;

class CreateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        [
            'username' => $username,
            'email' => $email,
            'name' => $name,
            'password' => $password
        ] = $this->request->getParsedBody();

        $user = new User($username, $email, $name, $password);
        $userId = $this->userRepository->store($user);
        $createdUser = $this->userRepository->findUserOfId($userId);

        $this->logger->info("New User was created.");

        return $this->respondWithData($createdUser);
    }
}
