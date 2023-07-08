<?php

test('example', function () {
    expect(true)->toBeTrue();
});

expect(fn() => throw new Exception('Something happened.'))->toThrow(Exception::class);