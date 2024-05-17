<?php
/**
 * Author: Amir Hossein Jahani | iAmir.net
 * Last modified: 10/12/20, 11:02 PM
 * Copyright (c) 2021. Powered by iamir.net
 */

Route::namespace('v1')->prefix('v1/wx')->middleware('auth:api')->group(function () {
    Route::get('{src}/single/{longitude}/{latitude}/{level?}', 'WXController@single')->name('api.iwx.single');
    Route::get('{src}/range/{level?}', 'WXController@range')->name('api.iwx.range');
    Route::post('{src}/multi/{level?}', 'WXController@multi')->name('api.iwx.multi');
    Route::get('{src}/section/{section}/{level?}', 'WXController@section')->name('api.iwx.section');
    Route::get('gfs/job/delete', function () {
        return \iLaravel\iWX\Vendor\Classes\JobGFS::delete();
    })->name('api.iwx.job.delete');
//    Route::get('gfs/job/run', function () {
//        return \iLaravel\iWX\Vendor\Classes\JobGFS::run(/*null, 0,null, null, $degree = '1.00'*/);
//    })->name('api.iwx.job.run');
//    Route::get('ecmwf/job/delete', function () {
//        return \iLaravel\iWX\Vendor\Classes\JobECMWF::delete();
//    })->name('api.iwx.job.delete');
//    Route::get('ecmwf/job/run', function () {
//        return \iLaravel\iWX\Vendor\Classes\JobECMWF::run(/*null, 0,null, null, $degree = '1.00'*/);
//    })->name('api.iwx.job.run');
});

Route::namespace('v1')->prefix('v1/wx')->group(function () {
    /*Route::get('gfs/job/delete', function () {
        return \iLaravel\iWX\Vendor\Classes\JobGFS::delete();
    })->name('api.iwx.job.delete');*/
//    Route::get('ecmwf/job/delete', function () {
//        return \iLaravel\iWX\Vendor\Classes\JobECMWF::delete();
//    })->name('api.iwx.job.ecmwf.delete');
//    Route::get('gfs/job/run', function () {
//        return \iLaravel\iWX\Vendor\Classes\JobGFS::run(/*null, 0,null, null, $degree = '1.00'*/);
//    })->name('api.iwx.job.run');
//    Route::get('gfs/job/run', function () {
//        return \iLaravel\iWX\Vendor\Classes\JobGFS::run(/*null, 0,null, null, $degree = '1.00'*/);
//    })->name('api.iwx.job.run');
//    Route::get('ecmwf/job/delete', function () {
//        return \iLaravel\iWX\Vendor\Classes\JobECMWF::delete();
//    })->name('api.iwx.job.delete');
//    Route::get('ecmwf/job/run', function () {
//        return \iLaravel\iWX\Vendor\Classes\JobECMWF::run(/*null, 0,null, null, $degree = '1.00'*/);
//    })->name('api.iwx.job.run');
});
