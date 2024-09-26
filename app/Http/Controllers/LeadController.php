<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $page = $request->query('page', 1);
        $cacheKey = "leads_{$type}_page_{$page}";

        $leads = Cache::get($cacheKey);
        if (!$leads) {
            $leads = Cache::remember($cacheKey, 60, function () use ($type) {
                return Lead::where('type', $type)
                    ->with('user')
                    ->paginate(1000);
            });
        }

        return response()->json($leads);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'contact' => 'required|string',
            'email' => 'required|email',
            'name' => 'required|string',
            'type' => 'required|in:WEB,WALKIN,STORE',
        ]);

        $lead = Lead::create([
            'title' => $request->title,
            'contact' => $request->contact,
            'email' => $request->email,
            'name' => $request->name,
            'type' => $request->type,
            'user_id' => auth()->id(),
        ]);

        $this->flushCacheForType($lead->type);

        return response()->json($lead, 201);
    }

    public function show(Lead $lead)
    {
        return response()->json($lead);
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'title' => 'sometimes|required|string',
            'contact' => 'sometimes|required|string',
            'email' => 'sometimes|required|email',
            'name' => 'sometimes|required|string',
            'type' => 'sometimes|required|in:WEB,WALKIN,STORE',
        ]);

        $lead->update($request->only('title', 'contact', 'email', 'name', 'type'));

        $this->flushCacheForType($lead->type);

        return response()->json($lead);
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        $this->flushCacheForType($lead->type);
        return response()->json(['message' => 'Lead deleted']);
    }

    private function flushCacheForType($type)
    {
        for ($page = 1; $page <= 10; $page++) {
            Cache::forget("leads_{$type}_page_{$page}");
        }
    }
}
