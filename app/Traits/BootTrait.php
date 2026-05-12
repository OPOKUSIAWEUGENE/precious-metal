<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


trait BootTrait
{


    /**
     * built in function in laravel
     */
    protected static function bootBootTrait()
    {
        /**
         * boot other traitnns on model
         */
        static::creating(function ($model) {
            /**
             * assign an ordered uuid string to model
             */
            if ($model->getKey() === null) {
                $model->setAttribute($model->getKeyName(), Str::orderedUuid()->toString());
            }
            /**
             * if password column exists, hash password
             */
            // if (Schema::hasColumn($model->getTable(), 'password')) {
            //     $model->password = Hash::make($model->password);
            // }
         

            /**
             * check if user_id column exists| store current user's id
             */
            if (Schema::hasColumn($model->getTable(), 'added_by')) {
                $model->added_by = Auth::id();
            }
            // if (Schema::hasColumn($model->getTable(), 'user_id')) {
            //     $model->user_id = Auth::id();
            // }

            /**
             * check if created_by column exists| store current user's id
             */

            //    if (Schema::hasColumn($model->getTable(), 'created_by')) {
            //         $model->created_by = user::profileId(auth()->id());
            //     }
            //     if (Schema::hasColumn($model->getTable(), 'added_by')) {
            //         $model->added_by = User::profileId(auth()->id());
            //     }

            // /**
            //  * check if school_code column exists| generate a unique school code
            //  */
            // if (!$inst_code) {

            //     $model->institution_code=InstCodeGenerator::InstCode($model->name);
            // }
        });


        static::updating(function ($model) {

            // /**
            //  * if password column exists, hash password
            //  */
            // if (Schema::hasColumn($model->getTable(), 'password')) {          
            //     $model->password = Hash::make($model->password);
            // }

            /**
             * if account_pin column exists, hash password
             */
            // if (Schema::hasColumn($model->getTable(), 'account_pin')) {          
            //     $model->account_pin = Hash::make($model->account_pin);
            // }
        });
    }

    /**
     * Tell database not to auto increment this column
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * get the auto-incrementing key type
     */
    public function getKeyType()
    {
        return "string";
    }

}