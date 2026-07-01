<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackupRestoreController extends Controller
{
    public function index()
    {
        return response('OK', 200);
    }

    public function backupFull(Request $request)
    {
        return response('OK', 200);
    }

    public function backupSelective(Request $request)
    {
        return response('OK', 200);
    }

    public function download($file)
    {
        return response('OK', 200);
    }

    public function deleteBackup($file)
    {
        return response('OK', 200);
    }

    public function restore(Request $request)
    {
        return response('OK', 200);
    }

    public function importSql(Request $request)
    {
        return response('OK', 200);
    }
}
