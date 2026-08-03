<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\ActivityLog;
use App\Models\IpDevice;
use App\Models\Sparepart;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create teams
        $teamInfra = Team::create(['name' => 'Tim Infrastruktur', 'description' => 'Menangani hardware dan jaringan']);
        $teamSoftware = Team::create(['name' => 'Tim Software', 'description' => 'Menangani software dan aplikasi']);

        // Create spareparts
        Sparepart::create(['name' => 'RAM DDR4 8GB', 'stock' => 20, 'price' => 450000]);
        Sparepart::create(['name' => 'RAM DDR4 16GB', 'stock' => 10, 'price' => 850000]);
        Sparepart::create(['name' => 'SSD 256GB', 'stock' => 15, 'price' => 380000]);
        Sparepart::create(['name' => 'SSD 512GB', 'stock' => 8, 'price' => 650000]);
        Sparepart::create(['name' => 'HDD 1TB', 'stock' => 12, 'price' => 550000]);
        Sparepart::create(['name' => 'Keyboard USB', 'stock' => 25, 'price' => 125000]);
        Sparepart::create(['name' => 'Mouse Wireless', 'stock' => 20, 'price' => 95000]);
        Sparepart::create(['name' => 'Kabel LAN Cat6 (5m)', 'stock' => 50, 'price' => 35000]);
        Sparepart::create(['name' => 'Switch 8 Port', 'stock' => 5, 'price' => 280000]);
        Sparepart::create(['name' => 'Printer Head Canon', 'stock' => 3, 'price' => 320000]);

        // Create IP devices
        IpDevice::create(['name' => 'Server Utama', 'brand' => 'Dell', 'specifications' => 'RAM 64GB, Xeon E5-2620, SSD 1TB RAID', 'ip_address' => '192.168.1.10', 'location' => 'Server Room Lt.3']);
        IpDevice::create(['name' => 'Server Database', 'brand' => 'HP', 'specifications' => 'RAM 32GB, Xeon E5-2640, SSD 2TB RAID', 'ip_address' => '192.168.1.11', 'location' => 'Server Room Lt.3']);
        IpDevice::create(['name' => 'NAS Storage', 'brand' => 'Synology', 'specifications' => '4-Bay, 16TB HDD RAID 5', 'ip_address' => '192.168.1.12', 'location' => 'Server Room Lt.3']);
        IpDevice::create(['name' => 'Router Core', 'brand' => 'Mikrotik', 'specifications' => 'CCR1036-12G-4S, 36 Core', 'ip_address' => '192.168.1.1', 'location' => 'Server Room Lt.3']);
        IpDevice::create(['name' => 'Switch Lantai 1', 'brand' => 'Cisco', 'specifications' => 'Catalyst 2960, 24 Port Gigabit', 'ip_address' => '192.168.1.21', 'location' => 'Lantai 1']);
        IpDevice::create(['name' => 'Switch Lantai 2', 'brand' => 'Cisco', 'specifications' => 'Catalyst 2960, 24 Port Gigabit', 'ip_address' => '192.168.1.22', 'location' => 'Lantai 2']);
        IpDevice::create(['name' => 'Switch Lantai 3', 'brand' => 'D-Link', 'specifications' => 'DGS-1100-24, 24 Port Smart', 'ip_address' => '192.168.1.23', 'location' => 'Lantai 3']);
        IpDevice::create(['name' => 'Access Point Lt.1', 'brand' => 'Ubiquiti', 'specifications' => 'UniFi AP AC Pro, Dual Band', 'ip_address' => '192.168.1.31', 'location' => 'Lantai 1']);
        IpDevice::create(['name' => 'Access Point Lt.2', 'brand' => 'Ubiquiti', 'specifications' => 'UniFi AP AC Pro, Dual Band', 'ip_address' => '192.168.1.32', 'location' => 'Lantai 2']);
        IpDevice::create(['name' => 'Printer HP Lt.1', 'brand' => 'HP', 'specifications' => 'LaserJet Pro M404dn, Network Ready', 'ip_address' => '192.168.1.51', 'location' => 'Lantai 1']);
        IpDevice::create(['name' => 'Printer Canon Lt.2', 'brand' => 'Canon', 'specifications' => 'imageCLASS MF269dw, All-in-One', 'ip_address' => '192.168.1.52', 'location' => 'Lantai 2']);
        IpDevice::create(['name' => 'CCTV Server', 'brand' => 'Hikvision', 'specifications' => 'DS-7600NI-K4, 4CH NVR', 'ip_address' => '192.168.1.40', 'location' => 'Server Room Lt.3']);

        // Create admin
        $admin = User::create([
            'name' => 'Admin IT',
            'email' => 'admin@itdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'team_id' => $teamInfra->id,
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        // Create technicians
        $tech1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@itdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'team_id' => $teamInfra->id,
            'phone' => '081234567891',
            'is_active' => true,
        ]);

        $tech2 = User::create([
            'name' => 'Sari Dewi',
            'email' => 'sari@itdesk.com',
            'password' => Hash::make('password123'),
            'role' => 'technician',
            'team_id' => $teamSoftware->id,
            'phone' => '081234567892',
            'is_active' => true,
        ]);

        // Create sample activities
        $activities = [
            [
                'ticket_number' => 'TKT-20240701',
                'title' => 'Laptop tidak menyala',
                'description' => 'Laptop milik bagian HRD tidak bisa menyala sejak pagi',
                'category' => 'hardware',
                'priority' => 'high',
                'status' => 'completed',
                'reporter_name' => 'Rina HRD',
                'reporter_phone' => '081234567893',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
                'assigned_to' => $tech1->id,
                'team_id' => $teamInfra->id,
                'created_by' => $admin->id,
                'completed_at' => now()->subHours(2),
            ],
            [
                'ticket_number' => 'TKT-20240702',
                'title' => 'Aplikasi error saat login',
                'description' => 'Aplikasi CRM menampilkan error 500 saat login',
                'category' => 'software',
                'priority' => 'medium',
                'status' => 'in_progress',
                'reporter_name' => 'Andi Marketing',
                'reporter_phone' => '081234567894',
                'latitude' => -6.2090,
                'longitude' => 106.8460,
                'assigned_to' => $tech2->id,
                'team_id' => $teamSoftware->id,
                'created_by' => $admin->id,
            ],
            [
                'ticket_number' => 'TKT-20240703',
                'title' => 'Wifi putus-putus',
                'description' => 'Koneksi wifi di lantai 3 sering terputus',
                'category' => 'network',
                'priority' => 'high',
                'status' => 'pending',
                'reporter_name' => 'Dina Finance',
                'reporter_phone' => '081234567895',
                'latitude' => -6.2092,
                'longitude' => 106.8462,
                'created_by' => $admin->id,
            ],
        ];

        foreach ($activities as $activityData) {
            Activity::create($activityData);
        }
    }
}
