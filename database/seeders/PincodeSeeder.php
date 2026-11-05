<?php

namespace Database\Seeders;

use App\Models\Pincode;
use Illuminate\Database\Seeder;

class PincodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pincodes = [
            // Ahmedabad West & SG Highway
            ['pincode' => '380015', 'area_name' => 'Satellite / Vastrapur / Bodakdev', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0304000, 'longitude' => 72.5178000],
            ['pincode' => '380054', 'area_name' => 'Thaltej / SG Highway / Hebatpur', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0531000, 'longitude' => 72.5089000],
            ['pincode' => '380058', 'area_name' => 'Bopal / South Bopal / Ghuma', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0336000, 'longitude' => 72.4632000],
            ['pincode' => '380051', 'area_name' => 'Prahlad Nagar / Makarba / Vejalpur', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 22.9972000, 'longitude' => 72.5083000],
            ['pincode' => '380052', 'area_name' => 'Drive-In / Memnagar / Gurukul', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0519000, 'longitude' => 72.5348000],
            ['pincode' => '380059', 'area_name' => 'Sola / Science City / Ghatlodiya', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0712000, 'longitude' => 72.5358000],
            ['pincode' => '380061', 'area_name' => 'Chanakyapuri / Nirnay Nagar', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0841000, 'longitude' => 72.5321000],
            ['pincode' => '380060', 'area_name' => 'Chandkheda / Motera / Sabarmati', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.1092000, 'longitude' => 72.5852000],
            ['pincode' => '382481', 'area_name' => 'Shilaj / Rancharda / Bopal Ext', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0645000, 'longitude' => 72.4721000],
            ['pincode' => '382210', 'area_name' => 'Sanand / Shela / Godhavi', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 22.9921000, 'longitude' => 72.3812000],

            // Ahmedabad Central & East
            ['pincode' => '380009', 'area_name' => 'Navrangpura / CG Road / Ashram Road', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0365000, 'longitude' => 72.5611000],
            ['pincode' => '380006', 'area_name' => 'Ellisbridge / Ambawadi / Panjrapole', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0211000, 'longitude' => 72.5601000],
            ['pincode' => '380007', 'area_name' => 'Paldi / Vasna / Fatehpura', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0094000, 'longitude' => 72.5654000],
            ['pincode' => '380013', 'area_name' => 'Naranpura / Wadaj / Usmanpura', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0558000, 'longitude' => 72.5574000],
            ['pincode' => '380014', 'area_name' => 'Shahibaug / Camp / Airport Road', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0612000, 'longitude' => 72.5932000],
            ['pincode' => '380001', 'area_name' => 'Lal Darwaja / Kalupur / Relief Road', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0258000, 'longitude' => 72.5873000],
            ['pincode' => '380008', 'area_name' => 'Maninagar / Kankaria / Khokhra', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 22.9981000, 'longitude' => 72.6025000],
            ['pincode' => '380028', 'area_name' => 'Isanpur / Ghodasar / Vatva', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 22.9782000, 'longitude' => 72.5991000],
            ['pincode' => '380024', 'area_name' => 'Bapunagar / Rakhial / Odhav', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0373000, 'longitude' => 72.6310000],
            ['pincode' => '382330', 'area_name' => 'Naroda / Nikol / Krishnanagar', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 23.0721000, 'longitude' => 72.6612000],
            ['pincode' => '382350', 'area_name' => 'Vastral / Ramol / SP Ring Road East', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'latitude' => 22.9989000, 'longitude' => 72.6614000],

            // Gandhinagar
            ['pincode' => '382010', 'area_name' => 'Kudasan / Infocity / Bhaijipura', 'city' => 'Gandhinagar', 'state' => 'Gujarat', 'latitude' => 23.1895000, 'longitude' => 72.6312000],
            ['pincode' => '382007', 'area_name' => 'Sector 7 / Sector 11 / GH Road', 'city' => 'Gandhinagar', 'state' => 'Gujarat', 'latitude' => 23.2189000, 'longitude' => 72.6412000],
            ['pincode' => '382421', 'area_name' => 'Gandhinagar Sectors 21-30 / PDPU', 'city' => 'Gandhinagar', 'state' => 'Gujarat', 'latitude' => 23.2356000, 'longitude' => 72.6569000],
            ['pincode' => '382424', 'area_name' => 'Chandkheda / Zundal / Adalaj', 'city' => 'Gandhinagar', 'state' => 'Gujarat', 'latitude' => 23.1412000, 'longitude' => 72.5812000],

            // Surat
            ['pincode' => '395007', 'area_name' => 'Vesu / Piplod / VIP Road', 'city' => 'Surat', 'state' => 'Gujarat', 'latitude' => 21.1418000, 'longitude' => 72.7758000],
            ['pincode' => '395001', 'area_name' => 'Athwalines / Ring Road / Nanpura', 'city' => 'Surat', 'state' => 'Gujarat', 'latitude' => 21.1860000, 'longitude' => 72.8081000],
            ['pincode' => '395009', 'area_name' => 'Adajan / Pal / LP Savani Road', 'city' => 'Surat', 'state' => 'Gujarat', 'latitude' => 21.1982000, 'longitude' => 72.7885000],
            ['pincode' => '395006', 'area_name' => 'Varachha / Katargam', 'city' => 'Surat', 'state' => 'Gujarat', 'latitude' => 21.2185000, 'longitude' => 72.8451000],

            // Vadodara
            ['pincode' => '390007', 'area_name' => 'Alkapuri / Race Course / Old Padra Road', 'city' => 'Vadodara', 'state' => 'Gujarat', 'latitude' => 22.3106000, 'longitude' => 73.1706000],
            ['pincode' => '390020', 'area_name' => 'Gotri / Vasna Road / Subhanpura', 'city' => 'Vadodara', 'state' => 'Gujarat', 'latitude' => 22.3214000, 'longitude' => 73.1412000],
            ['pincode' => '390019', 'area_name' => 'Karelibaug / Harni / VIP Road', 'city' => 'Vadodara', 'state' => 'Gujarat', 'latitude' => 22.3312000, 'longitude' => 73.2081000],

            // Rajkot
            ['pincode' => '360001', 'area_name' => 'Yagnik Road / Ring Road / Jubilee', 'city' => 'Rajkot', 'state' => 'Gujarat', 'latitude' => 22.3039000, 'longitude' => 70.8022000],
            ['pincode' => '360005', 'area_name' => 'Kalawad Road / University Road', 'city' => 'Rajkot', 'state' => 'Gujarat', 'latitude' => 22.2891000, 'longitude' => 70.7712000],

            // Mumbai Key Areas
            ['pincode' => '400050', 'area_name' => 'Bandra West / Pali Hill / Linking Road', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'latitude' => 19.0596000, 'longitude' => 72.8295000],
            ['pincode' => '400053', 'area_name' => 'Andheri West / Lokhandwala / Oshiwara', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'latitude' => 19.1363000, 'longitude' => 72.8277000],
            ['pincode' => '400054', 'area_name' => 'Santacruz West / Juhu', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'latitude' => 19.0831000, 'longitude' => 72.8351000],
            ['pincode' => '400001', 'area_name' => 'Fort / Colaba / Nariman Point', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'latitude' => 18.9322000, 'longitude' => 72.8335000],

            // Delhi Key Areas
            ['pincode' => '110001', 'area_name' => 'Connaught Place / Janpath / Barakhamba', 'city' => 'New Delhi', 'state' => 'Delhi', 'latitude' => 28.6315000, 'longitude' => 77.2167000],
            ['pincode' => '110016', 'area_name' => 'Hauz Khas / Green Park / Safdarjung', 'city' => 'New Delhi', 'state' => 'Delhi', 'latitude' => 28.5494000, 'longitude' => 77.2001000],
        ];

        foreach ($pincodes as $p) {
            Pincode::updateOrCreate(['pincode' => $p['pincode']], $p);
        }
    }
}

