<?php

// CommentSecurityCheckTest

use MFissehaye\CommentSecurityCheck\CommentSecurityCheck;

class CommentSecurityCheckTest extends PHPUnit_Framework_TestCase {

    public function testCommentSecurityCheckHasCheese() {
        $csc = new CommentSecurityCheck();
        $this->assertTrue($csc->hasCheese());
    }
}