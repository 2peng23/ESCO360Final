<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class DeviceController extends Controller
{
    public function devices()
    {
        $devices = Device::orderBy("created_at", 'desc');

        // For users only
        if (Auth::user()->usertype == 0) {
            $devices->where('user_id', Auth::id());
        }

        return response()->json([
            'devices' => $devices->get()
        ]);
    }

    public function addAccount(Request $request)
    {
        $subscriptionKey = $request->subscription_key;
        $accountId = $request->account_id;
        // Define the endpoint URL
        $url = "https://api.crestron.io/api/v1/device/accountid/{$accountId}/devices";
        // Make the request to the API
        $response = Http::withHeaders([
            'XiO-subscription-key' => $subscriptionKey,
        ])->get($url);
        // Check if the request was successful
        if ($response->successful()) {
            // Decode the JSON response use json()
            $data = $response->json();
            // create New Account Details
            $existingAccount = Account::where('accountId', $accountId)->first();
            if (!$existingAccount) {
                $account = new Account();
                $account->user_id = Auth::id();
                $account->accountId = Hash::make($accountId) ;
                $account->apiKey = Hash::make($subscriptionKey);
                $account->save();
            }

            // Save device(S) of the account
            foreach ($data as $item) {
                $existingDevice = Device::where('device_accId', $existingAccount->accountId)->first();
                if (!$existingDevice) {
                    $device = new Device();
                    $device->user_id = Auth::id();
                    $device->api_key =  Hash::make($account->apiKey);
                    $device->device_accId =  Hash::make($account->accountId);
                    $device->device_cid = $item['device-cid'];
                    $device->device_id = $item['device-id'];
                    // $device->room_name = $item['room_name'];
                    $device->device_name = $item['device-name'];
                    $device->device_model = $item['device-model'];
                    $device->device_category = $item['device-category'];
                    $device->device_manufacturer = $item['device-manufacturer'];
                    $device->device_buildDate = $item['device-builddate'];
                    $device->device_serialN = $item['serial-number'];
                    $device->device_status = $item['device-status'];

                    //   Get each device  firmware version and occupancy status
                    $device_data = $this->deviceInfo($account->accountId, $device->device_cid, $device->api_key);
                    $device->device_firmwareVer = $device_data['device']['firmware-version'];
                    if ($device_data['device']['occupancy-status'] == null) {
                        $device->device_occupancyStat = 'Vacant';
                    } else {
                        $device->device_occupancyStat = $device_data['device']['occupancy-status'];
                    }
                    $device->save();
                }
            }
            return response()->json([
                'success' => "Account Added!"
            ]);
        } else {
            // return the error response message
            return response()->json([
                'error' => $response['message']
            ]);
        }
    }
    public function deviceInfo($accountId, $deviceCID, $subscriptionKey)
    {
        // Define the endpoint URL
        $url = "https://api.crestron.io/api/v2/device/accountid/{$accountId}/devicecid/{$deviceCID}/status";
        // Make the request to the API
        $response = Http::withHeaders([
            'XiO-subscription-key' => $subscriptionKey,
        ])->get($url);
        // Check if the request was successful
        if ($response->successful()) {
            // Decode the JSON response
            $data = $response->json();
            // Return the data retrieved from the API
            return $data;
        } else {
            // Print error message along with the status code
            return response()->json([
                'error' => $response['message']
            ]);
        }
    }
    public function deviceInformation($deviceCID)
    {
        $device = Device::where('device_cid', $deviceCID)->first();

        return $device ? response()->json(['device' => $device]) : response()->json(['error' => 'Device not found.']);
    }


}
