<?php

declare(strict_types=1);

namespace app\tests\Acceptance;

use app\tests\Support\AcceptanceTester;

final class HomeCest
{
    public function ensureThatHomePageWorks(AcceptanceTester $I)
    {
        // the labors list is the home page
        $I->amOnPage('/');
        $I->see(\Yii::$app->name);
        $I->seeInTitle(\Yii::t('app', 'Labors'));
        $I->seeElement('.grid-view table');

        $I->seeLink(\Yii::t('app', 'Create Labors'));
        $I->click(\Yii::t('app', 'Create Labors'));
        $I->seeInTitle(\Yii::t('app', 'Create Labors'));
    }
}
