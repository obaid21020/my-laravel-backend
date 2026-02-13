<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    public function index(): JsonResponse
    {
        $rooms = Room::with('bookings')->get()->map(function ($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'description' => $room->description,
                'price' => (float) $room->price,
                'image' => $room->image,
                'guests' => $room->guests,
                'beds' => $room->beds,
                'size' => $room->size,
                'view' => $room->view,
                'amenities' => $room->amenities,
                'popular' => $room->popular,
                'occupants' => $room->occupants,
                'bookings' => $room->bookings->map(function ($booking) {
                    return [
                        'from' => $booking->from_date->format('Y-m-d'),
                        'to' => $booking->to_date->format('Y-m-d')
                    ];
                })->toArray()
            ];
        });

        return response()->json($rooms);
    }

    public function book(Request $request, $id): JsonResponse
    {
        $request->validate([
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after:from_date'
        ]);

        $room = Room::findOrFail($id);

        // Check for overlapping bookings
        $overlapping = Booking::where('room_id', $id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('from_date', [$request->from_date, $request->to_date])
                    ->orWhereBetween('to_date', [$request->from_date, $request->to_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('from_date', '<=', $request->from_date)
                          ->where('to_date', '>=', $request->to_date);
                    });
            })
            ->exists();

        if ($overlapping) {
            return response()->json([
                'message' => 'Room is not available for the selected dates'
            ], 422);
        }

        Booking::create([
            'room_id' => $id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date
        ]);

        return response()->json([
            'message' => 'Room booked successfully'
        ]);
    }

    public function cancelBooking(Request $request, $id): JsonResponse
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date'
        ]);

        $booking = Booking::where('room_id', $id)
            ->where('from_date', $request->from_date)
            ->where('to_date', $request->to_date)
            ->first();

        if (!$booking) {
            return response()->json([
                'message' => 'Booking not found'
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'message' => 'Booking cancelled successfully'
        ]);
    }
}