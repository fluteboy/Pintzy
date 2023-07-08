<?php

require_once("startup.php");

Use App\Models\PintzyUserInfo;
use App\Foundation;
use App\Exceptions\ModelDestroyedException;

/**
 * This suite of tests verify that the models are loaded OK
 */


 /**
  * Flushes out all users so that fresh tests can be performed
  */
function flushTable()
{
    $db = Foundation::db();

    $db->execute("delete from pintzy_user_info");

    $db->commit();
}

function createNewModel(string $username, string $email): PintzyUserInfo
{
    $pintzyUser = new PintzyUserInfo();
    $pintzyUser->user_name = $username;
    $pintzyUser->user_email = $email;
    $pintzyUser->user_password = PintzyUserInfo::hashPassword("password123");
    $pintzyUser->save();

    return $pintzyUser;
}

flushTable();

test("simple Model Creation", function () {
    $model = createNewModel("modelCreate", "modelCreate@pintzy.com");

    expect($model)->toBeInstanceOf(PintzyUserInfo::class);
    expect($model->primaryKey())->toBeNumeric();
    expect($model->user_name)->toBe("modelCreate");
    expect($model->user_email)->toBe("modelCreate@pintzy.com");
});

test("Create Model And Update", function () {
    $model = createNewModel("modelCreate", "modelCreate@pintzy.com");

    expect($model)->toBeInstanceOf(PintzyUserInfo::class);

    $model->user_name = "modelUpdate";

    expect($model->requiresSaving())->toBeTrue();

    $model->save();

    expect($model->requiresSaving())->toBeFalse();
});

test("Create and Delete, ensuring that nothing further can be done to the model", function () {

    $model = createNewModel("modelCreate", "modelCreate@pintzy.com");

    expect($model)->toBeInstanceOf(PintzyUserInfo::class);

    $model->destroy();

    try {
        $model->user_email = "test";
    } catch (Exception $e){
        expect($e)->ToBeInstanceOf(ModelDestroyedException::class);
    }
});