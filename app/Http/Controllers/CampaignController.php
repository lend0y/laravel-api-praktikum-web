<?php
s
namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::query();

        // Search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('disaster_type', 'like', "%{$search}%");
            });
        }

        // Sorting
        $orderable = ['title', 'target_amount', 'collected_amount', 'start_date', 'created_at'];
        $orderBy = $request->query('orderBy', 'created_at');
        $sortBy  = $request->query('sortBy', 'desc');

        if (!in_array($orderBy, $orderable)) {
            $orderBy = 'created_at';
        }

        if (!in_array($sortBy, ['asc', 'desc'])) {
            $sortBy = 'desc';
        }

        $query->orderBy($orderBy, $sortBy);

        // Pagination
        $limit = (int) $request->query('limit', 10);
        if ($limit <= 0) $limit = 10;

        $result = $query->paginate($limit);

        // Tambahkan image_url untuk setiap item
        foreach ($result as $item) {
            $item->image_url = $item->image
                ? asset('storage/' . $item->image)
                : null;
        }

        return $result;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'disaster_type'  => 'required|in:flood,earthquake,tsunami,landslide,fire,other',
            'location'       => 'required|string|max:255',
            'target_amount'  => 'required|numeric|min:0',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['collected_amount'] = 0;
        $data['status'] = 'open';

        // Upload Image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign = Campaign::create($data);

        return response()->json([
            'message' => 'Campaign created successfully',
            'data'    => $campaign
        ], 201);
    }

    public function show($id)
    {
        $campaign = Campaign::findOrFail($id);

        // Tambahkan image_url
        $campaign->image_url = $campaign->image
            ? asset('storage/' . $campaign->image)
            : null;

        return $campaign;
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);

        $data = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'description'      => 'sometimes|string',
            'disaster_type'    => 'sometimes|in:flood,earthquake,tsunami,landslide,fire,other',
            'location'         => 'sometimes|string|max:255',
            'target_amount'    => 'sometimes|numeric|min:0',
            'collected_amount' => 'sometimes|numeric|min:0',
            'status'           => 'sometimes|in:open,closed,completed',
            'start_date'       => 'sometimes|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Replace image
        if ($request->hasFile('image')) {

            // Delete old image
            if ($campaign->image && Storage::disk('public')->exists($campaign->image)) {
                Storage::disk('public')->delete($campaign->image);
            }

            // Upload new image
            $data['image'] = $request->file('image')->store('campaigns', 'public');
        }

        $campaign->update($data);

        return response()->json([
            'message' => 'Campaign updated successfully',
            'data'    => $campaign
        ]);
    }

    public function destroy($id)
    {
        $campaign = Campaign::findOrFail($id);

        // Delete image if exists
        if ($campaign->image && Storage::disk('public')->exists($campaign->image)) {
            Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }
}
