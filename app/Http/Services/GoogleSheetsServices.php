<?php

namespace App\Http\Services;

use Google\Client;
use Google\Service\Sheets;
use Google\Service\Sheets\ValueRange;
use Google\Service\Drive;
use Google\Service\Sheets\SpreadSheet;
use Google\Service\Drive\Permission;

class GoogleSheetsServices
{
    public $client, $service, $id, $range;

    public function __construct()
    {
        $this->client = $this->getClient();
        $this->service = new Sheets($this->client);
        $this->id = '';
        $this->range = 'A:Z';
    }

    public function getClient()
    {
        $client = new Client();
        $client->setApplicationName('Google Sheets API PHP');
        $client->setRedirectUri('http://127.0.0.1:8000/googlesheet');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig(storage_path('key.json'));
        $client->setAccessType('offline');

        return $client;
    }

    public function readSheet($id)
    {
        $this->id = $id;
        $doc = $this->service->spreadsheets_values->get($this->id, $this->range);

        return $doc;
    }

    public function writeSheet($id, $values)
    {
        $this->id = $id;

        $body = new ValueRange([
            'values' => $values
        ]);

        $params = [
            'valueInputOption' => 'RAW'
        ];

        //executing the request
        $result = $this->service->spreadsheets_values->update($this->id, $this->range,
        $body, $params);

        // dd($result);
    }

    public function create($email)
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('key.json'));
        $client->useApplicationDefaultCredentials();
        $client->addScope(Drive::DRIVE);
        $service = new Sheets($client);

        try{

            $spreadsheet = new SpreadSheet([
                'properties' => [
                    'title' => '公司權益資料'
                    ]
                ]);

            $spreadsheet = $service->spreadsheets->create($spreadsheet, [
                'fields' => 'spreadsheetId'
            ]);

            // printf("google試算表: %s\n", $spreadsheet->spreadsheetId);

            $drive = new Drive($client);
            $drivePermisson = new Permission();
            // Type permission
            $drivePermisson->setType('user');
            // You email
            $drivePermisson->setEmailAddress($email);

            $drivePermisson->setRole('writer');

            $drive->permissions->create($spreadsheet->spreadsheetId, $drivePermisson);
            return $spreadsheet->spreadsheetId;
        }
        catch(Exception $e) {
            // TODO(developer) - handle error appropriately
            echo 'Message: ' .$e->getMessage();
          }
    }

    // public function
}
