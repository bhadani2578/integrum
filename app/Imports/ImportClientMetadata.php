<?php

namespace App\Imports;

use App\Models\ClientMetadata;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ImportClientMetadata implements ToCollection
{

    protected $client;

    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
        $check = ClientMetadata::where('client_id', $this->client->id)->get();
        if(isset($check) && !empty($check)){
            
            // ClientMetadata::where('client_id', $this->client->id)->delete();  
            // Delete a record
            $deletedRows = DB::table('client_metadata')->where('client_id', $this->client->id)->delete();
        }
        
        foreach ($rows as $key => $row) {
            if(isset($row) && count($row) > 0 && $key != 0){
                $client_id = $this->client->id;
                $address = $row[0];
                $city = $row[1];
                $zipcode = $row[2];
               
                ClientMetadata::create([
                    'client_id' => $client_id,
                    'address' => $address,
                    'city' => $city,
                    'zipcode' => $zipcode,
                
                ]);
            }
            
        
        }
        
    }
}
