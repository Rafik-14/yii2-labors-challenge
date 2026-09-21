<?php

declare(strict_types=1);

namespace app\models;

use yii\base\BaseObject;
use yii\web\IdentityInterface;

/**
 * Deprecated compatibility stub.
 * This project intentionally does not expose a Yii identity-based login flow.
 */
class User extends BaseObject implements IdentityInterface
{
    public int|string $id = '';
    public ?string $username = null;

    public static function findIdentity($id): ?static
    {
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null): ?static
    {
        return null;
    }

    public static function findByUsername(string $username): ?static
    {
        return null;
    }

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getAuthKey(): ?string
    {
        return null;
    }

    public function validateAuthKey($authKey): bool
    {
        return false;
    }
}
