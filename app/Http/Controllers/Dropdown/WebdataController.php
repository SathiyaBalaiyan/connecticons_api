<?php

namespace App\Http\Controllers\Dropdown;

use Exception;
use Carbon\Carbon;
use App\Models\Webtheme;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Methods\DbactionsController;

class WebdataController extends Controller
{
    //
    private $dbactions;
    public function __construct()
    {
        $this->dbactions = new DbactionsController();
    }
    // set theme color
    public function postThemeColor(Request $request)
    {
        try {
            $validationResult = $this->dbactions->getInputDatas( [
                'userId',
                'themeColor'
            ]);
            if ($validationResult[0]) {
                return $validationResult[1];
            }
            ['userId' => $userId, 'themeColor' => $themeColor] = $validationResult[1];
            //  post and update both action in same function 

            $queryResult = Webtheme::where('user_id', $userId)->first();

            // Set the current timestamp for created_at and updated_at
            $currentTimestamp = Carbon::now('Asia/Kolkata');
            if ($queryResult) {
                // Update the theme_number if the record exists
                $queryResult->theme_number = 0 < $themeColor && $themeColor < 8 ? $themeColor : 1;
                $queryResult->updated_at = $currentTimestamp;
                $queryResult->save();

                return $this->dbactions->message('theme color updated successfully');
            }
            $webTheme = new Webtheme();
            $webTheme->user_id = $userId;
            $webTheme->theme_number = $themeColor;
            $webTheme->created_at = $currentTimestamp;
            $webTheme->updated_at = $currentTimestamp;
            // Save the webTheme to the database
            $saved = $webTheme->save();
            if ($saved) {
                return $this->dbactions->message('theme color created successfully');
            }
            return $this->dbactions->errorMessage('theme color faild to created');


        } catch (Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
        }
    }
    public function getThemeColor(Request $request)
    {
        try {
            //  $request = new Request();
            $validationResult = $this->dbactions->getInputDatas( [
                'userId'
            ]);
            if ($validationResult[0]) {
                return $validationResult[1];
            }
            ['userId' => $userId] = $validationResult[1];
            //  post and update both action in same function 

            // $methodsCollection = new MethodsCollection();
            $queryResult = $this->dbactions->getThemeNumber($userId);

            return $this->dbactions->message('data fetched successfully', ['themeColor' => $queryResult]);

        } catch (Exception $e) {
            return $this->dbactions->serverErrorMessage($e->getMessage());
        }
    }


}
