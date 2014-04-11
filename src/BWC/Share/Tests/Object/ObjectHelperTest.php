<?php

namespace BWC\Share\Tests\Object;

use BWC\Share\Object\ObjectHelper;

class ObjectHelperTest extends \PHPUnit_Framework_TestCase
{
    function testSimpleFromArray() {
        $from = array(
            'id' => 1,
            'name' => 'name',
            'email' => 'email'
        );
        $user = new User();
        ObjectHelper::copyExistingProperties($from, $user);
        $this->assertEquals($from['id'], $user->id);
        $this->assertEquals($from['name'], $user->name);
        $this->assertEquals($from['email'], $user->email);
    }


    function testSimpleFromObject() {
        $from = (object)array(
            'id' => 1,
            'name' => 'name',
            'email' => 'email'
        );
        $user = new User();
        ObjectHelper::copyExistingProperties($from, $user);
        $this->assertEquals($from->id, $user->id);
        $this->assertEquals($from->name, $user->name);
        $this->assertEquals($from->email, $user->email);
    }


    function testPrefixOneFromArray() {
        $from = array(
            'id' => 1,
            'name' => 'name',
            'email' => 'email',
            'g_id' => 2,
            'g_name' => 'group_name'
        );
        $user = new User();
        $group = new Group();
        ObjectHelper::copyExistingProperties($from, $user);
        ObjectHelper::copyExistingProperties($from, $group, 'g_');
        $this->assertEquals($from['id'], $user->id);
        $this->assertEquals($from['name'], $user->name);
        $this->assertEquals($from['email'], $user->email);
        $this->assertEquals($from['g_id'], $group->id);
        $this->assertEquals($from['g_name'], $group->name);
    }

    function testPrefixBothFromArray() {
        $from = array(
            'u_id' => 1,
            'u_name' => 'name',
            'u_email' => 'email',
            'g_id' => 2,
            'g_name' => 'group_name'
        );
        $user = new User();
        $group = new Group();
        ObjectHelper::copyExistingProperties($from, $user, 'u_');
        ObjectHelper::copyExistingProperties($from, $group, 'g_');
        $this->assertEquals($from['u_id'], $user->id);
        $this->assertEquals($from['u_name'], $user->name);
        $this->assertEquals($from['u_email'], $user->email);
        $this->assertEquals($from['g_id'], $group->id);
        $this->assertEquals($from['g_name'], $group->name);
    }


    function testPrefixOneFromObject() {
        $from = (object)array(
            'id' => 1,
            'name' => 'name',
            'email' => 'email',
            'g_id' => 2,
            'g_name' => 'group_name'
        );
        $user = new User();
        $group = new Group();
        ObjectHelper::copyExistingProperties($from, $user);
        ObjectHelper::copyExistingProperties($from, $group, 'g_');
        $this->assertEquals($from->id, $user->id);
        $this->assertEquals($from->name, $user->name);
        $this->assertEquals($from->email, $user->email);
        $this->assertEquals($from->g_id, $group->id);
        $this->assertEquals($from->g_name, $group->name);
    }

    function testPrefixBothFromObject() {
        $from = (object)array(
            'u_id' => 1,
            'u_name' => 'name',
            'u_email' => 'email',
            'g_id' => 2,
            'g_name' => 'group_name'
        );
        $user = new User();
        $group = new Group();
        ObjectHelper::copyExistingProperties($from, $user, 'u_');
        ObjectHelper::copyExistingProperties($from, $group, 'g_');
        $this->assertEquals($from->u_id, $user->id);
        $this->assertEquals($from->u_name, $user->name);
        $this->assertEquals($from->u_email, $user->email);
        $this->assertEquals($from->g_id, $group->id);
        $this->assertEquals($from->g_name, $group->name);
    }

    function testPrefixTreeFromArray() {
        $from = array(
            'id' => 1,
            'name' => 'name',
            'email' => 'email',
            'g_id' => 2,
            'g_name' => 'group_name',
            'r_id' => 3,
            'r_name' => 'role_name'
        );
        $user = new User();
        $group = new Group();
        $role = new Role();
        ObjectHelper::copyExistingProperties($from, $user);
        ObjectHelper::copyExistingProperties($from, $group, 'g_');
        ObjectHelper::copyExistingProperties($from, $role, 'r_');
        $this->assertEquals($from['id'], $user->id);
        $this->assertEquals($from['name'], $user->name);
        $this->assertEquals($from['email'], $user->email);
        $this->assertEquals($from['g_id'], $group->id);
        $this->assertEquals($from['g_name'], $group->name);
        $this->assertEquals($from['r_id'], $role->id);
        $this->assertEquals($from['r_name'], $role->name);
    }

    function testPrefixTreeFromObject() {
        $from = (object)array(
            'id' => 1,
            'name' => 'name',
            'email' => 'email',
            'g_id' => 2,
            'g_name' => 'group_name',
            'r_id' => 3,
            'r_name' => 'role_name'
        );
        $user = new User();
        $group = new Group();
        $role = new Role();
        ObjectHelper::copyExistingProperties($from, $user);
        ObjectHelper::copyExistingProperties($from, $group, 'g_');
        ObjectHelper::copyExistingProperties($from, $role, 'r_');
        $this->assertEquals($from->id, $user->id);
        $this->assertEquals($from->name, $user->name);
        $this->assertEquals($from->email, $user->email);
        $this->assertEquals($from->g_id, $group->id);
        $this->assertEquals($from->g_name, $group->name);
        $this->assertEquals($from->r_id, $role->id);
        $this->assertEquals($from->r_name, $role->name);
    }

}