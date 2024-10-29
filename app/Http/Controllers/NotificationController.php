<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\NewsWasPublished;
use Illuminate\Http\Request;
use App\Traits\FormDataTrait;
use App\Services\TableViewDataService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Config;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Client;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use ReflectionClass;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FormDataTrait;
    protected $controller;
    protected $model;
    private $tableViewDataService;

    public function __construct(TableViewDataService $tableViewDataService)
    {
        $this->model = Notification::class;
        $this->controller = collect([
            '0' => 'notification', // Use a string for the controller name
            '1' => 'Notification',
        ]);

        $this->tableViewDataService = $tableViewDataService;
    }
    public function index(Request $request)
    {
        $filters = $request->except(['tab', '_token', '_method']);
        //  $filterData = $this->filterData($this->model);
        $controller = $this->controller;

        return View('admin.Communication.notification_index');
    }

  

    public function sendText(Request $request)
    {
        $smsContent = "Test Message";
        $username = env('AT_USERNAME');
        $apiKey = env('AT_KEY');
        $from = env('AT_FROM');
        
        
        $client = new Client([
            'verify' => false,
            'base_uri' => 'https://api.sandbox.africastalking.com'
        ]);
    
        try {
            $response = $client->post('/version1/messaging', [
                'form_params' => [
                    'username' => $username,
                    'to' => '+254723710025', // The number you want to test with
                    'message' => $smsContent,
                    
                ],
                'headers' => [
                    'apiKey' => $apiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json'
                ]
            ]);
    
            $responseBody = $response->getBody()->getContents();
            
            // Try to parse as JSON first
            $result = json_decode($responseBody, true);
            
            if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
                // If JSON parsing fails, try XML
                $xml = simplexml_load_string($responseBody);
                if ($xml === false) {
                    Log::error('AfricasTalking API Response Parse Error', [
                        'response' => $responseBody
                    ]);
                    return 'Error parsing API response';
                }
                $result = $this->xmlToArray($xml);
            }
    
            Log::info('AfricasTalking API Response', ['result' => $result]);
    
            if (isset($result['SMSMessageData']['Recipients'][0]['status']) && 
                $result['SMSMessageData']['Recipients'][0]['status'] === 'Success') {
                return 'SMS sent successfully';
            } else {
                $errorMessage = $result['SMSMessageData']['Message'] ?? 'Unknown error';
                Log::error('AfricasTalking API Error', ['result' => $result]);
                return 'SMS sending failed: ' . $errorMessage;
            }
        } catch (\Exception $e) {
            Log::error('AfricasTalking API Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 'Error: ' . $e->getMessage();
        }
    }
    
    private function xmlToArray($xml)
    {
        $array = json_decode(json_encode((array) $xml), true);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->xmlToArray($value);
            }
        }
        return $array;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::find(1); // get some user

        // send notification 
        $user->notify(new NewsWasPublished());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
