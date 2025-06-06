<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';


use com\zoho\api\authenticator\AuthBuilder;
use com\zoho\officeintegrator\dc\datacenter\Production;
use com\zoho\officeintegrator\InitializeBuilder;
use com\zoho\officeintegrator\logger\Levels;
use com\zoho\officeintegrator\logger\LogBuilder;
use com\zoho\officeintegrator\util\StreamWrapper;
use com\zoho\officeintegrator\v1\Authentication;
use com\zoho\officeintegrator\v1\CallbackSettings;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\CreatePresentationParameters;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\UserInfo;
use com\zoho\officeintegrator\v1\V1Operations;
use com\zoho\officeintegrator\v1\ZohoShowEditorSettings;
use Exception;

class EditPresentation {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-show-edit-presentation-v1.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new CreatePresentationParameters();

            $url = 'https://demo.office-integrator.com/samples/show/Zoho_Show.pptx';
            $parameters->setUrl($url);

            // Either you can give the document as publicly downloadable url as above or add the file in request body itself using below code.
            // $filePath = getcwd() . DIRECTORY_SEPARATOR . "sample_documents" . DIRECTORY_SEPARATOR . "Zoho_Show.pptx";
            // $parameters->setDocument(new StreamWrapper(null, null, $filePath));

            $documentInfo = new DocumentInfo();

            // Time value used to generate a unique document every time. You can replace it based on your application.
            $documentInfo->setDocumentId(strval(time()));
            $documentInfo->setDocumentName("New Presentation");

            $parameters->setDocumentInfo($documentInfo);

            $userInfo = new UserInfo();

            $userInfo->setUserId("100");
            $userInfo->setDisplayName("User 1");

            $parameters->setUserInfo($userInfo);

            $editorSettings = new ZohoShowEditorSettings();

            $editorSettings->setLanguage("en");

            $parameters->setEditorSettings($editorSettings);

            $permissions = array();

            $permissions["document.export"] = "true";
            $permissions["document.print"] = "true";
            $permissions["document.edit"] = "true";

            $parameters->setPermissions($permissions);

            $callbackSettings = new CallbackSettings();
            $saveUrlParams = array();

            $saveUrlParams["param1"] = "value1";
            $saveUrlParams["param2"] = "value2";

            $callbackSettings->setSaveUrlParams($saveUrlParams);
            
            $saveUrlHeaders = array();

            $saveUrlHeaders["header1"] = "value1";
            $saveUrlHeaders["header2"] = "value2";

            //$callbackSettings->setSaveUrlHeaders($saveUrlHeaders);

            $callbackSettings->setRetries(1);
            $callbackSettings->setSaveFormat("pptx");
            $callbackSettings->setHttpMethodType("post");
            $callbackSettings->setTimeout(100000);
            $callbackSettings->setSaveUrl("https://officeintegrator.zoho.com/v1/api/webhook/savecallback/601e12157123434d4e6e00cc3da2406df2b9a1d84a903c6cfccf92c8286");

            $parameters->setCallbackSettings($callbackSettings);

            $responseObject = $sdkOperations->createPresentation($parameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $presentationResponseObject = $responseObject->getObject();

                if ($presentationResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($presentationResponseObject instanceof CreateDocumentResponse) {
                        echo "\nPresentation ID - " . $presentationResponseObject->getDocumentId() . "\n";
                        echo "\nPresentation session ID - " . $presentationResponseObject->getSessionId() . "\n";
                        echo "\nPresentation session URL - " . $presentationResponseObject->getDocumentUrl() . "\n";
                        echo "\nPresentation save URL - " . $presentationResponseObject->getSaveUrl() . "\n";
                        echo "\nPresentation delete URL - " . $presentationResponseObject->getDocumentDeleteUrl() . "\n";
                        echo "\nPresentation session delete URL - " . $presentationResponseObject->getSessionDeleteUrl() . "\n";
                    } elseif ($presentationResponseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception." . "\n";
                        echo "\nError Code - " . $presentationResponseObject->getCode() . "\n";
                        echo "\nError Message - " . $presentationResponseObject->getMessage() . "\n";
                        if ( $presentationResponseObject->getKeyName() ) {
                            echo "\nError Key Name - " . $presentationResponseObject->getKeyName() . "\n";
                        }
                        if ( $presentationResponseObject->getParameterName() ) {
                            echo "\nError Parameter Name - " . $presentationResponseObject->getParameterName() . "\n";
                        }
                    } else {
                        echo "\nRequest not completed successfully\n";
                    }
                }
            }
        } catch (Exception $error) {
            echo "\nException while running sample code: " . $error . "\n";
        }
    }

    public static function initializeSdk() {

        # Update the api domain based on in which data center user register your apikey
        # To know more - https://www.zoho.com/officeintegrator/api/v1/getting-started.html
        $environment = new Production("https://api.office-integrator.com");
        # User your apikey that you have in office integrator dashboard
        //Update this apikey with your own apikey signed up in office inetgrator service
        $authBuilder = new AuthBuilder();
        $authentication = new Authentication();
        $authBuilder->addParam("apikey", "2ae438cf864488657cc9754a27daa480");
        $authBuilder->authenticationSchema($authentication->getTokenFlow());
        $tokens = [ $authBuilder->build() ];

        # Configure a proper file path to write the sdk logs
        $logger = (new LogBuilder())
            ->level(Levels::INFO)
            ->filePath("./app.log")
            ->build();
        
        (new InitializeBuilder())
            ->environment($environment)
            ->tokens($tokens)
            ->logger($logger)
            ->initialize();

        echo "SDK initialized successfully.\n";
    }
}

EditPresentation::execute();

