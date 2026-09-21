<?php

declare(strict_types=1);

namespace app\tests\Unit\Models;

use app\models\User;

/**
 * The project intentionally ships without a login flow: User is a stub that never resolves an identity.
 */
final class UserTest extends \Codeception\Test\Unit
{
    public function testNoIdentityCanBeFound()
    {
        verify(User::findIdentity(100))->null();
        verify(User::findIdentityByAccessToken('100-token'))->null();
        verify(User::findByUsername('admin'))->null();
    }

    public function testAuthKeyIsNeverValid()
    {
        $user = new User(['id' => 1, 'username' => 'admin']);

        verify($user->getAuthKey())->null();
        verify($user->validateAuthKey('test100key'))->false();
    }
}
