<?php
namespace com\zoho\officeintegrator\v1\writer;

require_once dirname(__FILE__) . '/../vendor/autoload.php';


use com\zoho\api\authenticator\AuthBuilder;
use com\zoho\officeintegrator\dc\datacenter\Production;
use com\zoho\officeintegrator\InitializeBuilder;
use com\zoho\officeintegrator\logger\Levels;
use com\zoho\officeintegrator\logger\LogBuilder;
use com\zoho\officeintegrator\v1\Authentication;
use com\zoho\officeintegrator\v1\CreateDocumentResponse;
use com\zoho\officeintegrator\v1\InvalidConfigurationException;
use com\zoho\officeintegrator\v1\CreateDocumentParameters;
use com\zoho\officeintegrator\v1\DocumentInfo;
use com\zoho\officeintegrator\v1\DocumentSessionDeleteSuccessResponse;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class DeleteDocumentSession {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-writer-delete-user-session.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $createDocumentParameters = new CreateDocumentParameters();

            $documentInfo = new DocumentInfo();

            // Time value used to generate a unique document every time. You can replace it based on your application.
            $documentInfo->setDocumentId(strval(time()));
            $documentInfo->setDocumentName("New Document");

            $createDocumentParameters->setDocumentInfo($documentInfo);

            echo "\nCreating a document to demonstrate document session delete api";

            $responseObject = $sdkOperations->createDocument($createDocumentParameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($writerResponseObject instanceof CreateDocumentResponse) {
                        $sessionId = $writerResponseObject->getSessionId();

                        echo "\nDocument Session ID to be deleted - " . $sessionId . "\n";
                        
                        $deleteApiResponse = $sdkOperations->deleteSession($sessionId);

                        if ($deleteApiResponse != null) {
                            // Get the status code from response
                            echo "\nDelete API Response Status Code: " . $deleteApiResponse->getStatusCode() . "\n";
            
                            // Get the api response object from responseObject
                            $deleteResponseObject = $deleteApiResponse->getObject();
            
                            if ($deleteResponseObject != null) {
                                // Check if the expected CreateDocumentResponse instance is received
                                if ($deleteResponseObject instanceof DocumentSessionDeleteSuccessResponse) {            
                                    echo "\nDocument Session delete status :  - " . $deleteResponseObject->getSessionDeleted() . "\n";
                                } elseif ($deleteResponseObject instanceof InvalidConfigurationException) {
                                    echo "\nInvalid configuration exception." . "\n";
                                    echo "\nError Code - " . $deleteResponseObject->getCode() . "\n";
                                    echo "\nError Message - " . $deleteResponseObject->getMessage() . "\n";
                                    if ( $deleteResponseObject->getKeyName() ) {
                                        echo "\nError Key Name - " . $deleteResponseObject->getKeyName() . "\n";
                                    }
                                    if ( $deleteResponseObject->getParameterName() ) {
                                        echo "\nError Parameter Name - " . $deleteResponseObject->getParameterName() . "\n";
                                    }
                                } else {
                                    echo "\nRequest not completed successfully\n";
                                }
                            }
                        }

                    } elseif ($writerResponseObject instanceof InvalidConfigurationException) {
                        echo "\nInvalid configuration exception." . "\n";
                        echo "\nError Code - " . $writerResponseObject->getCode() . "\n";
                        echo "\nError Message - " . $writerResponseObject->getMessage() . "\n";
                        if ( $writerResponseObject->getKeyName() ) {
                            echo "\nError Key Name - " . $writerResponseObject->getKeyName() . "\n";
                        }
                        if ( $writerResponseObject->getParameterName() ) {
                            echo "\nError Parameter Name - " . $writerResponseObject->getParameterName() . "\n";
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

DeleteDocumentSession::execute();

