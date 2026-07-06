<?php

namespace App\Http\Controllers;

use App\Models\SystemUpdate;
use Illuminate\Http\Request;

class SystemUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    public function index()
    {
        $updates    = SystemUpdate::with('author')->latest()->paginate(10);
        $nextVersion = SystemUpdate::nextVersion();
        return view('pages.system_updates', compact('updates', 'nextVersion'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'version'    => 'required|string|max:20',
            'title'      => 'required|string|max:200',
            'system'     => 'required|in:ims,cs,both',
            'content'    => 'required|string',
            'show_modal' => 'nullable|boolean',
        ]);

        // If this update shows modal, turn off all previous modals of same system
        if ($request->boolean('show_modal')) {
            $system = $request->system;
            SystemUpdate::where(function ($q) use ($system) {
                $q->where('system', $system)->orWhere('system', 'both');
            })->update(['show_modal' => false]);
        }

        SystemUpdate::create([
            'version'    => $request->version,
            'title'      => $request->title,
            'system'     => $request->system,
            'content'    => $request->content,
            'show_modal' => $request->boolean('show_modal'),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', "Version {$request->version} update published successfully.");
    }

    public function update(Request $request, SystemUpdate $systemUpdate)
    {
        $request->validate([
            'version'    => 'required|string|max:20',
            'title'      => 'required|string|max:200',
            'system'     => 'required|in:ims,cs,both',
            'content'    => 'required|string',
            'show_modal' => 'nullable|boolean',
        ]);

        if ($request->boolean('show_modal')) {
            $system = $request->system;
            SystemUpdate::where('id', '!=', $systemUpdate->id)
                ->where(function ($q) use ($system) {
                    $q->where('system', $system)->orWhere('system', 'both');
                })->update(['show_modal' => false]);
        }

        $systemUpdate->update([
            'version'    => $request->version,
            'title'      => $request->title,
            'system'     => $request->system,
            'content'    => $request->content,
            'show_modal' => $request->boolean('show_modal'),
        ]);

        return back()->with('success', "Update {$request->version} saved.");
    }

    public function destroy(SystemUpdate $systemUpdate)
    {
        $systemUpdate->delete();
        return back()->with('success', 'Update deleted.');
    }

    public function toggleModal(SystemUpdate $systemUpdate)
    {
        // Turn off others first if enabling
        if (!$systemUpdate->show_modal) {
            $system = $systemUpdate->system;
            SystemUpdate::where('id', '!=', $systemUpdate->id)
                ->where(function ($q) use ($system) {
                    $q->where('system', $system)->orWhere('system', 'both');
                })->update(['show_modal' => false]);
        }

        $systemUpdate->update(['show_modal' => !$systemUpdate->show_modal]);

        return response()->json([
            'show_modal' => $systemUpdate->fresh()->show_modal,
            'message'    => $systemUpdate->show_modal ? 'Modal enabled.' : 'Modal disabled.',
        ]);
    }

    // Called after user dismisses the modal — stored per-user in session
    public function dismissModal()
    {
        $system = auth()->user()->source ?? 'ims';

        $latest = \App\Models\SystemUpdate::where(function($q) use ($system) {
            $q->where('system', $system)->orWhere('system', 'both');
        })->where('show_modal', true)->latest()->first();

        if ($latest) {
            session(['update_modal_dismissed_' . $system => $latest->version]);
            session()->save(); // force immediate save
        }

        return response()->json(['ok' => true]);
    }
}