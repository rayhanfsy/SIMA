<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_if(auth()->user()->role !== 'lurah', 403);

        $dateToRules = ['nullable', 'date'];
        if ($request->filled('date_from')) {
            $dateToRules[] = 'after_or_equal:date_from';
        }

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'event' => ['nullable', 'string', 'max:40'],
            'date_from' => ['nullable', 'date'],
            'date_to' => $dateToRules,
        ]);

        $query = AuditLog::query()->with('user')->latest();

        if (!empty($validated['q'])) {
            $keyword = trim($validated['q']);
            $query->where(function ($builder) use ($keyword) {
                $builder->where('description', 'like', "%{$keyword}%")
                    ->orWhere('ip_address', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        if (!empty($validated['event'])) {
            $event = $validated['event'];

            if ($event === 'DATA.MUTATION') {
                $query->whereIn('action', ['DATA.MUTATION', 'CREATE', 'UPDATE', 'DELETE']);
            } else {
                $query->where('action', $event);
            }
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        $logs = $query->paginate(20)->withQueryString();

        $events = [
            'LOGIN.SUCCESS' => 'LOGIN.SUCCESS',
            'LOGIN.FAILED' => 'LOGIN.FAILED',
            'LOGOUT' => 'LOGOUT',
            'DATA.MUTATION' => 'DATA.MUTATION',
        ];

        return view('audit', compact('logs', 'events'));
    }
}
