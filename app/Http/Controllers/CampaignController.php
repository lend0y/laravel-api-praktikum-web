<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('disaster_type', 'like', "%{$search}%");
            });
        }

        $orderable = ['title', 'target_amount', 'collected_amount', 'start_date', 'created_at'];
        $orderBy = $request->query('orderBy', 'created_at');
        if (!in_array($orderBy, $orderable, true)) {
            $orderBy = 'created_at';
        }

        $sortBy = strtolower($request->query('sortBy', 'desc'));
        if (!in_array($sortBy, ['asc', 'desc'], true)) {
            $sortBy = 'desc';
        }

        $query->orderBy($orderBy, $sortBy);

        $limit = (int) $request->query('limit', 10);
        if ($limit <= 0) {
            $limit = 10;
        }

        return $query->paginate($limit);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'disaster_type'  => 'required|in:flood,earthquake,tsunami,landslide,fire,other',
            'location'       => 'required|string|max:255',
            'target_amount'  => 'required|numeric|min:0',
            'collected_amount' => 'nullable|numeric|min:0',
            'status'         => 'required|in:open,closed,completed',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
        ]);

        if (!isset($data['collected_amount'])) {
            $data['collected_amount'] = 0;
        }

        $campaign = Campaign::create($data);

        return response()->json($campaign, 201);
    }

    public function show($id)
    {
        return Campaign::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $data = $request->validate([
            'title'          => 'sometimes|required|string|max:255',
            'description'    => 'sometimes|required|string',
            'disaster_type'  => 'sometimes|required|in:flood,earthquake,tsunami,landslide,fire,other',
            'location'       => 'sometimes|required|string|max:255',
            'target_amount'  => 'sometimes|required|numeric|min:0',
            'collected_amount' => 'sometimes|numeric|min:0',
            'status'         => 'sometimes|required|in:open,closed,completed',
            'start_date'     => 'sometimes|required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
        ]);

        $campaign->update($data);

        return $campaign;
    }

    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
