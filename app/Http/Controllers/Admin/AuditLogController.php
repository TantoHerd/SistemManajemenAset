<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter berdasarkan module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('record_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(50);

        // Data untuk filter dropdown
        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);
        $actions = AuditLog::distinct()->pluck('action');
        $modules = AuditLog::distinct()->pluck('module');

        return view('admin.audit-log.index', compact('logs', 'users', 'actions', 'modules'));
    }

    public function show(AuditLog $auditLog)
    {
        return view('admin.audit-log.show', compact('auditLog'));
    }

    public function export(Request $request)
    {
        // Implement export Excel
        // Bisa pakai Maatwebsite/Excel
    }

    public function destroy($id)
    {
        // Hanya super admin yang bisa hapus log (jika diperlukan)
        if (!auth()->user()->hasRole('super_admin')) {
            return back()->with('error', 'Hanya Super Admin yang dapat menghapus log.');
        }

        AuditLog::where('created_at', '<', now()->subMonths(3))->delete();
        
        return back()->with('success', 'Log lebih dari 3 bulan berhasil dihapus.');
    }
}