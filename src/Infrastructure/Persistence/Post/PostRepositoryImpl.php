<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use App\Domain\Post\Post;
use App\Domain\Post\PostCouldNotBeCreatedException;
use App\Domain\Post\PostNotFoundException;
use App\Domain\Post\PostRepository;
use App\Infrastructure\Connection;
use PDO;
use PDOException;

class PostRepositoryImpl implements PostRepository
{
    private ?PDO $db = null;

    private ?string $dateNow = null;

    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->dateNow = now();
    }

    /** {@inheritdoc} */
    public function findPostOfId(int $id): array
    {
        $post = $this->db->query(
            "SELECT image_url, description, user_id FROM posts WHERE id = {$id}"
        )->fetch(PDO::FETCH_ASSOC);

        if (false == $post) {
            throw new PostNotFoundException();
        }
        return $post;
    }

    /** {@inheritdoc} */
    public function listPostsBy(int $userId): array
    {
        $posts = $this->db->query(
            "SELECT posts.id, posts.image_url, posts.description, posts.user_id, posts.created_at, users.username FROM posts INNER JOIN users ON user_id = users.id WHERE EXISTS (SELECT * FROM followers WHERE followed_user = user_id AND following_user = {$userId});"
        )->fetchAll(PDO::FETCH_ASSOC);

        return $posts ? $posts : [];
    }

    /** {@inheritdoc} */
    public function store(Post $post): bool
    {
        try {
            $createUserQuery = $this->db->prepare("
            INSERT INTO posts (image_url, description, user_id, created_at, updated_at)
             VALUES (:i, :d, :u, '{$this->dateNow}', '{$this->dateNow}')
            ");
            $createUserQuery->bindValue(':i', $post->getImageUrl());
            $createUserQuery->bindValue(':d', $post->getDescription());
            $createUserQuery->bindValue(':u', $post->getUserId());
            $createUserQuery->execute();

            return true;
        } catch(PDOException $e) {
            throw new PostCouldNotBeCreatedException($e->getMessage());
        }
    }

    /** {@inheritdoc} */
    public function destroy(int $id): bool
    {
        return true;
    }
}