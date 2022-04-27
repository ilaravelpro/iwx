<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 10/12/20, 11:02 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

Route::namespace('v1')->prefix('v1/wx')->middleware('auth:api')->group(function () {
    Route::get('gfs/single/{longitude}/{latitude}/{level?}', 'WXGFSController@single')->name('api.iwx.gfs.single');
    Route::get('gfs/range/{level?}', 'WXGFSController@range')->name('api.iwx.gfs.range');
    Route::post('gfs/multi/{level?}', 'WXGFSController@multi')->name('api.iwx.gfs.multi');
    Route::get('gfs/section/{section}/{level?}', 'WXGFSController@section')->name('api.iwx.gfs.section');
    /*Route::get('gfs/job/delete', function () {
        return \iLaravel\iWX\Vendor\Classes\Job::delete();
    })->name('api.iwx.job.delete');*/
//    Route::get('gfs/job/run', function () {
//        return \iLaravel\iWX\Vendor\Classes\Job::run(/*null, 0,null, null, $degree = '1.00'*/);
//    })->name('api.iwx.job.run');
});
