<?php
/**
 * Created by PhpStorm.
 * User: ramy
 * Date: 31/12/14
 * Time: 15:36
 */

namespace Tafrika\UserBundle\Tests;

use Tafrika\UserBundle\Entity\User;
class UserTest extends \PHPUnit_Framework_TestCase {

    public function testSubscription(){
        $u1 = new User();
        $u1->setEmail("test1@test.com");
        $u1->setUsername("test1");
        $u2 = new User();
        $u2->setEmail("test2@test.com");
        $u2->setUsername("test2");
        $u3 = new User();
        $u3->setEmail("test3@test.com");
        $u3->setUsername("test3");
        $u4 = new User();
        $u4->setEmail("test4@test.com");
        $u4->setUsername("test4");
        $this->assertFalse($u1->isFollowed($u2));
        $u1->addfollowed($u2);
        $this->assertTrue($u1->getfollowed()->contains($u2));
        $this->assertTrue($u2->getfollowers()->contains($u1));
    }
}