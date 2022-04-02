<?php

namespace Tests\Unit;

use Tests\TestCase;


class BitcoinapiTest extends TestCase
{
   

    public function testInvalidRoute()
    {
        $this->get('/getGitcoinInfo')
             ->assertJson([
                 'result'   => false,                               
             ]);
    }

    public function testCallingWithoutParameter()
    {
        $this->get('/getBitcoinInfo')
             ->assertJson([
                 'result'   => false,                               
             ]);
    }

    public function testCallingWithWrongParameter()
    {
        $this->get('/getBitcoinInfocurrency_code=bdt')
             ->assertJson([
                 'result'   => false,                               
             ]);
    }

    public function testBasicExample()
    {
        $this->get('/getBitcoinInfo?currency_code=usd')
             ->assertJson([
                 'result'   => true,                               
             ]);
    }

    
}
