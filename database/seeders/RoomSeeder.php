<?php
// database/seeders/RoomSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run()
    {
        $rooms = [
            [
                'name' => 'Presidential Suite',
                'description' => 'The ultimate luxury experience with panoramic city views, separate living area, and premium amenities.',
                'price' => 599,
                'image' => '/images/hotel_room1_triple.jpg',
                'guests' => 4,
                'beds' => 'King Bed + Sofa Bed',
                'size' => '1200 sq ft',
                'view' => 'City Skyline',
                'amenities' => ['Private Balcony', 'Jacuzzi', 'Mini Bar', 'Work Desk'],
                'popular' => true,
                'occupants' => 0,
            ],
            [
                'name' => 'Executive Room',
                'description' => 'Perfect for business travelers with dedicated work space and modern amenities.',
                'price' => 199,
                'image' => '/images/hotel_room2_double.jpg',
                'guests' => 2,
                'beds' => 'Queen Bed',
                'size' => '500 sq ft',
                'view' => 'Garden View',
                'amenities' => ['Work Desk', 'Ergonomic Chair', 'Coffee Machine', 'Business Center Access'],
                'popular' => false,
                'occupants' => 0,
            ],
            [
                'name' => 'Standard Room',
                'description' => 'Comfortable and well-appointed room with all essential amenities for a pleasant stay.',
                'price' => 149,
                'image' => '/images/hotel_room3_single.jpg',
                'guests' => 2,
                'beds' => 'Queen Bed',
                'size' => '400 sq ft',
                'view' => 'Courtyard View',
                'amenities' => ['Work Area', 'Coffee Machine', 'Safe', 'Hair Dryer'],
                'popular' => false,
                'occupants' => 0,
            ],
            [
                'name' => 'Executive Room',
                'description' => 'Perfect for business travelers with dedicated work space and modern amenities.',
                'price' => 199,
                'image' => '/images/hotel_room2_double.jpg',
                'guests' => 2,
                'beds' => 'Queen Bed',
                'size' => '500 sq ft',
                'view' => 'Garden View',
                'amenities' => ['Work Desk', 'Ergonomic Chair', 'Coffee Machine', 'Business Center Access'],
                'popular' => false,
                'occupants' => 0,
            ],
            [
                'name' => 'Standard Room',
                'description' => 'Comfortable and well-appointed room with all essential amenities for a pleasant stay.',
                'price' => 149,
                'image' => '/images/hotel_room3_single.jpg',
                'guests' => 2,
                'beds' => 'Queen Bed',
                'size' => '400 sq ft',
                'view' => 'Courtyard View',
                'amenities' => ['Work Area', 'Coffee Machine', 'Safe', 'Hair Dryer'],
                'popular' => false,
                'occupants' => 0,
            ],
            [
                'name' => 'Economy Room',
                'description' => 'Budget-friendly accommodation with essential amenities for comfortable stay.',
                'price' => 99,
                'image' => '/images/hotel_room3_single.jpg',
                'guests' => 2,
                'beds' => 'Double Bed',
                'size' => '300 sq ft',
                'view' => 'Interior View',
                'amenities' => ['Basic Amenities', 'Clean Linens', 'Private Bathroom', 'Wi-Fi'],
                'popular' => false,
                'occupants' => 0,
            ],
        ];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}

// Also update database/seeders/DatabaseSeeder.php
// Add this line in the run() method:
// $this->call(RoomSeeder::class);