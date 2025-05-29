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
use com\zoho\officeintegrator\v1\EditPdfParameters;
use com\zoho\officeintegrator\v1\SessionMeta;
use com\zoho\officeintegrator\v1\V1Operations;
use Exception;

class GetPdfSessionDetails {

    //Refer API documentation - https://www.zoho.com/officeintegrator/api/v1/zoho-writer-document-details.html
    public static function execute() {
        // Initializing SDK once is enough. Calling here since the code sample will be tested standalone. 
        // You can place SDK initializer code in your application and call it once while your application starts up.
        self::initializeSdk();

        try {
            $sdkOperations = new V1Operations();
            $parameters = new EditPdfParameters();

            $url = 'https://demo.office-integrator.com/zdocs/EventForm.pdf';
            $parameters->setUrl($url);

            $responseObject = $sdkOperations->editPdf($parameters);

            if ($responseObject != null) {
                // Get the status code from response
                echo "\nStatus Code: " . $responseObject->getStatusCode() . "\n";

                // Get the api response object from responseObject
                $writerResponseObject = $responseObject->getObject();

                if ($writerResponseObject != null) {
                    // Check if the expected CreateDocumentResponse instance is received
                    if ($writerResponseObject instanceof CreateDocumentResponse) {
                        $sessionId = $writerResponseObject->getSessionId();

                        echo "\nCreated PDF Session ID : " . $sessionId . "\n";
                        
                        $documentDetailsResponse = $sdkOperations->getPdfDocumentSession($sessionId);

                        if ($documentDetailsResponse != null) {
                            // Get the status code from response
                            echo "\nStatus Code: " . $documentDetailsResponse->getStatusCode() . "\n";
            
                            // Get the api response object from responseObject
                            $documentSessionInfoObj = $documentDetailsResponse->getObject();
            
                            if ($documentSessionInfoObj != null) {
                                // Check if the expected CreateDocumentResponse instance is received
                                if ($documentSessionInfoObj instanceof SessionMeta) {            
                                    echo "\nPdf Session Status :  - " . $documentSessionInfoObj->getStatus() . "\n";
                                    echo "\nPdf Session User DisplayName :  - " . $documentSessionInfoObj->getUserInfo()->getDisplayName() . "\n";
                                    echo "\nPdf Session Expires On :  - " . $documentSessionInfoObj->getInfo()->getExpiresOn() . "\n";
                                } elseif ($documentSessionInfoObj instanceof InvalidConfigurationException) {
                                    echo "\nInvalid configuration exception." . "\n";
                                    echo "\nError Code - " . $documentSessionInfoObj->getCode() . "\n";
                                    echo "\nError Message - " . $documentSessionInfoObj->getMessage() . "\n";
                                    if ( $documentSessionInfoObj->getKeyName() ) {
                                        echo "\nError Key Name - " . $documentSessionInfoObj->getKeyName() . "\n";
                                    }
                                    if ( $documentSessionInfoObj->getParameterName() ) {
                                        echo "\nError Parameter Name - " . $documentSessionInfoObj->getParameterName() . "\n";
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

GetPdfSessionDetails::execute();
