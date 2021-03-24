<?php

Route::post('login', 'API\UserController@login');
Route::post('driver/login', 'API\DriverController@login');
Route::post('supervisor/login', 'API\SupervisorController@login');
Route::post('parent/login', 'API\ParentController@login');
Route::post('teacher/login', 'API\TeacherController@login');
Route::post('security/login', 'API\SecurityController@login');
Route::post('principal/login', 'API\PrincipalController@login');

Route::post('accountant/login', 'API\Accounting\UserController@login');
Route::post('accountant/parent/login', 'API\Accounting\ParentController@login');
Route::get('{any}', 'API\UserController@nologin')->where('any', '.*');
Route::get('/', 'API\UserController@nologin');

Route::post('bank/index', 'API\SectionController@index');
/* Route::group(['middleware' => 'auth:api'], function(){
Route::post('/details', 'API\DriverController@details');

}); */
Route::post('/logsystem', 'API\LogsystemController@logsystem');
//Route::post ('parent/resetpassword', 'API\ParentController@resetpassword');
//Route::post ('password/email', 'API\ForgotPasswordController@sendResetLinkEmail');
//Route::post('forgot/password', 'API\ForgotPasswordController')->name('forgot.password');

Route::middleware('AccountedApiToken')->group(function () {

    Route::post('user/detail', 'API\UserController@details');
    Route::post('parent/detail', 'API\Accounting\ParentController@details');
    Route::post('accounting/user/store', 'API\Accounting\UserManagement\UsersController@store');

    Route::post('accounting/roles/index', 'API\Accounting\UserManagement\RolesController@index');
    Route::post('accounting/roles/getallroles', 'API\Accounting\UserManagement\RolesController@getallroles');
    Route::post('accounting/role/store', 'API\Accounting\UserManagement\RolesController@store');
    Route::post('accounting/role/edit', 'API\Accounting\UserManagement\RolesController@edit');
    Route::post('accounting/role/update', 'API\Accounting\UserManagement\RolesController@update');
    Route::delete('accounting/role/delete', 'API\Accounting\UserManagement\RolesController@destroy');
    Route::post('accounting/role/change_status', 'API\Accounting\UserManagement\RolesController@changestatus');

    Route::post('accounting/permissions/index', 'API\Accounting\UserManagement\PermissionsController@index');

    Route::post('accounting/users/index', 'API\Accounting\UserManagement\UsersController@index');
    Route::post('accounting/users/getallusers', 'API\Accounting\UserManagement\UsersController@getusers');
    Route::post('accounting/user/edit', 'API\Accounting\UserManagement\UsersController@edit');
    Route::post('accounting/user/update', 'API\Accounting\UserManagement\UsersController@update');
    Route::delete('accounting/user/delete', 'API\Accounting\UserManagement\UsersController@destroy');
    Route::post('accounting/user/change_status', 'API\Accounting\UserManagement\UsersController@changestatus');
    Route::post('accounting/user/change_permissions', 'API\Accounting\UserManagement\UsersController@changepermissions');

    Route::post('accounting/user/changepassword', 'API\Accounting\UserController@changepassword');

    Route::post('accounting/accounts/index', 'API\Accounting\AccountsController@index');
    Route::post('accounting/accounts/getallaccounts', 'API\Accounting\AccountsController@getallaccounts');
    Route::post('accounting/accounts/getallaccountwithjvs', 'API\Accounting\AccountsController@getaccountswithjvs');
    Route::post('accounting/accounts/getallaccountscategory', 'API\Accounting\AccountsController@getallaccountscategory');
    Route::post('accounting/account/store', 'API\Accounting\AccountsController@store');
    Route::post('accounting/account/edit', 'API\Accounting\AccountsController@edit');
    Route::post('accounting/account/update', 'API\Accounting\AccountsController@update');
    Route::post('accounting/account/change_status', 'API\Accounting\AccountsController@changestatus');
    Route::post('accounting/account/delete', 'API\Accounting\AccountsController@destroy');

    Route::post('accounting/journals/index', 'API\Accounting\JournalVoucherController@index');
    Route::post('accounting/journals/getalljournals', 'API\Accounting\JournalVoucherController@getalljournals');
    Route::post('accounting/journals/getallJvs', 'API\Accounting\JournalVoucherController@getallJvs');
    Route::post('accounting/journal/store', 'API\Accounting\JournalVoucherController@store');
    Route::post('accounting/journal/addjvs', 'API\Accounting\JournalVoucherController@addjvs');
    Route::post('accounting/journal/upload_jvs', 'API\Accounting\JournalVoucherController@upload_jvs');
    Route::post('accounting/journal/update_jvs', 'API\Accounting\JournalVoucherController@update_jvs');
    Route::post('accounting/journal/edit', 'API\Accounting\JournalVoucherController@edit');
    Route::post('accounting/journal/journaledit', 'API\Accounting\JournalVoucherController@journaledit');
    Route::post('accounting/journal/getjournal', 'API\Accounting\JournalVoucherController@getjournal');
    Route::post('accounting/journal/update', 'API\Accounting\JournalVoucherController@update');
    Route::post('accounting/journal/change_status', 'API\Accounting\JournalVoucherController@changestatus');
    Route::post('accounting/journal/delete_jvs', 'API\Accounting\JournalVoucherController@delete_jvs');
    Route::post('accounting/journal/delete_journal', 'API\Accounting\JournalVoucherController@delete_journal');


    Route::post('accounting/analytics/index', 'API\Accounting\AnalyticsCodesController@index');
    Route::post('accounting/analytics/getallanalystic', 'API\Accounting\AnalyticsCodesController@getallanalystic');
    Route::post('accounting/analytics/store', 'API\Accounting\AnalyticsCodesController@store');
    Route::post('accounting/analytics/edit', 'API\Accounting\AnalyticsCodesController@edit');

    Route::post('accounting/analytics/getdimensionscode', 'API\Accounting\AnalyticsCodesController@getdimensionscode');
    Route::post('accounting/analytics/adddimensionscode', 'API\Accounting\AnalyticsCodesController@adddimensionscode');
    Route::post('accounting/analytics/update', 'API\Accounting\AnalyticsCodesController@update');
    Route::post('accounting/analytics/dimensioncode/change_status', 'API\Accounting\AnalyticsCodesController@changestatusdimension');

    Route::post('accounting/analytics/change_status', 'API\Accounting\AnalyticsCodesController@changestatus');

    Route::post('accounting/global_numbering/store', 'API\Accounting\GlobalNumbering@store');
    Route::post('accounting/global_numbering/idex', 'API\Accounting\GlobalNumbering@getlastinsertid');

    Route::post('accounting/dashboard', 'API\Accounting\GlobalNumbering@dashboard');

    Route::post('accounting/accountant_period', 'API\Accounting\GlobalNumbering@accountantperiod');
    Route::post('accounting/get_accountant_period', 'API\Accounting\GlobalNumbering@getaccountantperiod');
    Route::post('accounting/getallstudent','API\Accounting\GlobalNumbering@getallstudent');

    //Route::post('accounting/permissions/index', 'API\Accounting\UserManagement\PermissionsController@index');
    Route::post('accounting/permission/store', 'API\Accounting\UserManagement\PermissionsController@store');
    Route::post('accounting/permission/update', 'API\Accounting\UserManagement\PermissionsController@update');
    Route::delete('accounting/permission/delete', 'API\Accounting\UserManagement\PermissionsController@destroy');

    Route::post('accounting/store/index', 'API\Accounting\StoreCodingController@index');
    Route::post('accounting/store/getallstores', 'API\Accounting\StoreCodingController@getallstores');
    Route::post('accounting/store/store', 'API\Accounting\StoreCodingController@store');
    Route::post('accounting/store/edit', 'API\Accounting\StoreCodingController@edit');
    Route::post('accounting/store/update', 'API\Accounting\StoreCodingController@update');
    Route::post('accounting/store/change_status', 'API\Accounting\StoreCodingController@changestatus');
    // Route::post('accounting/store/delete', 'API\Accounting\StoreCodingController@destroy');

    Route::post('accounting/category/index', 'API\Accounting\CategoryCodingController@index');
    Route::post('accounting/category/getallcategories', 'API\Accounting\CategoryCodingController@getallcategories');
    Route::post('accounting/category/store', 'API\Accounting\CategoryCodingController@store');
    Route::post('accounting/category/edit', 'API\Accounting\CategoryCodingController@edit');
    Route::post('accounting/category/update', 'API\Accounting\CategoryCodingController@update');
    Route::post('accounting/category/change_status', 'API\Accounting\CategoryCodingController@changestatus');
    Route::post('accounting/category/delete', 'API\Accounting\CategoryCodingController@destroy');

    Route::post('accounting/item/index', 'API\Accounting\ItemCodingController@index');
    Route::post('accounting/item/getallitems', 'API\Accounting\ItemCodingController@getallitems');
    Route::post('accounting/item/store', 'API\Accounting\ItemCodingController@store');
    Route::post('accounting/item/edit', 'API\Accounting\ItemCodingController@edit');
    Route::post('accounting/item/update', 'API\Accounting\ItemCodingController@update');
    Route::post('accounting/item/change_status', 'API\Accounting\ItemCodingController@changestatus');
    // Route::post('accounting/item/delete', 'API\Accounting\ItemCodingController@destroy');

    Route::post('accounting/bank/index', 'API\Accounting\BankController@index');
    Route::post('accounting/bank/getallbanks', 'API\Accounting\BankController@getallbanks');
    Route::post('accounting/bank/store', 'API\Accounting\BankController@store');
    Route::post('accounting/bank/edit', 'API\Accounting\BankController@edit');
    Route::post('accounting/bank/update', 'API\Accounting\BankController@update');
    Route::post('accounting/bank/change_status', 'API\Accounting\BankController@changestatus');
    // Route::post('accounting/bank/delete', 'API\Accounting\BankController@destroy');

    Route::post('accounting/supplier/index', 'API\Accounting\SuplierCodingController@index');
    Route::post('accounting/supplier/getallitems', 'API\Accounting\SuplierCodingController@getallitems');
    Route::post('accounting/supplier/store', 'API\Accounting\SuplierCodingController@store');
    Route::post('accounting/supplier/edit', 'API\Accounting\SuplierCodingController@edit');
    Route::post('accounting/supplier/update', 'API\Accounting\SuplierCodingController@update');
    Route::post('accounting/supplier/change_status', 'API\Accounting\SuplierCodingController@changestatus');
    // Route::post('accounting/supplier/delete', 'API\Accounting\ItemCodingController@destroy');

    Route::post('accounting/payment/success', 'API\Accounting\ParentsCollectionsController@getpaymentsuccess');
    Route::post('accounting/payment/pendding', 'API\Accounting\ParentsCollectionsController@getpaymentpendding');
    Route::post('accounting/payment/failed', 'API\Accounting\ParentsCollectionsController@getpaymentfailed');
    Route::post('accounting/payment/paymentdetails', 'API\Accounting\ParentsCollectionsController@paymentdetails');

    Route::post('accounting/payment_voucher/index', 'API\Accounting\PaymentVoucherController@index');
    Route::post('accounting/payment_voucher/getsuccess', 'API\Accounting\PaymentVoucherController@getsuccess');
    Route::post('accounting/payment_voucher/store', 'API\Accounting\PaymentVoucherController@store');
    Route::post('accounting/payment_voucher/edit', 'API\Accounting\PaymentVoucherController@edit');
    Route::post('accounting/payment_voucher/update', 'API\Accounting\PaymentVoucherController@update');
    Route::post('accounting/payment_voucher/change_status', 'API\Accounting\PaymentVoucherController@changestatus');
    // Route::post('accounting/payment_voucher/paymentdetails', 'API\Accounting\PaymentVoucherController@paymentdetails');

    Route::post('accounting/cash_receipt/index', 'API\Accounting\CashReceiptController@index');
    Route::post('accounting/cash_receipt/getallitems', 'API\Accounting\CashReceiptController@getallitems');
    Route::post('accounting/cash_receipt/getsuccess', 'API\Accounting\CashReceiptController@getsuccess');
    Route::post('accounting/cash_receipt/store', 'API\Accounting\CashReceiptController@store');
    Route::post('accounting/cash_receipt/edit', 'API\Accounting\CashReceiptController@edit');
    Route::post('accounting/cash_receipt/update', 'API\Accounting\CashReceiptController@update');
    Route::post('accounting/cash_receipt/change_status', 'API\Accounting\CashReceiptController@changestatus');
    // Route::post('accounting/cash_receipt/paymentdetails', 'API\Accounting\CashReceiptController@paymentdetails');

    Route::post('accounting/budgets/index', 'API\Accounting\BudgetController@index');
//    Route::post('accounting/budgets/getallusers', 'API\Accounting\BudgetController@getusers');
    Route::post('accounting/budget/store', 'API\Accounting\BudgetController@store');
    Route::post('accounting/budget/update', 'API\Accounting\BudgetController@update');
    Route::delete('accounting/budget/delete', 'API\Accounting\BudgetController@destroy');

});
Route::middleware('apiToken')->group(function () {
    Route::post('user/details', 'API\UserController@details');
    Route::post('changepassword', 'API\UserController@changepassword');
    Route::post('datausage', 'API\UserController@datausage');

    Route::post('driver/details', 'API\DriverController@details');
    Route::post('driver/getroutes', 'API\DriverController@getroutes');
    Route::post('supervisor/details', 'API\SupervisorController@details');
    Route::post('supervisor/getroutes', 'API\SupervisorController@getroutes');
    Route::post('supervisor/setstation', 'API\SupervisorController@setstation');
    Route::post('supervisor/getroutestudent', 'API\StudentController@getroutestudent');
    Route::post('supervisor/getstationstudent', 'API\StudentController@getstationstudent');
    Route::post('supervisor/addstationstudent', 'API\StudentController@addstationstudent');
    Route::post('parent/getsoninfo', 'API\ParentController@getsoninfo');
    Route::post('parent/getparentinfo', 'API\ParentController@get_parent_info');

    #--------->medical api<---------------#
    Route::post('parent/medical/profile/get', 'API\MedicalController@profile_get');
    Route::post('parent/medical/clinic/get', 'API\MedicalController@clinic_get');
    Route::post('parent/medical/profile/form', 'API\MedicalController@profile_form');
    Route::post('parent/medical/profile/edit', 'API\MedicalController@profile_edit');
    Route::post('parent/medical/profile/remove', 'API\MedicalController@profile_remove');

    #--------->pickup api<---------------#
    Route::post('parent/pickup/form', 'API\Pickup_managmentController@form');
    Route::post('parent/pickup/get', 'API\Pickup_managmentController@get');
    Route::post('parent/pickup/remove', 'API\Pickup_managmentController@remove');
    Route::post('parent/qr/form', 'API\Qr_codeController@form');
    Route::post('parent/qr/remove', 'API\Qr_codeController@remove');

    #--------->security api<---------------#
    Route::post('security/getauthinfo', 'API\SecurityController@get_auth_info');
    Route::post('security/pickupconfirm', 'API\SecurityController@pickupconfirm');


    Route::post('teacher/updatedevicetoken', 'API\TeacherController@updatedevicetoken');
    Route::post('teacher/getteacherclasses', 'API\TeacherController@getteacherclasses');
    Route::post('teacher/getstudent', 'API\TeacherController@getstudent');
    Route::post('teacher/attendance', 'API\TeacherController@attendance');
    Route::post('teacher/groupattendance', 'API\TeacherController@group_attendance');
    Route::post('teacher/getbusdetails', 'API\TeacherController@get_bus_details');
    Route::post('teacher/changepassword', 'API\TeacherController@changepassword');
    Route::post('teacher/addhomeworks', 'API\TeacherController@add_homeworks');
    Route::post('teacher/addbehavior', 'API\TeacherController@add_behavior');
    Route::post('teacher/addreward', 'API\TeacherController@add_reward');
    Route::post('teacher/sendtechnicalissue', 'API\TeacherController@send_technical_issue');
    Route::post('teacher/getgradecards', 'API\TeacherController@get_grade_cards');

    #========================>new teacher ==========================#
    Route::post('teacher/schedule', 'API\TeacherController@schedule');
    Route::post('teacher/getstudentreport', 'API\TeacherController@getabsencestudentreport');
    Route::post('teacher/attendance', 'API\TeacherController@attendance1');
    Route::post('teacher/getstudent', 'API\TeacherController@getstudent');


    #========================>principle ==========================#

    Route::post('principal/students/reports', 'API\PrincipalController@sendstudentsreport');
    Route::post('principal/students/report', 'API\PrincipalController@studentsreport');
    Route::post('principal/staff/report', 'API\PrincipalController@staffreport');


});
