<?php

namespace App\Test\Json;

use App\Services\JsonService;

class JsonServiceTest extends \PHPUnit\Framework\TestCase
{
    /** @dataProvider theTests */
    public function test($json, $expect, $msg = '')
    {
        $this->assertSame($expect, (new JsonService())->fix($json), $msg);
    }

    public function test_invalid_literal()
    {
        $JsonService = (new JsonService())->silent(true);

        $this->assertSame('{"a" : invalid', $JsonService->fix('{"a" : invalid'));
        $this->assertSame(' hmm ', $JsonService->fix(' hmm '));
    }

    public function test_ws()
    {
        $this->assertSame('{ "a"  :null}', (new JsonService())->missingValue(null)->fix('{ "a"  :'));
        $this->assertSame("\n [{}]", (new JsonService())->fix("\n [{,"));
    }

    public function test_custom_missing()
    {
        $JsonService = new JsonService();
        $this->assertSame('{"a":false}', $JsonService->missingValue(false)->fix('{"a'));
        $this->assertSame('{"a":true}', $JsonService->missingValue('true')->fix('{"a":'));
        $this->assertSame('{"a":1,"b":"missing"}', $JsonService->missingValue('"missing"')->fix('{"a":1,"b"'));
    }

    public function test_fail_silent()
    {
        $this->assertSame('{"a"}', (new JsonService())->silent(true)->fix('{"a"}'));
    }

    public function test_fail_throws()
    {
        $this->expectException('\RuntimeException');
        $this->expectExceptionMessage('Could not fix JSON');

        (new JsonService())->silent(false)->fix('{,"a');
    }

    public function theTests()
    {
        return [[
            'json'   => '',
            'expect' => '',
        ], [
            'json'   => '"',
            'expect' => '""',
        ], [
            'json'   => '"a"',
            'expect' => '"a"',
        ], [
            'json'   => 'true',
            'expect' => 'true',
        ], [
            'json'   => 'false',
            'expect' => 'false',
        ], [
            'json'   => 'null',
            'expect' => 'null',
        ], [
            'json'   => 'fal',
            'expect' => 'false',
        ], [
            'json'   => 't',
            'expect' => 'true',
        ], [
            'json'   => 'nu',
            'expect' => 'null',
        ], [
            'json'   => '{',
            'expect' => '{}',
        ], [
            'json'   => '[',
            'expect' => '[]',
        ], [
            'json'   => '12.34',
            'expect' => '12.34',
        ], [
            'json'   => '"str',
            'expect' => '"str"',
        ], [
            'json'   => '[{',
            'expect' => '[{}]',
        ], [
            'json'   => '[1',
            'expect' => '[1]',
        ], [
            'json'   => '["',
            'expect' => '[""]',
        ], [
            'json'   => '[1,',
            'expect' => '[1]',
        ], [
            'json'   => '[1,{',
            'expect' => '[1,{}]',
        ], [
            'json'   => '["a',
            'expect' => '["a"]',
        ], [
            'json'   => '["b,',
            'expect' => '["b,"]',
        ], [
            'json'   => '["b",{"',
            'expect' => '["b",{"":null}]',
        ], [
            'json'   => '["b",{"a',
            'expect' => '["b",{"a":null}]',
        ], [
            'json'   => '["b",{"a":',
            'expect' => '["b",{"a":null}]',
        ], [
            'json'   => '["b",{"a":[t',
            'expect' => '["b",{"a":[true]}]',
        ], [
            'json'   => '{"a":2',
            'expect' => '{"a":2}',
        ], [
            'json'   => '{"a":',
            'expect' => '{"a":null}',
        ], [
            'json'   => '{"a"',
            'expect' => '{"a":null}',
        ], [
            'json'   => '{"',
            'expect' => '{"":null}',
        ], [
            'json'   => '{"a":1.2,',
            'expect' => '{"a":1.2}',
        ], [
            'json'   => '{"a":"',
            'expect' => '{"a":""}',
        ], [
            'json'   => '{"a":[',
            'expect' => '{"a":[]}',
        ], [
            'json'   => '{"a":"b","b":["',
            'expect' => '{"a":"b","b":[""]}',
        ], [
            'json'   => '{"a":"b","b":[t',
            'expect' => '{"a":"b","b":[true]}',
        ], [
            'json'   => '[ {"id":1, "data": []}, {"id":2, "data": [',
            'expect' => '[ {"id":1, "data": []}, {"id":2, "data": []}]',
        ],
        ];
    }
}